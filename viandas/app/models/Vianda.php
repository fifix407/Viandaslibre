<?php
/**
 * Modelo Vianda - Adaptado para MySQLi (Demo Técnica)
 */

require_once __DIR__ . '/../../config/db.php';

class Vianda {
    private $db;

    /**
     * Constructor de la clase.
     * Establece la conexión a la base de datos global.
     */
    public function __construct() {
        // Usamos la variable global $conexion definida en config/db.php
        global $conexion;
        $this->db = $conexion;

        // Verificamos que la conexión no sea nula para evitar el Fatal Error
        if (!$this->db) {
            die("Error crítico: El modelo Vianda no pudo acceder a la conexión de la base de datos.");
        }
    }

    /**
     * Obtiene todas las viandas del catálogo.
     * 
     * @return array Lista de todas las viandas.
     */
    public function getAll() {
        // Paso 2 del PDF: Usar mysqli_query y mysqli_fetch_all
        $sql = "SELECT * FROM viandas";
        $resultado = mysqli_query($this->db, $sql);
        return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
    }

    /**
     * Obtiene solo las viandas marcadas como disponibles.
     * 
     * @return array Lista de viandas disponibles.
     */
    public function getAllDisponibles() {
        $sql = "SELECT * FROM viandas WHERE disponible = 1";
        $resultado = mysqli_query($this->db, $sql);
        return $resultado ? mysqli_fetch_all($resultado, MYSQLI_ASSOC) : [];
    }

    /**
     * Obtiene una vianda por su ID.
     * 
     * @param int $id ID de la vianda.
     * @return array|null Datos de la vianda.
     */
    public function getById($id) {
        // Usamos sentencias preparadas de MySQLi para seguridad
        $stmt = mysqli_prepare($this->db, "SELECT * FROM viandas WHERE id_vianda = ?");
        mysqli_stmt_bind_param($stmt, "i", $id); // "i" de integer
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($resultado);
    }

    /**
     * Crea una nueva vianda.
     * 
     * @param array $data Datos de la vianda (nombre, descripcion, precio, imagen).
     * @return bool True si la creación fue exitosa.
     */
    public function create($data) {
        $stmt = mysqli_prepare($this->db, "INSERT INTO viandas (nombre, descripcion, precio, imagen_url) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssds", $data['nombre'], $data['descripcion'], $data['precio'], $data['imagen']);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Actualiza los datos de una vianda existente.
     * 
     * @param int $id ID de la vianda.
     * @param array $data Nuevos datos de la vianda.
     * @return bool True si la actualización fue exitosa.
     */
    public function update($id, $data) {
        $stmt = mysqli_prepare($this->db, "UPDATE viandas SET nombre = ?, descripcion = ?, precio = ?, imagen_url = ? WHERE id_vianda = ?");
        mysqli_stmt_bind_param($stmt, "ssdsi", $data['nombre'], $data['descripcion'], $data['precio'], $data['imagen'], $id);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Elimina físicamente una vianda de la base de datos.
     * 
     * @param int $id ID de la vianda.
     * @return bool True si la eliminación fue exitosa.
     */
    public function delete($id) {
        $stmt = mysqli_prepare($this->db, "DELETE FROM viandas WHERE id_vianda = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Realiza un borrado lógico marcando la vianda como no disponible.
     * 
     * @param int $id ID de la vianda.
     * @return bool True si el cambio de estado fue exitoso.
     */
    public function softDelete($id) {
        $stmt = mysqli_prepare($this->db, "UPDATE viandas SET disponible = 0 WHERE id_vianda = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Actualiza el stock o disponibilidad de forma rápida.
     * 
     * @param int $id ID de la vianda.
     * @param int $cantidad Nueva cantidad de stock.
     * @return array Resultado de la operación y metadatos.
     */
    public function updateStockRapido($id, $cantidad) {
        $columnas = [];
        $result = mysqli_query($this->db, "SHOW COLUMNS FROM viandas");
        if ($result) {
            while ($col = mysqli_fetch_assoc($result)) {
                $columnas[] = $col['Field'];
            }
        }

        $usaStock = in_array('stock', $columnas, true);
        $warning = null;

        if ($usaStock) {
            $disponible = $cantidad > 0 ? 1 : 0;
            $stmt = mysqli_prepare($this->db, "UPDATE viandas SET stock = ?, disponible = ? WHERE id_vianda = ?");
            mysqli_stmt_bind_param($stmt, "iii", $cantidad, $disponible, $id);
        } else {
            $disponible = $cantidad > 0 ? 1 : 0;
            $stmt = mysqli_prepare($this->db, "UPDATE viandas SET disponible = ? WHERE id_vianda = ?");
            mysqli_stmt_bind_param($stmt, "ii", $disponible, $id);
            $warning = 'La columna stock no existe. Se actualizo solo disponible segun stock>0.';
        }

        $ok = mysqli_stmt_execute($stmt);
        return [
            'ok' => $ok,
            'warning' => $warning,
            'usa_stock' => $usaStock,
            'disponible' => $disponible,
        ];
    }

    /**
     * Obtiene el listado de categorías de viandas.
     * 
     * @return array Lista de categorías.
     */
    public function getCategorias() {
        $sql = "SELECT id_categoria, nombre FROM categorias ORDER BY nombre ASC";
        $resultado = mysqli_query($this->db, $sql);
        return $resultado ? mysqli_fetch_all($resultado, MYSQLI_ASSOC) : [];
    }
}
?>