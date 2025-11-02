<?php
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="contenedor-principal">
    <div class="encabezado-seccion">
        <h1>Gestión de Cursos</h1>
        <a href="index.php?modulo=cursos&accion=crear" class="boton-primario">
            + Nuevo Curso
        </a>
    </div>

    <?php if (isset($mensaje)): ?>
        <div class="mensaje-exito">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="panel-filtros">
        <form method="GET" action="index.php" class="formulario-filtros">
            <input type="hidden" name="modulo" value="cursos">
            <input type="hidden" name="accion" value="listar">
            
            <div class="grupo-filtros">
                <select name="estado" class="select-filtro">
                    <option value="">Todos los estados</option>
                    <option value="Programado" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Programado') ? 'selected' : ''; ?>>Programado</option>
                    <option value="En Curso" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'En Curso') ? 'selected' : ''; ?>>En Curso</option>
                    <option value="Finalizado" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Finalizado') ? 'selected' : ''; ?>>Finalizado</option>
                    <option value="Cancelado" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                </select>
                
                <button type="submit" class="boton-secundario">Filtrar</button>
                <a href="index.php?modulo=cursos&accion=listar" class="boton-limpiar">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="tabla-contenedor">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Nombre del Curso</th>
                    <th>Ponente</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Cupos</th>
                    <th>Costo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($cursos) && count($cursos) > 0): ?>
                    <?php foreach ($cursos as $curso): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($curso['nombrecurso']); ?></td>
                            <td><?php echo htmlspecialchars($curso['nombreponente'] ?? 'Sin asignar'); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($curso['fechainicio'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($curso['fechafin'])); ?></td>
                            <td><?php echo $curso['cupostotales']; ?></td>
                            <td>S/ <?php echo number_format($curso['costoinscripcion'], 2); ?></td>
                            <td>
                                <span class="estado-badge estado-<?php echo strtolower(str_replace(' ', '-', $curso['estado'])); ?>">
                                    <?php echo htmlspecialchars($curso['estado']); ?>
                                </span>
                            </td>
                            <td class="acciones-celda">
                                <a href="index.php?modulo=cursos&accion=inscripciones&id=<?php echo $curso['idcurso']; ?>" 
                                   class="boton-accion boton-ver" title="Gestionar inscripciones">Inscritos</a>
                                <a href="index.php?modulo=cursos&accion=editar&id=<?php echo $curso['idcurso']; ?>" 
                                    class="boton-accion boton-editar">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="texto-centrado">No hay cursos registrados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>