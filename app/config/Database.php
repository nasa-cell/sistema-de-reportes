<?php
/**
 * Clase de conexión a base de datos con PDO
 */

class BaseDatos {
    private $conexion;
    private static $instancia = null;

    /**
     * Constructor privado para evitar múltiples instancias
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PUERTO . ";dbname=" . DB_NOMBRE . ";charset=" . DB_CHARSET;
            
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES  => false,
            ];

            $this->conexion = new PDO($dsn, DB_USUARIO, DB_PASSWORD, $opciones);
        } catch (PDOException $e) {
            die("Error de conexión a base de datos: " . $e->getMessage());
        }
    }

    /**
     * Patrón Singleton para obtener la instancia
     */
    public static function obtenerInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    /**
     * Obtener la conexión PDO
     */
    public function obtenerConexion() {
        return $this->conexion;
    }

    /**
     * Ejecutar una consulta con prepared statement
     */
    public function ejecutar($sql, $parametros = []) {
        try {
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->execute($parametros);
            return $sentencia;
        } catch (PDOException $e) {
            throw new Exception("Error en la consulta: " . $e->getMessage());
        }
    }

    /**
     * Obtener un registro
     */
    public function obtenerUno($sql, $parametros = []) {
        $sentencia = $this->ejecutar($sql, $parametros);
        return $sentencia->fetch();
    }

    /**
     * Obtener varios registros
     */
    public function obtenerTodos($sql, $parametros = []) {
        $sentencia = $this->ejecutar($sql, $parametros);
        return $sentencia->fetchAll();
    }

    /**
     * Obtener el ID de la última inserción
     */
    public function obtenerUltimoId() {
        return $this->conexion->lastInsertId();
    }

    /**
     * Obtener el número de filas afectadas
     */
    public function obtenerFilasAfectadas($sentencia) {
        return $sentencia->rowCount();
    }

    /**
     * Iniciar transacción
     */
    public function iniciarTransaccion() {
        return $this->conexion->beginTransaction();
    }

    /**
     * Confirmar transacción
     */
    public function confirmarTransaccion() {
        return $this->conexion->commit();
    }

    /**
     * Revertir transacción
     */
    public function revertirTransaccion() {
        return $this->conexion->rollBack();
    }

    /**
     * Evitar la clonación
     */
    private function __clone() {}

    /**
     * Evitar la deserialización
     */
    public function __wakeup() {
        throw new Exception("No se puede deserializar una conexión de base de datos");
    }
}
