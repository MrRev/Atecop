<?php

require_once __DIR__ . '/../../socios/dao/ProfesionDAO.php';
require_once __DIR__ . '/../dao/PonenteDAO.php';
require_once __DIR__ . '/../../../util_global/ApiPeruDev.php';

/**
 * Clase ControladorPonente
 * Controlador para gestionar ponentes (tutores de cursos)
 */
class ControladorPonente {
    private $ponenteDAO;
    private $profesionDAO;
    private $apiPeruDev;

    public function __construct() {
        $this->ponenteDAO = new PonenteDAO();
        $this->profesionDAO = new ProfesionDAO();
        $this->apiPeruDev = new ApiPeruDev();
    }

    /**
     * Listar todos los ponentes
     */
    public function listar() {
        $filtros = [
            'estado' => $_GET['estado'] ?? '',
            'busqueda' => $_GET['busqueda'] ?? ''
        ];
        
        $ponentes = $this->ponenteDAO->listarPonentes($filtros);
        
        require_once __DIR__ . '/../vistas/VistaListarPonentes.php';
    }

    /**
     * Mostrar formulario de registro/edición
     */
    public function mostrarFormulario($id = null) {
        $idponente = $id ?? $_GET['id'] ?? null;
        $ponente = null;
        
        if ($idponente) {
            $ponente = $this->ponenteDAO->readPonente($idponente);
            if (!$ponente) {
                $_SESSION['error'] = "Ponente no encontrado";
                header("Location: index.php?modulo=ponentes&accion=listar");
                exit;
            }
        }
        
        $profesiones = $this->profesionDAO->listAll();
        
        require_once __DIR__ . '/../vistas/VistaFormPonente.php';
    }

    /**
     * Validar DNI con API externa
     */
    public function validarDni() {
        header('Content-Type: application/json');

        // Aceptar GET o POST
        $dni = $_GET['dni'] ?? $_POST['dni'] ?? '';

        if (empty($dni) || strlen($dni) != 8) {
            echo json_encode(['success' => false, 'message' => 'DNI inválido']);
            exit;
        }

        try {
            $datos = $this->apiPeruDev->consultarDNI($dni);

            if (isset($datos['success']) && $datos['success']) {
                $d = $datos['data'] ?? [];
                $nombre = $d['nombre_completo'] ?? $d['nombres'] ?? '';
                $direccion = $d['direccion'] ?? '';
                echo json_encode(['success' => true, 'nombre' => $nombre, 'direccion' => $direccion]);
            } else {
                $msg = $datos['message'] ?? $datos['mensaje'] ?? 'DNI no encontrado';
                echo json_encode(['success' => false, 'message' => $msg]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Guardar ponente (crear o actualizar)
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?modulo=ponentes&accion=listar");
            exit;
        }

        try {
            // Validar datos
            $idponente = filter_input(INPUT_POST, 'idponente', FILTER_VALIDATE_INT);
            $nombrecompleto = trim($_POST['nombrecompleto'] ?? '');
            $dni = trim($_POST['dni'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $idprofesion = filter_input(INPUT_POST, 'idprofesion', FILTER_VALIDATE_INT);
            $estado = $_POST['estado'] ?? 'Activo';

            if (empty($nombrecompleto) || empty($dni)) {
                throw new Exception("Nombre completo y DNI son obligatorios");
            }

            // Validar DNI único
            if ($this->ponenteDAO->existeDni($dni, $idponente)) {
                throw new Exception("El DNI ya está registrado para otro ponente");
            }

            // Crear objeto Ponente
            $ponente = new Ponente();
            if ($idponente) {
                $ponente->setIdponente($idponente);
            }
            $ponente->setNombrecompleto($nombrecompleto);
            $ponente->setDni($dni);
            $ponente->setEmail($email);
            $ponente->setTelefono($telefono);
            $ponente->setEstado($estado);
            $ponente->setIdprofesion($idprofesion);

            // Guardar
            if ($idponente) {
                $resultado = $this->ponenteDAO->updatePonente($ponente);
                $mensaje = "Ponente actualizado exitosamente";
            } else {
                $resultado = $this->ponenteDAO->createPonente($ponente);
                $mensaje = "Ponente registrado exitosamente";
            }

            if ($resultado) {
                $_SESSION['mensaje'] = $mensaje;
                header("Location: index.php?modulo=ponentes&accion=listar");
            } else {
                throw new Exception("Error al guardar el ponente");
            }

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: index.php?modulo=ponentes&accion=formulario" . ($idponente ? "&id=$idponente" : ""));
        }
        exit;
    }
}
