<?php
// public/index.php - Front Controller

require_once __DIR__ . '/../includes/auth.php';

// Detecta la base real aunque el front-controller viva en /public.
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$requestPath = str_replace('\\', '/', $requestPath);

$base_path = $scriptDir;
if ($scriptDir !== '' && preg_match('#/public$#', $scriptDir)) {
    $baseWithoutPublic = preg_replace('#/public$#', '', $scriptDir);
    $baseWithoutPublic = rtrim($baseWithoutPublic, '/');

    // Si la URL actual no incluye /public pero sí coincide con la base padre,
    // usamos la base padre para evitar redirecciones rotas.
    if ($baseWithoutPublic === '') {
        if (strpos($requestPath, '/public/') !== 0 && $requestPath !== '/public') {
            $base_path = '';
        }
    } elseif (strpos($requestPath, $baseWithoutPublic . '/') === 0 || $requestPath === $baseWithoutPublic) {
        if (strpos($requestPath, $scriptDir . '/') !== 0 && $requestPath !== $scriptDir) {
            $base_path = $baseWithoutPublic;
        }
    }
}

$base_path = rtrim($base_path, '/');
if ($base_path === '') {
    $base_path = '/';
}
define('BASE_URL', $base_path);
$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$uriPath = str_replace('\\', '/', $uriPath);
$usesFrontControllerInUrl = (strpos($uriPath, '/index.php') !== false) || (isset($_GET['route']) && $_GET['route'] !== '');
define('ROUTER_BASE', BASE_URL . ($usesFrontControllerInUrl ? '/index.php' : ''));

if (!function_exists('route_url')) {
    function route_url($path = '/') {
        $normalizedPath = '/' . ltrim((string)$path, '/');
        if ($normalizedPath === '//') {
            $normalizedPath = '/';
        }
        $base = defined('ROUTER_BASE') ? ROUTER_BASE : (defined('BASE_URL') ? BASE_URL : '');
        if ($normalizedPath === '/') {
            return $base === '' ? '/' : $base;
        }
        return rtrim($base, '/') . $normalizedPath;
    }
}

$request = $_SERVER['REQUEST_URI'];
$usesFrontControllerPath = false;

// Limpiar la query string (ej: ?id=1)
if (strpos($request, '?') !== false) {
    $request = strstr($request, '?', true);
}


if ($base_path !== '/' && strpos($request, $base_path) === 0) {
    $request = substr($request, strlen($base_path));
}

if (strpos($request, '/index.php') === 0) {
    $request = substr($request, strlen('/index.php'));
    $usesFrontControllerPath = true;
}

if (isset($_GET['route']) && is_string($_GET['route']) && $_GET['route'] !== '') {
    $request = '/' . ltrim($_GET['route'], '/');
    $usesFrontControllerPath = true;
}

// Normalizar rutas vacías
if ($request === false || $request === '' || $request === '/') {
    $request = '/';
}

// Eliminar .php al final de la ruta procesada para evitar el error 404
if (strpos($request, '.php') !== false) {
    // Si la ruta no es una de la API, le quitamos el .php
    if (strpos($request, 'api/') === false) {
        $request = str_replace('.php', '', $request);
    }
}


if (strpos($request, 'api/get_viandas.php') !== false) {
    require_once __DIR__ . '/api/get_viandas.php';
    exit;
} elseif (strpos($request, 'api/vianda_image.php') !== false) {
    require_once __DIR__ . '/api/vianda_image.php';
    exit;

// B. Ruta para el Catálogo (Página Principal)
} elseif ($request === '/' || $request === '/index.php' || $request === '/catalogo') {
    require_once __DIR__ . '/../app/controladores/ViandaController.php';
    $controller = new ViandaController();
    $controller->index();

// C. Rutas de Administración de Viandas
} elseif (preg_match('/^\/admin\/viandas$/', $request)) {
    requireLogin();
    require_once __DIR__ . '/../app/controladores/ViandaController.php';
    $controller = new ViandaController();
    $controller->adminIndex(); 
} elseif (preg_match('/^\/admin\/viandas\/create$/', $request)) {
    requireLogin();
    require_once __DIR__ . '/../app/controladores/ViandaController.php';
    $controller = new ViandaController();
    $controller->create();
} elseif (preg_match('/^\/admin\/viandas\/update\/(\d+)$/', $request, $matches)) {
    requireLogin();
    require_once __DIR__ . '/../app/controladores/ViandaController.php';
    $controller = new ViandaController();
    $controller->update($matches[1]);
} elseif (preg_match('/^\/admin\/viandas\/delete\/(\d+)$/', $request, $matches)) {
    requireLogin();
    require_once __DIR__ . '/../app/controladores/ViandaController.php';
    $controller = new ViandaController();
    $controller->delete($matches[1]);

// D. Rutas de Administración de Pedidos
} elseif (preg_match('/^\/admin\/pedidos$/', $request) || preg_match('/^\/admin\/comandas$/', $request)) {
    requireLogin();
    require_once __DIR__ . '/../app/controladores/PedidoController.php';
    $controller = new PedidoController();
    $controller->index();
} elseif (preg_match('/^\/admin\/pedidos\/update\/(\d+)$/', $request, $matches)) {
    requireLogin();
    require_once __DIR__ . '/../app/controladores/PedidoController.php';
    $controller = new PedidoController();
    $controller->updateStatus($matches[1]);

// E. Rutas de Autenticación
} elseif ($request === '/admin') {
    header('Location: ' . route_url('/admin/comandas'));
    exit;
} elseif ($request === '/admin/login' || $request === '/admin/login.php') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        if (login($username, $password)) {
            header('Location: ' . route_url('/admin/comandas'));
        } else {
            header('Location: ' . route_url('/admin/login') . '?error=1');
        }
        exit;
    } else {
        require_once __DIR__ . '/../app/views/admin/login.php';
    }
} elseif ($request === '/admin/logout') {
    logout();
    header('Location: ' . route_url('/admin/login'));
    exit;

// F. Error 404
} else {
    http_response_code(404);
    echo "<h1>404 - No encontrado</h1>";
    echo "Carpeta base: " . BASE_URL . "<br>";
    echo "Ruta procesada: " . htmlspecialchars($request) . "<br>";
    echo "URI Original: " . htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'N/A') . "<br>";
    echo "Script Name: " . htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "<br>";
}
?>