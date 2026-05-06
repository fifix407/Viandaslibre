<?php
// app/views/admin/login.php - Login View

require_once __DIR__ . '/../../../includes/header.php';

if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger">Credenciales incorrectas</div>';
}
?>

<h1>Login Admin de Comandas</h1>
<p class="text-muted">Ingresa para gestionar pedidos y viandas.</p>

<form method="POST" action="<?php echo route_url('/admin/login'); ?>">
    <div class="mb-3">
        <label for="username" class="form-label">Usuario</label>
        <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
</form>

<?php
require_once __DIR__ . '/../../../includes/footer.php';
?>