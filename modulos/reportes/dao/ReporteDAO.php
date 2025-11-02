<?php

/**
 * Clase ReporteDAO
 * Maneja consultas especializadas para la generación de reportes del sistema
 */
class ReporteDAO {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * RF-010: Obtiene lista de socios morosos (vencimiento pasado y estado Moroso)
     */
    public function getSociosMorosos() {
        $sql = "SELECT s.idsocio, s.dni, s.nombrecompleto, s.email, s.telefono, 
                       s.fechavencimiento, ts.nombretipo, p.nombreplan
                FROM socio s
                INNER JOIN tiposocio ts ON s.idtiposocio = ts.idtiposocio
                INNER JOIN planmembresia p ON s.idplan = p.idplan
                WHERE s.estado = 'Moroso' AND s.fechavencimiento < CURDATE()
                ORDER BY s.fechavencimiento ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * RF-011: Obtiene socios con próximos vencimientos (próximos 30 días)
     */
    public function getProximosVencimientos($dias = 30) {
        $sql = "SELECT s.idsocio, s.dni, s.nombrecompleto, s.email, s.telefono, 
                       s.fechavencimiento, ts.nombretipo, p.nombreplan,
                       DATEDIFF(s.fechavencimiento, CURDATE()) as dias_restantes
                FROM socio s
                INNER JOIN tiposocio ts ON s.idtiposocio = ts.idtiposocio
                INNER JOIN planmembresia p ON s.idplan = p.idplan
                WHERE s.estado = 'Activo' 
                  AND s.fechavencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :dias DAY)
                ORDER BY s.fechavencimiento ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':dias', $dias, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * RF-012: Obtiene datos completos de un socio para reporte detallado
     */
    public function getDatosCompletosSocio($idsocio) {
        // Datos básicos del socio
        $sqlSocio = "SELECT s.*, ts.nombretipo, p.nombreplan, p.costo as costo_plan,
                            prof.nombreprofesion
                     FROM socio s
                     INNER JOIN tiposocio ts ON s.idtiposocio = ts.idtiposocio
                     INNER JOIN planmembresia p ON s.idplan = p.idplan
                     LEFT JOIN profesion prof ON s.idprofesion = prof.idprofesion
                     WHERE s.idsocio = :idsocio";
        
        $stmt = $this->db->prepare($sqlSocio);
        $stmt->bindParam(':idsocio', $idsocio, PDO::PARAM_INT);
        $stmt->execute();
        $socio = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$socio) {
            return null;
        }

        // Historial de pagos
        $sqlPagos = "SELECT pa.*, mp.nombremetodo
                     FROM pago pa
                     INNER JOIN metodopago mp ON pa.idmetodopago = mp.idmetodopago
                     WHERE pa.idsocio = :idsocio
                     ORDER BY pa.fechapago DESC";
        
        $stmt = $this->db->prepare($sqlPagos);
        $stmt->bindParam(':idsocio', $idsocio, PDO::PARAM_INT);
        $stmt->execute();
        $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Cursos inscritos
        $sqlCursos = "SELECT c.*, ci.fechainscripcion, ci.estadopagocurso,
                             po.nombrecompleto as nombre_ponente
                      FROM cursoinscrito ci
                      INNER JOIN curso c ON ci.idcurso = c.idcurso
                      LEFT JOIN ponente po ON c.idponente = po.idponente
                      WHERE ci.idsocio = :idsocio
                      ORDER BY c.fechainicio DESC";
        
        $stmt = $this->db->prepare($sqlCursos);
        $stmt->bindParam(':idsocio', $idsocio, PDO::PARAM_INT);
        $stmt->execute();
        $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'socio' => $socio,
            'pagos' => $pagos,
            'cursos' => $cursos
        ];
    }

    /**
     * Obtiene datos completos de un socio por DNI para reporte detallado
     */
    public function getDatosCompletosSocioPorDni($dni) {
        // Buscar el idsocio correspondiente al DNI
        $sqlId = "SELECT idsocio FROM socio WHERE dni = :dni LIMIT 1";
        $stmt = $this->db->prepare($sqlId);
        $stmt->bindParam(':dni', $dni);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }
        $idsocio = $result['idsocio'];

        // Reusar la función existente para devolver la estructura completa
        return $this->getDatosCompletosSocio($idsocio);
    }

    /**
     * Obtiene socios para inhabilitar (morosos por más de X días)
     */
    public function getSociosParaInhabilitar($diasMora = 60) {
        $sql = "SELECT s.idsocio, s.dni, s.nombrecompleto, s.email, s.telefono, 
                       s.fechavencimiento, ts.nombretipo,
                       DATEDIFF(CURDATE(), s.fechavencimiento) as dias_mora
                FROM socio s
                INNER JOIN tiposocio ts ON s.idtiposocio = ts.idtiposocio
                WHERE s.estado IN ('Moroso', 'Activo') 
                  AND s.fechavencimiento < DATE_SUB(CURDATE(), INTERVAL :dias DAY)
                ORDER BY s.fechavencimiento ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':dias', $diasMora, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene estadísticas generales del sistema
     */
    public function getEstadisticasGenerales() {
        $stats = [];

        // Total de socios por estado
        $sql = "SELECT estado, COUNT(*) as total FROM socio GROUP BY estado";
        $stmt = $this->db->query($sql);
        $stats['socios_por_estado'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Total de pagos por mes (último año)
        $sql = "SELECT DATE_FORMAT(fechapago, '%Y-%m') as mes, 
                       COUNT(*) as cantidad, SUM(monto) as total
                FROM pago
                WHERE fechapago >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                  AND estado = 'Registrado'
                GROUP BY DATE_FORMAT(fechapago, '%Y-%m')
                ORDER BY mes DESC";
        $stmt = $this->db->query($sql);
        $stats['pagos_mensuales'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Cursos activos
        $sql = "SELECT COUNT(*) as total FROM curso WHERE estado IN ('Programado', 'En Curso')";
        $stmt = $this->db->query($sql);
        $stats['cursos_activos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return $stats;
    }
}
