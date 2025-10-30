<?php

require_once __DIR__ . '/../dao/PagoDAO.php';
require_once __DIR__ . '/../dao/MetodoPagoDAO.php';
require_once __DIR__ . '/../../socios/dao/SocioDAO.php';
require_once __DIR__ . '/../../membresias/dao/PlanMembresiaDAO.php';

/**
 * Clase ControladorPago
 * Controlador para gestionar los pagos de membresías
 */
class ControladorPago {
    private $pagoDAO;
    private $metodoPagoDAO;
    private $socioDAO;
    private $planDAO;

    public function __construct() {
        $this->pagoDAO = new PagoDAO();
        $this->metodoPagoDAO = new MetodoPagoDAO();
        $this->socioDAO = new SocioDAO();
        $this->planDAO = new PlanMembresiaDAO();
    }

    /**
     * Mostrar formulario de registro de pago
     */
    public function mostrarFormulario() {
        $idsocio = $_GET['idsocio'] ?? null;
        $busqueda = $_GET['busqueda'] ?? '';
        $metodosPago = $this->metodoPagoDAO->listAll();
        $socio = null;
        $socios = [];
        
        require_once __DIR__ . '/../../../util_global/Database.php';
        $db = Database::getInstance()->getConnection();
        
        if ($idsocio) {
            // Si hay un socio seleccionado, obtener sus datos
            $sql = "SELECT s.*, 
                           ts.nombretipo as tipo_socio,
                           DATEDIFF(CURRENT_DATE, s.fechavencimiento) as dias_vencido,
                           p.nombreplan as nombre_plan
                    FROM socio s
                    LEFT JOIN tiposocio ts ON s.idtiposocio = ts.idtiposocio
                    LEFT JOIN planmembresia p ON s.idplan = p.idplan
                    WHERE s.idsocio = :idsocio";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':idsocio', $idsocio, PDO::PARAM_INT);
            $stmt->execute();
            $socio = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$socio) {
                $_SESSION['error'] = "Socio no encontrado";
                header("Location: index.php?modulo=pagos&accion=listar");
                exit;
            }
        } else {
            // Si no hay socio seleccionado, mostrar lista de búsqueda
            $sql = "SELECT s.*, 
                           ts.nombretipo as tipo_socio,
                           DATEDIFF(CURRENT_DATE, s.fechavencimiento) as dias_vencido
                    FROM socio s
                    LEFT JOIN tiposocio ts ON s.idtiposocio = ts.idtiposocio
                    WHERE s.estado IN ('Activo', 'Moroso')";

            if (!empty($busqueda)) {
                $sql .= " AND (s.dni LIKE :busqueda 
                              OR s.nombrecompleto LIKE :busqueda)";
            }
            
            $sql .= " ORDER BY dias_vencido DESC";
            
            $stmt = $db->prepare($sql);
            
            if (!empty($busqueda)) {
                $busquedaParam = "%{$busqueda}%";
                $stmt->bindParam(':busqueda', $busquedaParam, PDO::PARAM_STR);
            }
            
            $stmt->execute();
            $socios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        require_once __DIR__ . '/../vistas/VistaRegistrarPago.php';
    }

    /**
     * Registrar un nuevo pago
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?modulo=pagos&accion=listar");
            exit;
        }

        try {
            // Validar datos requeridos
            $idsocio = filter_input(INPUT_POST, 'idsocio', FILTER_VALIDATE_INT);
            $monto = filter_input(INPUT_POST, 'monto', FILTER_VALIDATE_FLOAT);
            $fechapago = $_POST['fechapago'] ?? date('Y-m-d');
            $concepto = trim($_POST['concepto'] ?? '');
            $idmetodopago = filter_input(INPUT_POST, 'idmetodopago', FILTER_VALIDATE_INT);

            if (!$idsocio || !$monto || !$idmetodopago) {
                throw new Exception("Datos incompletos o inválidos");
            }

            $urlcomprobante = null;
            if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
                $comprobante = $_FILES['comprobante'];
                $extension = strtolower(pathinfo($comprobante['name'], PATHINFO_EXTENSION));
                
                if (!in_array($extension, ['pdf', 'jpg', 'jpeg', 'png'])) {
                    throw new Exception("Formato de archivo no permitido");
                }
                
                if ($comprobante['size'] > 2 * 1024 * 1024) { // 2MB
                    throw new Exception("El archivo es demasiado grande");
                }
                
                $urlcomprobante = uniqid() . '.' . $extension;
                $rutaDestino = __DIR__ . '/../../../public/uploads/comprobantes/' . $urlcomprobante;
                
                if (!move_uploaded_file($comprobante['tmp_name'], $rutaDestino)) {
                    throw new Exception("Error al guardar el comprobante");
                }
            }

            // Registrar el pago
            $datos = [
                'idsocio' => $idsocio,
                'monto' => $monto,
                'fechapago' => $fechapago,
                'concepto' => $concepto,
                'idmetodopago' => $idmetodopago,
                'urlcomprobante' => $urlcomprobante,
                'estado' => 'Registrado'
            ];

            if ($this->pagoDAO->create($datos)) {
                // Actualizar fecha de vencimiento del socio
                $socio = $this->socioDAO->read($idsocio);
                $plan = $this->planDAO->read($socio['idplan']);
                
                $fechaVencimiento = new DateTime($socio['fechavencimiento']);
                if ($fechaVencimiento < new DateTime()) {
                    $fechaVencimiento = new DateTime();
                }
                $fechaVencimiento->modify('+' . $plan['duracionmeses'] . ' months');
                
                $this->socioDAO->updateVencimiento($idsocio, $fechaVencimiento->format('Y-m-d'), 'Activo');
                
                $_SESSION['success'] = "Pago registrado correctamente";
            } else {
                throw new Exception("Error al registrar el pago");
            }
            
            header("Location: index.php?modulo=pagos&accion=listar");
            
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: index.php?modulo=pagos&accion=registrar" . ($idsocio ? "&idsocio=$idsocio" : ""));
        }
        exit;
    }

    public function listar() {
        $pagos = $this->pagoDAO->listAll();
        require_once __DIR__ . '/../vistas/VistaListarPagos.php';
    }
}
