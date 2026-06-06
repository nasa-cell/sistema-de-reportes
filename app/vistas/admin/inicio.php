<?php 
$vista = new Vista();
echo $vista->cargar('layout/encabezado-admin');
?>

            <div class="dashboard-admin">
                <h2>Panel de Administración</h2>
                
                <div class="estadisticas">
                    <div class="tarjeta-estadistica">
                        <div class="numero"><?php echo $total_clientes; ?></div>
                        <div class="etiqueta">Clientes Totales</div>
                    </div>
                    <div class="tarjeta-estadistica">
                        <div class="numero"><?php echo $total_empleados; ?></div>
                        <div class="etiqueta">Empleados</div>
                    </div>
                    <div class="tarjeta-estadistica">
                        <div class="numero"><?php echo $total_proyectos; ?></div>
                        <div class="etiqueta">Proyectos Totales</div>
                    </div>
                    <div class="tarjeta-estadistica">
                        <div class="numero"><?php echo $solicitudes_pendientes; ?></div>
                        <div class="etiqueta">Solicitudes Pendientes</div>
                    </div>
                    <div class="tarjeta-estadistica">
                        <div class="numero"><?php echo $proyectos_en_progreso; ?></div>
                        <div class="etiqueta">Proyectos en Progreso</div>
                    </div>
                </div>

                <div class="acciones-rapidas">
                    <h3>Acciones Rápidas</h3>
                    <a href="<?php echo URL_RAIZ; ?>admin/agregar-cliente" class="boton boton-primario">Agregar Cliente</a>
                    <a href="<?php echo URL_RAIZ; ?>admin/agregar-empleado" class="boton boton-primario">Agregar Empleado</a>
                    <a href="<?php echo URL_RAIZ; ?>admin/solicitudes" class="boton boton-secundario">Ver Solicitudes</a>
                    <a href="<?php echo URL_RAIZ; ?>admin/proyectos" class="boton boton-secundario">Ver Proyectos</a>
                    <a href="<?php echo URL_RAIZ; ?>reportes/reporte-proyectos" class="boton boton-info">Reporte Proyectos</a>
                    <a href="<?php echo URL_RAIZ; ?>reportes-lista/reporte-clientes" class="boton boton-info">Reporte Clientes</a>
                </div>
            </div>

<?php 
echo $vista->cargar('layout/pie-admin');
?>
