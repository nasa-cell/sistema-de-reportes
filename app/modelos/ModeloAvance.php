<?php
/**
 * Modelo para la tabla avances_proyecto
 */

class ModeloAvance extends Modelo {
    protected $tabla = 'avances_proyecto';

    /**
     * Registrar un avance
     */
    public function registrar($datos) {
        $sql = "INSERT INTO " . $this->tabla . " (id_proyecto, id_empleado, porcentaje, observacion) 
                VALUES (?, ?, ?, ?)";
        
        $parametros = [
            $datos['id_proyecto'],
            $datos['id_empleado'],
            $datos['porcentaje'],
            Seguridad::sanitizar($datos['observacion'] ?? '')
        ];

        $this->ejecutarSQL($sql, $parametros);
        return $this->obtenerUltimoId();
    }

    /**
     * Obtener avances de un proyecto
     */
    public function obtenerDelProyecto($id_proyecto) {
        $sql = "SELECT av.*, u.nombres, u.apellidos
                FROM " . $this->tabla . " av
                INNER JOIN empleados e ON av.id_empleado = e.id_empleado
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                WHERE av.id_proyecto = ?
                ORDER BY av.fecha_actualizacion DESC";
        return $this->obtenerResultados($sql, [$id_proyecto]);
    }

    /**
     * Obtener avance más reciente de un proyecto
     */
    public function obtenerUltimoDelProyecto($id_proyecto) {
        $sql = "SELECT av.*, u.nombres, u.apellidos
                FROM " . $this->tabla . " av
                INNER JOIN empleados e ON av.id_empleado = e.id_empleado
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                WHERE av.id_proyecto = ?
                ORDER BY av.fecha_actualizacion DESC
                LIMIT 1";
        return $this->obtenerUnResultado($sql, [$id_proyecto]);
    }

    /**
     * Obtener avances de un empleado
     */
    public function obtenerDelEmpleado($id_empleado) {
        $sql = "SELECT av.*, p.titulo as proyecto_titulo, p.id_proyecto
                FROM " . $this->tabla . " av
                INNER JOIN proyectos p ON av.id_proyecto = p.id_proyecto
                WHERE av.id_empleado = ?
                ORDER BY av.fecha_actualizacion DESC";
        return $this->obtenerResultados($sql, [$id_empleado]);
    }

    /**
     * Obtener porcentaje promedio de un proyecto
     */
    public function obtenerPorcentajePromedio($id_proyecto) {
        $sql = "SELECT AVG(porcentaje) as promedio FROM " . $this->tabla . " WHERE id_proyecto = ?";
        $resultado = $this->obtenerUnResultado($sql, [$id_proyecto]);
        return round($resultado['promedio'] ?? 0);
    }

    /**
     * Obtener historial de cambios de un proyecto
     */
    public function obtenerHistorial($id_proyecto) {
        $sql = "SELECT av.*, u.nombres, u.apellidos, e.cargo
                FROM " . $this->tabla . " av
                INNER JOIN empleados e ON av.id_empleado = e.id_empleado
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                WHERE av.id_proyecto = ?
                ORDER BY av.fecha_actualizacion DESC";
        return $this->obtenerResultados($sql, [$id_proyecto]);
    }
}
