<?php 
$vista = new Vista();
echo $vista->cargar('layout/encabezado-admin');
?>

            <div class="contenedor-pagina">
                <h2>Crear Proyecto</h2>
                
                <div class="detalle-solicitud">
                    <h3><?php echo Seguridad::escaparHTML($solicitud['titulo']); ?></h3>
                    <p><strong>Cliente:</strong> <?php echo Seguridad::escaparHTML($solicitud['nombres'] . ' ' . $solicitud['apellidos']); ?></p>
                    <p><strong>Descripción:</strong></p>
                    <div class="descripcion-bloque">
                        <?php echo nl2br(Seguridad::escaparHTML($solicitud['descripcion'])); ?>
                    </div>
                    <p><strong>Precio Propuesto:</strong> $<?php echo number_format($solicitud['precio_propuesto'], 2, '.', ','); ?></p>
                </div>

                <form class="formulario" method="POST" action="<?php echo URL_RAIZ; ?>admin/crear-proyecto?id_solicitud=<?php echo $solicitud['id_solicitud']; ?>">
                    <fieldset>
                        <legend>Datos del Proyecto</legend>
                        
                        <div class="grupo-formulario">
                            <label for="prioridad">Prioridad:</label>
                            <select id="prioridad" name="prioridad" class="campo-entrada">
                                <option value="<?php echo PRIORIDAD_BAJA; ?>">Baja</option>
                                <option value="<?php echo PRIORIDAD_MEDIA; ?>" selected>Media</option>
                                <option value="<?php echo PRIORIDAD_ALTA; ?>">Alta</option>
                                <option value="<?php echo PRIORIDAD_URGENTE; ?>">Urgente</option>
                            </select>
                        </div>

                        <div class="grupo-formulario">
                            <label for="fecha_inicio">Fecha de Inicio:</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="campo-entrada">
                        </div>

                        <div class="grupo-formulario">
                            <label for="fecha_entrega">Fecha de Entrega:</label>
                            <input type="date" id="fecha_entrega" name="fecha_entrega" class="campo-entrada">
                        </div>
                    </fieldset>

                    <div class="botones-formulario">
                        <button type="submit" class="boton boton-primario">Crear Proyecto</button>
                        <a href="<?php echo URL_RAIZ; ?>admin/solicitudes" class="boton boton-secundario">Cancelar</a>
                    </div>
                </form>
            </div>

<?php 
echo $vista->cargar('layout/pie-admin');
?>
