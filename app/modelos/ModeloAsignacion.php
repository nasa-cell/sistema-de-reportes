<?php
/**
 * Modelo para la tabla asignaciones_proyecto
 */

class ModeloAsignacion extends Modelo {
    protected $tabla = 'asignaciones_proyecto';

    /**
     * Asignar un empleado a un proyecto
     */
    public function asignar($datos) {
        // Evitar asignar más de 3 empleados al mismo proyecto
        $sqlCount = "SELECT COUNT(*) as total FROM " . $this->tabla . " WHERE id_proyecto = ?";
        $resCount = $this->obtenerUnResultado($sqlCount, [$datos['id_proyecto']]);
        if (!empty($resCount) && intval($resCount['total']) >= 3) {
            throw new Exception('Límite de 3 empleados alcanzado para este proyecto.');
        }

        // Evitar asignar el mismo empleado dos veces al mismo proyecto
        $sqlCheck = "SELECT COUNT(*) as total FROM " . $this->tabla . " WHERE id_proyecto = ? AND id_empleado = ?";
        $resultado = $this->obtenerUnResultado($sqlCheck, [$datos['id_proyecto'], $datos['id_empleado']]);
        if (!empty($resultado) && intval($resultado['total']) > 0) {
            throw new Exception('Empleado ya asignado a este proyecto.');
        }

        $sql = "INSERT INTO " . $this->tabla . " (id_proyecto, id_empleado, rol_en_proyecto, observacion) 
                VALUES (?, ?, ?, ?)";

        $parametros = [
            $datos['id_proyecto'],
            $datos['id_empleado'],
            Seguridad::sanitizar($datos['rol_en_proyecto'] ?? ''),
            Seguridad::sanitizar($datos['observacion'] ?? '')
        ];

        $this->ejecutarSQL($sql, $parametros);
        return $this->obtenerUltimoId();
    }

    /**
     * Obtener asignaciones de un proyecto
     */
    public function obtenerDelProyecto($id_proyecto) {
        $sql = "SELECT ap.*, u.nombres, u.apellidos, u.correo, e.cargo, e.especialidad
                FROM " . $this->tabla . " ap
                INNER JOIN empleados e ON ap.id_empleado = e.id_empleado
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                WHERE ap.id_proyecto = ?
                ORDER BY u.nombres ASC";
        return $this->obtenerResultados($sql, [$id_proyecto]);
    }

    /**
     * Obtener asignaciones de un empleado
     */
    public function obtenerDelEmpleado($id_empleado) {
        $sql = "SELECT ap.*, p.id_proyecto, p.titulo, p.descripcion, p.prioridad, p.estado
                FROM " . $this->tabla . " ap
                INNER JOIN proyectos p ON ap.id_proyecto = p.id_proyecto
                WHERE ap.id_empleado = ?
                ORDER BY p.fecha_entrega ASC";
        return $this->obtenerResultados($sql, [$id_empleado]);
    }

    /**
     * Obtener una asignación específica
     */
    public function obtenerPorId($id) {
        $sql = "SELECT ap.*, u.nombres, u.apellidos, e.cargo
                FROM " . $this->tabla . " ap
                INNER JOIN empleados e ON ap.id_empleado = e.id_empleado
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                WHERE ap.id_asignacion = ?";
        return $this->obtenerUnResultado($sql, [$id]);
    }

    /**
     * Actualizar asignación
     */
    public function actualizar($id, $datos) {
        $campos = [];
        $parametros = [];

        foreach ($datos as $campo => $valor) {
            if ($campo !== 'id_asignacion') {
                $campos[] = "$campo = ?";
                $parametros[] = Seguridad::sanitizar($valor);
            }
        }

        $parametros[] = $id;

        $sql = "UPDATE " . $this->tabla . " SET " . implode(', ', $campos) . " WHERE id_asignacion = ?";
        return $this->ejecutarSQL($sql, $parametros);
    }

    /**
     * Eliminar una asignación
     */
    public function eliminar($id) {
        $sql = "DELETE FROM " . $this->tabla . " WHERE id_asignacion = ?";
        return $this->ejecutarSQL($sql, [$id]);
    }

    /**
     * Obtener empleados asignados a un proyecto
     */
    public function obtenerEmpleadosDelProyecto($id_proyecto) {
        $sql = "SELECT COUNT(*) as total FROM " . $this->tabla . " WHERE id_proyecto = ?";
        $resultado = $this->obtenerUnResultado($sql, [$id_proyecto]);
        return $resultado['total'] ?? 0;
    }
}
