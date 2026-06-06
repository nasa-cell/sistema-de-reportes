<?php
/**
 * Modelo para la tabla empleados
 */

class ModeloEmpleado extends Modelo {
    protected $tabla = 'empleados';

    /**
     * Crear un empleado
     */
    public function crear($datos) {
        $sql = "INSERT INTO " . $this->tabla . " (id_usuario, cargo, especialidad, estado) 
                VALUES (?, ?, ?, ?)";
        
        $parametros = [
            $datos['id_usuario'],
            Seguridad::sanitizar($datos['cargo'] ?? ''),
            Seguridad::sanitizar($datos['especialidad'] ?? ''),
            ESTADO_ACTIVO
        ];

        $this->ejecutarSQL($sql, $parametros);
        return $this->obtenerUltimoId();
    }

    /**
     * Obtener empleado por ID de usuario
     */
    public function obtenerPorIdUsuario($id_usuario) {
        $sql = "SELECT * FROM " . $this->tabla . " WHERE id_usuario = ?";
        return $this->obtenerUnResultado($sql, [$id_usuario]);
    }

    /**
     * Obtener todos los empleados activos con sus datos
     */
    public function obtenerTodosConDatos() {
        $sql = "SELECT e.*, u.nombres, u.apellidos, u.correo, u.usuario, u.estado 
                FROM " . $this->tabla . " e
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                WHERE e.estado = ?
                ORDER BY e.id_empleado ASC";
        return $this->obtenerResultados($sql, [ESTADO_ACTIVO]);
    }

    /**
     * Obtener todos los empleados con sus datos (incluye inactivos)
     */
    public function obtenerTodosConDatosIncluyeInactivos() {
        $sql = "SELECT e.*, u.nombres, u.apellidos, u.correo, u.usuario, u.estado 
                FROM " . $this->tabla . " e
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                ORDER BY e.id_empleado ASC";
        return $this->obtenerResultados($sql, []);
    }

    /**
     * Obtener empleado con datos por ID
     */
    public function obtenerConDatos($id) {
        $sql = "SELECT e.*, u.nombres, u.apellidos, u.correo, u.usuario, u.estado 
                FROM " . $this->tabla . " e
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                WHERE e.id_empleado = ?";
        return $this->obtenerUnResultado($sql, [$id]);
    }

    /**
     * Actualizar empleado
     */
    public function actualizar($id, $datos) {
        $datosEmpleado = [];
        $datosUsuario = [];
        $camposEmpleado = [];
        $camposUsuario = [];
        $parametrosEmpleado = [];
        $parametrosUsuario = [];

        // Separar datos de usuario y empleado
        foreach ($datos as $campo => $valor) {
            if ($campo === 'estado') {
                $camposUsuario[] = "estado = ?";
                $parametrosUsuario[] = Seguridad::sanitizar($valor);
                $camposEmpleado[] = "estado = ?";
                $parametrosEmpleado[] = Seguridad::sanitizar($valor);
            } elseif (in_array($campo, ['nombres', 'apellidos', 'correo'])) {
                $camposUsuario[] = "$campo = ?";
                $parametrosUsuario[] = Seguridad::sanitizar($valor);
            } elseif ($campo !== 'id_empleado') {
                $camposEmpleado[] = "$campo = ?";
                $parametrosEmpleado[] = Seguridad::sanitizar($valor);
            }
        }

        // Obtener id_usuario primero
        $empleado = $this->obtenerUnResultado("SELECT id_usuario FROM " . $this->tabla . " WHERE id_empleado = ?", [$id]);
        if (!$empleado) {
            throw new Exception("Empleado no encontrado");
        }
        $id_usuario = $empleado['id_usuario'];

        // Actualizar tabla usuarios si hay cambios
        if (!empty($camposUsuario)) {
            $parametrosUsuario[] = $id_usuario;
            $sql = "UPDATE usuarios SET " . implode(', ', $camposUsuario) . " WHERE id_usuario = ?";
            $this->ejecutarSQL($sql, $parametrosUsuario);
        }

        // Actualizar tabla empleados si hay cambios
        if (!empty($camposEmpleado)) {
            $parametrosEmpleado[] = $id;
            $sql = "UPDATE " . $this->tabla . " SET " . implode(', ', $camposEmpleado) . " WHERE id_empleado = ?";
            return $this->ejecutarSQL($sql, $parametrosEmpleado);
        }

        return true;
    }

    /**
     * Inactivar un empleado
     */
    public function inactivar($id) {
        $sql = "UPDATE " . $this->tabla . " SET estado = ? WHERE id_empleado = ?";
        return $this->ejecutarSQL($sql, [ESTADO_INACTIVO, $id]);
    }
}
