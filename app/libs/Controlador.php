<?php
/**
 * Clase base para todos los controladores
 */

class Controlador {
    protected $vista;

    public function __construct() {
        $this->vista = new Vista();
    }

    /**
     * Cargar una vista con datos y mostrarla
     */
    protected function vista($nombre, $datos = []) {
        $contenido = $this->vista->cargar($nombre, $datos);
        echo $contenido;
        return $contenido;
    }

    /**
     * Redirigir a una URL
     */
    protected function redirigir($url) {
        $target = rtrim(URL_RAIZ . $url, '/');
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https' : 'http';
        $current = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '');
        $current = rtrim($current, '/');
        // Evitar redirigir a la misma URL (posible bucle)
        if ($current === $target) {
            return;
        }

        header("Location: " . URL_RAIZ . $url);
        exit;
    }

    /**
     * Verificar si el usuario está autenticado
     */
    protected function verificarAutenticacion() {
        if (!isset($_SESSION['id_usuario'])) {
            $this->redirigir('autenticacion/iniciar-sesion');
        }
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    protected function verificarRol($rol) {
        $this->verificarAutenticacion();
        
        if ($_SESSION['rol'] !== $rol) {
            header("HTTP/1.1 403 Forbidden");
            die("Acceso denegado. No tienes permisos para acceder a esta sección.");
        }
    }

    /**
     * Obtener los datos del usuario en sesión
     */
    protected function usuarioEnSesion() {
        return $_SESSION ?? null;
    }

    /**
     * Enviar respuesta JSON
     */
    protected function json($datos, $codigo = 200) {
        header('Content-Type: application/json');
        http_response_code($codigo);
        echo json_encode($datos);
        exit;
    }
}
