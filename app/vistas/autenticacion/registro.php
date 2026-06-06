<?php 
$vista = new Vista();
$errores = $errores ?? [];
$old = $old ?? [];
echo $vista->cargar('layout/encabezado-auth');
?>

            <form class="formulario" method="POST" action="<?php echo URL_RAIZ; ?>autenticacion/procesar-registro" novalidate>
                <h2>Crear Cuenta</h2>

                <div class="grupo-formulario">
                    <label for="nombres">Nombres:</label>
                    <input type="text" id="nombres" name="nombres" required class="campo-entrada" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+" title="Solo letras y espacios" value="<?php echo htmlspecialchars($old['nombres'] ?? '', ENT_QUOTES); ?>">
                    <p class="campo-ayuda">Solo letras y espacios.</p>
                    <?php if (!empty($errores['nombres'])): ?>
                        <p class="campo-error"><?php echo $errores['nombres']; ?></p>
                    <?php endif; ?>
                </div>

                <div class="grupo-formulario">
                    <label for="apellidos">Apellidos:</label>
                    <input type="text" id="apellidos" name="apellidos" required class="campo-entrada" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+" title="Solo letras y espacios" value="<?php echo htmlspecialchars($old['apellidos'] ?? '', ENT_QUOTES); ?>">
                    <p class="campo-ayuda">Solo letras y espacios.</p>
                    <?php if (!empty($errores['apellidos'])): ?>
                        <p class="campo-error"><?php echo $errores['apellidos']; ?></p>
                    <?php endif; ?>
                </div>

                <div class="grupo-formulario">
                    <label for="usuario">Usuario:</label>
                    <input type="text" id="usuario" name="usuario" required class="campo-entrada" minlength="4" pattern="[A-Za-z0-9_]+" title="Solo letras, números y guiones bajos" value="<?php echo htmlspecialchars($old['usuario'] ?? '', ENT_QUOTES); ?>">
                    <p class="campo-ayuda">Mínimo 4 caracteres. Solo letras, números y guiones bajos.</p>
                    <?php if (!empty($errores['usuario'])): ?>
                        <p class="campo-error"><?php echo $errores['usuario']; ?></p>
                    <?php endif; ?>
                </div>

                <div class="grupo-formulario">
                    <label for="correo">Correo:</label>
                    <input type="email" id="correo" name="correo" required class="campo-entrada" value="<?php echo htmlspecialchars($old['correo'] ?? '', ENT_QUOTES); ?>">
                    <?php if (!empty($errores['correo'])): ?>
                        <p class="campo-error"><?php echo $errores['correo']; ?></p>
                    <?php endif; ?>
                </div>

                <div class="grupo-formulario">
                    <label for="telefono">Teléfono (Opcional):</label>
                    <input type="tel" id="telefono" name="telefono" class="campo-entrada" minlength="9" maxlength="9" pattern="[0-9]{9}" title="Debe tener exactamente 9 dígitos" value="<?php echo htmlspecialchars($old['telefono'] ?? '', ENT_QUOTES); ?>">
                    <p class="campo-ayuda">Opcional. Si lo ingresas, debe ser exactamente 9 dígitos.</p>
                    <?php if (!empty($errores['telefono'])): ?>
                        <p class="campo-error"><?php echo $errores['telefono']; ?></p>
                    <?php endif; ?>
                </div>

                <div class="grupo-formulario">
                    <label for="empresa">Empresa (Opcional):</label>
                    <input type="text" id="empresa" name="empresa" class="campo-entrada" maxlength="100" value="<?php echo htmlspecialchars($old['empresa'] ?? '', ENT_QUOTES); ?>">
                    <p class="campo-ayuda">Opcional. Máximo 100 caracteres.</p>
                    <?php if (!empty($errores['empresa'])): ?>
                        <p class="campo-error"><?php echo $errores['empresa']; ?></p>
                    <?php endif; ?>
                </div>

                <div class="grupo-formulario">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required class="campo-entrada" minlength="6" pattern="(?=.*[A-Za-z])(?=.*\d).{6,}" title="Mínimo 6 caracteres, con letras y números">
                    <p class="campo-ayuda">Mínimo 6 caracteres, incluye letras y números.</p>
                    <?php if (!empty($errores['password'])): ?>
                        <p class="campo-error"><?php echo $errores['password']; ?></p>
                    <?php endif; ?>
                </div>

                <div class="grupo-formulario">
                    <label for="confirmar_password">Confirmar Contraseña:</label>
                    <input type="password" id="confirmar_password" name="confirmar_password" required class="campo-entrada" minlength="6" pattern="(?=.*[A-Za-z])(?=.*\d).{6,}" title="Repita la misma contraseña">
                    <?php if (!empty($errores['confirmar_password'])): ?>
                        <p class="campo-error"><?php echo $errores['confirmar_password']; ?></p>
                    <?php endif; ?>
                </div>

                <button type="submit" class="boton boton-primario boton-completo">Registrarse</button>

                <p class="texto-enlace">¿Ya tienes cuenta? <a href="<?php echo URL_RAIZ; ?>autenticacion/iniciar-sesion">Inicia sesión aquí</a></p>
            </form>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.querySelector('form.formulario');
                    if (!form) return;

                    const password = form.querySelector('#password');
                    const confirmar = form.querySelector('#confirmar_password');
                    const errorNodo = document.createElement('p');
                    errorNodo.className = 'campo-error';
                    errorNodo.style.display = 'none';
                    errorNodo.id = 'password-validation-error';
                    confirmar.parentNode.appendChild(errorNodo);

                    form.addEventListener('submit', function(event) {
                        let valido = true;
                        errorNodo.style.display = 'none';
                        errorNodo.textContent = '';

                        const regexContrasena = /^(?=.*[A-Za-z])(?=.*\d).{6,}$/;
                        if (password.value && !regexContrasena.test(password.value)) {
                            errorNodo.textContent = 'La contraseña debe tener al menos 6 caracteres e incluir letras y números.';
                            valido = false;
                        } else if (password.value !== confirmar.value) {
                            errorNodo.textContent = 'Las contraseñas no coinciden.';
                            valido = false;
                        }

                        if (!valido) {
                            errorNodo.style.display = 'block';
                            event.preventDefault();
                        }
                    });
                });
            </script>

<?php 
echo $vista->cargar('layout/pie-auth');
?>
