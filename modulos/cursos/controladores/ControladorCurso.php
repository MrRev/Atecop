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
    public function formulario() {
        $idcurso = $_GET['id'] ?? null;
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
        
        // Para búsqueda de socios
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?modulo=cursos&accion=listar");
            exit;
        }

        try {
            $idsocio = filter_input(INPUT_POST, 'idsocio', FILTER_VALIDATE_INT);
            $idcurso = filter_input(INPUT_POST, 'idcurso', FILTER_VALIDATE_INT);

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
}
