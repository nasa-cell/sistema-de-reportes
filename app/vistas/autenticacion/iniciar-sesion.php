<?php 
$vista = new Vista();
echo $vista->cargar('layout/encabezado-auth');
?>

            <div class="roles-disponibles">
                <button type="button" class="boton-rol activo" data-rol="CLIENTE">
                    <span class="icono">👤</span>
                    <span>Cliente</span>
                </button>
                <button type="button" class="boton-rol" data-rol="EMPLEADO">
                    <span class="icono">👨‍💼</span>
                    <span>Empleado</span>
                </button>
                <button type="button" class="boton-rol" data-rol="ADMIN">
                    <span class="icono">⚙️</span>
                    <span>Admin</span>
                </button>
            </div>

            <form class="formulario" method="POST" action="<?php echo URL_RAIZ; ?>autenticacion/procesar-login">
                <input type="hidden" name="rol" id="rol" value="CLIENTE">
                <h2>Iniciar Sesión</h2>
                <p class="auth-subtitulo" id="rol-texto">Inicia como Cliente</p>

                <?php
                    // Mostrar aviso si viene por GET (compatibilidad) o por mensaje flash en sesión
                    $mostrarPorGet = isset($_GET['expirada']) && $_GET['expirada'] == 1;
                    $mensajeExpirada = Sesion::obtenerMensaje('advertencia');
                ?>
                <?php if ($mostrarPorGet): ?>
                    <div class="alerta alerta-advertencia">
                        Tu sesión ha expirado. Por favor inicia sesión nuevamente.
                    </div>
                <?php elseif ($mensajeExpirada): ?>
                    <div class="alerta alerta-advertencia">
                        <?php echo $mensajeExpirada; ?>
                    </div>
                <?php endif; ?>

                <div class="grupo-formulario">
                    <label for="usuario">Usuario:</label>
                    <input type="text" id="usuario" name="usuario" required class="campo-entrada">
                </div>

                <div class="grupo-formulario">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required class="campo-entrada">
                </div>

                <button type="submit" class="boton boton-primario boton-completo">Inicia Sesión</button>

                <p class="texto-enlace" id="registro-cliente">¿No tienes cuenta? <a href="<?php echo URL_RAIZ; ?>autenticacion/registro">Regístrate aquí</a></p>
            </form>

            <script>
            document.addEventListener('DOMContentLoaded', function(){
                const botones = document.querySelectorAll('.boton-rol');
                const inputRol = document.getElementById('rol');
                const rolTexto = document.getElementById('rol-texto');
                const registroCliente = document.getElementById('registro-cliente');
                const roles = {
                    CLIENTE: 'Cliente',
                    EMPLEADO: 'Empleado',
                    ADMIN: 'Admin'
                };
                const actualizarTexto = function(rol) {
                    const etiqueta = roles[rol] || 'Cliente';
                    if (rolTexto) rolTexto.textContent = 'Inicia ' + etiqueta;
                };
                const actualizarRegistro = function(rol) {
                    if (registroCliente) {
                        registroCliente.style.display = rol === 'CLIENTE' ? 'block' : 'none';
                    }
                };
                const rolInicial = inputRol ? inputRol.value : 'CLIENTE';
                actualizarTexto(rolInicial);
                actualizarRegistro(rolInicial);

                botones.forEach(b => b.addEventListener('click', function(){
                    botones.forEach(x => x.classList.remove('activo'));
                    this.classList.add('activo');
                    const rolSeleccionado = this.dataset.rol || 'cliente';
                    if (inputRol) inputRol.value = rolSeleccionado;
                    actualizarTexto(rolSeleccionado);
                    actualizarRegistro(rolSeleccionado);
                }));
            });
            </script>

<?php 
echo $vista->cargar('layout/pie-auth');
?>
