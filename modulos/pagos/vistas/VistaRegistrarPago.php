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
        <h1>Registrar Pago</h1>
        <a href="index.php?modulo=pagos&accion=listar" class="boton-secundario">
            ← Volver
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

    <form method="POST" action="index.php?modulo=pagos&accion=guardar" 
          enctype="multipart/form-data" class="formulario-principal" id="formPago">

        <!-- Sección: Selección de Socio -->
        <div class="seccion-formulario">
            <h2>Datos del Socio</h2>
            
            <?php if (!isset($socio)): ?>
                <!-- Formulario de búsqueda -->
                <div class="busqueda-contenedor mb-4">
                    <form method="GET" action="" class="form-busqueda">
                        <input type="hidden" name="modulo" value="pagos">
                        <input type="hidden" name="accion" value="registrar">
                        <div class="input-group">
                            <input type="text" 
                                   name="busqueda" 
                                   class="form-control" 
                                   placeholder="Buscar por DNI, nombres o apellidos..."
                                   value="<?php echo htmlspecialchars($busqueda ?? ''); ?>">
                            <button type="submit" class="btn btn-primario">Buscar</button>
                        </div>
                    </form>
                </div>

                <!-- Tabla de socios -->
                <div class="tabla-contenedor mb-4">
                    <table class="tabla">
                        <thead>
                            <tr>
                                <th>DNI</th>
                                <th>Nombres y Apellidos</th>
                                <th>Tipo Socio</th>
                                <th>Estado</th>
                                <th>Vencimiento</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($socios as $s): ?>
                                <tr class="<?php echo $s['dias_vencido'] > 0 ? 'fila-vencida' : ''; ?>">
                                    <td><?php echo htmlspecialchars($s['dni']); ?></td>
                                    <td><?php echo htmlspecialchars($s['nombrecompleto']); ?></td>
                                    <td><?php echo htmlspecialchars($s['tipo_socio']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $s['estado'] === 'Activo' ? 'badge-success' : 'badge-warning'; ?>">
                                            <?php echo htmlspecialchars($s['estado']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        echo date('d/m/Y', strtotime($s['fechavencimiento']));
                                        if ($s['dias_vencido'] > 0) {
                                            echo " <span class='text-danger'>(" . $s['dias_vencido'] . " días vencido)</span>";
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>/index.php?modulo=pagos&accion=registrar&idsocio=<?php echo $s['idsocio']; ?>" 
                                           class="btn btn-primario btn-sm">
                                            Seleccionar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($socios)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No se encontraron socios</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <!-- Socio ya seleccionado -->
                <input type="hidden" name="idsocio" value="<?php echo $socio['idsocio']; ?>">
                
                <div class="info-socio-seleccionado">
                    <div class="campo-info">
                        <label>DNI/RUC:</label>
                        <span><?php echo htmlspecialchars($socio['dni']); ?></span>
                    </div>
                    <div class="campo-info">
                        <label>Nombre:</label>
                        <span><?php echo htmlspecialchars($socio['nombrecompleto']); ?></span>
                    </div>
                    <div class="campo-info">
                        <label>Tipo de Socio:</label>
                        <span><?php echo htmlspecialchars($socio['tipo_socio']); ?></span>
                    </div>
                    <div class="campo-info">
                        <label>Plan Actual:</label>
                        <span><?php echo htmlspecialchars($socio['nombre_plan']); ?></span>
                    </div>
                    <div class="campo-info">
                        <label>Vencimiento:</label>
                        <span class="<?php echo $socio['dias_vencido'] > 0 ? 'text-danger' : ''; ?>">
                            <?php echo date('d/m/Y', strtotime($socio['fechavencimiento']));
                            if ($socio['dias_vencido'] > 0) {
                                echo " ({$socio['dias_vencido']} días vencido)";
                            }
                            ?>
                        </span>
                    </div>
                </div>

                <div class="formulario-pago mt-4">
                    <h3>Datos del Pago</h3>
                    
                    <div class="form-group">
                        <label for="monto">Monto (S/):</label>
                        <input type="number" step="0.01" name="monto" id="monto" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="fechapago">Fecha de Pago:</label>
                        <input type="date" name="fechapago" id="fechapago" class="form-control" 
                               value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="concepto">Concepto:</label>
                        <input type="text" name="concepto" id="concepto" class="form-control" 
                               value="Pago de renovación de Membresia: <?php echo htmlspecialchars($socio['nombre_plan'] ?? 'Plan Mensual'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="idmetodopago">Método de Pago:</label>
                        <select name="idmetodopago" id="idmetodopago" class="form-control" required>
                            <option value="">Seleccione un método</option>
                            <?php foreach ($metodosPago as $metodo): ?>
                                <option value="<?php echo $metodo['idmetodopago']; ?>">
                                    <?php echo htmlspecialchars($metodo['nombremetodo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="comprobante">Comprobante:</label>
                        <input type="file" name="comprobante" id="comprobante" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Formatos permitidos: PDF, JPG, PNG. Máximo 2MB.</small>
                        <div id="errorComprobante" class="text-danger" style="display: none;"></div>
                    </div>

                    <div class="botones-form">
                        <button type="submit" class="btn btn-primario">Registrar Pago</button>
                        <a href="<?php echo BASE_URL; ?>/index.php?modulo=pagos&accion=listar" class="btn btn-secundario">Cancelar</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </form>
</div>

<script>
// Validación del comprobante
document.getElementById('comprobante').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const errorDiv = document.getElementById('errorComprobante');
    const submitBtn = document.querySelector('button[type="submit"]');
    
    // Resetear mensaje de error
    errorDiv.style.display = 'none';
    errorDiv.textContent = '';
    submitBtn.disabled = false;
    
    if (file) {
        // Validar tamaño (2MB = 2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
            errorDiv.textContent = 'El archivo es demasiado grande. El tamaño máximo es 2MB.';
            errorDiv.style.display = 'block';
            submitBtn.disabled = true;
            this.value = ''; // Limpiar el input
            return;
        }
        
        // Validar tipo de archivo
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            errorDiv.textContent = 'Tipo de archivo no permitido. Use PDF, JPG o PNG.';
            errorDiv.style.display = 'block';
            submitBtn.disabled = true;
            this.value = ''; // Limpiar el input
            return;
        }
    }
});

function buscarSocio() {
    const termino = document.getElementById('buscar_socio').value;
    
    if (termino.length < 3) {
        alert('Ingrese al menos 3 caracteres para buscar');
        return;
    }
    
    fetch('index.php?modulo=socios&accion=buscar&termino=' + encodeURIComponent(termino))
        .then(response => response.json())
        .then(data => {
            const contenedor = document.getElementById('resultados_busqueda');
            
            if (data.length === 0) {
                contenedor.innerHTML = '<p class="texto-centrado">No se encontraron socios</p>';
                contenedor.style.display = 'block';
                return;
            }
            
            let html = '<table class="tabla-datos tabla-compacta"><thead><tr>' +
                       '<th>DNI</th><th>Nombre</th><th>Plan</th><th>Vencimiento</th><th>Acción</th>' +
                       '</tr></thead><tbody>';
            
            data.forEach(socio => {
                html += '<tr>' +
                        '<td>' + socio.dni + '</td>' +
                        '<td>' + socio.nombrecompleto + '</td>' +
                        '<td>' + socio.nombreplan + '</td>' +
                        '<td>' + socio.fechavencimiento + '</td>' +
                        '<td><button type="button" onclick="seleccionarSocio(' + socio.idsocio + ', \'' + 
                        socio.nombrecompleto + '\')" class="boton-pequeno">Seleccionar</button></td>' +
                        '</tr>';
            });
            
            html += '</tbody></table>';
            contenedor.innerHTML = html;
            contenedor.style.display = 'block';
        })
        .catch(error => {
            alert('Error al buscar socio: ' + error);
        });
}

function seleccionarSocio(idsocio, nombre) {
    document.getElementById('idsocio').value = idsocio;
    document.getElementById('buscar_socio').value = nombre;
    document.getElementById('resultados_busqueda').style.display = 'none';
    alert('Socio seleccionado: ' + nombre);
}

// Validación del formulario
document.getElementById('formPago').addEventListener('submit', function(e) {
    const idsocio = document.getElementById('idsocio').value;
    const monto = parseFloat(document.getElementById('monto').value);
    
    if (!idsocio) {
        e.preventDefault();
        alert('Debe seleccionar un socio');
        return false;
    }
    
    if (monto <= 0) {
        e.preventDefault();
        alert('El monto debe ser mayor a 0');
        return false;
    }
    
    // Validar tamaño del archivo
    const archivo = document.getElementById('comprobante').files[0];
    if (archivo && archivo.size > 5 * 1024 * 1024) {
        e.preventDefault();
        alert('El archivo no debe superar los 5MB');
        return false;
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
