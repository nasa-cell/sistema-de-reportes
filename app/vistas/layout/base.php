<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo NOMBRE_APP; ?></title>
    <link rel="stylesheet" href="<?php echo URL_RAIZ; ?>public/css/estilos.css">
</head>
<body>
    <div class="contenedor">
        <header class="encabezado">
            <div class="encabezado-contenido">
                <h1><?php echo NOMBRE_APP; ?></h1>
                <?php if (Sesion::existe('id_usuario')): ?>
                    <nav class="nav-usuario">
                        <span class="usuario-actual">
                            <?php echo $_SESSION['nombres'] . ' ' . $_SESSION['apellidos']; ?> 
                            (<?php echo $_SESSION['rol']; ?>)
                        </span>
                        <a href="<?php echo URL_RAIZ; ?>autenticacion/cerrar-sesion" class="btn-logout">Cerrar sesión</a>
                    </nav>
                <?php endif; ?>
            </div>
        </header>

        <main class="principal">
            <?php if (Sesion::hayMensaje('exito')): ?>
                <div class="alerta alerta-exito">
                    <strong>¡Éxito!</strong> <?php echo Sesion::obtenerMensaje('exito'); ?>
                </div>
            <?php endif; ?>

            <?php if (Sesion::hayMensaje('error')): ?>
                <div class="alerta alerta-error">
                    <strong>¡Error!</strong> <?php echo Sesion::obtenerMensaje('error'); ?>
                </div>
            <?php endif; ?>

            <?php if (Sesion::hayMensaje('advertencia')): ?>
                <div class="alerta alerta-advertencia">
                    <strong>¡Advertencia!</strong> <?php echo Sesion::obtenerMensaje('advertencia'); ?>
                </div>
            <?php endif; ?>
        </main>

        <footer class="pie">
            <p>&copy; <?php echo date('Y'); ?> <?php echo NOMBRE_APP; ?>. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>
