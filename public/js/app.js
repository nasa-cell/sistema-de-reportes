/**
 * JavaScript - TecnoSoluciones S.A.
 * Funciones globales de la aplicación
 */

// Mostrar confirmación antes de eliminar
function confirmarEliminar(mensaje = '¿Estás seguro de que deseas eliminar esto? Esta acción no se puede deshacer.') {
    return confirm(mensaje);
}

// Validar que un formulario tenga datos
function validarFormulario(formularioId) {
    const campos = document.querySelector('#' + formularioId).querySelectorAll('input, textarea, select');
    let valido = true;
    
    campos.forEach(campo => {
        if (!campo.value.trim()) {
            campo.style.borderColor = '#dc3545';
            valido = false;
        } else {
            campo.style.borderColor = '';
        }
    });
    
    return valido;
}

// Mostrar/Ocultar elementos
function toggleElemento(elementoId) {
    const elemento = document.getElementById(elementoId);
    if (elemento) {
        elemento.style.display = elemento.style.display === 'none' ? 'block' : 'none';
    }
}

// Copiar texto al portapapeles
function copiarAlPortapapeles(texto) {
    navigator.clipboard.writeText(texto).then(() => {
        alert('¡Copiado al portapapeles!');
    }).catch(() => {
        alert('Error al copiar');
    });
}

// Formatear moneda
function formatearMoneda(valor) {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    }).format(valor);
}

// Formatear fecha
function formatearFecha(fecha) {
    return new Date(fecha).toLocaleDateString('es-MX');
}

// Actualizar barra de progreso en tiempo real
function actualizarProgreso(elementoId, porcentaje) {
    const elemento = document.getElementById(elementoId);
    if (elemento) {
        elemento.style.width = porcentaje + '%';
    }
}

// Validar correo
function validarCorreo(correo) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(correo);
}

// Mostrar alerta personalizada
function mostrarAlerta(tipo, mensaje) {
    const alerta = document.createElement('div');
    alerta.className = 'alerta alerta-' + tipo;
    alerta.textContent = mensaje;
    alerta.style.marginBottom = '20px';
    
    const principal = document.querySelector('main') || document.querySelector('.contenido-usuario') || document.querySelector('.contenido-admin');
    if (principal) {
        principal.insertBefore(alerta, principal.firstChild);
        
        setTimeout(() => {
            alerta.style.opacity = '0';
            setTimeout(() => alerta.remove(), 300);
        }, 5000);
    }
}

// Enviar formulario por AJAX
function enviarFormularioAjax(formularioId, urlAccion) {
    const formulario = document.getElementById(formularioId);
    if (!formulario) return;
    
    const formData = new FormData(formulario);
    
    fetch(urlAccion, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.exito) {
            mostrarAlerta('exito', data.mensaje);
            if (data.redirigir) {
                setTimeout(() => {
                    window.location.href = data.redirigir;
                }, 1500);
            }
        } else {
            mostrarAlerta('error', data.mensaje);
        }
    })
    .catch(error => {
        mostrarAlerta('error', 'Error en la solicitud: ' + error);
    });
}

// Descargar reporte
function descargarReporte(ruta) {
    window.location.href = ruta;
}

// Mostrar/ocultar formulario de rechazo y gestionar required/disabled
function mostrarFormRechazo() {
    const fr = document.getElementById('form-rechazo');
    if (!fr) return;
    fr.style.display = 'block';
    const ta = fr.querySelector('textarea[name="motivo_rechazo"]');
    if (ta) {
        ta.disabled = false;
        ta.required = true;
        ta.focus();
    }
}

function ocultarFormRechazo() {
    const fr = document.getElementById('form-rechazo');
    if (!fr) return;
    fr.style.display = 'none';
    const ta = fr.querySelector('textarea[name="motivo_rechazo"]');
    if (ta) {
        ta.disabled = true;
        ta.required = false;
        ta.value = '';
    }
}

// Inicialización en carga del documento
document.addEventListener('DOMContentLoaded', function() {
    // Validación de formularios con Enter
    document.querySelectorAll('.campo-entrada').forEach(campo => {
        campo.addEventListener('keypress', function(event) {
            if (event.key === 'Enter' && this.tagName !== 'TEXTAREA') {
                event.preventDefault();
                this.form.submit();
            }
        });
    });
    // Asegurar que el formulario de rechazo esté deshabilitado inicialmente
    ocultarFormRechazo();
});
