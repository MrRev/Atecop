<?php
// Verificar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['idadmin'])) {
    header('Location: index.php?modulo=seguridad&accion=login');
    exit;
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
        class="formulario-principal" id="formSocio">
        
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
                            <button type="button" onclick="validarDNI()" class="boton-validar">Validar</button>
                        <?php endif; ?>
                    </div>
                    <small>Ingrese 8 dígitos para DNI u 11 para RUC</small>
                </div>

                <div class="campo-formulario">
                    <label for="nombrecompleto">Nombre Completo *</label>
              <input type="text" id="nombrecompleto" name="nombrecompleto" 
                  value="<?php echo $esEdicion ? htmlspecialchars($socio->getNombrecompleto()) : ''; ?>"
                           required maxlength="255">
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

<script>
// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar validaciones del formulario
    initFormValidations();
});                <div class="campo-formulario">
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
function validarDNI() {
    const dni = document.getElementById('dni').value;
    
    if (dni.length !== 8 && dni.length !== 11) {
        alert('Ingrese un DNI válido (8 dígitos) o RUC (11 dígitos)');
        return;
    }
    
    // Mostrar indicador de carga
    const boton = event.target;
    boton.disabled = true;
    boton.textContent = 'Validando...';
    
    // Llamada AJAX para validar DNI/RUC
    fetch('index.php?modulo=socios&accion=validarDNI&dni=' + dni)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('nombrecompleto').value = data.nombre;
                if (data.direccion) {
                    document.getElementById('direccion').value = data.direccion;
                }
                alert('DNI/RUC validado correctamente');
            } else {
                alert('Error: ' + data.mensaje);
            }
        })
        .catch(error => {
            alert('Error al validar DNI/RUC: ' + error);
        })
        .finally(() => {
            boton.disabled = false;
            boton.textContent = 'Validar';
        });
}

// Validación del formulario
document.getElementById('formSocio').addEventListener('submit', function(e) {
    const dni = document.getElementById('dni').value;
    const email = document.getElementById('email').value;
    
    if (dni.length !== 8 && dni.length !== 11) {
        e.preventDefault();
        alert('El DNI debe tener 8 dígitos o el RUC 11 dígitos');
        return false;
    }
    
    if (!email.includes('@')) {
        e.preventDefault();
        alert('Ingrese un email válido');
        return false;
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
