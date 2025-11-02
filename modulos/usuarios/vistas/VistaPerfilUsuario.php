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
        <h1>Perfil de Usuario</h1>
        <div class="grupo-botones">
            <a href="index.php?modulo=usuarios&accion=formulario&id=<?php echo $usuario->getIdusuario(); ?>" 
               class="boton-primario">Editar</a>
            <a href="index.php?modulo=usuarios&accion=listar" class="boton-secundario">← Volver</a>
        </div>
    </div>

    <!-- Información del Usuario -->
    <div class="tarjeta-perfil">
        <div class="encabezado-tarjeta">
            <h2>Información General</h2>
            <span class="estado-badge estado-<?php echo strtolower($usuario->getEstado()); ?>">
                <?php echo htmlspecialchars($usuario->getEstado()); ?>
            </span>
        </div>
        
        <div class="contenido-tarjeta">
            <div class="fila-info">
                <div class="campo-info">
                    <label>DNI:</label>
                    <span><?php echo htmlspecialchars($usuario->getDni()); ?></span>
                </div>
                <div class="campo-info">
                    <label>Nombre Completo:</label>
                    <span><?php echo htmlspecialchars($usuario->getNombrecompleto()); ?></span>
                </div>
            </div>

            <div class="fila-info">
                <div class="campo-info">
                    <label>Nombre de Usuario:</label>
                    <span><?php echo htmlspecialchars($usuario->getNombreusuario()); ?></span>
                </div>
                <div class="campo-info">
                    <label>Email:</label>
                    <span><?php echo htmlspecialchars($usuario->getEmail() ?? 'No registrado'); ?></span>
                </div>
            </div>

            <div class="fila-info">
                <div class="campo-info">
                    <label>Teléfono:</label>
                    <span><?php echo htmlspecialchars($usuario->getTelefono() ?? 'No registrado'); ?></span>
                </div>
                <div class="campo-info">
                    <label>Rol:</label>
                    <span><?php echo htmlspecialchars($usuario->getRol()); ?></span>
                </div>
            </div>

            <div class="campo-info campo-completo">
                <label>Dirección:</label>
                <span><?php echo htmlspecialchars($usuario->getDireccion() ?? 'No registrada'); ?></span>
            </div>

            <?php if ($usuario->getIdsocio()): ?>
                <div class="campo-info campo-completo">
                    <label>Vinculado al Socio:</label>
                    <span><?php echo htmlspecialchars($usuario->nombre_socio ?? 'Socio #' . $usuario->getIdsocio()); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($usuario->getIdsocio() && !empty($cursos)): ?>
    <!-- Cursos Inscritos (si es socio) -->
    <div class="tarjeta-perfil">
        <div class="encabezado-tarjeta">
            <h2>Cursos Inscritos</h2>
        </div>
        
        <div class="contenido-tarjeta">
            <table class="tabla-datos tabla-compacta">
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Estado Pago</th>
                        <th>Estado Curso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cursos as $curso): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($curso['nombrecurso'] ?? ''); ?></td>
                            <td><?php echo !empty($curso['fechainicio']) ? date('d/m/Y', strtotime($curso['fechainicio'])) : ''; ?></td>
                            <td><?php echo !empty($curso['fechafin']) ? date('d/m/Y', strtotime($curso['fechafin'])) : ''; ?></td>
                            <td>
                                <span class="estado-badge estado-<?php echo strtolower($curso['estadopagocurso'] ?? ''); ?>">
                                    <?php echo htmlspecialchars($curso['estadopagocurso'] ?? ''); ?>
                                </span>
                            </td>
                            <td>
                                <?php $estadoCurso = $curso['estado_curso'] ?? $curso['estado'] ?? ''; ?>
                                <span class="estado-badge estado-<?php echo strtolower($estadoCurso); ?>">
                                    <?php echo htmlspecialchars($estadoCurso); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>