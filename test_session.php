<?php
/**
 * Archivo de prueba para verificar que el hosting envía cookies de sesión
 */
require_once __DIR__ . '/app/config/Config.php';
require_once __DIR__ . '/app/helpers/Sesion.php';

Sesion::iniciar();

echo "<pre>";
if (!Sesion::existe('prueba_cookie')) {
    Sesion::crear('prueba_cookie', 'ok');
    echo "Creada sesión y cookie.\n";
} else {
    echo "Sesión existente: " . (Sesion::obtener('prueba_cookie') ?? 'null') . "\n";
}

echo "\n\\_COOKIE:\n";
var_export($_COOKIE);
echo "</pre>";

?>
