<?php
/**
 * Modelo para la tabla clientes
 */

class ModeloCliente extends Modelo {
    protected $tabla = 'clientes';

    /**
     * Crear un cliente
     */
    public function crear($datos) {
        $sql = "INSERT INTO " . $this->tabla . " (id_usuario, empresa, telefono, direccion, estado) 
                VALUES (?, ?, ?, ?, ?)";
        
        $parametros = [
            $datos['id_usuario'],
            Seguridad::sanitizar($datos['empresa'] ?? ''),
            Seguridad::sanitizar($datos['telefono'] ?? ''),
            Seguridad::sanitizar($datos['direccion'] ?? ''),
            ESTADO_ACTIVO
        ];

        $this->ejecutarSQL($sql, $parametros);
        return $this->obtenerUltimoId();
    }

    /**
     * Obtener cliente por ID de usuario
     */
    public function obtenerPorIdUsuario($id_usuario) {
        $sql = "SELECT * FROM " . $this->tabla . " WHERE id_usuario = ?";
        return $this->obtenerUnResultado($sql, [$id_usuario]);
    }

    /**
     * Obtener todos los clientes con sus datos de usuario
     */
    public function obtenerTodosConDatos() {
        $sql = "SELECT c.*, u.nombres, u.apellidos, u.correo, u.usuario, u.estado 
            FROM " . $this->tabla . " c
            INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
            WHERE c.estado = ? AND u.estado = ?
            ORDER BY c.id_cliente ASC";
        return $this->obtenerResultados($sql, [ESTADO_ACTIVO, ESTADO_ACTIVO]);
    }

    /**
     * Obtener cliente con datos por ID
     */
    public function obtenerConDatos($id) {
        $sql = "SELECT c.*, u.nombres, u.apellidos, u.correo, u.usuario, u.estado 
                FROM " . $this->tabla . " c
                INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
                WHERE c.id_cliente = ?";
        return $this->obtenerUnResultado($sql, [$id]);
    }

    /**
     * Actualizar cliente
     */
    public function actualizar($id, $datos) {
        $datosCliente = [];
        $datosUsuario = [];
        $camposCliente = [];
        $camposUsuario = [];
        $parametrosCliente = [];
        $parametrosUsuario = [];

        // Separar datos de usuario y cliente
        foreach ($datos as $campo => $valor) {
            if ($campo === 'estado') {
                $camposUsuario[] = "estado = ?";
                $parametrosUsuario[] = Seguridad::sanitizar($valor);
                $camposCliente[] = "estado = ?";
                $parametrosCliente[] = Seguridad::sanitizar($valor);
            } elseif (in_array($campo, ['nombres', 'apellidos', 'correo'])) {
                $camposUsuario[] = "$campo = ?";
                $parametrosUsuario[] = Seguridad::sanitizar($valor);
            } elseif ($campo !== 'id_cliente') {
                $camposCliente[] = "$campo = ?";
                $parametrosCliente[] = Seguridad::sanitizar($valor);
            }
        }

        // Obtener id_usuario primero
        $cliente = $this->obtenerUnResultado("SELECT id_usuario FROM " . $this->tabla . " WHERE id_cliente = ?", [$id]);
        if (!$cliente) {
            throw new Exception("Cliente no encontrado");
        }
        $id_usuario = $cliente['id_usuario'];

        // Actualizar tabla usuarios si hay cambios
        if (!empty($camposUsuario)) {
            $parametrosUsuario[] = $id_usuario;
            $sql = "UPDATE usuarios SET " . implode(', ', $camposUsuario) . " WHERE id_usuario = ?";
            $this->ejecutarSQL($sql, $parametrosUsuario);
        }

        // Actualizar tabla clientes si hay cambios
        if (!empty($camposCliente)) {
            $parametrosCliente[] = $id;
            $sql = "UPDATE " . $this->tabla . " SET " . implode(', ', $camposCliente) . " WHERE id_cliente = ?";
            return $this->ejecutarSQL($sql, $parametrosCliente);
        }

        return true;
    }

    /**
     * Inactivar cliente sin borrar sus datos
     */
    public function inactivar($id) {
        $sql = "UPDATE " . $this->tabla . " SET estado = ? WHERE id_cliente = ?";
        return $this->ejecutarSQL($sql, [ESTADO_INACTIVO, $id]);
    }

    /**
     * Eliminar cliente físicamente
     */
    public function eliminar($id) {
        $sql = "DELETE FROM " . $this->tabla . " WHERE id_cliente = ?";
        return $this->ejecutarSQL($sql, [$id]);
    }
}
