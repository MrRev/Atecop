<?php
/**
 * Clase ApiPeruDev - Cliente para API de Perú Devs
 * 
 * Permite consultar datos de DNI y RUC desde la API externa.
 */

class ApiPeruDev {
    private $apiKey;
    private $baseUrl;
    
    /**
     * Constructor
     * 
     * @param string $apiKey Clave de API
     */
    public function __construct($apiKey = null) {
        if ($apiKey === null && defined('API_PERUDEV_TOKEN')) {
            $apiKey = API_PERUDEV_TOKEN;
        }
        $this->apiKey = $apiKey;
        $this->baseUrl = API_PERUDEV_URL;
    }
    
    /**
     * Consulta datos por DNI
     * 
     * @param string $dni Número de DNI (8 dígitos)
     * @return array|null Datos del ciudadano o null si hay error
     */
    public function consultarDNI($dni) {
        if (!$this->validarDNI($dni)) {
            return ['success' => false, 'message' => 'DNI inválido. Debe tener 8 dígitos.'];
        }

        // La API espera POST JSON a /dni con { "dni": "..." }
        return $this->realizarConsulta('/dni', ['dni' => $dni]);
    }
    
    /**
     * Consulta datos por RUC
     * 
     * @param string $ruc Número de RUC (11 dígitos)
     * @return array|null Datos de la empresa o null si hay error
     */
    public function consultarRUC($ruc) {
        if (!$this->validarRUC($ruc)) {
            return ['success' => false, 'message' => 'RUC inválido. Debe tener 11 dígitos.'];
        }

        // La API espera POST JSON a /ruc con { "ruc": "..." }
        return $this->realizarConsulta('/ruc', ['ruc' => $ruc]);
    }

    /**
     * Consulta datos por DNI o RUC según la longitud
     * @param string $documento DNI (8) o RUC (11)
     * @return array Respuesta de la API
     */
    public function consultarDocumento($documento) {
        $documento = trim($documento);
        if ($this->validarDNI($documento)) {
            return $this->consultarDNI($documento);
        } elseif ($this->validarRUC($documento)) {
            return $this->consultarRUC($documento);
        } else {
            return ['success' => false, 'message' => 'El documento debe ser DNI (8 dígitos) o RUC (11 dígitos).'];
        }
    }
    
    /**
     * Realiza la consulta HTTP a la API
     * 
     * @param string $url URL completa del endpoint
     * @return array Respuesta de la API
     */
    /**
     * Realiza la consulta HTTP a la API
     *
     * @param string $endpoint Ruta del endpoint (p. ej. '/dni' o '/ruc')
     * @param array|null $payload Datos a enviar en el body (si es POST)
     * @return array Respuesta con keys: success (bool), data|message
     */
    private function realizarConsulta($endpoint, $payload = null) {
        try {
            $url = rtrim($this->baseUrl, '/') . $endpoint;
            $ch = curl_init();

            $headers = [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ];

            $options = [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_HTTPHEADER => $headers,
            ];

            if ($payload !== null) {
                $json = json_encode($payload);
                $options[CURLOPT_CUSTOMREQUEST] = 'POST';
                $options[CURLOPT_POSTFIELDS] = $json;
            }

            curl_setopt_array($ch, $options);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            curl_close($ch);

            if ($error) {
                return ['success' => false, 'message' => 'Error de conexión: ' . $error, 'response' => $response];
            }

            $data = json_decode($response, true);

            if ($httpCode !== 200) {
                $msg = 'Error en la API (Código: ' . $httpCode . ')';
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $msg .= '. Respuesta: ' . substr($response, 0, 200);
                } elseif (isset($data['message'])) {
                    $msg .= ' - ' . $data['message'];
                }
                return ['success' => false, 'message' => $msg, 'response' => $response];
            }

            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['success' => false, 'message' => 'Respuesta no JSON de la API. Recibido: ' . substr($response, 0, 200), 'response' => $response];
            }

            // La API devuelve { success: true, data: { ... } }
            if (is_array($data) && array_key_exists('success', $data)) {
                if ($data['success']) {
                    return ['success' => true, 'data' => $data['data'] ?? $data];
                }
                return ['success' => false, 'message' => $data['message'] ?? 'Sin resultado', 'response' => $response];
            }

            // Si la estructura es diferente, retornarla como data
            return ['success' => true, 'data' => $data];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Excepción: ' . $e->getMessage()];
        }
    }
    
    /**
     * Valida formato de DNI
     * 
     * @param string $dni DNI a validar
     * @return bool True si es válido
     */
    private function validarDNI($dni) {
        return preg_match('/^\d{8}$/', $dni);
    }
    
    /**
     * Valida formato de RUC
     * 
     * @param string $ruc RUC a validar
     * @return bool True si es válido
     */
    private function validarRUC($ruc) {
        return preg_match('/^\d{11}$/', $ruc);
    }
    
    /**
     * Establece la API Key
     * 
     * @param string $apiKey Nueva API Key
     */
    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
    }
}
