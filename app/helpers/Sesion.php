<?php
/**
 * Clase para gestionar sesiones
 * Usa sesiones basadas en base de datos para evitar problemas con cookies en hosts restrictivos
 */

class Sesion {
    private static $datos = [];
    private static $sesion_id = null;

    public static function iniciar() {
        if (session_status() === PHP_SESSION_NONE) {
            // Deshabilitar cookies automáticas para evitar problemas con hosts restrictivos
            ini_set('session.use_cookies', 0);
            ini_set('session.use_only_cookies', 0);
            
            // Obtener ID de sesión de URL o crear uno nuevo
            self::$sesion_id = $_GET['sesion_id'] ?? $_POST['sesion_id'] ?? null;
            
            if (!self::$sesion_id) {
                self::$sesion_id = bin2hex(random_bytes(32));
            }
            
            session_id(self::$sesion_id);
            session_name(SESION_NOMBRE);
            
            // Intentar usar handlers de sesión personalizados
            session_set_save_handler(
                [self::class, 'open'],
                [self::class, 'close'],
                [self::class, 'read'],
                [self::class, 'write'],
                [self::class, 'destroy'],
                [self::class, 'gc']
            );
            
            session_start();
            self::verificarExpiracion();
        }
    }

    // Handlers de sesión para base de datos
    public static function open($path, $name) {
        return true;
    }

    public static function close() {
        return true;
    }

    public static function read($sid) {
        try {
            $bd = BaseDatos::obtenerInstancia();
            $resultado = $bd->obtenerUno(
                "SELECT datos FROM sesiones WHERE id_sesion = ? AND expira_en > NOW()",
                [$sid]
            );
            return $resultado ? $resultado['datos'] : '';
        } catch (Exception $e) {
            return '';
        }
    }

    public static function write($sid, $data) {
        try {
            $bd = BaseDatos::obtenerInstancia();
            $expira = date('Y-m-d H:i:s', time() + SESION_DURACION);
            
            // Intentar actualizar, si no existe, insertar
            $existente = $bd->obtenerUno(
                "SELECT id_sesion FROM sesiones WHERE id_sesion = ?",
                [$sid]
            );
            
            if ($existente) {
                $bd->ejecutar(
                    "UPDATE sesiones SET datos = ?, expira_en = ? WHERE id_sesion = ?",
                    [$data, $expira, $sid]
                );
            } else {
                $bd->ejecutar(
                    "INSERT INTO sesiones (id_sesion, datos, expira_en, creada_en) VALUES (?, ?, ?, NOW())",
                    [$sid, $data, $expira]
                );
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function destroy($sid) {
        try {
            $bd = BaseDatos::obtenerInstancia();
            $bd->ejecutar("DELETE FROM sesiones WHERE id_sesion = ?", [$sid]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function gc($maxlifetime) {
        try {
            $bd = BaseDatos::obtenerInstancia();
            $bd->ejecutar("DELETE FROM sesiones WHERE expira_en < NOW()");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function obtenerIdSesion() {
        return self::$sesion_id ?? session_id();
    }

    /**
     * Generar URL con parámetro de sesión para navegación sin cookies
     */
    public static function agregarIdSesionAURL($url) {
        $sesion_id = self::obtenerIdSesion();
        $separador = (strpos($url, '?') !== false) ? '&' : '?';
        return $url . $separador . 'sesion_id=' . urlencode($sesion_id);
    }

    public static function crear($clave, $valor) {
        $_SESSION[$clave] = $valor;
    }

    public static function obtener($clave) {
        return $_SESSION[$clave] ?? null;
    }

    public static function flash($clave) {
        $valor = $_SESSION[$clave] ?? null;
        if (isset($_SESSION[$clave])) {
            unset($_SESSION[$clave]);
        }
        return $valor;
    }

    public static function eliminar($clave) {
        if (isset($_SESSION[$clave])) {
            unset($_SESSION[$clave]);
        }
    }

    public static function destruir() {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function existe($clave) {
        return isset($_SESSION[$clave]);
    }

    /**
     * CSRF token helpers
     */
    public static function generarTokenCSRF() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verificarTokenCSRF($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function hayMensaje($tipo) {
        return isset($_SESSION['mensaje_' . $tipo]);
    }

    public static function obtenerMensaje($tipo) {
        $mensaje = $_SESSION['mensaje_' . $tipo] ?? null;
        if ($mensaje) {
            unset($_SESSION['mensaje_' . $tipo]);
        }
        return $mensaje;
    }

    public static function crearMensaje($tipo, $mensaje) {
        $_SESSION['mensaje_' . $tipo] = $mensaje;
    }

    private static function verificarExpiracion() {
        $ahora = time();
        if (isset($_SESSION['ultima_actividad']) && ($ahora - $_SESSION['ultima_actividad']) > SESION_DURACION) {
            // Limpiar sólo los datos de usuario para preservar mensajes flash
            unset($_SESSION['id_usuario']);
            unset($_SESSION['usuario']);
            unset($_SESSION['rol']);
            unset($_SESSION['nombres']);
            unset($_SESSION['apellidos']);
            unset($_SESSION['correo']);
            unset($_SESSION['ultima_actividad']);

            if (!isset($_GET['expirada']) || $_GET['expirada'] != 1) {
                self::crearMensaje('advertencia', 'Tu sesión ha expirado. Por favor inicia sesión nuevamente.');
                // Redirigir añadiendo un parámetro para evitar bucles de redirección
                $sep = (strpos(URL_RAIZ . 'autenticacion/iniciar-sesion', '?') === false) ? '?' : '&';
                header("Location: " . URL_RAIZ . "autenticacion/iniciar-sesion" . $sep . "expirada=1");
            }
            exit;
        }
        $_SESSION['ultima_actividad'] = $ahora;
    }
}
