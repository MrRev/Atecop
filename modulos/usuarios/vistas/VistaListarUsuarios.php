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
        <h1>Usuarios del Sistema</h1>
        <div class="grupo-botones">
            <a href="index.php?modulo=usuarios&accion=formulario" class="boton-primario">+ Nuevo Usuario</a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filtros-contenedor">
        <form method="GET" action="index.php" class="formulario-filtros">
            <input type="hidden" name="modulo" value="usuarios">
            <input type="hidden" name="accion" value="listar">
            
            <div class="grupo-filtro">
                <input type="text" name="buscar" 
                       value="<?php echo htmlspecialchars($_GET['buscar'] ?? ''); ?>" 
                       placeholder="Buscar por DNI o nombre..." 
                       class="campo-filtro">
            </div>
            
            <div class="grupo-filtro">
                <select name="estado" class="campo-filtro">
                    <option value="">Todos los estados</option>
                    <option value="Activo" <?php echo (isset($_GET['estado']) && $_GET['estado'] === 'Activo') ? 'selected' : ''; ?>>
                        Activos
                    </option>
                    <option value="Inactivo" <?php echo (isset($_GET['estado']) && $_GET['estado'] === 'Inactivo') ? 'selected' : ''; ?>>
                        Inactivos
                    </option>
                </select>
            </div>
            
            <div class="grupo-filtro">
                <button type="submit" class="boton-secundario">Filtrar</button>
                <a href="index.php?modulo=usuarios&accion=listar" class="boton-link">Limpiar</a>
            </div>
        </form>
    </div>

    <!-- Mensajes de éxito/error -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alerta alerta-exito">
            <?php 
            echo htmlspecialchars($_SESSION['success']); 
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alerta alerta-error">
            <?php 
            echo htmlspecialchars($_SESSION['error']); 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <!-- Tabla de Usuarios -->
    <?php if (isset($usuarios) && count($usuarios) > 0): ?>
        <div class="tabla-responsive">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Nombre Completo</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Es Socio</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario->getDni()); ?></td>
                            <td><?php echo htmlspecialchars($usuario->getNombrecompleto()); ?></td>
                            <td><?php echo htmlspecialchars($usuario->getNombreusuario()); ?></td>
                            <td><?php echo htmlspecialchars($usuario->getEmail() ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($usuario->getRol()); ?></td>
                            <td>
                                <?php if ($usuario->getIdsocio()): ?>
                                    <span class="badge badge-success">Sí</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="estado-badge estado-<?php echo strtolower($usuario->getEstado()); ?>">
                                    <?php echo htmlspecialchars($usuario->getEstado()); ?>
                                </span>
                            </td>
                            <td class="acciones">
                                <div class="btn-group">
                                    <a href="index.php?modulo=usuarios&accion=perfil&id=<?php echo $usuario->getIdusuario(); ?>" 
                                       class="btn btn-sm btn-info" title="Ver perfil">
                                        Perfil Completo
                                    </a>
                                    <a href="index.php?modulo=usuarios&accion=formulario&id=<?php echo $usuario->getIdusuario(); ?>" 
                                       class="btn btn-sm btn-warning" title="Editar usuario">
                                        Editar
                                    </a>
                                    <button onclick="cambiarEstadoUsuario(<?php echo $usuario->getIdusuario(); ?>, '<?php echo $usuario->getEstado(); ?>')"
                                            class="btn btn-sm <?php echo $usuario->getEstado() === 'Activo' ? 'btn-danger' : 'btn-success'; ?>">
                                        <?php echo $usuario->getEstado() === 'Activo' ? 'Desactivar' : 'Activar'; ?>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="texto-centrado texto-gris">No se encontraron usuarios</p>
    <?php endif; ?>
</div>

<!-- JavaScript para cambiar estado -->
<script>
function cambiarEstadoUsuario(idusuario, estadoActual) {
    if (!confirm('¿Está seguro de ' + (estadoActual === 'Activo' ? 'desactivar' : 'activar') + ' este usuario?')) {
        return;
    }
    
    fetch('index.php?modulo=usuarios&accion=cambiarEstado', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'idusuario=' + idusuario
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.mensaje || 'Error al cambiar estado');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    });
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>