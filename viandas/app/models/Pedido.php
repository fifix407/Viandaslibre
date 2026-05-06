<?php
// app/models/Pedido.php - Pedido Model (MySQLi)

require_once __DIR__ . '/../../config/db.php';

class Pedido {
    private $db;

    /**
     * Constructor de la clase.
     * Inicializa la conexión a la base de datos usando la variable global $conexion.
     */
    public function __construct() {
        global $conexion;
        $this->db = $conexion;
    }

    /**
     * Obtiene todos los pedidos registrados en la base de datos.
     * 
     * @return array Lista de pedidos ordenados por fecha descendente.
     */
    public function getAll() {
        $sql = "SELECT * FROM pedidos ORDER BY fecha_pedido DESC, id_pedido DESC";
        $result = mysqli_query($this->db, $sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    /**
     * Obtiene un pedido específico por su ID.
     * 
     * @param int $id ID del pedido.
     * @return array|null Datos del pedido o null si no se encuentra.
     */
    public function getById($id) {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM pedidos WHERE id_pedido = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    /**
     * Crea un nuevo pedido simple.
     * 
     * @param array $data Datos del cliente y total.
     * @return bool True si la creación fue exitosa, false en caso contrario.
     */
    public function create($data) {
        $stmt = mysqli_prepare(
            $this->db,
            "INSERT INTO pedidos (cliente_nombre, cliente_whatsapp, direccion_entrega, total_pago, fecha_pedido) VALUES (?, ?, ?, ?, NOW())"
        );
        mysqli_stmt_bind_param(
            $stmt,
            "sssd",
            $data['cliente_nombre'],
            $data['cliente_whatsapp'],
            $data['direccion_entrega'],
            $data['total_pago']
        );
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Actualiza el estado de un pedido.
     * 
     * @param int $id ID del pedido.
     * @param string $estado Nuevo estado ('Pendiente', 'En Cocina', 'Enviado', 'Entregado').
     * @return bool True si la actualización fue exitosa.
     */
    public function updateStatus($id, $estado) {
        $stmt = mysqli_prepare($this->db, "UPDATE pedidos SET estado = ? WHERE id_pedido = ?");
        mysqli_stmt_bind_param($stmt, "si", $estado, $id);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Elimina un pedido y sus detalles (vía ON DELETE CASCADE).
     * 
     * @param int $id ID del pedido.
     * @return bool True si la eliminación fue exitosa.
     */
    public function delete($id) {
        $stmt = mysqli_prepare($this->db, "DELETE FROM pedidos WHERE id_pedido = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Crea un pedido completo incluyendo su detalle.
     * Realiza validaciones de stock/disponibilidad y usa transacciones para asegurar la integridad.
     * 
     * @param array $payload Datos del cliente e items del carrito.
     * @return array Resultado de la operación ['ok' => bool, 'message' => string, 'id_pedido' => int, 'total_pago' => float]
     */
    public function createWithDetails($payload) {
        if (empty($payload['cliente_nombre']) || empty($payload['cliente_whatsapp']) || empty($payload['direccion_entrega'])) {
            return ['ok' => false, 'message' => 'Faltan datos del cliente obligatorios.'];
        }

        if (empty($payload['items']) || !is_array($payload['items'])) {
            return ['ok' => false, 'message' => 'El carrito esta vacio.'];
        }

        $itemsCalculados = [];
        $totalCalculado = 0.0;

        $stmtVianda = mysqli_prepare(
            $this->db,
            "SELECT id_vianda, precio, disponible FROM viandas WHERE id_vianda = ? LIMIT 1"
        );

        foreach ($payload['items'] as $item) {
            $idVianda = isset($item['id_vianda']) ? (int) $item['id_vianda'] : 0;
            $cantidad = isset($item['cantidad']) ? (int) $item['cantidad'] : 0;

            if ($idVianda <= 0 || $cantidad <= 0) {
                return ['ok' => false, 'message' => 'Hay items con formato invalido.'];
            }

            mysqli_stmt_bind_param($stmtVianda, "i", $idVianda);
            mysqli_stmt_execute($stmtVianda);
            $resultVianda = mysqli_stmt_get_result($stmtVianda);
            $vianda = $resultVianda ? mysqli_fetch_assoc($resultVianda) : null;

            if (!$vianda || (int) $vianda['disponible'] !== 1) {
                return ['ok' => false, 'message' => 'Una o mas viandas no estan disponibles.'];
            }

            $precioUnitario = (float) $vianda['precio'];
            $subtotal = $precioUnitario * $cantidad;
            $totalCalculado += $subtotal;

            $itemsCalculados[] = [
                'id_vianda' => $idVianda,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
            ];
        }

        mysqli_begin_transaction($this->db);
        try {
            $stmtPedido = mysqli_prepare(
                $this->db,
                "INSERT INTO pedidos (cliente_nombre, cliente_whatsapp, direccion_entrega, total_pago, estado) VALUES (?, ?, ?, ?, 'Pendiente')"
            );
            mysqli_stmt_bind_param(
                $stmtPedido,
                "sssd",
                $payload['cliente_nombre'],
                $payload['cliente_whatsapp'],
                $payload['direccion_entrega'],
                $totalCalculado
            );

            if (!mysqli_stmt_execute($stmtPedido)) {
                throw new Exception('Error al insertar pedido: ' . mysqli_error($this->db));
            }

            $idPedido = (int) mysqli_insert_id($this->db);
            $stmtDetalle = mysqli_prepare(
                $this->db,
                "INSERT INTO detalle_pedido (id_pedido, id_vianda, cantidad, precio_unitario) VALUES (?, ?, ?, ?)"
            );

            foreach ($itemsCalculados as $itemCalculado) {
                mysqli_stmt_bind_param(
                    $stmtDetalle,
                    "iiid",
                    $idPedido,
                    $itemCalculado['id_vianda'],
                    $itemCalculado['cantidad'],
                    $itemCalculado['precio_unitario']
                );
                if (!mysqli_stmt_execute($stmtDetalle)) {
                    throw new Exception('Error al insertar detalle: ' . mysqli_error($this->db));
                }
            }

            mysqli_commit($this->db);

            return [
                'ok' => true,
                'id_pedido' => $idPedido,
                'total_pago' => round($totalCalculado, 2),
            ];
        } catch (Throwable $e) {
            mysqli_rollback($this->db);
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }
}
?>