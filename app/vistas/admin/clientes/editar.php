<?php 
$vista = new Vista();
echo $vista->cargar('layout/encabezado-admin');
?>

            <div class="contenedor-pagina">
                <h2>Editar Cliente</h2>
                
                <?php
                    $errores = Sesion::flash('errores_editar_cliente') ?? [];
                    $old = Sesion::flash('old_editar_cliente') ?? [];
                ?>

                <form class="formulario" method="POST" action="<?php echo URL_RAIZ; ?>admin/editar-cliente?id=<?php echo $cliente['id_cliente']; ?>">
                    <fieldset>
                        <legend>Información del Cliente</legend>
                        
                        <div class="grupo-formulario">
                            <label for="nombres">Nombres:</label>
                            <input type="text" id="nombres" name="nombres" value="<?php echo Seguridad::escaparHTML($old['nombres'] ?? $cliente['nombres']); ?>" class="campo-entrada" required>
                            <?php if(!empty($errores['nombres'])): ?><p class="error-campo"><?php echo Seguridad::escaparHTML($errores['nombres']); ?></p><?php endif; ?>
                        </div>

                        <div class="grupo-formulario">
                            <label for="apellidos">Apellidos:</label>
                            <input type="text" id="apellidos" name="apellidos" value="<?php echo Seguridad::escaparHTML($old['apellidos'] ?? $cliente['apellidos']); ?>" class="campo-entrada" required>
                            <?php if(!empty($errores['apellidos'])): ?><p class="error-campo"><?php echo Seguridad::escaparHTML($errores['apellidos']); ?></p><?php endif; ?>
                        </div>

                        <div class="grupo-formulario">
                            <label for="correo">Correo:</label>
                            <input type="email" id="correo" name="correo" value="<?php echo Seguridad::escaparHTML($old['correo'] ?? $cliente['correo']); ?>" class="campo-entrada" required>
                            <?php if(!empty($errores['correo'])): ?><p class="error-campo"><?php echo Seguridad::escaparHTML($errores['correo']); ?></p><?php endif; ?>
                        </div>

                        <div class="grupo-formulario">
                            <label for="empresa">Empresa:</label>
                            <input type="text" id="empresa" name="empresa" value="<?php echo Seguridad::escaparHTML($old['empresa'] ?? ($cliente['empresa'] ?? '')); ?>" class="campo-entrada">
                            <?php if(!empty($errores['empresa'])): ?><p class="error-campo"><?php echo Seguridad::escaparHTML($errores['empresa']); ?></p><?php endif; ?>
                        </div>

                        <div class="grupo-formulario">
                            <label for="telefono">Teléfono:</label>
                            <input type="tel" id="telefono" name="telefono" value="<?php echo Seguridad::escaparHTML($old['telefono'] ?? ($cliente['telefono'] ?? '')); ?>" class="campo-entrada">
                            <?php if(!empty($errores['telefono'])): ?><p class="error-campo"><?php echo Seguridad::escaparHTML($errores['telefono']); ?></p><?php endif; ?>
                        </div>

                        <div class="grupo-formulario">
                            <label for="direccion">Dirección:</label>
                            <textarea id="direccion" name="direccion" class="campo-entrada campo-area"><?php echo Seguridad::escaparHTML($old['direccion'] ?? ($cliente['direccion'] ?? '')); ?></textarea>
                            <?php if(!empty($errores['direccion'])): ?><p class="error-campo"><?php echo Seguridad::escaparHTML($errores['direccion']); ?></p><?php endif; ?>
                        </div>

                        <div class="grupo-formulario">
                            <label for="estado">Estado:</label>
                            <select id="estado" name="estado" class="campo-entrada">
                                <option value="<?php echo ESTADO_ACTIVO; ?>" <?php echo (($old['estado'] ?? $cliente['estado']) === ESTADO_ACTIVO) ? 'selected' : ''; ?>>Activo</option>
                                <option value="<?php echo ESTADO_INACTIVO; ?>" <?php echo (($old['estado'] ?? $cliente['estado']) === ESTADO_INACTIVO) ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                        </div>
                    </fieldset>

                    <div class="botones-formulario">
                        <button type="submit" class="boton boton-primario">Guardar Cambios</button>
                        <a href="<?php echo URL_RAIZ; ?>admin/clientes" class="boton boton-secundario">Cancelar</a>
                    </div>
                </form>
            </div>

<?php 
echo $vista->cargar('layout/pie-admin');
?>
