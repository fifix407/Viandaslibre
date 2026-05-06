<?php
/**
 * Vista del Catálogo - Demo Técnica
 * Conecta el navegador con el Backend para traer el "Menú del Día"
 */
require_once __DIR__ . '/../../includes/header.php';
?>

<h1 class="text-center my-4">Catálogo de Viandas</h1>

<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h5 mb-3">Carrito de compras</h2>
                    <div id="carrito-vacio" class="text-muted">Todavia no agregaste viandas.</div>
                    <div id="carrito-lista" class="d-none"></div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <strong>Total:</strong>
                        <strong id="carrito-total">$0.00</strong>
                    </div>
                    <hr>
                    <form id="checkout-form" class="row g-2">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="cliente_nombre" placeholder="Nombre" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="cliente_whatsapp" placeholder="WhatsApp" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="direccion_entrega" placeholder="Direccion de entrega" required>
                        </div>
                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-success">Confirmar pedido</button>
                        </div>
                    </form>
                    <div id="checkout-mensaje" class="small mt-2"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="contenedor-viandas" class="row">
        <div class="text-center" id="loader">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p>Buscando las mejores viandas para vos...</p>
        </div>
    </div>

    <!-- Modal de Pedido Confirmado -->
    <div class="modal fade" id="modalPedidoConfirmado" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title">¡Pedido Confirmado!</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center py-5">
            <div class="mb-4">
                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <span style="font-size: 40px;">✓</span>
                </div>
            </div>
            <h4 class="mb-3" id="modal-pedido-id">Pedido #0</h4>
            <p class="mb-4">Tu pedido ha sido recibido correctamente.<br>Pronto lo estaremos preparando para enviarlo.</p>
            <div class="p-3 bg-light rounded">
                <p class="mb-0 text-muted small">Total a pagar:</p>
                <h5 class="mb-0 text-primary"><strong id="modal-pedido-total">$0.00</strong></h5>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-success w-100 py-2" data-bs-dismiss="modal">Entendido</button>
          </div>
        </div>
      </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const contenedor = document.getElementById('contenedor-viandas');
        
        // Definimos la URL de nuestra API interna (Ruta absoluta desde BASE_URL)
        const apiUrl = '<?php echo BASE_URL; ?>/api/get_viandas.php';

        // PASO 3: El fetch pide los datos y los dibuja sin recargar la página
        fetch(apiUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error 500: Falló la conexión con el servidor');
                }
                return response.json();
            })
            .then(payload => {
                // Limpiamos el contenedor (quitamos el spinner de carga)
                contenedor.innerHTML = '';
                const viandas = payload.data || [];

                // Si la API devuelve un error 404 simulado (tabla vacía)
                if (!Array.isArray(viandas) || viandas.length === 0) {
                    contenedor.innerHTML = '<p class="text-center alert alert-warning">No hay viandas disponibles en este momento.</p>';
                    return;
                }

                // Bucle .forEach para crear las cards de Bootstrap dinámicamente
                viandas.forEach(vianda => {
                    const rutaImagen = vianda.imagen_src || '';
                    const fallbackImagen = vianda.imagen_fallback || 'data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%27http%3A//www.w3.org/2000/svg%27%20width%3D%27800%27%20height%3D%27600%27%20viewBox%3D%270%200%20800%20600%27%3E%3Crect%20width%3D%27800%27%20height%3D%27600%27%20fill%3D%27%23f3f4f6%27/%3E%3Crect%20x%3D%27180%27%20y%3D%27140%27%20width%3D%27440%27%20height%3D%27320%27%20rx%3D%2720%27%20fill%3D%27%23e5e7eb%27/%3E%3Ccircle%20cx%3D%27300%27%20cy%3D%27250%27%20r%3D%2745%27%20fill%3D%27%23d1d5db%27/%3E%3Cpath%20d%3D%27M225%20410l110-95%2090%2075%2070-55%2080%2075H225z%27%20fill%3D%27%23cbd5e1%27/%3E%3Ctext%20x%3D%27400%27%20y%3D%27500%27%20text-anchor%3D%27middle%27%20font-size%3D%2730%27%20fill%3D%27%236b7280%27%20font-family%3D%27Arial%2C%20sans-serif%27%3ESin%20imagen%3C/text%3E%3C/svg%3E';

                    // Inyectamos el HTML de la tarjeta (Card)
                    contenedor.innerHTML += `
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <img src="${rutaImagen}"
                                 class="card-img-top" 
                                 alt="${vianda.nombre}" 
                                 style="height: 250px; width: 100%; object-fit: cover; object-position: center;"
                                 onerror="this.onerror=null; this.src='${fallbackImagen}'">
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-dark">${vianda.nombre}</h5>
                                <p class="card-text text-muted small">${vianda.descripcion}</p>
                                <div class="mt-auto">
                                    <p class="card-text h5 text-primary mb-3"><strong>$${vianda.precio}</strong></p>
                                    <button
                                        class="btn btn-primary w-100 shadow-sm btn-agregar-carrito"
                                        data-id="${vianda.id_vianda}"
                                        data-nombre="${vianda.nombre}"
                                        data-precio="${vianda.precio}"
                                    >
                                        Agregar al pedido
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                });

                if (window.CarritoViandas) {
                    window.CarritoViandas.bindAddButtons();
                }
            })
            .catch(error => {
                // Manejo de error de conexión (Backend caído o error de red)
                contenedor.innerHTML = `
                    <div class="alert alert-danger text-center">
                        <h5>Error de conexión con la API</h5>
                        <p>No pudimos traer el menú. Por favor, verifica la conexión a la base de datos.</p>
                    </div>`;
                console.error('Error REST API:', error);
            });
    });
</script>
<script>
    window.VIANDAS_BASE_URL = '<?php echo BASE_URL; ?>';
</script>
<script src="<?php echo BASE_URL; ?>/js/carrito.js?v=1.1"></script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>