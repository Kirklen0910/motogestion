// Funciones auxiliares globales
function confirmDelete(message) {
    return confirm(message || '¿Estás seguro de eliminar este registro?');
}

function formatCurrency(amount) {
    return 'L ' + parseFloat(amount).toFixed(2);
}

// Auto cerrar alertas después de 3 segundos
setTimeout(() => {
    let alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 3000);