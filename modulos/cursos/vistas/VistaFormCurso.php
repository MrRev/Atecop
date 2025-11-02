<?php
// (La verificación de sesión ya se hizo en index.php)

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

    <!-- CORRECCIÓN 2: El 'action' ahora apunta a 'guardar' -->
    <form method="POST" action="index.php?modulo=cursos&accion=guardar" 
          class="formulario-principal" id="formCurso">
        
        <?php if ($esEdicion): ?>
            <!-- CORRECCIÓN 3: Sintaxis de Objeto -->
            <input type="hidden" name="idcurso" value="<?php echo $curso->getIdcurso(); ?>">
        <?php endif; ?>

        <div class="seccion-formulario">
            <h2>Información del Curso</h2>
            
            <div class="campo-formulario">
                <label for="nombrecurso">Nombre del Curso *</label>
                <!-- CORRECCIÓN 3: Sintaxis de Objeto -->
                <input type="text" id="nombrecurso" name="nombrecurso" 
                       value="<?php echo $esEdicion ? htmlspecialchars($curso->getNombrecurso()) : ''; ?>"
                       required maxlength="255"
                       placeholder="Ej: Gestión de Proyectos con MS Project">
            </div>

            <div class="campo-formulario">
                <label for="descripcion">Descripción</label>
                <!-- CORRECCIÓN 3: Sintaxis de Objeto -->
                <textarea id="descripcion" name="descripcion" rows="4"
                          placeholder="Descripción detallada del curso"><?php echo $esEdicion ? htmlspecialchars($curso->getDescripcion()) : ''; ?></textarea>
            </div>

            <div class="fila-formulario">
                <div class="campo-formulario">
                    <label for="idponente">Ponente</label>
                    <select id="idponente" name="idponente">
                        <option value="">Sin asignar</option>
                        <?php if (isset($ponentes)): ?>
                            <?php foreach ($ponentes as $ponente): ?>
                                <!-- CORRECCIÓN 3: Sintaxis de Objeto -->
                                <option value="<?php echo $ponente['idponente']; ?>"
                                    <?php echo ($esEdicion && $curso->getIdponente() == $ponente['idponente']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ponente['nombrecompleto']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="campo-formulario">
                    <label for="cupostotales">Cupos Totales *</label>
                    <!-- CORRECCIÓN 3: Sintaxis de Objeto -->
                    <input type="number" id="cupostotales" name="cupostotales" 
                           value="<?php echo $esEdicion ? $curso->getCupostotales() : ''; ?>"
                           required min="1" max="500"
                           placeholder="Ej: 30">
                </div>
            </div>

            <div class="fila-formulario">
                <div class="campo-formulario">
                    <label for="costoinscripcion">Costo de Inscripción (S/) *</label>
                    <!-- CORRECCIÓN 3: Sintaxis de Objeto -->
                    <input type="number" id="costoinscripcion" name="costoinscripcion" 
                           value="<?php echo $esEdicion ? $curso->getCostoinscripcion() : ''; ?>"
                           required min="0" step="0.01"
                           placeholder="Ej: 150.00">
                </div>

                <div class="campo-formulario">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <!-- CORRECCIÓN 3: Sintaxis de Objeto -->
                        <option value="Programado" <?php echo ($esEdicion && $curso->getEstado() == 'Programado') ? 'selected' : ''; ?>>Programado</option>
                        <option value="En Curso" <?php echo ($esEdicion && $curso->getEstado() == 'En Curso') ? 'selected' : ''; ?>>En Curso</option>
                        <option value="Finalizado" <?php echo ($esEdicion && $curso->getEstado() == 'Finalizado') ? 'selected' : ''; ?>>Finalizado</option>
                        <option value="Cancelado" <?php echo ($esEdicion && $curso->getEstado() == 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>
            </div>

            <div class="fila-formulario">
                <div class="campo-formulario">
                    <label for="fechainicio">Fecha de Inicio</label>
                    <!-- CORRECCIÓN 3: Sintaxis de Objeto -->
                    <input type="date" id="fechainicio" name="fechainicio" 
                           value="<?php echo $esEdicion ? $curso->getFechainicio() : ''; ?>">
                </div>

                <div class="campo-formulario">
                    <label for="fechafin">Fecha de Fin</label>
                    <!-- CORRECCIÓN 3: Sintaxis de Objeto -->
                    <input type="date" id="fechafin" name="fechafin" 
                           value="<?php echo $esEdicion ? $curso->getFechafin() : ''; ?>">
                </div>
            </div>

            <div class="campo-formulario">
                <label for="urlenlacevirtual">Enlace Virtual (Zoom, Meet, etc.)</label>
                <!-- CORRECCIÓN 3: Sintaxis de Objeto -->
                <input type="url" id="urlenlacevirtual" name="urlenlacevirtual" 
                       value="<?php echo $esEdicion ? htmlspecialchars($curso->getUrlenlacevirtual()) : ''; ?>"
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
