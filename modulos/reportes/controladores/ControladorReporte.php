<?php

require_once __DIR__ . '/../dao/ReporteDAO.php';
require_once __DIR__ . '/../util/PDFGenerator.php';
require_once __DIR__ . '/../util/ExcelGenerator.php';

/**
 * Clase ControladorReporte
 * Maneja la generación de reportes en diferentes formatos
 */
class ControladorReporte {
    private $reporteDAO;

    public function __construct() {
        $this->reporteDAO = new ReporteDAO();
    }

    /**
     * Muestra el menú principal de reportes
     */
    public function mostrarMenu() {
        require_once __DIR__ . '/../vistas/VistaMenuReportes.php';
    }

    /**
     * Genera reporte de socios morosos
     */
    public function reporteSociosMorosos() {
        $formato = $_GET['formato'] ?? 'html';
        $datos = $this->reporteDAO->getSociosMorosos();

        switch ($formato) {
            case 'pdf':
                $pdf = new PDFGenerator();
                $pdf->generarReporteSociosMorosos($datos);
                break;
            case 'excel':
                $excel = new ExcelGenerator();
                $excel->generarReporteSociosMorosos($datos);
                break;
            default:
                require_once __DIR__ . '/../vistas/VistaReporteSociosMorosos.php';
        }
    }

    /**
     * Genera reporte de próximos vencimientos
     */
    public function reporteProximosVencimientos() {
        $formato = $_GET['formato'] ?? 'html';
        $dias = $_GET['dias'] ?? 30;
        $datos = $this->reporteDAO->getProximosVencimientos($dias);

        switch ($formato) {
            case 'pdf':
                $pdf = new PDFGenerator();
                $pdf->generarReporteProximosVencimientos($datos, $dias);
                break;
            case 'excel':
                $excel = new ExcelGenerator();
                $excel->generarReporteProximosVencimientos($datos, $dias);
                break;
            default:
                require_once __DIR__ . '/../vistas/VistaReporteProximosVencimientos.php';
        }
    }

    /**
     * Genera reporte detallado de un socio
     */
    public function reporteDetalleSocio() {
        $idsocio = $_GET['idsocio'] ?? null;
        $formato = $_GET['formato'] ?? 'html';

        if (!$idsocio) {
            header('Location: index.php?modulo=reportes&accion=menu&error=socio_requerido');
            exit;
        }

        $datos = $this->reporteDAO->getDatosCompletosSocio($idsocio);

        if (!$datos) {
            header('Location: index.php?modulo=reportes&accion=menu&error=socio_no_encontrado');
            exit;
        }

        switch ($formato) {
            case 'pdf':
                $pdf = new PDFGenerator();
                $pdf->generarReporteDetalleSocio($datos);
                break;
            default:
                require_once __DIR__ . '/../vistas/VistaReporteDetalleSocio.php';
        }
    }

    /**
     * Genera reporte de socios para inhabilitar
     */
    public function reporteSociosInhabilitar() {
        $formato = $_GET['formato'] ?? 'html';
        $diasMora = $_GET['dias_mora'] ?? 60;
        $datos = $this->reporteDAO->getSociosParaInhabilitar($diasMora);

        switch ($formato) {
            case 'pdf':
                $pdf = new PDFGenerator();
                $pdf->generarReporteSociosInhabilitar($datos, $diasMora);
                break;
            case 'excel':
                $excel = new ExcelGenerator();
                $excel->generarReporteSociosInhabilitar($datos, $diasMora);
                break;
            default:
                require_once __DIR__ . '/../vistas/VistaReporteSociosInhabilitar.php';
        }
    }
}
