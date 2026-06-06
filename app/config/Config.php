<?php
/**
 * Configuración global de la aplicación TecnoSoluciones S.A.
 * Soporta tanto ifastnet como Render.com
 */

// Cargar variables de entorno si existen
if (file_exists(__DIR__ . '/../../.env')) {
    $env = parse_ini_file(__DIR__ . '/../../.env');
    foreach ($env as $key => $value) {
        if (!isset($_ENV[$key])) {
            $_ENV[$key] = $value;
        }
    }
}

// Detectar ambiente
$environment = $_ENV['ENVIRONMENT'] ?? $_SERVER['ENVIRONMENT'] ?? 'production';

// URL según el ambiente
if ($environment === 'local') {
    define('URL_RAIZ', 'http://localhost:8000/');
} else {
    define('URL_RAIZ', $_ENV['URL_RAIZ'] ?? 'https://sistemareportes.42web.io/');
}

define('NOMBRE_APP', 'TecnoSoluciones S.A.');
define('RUTA_ALMACENAMIENTO_PDF', __DIR__ . '/../../storage/pdf/');

define('ZONA_HORARIA', 'America/Lima');

date_default_timezone_set(ZONA_HORARIA);

// Configuración de base de datos (soporta MySQL e Render PostgreSQL)
define('DB_HOST', $_ENV['DB_HOST'] ?? 'sql308.infinityfree.com');
define('DB_USUARIO', $_ENV['DB_USUARIO'] ?? 'if0_42055638');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? '3BNmZ0OqAQKW');
define('DB_NOMBRE', $_ENV['DB_NOMBRE'] ?? 'if0_42055638_sistemareportes');
define('DB_PUERTO', $_ENV['DB_PUERTO'] ?? 3306);
define('DB_CHARSET', 'utf8mb4');

// Configuración de sesión
define('SESION_NOMBRE', 'TECNOSOL_SESSION');
define('SESION_DURACION', 3600); // 1 hora

// Roles permitidos
define('ROL_ADMIN', 'ADMIN');
define('ROL_EMPLEADO', 'EMPLEADO');
define('ROL_CLIENTE', 'CLIENTE');

// Estados de usuarios
define('ESTADO_ACTIVO', 'ACTIVO');
define('ESTADO_INACTIVO', 'INACTIVO');

// Estados de solicitudes
define('ESTADO_SOLICITUD_PENDIENTE', 'PENDIENTE');
define('ESTADO_SOLICITUD_COTIZADO', 'COTIZADO');
define('ESTADO_SOLICITUD_ACEPTADO', 'ACEPTADO');
define('ESTADO_SOLICITUD_RECHAZADO', 'RECHAZADO');

// Estados de proyectos
define('ESTADO_PROYECTO_EN_ESPERA', 'EN_ESPERA');
define('ESTADO_PROYECTO_EN_PROGRESO', 'EN_PROGRESO');
define('ESTADO_PROYECTO_FINALIZADO', 'FINALIZADO');
define('ESTADO_PROYECTO_CANCELADO', 'CANCELADO');

// Urgencias
define('URGENCIA_BAJA', 'BAJA');
define('URGENCIA_MEDIA', 'MEDIA');
define('URGENCIA_ALTA', 'ALTA');
define('URGENCIA_URGENTE', 'URGENTE');

// Prioridades
define('PRIORIDAD_BAJA', 'BAJA');
define('PRIORIDAD_MEDIA', 'MEDIA');
define('PRIORIDAD_ALTA', 'ALTA');
define('PRIORIDAD_URGENTE', 'URGENTE');

// Rutas protegidas por rol
$RUTAS_PROTEGIDAS = [
    '/admin/' => [ROL_ADMIN],
    '/cliente/' => [ROL_CLIENTE],
    '/empleado/' => [ROL_EMPLEADO],
];
