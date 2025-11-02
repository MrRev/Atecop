<?php
require_once __DIR__ . '/../../../util_global/SessionManager.php';
SessionManager::checkSession();

// Debug: Verificar que los planes se están cargando
if (empty($planes)) {
    error_log('ATENCIÓN: No se han cargado planes de membresía en VistaFormSocio.php');
}

$esEdicion = isset($socio) && $socio != null;
$titulo = $esEdicion ? 'Editar Socio' : 'Registrar Nuevo Socio';

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="contenedor-principal">
    <div class="encabezado-seccion">
        <h1><?php echo $titulo; ?></h1>
        <a href="index.php?modulo=socios&accion=listar" class="boton-secundario">
            ← Volver al listado
        </a>
    </div>

    <?php if (isset($_SESSION['error_socios'])): ?>
        <div class="mensaje-error">
            <?php 
            echo htmlspecialchars($_SESSION['error_socios']);
            unset($_SESSION['error_socios']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($exito)): ?>
        <div class="mensaje-exito">
            <?php echo htmlspecialchars($exito); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?modulo=socios&accion=guardar" 
        class="formulario-principal" id="formSocio" onsubmit="return validarFormularioSocio(event)">
        
        <?php if ($esEdicion): ?>
            <input type="hidden" name="idsocio" value="<?php echo $socio->getIdsocio(); ?>">
        <?php endif; ?>

        <!-- Sección: Datos de Identificación -->
        <div class="seccion-formulario">
            <h2>Datos de Identificación</h2>
            
            <div class="fila-formulario">
                <div class="campo-formulario">
                    <label for="dni">DNI / RUC *</label>
                    <div class="grupo-input-boton">
               <input type="text" id="dni" name="dni" 
                   value="<?php echo $esEdicion ? htmlspecialchars($socio->getDni()) : ''; ?>"
                               required maxlength="11" pattern="[0-9]{8,11}"
                               <?php echo $esEdicion ? 'readonly' : ''; ?>>
                        <?php if (!$esEdicion): ?>
                            <button type="button" id="btnValidar" class="boton-validar">Validar</button>
                        <?php endif; ?>
                    </div>
                    <div id="resultadoValidacion"></div>
                    <small>Ingrese 8 dígitos para DNI u 11 para RUC</small>
                </div>

                <div class="campo-formulario">
                    <label for="nombrecompleto">Nombre Completo *</label>
                <input type="text" id="nombrecompleto" name="nombrecompleto" 
                  value="<?php echo $esEdicion ? htmlspecialchars($socio->getNombrecompleto()) : ''; ?>"
                           required maxlength="255"
                           <?php echo $esEdicion ? 'readonly' : ''; ?>>
                </div>
            </div>

            <div class="fila-formulario">
                <div class="campo-formulario">
                    <label for="fechanacimiento">Fecha de Nacimiento</label>
              <input type="date" id="fechanacimiento" name="fechanacimiento" 
                  value="<?php echo $esEdicion ? $socio->getFechanacimiento() : ''; ?>">
                </div>

                <div class="campo-formulario">
                    <label for="idprofesion">Profesión</label>
                    <select id="idprofesion" name="idprofesion">
                        <option value="">Seleccione una profesión</option>
                        <?php if (isset($profesiones)): ?>
                            <?php foreach ($profesiones as $profesion): ?>
                                <option value="<?php echo $profesion['idprofesion']; ?>"
                                    <?php echo ($esEdicion && $socio->getIdprofesion() == $profesion['idprofesion']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($profesion['nombreprofesion']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Sección: Datos de Contacto -->
        <div class="seccion-formulario">
            <h2>Datos de Contacto</h2>
            
            <div class="fila-formulario">
                <div class="campo-formulario">
                    <label for="email">Email *</label>
              <input type="email" id="email" name="email" 
                  value="<?php echo $esEdicion ? htmlspecialchars($socio->getEmail()) : ''; ?>"
                           required maxlength="100">
                </div>

                <div class="campo-formulario">
                    <label for="telefono">Teléfono</label>
              <input type="tel" id="telefono" name="telefono" 
                  value="<?php echo $esEdicion ? htmlspecialchars($socio->getTelefono()) : ''; ?>"
                           maxlength="20">
                </div>
            </div>

            <div class="campo-formulario">
                <label for="direccion">Dirección</label>
          <input type="text" id="direccion" name="direccion" 
              value="<?php echo $esEdicion ? htmlspecialchars($socio->getDireccion()) : ''; ?>"
                       maxlength="255">
            </div>
        </div>

        <!-- Sección: Datos de Membresía -->
        <div class="seccion-formulario">
            <h2>Datos de Membresía</h2>
            
            <div class="fila-formulario">
                <div class="campo-formulario">
                    <label for="idtiposocio">Tipo de Socio *</label>
                    <select id="idtiposocio" name="idtiposocio" required>
                        <option value="">Seleccione un tipo</option>
                        <?php if (isset($tiposSocio)): ?>
                            <?php foreach ($tiposSocio as $tipo): ?>
                                <option value="<?php echo $tipo['idtiposocio']; ?>"
                                    <?php echo ($esEdicion && $socio->getIdtiposocio() == $tipo['idtiposocio']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($tipo['nombretipo']); ?>
                                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>

            <div class="campo-formulario">
                <label for="idplan">Plan de Membresía *</label>
                <select id="idplan" name="idplan" required>
                    <option value="">Seleccione un plan</option>
                    <?php if (isset($planes)): ?>
                        <?php foreach ($planes as $plan): ?>
                            <option value="<?php echo $plan['idplan']; ?>"
                                <?php echo ($esEdicion && $socio->getIdplan() == $plan['idplan']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($plan['nombreplan']); ?> - S/ <?php echo number_format($plan['costo'], 2); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="fila-formulario">
                <div class="campo-formulario">
                    <label for="numcuentabancaria">Número de Cuenta Bancaria</label>
                    <input type="text" id="numcuentabancaria" name="numcuentabancaria" 
                           value="<?php echo $esEdicion ? htmlspecialchars($socio->getNumcuentabancaria()) : ''; ?>"
                           maxlength="50">
                </div>

                <?php if ($esEdicion): ?>
                <div class="campo-formulario">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="Activo" <?php echo ($socio->getEstado() == 'Activo') ? 'selected' : ''; ?>>Activo</option>
                        <option value="Moroso" <?php echo ($socio->getEstado() == 'Moroso') ? 'selected' : ''; ?>>Moroso</option>
                        <option value="Inactivo" <?php echo ($socio->getEstado() == 'Inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                        <option value="Pendiente" <?php echo ($socio->getEstado() == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    </select>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="botones-formulario">
            <button type="submit" class="boton-primario">
                <?php echo $esEdicion ? 'Actualizar Socio' : 'Registrar Socio'; ?>
            </button>
            <a href="index.php?modulo=socios&accion=listar" class="boton-cancelar">Cancelar</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnValidar = document.getElementById('btnValidar');
        const formSocio = document.getElementById('formSocio');
        
        if (btnValidar) {
            btnValidar.addEventListener('click', function() {
                const dniInput = document.getElementById('dni');
                const dni = dniInput.value.trim();
                const resultadoDiv = document.getElementById('resultadoValidacion');
                
                let tipo;
                
                // 1. DETERMINAR EL TIPO BASADO EN LA LONGITUD
                if (dni.length === 8) {
                    tipo = 'dni';
                } else if (dni.length === 11) {
                    tipo = 'ruc';
                } else {
                    resultadoDiv.innerHTML = '<p class="mensaje-error">Ingrese un DNI (8 dígitos) o RUC (11 dígitos) válido.</p>';
                    return;
                }
                
                // Deshabilitar botón y mostrar carga
                this.disabled = true;
                this.textContent = 'Validando...';
                resultadoDiv.innerHTML = '<p class="mensaje-info">Validando documento...</p>';
                
                // 2. CONSTRUIR LA URL CORRECTA (INCLUYE 'tipo')
                const url = `index.php?modulo=socios&accion=validarDocumento&documento=${dni}&tipo=${tipo}`;

                // 3. LLAMADA FETCH CON LA CABECERA AJAX
                fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest' // <-- Esta es la cabecera clave
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    // Si la respuesta NO es 200 OK, algo falló (ej. error 500)
                    if (!response.ok) {
                        console.error('Error de red o servidor:', response.status);
                        window.location.href = 'index.php?modulo=seguridad&accion=login';
                        throw new Error('Error en la respuesta del servidor');
                    }
                    
                    const contentType = response.headers.get('content-type');
                    
                    // Si la respuesta no es JSON (ej. una redirección HTML)
                    if (!contentType || !contentType.includes('application/json')) {
                        console.error('Respuesta no es JSON. Posible redirección.');
                        // La sesión expiró y fuimos redirigidos al login
                        window.location.href = 'index.php?modulo=seguridad&accion=login';
                        throw new Error('Sesión expirada o respuesta inesperada');
                    }
                    
                    return response.json();
                })
                .then(data => {
                    // 4. PROCESAR LA RESPUESTA JSON
                    
                    // Tu SessionManager devuelve { success: false, mensaje: '...' } si la sesión expiró
                    if (data.success === false && data.mensaje === 'Sesión expirada') {
                        console.error('La sesión expiró (detectado por JSON).');
                        window.location.href = 'index.php?modulo=seguridad&accion=login';
                        return;
                    }

                    // Tu ControladorSocio devuelve { success: true, data: { ... } }
                    if (data.success && data.data) { 
                        document.getElementById('nombrecompleto').value = data.data.nombre || '';
                        if (data.data.direccion) {
                            document.getElementById('direccion').value = data.data.direccion;
                        }
                        resultadoDiv.innerHTML = '<p class="mensaje-exito">Documento validado correctamente</p>';
                    } else {
                        resultadoDiv.innerHTML = `<p class="mensaje-error">Error: ${data.mensaje || data.message || 'No se pudo validar el documento'}</p>`;
                    }
                })
                .catch(error => {
                    // No redirigir aquí, solo mostrar el error si no fue uno ya manejado
                    if (error.message.includes('Sesión expirada')) {
                        // Ya manejamos esta redirección, no hacer nada más
                    } else {
                        console.error('Error en fetch:', error);
                        resultadoDiv.innerHTML = '<p class="mensaje-error">Error fatal al validar. Revise la consola.</p>';
                    }
                })
                .finally(() => {
                    this.disabled = false;
                    this.textContent = 'Validar';
                });
            });
        }
        
        // Validación de formulario al enviar
        if (formSocio) {
            // Asegúrate de que tu función 'validarFormularioSocio' exista
            // O usa esta validación simple:
            
            // Quitar la validación duplicada del final de tu archivo original
            // formSocio.addEventListener('submit', function(e) { ... });
        }
    });

    // TU FUNCIÓN DE VALIDACIÓN 'onsubmit' (ya la tenías en el <form>)
    // Asegúrate de que esta función exista
    function validarFormularioSocio(event) {
        const dni = document.getElementById('dni').value;
        const email = document.getElementById('email').value;
        
        if (dni.length !== 8 && dni.length !== 11) {
            alert('El DNI debe tener 8 dígitos o el RUC 11 dígitos');
            event.preventDefault(); // Detener el envío
            return false;
        }
        
        if (!email.includes('@')) {
            alert('Ingrese un email válido');
            event.preventDefault(); // Detener el envío
            return false;
        }
        
        return true; // Permitir el envío
    }
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
