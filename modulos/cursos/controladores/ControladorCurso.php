<?php

/**
 * Clase ControladorCurso
 * Controlador para gestionar cursos e inscripciones
 */
class ControladorCurso {
    private $cursoDAO;
    private $cursoInscritoDAO;
    private $ponenteDAO;
    private $socioDAO;

    public function __construct() {
        $this->cursoDAO = new CursoDAO();
        $this->cursoInscritoDAO = new CursoInscritoDAO();
        $this->ponenteDAO = new PonenteDAO();
        $this->socioDAO = new SocioDAO();
    }

    /**
     * Listar todos los cursos
     */
    public function listar() {
        $filtros = [
            'estado' => $_GET['estado'] ?? '',
            'busqueda' => $_GET['busqueda'] ?? ''
        ];
        
        $cursos = $this->cursoDAO->listarCursos($filtros);
        
        require_once __DIR__ . '/../vistas/VistaListarCursos.php';
    }

    /**
     * Mostrar formulario de registro/edición
     */
    public function mostrarFormulario($id = null) { // <-- ¡NOMBRE CAMBIADO Y AÑADIDO $id!
        $idcurso = $id ?? $_GET['id'] ?? null; // <-- Lógica mejorada para aceptar el $id
        $curso = null;
        
        if ($idcurso) {
            $curso = $this->cursoDAO->readCurso($idcurso);
            if (!$curso) {
                $_SESSION['error'] = "Curso no encontrado";
                header("Location: index.php?modulo=cursos&accion=listar");
                exit;
            }
        }
        
        $ponentes = $this->ponenteDAO->getListaPonentesActivos();
        
        require_once __DIR__ . '/../vistas/VistaFormCurso.php';
    }

    /**
     * Guardar curso (crear o actualizar)
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?modulo=cursos&accion=listar");
            exit;
        }

        try {
            // Validar datos
            $idcurso = filter_input(INPUT_POST, 'idcurso', FILTER_VALIDATE_INT);
            $nombrecurso = trim($_POST['nombrecurso'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $cupostotales = filter_input(INPUT_POST, 'cupostotales', FILTER_VALIDATE_INT);
            $costoinscripcion = filter_input(INPUT_POST, 'costoinscripcion', FILTER_VALIDATE_FLOAT);
            $fechainicio = $_POST['fechainicio'] ?? null;
            $fechafin = $_POST['fechafin'] ?? null;
            $urlenlacevirtual = trim($_POST['urlenlacevirtual'] ?? '');
            $estado = $_POST['estado'] ?? 'Programado';
            $idponente = filter_input(INPUT_POST, 'idponente', FILTER_VALIDATE_INT);

            if (empty($nombrecurso) || !$cupostotales) {
                throw new Exception("Nombre del curso y cupos son obligatorios");
            }

            // Crear objeto Curso
            $curso = new Curso();
            if ($idcurso) {
                $curso->setIdcurso($idcurso);
            }
            $curso->setNombrecurso($nombrecurso);
            $curso->setDescripcion($descripcion);
            $curso->setCupostotales($cupostotales);
            $curso->setCostoinscripcion($costoinscripcion);
            $curso->setFechainicio($fechainicio);
            $curso->setFechafin($fechafin);
            $curso->setUrlenlacevirtual($urlenlacevirtual);
            $curso->setEstado($estado);
            $curso->setIdponente($idponente);

            // Guardar
            if ($idcurso) {
                $resultado = $this->cursoDAO->updateCurso($curso);
                $mensaje = "Curso actualizado exitosamente";
            } else {
                $resultado = $this->cursoDAO->createCurso($curso);
                $mensaje = "Curso registrado exitosamente";
            }

            if ($resultado) {
                $_SESSION['mensaje'] = $mensaje;
                header("Location: index.php?modulo=cursos&accion=listar");
            } else {
                throw new Exception("Error al guardar el curso");
            }

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: index.php?modulo=cursos&accion=formulario" . ($idcurso ? "&id=$idcurso" : ""));
        }
        exit;
    }

    /**
     * Gestionar inscripciones de un curso
     */
    public function gestionarInscripciones() {
        $idcurso = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$idcurso) {
            $_SESSION['error'] = "Debe especificar un curso";
            header("Location: index.php?modulo=cursos&accion=listar");
            exit;
        }

        $curso = $this->cursoDAO->readCurso($idcurso);
        $inscritos = $this->cursoInscritoDAO->getInscritosPorCurso($idcurso);
        $cuposDisponibles = $this->cursoDAO->getCuposDisponibles($idcurso);

        $ponente = null;
        if ($curso && $curso->getIdponente()) {
            $ponente = $this->ponenteDAO->readPonente($curso->getIdponente());
        }

        $busqueda = $_GET['busqueda'] ?? '';
        $socios = [];
        if (!empty($busqueda)) {
            $socios = $this->socioDAO->findSocios($busqueda);
        }

