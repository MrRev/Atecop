<?php
$esEdicion = isset($plan) && $plan != null;
$titulo = $esEdicion ? 'Editar Plan de Membresía' : 'Crear Nuevo Plan';

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="contenedor-principal">
    <div class="encabezado-seccion">
        <h1><?php echo $titulo; ?></h1>
        <a href="index.php?modulo=membresias&accion=listar" class="boton-secundario">
            ← Volver al listado
        </a>
    </div>

    <?php if (isset($error)): ?>
        <div class="mensaje-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?modulo=membresias&accion=guardar" 
        class="formulario-principal" id="formPlan">
        
        <?php if ($esEdicion): ?>
            <input type="hidden" name="idplan" value="<?php echo $plan['idplan']; ?>">
        <?php endif; ?>

        <div class="seccion-formulario">
            <h2>Información del Plan</h2>
            
            <div class="campo-formulario">
                <label for="nombreplan">Nombre del Plan *</label>
                <input type="text" id="nombreplan" name="nombreplan" 
                       value="<?php echo $esEdicion ? htmlspecialchars($plan['nombreplan']) : ''; ?>"
                       required maxlength="100"
                       placeholder="Ej: Membresía Anual, Membresía Trimestral">
            </div>

            <div class="fila-formulario">
                <div class="campo-formulario">
                    <label for="duracionmeses">Duración (meses) *</label>
                    <input type="number" id="duracionmeses" name="duracionmeses" 
                           value="<?php echo $esEdicion ? $plan['duracionmeses'] : ''; ?>"
                           required min="1" max="120"
                           placeholder="Ej: 1, 3, 6, 12">
                    <small>Ingrese la duración en meses (1 = Mensual, 12 = Anual)</small>
                </div>

                <div class="campo-formulario">
                    <label for="costo">Costo (S/) *</label>
                    <input type="number" id="costo" name="costo" 
                           value="<?php echo $esEdicion ? $plan['costo'] : ''; ?>"
                           required min="0" step="0.01"
                           placeholder="Ej: 100.00">
                    <small>Ingrese el costo en soles</small>
                </div>
            </div>

            <?php if ($esEdicion): ?>
            <div class="campo-formulario">
                <label for="estado">Estado</label>
                <select id="estado" name="estado">
                    <option value="Activo" <?php echo ($plan['estado'] == 'Activo') ? 'selected' : ''; ?>>Activo</option>
                    <option value="Inactivo" <?php echo ($plan['estado'] == 'Inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                </select>
                <small>Los planes inactivos no aparecerán en el registro de socios</small>
            </div>
            <?php endif; ?>
        </div>

        <div class="botones-formulario">
            <button type="submit" class="boton-primario">
                <?php echo $esEdicion ? 'Actualizar Plan' : 'Crear Plan'; ?>
            </button>
            <a href="index.php?modulo=membresias&accion=listar" class="boton-cancelar">Cancelar</a>
        </div>
    </form>
</div>

<script>
document.getElementById('formPlan').addEventListener('submit', function(e) {
    const duracion = parseInt(document.getElementById('duracionmeses').value);
    const costo = parseFloat(document.getElementById('costo').value);
    
    if (duracion < 1) {
        e.preventDefault();
        alert('La duración debe ser al menos 1 mes');
        return false;
    }
    
    if (costo < 0) {
        e.preventDefault();
        alert('El costo no puede ser negativo');
        return false;
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
