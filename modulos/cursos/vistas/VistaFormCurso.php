<?php
// Verificar sesión
session_start();
if (!isset($_SESSION['idadmin'])) {
    header('Location: index.php?modulo=seguridad&accion=login');
    exit;
}

$esEdicion = isset($curso) && $curso != null;
$titulo = $esEdicion ? 'Editar Curso' : 'Crear Nuevo Curso';

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="contenedor-principal">
    <div class="encabezado-seccion">
        <h1><?php echo $titulo; ?></h1>
        <a href="index.php?modulo=cursos&accion=listar" class="boton-secundario">
            ← Volver al listado
        </a>
    </div>

    <?php if (isset($error)): ?>
        <div class="mensaje-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?modulo=cursos&accion=<?php echo $esEdicion ? 'modificar' : 'crear'; ?>" 
          class="formulario-principal" id="formCurso">
        
        <?php if ($esEdicion): ?>
            <input type="hidden" name="idcurso" value="<?php echo $curso['idcurso']; ?>">
        <?php endif; ?>

        <div class="seccion-formulario">
            <h2>Información del Curso</h2>
            
            <div class="campo-formulario">
                <label for="nombrecurso">Nombre del Curso *</label>
                <input type="text" id="nombrecurso" name="nombrecurso" 
                       value="<?php echo $esEdicion ? htmlspecialchars($curso['nombrecurso']) : ''; ?>"
                       required maxlength="255"
                       placeholder="Ej: Gestión de Proyectos con MS Project">
            </div>

            <div class="campo-formulario">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4"
                          placeholder="Descripción detallada del curso"><?php echo $esEdicion ? htmlspecialchars($curso['descripcion']) : ''; ?></textarea>
            </div>

            <div class="fila-formulario">
                <div class="campo-formulario">
                    <label for="idponente">Ponente</label>
                    <select id="idponente" name="idponente">
                        <option value="">Sin asignar</option>
                        <?php if (isset($ponentes)): ?>
                            <?php foreach ($ponentes as $ponente): ?>
                                <option value="<?php echo $ponente['idponente']; ?>"
                                    <?php echo ($esEdicion && $curso['idponente'] == $ponente['idponente']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ponente['nombrecompleto']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="campo-formulario">
                    <label for="cupostotales">Cupos Totales *</label>
                    <input type="number" id="cupostotales" name="cupostotales" 
                           value="<?php echo $esEdicion ? $curso['cupostotales'] : ''; ?>"
                           required min="1" max="500"
                           placeholder="Ej: 30">
                </div>
            </div>

            <div class="fila-formulario">
                <div class="campo-formulario">
                    <label for="costoinscripcion">Costo de Inscripción (S/) *</label>
                    <input type="number" id="costoinscripcion" name="costoinscripcion" 
                           value="<?php echo $esEdicion ? $curso['costoinscripcion'] : ''; ?>"
                           required min="0" step="0.01"
                           placeholder="Ej: 150.00">
                </div>

                <div class="campo-formulario">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="Programado" <?php echo ($esEdicion && $curso['estado'] == 'Programado') ? 'selected' : ''; ?>>Programado</option>
                        <option value="En Curso" <?php echo ($esEdicion && $curso['estado'] == 'En Curso') ? 'selected' : ''; ?>>En Curso</option>
                        <option value="Finalizado" <?php echo ($esEdicion && $curso['estado'] == 'Finalizado') ? 'selected' : ''; ?>>Finalizado</option>
                        <option value="Cancelado" <?php echo ($esEdicion && $curso['estado'] == 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>
            </div>

            <div class="fila-formulario">
                <div class="campo-formulario">
                    <label for="fechainicio">Fecha de Inicio</label>
                    <input type="date" id="fechainicio" name="fechainicio" 
                           value="<?php echo $esEdicion ? $curso['fechainicio'] : ''; ?>">
                </div>

                <div class="campo-formulario">
                    <label for="fechafin">Fecha de Fin</label>
                    <input type="date" id="fechafin" name="fechafin" 
                           value="<?php echo $esEdicion ? $curso['fechafin'] : ''; ?>">
                </div>
            </div>

            <div class="campo-formulario">
                <label for="urlenlacevirtual">Enlace Virtual (Zoom, Meet, etc.)</label>
                <input type="url" id="urlenlacevirtual" name="urlenlacevirtual" 
                       value="<?php echo $esEdicion ? htmlspecialchars($curso['urlenlacevirtual']) : ''; ?>"
                       maxlength="255"
                       placeholder="https://zoom.us/j/123456789">
            </div>
        </div>

        <div class="botones-formulario">
            <button type="submit" class="boton-primario">
                <?php echo $esEdicion ? 'Actualizar Curso' : 'Crear Curso'; ?>
            </button>
            <a href="index.php?modulo=cursos&accion=listar" class="boton-cancelar">Cancelar</a>
        </div>
    </form>
</div>

<script>
document.getElementById('formCurso').addEventListener('submit', function(e) {
    const fechaInicio = document.getElementById('fechainicio').value;
    const fechaFin = document.getElementById('fechafin').value;
    
    if (fechaInicio && fechaFin && fechaInicio > fechaFin) {
        e.preventDefault();
        alert('La fecha de inicio no puede ser posterior a la fecha de fin');
        return false;
    }
    
    const cupos = parseInt(document.getElementById('cupostotales').value);
    if (cupos < 1) {
        e.preventDefault();
        alert('Los cupos deben ser al menos 1');
        return false;
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
