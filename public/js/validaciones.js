/**
 * Archivo de validaciones y funciones JavaScript globales
 * Sistema ATECOP
 */

// Validación de DNI (8 dígitos)
function validarDNI(dni) {
  const regex = /^\d{8}$/
  return regex.test(dni)
}

// Validación de RUC (11 dígitos)
function validarRUC(ruc) {
  const regex = /^\d{11}$/
  return regex.test(ruc)
}

// Validación de email
function validarEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return regex.test(email)
}

// Validación de teléfono (9 dígitos)
function validarTelefono(telefono) {
  const regex = /^\d{9}$/
  return regex.test(telefono)
}

// Función para validar DNI/RUC con API Perú Dev
async function validarDocumentoAPI(documento, tipo) {
  const btnValidar = document.getElementById("btnValidar")
  const resultadoDiv = document.getElementById("resultadoValidacion")

  if (btnValidar) btnValidar.disabled = true
  if (resultadoDiv) resultadoDiv.innerHTML = '<p class="cargando">Validando...</p>'

  try {
    const response = await fetch(`index.php?modulo=socios&accion=validar_api&documento=${documento}&tipo=${tipo}`)
    const data = await response.json()

    if (data.success) {
      // Auto-rellenar campos
      if (data.data.nombre) {
        document.getElementById("nombrecompleto").value = data.data.nombre
      }
      if (data.data.direccion) {
        document.getElementById("direccion").value = data.data.direccion
      }

      if (resultadoDiv) {
        resultadoDiv.innerHTML = '<p class="exito">✓ Documento validado correctamente</p>'
      }
    } else {
      if (resultadoDiv) {
        resultadoDiv.innerHTML = `<p class="error">✗ ${data.message}</p>`
      }
    }
  } catch (error) {
    if (resultadoDiv) {
      resultadoDiv.innerHTML = '<p class="error">✗ Error al conectar con el servicio de validación</p>'
    }
  } finally {
    if (btnValidar) btnValidar.disabled = false
  }
}

// Confirmación de eliminación/baja
function confirmarAccion(mensaje) {
  return confirm(mensaje || "¿Está seguro de realizar esta acción?")
}

// Validación de formulario de socio
function validarFormularioSocio(event) {
  const dni = document.getElementById("dni").value
  const nombre = document.getElementById("nombrecompleto").value
  const email = document.getElementById("email").value

  const errores = []

  if (!validarDNI(dni) && !validarRUC(dni)) {
    errores.push("El DNI debe tener 8 dígitos o el RUC 11 dígitos")
  }

  if (nombre.trim().length < 3) {
    errores.push("El nombre completo debe tener al menos 3 caracteres")
  }

  if (email && !validarEmail(email)) {
    errores.push("El email no tiene un formato válido")
  }

  if (errores.length > 0) {
    event.preventDefault()
    alert("Errores en el formulario:\n\n" + errores.join("\n"))
    return false
  }

  return true
}

// Validación de formulario de pago
function validarFormularioPago(event) {
  const monto = Number.parseFloat(document.getElementById("monto").value)
  const concepto = document.getElementById("concepto").value
  const archivo = document.getElementById("comprobante").files[0]

  const errores = []

  if (isNaN(monto) || monto <= 0) {
    errores.push("El monto debe ser mayor a 0")
  }

  if (concepto.trim().length < 5) {
    errores.push("El concepto debe tener al menos 5 caracteres")
  }

  if (archivo) {
    const tiposPermitidos = ["application/pdf", "image/jpeg", "image/png"]
    if (!tiposPermitidos.includes(archivo.type)) {
      errores.push("El comprobante debe ser PDF, JPG o PNG")
    }

    const maxSize = 5 * 1024 * 1024 // 5MB
    if (archivo.size > maxSize) {
      errores.push("El comprobante no debe superar los 5MB")
    }
  }

  if (errores.length > 0) {
    event.preventDefault()
    alert("Errores en el formulario:\n\n" + errores.join("\n"))
    return false
  }

  return true
}

// Previsualización de imagen
function previsualizarImagen(input, previewId) {
  if (input.files && input.files[0]) {
    const reader = new FileReader()
    reader.onload = (e) => {
      const preview = document.getElementById(previewId)
      if (preview) {
        preview.src = e.target.result
        preview.style.display = "block"
      }
    }
    reader.readAsDataURL(input.files[0])
  }
}

// Filtro de tabla en tiempo real
function filtrarTabla(inputId, tablaId) {
  const input = document.getElementById(inputId)
  const tabla = document.getElementById(tablaId)

  if (!input || !tabla) return

  input.addEventListener("keyup", function () {
    const filtro = this.value.toLowerCase()
    const filas = tabla.getElementsByTagName("tr")

    for (let i = 1; i < filas.length; i++) {
      const fila = filas[i]
      const texto = fila.textContent.toLowerCase()

      if (texto.includes(filtro)) {
        fila.style.display = ""
      } else {
        fila.style.display = "none"
      }
    }
  })
}

// Inicialización al cargar la página
document.addEventListener("DOMContentLoaded", () => {
  // Agregar validación a formularios
  const formSocio = document.getElementById("formSocio")
  if (formSocio) {
    formSocio.addEventListener("submit", validarFormularioSocio)
  }

  const formPago = document.getElementById("formPago")
  if (formPago) {
    formPago.addEventListener("submit", validarFormularioPago)
  }

  // Inicializar filtros de tabla
  if (document.getElementById("filtroSocios")) {
    filtrarTabla("filtroSocios", "tablaSocios")
  }

  // Botón de validar DNI/RUC
  const btnValidar = document.getElementById("btnValidar")
  if (btnValidar) {
    btnValidar.addEventListener("click", () => {
      const documento = document.getElementById("dni").value
      const tipo = documento.length === 8 ? "dni" : "ruc"

      if (validarDNI(documento) || validarRUC(documento)) {
        validarDocumentoAPI(documento, tipo)
      } else {
        alert("Ingrese un DNI (8 dígitos) o RUC (11 dígitos) válido")
      }
    })
  }
})
