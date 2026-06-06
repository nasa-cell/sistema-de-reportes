<?php 
$vista = new Vista();
echo $vista->cargar('layout/encabezado-admin');
?>

            <div class="contenedor-pagina">
                <h2>Ver Cliente</h2>

                <div class="detalle-contenedor">
                    <div class="detalle-item">
                        <strong>ID:</strong>
                        <span><?php echo $cliente['id_cliente']; ?></span>
                    </div>
                    <div class="detalle-item">
                        <strong>Nombres:</strong>
                        <span><?php echo Seguridad::escaparHTML($cliente['nombres'] . ' ' . $cliente['apellidos']); ?></span>
                    </div>
                    <div class="detalle-item">
                        <strong>Correo:</strong>
                        <span><?php echo Seguridad::escaparHTML($cliente['correo']); ?></span>
                    </div>
                    <div class="detalle-item">
                        <strong>Empresa:</strong>
                        <span><?php echo Seguridad::escaparHTML($cliente['empresa'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="detalle-item">
                        <strong>Teléfono:</strong>
                        <span><?php echo Seguridad::escaparHTML($cliente['telefono'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="detalle-item">
                        <strong>Dirección:</strong>
                        <span><?php echo Seguridad::escaparHTML($cliente['direccion'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="detalle-item">
                        <strong>Estado:</strong>
                        <span><?php echo $cliente['estado']; ?></span>
                    </div>
                </div>

                <div class="botones-formulario">
                    <a href="<?php echo URL_RAIZ; ?>admin/clientes" class="boton boton-secundario">Volver a Clientes</a>
                    <a href="<?php echo URL_RAIZ; ?>admin/editar-cliente?id=<?php echo $cliente['id_cliente']; ?>" class="boton boton-primario">Editar Cliente</a>
                </div>
            </div>

<?php 
echo $vista->cargar('layout/pie-admin');
?>