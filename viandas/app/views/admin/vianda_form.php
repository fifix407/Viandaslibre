<?php
// app/views/admin/vianda_form.php - Vianda Form View

require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/auth.php';
requireLogin();
?>

<h1><?php echo isset($vianda) ? 'Editar Vianda' : 'Nueva Vianda'; ?></h1>

<form method="POST">
    <label>Nombre:</label>
    <input type="text" name="nombre" value="<?php echo $vianda['nombre'] ?? ''; ?>" required>

    <label>Descripción:</label>
    <textarea name="descripcion" required><?php echo $vianda['descripcion'] ?? ''; ?></textarea>

    <label>Precio:</label>
    <input type="number" step="0.01" name="precio" value="<?php echo $vianda['precio'] ?? ''; ?>" required>

    <label>Imagen:</label>
    <input type="text" name="imagen_url" value="<?php echo $vianda['imagen_url'] ?? ''; ?>" required>

    <button type="submit"><?php echo isset($vianda) ? 'Actualizar' : 'Crear'; ?></button>
</form>

<?php
require_once __DIR__ . '/../../../includes/footer.php';
?>