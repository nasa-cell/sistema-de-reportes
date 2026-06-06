<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo NOMBRE_APP; ?></title>
    <link rel="stylesheet" href="<?php echo URL_RAIZ; ?>public/css/estilos.css?v=<?php echo filemtime(__DIR__ . '/../../../public/css/estilos.css'); ?>">
</head>
<body class="auth-pagina">
    <div class="auth-contenedor">
        <div class="auth-caja">
            <div class="auth-titulo">
                <h1><?php echo NOMBRE_APP; ?></h1>
            </div>

            <?php if (Sesion::hayMensaje('exito')): ?>
                <div class="alerta alerta-exito">
                    <?php echo Sesion::obtenerMensaje('exito'); ?>
                </div>
            <?php endif; ?>

            <?php if (Sesion::hayMensaje('error')): ?>
                <div class="alerta alerta-error">
                    <?php echo Sesion::obtenerMensaje('error'); ?>
                </div>
            <?php endif; ?>
