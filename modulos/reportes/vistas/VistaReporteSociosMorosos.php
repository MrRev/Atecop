<?php
// (VistaReporteSociosMorosos.php)
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="contenedor-principal">
    <div class="encabezado-seccion">
        <h1>Reporte: Socios Morosos</h1>
        <a href="index.php?modulo=reportes&accion=menu" class="boton-secundario">
            ← Volver al Menú
        </a>
    </div>

    <div class="tabla-contenedor">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th>Fecha Vencimiento</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($datos) && count($datos) > 0): ?>
                    <?php foreach ($datos as $socio): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($socio['dni']); ?></td>
                            <td><?php echo htmlspecialchars($socio['nombrecompleto']); ?></td>
                            <td><?php echo htmlspecialchars($socio['email']); ?></td>
                            <td><?php echo htmlspecialchars($socio['telefono']); ?></td>
                            <td>
                                <span class="estado-badge estado-moroso">
                                    <?php echo htmlspecialchars($socio['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="fecha-vencimiento vencido">
                                    <?php echo date('d/m/Y', strtotime($socio['fechavencimiento'])); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="texto-centrado">No se encontraron socios morosos.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>