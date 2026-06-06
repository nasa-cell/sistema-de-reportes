<?php 
$vista = new Vista();
echo $vista->cargar('layout/encabezado-empleado');
?>

            <div class="dashboard-empleado">
                <h2>Panel de Control - Empleado</h2>
                
                <div class="estadisticas">
                    <div class="tarjeta-estadistica">
                        <div class="numero"><?php echo $total_proyectos; ?></div>
                        <div class="etiqueta">Proyectos Asignados</div>
                    </div>
                    <div class="tarjeta-estadistica">
                        <div class="numero"><?php echo $proyectos_en_progreso; ?></div>
                        <div class="etiqueta">En Progreso</div>
                    </div>
                    <div class="tarjeta-estadistica">
                        <div class="numero"><?php echo $proyectos_finalizados; ?></div>
                        <div class="etiqueta">Finalizados</div>
                    </div>
                </div>

                <div class="acciones-rapidas">
                    <h3>Acciones Rápidas</h3>
                    <a href="<?php echo URL_RAIZ; ?>empleado/mis-proyectos" class="boton boton-primario">Ver Mis Proyectos</a>
                </div>
            </div>

<?php 
echo $vista->cargar('layout/pie-empleado');
?>
