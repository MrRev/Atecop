<?php
// VistaReporteSociosInhabilitar.php
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="contenedor-principal">
    <div class="encabezado-seccion">
        <h1>Reporte: Socios para Inhabilitar (Mora > <?php echo htmlspecialchars($diasMora); ?> días)</h1>
        <a href="index.php?modulo=reportes&accion=menu" class="boton-secundario">← Volver al Menú</a>
    </div>

    <div class="tabla-contenedor">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Fecha Vencimiento</th>
                    <th>Días Mora</th>
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
                            <td><?php echo date('d/m/Y', strtotime($socio['fechavencimiento'])); ?></td>
                            <td>
                                <span class="<?php echo ($socio['dias_mora'] > 90) ? 'texto-rojo' : ''; ?>">
                                    <?php echo htmlspecialchars($socio['dias_mora']); ?> días
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="texto-centrado">No se encontraron socios con mora mayor a <?php echo htmlspecialchars($diasMora); ?> días.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
