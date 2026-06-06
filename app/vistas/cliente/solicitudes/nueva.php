<?php 
$vista = new Vista();
echo $vista->cargar('layout/encabezado-cliente');
?>

            <div class="contenedor-pagina">
                <h2>Nueva Solicitud de Proyecto</h2>
                
                <form class="formulario" method="POST" action="<?php echo URL_RAIZ; ?>cliente/nueva-solicitud">
                    <fieldset>
                        <legend>Detalles de la Solicitud</legend>
                        
                        <div class="grupo-formulario">
                            <label for="titulo">Título del Proyecto:</label>
                            <input type="text" id="titulo" name="titulo" required class="campo-entrada">
                        </div>

                        <div class="grupo-formulario">
                            <label for="descripcion">Descripción:</label>
                            <textarea id="descripcion" name="descripcion" required class="campo-entrada campo-area"></textarea>
                        </div>

                        <div class="grupo-formulario">
                            <label for="tipo_sistema">Tipo de Sistema:</label>
                            <select id="tipo_sistema" name="tipo_sistema" required class="campo-entrada">
                                <option value="">Selecciona un tipo</option>
                                <option value="Sitio Web">Sitio Web</option>
                                <option value="Aplicación Web">Aplicación Web</option>
                                <option value="Aplicación Móvil">Aplicación Móvil</option>
                                <option value="Software Escritorio">Software Escritorio</option>
                                <option value="Consultoría">Consultoría</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>

                        <div class="grupo-formulario">
                            <label for="urgencia">Urgencia:</label>
                            <select id="urgencia" name="urgencia" class="campo-entrada">
                                <option value="<?php echo URGENCIA_BAJA; ?>">Baja</option>
                                <option value="<?php echo URGENCIA_MEDIA; ?>" selected>Media</option>
                                <option value="<?php echo URGENCIA_ALTA; ?>">Alta</option>
                                <option value="<?php echo URGENCIA_URGENTE; ?>">Urgente</option>
                            </select>
                        </div>
                    </fieldset>

                    <div class="botones-formulario">
                        <button type="submit" class="boton boton-primario">Enviar Solicitud</button>
                        <a href="<?php echo URL_RAIZ; ?>cliente/inicio" class="boton boton-secundario">Cancelar</a>
                    </div>
                </form>
            </div>

<?php 
echo $vista->cargar('layout/pie-cliente');
?>
