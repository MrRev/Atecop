<?php
// VistaReporteDetalleSocio.php
require_once __DIR__ . '/../../layouts/header.php';

$socio = $datos['socio'] ?? null;
$pagos = $datos['pagos'] ?? [];
$cursos = $datos['cursos'] ?? [];
?>

<div class="contenedor-principal">
    <div class="encabezado-seccion">
        <h1>Reporte Detallado de Socio</h1>
        <a href="index.php?modulo=reportes&accion=menu" class="boton-secundario">← Volver al Menú</a>
    </div>

    <?php if (!$socio): ?>
        <div class="mensaje-error">Socio no encontrado</div>
    <?php else: ?>
        <div class="tarjeta-info-curso">
            <h2><?php echo htmlspecialchars($socio['nombrecompleto']); ?> — DNI: <?php echo htmlspecialchars($socio['dni']); ?></h2>
            <div class="info-curso-grid">
                <div class="info-item"><label>Email:</label> <span><?php echo htmlspecialchars($socio['email']); ?></span></div>
                <div class="info-item"><label>Teléfono:</label> <span><?php echo htmlspecialchars($socio['telefono']); ?></span></div>
                <div class="info-item"><label>Tipo:</label> <span><?php echo htmlspecialchars($socio['nombretipo'] ?? ''); ?></span></div>
                <div class="info-item"><label>Plan:</label> <span><?php echo htmlspecialchars($socio['nombreplan'] ?? ''); ?></span></div>
                <div class="info-item"><label>Vencimiento:</label> <span><?php echo isset($socio['fechavencimiento']) ? date('d/m/Y', strtotime($socio['fechavencimiento'])) : ''; ?></span></div>
                <div class="info-item"><label>Estado:</label> <span class="estado-badge estado-<?php echo strtolower(str_replace(' ', '-', $socio['estado'] ?? '')); ?>"><?php echo htmlspecialchars($socio['estado'] ?? ''); ?></span></div>
            </div>
        </div>

        <h3>Historial de Pagos</h3>
        <?php if (!empty($pagos)): ?>
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Concepto</th>
                        <th>Método</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pagos as $p): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($p['fechapago'])); ?></td>
                            <td><?php echo 'S/ ' . number_format($p['monto'], 2); ?></td>
                            <td><?php echo htmlspecialchars($p['concepto']); ?></td>
                            <td><?php echo htmlspecialchars($p['nombremetodo'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($p['estado']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="texto-centrado texto-gris">No hay pagos registrados para este socio.</p>
        <?php endif; ?>

        <h3>Cursos Inscritos</h3>
        <?php if (!empty($cursos)): ?>
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Fecha Inscripción</th>
                        <th>Estado Pago</th>
                        <th>Ponente</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cursos as $c): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($c['nombrecurso'] ?? $c['nombre']); ?></td>
                            <td><?php echo isset($c['fechainscripcion']) ? date('d/m/Y H:i', strtotime($c['fechainscripcion'])) : ''; ?></td>
                            <td><?php echo htmlspecialchars($c['estadopagocurso'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($c['nombre_ponente'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="texto-centrado texto-gris">No hay cursos asociados a este socio.</p>
        <?php endif; ?>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
