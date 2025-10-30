/**
 * Scripts Globales - Sistema ATECOP
 */

// Confirmación para acciones destructivas
function confirmarAccion(mensaje) {
  return confirm(mensaje || "¿Está seguro de realizar esta acción?")
}

// Validación de formularios
document.addEventListener("DOMContentLoaded", () => {
  // Auto-ocultar alertas después de 5 segundos
  const alertas = document.querySelectorAll(".alert")
  alertas.forEach((alerta) => {
    setTimeout(() => {
      alerta.style.opacity = "0"
      setTimeout(() => {
        alerta.remove()
      }, 300)
    }, 5000)
  })

  // Validación de DNI (8 dígitos)
  const inputsDni = document.querySelectorAll('input[name="dni"]')
  inputsDni.forEach((input) => {
    input.addEventListener("blur", function () {
      const dni = this.value.trim()
      if (dni && dni.length !== 8) {
        alert("El DNI debe tener 8 dígitos")
        this.focus()
      }
    })
  })

  // Validación de RUC (11 dígitos)
  const inputsRuc = document.querySelectorAll('input[name="ruc"]')
  inputsRuc.forEach((input) => {
    input.addEventListener("blur", function () {
      const ruc = this.value.trim()
      if (ruc && ruc.length !== 11) {
        alert("El RUC debe tener 11 dígitos")
        this.focus()
      }
    })
  })

  // Validación de email
  const inputsEmail = document.querySelectorAll('input[type="email"]')
  inputsEmail.forEach((input) => {
    input.addEventListener("blur", function () {
      const email = this.value.trim()
      const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      if (email && !regex.test(email)) {
        alert("Por favor ingrese un email válido")
        this.focus()
      }
    })
  })

  // Prevenir envío múltiple de formularios
  const formularios = document.querySelectorAll("form")
  formularios.forEach((form) => {
    form.addEventListener("submit", function () {
      const btnSubmit = this.querySelector('button[type="submit"]')
      if (btnSubmit) {
        btnSubmit.disabled = true
        btnSubmit.textContent = "Procesando..."
      }
    })
  })
})

// Función para validar API Perú Dev (DNI/RUC)
function validarDocumento(tipo, numero, callback) {
  const btnValidar = event.target
  btnValidar.disabled = true
  btnValidar.textContent = "Validando..."

  fetch("index.php?modulo=" + tipo + "&accion=validarDni", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "dni=" + encodeURIComponent(numero),
  })
    .then((response) => response.json())
    .then((data) => {
      btnValidar.disabled = false
      btnValidar.textContent = "Validar"

      if (data.success) {
        callback(data.data)
      } else {
        alert(data.message || "Error al validar el documento")
      }
    })
    .catch((error) => {
      btnValidar.disabled = false
      btnValidar.textContent = "Validar"
      alert("Error de conexión: " + error.message)
    })
}

// Función para formatear moneda
function formatearMoneda(monto) {
  return "S/ " + Number.parseFloat(monto).toFixed(2)
}

// Función para formatear fecha
function formatearFecha(fecha) {
  const opciones = { year: "numeric", month: "long", day: "numeric" }
  return new Date(fecha).toLocaleDateString("es-PE", opciones)
}
