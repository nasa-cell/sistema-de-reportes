<?php
/**
 * Script diagnóstico temporal.
 * - Registra cada petición en storage/debug_requests.log
 * - Muestra las últimas 200 líneas del log
 * IMPORTANTE: elimínalo del servidor cuando termines (temporal).
 */

date_default_timezone_set('UTC');
$logDir = __DIR__ . '/storage';
$logFile = $logDir . '/debug_requests.log';

if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}

$entry = [];
$entry[] = "----- " . date('Y-m-d H:i:s') . " -----";
$entry[] = 'REQUEST_URI: ' . ($_SERVER['REQUEST_URI'] ?? '');
$entry[] = 'REMOTE_ADDR: ' . ($_SERVER['REMOTE_ADDR'] ?? '');
$entry[] = 'HTTP_HOST: ' . ($_SERVER['HTTP_HOST'] ?? '');

$entry[] = 'HEADERS:';
foreach (getallheaders() as $k => $v) {
    $entry[] = "  $k: $v";
}

$entry[] = 'COOKIES:';
foreach ($_COOKIE as $k => $v) {
    $entry[] = "  $k = $v";
}

$entry[] = 'GET:';
foreach ($_GET as $k => $v) {
    $entry[] = "  $k = $v";
}

$entry[] = 'POST:';
foreach ($_POST as $k => $v) {
    $entry[] = "  $k = $v";
}

$entry[] = "\n";

@file_put_contents($logFile, implode("\n", $entry), FILE_APPEND | LOCK_EX);

// Mostrar últimas 200 líneas
if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $last = array_slice($lines, -200);
    echo '<pre>' . htmlspecialchars(implode("\n", $last)) . '</pre>';
} else {
    echo "No hay entradas de log aún.";
}

?>
