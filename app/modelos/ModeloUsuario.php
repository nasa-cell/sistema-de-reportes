<?php
/**
 * Modelo para la tabla usuarios
 */

class ModeloUsuario extends Modelo {
    protected $tabla = 'usuarios';

    /**
     * Registrar un nuevo usuario
     */
    public function registrar($datos) {
        $sql = "INSERT INTO " . $this->tabla . " (nombres, apellidos, usuario, correo, password, rol, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $parametros = [
            Seguridad::sanitizar($datos['nombres']),
            Seguridad::sanitizar($datos['apellidos']),
            Seguridad::sanitizar($datos['usuario']),
            Seguridad::sanitizar($datos['correo']),
            $datos['password'], // Ya está encriptado
            $datos['rol'] ?? ROL_CLIENTE,
            ESTADO_ACTIVO
        ];

        $resultado = $this->ejecutarSQL($sql, $parametros);
        return $this->obtenerUltimoId();
    }

    /**
     * Obtener usuario por correo
     */
    public function obtenerPorCorreo($correo) {
        $sql = "SELECT * FROM " . $this->tabla . " WHERE correo = ? AND estado = ?";
        return $this->obtenerUnResultado($sql, [
            Seguridad::sanitizar($correo),
            ESTADO_ACTIVO
        ]);
    }

    /**
     * Obtener usuario por usuario
     */
    public function obtenerPorUsuario($usuario) {
        $sql = "SELECT * FROM " . $this->tabla . " WHERE usuario = ? AND estado = ?";
        return $this->obtenerUnResultado($sql, [
            Seguridad::sanitizar($usuario),
            ESTADO_ACTIVO
        ]);
    }

    /**
     * Verificar si un correo ya existe
     */
    public function correoExiste($correo) {
        $sql = "SELECT COUNT(*) as total FROM " . $this->tabla . " WHERE correo = ?";
        $resultado = $this->obtenerUnResultado($sql, [Seguridad::sanitizar($correo)]);
        return $resultado['total'] > 0;
    }

    /**
     * Verificar si un usuario ya existe
     */
    public function usuarioExiste($usuario) {
        $sql = "SELECT COUNT(*) as total FROM " . $this->tabla . " WHERE usuario = ?";
        $resultado = $this->obtenerUnResultado($sql, [Seguridad::sanitizar($usuario)]);
        return $resultado['total'] > 0;
    }

    /**
     * Actualizar usuario
     */
    public function actualizar($id, $datos) {
        $campos = [];
        $parametros = [];

        foreach ($datos as $campo => $valor) {
            $campos[] = "$campo = ?";
            $parametros[] = Seguridad::sanitizar($valor);
        }

        $parametros[] = $id;

        $sql = "UPDATE " . $this->tabla . " SET " . implode(', ', $campos) . " WHERE id_usuario = ?";
        return $this->ejecutarSQL($sql, $parametros);
    }

    /**
     * Obtener todos los usuarios de un rol específico
     */
    public function obtenerPorRol($rol) {
        $sql = "SELECT * FROM " . $this->tabla . " WHERE rol = ? AND estado = ? ORDER BY nombres ASC";
        return $this->obtenerResultados($sql, [$rol, ESTADO_ACTIVO]);
    }

    /**
     * Inactivar un usuario
     */
    public function inactivar($id) {
        $sql = "UPDATE " . $this->tabla . " SET estado = ? WHERE id_usuario = ?";
        return $this->ejecutarSQL($sql, [ESTADO_INACTIVO, $id]);
    }

    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM " . $this->tabla . " WHERE id_usuario = ?";
        return $this->obtenerUnResultado($sql, [$id]);
    }

    /**
     * Borrar usuario físicamente
     */
    public function eliminar($id) {
        $sql = "DELETE FROM " . $this->tabla . " WHERE id_usuario = ?";
        return $this->ejecutarSQL($sql, [$id]);
    }
}
