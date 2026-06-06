<?php 
$vista = new Vista();
echo $vista->cargar('layout/encabezado-admin');
?>

            <div class="contenedor-pagina">
                <h2>Gestión de Clientes</h2>
                
                <div class="acciones">
                    <a href="<?php echo URL_RAIZ; ?>admin/agregar-cliente" class="boton boton-primario">Agregar Cliente</a>
                    <a href="<?php echo URL_RAIZ; ?>reportes-lista/reporte-clientes" class="boton boton-info">Generar PDF</a>
                </div>

                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombres</th>
                            <th>Correo</th>
                            <th>Empresa</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td><?php echo $cliente['id_cliente']; ?></td>
                                <td><?php echo Seguridad::escaparHTML($cliente['nombres'] . ' ' . $cliente['apellidos']); ?></td>
                                <td><?php echo Seguridad::escaparHTML($cliente['correo']); ?></td>
                                <td><?php echo Seguridad::escaparHTML($cliente['empresa'] ?? 'N/A'); ?></td>
                                <td><?php echo Seguridad::escaparHTML($cliente['telefono'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="etiqueta etiqueta-<?php echo strtolower($cliente['estado']); ?>">
                                        <?php echo $cliente['estado']; ?>
                                    </span>
                                </td>
                                <td class="acciones-celda">
                                    <a href="<?php echo URL_RAIZ; ?>admin/ver-cliente?id=<?php echo $cliente['id_cliente']; ?>" class="boton boton-pequeno boton-secundario">Ver</a>
                                    <a href="<?php echo URL_RAIZ; ?>admin/editar-cliente?id=<?php echo $cliente['id_cliente']; ?>" class="boton boton-pequeno boton-info">Editar</a>
                                    <a href="<?php echo URL_RAIZ; ?>admin/eliminar-cliente?id=<?php echo $cliente['id_cliente']; ?>" class="boton boton-pequeno boton-peligro" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if (empty($clientes)): ?>
                    <div class="vacio">
                        <p>No hay clientes registrados.</p>
                    </div>
                <?php endif; ?>
            </div>

<?php 
echo $vista->cargar('layout/pie-admin');
?>
