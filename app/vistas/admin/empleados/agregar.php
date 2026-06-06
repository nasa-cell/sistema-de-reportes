<?php 
$vista = new Vista();
$errores = $errores ?? [];
$old = $old ?? [];
echo $vista->cargar('layout/encabezado-admin');
?>

            <div class="contenedor-pagina">
                <h2>Agregar Empleado</h2>

                <form class="formulario" method="POST" action="<?php echo URL_RAIZ; ?>admin/agregar-empleado">
                    <?php if (!empty($errores)): ?>
                        <div class="alerta alerta-error">
                            <ul>
                                <?php foreach ($errores as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <fieldset>
                        <legend>Datos del Usuario</legend>

                        <div class="grupo-formulario">
                            <label for="nombres">Nombres:</label>
                            <input type="text" id="nombres" name="nombres" required class="campo-entrada" value="<?php echo htmlspecialchars($old['nombres'] ?? '', ENT_QUOTES); ?>" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]{2,}" title="Solo letras y espacios, mínimo 2 caracteres">
                            <?php if (!empty($errores['nombres'])): ?><p class="campo-error"><?php echo $errores['nombres']; ?></p><?php endif; ?>
                        </div>

                        <div class="grupo-formulario">
                            <label for="apellidos">Apellidos:</label>
                            <input type="text" id="apellidos" name="apellidos" required class="campo-entrada" value="<?php echo htmlspecialchars($old['apellidos'] ?? '', ENT_QUOTES); ?>" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]{2,}" title="Solo letras y espacios, mínimo 2 caracteres">
                            <?php if (!empty($errores['apellidos'])): ?><p class="campo-error"><?php echo $errores['apellidos']; ?></p><?php endif; ?>
                        </div>

                        <div class="grupo-formulario">
                            <label for="usuario">Usuario:</label>
                            <input type="text" id="usuario" name="usuario" required class="campo-entrada" minlength="4" maxlength="20" value="<?php echo htmlspecialchars($old['usuario'] ?? '', ENT_QUOTES); ?>" pattern="[A-Za-z0-9_]{4,20}" title="Usuario de 4 a 20 caracteres, solo letras, números y guiones bajos.">
                            <p class="campo-ayuda">Usa entre 4 y 20 caracteres. Sin espacios.</p>
                            <?php if (!empty($errores['usuario'])): ?><p class="campo-error"><?php echo $errores['usuario']; ?></p><?php endif; ?>
                        </div>

                        <div class="grupo-formulario">
                            <label for="correo">Correo:</label>
                            <input type="email" id="correo" name="correo" required class="campo-entrada" value="<?php echo htmlspecialchars($old['correo'] ?? '', ENT_QUOTES); ?>">
                            <?php if (!empty($errores['correo'])): ?><p class="campo-error"><?php echo $errores['correo']; ?></p><?php endif; ?>
                        </div>

                        <div class="grupo-formulario">
                            <label for="password">Contraseña:</label>
                            <input type="password" id="password" name="password" required class="campo-entrada" minlength="6" pattern="(?=.*[A-Za-z])(?=.*\d).{6,}" title="Mínimo 6 caracteres, con letras y números">
                            <p class="campo-ayuda">Mínimo 6 caracteres, incluye letras y números.</p>
                            <?php if (!empty($errores['password'])): ?><p class="campo-error"><?php echo $errores['password']; ?></p><?php endif; ?>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Datos del Empleado</legend>

                        <div class="grupo-formulario">
                            <label for="cargo">Cargo:</label>
                            <input type="text" id="cargo" name="cargo" class="campo-entrada" value="<?php echo htmlspecialchars($old['cargo'] ?? '', ENT_QUOTES); ?>" maxlength="50" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+" title="Solo letras y espacios">
                            <?php if (!empty($errores['cargo'])): ?><p class="campo-error"><?php echo $errores['cargo']; ?></p><?php endif; ?>
                        </div>

                        <div class="grupo-formulario">
                            <label for="especialidad">Especialidad:</label>
                            <input type="text" id="especialidad" name="especialidad" class="campo-entrada" value="<?php echo htmlspecialchars($old['especialidad'] ?? '', ENT_QUOTES); ?>" maxlength="50" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+" title="Solo letras y espacios">
                            <?php if (!empty($errores['especialidad'])): ?><p class="campo-error"><?php echo $errores['especialidad']; ?></p><?php endif; ?>
                        </div>
                    </fieldset>

                    <div class="botones-formulario">
                        <button type="submit" class="boton boton-primario">Guardar Empleado</button>
                        <a href="<?php echo URL_RAIZ; ?>admin/empleados" class="boton boton-secundario">Cancelar</a>
                    </div>
                </form>
            </div>

<?php 
echo $vista->cargar('layout/pie-admin');
?>
