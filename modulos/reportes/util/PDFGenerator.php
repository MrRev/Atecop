<?php

require_once __DIR__ . '/../../../vendor/setasign/fpdf/fpdf.php';

// No usar namespace, la clase es FPDF

/**
 * Clase PDFGenerator
 * Genera reportes en formato PDF usando FPDF
 */
class PDFGenerator extends FPDF {
    
    // Colores corporativos ATECOP
    private $colorPrimario = [0, 46, 93]; // #002E5D Azul Marino
    private $colorSecundario = [59, 175, 218]; // #3BAFDA Celeste
    private $colorTexto = [112, 112, 112]; // #707070 Gris

    /**
     * Encabezado del PDF
     */
    function Header() {
        // Logo (si existe)
        if (file_exists(__DIR__ . '/../../../public/img/logo-atecop.png')) {
            $this->Image(__DIR__ . '/../../../public/img/logo-atecop.png', 10, 6, 30);
        }
        
        // Título
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor($this->colorPrimario[0], $this->colorPrimario[1], $this->colorPrimario[2]);
        $this->Cell(0, 10, 'ATECOP - Sistema de Gestion', 0, 1, 'C');
        $this->Ln(5);
    }

    /**
     * Pie de página
     */
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor($this->colorTexto[0], $this->colorTexto[1], $this->colorTexto[2]);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . ' - Generado el ' . date('d/m/Y H:i'), 0, 0, 'C');
    }

    /**
     * Genera reporte de socios morosos
     */
    public function generarReporteSociosMorosos($datos) {
        $this->AddPage();
        
        // Título del reporte
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor($this->colorPrimario[0], $this->colorPrimario[1], $this->colorPrimario[2]);
        $this->Cell(0, 10, 'Reporte de Socios Morosos', 0, 1, 'C');
        $this->Ln(5);

        // Encabezados de tabla
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor($this->colorPrimario[0], $this->colorPrimario[1], $this->colorPrimario[2]);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(25, 8, 'DNI/RUC', 1, 0, 'C', true);
        $this->Cell(60, 8, 'Nombre Completo', 1, 0, 'C', true);
        $this->Cell(35, 8, 'Tipo Socio', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Vencimiento', 1, 0, 'C', true);
        $this->Cell(40, 8, 'Plan', 1, 1, 'C', true);

        // Datos
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor($this->colorTexto[0], $this->colorTexto[1], $this->colorTexto[2]);
        
        foreach ($datos as $row) {
            $this->Cell(25, 7, $row['dni'], 1);
            $this->Cell(60, 7, substr($row['nombrecompleto'], 0, 30), 1);
            $this->Cell(35, 7, $row['nombretipo'], 1);
            $this->Cell(30, 7, date('d/m/Y', strtotime($row['fechavencimiento'])), 1);
            $this->Cell(40, 7, $row['nombreplan'], 1);
            $this->Ln();
        }

        // Total
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 8, 'Total de socios morosos: ' . count($datos), 0, 1);

        // Salida
        $this->Output('D', 'reporte_socios_morosos_' . date('Ymd') . '.pdf');
    }

    /**
     * Genera reporte de próximos vencimientos
     */
    public function generarReporteProximosVencimientos($datos, $dias) {
        $this->AddPage();
        
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor($this->colorPrimario[0], $this->colorPrimario[1], $this->colorPrimario[2]);
        $this->Cell(0, 10, 'Reporte de Proximos Vencimientos (' . $dias . ' dias)', 0, 1, 'C');
        $this->Ln(5);

        // Encabezados
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor($this->colorSecundario[0], $this->colorSecundario[1], $this->colorSecundario[2]);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(25, 8, 'DNI/RUC', 1, 0, 'C', true);
        $this->Cell(55, 8, 'Nombre Completo', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Vencimiento', 1, 0, 'C', true);
        $this->Cell(20, 8, 'Dias Rest.', 1, 0, 'C', true);
        $this->Cell(35, 8, 'Plan', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Telefono', 1, 1, 'C', true);

        // Datos
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor($this->colorTexto[0], $this->colorTexto[1], $this->colorTexto[2]);
        
        foreach ($datos as $row) {
            $this->Cell(25, 7, $row['dni'], 1);
            $this->Cell(55, 7, substr($row['nombrecompleto'], 0, 28), 1);
            $this->Cell(30, 7, date('d/m/Y', strtotime($row['fechavencimiento'])), 1);
            $this->Cell(20, 7, $row['dias_restantes'], 1, 0, 'C');
            $this->Cell(35, 7, $row['nombreplan'], 1);
            $this->Cell(25, 7, $row['telefono'], 1);
            $this->Ln();
        }

        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 8, 'Total de socios: ' . count($datos), 0, 1);

        $this->Output('D', 'reporte_proximos_vencimientos_' . date('Ymd') . '.pdf');
    }

    /**
     * Genera reporte detallado de un socio
     */
    public function generarReporteDetalleSocio($datos) {
        $this->AddPage();
        $socio = $datos['socio'];
        
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor($this->colorPrimario[0], $this->colorPrimario[1], $this->colorPrimario[2]);
        $this->Cell(0, 10, 'Reporte Detallado de Socio', 0, 1, 'C');
        $this->Ln(5);

        // Datos del socio
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 8, 'Datos Personales', 0, 1);
        $this->SetFont('Arial', '', 10);
        $this->Cell(50, 6, 'DNI/RUC:', 0, 0);
        $this->Cell(0, 6, $socio['dni'], 0, 1);
        $this->Cell(50, 6, 'Nombre Completo:', 0, 0);
        $this->Cell(0, 6, $socio['nombrecompleto'], 0, 1);
        $this->Cell(50, 6, 'Email:', 0, 0);
        $this->Cell(0, 6, $socio['email'], 0, 1);
        $this->Cell(50, 6, 'Telefono:', 0, 0);
        $this->Cell(0, 6, $socio['telefono'], 0, 1);
        $this->Cell(50, 6, 'Tipo de Socio:', 0, 0);
        $this->Cell(0, 6, $socio['nombretipo'], 0, 1);
        $this->Cell(50, 6, 'Estado:', 0, 0);
        $this->Cell(0, 6, $socio['estado'], 0, 1);
        $this->Ln(5);

        // Membresía
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 8, 'Membresia', 0, 1);
        $this->SetFont('Arial', '', 10);
        $this->Cell(50, 6, 'Plan:', 0, 0);
        $this->Cell(0, 6, $socio['nombreplan'], 0, 1);
        $this->Cell(50, 6, 'Fecha Vencimiento:', 0, 0);
        $this->Cell(0, 6, date('d/m/Y', strtotime($socio['fechavencimiento'])), 0, 1);
        $this->Ln(5);

        // Historial de pagos
        if (!empty($datos['pagos'])) {
            $this->SetFont('Arial', 'B', 11);
            $this->Cell(0, 8, 'Historial de Pagos', 0, 1);
            
            $this->SetFont('Arial', 'B', 9);
            $this->SetFillColor($this->colorSecundario[0], $this->colorSecundario[1], $this->colorSecundario[2]);
            $this->SetTextColor(255, 255, 255);
            $this->Cell(25, 7, 'Fecha', 1, 0, 'C', true);
            $this->Cell(25, 7, 'Monto', 1, 0, 'C', true);
            $this->Cell(80, 7, 'Concepto', 1, 0, 'C', true);
            $this->Cell(35, 7, 'Metodo', 1, 0, 'C', true);
            $this->Cell(25, 7, 'Estado', 1, 1, 'C', true);

            $this->SetFont('Arial', '', 8);
            $this->SetTextColor($this->colorTexto[0], $this->colorTexto[1], $this->colorTexto[2]);
            
            foreach ($datos['pagos'] as $pago) {
                $this->Cell(25, 6, date('d/m/Y', strtotime($pago['fechapago'])), 1);
                $this->Cell(25, 6, 'S/ ' . number_format($pago['monto'], 2), 1);
                $this->Cell(80, 6, substr($pago['concepto'], 0, 40), 1);
                $this->Cell(35, 6, $pago['nombremetodo'], 1);
                $this->Cell(25, 6, $pago['estado'], 1);
                $this->Ln();
            }
        }

        $this->Output('D', 'reporte_socio_' . $socio['dni'] . '_' . date('Ymd') . '.pdf');
    }

    /**
     * Genera reporte de socios para inhabilitar
     */
    public function generarReporteSociosInhabilitar($datos, $diasMora) {
        $this->AddPage();
        
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor($this->colorPrimario[0], $this->colorPrimario[1], $this->colorPrimario[2]);
        $this->Cell(0, 10, 'Reporte de Socios para Inhabilitar', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, '(Mora mayor a ' . $diasMora . ' dias)', 0, 1, 'C');
        $this->Ln(5);

        // Encabezados
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(220, 53, 69); // Rojo para alerta
        $this->SetTextColor(255, 255, 255);
        $this->Cell(25, 8, 'DNI/RUC', 1, 0, 'C', true);
        $this->Cell(60, 8, 'Nombre Completo', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Vencimiento', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Dias Mora', 1, 0, 'C', true);
        $this->Cell(50, 8, 'Email', 1, 1, 'C', true);

        // Datos
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor($this->colorTexto[0], $this->colorTexto[1], $this->colorTexto[2]);
        
        foreach ($datos as $row) {
            $this->Cell(25, 7, $row['dni'], 1);
            $this->Cell(60, 7, substr($row['nombrecompleto'], 0, 30), 1);
            $this->Cell(30, 7, date('d/m/Y', strtotime($row['fechavencimiento'])), 1);
            $this->Cell(25, 7, $row['dias_mora'], 1, 0, 'C');
            $this->Cell(50, 7, substr($row['email'], 0, 25), 1);
            $this->Ln();
        }

        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 8, 'Total de socios: ' . count($datos), 0, 1);

        $this->Output('D', 'reporte_socios_inhabilitar_' . date('Ymd') . '.pdf');
    }
}
