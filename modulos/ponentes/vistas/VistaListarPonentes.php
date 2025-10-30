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
        <h1>Gestión de Ponentes</h1>
        <a href="index.php?modulo=ponentes&accion=crear" class="boton-primario">
            + Nuevo Ponente
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
                    <th>DNI</th>
                    <th>Nombre Completo</th>
                    <th>Profesión</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($ponentes) && count($ponentes) > 0): ?>
                    <?php foreach ($ponentes as $ponente): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ponente['dni']); ?></td>
                            <td><?php echo htmlspecialchars($ponente['nombrecompleto']); ?></td>
                            <td><?php echo htmlspecialchars($ponente['nombreprofesion'] ?? 'No especificada'); ?></td>
                            <td><?php echo htmlspecialchars($ponente['email']); ?></td>
                            <td><?php echo htmlspecialchars($ponente['telefono']); ?></td>
                            <td>
                                <span class="estado-badge estado-<?php echo strtolower($ponente['estado']); ?>">
                                    <?php echo htmlspecialchars($ponente['estado']); ?>
                                </span>
                            </td>
                            <td class="acciones-celda">
                                <a href="index.php?modulo=ponentes&accion=formulario&id=<?php echo $ponente['idponente']; ?>" 
                                   class="boton-accion boton-editar">Editar</a>
                                <button onclick="cambiarEstado(<?php echo $ponente['idponente']; ?>, '<?php echo $ponente['estado']; ?>')" 
                                        class="boton-accion boton-<?php echo ($ponente['estado'] == 'Activo') ? 'eliminar' : 'activar'; ?>">
                                    <?php echo ($ponente['estado'] == 'Activo') ? 'Desactivar' : 'Activar'; ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="texto-centrado">No hay ponentes registrados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function cambiarEstado(idponente, estadoActual) {
    const nuevoEstado = (estadoActual === 'Activo') ? 'Inactivo' : 'Activo';
    const mensaje = (estadoActual === 'Activo') 
        ? '¿Desea desactivar este ponente?' 
        : '¿Desea activar este ponente?';
    
    if (confirm(mensaje)) {
        window.location.href = 'index.php?modulo=ponentes&accion=cambiarEstado&id=' + idponente + '&estado=' + nuevoEstado;
    }
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
