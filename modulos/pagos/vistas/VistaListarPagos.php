<?php
require_once __DIR__ . '/../../layouts/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="container">
    <div class="header-contenedor">
        <h2>Gestión de Pagos</h2>
        <a href="<?php echo BASE_URL; ?>/index.php?modulo=pagos&accion=registrar" class="btn btn-primario">
            + Nuevo Pago
        </a>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <div class="tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Socio</th>
                    <th>Monto</th>
                    <th>Fecha de Pago</th>
                    <th>Concepto</th>
                    <th>Método de Pago</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pagos as $pago): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pago['nombresocio'] ?? 'No especificado'); ?></td>
                        <td>S/ <?php echo number_format($pago['monto'], 2); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($pago['fechapago'])); ?></td>
                        <td><?php echo htmlspecialchars($pago['concepto']); ?></td>
                        <td><?php echo htmlspecialchars($pago['nombremetodo'] ?? 'No especificado'); ?></td>
                        <td>
                            <span class="badge <?php echo $pago['estado'] === 'Registrado' ? 'badge-success' : 'badge-danger'; ?>">
                                <?php echo htmlspecialchars($pago['estado']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($pago['estado'] === 'Registrado'): ?>
                                <a href="<?php echo BASE_URL; ?>/index.php?modulo=pagos&accion=anular&id=<?php echo $pago['idpago']; ?>" 
                                   class="btn btn-peligro btn-sm"
                                   onclick="return confirm('¿Está seguro de anular este pago?');">
                                    Anular
                                </a>
                            <?php endif; ?>
                            <?php if ($pago['urlcomprobante']): ?>
                                <a href="<?php echo BASE_URL . '/public/uploads/comprobantes/' . $pago['urlcomprobante']; ?>" 
                                   class="btn btn-secundario btn-sm" 
                                   target="_blank">
                                    Ver Comprobante
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>