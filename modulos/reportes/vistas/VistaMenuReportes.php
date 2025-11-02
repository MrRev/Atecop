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
            <!-- Cambiado a botones que construyen la URL con JS para evitar problemas con handlers globales de submit -->
            <div class="form-inline">
                <input type="hidden" name="modulo" value="reportes">
                <input type="hidden" name="accion" value="vencimientos">
                <label>Días: 
                    <input type="number" id="dias_vencimientos" name="dias" value="30" min="1" max="365" class="input-pequeno">
                </label>
                <div class="botones-reporte">
                    <button type="button" id="btn_venc_html" class="btn btn-secundario">Ver HTML</button>
                    <button type="button" id="btn_venc_pdf" class="btn btn-primario">PDF</button>
                    <button type="button" id="btn_venc_excel" class="btn btn-primario">Excel</button>
                </div>
            </div>
            <script>
                (function(){
                    const inputDias = document.getElementById('dias_vencimientos');
                    const btnHtml = document.getElementById('btn_venc_html');
                    const btnPdf = document.getElementById('btn_venc_pdf');
                    const btnExcel = document.getElementById('btn_venc_excel');

                    function buildUrl(formato) {
                        const dias = encodeURIComponent(inputDias.value || '30');
                        let url = 'index.php?modulo=reportes&accion=vencimientos&dias=' + dias;
                        if (formato) url += '&formato=' + formato;
                        return url;
                    }

                    btnHtml.addEventListener('click', function(){
                        window.location.href = buildUrl();
                    });
                    btnPdf.addEventListener('click', function(){
                        window.location.href = buildUrl('pdf');
                    });
                    btnExcel.addEventListener('click', function(){
                        window.location.href = buildUrl('excel');
                    });
                })();
            </script>
        </div>

        <!-- Reporte 3: Detalle de Socio -->
        <div class="tarjeta-reporte">
            <div class="icono-reporte">👤</div>
            <h3>Detalle de Socio</h3>
            <p>Reporte completo de un socio específico (buscar por DNI)</p>
            <!-- Usamos DNI en lugar de ID y botones JS para construir la URL -->
            <div class="form-inline">
                <input type="hidden" name="modulo" value="reportes">
                <input type="hidden" name="accion" value="socio">
                <label>DNI: 
                    <input type="text" id="dni_socio" name="dni" required class="input-pequeno" placeholder="Ej: 12345678">
                </label>
                <div class="botones-reporte">
                    <button type="button" id="btn_socio_html" class="btn btn-secundario">Ver HTML</button>
                    <button type="button" id="btn_socio_pdf" class="btn btn-primario">PDF</button>
                </div>
            </div>
            <script>
                (function(){
                    const inputDni = document.getElementById('dni_socio');
                    const btnHtml = document.getElementById('btn_socio_html');
                    const btnPdf = document.getElementById('btn_socio_pdf');

                    function buildUrl(formato) {
                        const dni = encodeURIComponent(inputDni.value.trim());
                        if (!dni) {
                            alert('Ingrese el DNI del socio');
                            return null;
                        }
                        let url = 'index.php?modulo=reportes&accion=socio&dni=' + dni;
                        if (formato) url += '&formato=' + formato;
                        return url;
                    }

                    btnHtml.addEventListener('click', function(){
                        const url = buildUrl();
                        if (url) window.location.href = url;
                    });
                    btnPdf.addEventListener('click', function(){
                        const url = buildUrl('pdf');
                        if (url) window.location.href = url;
                    });
                })();
            </script>
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