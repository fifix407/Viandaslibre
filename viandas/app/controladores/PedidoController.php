<?php
// app/controladores/PedidoController.php - Pedido Controller

require_once __DIR__ . '/../models/Pedido.php';

class PedidoController {
    private $pedidoModel;

    /**
     * Constructor del controlador.
     * Instancia el modelo de Pedido.
     */
    public function __construct() {
        $this->pedidoModel = new Pedido();
    }

    /**
     * Muestra el listado de pedidos para el administrador.
     * Carga la vista admin/pedidos.php.
     */
    public function index() {
        $pedidos = $this->pedidoModel->getAll();
        require_once __DIR__ . '/../views/admin/pedidos.php';
    }

    /**
     * Muestra el detalle de un pedido específico.
     * 
     * @param int $id ID del pedido.
     */
    public function show($id) {
        $pedido = $this->pedidoModel->getById($id);
        // Render view for single pedido
    }

    /**
     * Crea un nuevo pedido desde un formulario POST.
     * Redirecciona al listado de pedidos tras la creación.
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'cliente_nombre' => $_POST['cliente'] ?? '',
                'cliente_whatsapp' => $_POST['whatsapp'] ?? '',
                'direccion_entrega' => $_POST['direccion'] ?? '',
                'total_pago' => $_POST['total'] ?? 0
            ];
            $this->pedidoModel->create($data);
            header('Location: ' . route_url('/pedidos'));
        } else {
            // Show order form
        }
    }

    /**
     * Actualiza el estado de un pedido (ej: de Pendiente a Entregado).
     * 
     * @param int $id ID del pedido.
     */
    public function updateStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $estado = $_POST['estado'];
            $this->pedidoModel->updateStatus($id, $estado);
            header('Location: ' . route_url('/admin/pedidos'));
        }
    }

    /**
     * Elimina un pedido de la base de datos.
     * 
     * @param int $id ID del pedido.
     */
    public function delete($id) {
        $this->pedidoModel->delete($id);
        header('Location: ' . route_url('/admin/pedidos'));
    }
}
?>