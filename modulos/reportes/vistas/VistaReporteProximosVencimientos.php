<?php
// (VistaReporteProximosVencimientos.php)
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="contenedor-principal">
    <div class="encabezado-seccion">
        <h1>Reporte: Próximos Vencimientos (<?php echo htmlspecialchars($dias); ?> días)</h1>
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
                    <th>Estado Actual</th>
                    <th>Fecha Vencimiento</th>
                    <th>Días Restantes</th>
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
                                <span class="estado-badge estado-<?php echo strtolower(str_replace(' ', '-', $socio['estado'])); ?>">
                                    <?php echo htmlspecialchars($socio['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="fecha-vencimiento">
                                    <?php echo date('d/m/Y', strtotime($socio['fechavencimiento'])); ?>
                                </span>
                            </td>
                            <td>
                                <!-- Calculamos los días restantes para el reporte -->
                                <?php
                                    $fechaVenc = new DateTime($socio['fechavencimiento']);
                                    $hoy = new DateTime();
                                    $diferencia = $hoy->diff($fechaVenc)->format("%r%a"); // %r%a incluye el signo
                                ?>
                                <span class="<?php echo ($diferencia <= 0) ? 'vencido' : ''; ?>">
                                    <?php echo $diferencia; ?> días
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="texto-centrado">No se encontraron socios con vencimientos en los próximos <?php echo htmlspecialchars($dias); ?> días.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
