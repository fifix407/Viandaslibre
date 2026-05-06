<?php
// app/controladores/ViandaController.php - Vianda Controller

require_once __DIR__ . '/../models/Vianda.php';

class ViandaController {
    private $viandaModel;

    /**
     * Constructor del controlador.
     * Instancia el modelo de Vianda.
     */
    public function __construct() {
        $this->viandaModel = new Vianda();
    }

    /**
     * Muestra el catálogo de viandas para el usuario final.
     * Carga la vista catalogo.php.
     */
    public function index() {
        $viandas = $this->viandaModel->getAll();
        require_once __DIR__ . '/../views/catalogo.php';
    }

    /**
     * Muestra el panel de administración de viandas.
     * Carga la vista admin/viandas.php.
     */
    public function adminIndex() {
        $viandas = $this->viandaModel->getAll();
        require_once __DIR__ . '/../views/admin/viandas.php';
    }

    /**
     * Muestra la información de una vianda específica.
     * 
     * @param int $id ID de la vianda.
     */
    public function show($id) {
        $vianda = $this->viandaModel->getById($id);
        // Render view for single vianda
    }

    /**
     * Crea una nueva vianda. Si es GET muestra el formulario, si es POST guarda los datos.
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'],
                'precio' => $_POST['precio'],
                'imagen' => $_POST['imagen_url']
            ];
            $this->viandaModel->create($data);
            header('Location: ' . route_url('/admin/viandas'));
        } else {
            require_once __DIR__ . '/../views/admin/vianda_form.php';
        }
    }

    /**
     * Actualiza una vianda existente. Si es GET muestra el formulario con datos, si es POST actualiza.
     * 
     * @param int $id ID de la vianda.
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'],
                'precio' => $_POST['precio'],
                'imagen' => $_POST['imagen_url']
            ];
            $this->viandaModel->update($id, $data);
            header('Location: ' . route_url('/admin/viandas'));
        } else {
            $vianda = $this->viandaModel->getById($id);
            require_once __DIR__ . '/../views/admin/vianda_form.php';
        }
    }

    /**
     * Elimina una vianda del catálogo.
     * 
     * @param int $id ID de la vianda.
     */
    public function delete($id) {
        $this->viandaModel->delete($id);
        header('Location: ' . route_url('/admin/viandas'));
    }
}
?>