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
     * CORRECCIÓN: Renombrada a 'reporteMorosos' para coincidir con index.php
     */
    public function reporteMorosos() {
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
                // Pasamos los datos a la vista
                require_once __DIR__ . '/../vistas/VistaReporteSociosMorosos.php';
        }
    }

    /**
     * Genera reporte de próximos vencimientos
     * CORRECCIÓN: Renombrada a 'reporteVencimientos' para coincidir con index.php
     */
    public function reporteVencimientos() {
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
                // Pasamos los datos y días a la vista
                require_once __DIR__ . '/../vistas/VistaReporteProximosVencimientos.php';
        }
    }

    /**
     * Genera reporte detallado de un socio
     * CORRECCIÓN: Renombrada a 'reporteSocio' y se lee $_GET['id']
     */
    public function reporteSocio($dniOrId = null) {
        // Ahora aceptamos búsqueda por DNI (param name: dni) o por id (compatibilidad)
        $formato = $_GET['formato'] ?? 'html';

        $dni = $_GET['dni'] ?? null;
        if ($dni) {
            // Buscar por DNI
            $datos = $this->reporteDAO->getDatosCompletosSocioPorDni($dni);
            if (!$datos) {
                $_SESSION['error_reporte'] = 'Socio no encontrado con DNI ' . htmlspecialchars($dni);
                header('Location: index.php?modulo=reportes&accion=menu');
                exit;
            }
        } else {
            // Compatibilidad: aceptar id (antiguo comportamiento)
            $idsocio = $dniOrId ?? ($_GET['id'] ?? null);
            if (!$idsocio) {
                $_SESSION['error_reporte'] = 'ID o DNI de socio requerido';
                header('Location: index.php?modulo=reportes&accion=menu');
                exit;
            }
            $datos = $this->reporteDAO->getDatosCompletosSocio($idsocio);
            if (!$datos) {
                $_SESSION['error_reporte'] = 'Socio no encontrado con ID ' . $idsocio;
                header('Location: index.php?modulo=reportes&accion=menu');
                exit;
            }
        }

        switch ($formato) {
            case 'pdf':
                $pdf = new PDFGenerator();
                $pdf->generarReporteDetalleSocio($datos);
                break;
            case 'excel':
                $excel = new ExcelGenerator();
                $excel->generarReporteDetalleSocio($datos);
                break;
            default:
                // Pasamos los datos a la vista
                require_once __DIR__ . '/../vistas/VistaReporteDetalleSocio.php';
        }
    }

    /**
     * Genera reporte de socios para inhabilitar
     * CORRECCIÓN: Renombrada a 'reporteInhabilitar' para coincidir con index.php
     */
    public function reporteInhabilitar() {
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
                // Pasamos los datos y días a la vista
                require_once __DIR__ . '/../vistas/VistaReporteSociosInhabilitar.php';
        }
    }
}
