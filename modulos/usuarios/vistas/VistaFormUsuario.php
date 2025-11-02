<?php
// Verificar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['idadmin'])) {
    header('Location: index.php?modulo=seguridad&accion=login');
    exit;
}

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="contenedor-principal">
    <div class="encabezado-seccion">
        <h1><?php echo $usuario ? 'Editar Usuario' : 'Nuevo Usuario'; ?></h1>
        <div class="grupo-botones">
            <a href="index.php?modulo=usuarios&accion=listar" class="boton-secundario">← Volver</a>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alerta alerta-error">
            <?php 
            echo htmlspecialchars($_SESSION['error']); 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <div class="tarjeta-formulario">
        <form id="formUsuario" method="POST" action="index.php?modulo=usuarios&accion=guardar" class="formulario">
            <?php if ($usuario): ?>
                <input type="hidden" name="idusuario" value="<?php echo $usuario->getIdusuario(); ?>">
            <?php endif; ?>

            <!-- DNI -->
            <div class="grupo-formulario">
                <label for="dni">DNI:</label>
                <div class="campo-con-validacion">
                    <input type="text" id="dni" name="dni" pattern="[0-9]{8}" maxlength="8"
                           value="<?php echo htmlspecialchars($usuario ? $usuario->getDni() : ''); ?>"
                           <?php echo $usuario ? 'readonly' : 'required'; ?> 
                           oninput="validarFormatoDNI(this)"
                           class="campo-texto">
                    <?php if (!$usuario): ?>
                        <button type="button" id="btnValidarDNI" onclick="validarDNI()" class="boton-validar" disabled>
                            Validar
                        </button>
                    <?php endif; ?>
                </div>
                <small id="dniHelp" class="texto-ayuda">DNI de 8 dígitos</small>
                <div id="dniValidationStatus" class="estado-validacion"></div>
            </div>

            <style>
            .estado-validacion {
                margin-top: 5px;
                font-size: 0.9em;
            }
            .estado-validacion.error {
                color: #dc3545;
            }
            .estado-validacion.success {
                color: #28a745;
            }
            .campo-con-validacion {
                position: relative;
                display: flex;
                gap: 10px;
            }
            .spinner {
                display: none;
                width: 20px;
                height: 20px;
                border: 2px solid #f3f3f3;
                border-top: 2px solid #3498db;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin-left: 10px;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            </style>

            <!-- Nombre Completo (autocompletado por API) -->
            <div class="grupo-formulario">
                <label for="nombrecompleto">Nombre Completo:</label>
                <input type="text" id="nombrecompleto" 
                       value="<?php echo htmlspecialchars($usuario ? $usuario->getNombrecompleto() : ''); ?>"
                       readonly class="campo-texto">
                <small class="texto-ayuda">Se obtiene automáticamente al validar el DNI</small>
            </div>

            <!-- Nombre de Usuario -->
            <div class="grupo-formulario">
                <label for="nombreusuario">Nombre de Usuario:</label>
                <input type="text" id="nombreusuario" name="nombreusuario" required
                       value="<?php echo htmlspecialchars($usuario ? $usuario->getNombreusuario() : ''); ?>"
                       class="campo-texto">
                <small class="texto-ayuda">Se generará automáticamente, pero puede modificarlo</small>
            </div>

            <!-- Email -->
            <div class="grupo-formulario">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($usuario ? $usuario->getEmail() : ''); ?>"
                       class="campo-texto">
            </div>

            <!-- Teléfono -->
            <div class="grupo-formulario">
                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" 
                       value="<?php echo htmlspecialchars($usuario ? $usuario->getTelefono() : ''); ?>"
                       class="campo-texto">
            </div>

            <!-- Contraseña -->
            <div class="grupo-formulario">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" 
                       <?php echo !$usuario ? 'required' : ''; ?>
                       class="campo-texto">
                <small class="texto-ayuda">
                    <?php echo $usuario ? 'Dejar en blanco para mantener la actual' : 'Contraseña para el nuevo usuario'; ?>
                </small>
            </div>

            <!-- Dirección -->
            <div class="grupo-formulario">
                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion" 
                       value="<?php echo htmlspecialchars($usuario ? $usuario->getDireccion() : ''); ?>"
                       class="campo-texto">
            </div>

            <!-- Rol (por ahora solo administrador) -->
            <div class="grupo-formulario">
                <label for="rol">Rol:</label>
                <select id="rol" name="rol" required class="campo-select">
                    <option value="administrador" selected>Administrador</option>
                </select>
                <small class="texto-ayuda">Por ahora solo rol administrador disponible</small>
            </div>

            <!-- Vinculación con Socio -->
            <div class="grupo-formulario">
                <label for="idsocio">Vincular con Socio:</label>
                <select id="idsocio" name="idsocio" class="campo-select">
                    <option value="">Sin vincular</option>
                    <?php foreach ($socios as $socio): ?>
                        <option value="<?php echo $socio->getIdsocio(); ?>"
                                <?php echo ($usuario && $usuario->getIdsocio() == $socio->getIdsocio()) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($socio->getNombrecompleto()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="texto-ayuda">Opcional: vincular usuario con un socio existente</small>
            </div>

            <!-- Estado -->
            <div class="grupo-formulario">
                <label for="estado">Estado:</label>
                <select id="estado" name="estado" required class="campo-select">
                    <option value="Activo" <?php echo (!$usuario || $usuario->getEstado() === 'Activo') ? 'selected' : ''; ?>>
                        Activo
                    </option>
                    <option value="Inactivo" <?php echo ($usuario && $usuario->getEstado() === 'Inactivo') ? 'selected' : ''; ?>>
                        Inactivo
                    </option>
                </select>
            </div>

            <div class="grupo-botones">
                <button type="submit" class="boton-primario">Guardar Usuario</button>
                <a href="index.php?modulo=usuarios&accion=listar" class="boton-secundario">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript para validación de DNI y autocompletado -->
<script>
function validarFormatoDNI(input) {
    // Eliminar caracteres no numéricos
    input.value = input.value.replace(/[^0-9]/g, '');
    
    const dni = input.value;
    const btnValidar = document.getElementById('btnValidarDNI');
    const dniHelp = document.getElementById('dniHelp');
    const validationStatus = document.getElementById('dniValidationStatus');
    
    if (dni.length === 0) {
        validationStatus.innerHTML = '';
        dniHelp.textContent = 'DNI de 8 dígitos';
        btnValidar.disabled = true;
        return;
    }
    
    if (dni.length < 8) {
        validationStatus.innerHTML = `<span class="error">Faltan ${8 - dni.length} dígitos</span>`;
        btnValidar.disabled = true;
    } else if (dni.length === 8) {
        validationStatus.innerHTML = '<span class="success">Formato válido</span>';
        btnValidar.disabled = false;
    }
}

async function validarDNI() {
    const dni = document.getElementById('dni').value;
    const btnValidar = document.getElementById('btnValidarDNI');
    const validationStatus = document.getElementById('dniValidationStatus');
    
    if (!dni.match(/^\d{8}$/)) {
        validationStatus.innerHTML = '<span class="error">El DNI debe tener 8 dígitos</span>';
        return;
    }
    
    // Mostrar spinner y deshabilitar botón
    btnValidar.disabled = true;
    validationStatus.innerHTML = '<div class="spinner" style="display: inline-block;"></div> Validando...';
    
    try {
        const response = await fetch('index.php?modulo=usuarios&accion=validarDNI&dni=' + dni);
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('nombrecompleto').value = data.nombre;
            document.getElementById('direccion').value = data.direccion || '';
            
            // Generar nombre de usuario automáticamente
            const nombres = data.nombre.trim().split(' ');
            if (nombres.length >= 2) {
                const nombreUsuario = (nombres[0] + '_' + nombres[nombres.length - 1]).toLowerCase();
                document.getElementById('nombreusuario').value = nombreUsuario
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '') // Eliminar acentos
                    .replace(/[^a-z0-9_]/g, '_'); // Reemplazar caracteres especiales por guión bajo
            }
            
            validationStatus.innerHTML = '<span class="success">✓ DNI validado correctamente</span>';
        } else {
            validationStatus.innerHTML = `<span class="error">✗ ${data.mensaje || 'Error al validar DNI'}</span>`;
            btnValidar.disabled = false;
        }
    } catch (error) {
        console.error('Error:', error);
        validationStatus.innerHTML = '<span class="error">✗ Error al procesar la solicitud</span>';
        btnValidar.disabled = false;
    }
}

// Validar formulario antes de enviar
document.getElementById('formUsuario').onsubmit = function(e) {
    const dni = document.getElementById('dni').value;
    const nombrecompleto = document.getElementById('nombrecompleto').value;
    const validationStatus = document.getElementById('dniValidationStatus');
    
    if (!dni.match(/^\d{8}$/)) {
        validationStatus.innerHTML = '<span class="error">✗ El DNI debe tener 8 dígitos</span>';
        document.getElementById('dni').focus();
        e.preventDefault();
        return false;
    }
    
    if (!nombrecompleto) {
        validationStatus.innerHTML = '<span class="error">✗ Debe validar el DNI primero</span>';
        document.getElementById('btnValidarDNI').focus();
        e.preventDefault();
        return false;
    }
    
    return true;
};
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>