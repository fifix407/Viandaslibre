/* public/js/carrito.js */
(function () {
    const STORAGE_KEY = 'viandas_carrito';
    const carrito = new Map();

    function cargar() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return;
            const parsed = JSON.parse(raw);
            if (!Array.isArray(parsed)) return;
            parsed.forEach((item) => {
                if (item.id_vianda && item.cantidad > 0) {
                    carrito.set(String(item.id_vianda), item);
                }
            });
        } catch (e) {
            console.warn('No se pudo recuperar carrito', e);
        }
    }

    function guardar() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(Array.from(carrito.values())));
    }

    function total() {
        let acum = 0;
        carrito.forEach((item) => {
            acum += Number(item.precio) * Number(item.cantidad);
        });
        return acum;
    }

    function render() {
        const vacio = document.getElementById('carrito-vacio');
        const lista = document.getElementById('carrito-lista');
        const totalEl = document.getElementById('carrito-total');
        if (!vacio || !lista || !totalEl) return;

        const items = Array.from(carrito.values());
        if (!items.length) {
            vacio.classList.remove('d-none');
            lista.classList.add('d-none');
            lista.innerHTML = '';
            totalEl.textContent = '$0.00';
            return;
        }

        vacio.classList.add('d-none');
        lista.classList.remove('d-none');

        lista.innerHTML = items.map((item) => `
            <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                <div>
                    <strong>${item.nombre}</strong><br>
                    <span class="text-muted small">$${Number(item.precio).toFixed(2)} c/u</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-secondary btn-cant" data-id="${item.id_vianda}" data-delta="-1">-</button>
                    <span>${item.cantidad}</span>
                    <button class="btn btn-sm btn-outline-secondary btn-cant" data-id="${item.id_vianda}" data-delta="1">+</button>
                    <button class="btn btn-sm btn-outline-danger btn-quitar" data-id="${item.id_vianda}">x</button>
                </div>
            </div>
        `).join('');

        totalEl.textContent = '$' + total().toFixed(2);
    }

    function agregar(item) {
        const key = String(item.id_vianda);
        const existe = carrito.get(key);
        if (existe) {
            existe.cantidad += 1;
            carrito.set(key, existe);
        } else {
            carrito.set(key, {
                id_vianda: Number(item.id_vianda),
                nombre: String(item.nombre || ''),
                precio: Number(item.precio || 0),
                cantidad: 1
            });
        }
        guardar();
        render();
    }

    function cambiarCantidad(id, delta) {
        const key = String(id);
        const item = carrito.get(key);
        if (!item) return;
        item.cantidad += delta;
        if (item.cantidad <= 0) {
            carrito.delete(key);
        } else {
            carrito.set(key, item);
        }
        guardar();
        render();
    }

    function quitar(id) {
        carrito.delete(String(id));
        guardar();
        render();
    }

    function bindAddButtons() {
        document.querySelectorAll('.btn-agregar-carrito').forEach((btn) => {
            btn.addEventListener('click', function () {
                agregar({
                    id_vianda: this.dataset.id,
                    nombre: this.dataset.nombre,
                    precio: this.dataset.precio
                });
            });
        });
    }

    function bindDelegados() {
        const lista = document.getElementById('carrito-lista');
        if (!lista) return;
        lista.addEventListener('click', function (event) {
            const target = event.target;
            if (target.classList.contains('btn-cant')) {
                cambiarCantidad(target.dataset.id, Number(target.dataset.delta));
            }
            if (target.classList.contains('btn-quitar')) {
                quitar(target.dataset.id);
            }
        });
    }

    function bindCheckout() {
        const form = document.getElementById('checkout-form');
        const msg = document.getElementById('checkout-mensaje');
        if (!form || !msg) return;

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            const items = Array.from(carrito.values()).map((item) => ({
                id_vianda: item.id_vianda,
                cantidad: item.cantidad
            }));
            if (!items.length) {
                msg.className = 'small mt-2 text-danger';
                msg.textContent = 'El carrito esta vacio.';
                return;
            }

            const formData = new FormData(form);
            const payload = {
                cliente_nombre: String(formData.get('cliente_nombre') || '').trim(),
                cliente_whatsapp: String(formData.get('cliente_whatsapp') || '').trim(),
                direccion_entrega: String(formData.get('direccion_entrega') || '').trim(),
                items: items
            };

            try {
                const baseUrl = (window.VIANDAS_BASE_URL || '').replace(/\/$/, '');
                const res = await fetch(baseUrl + '/api/save_pedidos.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (!res.ok || data.status !== 'success') {
                    throw new Error(data.message || 'No se pudo guardar el pedido.');
                }

                console.log("Pedido guardado con éxito:", data);
                carrito.clear();
                guardar();
                render();
                form.reset();
                
                // Mostrar el modal de confirmación
                try {
                    console.log("Intentando mostrar modal...");
                    const modalEl = document.getElementById('modalPedidoConfirmado');
                    if (modalEl && typeof bootstrap !== 'undefined') {
                        document.getElementById('modal-pedido-id').textContent = 'Pedido #' + data.data.id_pedido;
                        document.getElementById('modal-pedido-total').textContent = '$' + Number(data.data.total_pago).toFixed(2);
                        const modal = new bootstrap.Modal(modalEl);
                        modal.show();
                        console.log("Modal mostrado correctamente.");
                    } else {
                        console.error("Bootstrap o el modal no están disponibles. El: " + !!modalEl + ", BS: " + typeof bootstrap);
                        alert('¡Pedido realizado con éxito! Número de pedido: #' + data.data.id_pedido);
                    }
                } catch (modalError) {
                    console.error("Error al mostrar el modal:", modalError);
                    alert('¡Pedido realizado con éxito! Número de pedido: #' + data.data.id_pedido);
                }

                msg.className = 'small mt-2 text-success';
                msg.textContent = 'Pedido #' + data.data.id_pedido + ' guardado con éxito.';
            } catch (error) {
                console.error("Error en el proceso de finalizar pedido:", error);
                msg.className = 'small mt-2 text-danger';
                msg.textContent = error.message;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        cargar();
        render();
        bindAddButtons();
        bindDelegados();
        bindCheckout();
    });

    window.CarritoViandas = {
        bindAddButtons
    };
})();
