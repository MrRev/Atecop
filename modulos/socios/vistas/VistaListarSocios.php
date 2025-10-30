<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['idadmin'])) {
    header('Location: ' . BASE_URL . '/index.php?modulo=seguridad&accion=login');
    exit;
}
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestión de Socios</h1>
        <a href="<?php echo BASE_URL; ?>/index.php?modulo=socios&accion=crear" class="btn btn-primary">
            + Nuevo Socio
        </a>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo BASE_URL; ?>/index.php" class="row g-3">
                <input type="hidden" name="modulo" value="socios">
                <input type="hidden" name="accion" value="listar">
                
                <div class="col-md-6">
                    <input type="text" name="buscar" placeholder="Buscar por DNI o nombre..." 
                           value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>"
                           class="form-control">
                </div>
                
                <div class="col-md-4">
                    <select name="estado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="Activo" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Activo') ? 'selected' : ''; ?>>Activo</option>
                        <option value="Moroso" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Moroso') ? 'selected' : ''; ?>>Moroso</option>
                        <option value="Inactivo" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                        <option value="Pendiente" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de socios -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>DNI/RUC</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Vencimiento</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($socios) && count($socios) > 0): ?>
                        <?php foreach ($socios as $socio): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($socio->getDni()); ?></td>
                                <td><?php echo htmlspecialchars($socio->getNombrecompleto()); ?></td>
                                <td><?php echo htmlspecialchars($socio->getEmail()); ?></td>
                                <td><?php echo htmlspecialchars($socio->getTelefono()); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower(str_replace(' ', '-', $socio->getEstado())); ?>">
                                        <?php echo htmlspecialchars($socio->getEstado()); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($socio->getFechavencimiento()))); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/index.php?modulo=socios&accion=perfil&id=<?php echo $socio->getIdsocio(); ?>" 
                                       class="btn btn-sm btn-info">Ver</a>
                                    <a href="<?php echo BASE_URL; ?>/index.php?modulo=socios&accion=formulario&id=<?php echo $socio->getIdsocio(); ?>" 
                                       class="btn btn-sm btn-warning">Editar</a>
                                    <button onclick="confirmarBaja(<?php echo $socio->getIdsocio(); ?>)" 
                                            class="btn btn-sm btn-danger">Baja</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">No se encontraron socios</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmarBaja(idsocio) {
    if (confirm('¿Está seguro de dar de baja a este socio?')) {
        window.location.href = '<?php echo BASE_URL; ?>/index.php?modulo=socios&accion=baja&id=' + idsocio;
    }
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
