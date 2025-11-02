<?php
// (La verificación de sesión ya se hizo en index.php)
require_once __DIR__ . '/../../layouts/header.php';
?>

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
                <!-- CORRECCIÓN: 'accion=morosos' -->
                <a href="index.php?modulo=reportes&accion=morosos" class="btn btn-secundario">Ver HTML</a>
                <a href="index.php?modulo=reportes&accion=morosos&formato=pdf" class="btn btn-primario">Descargar PDF</a>
                <a href="index.php?modulo=reportes&accion=morosos&formato=excel" class="btn btn-primario">Descargar Excel</a>
            </div>
        </div>

        <!-- Reporte 2: Próximos Vencimientos -->
        <div class="tarjeta-reporte">
            <div class="icono-reporte">📅</div>
            <h3>Próximos Vencimientos</h3>
            <p>Socios con vencimientos en los próximos días</p>
            <form method="get" action="index.php" class="form-inline">
                <input type="hidden" name="modulo" value="reportes">
                <!-- CORRECCIÓN: 'accion=vencimientos' -->
                <input type="hidden" name="accion" value="vencimientos">
                <label>Días: 
                    <input type="number" name="dias" value="30" min="1" max="365" class="input-pequeno">
                </label>
                <div class="botones-reporte">
                    <button type="submit" class="btn btn-secundario">Ver HTML</button>
                    <!-- Los 'formaction' ahora incluyen la ruta completa Y el formato -->
                    <button type="submit" formaction="index.php?modulo=reportes&accion=vencimientos&formato=pdf" class="btn btn-primario">PDF</button>
                    <button type="submit" formaction="index.php?modulo=reportes&accion=vencimientos&formato=excel" class="btn btn-primario">Excel</button>
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
                <!-- CORRECCIÓN: 'accion=socio' -->
                <input type="hidden" name="accion" value="socio">
                <label>ID Socio: 
                    <!-- CORRECCIÓN: El index.php espera 'id', no 'idsocio' -->
                    <input type="number" name="id" required class="input-pequeno">
                </label>
                <div class="botones-reporte">
                    <button type="submit" class="btn btn-secundario">Ver HTML</button>
                    <!-- CORRECCIÓN: Los 'formaction' deben incluir la ruta completa -->
                    <button type="submit" formaction="index.php?modulo=reportes&accion=socio&formato=pdf" class="btn btn-primario">PDF</button>
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
                <!-- CORRECCIÓN: 'accion=inhabilitar' -->
                <input type="hidden" name="accion" value="inhabilitar">
                <label>Días de mora: 
                    <input type="number" name="dias_mora" value="60" min="1" class="input-pequeno">
                </label>
                <div class="botones-reporte">
                    <button type="submit" class="btn btn-secundario">Ver HTML</button>
                    <!-- CORRECCIÓN: Los 'formaction' deben incluir la ruta completa -->
                    <button type="submit" formaction="index.php?modulo=reportes&accion=inhabilitar&formato=pdf" class="btn btn-primario">PDF</button>
                    <button type="submit" formaction="index.php?modulo=reportes&accion=inhabilitar&formato=excel" class="btn btn-primario">Excel</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>
</body>
</html>