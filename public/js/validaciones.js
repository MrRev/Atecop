/**
 * Archivo de validaciones y funciones JavaScript globales
 * Sistema ATECOP
 * --- VERSIÓN CORREGIDA Y FINAL ---
 */

// --- FUNCIÓN DE VALIDACIÓN DE API (CORREGIDA) ---
async function validarDocumentoAPI(documento, tipo) {
    const btnValidar = document.getElementById("btnValidar");
    const resultadoDiv = document.getElementById("resultadoValidacion");
    
    // Validar formato
    if (tipo === 'dni' && !validarDNI(documento)) {
        mostrarError("El DNI debe tener exactamente 8 dígitos");
        return;
    } else if (tipo === 'ruc' && !validarRUC(documento)) {
        mostrarError("El RUC debe tener exactamente 11 dígitos");
        return;
    }
    
    // Deshabilitar botón y mostrar carga
    if (btnValidar) btnValidar.disabled = true;
    mostrarMensaje("Validando documento...", "info");

    try {
        // Añadimos el bloque 'headers' para que la sesión AJAX sea reconocida
        const response = await fetch(`index.php?modulo=socios&accion=validarDocumento&documento=${documento}&tipo=${tipo}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest' // <-- Esto evita la redirección
            },
            credentials: 'same-origin'
        });

        // Manejo de respuesta
        if (!response.ok) {
            // Si la sesión expiró (error 401) o hubo otro error
            if (response.status === 401) {
                mostrarError("Sesión expirada. Redirigiendo al login...");
                window.location.href = 'index.php?modulo=seguridad&accion=login';
            } else {
                // Otro error (ej. 500 en el servidor)
                mostrarError("Error del servidor. Revise la consola.");
                console.error('Error en respuesta:', response.status, await response.text());
            }
            return;
        }

        const data = await response.json();

        if (data.success) {
            // Auto-rellenar campos
            if (data.data.nombre) {
                document.getElementById("nombrecompleto").value = data.data.nombre;
            }
            if (data.data.direccion) {
                document.getElementById("direccion").value = data.data.direccion;
            }
            mostrarMensaje("✓ Documento validado correctamente", "success");
        } else {
            // Mostrar error devuelto por la API (ej. "DNI no encontrado")
            mostrarError(data.mensaje || data.message || 'No se pudo validar');
        }

    } catch (error) {
        // Error de red o si la respuesta no fue JSON
        console.error('Error en fetch:', error);
        mostrarError("✗ Error al conectar. Revise la consola.");
    } finally {
        if (btnValidar) btnValidar.disabled = false;
    }
}


// --- INICIALIZACIÓN PRINCIPAL (UNIFICADA) ---
document.addEventListener("DOMContentLoaded", () => {
    
    // Botón de validar DNI/RUC
    const btnValidar = document.getElementById("btnValidar");
    if (btnValidar) {
        btnValidar.addEventListener("click", () => { // Eliminamos 'evento'
            const dniInput = document.getElementById("dni");
            if (!dniInput) return;

            const documento = dniInput.value.trim();
            const tipo = documento.length === 8 ? "dni" : (documento.length === 11 ? "ruc" : null);

            if (tipo) {
                // Llamada corregida (sin 'evento')
                validarDocumentoAPI(documento, tipo);
            } else {
                mostrarError("Ingrese un DNI (8 dígitos) o RUC (11 dígitos) válido");
            }
        });
    }

    // Agregar validación a formularios
    const formSocio = document.getElementById("formSocio");
    if (formSocio) {
        formSocio.addEventListener("submit", validarFormularioSocio);
    }

    const formPago = document.getElementById("formPago");
    if (formPago) {
        formPago.addEventListener("submit", validarFormularioPago);
    }

    // Inicializar filtros de tabla
    if (document.getElementById("filtroSocios")) {
        filtrarTabla("filtroSocios", "tablaSocios");
    }
});


// --- FUNCIONES AUXILIARES (Sin cambios) ---

// Validación de DNI (8 dígitos)
function validarDNI(dni) {
    const regex = /^\d{8}$/;
    return regex.test(dni);
}

// Validación de RUC (11 dígitos)
function validarRUC(ruc) {
    const regex = /^\d{11}$/;
    return regex.test(ruc);
}

// Validación de email
function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// Funciones auxiliares para mostrar mensajes
function mostrarError(mensaje) {
    const resultadoDiv = document.getElementById("resultadoValidacion");
    if (resultadoDiv) {
        resultadoDiv.innerHTML = `<p class="error">✗ ${mensaje}</p>`;
    }
}

function mostrarMensaje(mensaje, tipo = 'info') {
    const resultadoDiv = document.getElementById("resultadoValidacion");
    if (resultadoDiv) {
        const clase = tipo === 'error' ? 'error' : (tipo === 'success' ? 'exito' : 'info');
        resultadoDiv.innerHTML = `<p class="${clase}">${mensaje}</p>`;
    }
}

// Confirmación de eliminación/baja
function confirmarAccion(mensaje) {
    return confirm(mensaje || "¿Está seguro de realizar esta acción?");
}

// Validación de formulario de socio
function validarFormularioSocio(event) {
    const dni = document.getElementById("dni").value;
    const nombre = document.getElementById("nombrecompleto").value;
    const email = document.getElementById("email").value;
    const errores = [];

    if (!validarDNI(dni) && !validarRUC(dni)) {
        errores.push("El DNI debe tener 8 dígitos o el RUC 11 dígitos");
    }
    if (nombre.trim().length < 3) {
        errores.push("El nombre completo debe tener al menos 3 caracteres");
    }
    if (email && email.length > 0 && !validarEmail(email)) { // Solo validar si no está vacío
        errores.push("El email no tiene un formato válido");
    }

    if (errores.length > 0) {
        event.preventDefault();
        alert("Errores en el formulario:\n\n" + errores.join("\n"));
        return false;
    }
    return true;
}

// Validación de formulario de pago
function validarFormularioPago(event) {
    const monto = Number.parseFloat(document.getElementById("monto").value);
    const concepto = document.getElementById("concepto").value;
    const archivo = document.getElementById("comprobante") ? document.getElementById("comprobante").files[0] : null;
    const errores = [];

    if (isNaN(monto) || monto <= 0) {
        errores.push("El monto debe ser mayor a 0");
    }
    if (concepto.trim().length < 5) {
        errores.push("El concepto debe tener al menos 5 caracteres");
    }
    if (archivo) {
        const tiposPermitidos = ["application/pdf", "image/jpeg", "image/png"];
        if (!tiposPermitidos.includes(archivo.type)) {
            errores.push("El comprobante debe ser PDF, JPG o PNG");
        }
        const maxSize = 5 * 1024 * 1024; // 5MB
        if (archivo.size > maxSize) {
            errores.push("El comprobante no debe superar los 5MB");
        }
    }

    if (errores.length > 0) {
        event.preventDefault();
        alert("Errores en el formulario:\n\n" + errores.join("\n"));
        return false;
    }
    return true;
}

// Previsualización de imagen
function previsualizarImagen(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = "block";
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Filtro de tabla en tiempo real
function filtrarTabla(inputId, tablaId) {
    const input = document.getElementById(inputId);
    const tabla = document.getElementById(tablaId);
    if (!input || !tabla) return;

    input.addEventListener("keyup", function () {
        const filtro = this.value.toLowerCase();
        const filas = tabla.getElementsByTagName("tr");
        for (let i = 1; i < filas.length; i++) {
            const fila = filas[i];
            const texto = fila.textContent.toLowerCase();
            if (texto.includes(filtro)) {
                fila.style.display = "";
            } else {
                fila.style.display = "none";
            }
        }
    });
}