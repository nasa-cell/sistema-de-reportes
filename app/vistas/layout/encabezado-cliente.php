<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo NOMBRE_APP; ?></title>
    <link rel="stylesheet" href="<?php echo URL_RAIZ; ?>public/css/estilos.css?v=<?php echo filemtime(__DIR__ . '/../../../public/css/estilos.css'); ?>">
</head>
<body>
    <!-- Navbar Moderno -->
    <nav class="navbar-moderno">
        <div class="navbar-content">
            <!-- Logo -->
            <a href="<?php echo URL_RAIZ; ?>" class="navbar-brand">
                <div class="navbar-icon">⚡</div>
                <span class="navbar-text"><?php echo NOMBRE_APP; ?></span>
            </a>

            <!-- Hamburger (móvil) -->
            <button class="navbar-hamburger" id="navbar-hamburger">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <!-- Menú -->
            <ul class="navbar-menu" id="navbar-menu">
                <li class="navbar-menu-item">
                    <a href="<?php echo URL_RAIZ; ?>cliente/inicio" 
                       class="navbar-menu-link<?php echo (strpos(trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'), 'cliente/inicio') === 0) ? ' active' : ''; ?>" 
                       data-section="inicio">
                        🏠 Inicio
                    </a>
                </li>
                <li class="navbar-menu-item">
                    <a href="<?php echo URL_RAIZ; ?>cliente/nueva-solicitud" 
                       class="navbar-menu-link<?php echo (strpos(trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'), 'cliente/nueva-solicitud') === 0) ? ' active' : ''; ?>" 
                       data-section="nueva-solicitud">
                        ➕ Nueva Solicitud
                    </a>
                </li>
                <li class="navbar-menu-item">
                    <a href="<?php echo URL_RAIZ; ?>cliente/mis-solicitudes" 
                       class="navbar-menu-link<?php echo (strpos(trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'), 'cliente/mis-solicitudes') === 0) ? ' active' : ''; ?>" 
                       data-section="mis-solicitudes">
                        📋 Mis Solicitudes
                    </a>
                </li>
                <li class="navbar-menu-item">
                    <a href="<?php echo URL_RAIZ; ?>cliente/mis-proyectos" 
                       class="navbar-menu-link<?php echo (strpos(trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'), 'cliente/mis-proyectos') === 0) ? ' active' : ''; ?>" 
                       data-section="mis-proyectos">
                        🚀 Mis Proyectos
                    </a>
                </li>
                <li class="navbar-menu-item" style="margin-left: auto;">
                    <a href="<?php echo URL_RAIZ; ?>autenticacion/cerrar-sesion" 
                       class="navbar-menu-link" 
                       data-section="cerrar">
                        🚪 Cerrar sesión
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="contenedor-usuario">
        <main class="contenido-usuario">
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
