<?php 
$vista = new Vista();
echo $vista->cargar('layout/encabezado-admin');
?>

            <div class="contenedor-pagina">
                <h2>Editar Empleado</h2>
                
                <?php
                    $errores = Sesion::flash('errores_editar_empleado') ?? [];
                    $old = Sesion::flash('old_editar_empleado') ?? [];
                ?>

                <form class="formulario" method="POST" action="<?php echo URL_RAIZ; ?>admin/editar-empleado?id=<?php echo $empleado['id_empleado']; ?>">
                    <fieldset>
                        <legend>Información del Empleado</legend>
                        
                        <div class="grupo-formulario">
                            <label for="nombres">Nombres:</label>
                            <input type="text" id="nombres" name="nombres" value="<?php echo Seguridad::escaparHTML($old['nombres'] ?? $empleado['nombres']); ?>" class="campo-entrada" required>
                            <?php if(!empty($errores['nombres'])): ?><p class="error-campo"><?php echo Seguridad::escaparHTML($errores['nombres']); ?></p><?php endif; ?>
                        </div>

                        <div class="grupo-formulario">
                            <label for="apellidos">Apellidos:</label>
                            <input type="text" id="apellidos" name="apellidos" value="<?php echo Seguridad::escaparHTML($old['apellidos'] ?? $empleado['apellidos']); ?>" class="campo-entrada" required>
                            <?php if(!empty($errores['apellidos'])): ?><p class="error-campo"><?php echo Seguridad::escaparHTML($errores['apellidos']); ?></p><?php endif; ?>
                        </div>

                        <div class="grupo-formulario">
                            <label for="correo">Correo:</label>
                            <input type="email" id="correo" name="correo" value="<?php echo Seguridad::escaparHTML($old['correo'] ?? $empleado['correo']); ?>" class="campo-entrada" required>
                            <?php if(!empty($errores['correo'])): ?><p class="error-campo"><?php echo Seguridad::escaparHTML($errores['correo']); ?></p><?php endif; ?>
                        </div>

                        <div class="grupo-formulario">
                            <label for="cargo">Cargo:</label>
                            <input type="text" id="cargo" name="cargo" value="<?php echo Seguridad::escaparHTML($old['cargo'] ?? ($empleado['cargo'] ?? '')); ?>" class="campo-entrada">
                            <?php if(!empty($errores['cargo'])): ?><p class="error-campo"><?php echo Seguridad::escaparHTML($errores['cargo']); ?></p><?php endif; ?>
                        </div>

                        <div class="grupo-formulario">
                            <label for="especialidad">Especialidad:</label>
                            <input type="text" id="especialidad" name="especialidad" value="<?php echo Seguridad::escaparHTML($old['especialidad'] ?? ($empleado['especialidad'] ?? '')); ?>" class="campo-entrada">
                            <?php if(!empty($errores['especialidad'])): ?><p class="error-campo"><?php echo Seguridad::escaparHTML($errores['especialidad']); ?></p><?php endif; ?>
                        </div>

                        <div class="grupo-formulario">
                            <label for="estado">Estado:</label>
                            <select id="estado" name="estado" class="campo-entrada">
                                <option value="<?php echo ESTADO_ACTIVO; ?>" <?php echo (($old['estado'] ?? $empleado['estado']) === ESTADO_ACTIVO) ? 'selected' : ''; ?>>Activo</option>
                                <option value="<?php echo ESTADO_INACTIVO; ?>" <?php echo (($old['estado'] ?? $empleado['estado']) === ESTADO_INACTIVO) ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                        </div>
                    </fieldset>

                    <div class="botones-formulario">
                        <button type="submit" class="boton boton-primario">Guardar Cambios</button>
                        <a href="<?php echo URL_RAIZ; ?>admin/empleados" class="boton boton-secundario">Cancelar</a>
                    </div>
                </form>
            </div>

<?php 
echo $vista->cargar('layout/pie-admin');
?>
