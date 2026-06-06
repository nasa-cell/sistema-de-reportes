<?php 
$vista = new Vista();
echo $vista->cargar('layout/encabezado-admin');
?>

            <div class="contenedor-pagina">
                <h2>Cotizar Solicitud</h2>
                
                <div class="detalle-solicitud">
                    <h3><?php echo Seguridad::escaparHTML($solicitud['titulo']); ?></h3>
                    <p><strong>Cliente:</strong> <?php echo Seguridad::escaparHTML($solicitud['nombres'] . ' ' . $solicitud['apellidos']); ?></p>
                    <p><strong>Correo:</strong> <?php echo Seguridad::escaparHTML($solicitud['correo']); ?></p>
                    <p><strong>Tipo de Sistema:</strong> <?php echo Seguridad::escaparHTML($solicitud['tipo_sistema']); ?></p>
                    <p><strong>Urgencia:</strong> <?php echo $solicitud['urgencia']; ?></p>
                    <p><strong>Descripción:</strong></p>
                    <div class="descripcion-bloque">
                        <?php echo nl2br(Seguridad::escaparHTML($solicitud['descripcion'])); ?>
                    </div>
                </div>

                <form class="formulario" method="POST" action="<?php echo URL_RAIZ; ?>admin/cotizar-solicitud?id=<?php echo $solicitud['id_solicitud']; ?>">
                    <fieldset>
                        <legend>Cotización</legend>
                        
                        <div class="grupo-formulario">
                            <label for="precio">Precio Propuesto ($):</label>
                            <input type="number" id="precio" name="precio" required class="campo-entrada" step="0.01" min="0">
                        </div>
                    </fieldset>

                    <div class="botones-formulario">
                        <button type="submit" class="boton boton-primario">Enviar Cotización</button>
                        <a href="<?php echo URL_RAIZ; ?>admin/solicitudes" class="boton boton-secundario">Cancelar</a>
                    </div>
                </form>
            </div>

<?php 
echo $vista->cargar('layout/pie-admin');
?>
