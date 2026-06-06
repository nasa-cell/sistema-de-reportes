<?php 
$vista = new Vista();
$errores = $errores ?? [];
$old = $old ?? [];
echo $vista->cargar('layout/encabezado-admin');
?>

            <div class="contenedor-pagina">
                <h2>Agregar Cliente</h2>
                
                <form class="formulario" method="POST" action="<?php echo URL_RAIZ; ?>admin/agregar-cliente">
                    <fieldset>
                        <legend>Datos del Usuario</legend>
                        
                        <div class="grupo-formulario">
                            <label for="nombres">Nombres:</label>
                            <input type="text" id="nombres" name="nombres" required class="campo-entrada" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+" title="Solo letras y espacios" value="<?php echo htmlspecialchars($old['nombres'] ?? '', ENT_QUOTES); ?>">
                            <p class="campo-ayuda">Solo letras y espacios.</p>
                            <?php if (!empty($errores['nombres'])): ?><p class="campo-error"><?php echo $errores['nombres']; ?></p><?php endif; ?>
                        </div>

                        <div class="grupo-formulario">
                            <label for="apellidos">Apellidos:</label>
                            <input type="text" id="apellidos" name="apellidos" required class="campo-entrada" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+" title="Solo letras y espacios" value="<?php echo htmlspecialchars($old['apellidos'] ?? '', ENT_QUOTES); ?>">
                            <p class="campo-ayuda">Solo letras y espacios.</p>
                            <?php if (!empty($errores['apellidos'])): ?><p class="campo-error"><?php echo $errores['apellidos']; ?></p><?php endif; ?>
                        </div>

                        <div class="grupo-formulario">
                            <label for="usuario">Usuario:</label>
                            <input type="text" id="usuario" name="usuario" required class="campo-entrada" minlength="4" pattern="[A-Za-z0-9_]+" title="Solo letras, números y guiones bajos" value="<?php echo htmlspecialchars($old['usuario'] ?? '', ENT_QUOTES); ?>">
                            <p class="campo-ayuda">Mínimo 4 caracteres. Solo letras, números y guiones bajos.</p>
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
                        <legend>Datos del Cliente</legend>
                        
                        <div class="grupo-formulario">
                            <label for="empresa">Empresa:</label>
                            <input type="text" id="empresa" name="empresa" required class="campo-entrada" maxlength="100" value="<?php echo htmlspecialchars($old['empresa'] ?? '', ENT_QUOTES); ?>">
                            <p class="campo-ayuda">Empresa requerida. Máximo 100 caracteres.</p>
                            <?php if (!empty($errores['empresa'])): ?><p class="campo-error"><?php echo $errores['empresa']; ?></p><?php endif; ?>
                        </div>

                        <div class="grupo-formulario">
                            <label for="telefono">Teléfono:</label>
                            <input type="tel" id="telefono" name="telefono" required class="campo-entrada" pattern="[0-9+\s()\-]+" title="Solo números, espacios, +, paréntesis y guiones" value="<?php echo htmlspecialchars($old['telefono'] ?? '', ENT_QUOTES); ?>">
                            <p class="campo-ayuda">Teléfono requerido. Solo números y símbolos válidos.</p>
                            <?php if (!empty($errores['telefono'])): ?><p class="campo-error"><?php echo $errores['telefono']; ?></p><?php endif; ?>
                        </div>

                        <div class="grupo-formulario">
                            <label for="direccion">Dirección:</label>
                            <textarea id="direccion" name="direccion" required class="campo-entrada campo-area" maxlength="200"><?php echo htmlspecialchars($old['direccion'] ?? '', ENT_QUOTES); ?></textarea>
                            <p class="campo-ayuda">Dirección requerida. Máximo 200 caracteres.</p>
                            <?php if (!empty($errores['direccion'])): ?><p class="campo-error"><?php echo $errores['direccion']; ?></p><?php endif; ?>
                        </div>
                    </fieldset>

                    <div class="botones-formulario">
                        <button type="submit" class="boton boton-primario">Guardar Cliente</button>
                        <a href="<?php echo URL_RAIZ; ?>admin/clientes" class="boton boton-secundario">Cancelar</a>
                    </div>
                </form>
            </div>

<?php 
echo $vista->cargar('layout/pie-admin');
?>
