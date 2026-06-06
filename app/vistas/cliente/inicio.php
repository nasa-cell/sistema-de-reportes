<?php 
$vista = new Vista();
echo $vista->cargar('layout/encabezado-cliente');
?>

            <div class="dashboard-cliente">
                <h2>Bienvenido, <?php echo Seguridad::escaparHTML($_SESSION['nombres'] . ' ' . ($_SESSION['apellidos'] ?? '')); ?></h2>
                
                <div class="estadisticas">
                    <div class="tarjeta-estadistica">
                        <div class="numero"><?php echo $total_solicitudes; ?></div>
                        <div class="etiqueta">Solicitudes Totales</div>
                    </div>
                    <div class="tarjeta-estadistica">
                        <div class="numero"><?php echo $total_proyectos; ?></div>
                        <div class="etiqueta">Proyectos Activos</div>
                    </div>
                    <div class="tarjeta-estadistica">
                        <div class="numero"><?php echo $solicitudes_pendientes; ?></div>
                        <div class="etiqueta">Solicitudes Pendientes</div>
                    </div>
                </div>

                <div class="acciones-rapidas">
                    <h3>Acciones Rápidas</h3>
                    <a href="<?php echo URL_RAIZ; ?>cliente/nueva-solicitud" class="boton boton-primario">Nueva Solicitud</a>
                    <a href="<?php echo URL_RAIZ; ?>cliente/mis-solicitudes" class="boton boton-secundario">Ver Solicitudes</a>
                    <a href="<?php echo URL_RAIZ; ?>cliente/mis-proyectos" class="boton boton-secundario">Ver Proyectos</a>
                </div>
            </div>

<?php 
echo $vista->cargar('layout/pie-cliente');
?>
