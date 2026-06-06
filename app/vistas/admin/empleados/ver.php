<?php 
$vista = new Vista();
echo $vista->cargar('layout/encabezado-admin');
?>

            <div class="contenedor-pagina">
                <h2>Ver Empleado</h2>

                <div class="detalle-contenedor">
                    <div class="detalle-item">
                        <strong>ID:</strong>
                        <span><?php echo $empleado['id_empleado']; ?></span>
                    </div>
                    <div class="detalle-item">
                        <strong>Nombres:</strong>
                        <span><?php echo Seguridad::escaparHTML($empleado['nombres'] . ' ' . $empleado['apellidos']); ?></span>
                    </div>
                    <div class="detalle-item">
                        <strong>Correo:</strong>
                        <span><?php echo Seguridad::escaparHTML($empleado['correo']); ?></span>
                    </div>
                    <div class="detalle-item">
                        <strong>Cargo:</strong>
                        <span><?php echo Seguridad::escaparHTML($empleado['cargo'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="detalle-item">
                        <strong>Especialidad:</strong>
                        <span><?php echo Seguridad::escaparHTML($empleado['especialidad'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="detalle-item">
                        <strong>Estado:</strong>
                        <span><?php echo $empleado['estado']; ?></span>
                    </div>
                </div>

                <div class="botones-formulario">
                    <a href="<?php echo URL_RAIZ; ?>admin/empleados" class="boton boton-secundario">Volver a Empleados</a>
                    <a href="<?php echo URL_RAIZ; ?>admin/editar-empleado?id=<?php echo $empleado['id_empleado']; ?>" class="boton boton-primario">Editar Empleado</a>
                </div>
            </div>

<?php 
echo $vista->cargar('layout/pie-admin');
?>