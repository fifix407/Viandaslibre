// public/js/script.js - Funciones generales y utilidades

// Este archivo ya no maneja el carrito ni el catálogo directamente
// para evitar conflictos con carrito.js y la lógica integrada en catalogo.php.

// Si necesitas funciones globales que se usen en todo el sitio, agrégalas aquí.

document.addEventListener('DOMContentLoaded', () => {
    console.log("ViandaLibre: Scripts generales cargados.");
    
    // Aquí podrías agregar lógica para el menú móvil, tooltips, etc.
});

/*
// Ejemplo de verificación de sesión (opcional, ahora se maneja en PHP)
async function verificarSesion() {
    const path = window.location.pathname;
    if (path.includes('/admin/') && !path.includes('/admin/login')) {
        try {
            const res = await fetch(window.VIANDAS_BASE_URL + '/api/auth_status.php');
            const result = await res.json();
            if (result.status === 'success' && result.data && !result.data.admin_logged_in) {
                window.location.href = window.VIANDAS_BASE_URL + '/admin/login';
            }
        } catch (e) {
            console.error("Error verificando sesión:", e);
        }
    }
}
*/