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
        <h1>Gestión de Inscripciones</h1>
        <a href="index.php?modulo=cursos&accion=listar" class="boton-secundario">
            ← Volver a cursos
        </a>
    </div>

    <!-- Información del Curso -->
    <?php if (isset($curso)): ?>
    <div class="tarjeta-info-curso">
        <h2><?php echo htmlspecialchars($curso['nombrecurso']); ?></h2>
        <div class="info-curso-grid">
            <div class="info-item">
                <label>Ponente:</label>
                <span><?php echo htmlspecialchars($curso['nombreponente'] ?? 'Sin asignar'); ?></span>
            </div>
            <div class="info-item">
                <label>Cupos:</label>
                <span><?php echo $curso['inscritos']; ?> / <?php echo $curso['cupostotales']; ?></span>
            </div>
            <div class="info-item">
                <label>Costo:</label>
                <span>S/ <?php echo number_format($curso['costoinscripcion'], 2); ?></span>
            </div>
            <div class="info-item">
                <label>Estado:</label>
                <span class="estado-badge estado-<?php echo strtolower(str_replace(' ', '-', $curso['estado'])); ?>">
                    <?php echo htmlspecialchars($curso['estado']); ?>
                </span>
            </div>
        </div>
    </div>
    <?php endif; ?>

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

    <!-- Formulario de Inscripción -->
    <div class="seccion-inscripcion">
        <h3>Inscribir Nuevo Socio</h3>
        
        <div class="formulario-busqueda">
            <input type="text" id="buscar_socio" placeholder="Buscar socio por DNI o nombre...">
            <button type="button" onclick="buscarSocio()" class="boton-primario">Buscar</button>
        </div>

        <div id="resultados_busqueda" class="resultados-busqueda" style="display: none;">
            <!-- Resultados de búsqueda -->
        </div>
    </div>

    <!-- Lista de Inscritos -->
    <div class="seccion-inscritos">
        <div class="encabezado-seccion-interna">
            <h3>Socios Inscritos (<?php echo isset($inscritos) ? count($inscritos) : 0; ?>)</h3>
            <?php if (isset($inscritos) && count($inscritos) > 0): ?>
                <a href="index.php?modulo=cursos&accion=exportarInscritos&id=<?php echo $curso['idcurso']; ?>" 
                   class="boton-secundario">Exportar Lista</a>
            <?php endif; ?>
        </div>

        <?php if (isset($inscritos) && count($inscritos) > 0): ?>
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Fecha Inscripción</th>
                        <th>Estado Pago</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inscritos as $inscrito): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($inscrito['dni']); ?></td>
                            <td><?php echo htmlspecialchars($inscrito['nombrecompleto']); ?></td>
                            <td><?php echo htmlspecialchars($inscrito['email']); ?></td>
                            <td><?php echo htmlspecialchars($inscrito['telefono']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($inscrito['fechainscripcion'])); ?></td>
                            <td>
                                <select onchange="cambiarEstadoPago(<?php echo $inscrito['idsocio']; ?>, <?php echo $curso['idcurso']; ?>, this.value)" 
                                        class="select-estado-pago">
                                    <option value="Pendiente" <?php echo ($inscrito['estadopagocurso'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                    <option value="Pagado" <?php echo ($inscrito['estadopagocurso'] == 'Pagado') ? 'selected' : ''; ?>>Pagado</option>
                                    <option value="Exonerado" <?php echo ($inscrito['estadopagocurso'] == 'Exonerado') ? 'selected' : ''; ?>>Exonerado</option>
                                </select>
                            </td>
                            <td class="acciones-celda">
                                <button onclick="eliminarInscripcion(<?php echo $inscrito['idsocio']; ?>, <?php echo $curso['idcurso']; ?>)" 
                                        class="boton-accion boton-eliminar">Eliminar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="texto-centrado texto-gris">No hay socios inscritos en este curso</p>
        <?php endif; ?>
    </div>
</div>

<script>
const idcurso = <?php echo $curso['idcurso']; ?>;

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
                       '<th>DNI</th><th>Nombre</th><th>Email</th><th>Estado</th><th>Acción</th>' +
                       '</tr></thead><tbody>';
            
            data.forEach(socio => {
                html += '<tr>' +
                        '<td>' + socio.dni + '</td>' +
                        '<td>' + socio.nombrecompleto + '</td>' +
                        '<td>' + socio.email + '</td>' +
                        '<td>' + socio.estado + '</td>' +
                        '<td><button type="button" onclick="inscribirSocio(' + socio.idsocio + ')" ' +
                        'class="boton-pequeno">Inscribir</button></td>' +
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

function inscribirSocio(idsocio) {
    if (confirm('¿Desea inscribir a este socio en el curso?')) {
        window.location.href = 'index.php?modulo=cursos&accion=inscribir&idcurso=' + idcurso + '&idsocio=' + idsocio;
    }
}

function cambiarEstadoPago(idsocio, idcurso, nuevoEstado) {
    fetch('index.php?modulo=cursos&accion=cambiarEstadoPago', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'idsocio=' + idsocio + '&idcurso=' + idcurso + '&estado=' + nuevoEstado
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Estado de pago actualizado');
        } else {
            alert('Error: ' + data.mensaje);
        }
    })
    .catch(error => {
        alert('Error al actualizar estado: ' + error);
    });
}

function eliminarInscripcion(idsocio, idcurso) {
    if (confirm('¿Está seguro de eliminar esta inscripción?')) {
        window.location.href = 'index.php?modulo=cursos&accion=eliminarInscripcion&idcurso=' + idcurso + '&idsocio=' + idsocio;
    }
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
