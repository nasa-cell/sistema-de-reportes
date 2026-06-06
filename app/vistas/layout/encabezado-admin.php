<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo NOMBRE_APP; ?></title>
    <link rel="stylesheet" href="<?php echo URL_RAIZ; ?>public/css/estilos.css?v=<?php echo filemtime(__DIR__ . '/../../../public/css/estilos.css'); ?>">
</head>
<body>
    <!-- Navbar Moderno Admin -->
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

            <!-- Menú Admin -->
            <ul class="navbar-menu" id="navbar-menu">
                <li class="navbar-menu-item">
                    <a href="<?php echo URL_RAIZ; ?>admin/inicio" 
                       class="navbar-menu-link<?php echo (strpos(trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'), 'admin/inicio') === 0) ? ' active' : ''; ?>" 
                       data-section="inicio">
                        📊 Inicio
                    </a>
                </li>
                <li class="navbar-menu-item">
                    <a href="<?php echo URL_RAIZ; ?>admin/clientes" 
                       class="navbar-menu-link<?php echo (strpos(trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'), 'admin/clientes') === 0) ? ' active' : ''; ?>" 
                       data-section="clientes">
                        👥 Clientes
                    </a>
                </li>
                <li class="navbar-menu-item">
                    <a href="<?php echo URL_RAIZ; ?>admin/empleados" 
                       class="navbar-menu-link<?php echo (strpos(trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'), 'admin/empleados') === 0) ? ' active' : ''; ?>" 
                       data-section="empleados">
                        👔 Empleados
                    </a>
                </li>
                <li class="navbar-menu-item">
                    <a href="<?php echo URL_RAIZ; ?>admin/solicitudes" 
                       class="navbar-menu-link<?php echo (strpos(trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'), 'admin/solicitudes') === 0) ? ' active' : ''; ?>" 
                       data-section="solicitudes">
                        📋 Solicitudes
                    </a>
                </li>
                <li class="navbar-menu-item">
                    <a href="<?php echo URL_RAIZ; ?>admin/proyectos" 
                       class="navbar-menu-link<?php echo (strpos(trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'), 'admin/proyectos') === 0) ? ' active' : ''; ?>" 
                       data-section="proyectos">
                        🚀 Proyectos
                    </a>
                </li>
                <li class="navbar-menu-item">
                    <a href="<?php echo URL_RAIZ; ?>reportes/reporte-proyectos" 
                       class="navbar-menu-link<?php echo (strpos(trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'), 'reportes') === 0) ? ' active' : ''; ?>" 
                       data-section="reportes">
                        📄 Reportes
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

    <div class="contenedor-admin sin-sidebar">
        <main class="contenido-admin">
            <header class="encabezado-admin">
                <h1><?php echo $_SESSION['nombres']; ?> (Admin)</h1>
                <span class="fecha"><?php echo date('d/m/Y H:i'); ?></span>
            </header>

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

            <?php if (Sesion::hayMensaje('eliminado')): ?>
                <div class="alerta alerta-eliminado">
                    <?php echo Sesion::obtenerMensaje('eliminado'); ?>
                </div>
            <?php endif; ?>
