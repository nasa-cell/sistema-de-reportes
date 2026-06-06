<?php
/**
 * Modelo para la tabla proyectos
 */

class ModeloProyecto extends Modelo {
    protected $tabla = 'proyectos';

    /**
     * Crear un proyecto desde una solicitud aceptada
     */
    public function crear($datos) {
        $sql = "INSERT INTO " . $this->tabla . " (id_solicitud, titulo, descripcion, prioridad, precio, fecha_inicio, fecha_entrega, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $parametros = [
            $datos['id_solicitud'],
            Seguridad::sanitizar($datos['titulo']),
            Seguridad::sanitizar($datos['descripcion']),
            $datos['prioridad'] ?? PRIORIDAD_MEDIA,
            $datos['precio'] ?? 0,
            $datos['fecha_inicio'] ?? null,
            $datos['fecha_entrega'] ?? null,
            ESTADO_PROYECTO_EN_ESPERA
        ];

        $this->ejecutarSQL($sql, $parametros);
        return $this->obtenerUltimoId();
    }

    /**
     * Obtener todos los proyectos con datos
     */
    public function obtenerTodosConDatos() {
        $sql = "SELECT p.*, s.id_cliente, u.nombres as cliente_nombres, u.apellidos as cliente_apellidos
                FROM " . $this->tabla . " p
                INNER JOIN solicitudes_proyecto s ON p.id_solicitud = s.id_solicitud
                INNER JOIN clientes c ON s.id_cliente = c.id_cliente
                INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
                ORDER BY p.fecha_entrega ASC";
        return $this->obtenerResultados($sql);
    }

    /**
     * Obtener proyecto con datos completos
     */
    public function obtenerConDatos($id) {
        $sql = "SELECT p.*, s.id_cliente, u.nombres as cliente_nombres, u.apellidos as cliente_apellidos, u.correo as cliente_correo
                FROM " . $this->tabla . " p
                INNER JOIN solicitudes_proyecto s ON p.id_solicitud = s.id_solicitud
                INNER JOIN clientes c ON s.id_cliente = c.id_cliente
                INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
                WHERE p.id_proyecto = ?";
        return $this->obtenerUnResultado($sql, [$id]);
    }

    /**
     * Obtener proyectos de un cliente
     */
    public function obtenerDelCliente($id_cliente) {
        $sql = "SELECT p.* FROM " . $this->tabla . " p
                INNER JOIN solicitudes_proyecto s ON p.id_solicitud = s.id_solicitud
                WHERE s.id_cliente = ?
                ORDER BY p.fecha_entrega DESC";
        return $this->obtenerResultados($sql, [$id_cliente]);
    }

    /**
     * Eliminar todos los proyectos de un cliente
     */
    public function eliminarPorCliente($id_cliente) {
        $sql = "DELETE p FROM " . $this->tabla . " p
                INNER JOIN solicitudes_proyecto s ON p.id_solicitud = s.id_solicitud
                WHERE s.id_cliente = ?";
        return $this->ejecutarSQL($sql, [$id_cliente]);
    }

    /**
     * Obtener proyectos asignados a un empleado
     */
    public function obtenerDelEmpleado($id_empleado) {
        $sql = "SELECT DISTINCT p.*, s.id_cliente, u.nombres as cliente_nombres, u.apellidos as cliente_apellidos
                FROM " . $this->tabla . " p
                INNER JOIN solicitudes_proyecto s ON p.id_solicitud = s.id_solicitud
                INNER JOIN clientes c ON s.id_cliente = c.id_cliente
                INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
                INNER JOIN asignaciones_proyecto ap ON p.id_proyecto = ap.id_proyecto
                WHERE ap.id_empleado = ?
                ORDER BY p.fecha_entrega ASC";
        return $this->obtenerResultados($sql, [$id_empleado]);
    }

    /**
     * Actualizar proyecto
     */
    public function actualizar($id, $datos) {
        $campos = [];
        $parametros = [];

        foreach ($datos as $campo => $valor) {
            if ($campo !== 'id_proyecto') {
                $campos[] = "$campo = ?";
                $parametros[] = ($campo === 'estado' || $campo === 'prioridad') ? $valor : Seguridad::sanitizar($valor);
            }
        }

        $parametros[] = $id;

        $sql = "UPDATE " . $this->tabla . " SET " . implode(', ', $campos) . " WHERE id_proyecto = ?";
        return $this->ejecutarSQL($sql, $parametros);
    }

    /**
     * Actualizar estado del proyecto
     */
    public function actualizarEstado($id, $estado) {
        $sql = "UPDATE " . $this->tabla . " SET estado = ? WHERE id_proyecto = ?";
        return $this->ejecutarSQL($sql, [$estado, $id]);
    }

    /**
     * Actualizar porcentaje general
     */
    public function actualizarPorcentaje($id, $porcentaje) {
        $sql = "UPDATE " . $this->tabla . " SET porcentaje_general = ? WHERE id_proyecto = ?";
        return $this->ejecutarSQL($sql, [$porcentaje, $id]);
    }

    /**
     * Obtener proyectos en progreso
     */
    public function obtenerEnProgreso() {
        $sql = "SELECT * FROM " . $this->tabla . " WHERE estado = ? ORDER BY fecha_entrega ASC";
        return $this->obtenerResultados($sql, [ESTADO_PROYECTO_EN_PROGRESO]);
    }

    /**
     * Obtener proyectos finalizados
     */
    public function obtenerFinalizados() {
        $sql = "SELECT * FROM " . $this->tabla . " WHERE estado = ? ORDER BY fecha_entrega DESC";
        return $this->obtenerResultados($sql, [ESTADO_PROYECTO_FINALIZADO]);
    }
}
