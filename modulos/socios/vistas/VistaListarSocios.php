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
                                    <a href="<?php echo BASE_URL; ?>/index.php?modulo=socios&accion=editar&id=<?php echo $socio->getIdsocio(); ?>" 
                                        class="btn btn-sm btn-warning">Editar</a>
                                    
                                    <?php if ($socio->getEstado() != 'Inactivo'): ?>
                                        <button onclick="confirmarBaja(<?php echo $socio->getIdsocio(); ?>)" 
                                                class="btn btn-sm btn-danger">Baja</button>
                                    <?php else: ?>
                                        <button onclick="confirmarReactivar(<?php echo $socio->getIdsocio(); ?>)" 
                                                class="btn btn-sm btn-success">Activar</button>
                                    <?php endif; ?>
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
    /**
     * Confirma y ejecuta la reactivación de un socio
     */
    async function confirmarReactivar(idsocio) {
        
        if (!confirm('¿Está seguro de reactivar a este socio?')) {
            return; // El usuario canceló
        }
    
        // Apuntamos a la nueva acción
        const url = `<?php echo BASE_URL; ?>/index.php?modulo=socios&accion=reactivar&id=${idsocio}`; 
    
        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
    
            if (!response.ok) {
                if (response.status === 401) {
                    alert('Sesión expirada. Redirigiendo al login...');
                    window.location.href = 'index.php?modulo=seguridad&accion=login';
                } else {
                    throw new Error('Error en la respuesta del servidor');
                }
                return;
            }
    
            const data = await response.json();
    
            if (data.success) {
                alert(data.message); // "Socio reactivado correctamente"
                location.reload(); 
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error al reactivar:', error);
            alert('Error de conexión.');
        }
    }

    async function confirmarBaja(idsocio) {
        
        // 1. Pide confirmación (esto ya lo tenías)
        if (!confirm('¿Está seguro de dar de baja a este socio?')) {
            return; // El usuario hizo clic en "Cancelar"
        }

        // 2. Construir la URL
        const url = `<?php echo BASE_URL; ?>/index.php?modulo=socios&accion=baja&id=${idsocio}`;

        try {
            // 3. Hacer la llamada AJAX (Fetch)
            const response = await fetch(url, {
                method: 'GET', // Tu enrutador (index.php) espera un GET
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' // Importante para la sesión
                },
                credentials: 'same-origin'
            });

            // 4. Manejar si la sesión expiró (error 401)
            if (!response.ok) {
                if (response.status === 401) {
                    alert('Sesión expirada. Por favor, inicie sesión de nuevo.');
                    window.location.href = 'index.php?modulo=seguridad&accion=login';
                } else {
                    // Otro error de servidor (ej. 500)
                    throw new Error('Error en la respuesta del servidor: ' + response.status);
                }
                return;
            }

            // 5. Leer la respuesta JSON que envía tu controlador
            const data = await response.json();

            // 6. ¡ESTA ES LA LÓGICA QUE QUERÍAS!
            if (data.success) {
                // Mostrar el mensajito de éxito
                alert(data.message); 
                
                // Recargar la página para ver el estado actualizado
                location.reload(); 
            } else {
                // Mostrar un error si el PHP falló
                alert('Error: ' + data.message);
            }

        } catch (error) {
            console.error('Error al dar de baja:', error);
            alert('Error de conexión. No se pudo completar la acción.');
        }
    }
</script>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
