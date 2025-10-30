<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['idadmin'])) {
    header('Location: index.php?modulo=seguridad&accion=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - ATECOP</title>
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
    <?php include __DIR__ . '/../../layouts/header.php'; ?>

    <main class="contenedor">
        <div class="seccion-titulo">
            <h1>Módulo de Reportes</h1>
            <p>Genere reportes del sistema en formato HTML, PDF o Excel</p>
        </div>

        <div class="grid-reportes">
            <!-- Reporte 1: Socios Morosos -->
            <div class="tarjeta-reporte">
                <div class="icono-reporte">📊</div>
                <h3>Socios Morosos</h3>
                <p>Lista de socios con pagos vencidos y estado moroso</p>
                <div class="botones-reporte">
                    <a href="index.php?modulo=reportes&accion=socios_morosos" class="btn btn-secundario">Ver HTML</a>
                    <a href="index.php?modulo=reportes&accion=socios_morosos&formato=pdf" class="btn btn-primario">Descargar PDF</a>
                    <a href="index.php?modulo=reportes&accion=socios_morosos&formato=excel" class="btn btn-primario">Descargar Excel</a>
                </div>
            </div>

            <!-- Reporte 2: Próximos Vencimientos -->
            <div class="tarjeta-reporte">
                <div class="icono-reporte">📅</div>
                <h3>Próximos Vencimientos</h3>
                <p>Socios con vencimientos en los próximos días</p>
                <form method="get" action="index.php" class="form-inline">
                    <input type="hidden" name="modulo" value="reportes">
                    <input type="hidden" name="accion" value="proximos_vencimientos">
                    <label>Días: 
                        <input type="number" name="dias" value="30" min="1" max="365" class="input-pequeno">
                    </label>
                    <div class="botones-reporte">
                        <button type="submit" class="btn btn-secundario">Ver HTML</button>
                        <button type="submit" formaction="index.php?formato=pdf" class="btn btn-primario">PDF</button>
                        <button type="submit" formaction="index.php?formato=excel" class="btn btn-primario">Excel</button>
                    </div>
                </form>
            </div>

            <!-- Reporte 3: Detalle de Socio -->
            <div class="tarjeta-reporte">
                <div class="icono-reporte">👤</div>
                <h3>Detalle de Socio</h3>
                <p>Reporte completo de un socio específico</p>
                <form method="get" action="index.php" class="form-inline">
                    <input type="hidden" name="modulo" value="reportes">
                    <input type="hidden" name="accion" value="detalle_socio">
                    <label>ID Socio: 
                        <input type="number" name="idsocio" required class="input-pequeno">
                    </label>
                    <div class="botones-reporte">
                        <button type="submit" class="btn btn-secundario">Ver HTML</button>
                        <button type="submit" formaction="index.php?formato=pdf" class="btn btn-primario">PDF</button>
                    </div>
                </form>
            </div>

            <!-- Reporte 4: Socios para Inhabilitar -->
            <div class="tarjeta-reporte">
                <div class="icono-reporte">⚠️</div>
                <h3>Socios para Inhabilitar</h3>
                <p>Socios con mora prolongada que deben ser inhabilitados</p>
                <form method="get" action="index.php" class="form-inline">
                    <input type="hidden" name="modulo" value="reportes">
                    <input type="hidden" name="accion" value="socios_inhabilitar">
                    <label>Días de mora: 
                        <input type="number" name="dias_mora" value="60" min="1" class="input-pequeno">
                    </label>
                    <div class="botones-reporte">
                        <button type="submit" class="btn btn-secundario">Ver HTML</button>
                        <button type="submit" formaction="index.php?formato=pdf" class="btn btn-primario">PDF</button>
                        <button type="submit" formaction="index.php?formato=excel" class="btn btn-primario">Excel</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../../layouts/footer.php'; ?>
</body>
</html>
