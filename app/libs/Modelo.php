<?php
/**
 * Clase base para todos los modelos
 */

abstract class Modelo {
    protected $conexion;
    protected $tabla;

    public function __construct() {
        $this->conexion = BaseDatos::obtenerInstancia()->obtenerConexion();
    }

    /**
     * Obtener todos los registros
     */
    public function obtenerTodos() {
        $sql = "SELECT * FROM " . $this->tabla;
        return BaseDatos::obtenerInstancia()->obtenerTodos($sql);
    }

    /**
     * Obtener un registro por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM " . $this->tabla . " WHERE id_" . str_replace('_', '', lcfirst(str_replace('Modelo', '', get_class($this)))) . " = ?";
        return BaseDatos::obtenerInstancia()->obtenerUno($sql, [$id]);
    }

    /**
     * Obtener registros con WHERE
     */
    public function obtenerCon($condiciones) {
        $sql = "SELECT * FROM " . $this->tabla . " WHERE " . $condiciones;
        return BaseDatos::obtenerInstancia()->obtenerTodos($sql);
    }

    /**
     * Obtener un registro con WHERE
     */
    public function obtenerUnoCon($condiciones) {
        $sql = "SELECT * FROM " . $this->tabla . " WHERE " . $condiciones;
        return BaseDatos::obtenerInstancia()->obtenerUno($sql);
    }

    /**
     * Ejecutar una consulta SQL personalizada
     */
    public function ejecutarSQL($sql, $parametros = []) {
        return BaseDatos::obtenerInstancia()->ejecutar($sql, $parametros);
    }

    /**
     * Obtener resultados de SQL personalizado
     */
    public function obtenerResultados($sql, $parametros = []) {
        return BaseDatos::obtenerInstancia()->obtenerTodos($sql, $parametros);
    }

    /**
     * Obtener un resultado de SQL personalizado
     */
    public function obtenerUnResultado($sql, $parametros = []) {
        return BaseDatos::obtenerInstancia()->obtenerUno($sql, $parametros);
    }

    /**
     * Obtener el último ID insertado
     */
    protected function obtenerUltimoId() {
        return BaseDatos::obtenerInstancia()->obtenerUltimoId();
    }

    /**
     * Contar registros
     */
    public function contar($tabla = null) {
        $t = $tabla ?? $this->tabla;
        $sql = "SELECT COUNT(*) as total FROM " . $t;
        $resultado = BaseDatos::obtenerInstancia()->obtenerUno($sql);
        return $resultado['total'] ?? 0;
    }
}