        require_once __DIR__ . '/../vistas/VistaGestionInscripciones.php';
    }

    /**
     * Inscribir un socio a un curso
     */
    public function inscribir() {
        try {
            // Leemos desde $_GET en lugar de $_POST
            $idsocio = filter_input(INPUT_GET, 'idsocio', FILTER_VALIDATE_INT);
            $idcurso = filter_input(INPUT_GET, 'idcurso', FILTER_VALIDATE_INT);

            if (!$idsocio || !$idcurso) {
                throw new Exception("Datos incompletos");
            }

            // Verificar cupos disponibles
            $cuposDisponibles = $this->cursoDAO->getCuposDisponibles($idcurso);
            if ($cuposDisponibles <= 0) {
                throw new Exception("No hay cupos disponibles");
            }

            // Verificar si ya está inscrito
            if ($this->cursoInscritoDAO->checkInscripcion($idsocio, $idcurso)) {
                throw new Exception("El socio ya está inscrito en este curso");
            }

            // Crear inscripción
            $inscripcion = new CursoInscrito();
            $inscripcion->setIdsocio($idsocio);
            $inscripcion->setIdcurso($idcurso);
            $inscripcion->setFechainscripcion(date('Y-m-d H:i:s'));
            $inscripcion->setEstadopagocurso('Pendiente');

            if ($this->cursoInscritoDAO->createInscripcion($inscripcion)) {
                $_SESSION['mensaje'] = "Socio inscrito exitosamente";
            } else {
                throw new Exception("Error al inscribir al socio");
            }

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header("Location: index.php?modulo=cursos&accion=gestionarInscripciones&id=" . $idcurso);
        exit;
    }

    /**
     * Eliminar inscripción
     */
    public function eliminarInscripcion() {
        $idsocio = filter_input(INPUT_GET, 'idsocio', FILTER_VALIDATE_INT);
        $idcurso = filter_input(INPUT_GET, 'idcurso', FILTER_VALIDATE_INT);

        if (!$idsocio || !$idcurso) {
            $_SESSION['error'] = "Datos incompletos";
            header("Location: index.php?modulo=cursos&accion=listar");
            exit;
        }

        try {
            if ($this->cursoInscritoDAO->deleteInscripcion($idsocio, $idcurso)) {
                $_SESSION['mensaje'] = "Inscripción eliminada exitosamente";
            } else {
                throw new Exception("Error al eliminar la inscripción");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header("Location: index.php?modulo=cursos&accion=gestionarInscripciones&id=" . $idcurso);
        exit;
    }

    /**
     * Exportar la lista de inscritos a XLSX (Microsoft Excel)
     */
    public function exportarInscritos($idcurso = null) {
        if ($idcurso === null) {
            $_SESSION['error'] = "ID de curso no válido";
            header("Location: index.php?modulo=cursos&accion=listar");
            exit;
        }

        try {
            // 1. Obtener datos
            $curso = $this->cursoDAO->readCurso($idcurso);
            $inscritos = $this->cursoInscritoDAO->getInscritosPorCurso($idcurso);
            if (!$curso) {
                throw new Exception("Curso no encontrado");
            }

            // 2. Preparar nombre del archivo (ej: Inscritos_Gestion_de_Proyectos.xlsx)
            $nombreArchivo = 'Inscritos_' . preg_replace('/[^a-z0-9_]+/', '_', strtolower($curso->getNombrecurso())) . '.xlsx';

            // 3. Cargar autoload de Composer para PhpSpreadsheet
            $autoload = __DIR__ . '/../../../vendor/autoload.php';
            if (!file_exists($autoload)) {
                throw new Exception("Autoload de Composer no encontrado. Ejecute 'composer install'.");
            }
            require_once $autoload;

            // 4. Crear hoja de cálculo con PhpSpreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle(substr($curso->getNombrecurso(), 0, 31));

            // Estilos (copiados del módulo reportes)
            $colorPrimario = '002E5D'; // Azul Marino ATECOP
            $colorSecundario = '3BAFDA'; // Celeste ATECOP

            // Título similar al módulo reportes
            $sheet->setCellValue('A1', 'LISTA DE INSCRITOS - ' . strtoupper($curso->getNombrecurso()));
            $sheet->mergeCells('A1:F1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Fecha de generación
            $sheet->setCellValue('A2', 'Generado el: ' . date('d/m/Y H:i'));
            $sheet->mergeCells('A2:F2');

            // Encabezados en la fila 4 con estilo del reporte
            $headers = ['DNI', 'Nombre Completo', 'Email', 'Telefono', 'Fecha Inscripcion', 'Estado Pago'];
            $col = 'A';
            foreach ($headers as $h) {
                $sheet->setCellValue($col . '4', $h);
                $col++;
            }

            // Aplicar estilo de encabezado (fuente blanca, fondo colorPrimario, centrado, borde fino)
            $sheet->getStyle('A4:F4')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $colorPrimario]
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    ]
                ]
            ]);

            // 6. Datos
            $row = 5;
            if (count($inscritos) > 0) {
                foreach ($inscritos as $inscrito) {
                    $sheet->setCellValue('A' . $row, $inscrito['dni']);
                    $sheet->setCellValue('B' . $row, $inscrito['nombrecompleto']);
                    $sheet->setCellValue('C' . $row, $inscrito['email']);
                    $sheet->setCellValue('D' . $row, $inscrito['telefono']);
                    $sheet->setCellValue('E' . $row, date('d/m/Y H:i', strtotime($inscrito['fechainscripcion'])));
                    $sheet->setCellValue('F' . $row, $inscrito['estadopagocurso']);
                    // Borde por fila (ligeramente más claro que el header)
                    $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $row++;
                }
            }

            // 7. Auto-ajustar ancho de columnas (simple)
            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Total similar al módulo reportes
            $sheet->setCellValue('A' . ($row + 1), 'Total de inscritos: ' . (count($inscritos)));
            $sheet->mergeCells('A' . ($row + 1) . ':F' . ($row + 1));
            $sheet->getStyle('A' . ($row + 1))->getFont()->setBold(true);

            // 8. Enviar al navegador como XLSX
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
            header('Cache-Control: max-age=0');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;

        } catch (Exception $e) {
            $_SESSION['error'] = "Error al exportar: " . $e->getMessage();
            header("Location: index.php?modulo=cursos&accion=gestionarInscripciones&id=" . $idcurso);
            exit;
        }
    }
}