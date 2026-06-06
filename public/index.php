<?php
/**
 * Punto de entrada principal de la aplicación TecnoSoluciones S.A.
 * Router que gestiona todas las rutas de la aplicación
 */

// Incluir configuración
require_once __DIR__ . '/../app/config/Config.php';
require_once __DIR__ . '/../app/config/Database.php';

// Incluir clases base
require_once __DIR__ . '/../app/libs/Modelo.php';
require_once __DIR__ . '/../app/libs/Controlador.php';
require_once __DIR__ . '/../app/libs/Vista.php';

// Incluir helpers
require_once __DIR__ . '/../app/helpers/Seguridad.php';
require_once __DIR__ . '/../app/helpers/Validador.php';
require_once __DIR__ . '/../app/helpers/Sesion.php';

// Incluir modelos
$archivos_modelos = glob(__DIR__ . '/../app/modelos/Modelo*.php');
foreach ($archivos_modelos as $archivo) {
    require_once $archivo;
}

// Incluir controladores
$archivos_controladores = glob(__DIR__ . '/../app/controladores/Controlador*.php');
foreach ($archivos_controladores as $archivo) {
    require_once $archivo;
}

// Iniciar sesión
Sesion::iniciar();

// Obtener la ruta solicitada
if (isset($_GET['ruta'])) {
    $ruta = trim($_GET['ruta'], '/');
} else {
    $ruta = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    if ($ruta === '' || $ruta === 'index.php') {
        $ruta = 'inicio/index';
    }
}

// Dividir la ruta en controlador y acción
$partes = explode('/', $ruta);
$controlador = isset($partes[0]) ? $partes[0] : 'inicio';
$accion = isset($partes[1]) ? $partes[1] : 'index';

// Mapear a rutas de carpetas
$mapeo_controladores = [
    'autenticacion' => 'Autenticacion',
    'inicio' => 'Inicio',
    'admin' => 'Admin',
    'cliente' => 'Cliente',
    'empleado' => 'Empleado',
    'reportes' => 'Reportes',
    'reportes-lista' => 'ReportesListas',
];

// Verificar que el controlador existe
if (!isset($mapeo_controladores[$controlador])) {
    header("HTTP/1.1 404 Not Found");
    die("Controlador no encontrado: " . htmlspecialchars($controlador));
}

// Construir nombre del controlador
$nombre_controlador = 'Controlador' . $mapeo_controladores[$controlador];
$nombre_accion = 'accion_' . str_replace('-', '_', $accion);

// Verificar que la clase existe
if (!class_exists($nombre_controlador)) {
    header("HTTP/1.1 404 Not Found");
    die("La clase $nombre_controlador no existe");
}

// Crear instancia del controlador
$instancia_controlador = new $nombre_controlador();

// Convertir nombre con guiones a camelCase
$accion_metodo = str_replace('-', '_', $accion);
$partes_accion = explode('_', $accion_metodo);
$partes_accion = array_map('ucfirst', $partes_accion);
$accion_metodo = lcfirst(implode('', $partes_accion));

// Verificar que el método existe
if (!method_exists($instancia_controlador, $accion_metodo)) {
    header("HTTP/1.1 404 Not Found");
    die("El método $accion_metodo no existe en $nombre_controlador");
}

// Llamar al método
$instancia_controlador->$accion_metodo();
?>
