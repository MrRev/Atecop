<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

/**
 * Clase ExcelGenerator
 * Genera reportes en formato Excel usando PhpSpreadsheet
 */
class ExcelGenerator {
    
    private $colorPrimario = '002E5D'; // Azul Marino ATECOP
    private $colorSecundario = '3BAFDA'; // Celeste ATECOP

    /**
     * Aplica estilos de encabezado
     */
    private function aplicarEstiloEncabezado($sheet, $rango) {
        $sheet->getStyle($rango)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $this->colorPrimario]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);
    }

    /**
     * Genera reporte de socios morosos en Excel
     */
    public function generarReporteSociosMorosos($datos) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Socios Morosos');

        // Título
        $sheet->setCellValue('A1', 'REPORTE DE SOCIOS MOROSOS');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Fecha de generación
        $sheet->setCellValue('A2', 'Generado el: ' . date('d/m/Y H:i'));
        $sheet->mergeCells('A2:F2');

        // Encabezados
        $sheet->setCellValue('A4', 'DNI/RUC');
        $sheet->setCellValue('B4', 'Nombre Completo');
        $sheet->setCellValue('C4', 'Email');
        $sheet->setCellValue('D4', 'Tipo Socio');
        $sheet->setCellValue('E4', 'Fecha Vencimiento');
        $sheet->setCellValue('F4', 'Plan');
        
        $this->aplicarEstiloEncabezado($sheet, 'A4:F4');

        // Datos
        $fila = 5;
        foreach ($datos as $row) {
            $sheet->setCellValue('A' . $fila, $row['dni']);
            $sheet->setCellValue('B' . $fila, $row['nombrecompleto']);
            $sheet->setCellValue('C' . $fila, $row['email']);
            $sheet->setCellValue('D' . $fila, $row['nombretipo']);
            $sheet->setCellValue('E' . $fila, date('d/m/Y', strtotime($row['fechavencimiento'])));
            $sheet->setCellValue('F' . $fila, $row['nombreplan']);
            $fila++;
        }

        // Ajustar anchos de columna
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Total
        $sheet->setCellValue('A' . ($fila + 1), 'Total de socios morosos: ' . count($datos));
        $sheet->mergeCells('A' . ($fila + 1) . ':F' . ($fila + 1));
        $sheet->getStyle('A' . ($fila + 1))->getFont()->setBold(true);

        // Descargar
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_socios_morosos_' . date('Ymd') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Genera reporte de próximos vencimientos en Excel
     */
    public function generarReporteProximosVencimientos($datos, $dias) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Proximos Vencimientos');

        // Título
        $sheet->setCellValue('A1', 'REPORTE DE PROXIMOS VENCIMIENTOS (' . $dias . ' DIAS)');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Generado el: ' . date('d/m/Y H:i'));
        $sheet->mergeCells('A2:G2');

        // Encabezados
        $sheet->setCellValue('A4', 'DNI/RUC');
        $sheet->setCellValue('B4', 'Nombre Completo');
        $sheet->setCellValue('C4', 'Email');
        $sheet->setCellValue('D4', 'Telefono');
        $sheet->setCellValue('E4', 'Fecha Vencimiento');
        $sheet->setCellValue('F4', 'Dias Restantes');
        $sheet->setCellValue('G4', 'Plan');
        
        $this->aplicarEstiloEncabezado($sheet, 'A4:G4');

        // Datos
        $fila = 5;
        foreach ($datos as $row) {
            $sheet->setCellValue('A' . $fila, $row['dni']);
            $sheet->setCellValue('B' . $fila, $row['nombrecompleto']);
            $sheet->setCellValue('C' . $fila, $row['email']);
            $sheet->setCellValue('D' . $fila, $row['telefono']);
            $sheet->setCellValue('E' . $fila, date('d/m/Y', strtotime($row['fechavencimiento'])));
            $sheet->setCellValue('F' . $fila, $row['dias_restantes']);
            $sheet->setCellValue('G' . $fila, $row['nombreplan']);
            $fila++;
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->setCellValue('A' . ($fila + 1), 'Total de socios: ' . count($datos));
        $sheet->mergeCells('A' . ($fila + 1) . ':G' . ($fila + 1));
        $sheet->getStyle('A' . ($fila + 1))->getFont()->setBold(true);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_proximos_vencimientos_' . date('Ymd') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Genera reporte de socios para inhabilitar en Excel
     */
    public function generarReporteSociosInhabilitar($datos, $diasMora) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Socios Inhabilitar');

        $sheet->setCellValue('A1', 'REPORTE DE SOCIOS PARA INHABILITAR (Mora > ' . $diasMora . ' dias)');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Generado el: ' . date('d/m/Y H:i'));
        $sheet->mergeCells('A2:F2');

        $sheet->setCellValue('A4', 'DNI/RUC');
        $sheet->setCellValue('B4', 'Nombre Completo');
        $sheet->setCellValue('C4', 'Email');
        $sheet->setCellValue('D4', 'Tipo Socio');
        $sheet->setCellValue('E4', 'Fecha Vencimiento');
        $sheet->setCellValue('F4', 'Dias de Mora');
        
        $this->aplicarEstiloEncabezado($sheet, 'A4:F4');

        $fila = 5;
        foreach ($datos as $row) {
            $sheet->setCellValue('A' . $fila, $row['dni']);
            $sheet->setCellValue('B' . $fila, $row['nombrecompleto']);
            $sheet->setCellValue('C' . $fila, $row['email']);
            $sheet->setCellValue('D' . $fila, $row['nombretipo']);
            $sheet->setCellValue('E' . $fila, date('d/m/Y', strtotime($row['fechavencimiento'])));
            $sheet->setCellValue('F' . $fila, $row['dias_mora']);
            
            // Resaltar en rojo si la mora es muy alta
            if ($row['dias_mora'] > 90) {
                $sheet->getStyle('F' . $fila)->getFont()->getColor()->setRGB('DC3545');
                $sheet->getStyle('F' . $fila)->getFont()->setBold(true);
            }
            
            $fila++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->setCellValue('A' . ($fila + 1), 'Total de socios: ' . count($datos));
        $sheet->mergeCells('A' . ($fila + 1) . ':F' . ($fila + 1));
        $sheet->getStyle('A' . ($fila + 1))->getFont()->setBold(true);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_socios_inhabilitar_' . date('Ymd') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Genera reporte detallado de un socio en Excel
     * Recibe $datos con keys: 'socio', 'pagos', 'cursos'
     */
    public function generarReporteDetalleSocio($datos) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $socio = $datos['socio'] ?? null;
        $pagos = $datos['pagos'] ?? [];
        $cursos = $datos['cursos'] ?? [];

        $sheet->setTitle('Detalle Socio');

        // Título
        $nombre = $socio['nombrecompleto'] ?? '';
        $dni = $socio['dni'] ?? '';
        $sheet->setCellValue('A1', 'REPORTE DETALLADO DE SOCIO');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Generado el: ' . date('d/m/Y H:i'));
        $sheet->mergeCells('A2:F2');

        // Datos personales
        $sheet->setCellValue('A4', 'Nombre:');
        $sheet->setCellValue('B4', $nombre);
        $sheet->setCellValue('A5', 'DNI:');
        $sheet->setCellValue('B5', $dni);
        $sheet->setCellValue('A6', 'Email:');
        $sheet->setCellValue('B6', $socio['email'] ?? '');
        $sheet->setCellValue('A7', 'Telefono:');
        $sheet->setCellValue('B7', $socio['telefono'] ?? '');
        $sheet->setCellValue('A8', 'Tipo:');
        $sheet->setCellValue('B8', $socio['nombretipo'] ?? '');
        $sheet->setCellValue('A9', 'Plan:');
        $sheet->setCellValue('B9', $socio['nombreplan'] ?? '');
        $sheet->setCellValue('A10', 'Vencimiento:');
        $sheet->setCellValue('B10', isset($socio['fechavencimiento']) ? date('d/m/Y', strtotime($socio['fechavencimiento'])) : '');

        // Historial de pagos - encabezado
        $startRow = 12;
        $sheet->setCellValue('A' . $startRow, 'Fecha');
        $sheet->setCellValue('B' . $startRow, 'Monto');
        $sheet->setCellValue('C' . $startRow, 'Concepto');
        $sheet->setCellValue('D' . $startRow, 'Metodo');
        $sheet->setCellValue('E' . $startRow, 'Estado');
        $this->aplicarEstiloEncabezado($sheet, 'A' . $startRow . ':E' . $startRow);

        $fila = $startRow + 1;
        foreach ($pagos as $p) {
            $sheet->setCellValue('A' . $fila, isset($p['fechapago']) ? date('d/m/Y', strtotime($p['fechapago'])) : '');
            $sheet->setCellValue('B' . $fila, isset($p['monto']) ? number_format($p['monto'], 2) : '');
            $sheet->setCellValue('C' . $fila, $p['concepto'] ?? '');
            $sheet->setCellValue('D' . $fila, $p['nombremetodo'] ?? '');
            $sheet->setCellValue('E' . $fila, $p['estado'] ?? '');
            $fila++;
        }

        // Cursos inscritos - encabezado
        $fila += 1; // espacio
        $startCursos = $fila;
        $sheet->setCellValue('A' . $startCursos, 'Curso');
        $sheet->setCellValue('B' . $startCursos, 'Fecha Inscripcion');
        $sheet->setCellValue('C' . $startCursos, 'Estado Pago');
        $sheet->setCellValue('D' . $startCursos, 'Ponente');
        $this->aplicarEstiloEncabezado($sheet, 'A' . $startCursos . ':D' . $startCursos);

        $fila = $startCursos + 1;
        foreach ($cursos as $c) {
            $nombreCurso = $c['nombrecurso'] ?? ($c['nombre'] ?? '');
            $sheet->setCellValue('A' . $fila, $nombreCurso);
            $sheet->setCellValue('B' . $fila, isset($c['fechainscripcion']) ? date('d/m/Y H:i', strtotime($c['fechainscripcion'])) : '');
            $sheet->setCellValue('C' . $fila, $c['estadopagocurso'] ?? '');
            $sheet->setCellValue('D' . $fila, $c['nombre_ponente'] ?? '');
            $fila++;
        }

        // Ajustar columnas
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Descargar
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_detalle_socio_' . ($dni ?: date('Ymd')) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
