<?php
/**
 * Clase para funciones de seguridad
 */

class Seguridad {
    /**
     * Encriptar una contraseña
     */
    public static function encriptarPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verificar una contraseña
     */
    public static function verificarPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Sanitizar una entrada
     */
    public static function sanitizar($dato) {
        return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitizar array
     */
    public static function sanitizarArray($datos) {
        $sanitizados = [];
        foreach ($datos as $clave => $valor) {
            $sanitizados[$clave] = self::sanitizar($valor);
        }
        return $sanitizados;
    }

    /**
     * Generar un token CSRF
     */
    public static function generarTokenCSRF() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verificar token CSRF
     */
    public static function verificarTokenCSRF($token) {
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    /**
     * Escapar para HTML
     */
    public static function escaparHTML($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Escapar para SQL (aunque usar prepared statements es lo recomendado)
     */
    public static function escaparSQL($string) {
        return addslashes($string);
    }
}
