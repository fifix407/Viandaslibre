<?php
// app/views/admin/pedidos.php - Pedidos Admin View

require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/auth.php';
requireLogin();
?>

<h1>Administrar Pedidos</h1>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Total</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pedidos as $pedido): ?>
            <tr>
                <td><?php echo $pedido['id_pedido']; ?></td>
                <td><?php echo $pedido['cliente_nombre']; ?></td>
                <td>$<?php echo $pedido['total_pago']; ?></td>
                <td><?php echo substr($pedido['fecha_pedido'], 8, 2)."-".substr($pedido['fecha_pedido'], 5, 2)."-".substr($pedido['fecha_pedido'], 0, 4)." ".substr($pedido['fecha_pedido'], 10, 9) ; ?></td>
                <td><?php echo $pedido['estado']; ?></td>
                <td>
                    <?php if($pedido['estado'] != "Entregado"){?>
                    <form method="POST" action="<?php echo route_url('/admin/pedidos/update/' . $pedido['id_pedido']); ?>">
                        <select name="estado">
                            <option value="Pendiente">Pendiente</option>
                            <option value="En Cocina">En Cocina</option>
                            <option value="Enviado">Enviado</option>
                            <option value="Entregado">Entregado</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-success">Actualizar</button>
                    </form>
                    <?php
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
require_once __DIR__ . '/../../../includes/footer.php';
?>