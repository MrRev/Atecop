<?php

$esEdicion = isset($ponente) && $ponente != null;
$titulo = $esEdicion ? 'Editar Ponente' : 'Registrar Nuevo Ponente';

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="contenedor-principal">
    <div class="encabezado-seccion">
        <h1><?php echo $titulo; ?></h1>
        <a href="index.php?modulo=ponentes&accion=listar" class="boton-secundario">
            ← Volver al listado
        </a>
    </div>

    <?php if (isset($error)): ?>
        <div class="mensaje-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($exito)): ?>
        <div class="mensaje-exito">
            <?php echo htmlspecialchars($exito); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?modulo=ponentes&accion=guardar" 
          class="formulario-principal" id="formPonente">
        
        <?php if ($esEdicion): ?>
           <input type="hidden" name="idponente" value="<?php echo $ponente->getIdponente(); ?>">
        <?php endif; ?>

        <div class="seccion-formulario">
            <h2>Datos del Ponente</h2>
            
            <div class="fila-formulario">
                <div class="campo-formulario">
                    <label for="dni">DNI *</label>
                    <div class="grupo-input-boton">
                        <input type="text" id="dni" name="dni" 
                               value="<?php echo $esEdicion ? htmlspecialchars($ponente->getDni()) : ''; ?>"
                               required maxlength="8" pattern="[0-9]{8}"
                               <?php echo $esEdicion ? 'readonly' : ''; ?>>
                        <?php if (!$esEdicion): ?>
                            <button type="button" onclick="validarDNI()" class="boton-validar">Validar</button>
                        <?php endif; ?>
                    </div>
                    <small>Ingrese 8 dígitos del DNI</small>
                </div>
                <div class="campo-formulario">
                    <label for="nombrecompleto">Nombre Completo *</label>
                    <input type="text" id="nombrecompleto" name="nombrecompleto" 
                        value="<?php echo $esEdicion ? htmlspecialchars($ponente->getNombrecompleto()) : ''; ?>"
                        required maxlength="255"
                        <?php echo $esEdicion ? 'readonly' : ''; ?>>
                </div>
            </div>

            <div class="fila-formulario">
                <div class="campo-formulario">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo $esEdicion ? htmlspecialchars($ponente->getEmail()) : ''; ?>"
                           required maxlength="100">
                </div>

                <div class="campo-formulario">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" 
                           value="<?php echo $esEdicion ? htmlspecialchars($ponente->getTelefono()) : ''; ?>"
                           maxlength="20">
                </div>
            </div>

            <div class="fila-formulario">
                <div class="campo-formulario">
                    <label for="idprofesion">Profesión</label>
                    <select id="idprofesion" name="idprofesion">
                        <option value="">Seleccione una profesión</option>
                        <?php if (isset($profesiones)): ?>
                            <?php foreach ($profesiones as $profesion): ?>
                                <option value="<?php echo $profesion['idprofesion']; ?>"
                                    <?php echo ($esEdicion && $ponente->getIdprofesion() == $profesion['idprofesion']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($profesion['nombreprofesion']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <?php if ($esEdicion): ?>
                <div class="campo-formulario">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="Activo" <?php echo ($ponente->getEstado() == 'Activo') ? 'selected' : ''; ?>>Activo</option>
                        <option value="Inactivo" <?php echo ($ponente->getEstado() == 'Inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="botones-formulario">
            <button type="submit" class="boton-primario">
                <?php echo $esEdicion ? 'Actualizar Ponente' : 'Registrar Ponente'; ?>
            </button>
            <a href="index.php?modulo=ponentes&accion=listar" class="boton-cancelar">Cancelar</a>
        </div>
    </form>
</div>

<script>
    function validarDNI() {
        const dniInput = document.getElementById('dni');
        const dni = dniInput.value.trim();
        
        if (dni.length !== 8) {
            alert('Ingrese un DNI válido de 8 dígitos');
            return;
        }
        
        const boton = event.target;
        boton.disabled = true;
        boton.textContent = 'Validando...';
        
        // URL correcta (ya la tenías)
        const url = 'index.php?modulo=ponentes&accion=validarDNI&dni=' + dni;

        fetch(url, {
            // --- ¡ESTA ES LA PARTE QUE FALTABA! ---
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
            // --- FIN DEL BLOQUE AÑADIDO ---
        })
        .then(response => {
            // Añadimos un mejor manejo de errores de sesión
            if (!response.ok) {
                if (response.status === 401) {
                    alert('Sesión expirada. Redirigiendo al login...');
                    window.location.href = 'index.php?modulo=seguridad&accion=login';
                }
                throw new Error('Respuesta no válida del servidor');
            }
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Respuesta no es JSON. Posible redirección.');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                document.getElementById('nombrecompleto').value = data.nombre;
                // (Tu controlador también devuelve 'direccion', podrías usarla)
                // if (data.direccion) {
                //     document.getElementById('direccion_ponente').value = data.direccion;
                // }
                alert('DNI validado correctamente');
            } else {
                alert('Error: ' + (data.mensaje || data.message)); // Usamos el mensaje del JSON
            }
        })
        .catch(error => {
            console.error('Error al validar DNI:', error);
            alert('Error al validar DNI: ' + error.message);
        })
        .finally(() => {
            boton.disabled = false;
            boton.textContent = 'Validar';
        });
    }

    // Tu código de validación del formulario (está bien)
    document.getElementById('formPonente').addEventListener('submit', function(e) {
        const dni = document.getElementById('dni').value;
        const email = document.getElementById('email').value;
        
        if (dni.length !== 8) {
            e.preventDefault();
            alert('El DNI debe tener 8 dígitos');
            return false;
        }
        
        if (email.trim() === '' || !email.includes('@')) {
            e.preventDefault();
            alert('Ingrese un email válido');
            return false;
        }
    });
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
