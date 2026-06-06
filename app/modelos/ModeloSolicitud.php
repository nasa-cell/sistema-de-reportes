<?php
/**
 * Modelo para la tabla solicitudes_proyecto
 */

class ModeloSolicitud extends Modelo {
    protected $tabla = 'solicitudes_proyecto';

    /**
     * Crear una solicitud de proyecto
     */
    public function crear($datos) {
        $sql = "INSERT INTO " . $this->tabla . " (id_cliente, titulo, descripcion, tipo_sistema, urgencia, estado) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $parametros = [
            $datos['id_cliente'],
            Seguridad::sanitizar($datos['titulo']),
            Seguridad::sanitizar($datos['descripcion']),
            Seguridad::sanitizar($datos['tipo_sistema']),
            $datos['urgencia'] ?? URGENCIA_MEDIA,
            ESTADO_SOLICITUD_PENDIENTE
        ];

        $this->ejecutarSQL($sql, $parametros);
        return $this->obtenerUltimoId();
    }

    /**
     * Obtener solicitudes de un cliente
     */
    public function obtenerDelCliente($id_cliente) {
        $sql = "SELECT * FROM " . $this->tabla . " WHERE id_cliente = ? ORDER BY fecha_solicitud DESC";
        return $this->obtenerResultados($sql, [$id_cliente]);
    }

    /**
     * Eliminar todas las solicitudes de un cliente
     */
    public function eliminarPorCliente($id_cliente) {
        $sql = "DELETE FROM " . $this->tabla . " WHERE id_cliente = ?";
        return $this->ejecutarSQL($sql, [$id_cliente]);
    }

    /**
     * Obtener todas las solicitudes
     */
    public function obtenerTodas() {
        $sql = "SELECT s.*, c.id_usuario, u.nombres, u.apellidos, u.correo,
                (SELECT p.id_proyecto FROM proyectos p WHERE p.id_solicitud = s.id_solicitud LIMIT 1) AS id_proyecto
                FROM " . $this->tabla . " s
                INNER JOIN clientes c ON s.id_cliente = c.id_cliente
                INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
                ORDER BY s.fecha_solicitud DESC";
        return $this->obtenerResultados($sql);
    }

    /**
     * Obtener solicitud por ID con datos del cliente
     */
    public function obtenerConDatos($id) {
        $sql = "SELECT s.*, c.id_usuario, u.nombres, u.apellidos, u.correo, u.usuario,
                (SELECT p.id_proyecto FROM proyectos p WHERE p.id_solicitud = s.id_solicitud LIMIT 1) AS id_proyecto
                FROM " . $this->tabla . " s
                INNER JOIN clientes c ON s.id_cliente = c.id_cliente
                INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
                WHERE s.id_solicitud = ?";
        return $this->obtenerUnResultado($sql, [$id]);
    }

    /**
     * Cotizar una solicitud
     */
    public function cotizar($id, $precio) {
        $sql = "UPDATE " . $this->tabla . " SET estado = ?, precio_propuesto = ? WHERE id_solicitud = ?";
        return $this->ejecutarSQL($sql, [ESTADO_SOLICITUD_COTIZADO, $precio, $id]);
    }

    /**
     * Aceptar una solicitud
     */
    public function aceptar($id) {
        $sql = "UPDATE " . $this->tabla . " SET estado = ? WHERE id_solicitud = ?";
        return $this->ejecutarSQL($sql, [ESTADO_SOLICITUD_ACEPTADO, $id]);
    }

    /**
     * Rechazar una solicitud
     */
    public function rechazar($id, $motivo) {
        $sql = "UPDATE " . $this->tabla . " SET estado = ?, motivo_rechazo = ? WHERE id_solicitud = ?";
        return $this->ejecutarSQL($sql, [ESTADO_SOLICITUD_RECHAZADO, Seguridad::sanitizar($motivo), $id]);
    }

    /**
     * Obtener solicitudes pendientes de cotización
     */
    public function obtenerPendientesCotizacion() {
        $sql = "SELECT s.*, c.id_usuario, u.nombres, u.apellidos
                FROM " . $this->tabla . " s
                INNER JOIN clientes c ON s.id_cliente = c.id_cliente
                INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
                WHERE s.estado = ?
                ORDER BY s.fecha_solicitud DESC";
        return $this->obtenerResultados($sql, [ESTADO_SOLICITUD_PENDIENTE]);
    }

    /**
     * Obtener solicitudes cotizadas pendiente de respuesta del cliente
     */
    public function obtenerCotizadas() {
        $sql = "SELECT s.*, c.id_usuario, u.nombres, u.apellidos
                FROM " . $this->tabla . " s
                INNER JOIN clientes c ON s.id_cliente = c.id_cliente
                INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
                WHERE s.estado = ?
                ORDER BY s.fecha_solicitud DESC";
        return $this->obtenerResultados($sql, [ESTADO_SOLICITUD_COTIZADO]);
    }
}
