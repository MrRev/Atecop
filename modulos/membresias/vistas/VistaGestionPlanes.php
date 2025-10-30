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
        <h1>Gestión de Planes de Membresía</h1>
        <a href="index.php?modulo=membresias&accion=formulario" class="boton-primario">
            + Nuevo Plan
        </a>
    </div>

    <?php if (isset($mensaje)): ?>
        <div class="mensaje-exito">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <div class="tabla-contenedor">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Plan</th>
                    <th>Duración (meses)</th>
                    <th>Costo (S/)</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($planes) && count($planes) > 0): ?>
                    <?php foreach ($planes as $plan): ?>
                        <tr>
                            <td><?php echo $plan['idplan']; ?></td>
                            <td><?php echo htmlspecialchars($plan['nombreplan']); ?></td>
                            <td><?php echo $plan['duracionmeses']; ?></td>
                            <td>S/ <?php echo number_format($plan['costo'], 2); ?></td>
                            <td>
                                <span class="estado-badge estado-<?php echo strtolower($plan['estado']); ?>">
                                    <?php echo htmlspecialchars($plan['estado']); ?>
                                </span>
                            </td>
                            <td class="acciones-celda">
                                <a href="index.php?modulo=membresias&accion=formulario&id=<?php echo $plan['idplan']; ?>" 
                                   class="boton-accion boton-editar">Editar</a>
                                <button onclick="cambiarEstado(<?php echo $plan['idplan']; ?>, '<?php echo $plan['estado']; ?>')" 
                                        class="boton-accion boton-<?php echo ($plan['estado'] == 'Activo') ? 'eliminar' : 'activar'; ?>">
                                    <?php echo ($plan['estado'] == 'Activo') ? 'Desactivar' : 'Activar'; ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="texto-centrado">No hay planes registrados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function cambiarEstado(idplan, estadoActual) {
    const nuevoEstado = (estadoActual === 'Activo') ? 'Inactivo' : 'Activo';
    const mensaje = (estadoActual === 'Activo') 
        ? '¿Desea desactivar este plan? Los socios con este plan no se verán afectados.' 
        : '¿Desea activar este plan?';
    
    if (confirm(mensaje)) {
        window.location.href = 'index.php?modulo=membresias&accion=cambiarEstado&id=' + idplan + '&estado=' + nuevoEstado;
    }
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
