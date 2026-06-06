<?php 
$vista = new Vista();
echo $vista->cargar('layout/encabezado-admin');
?>

            <div class="contenedor-pagina">
                <h2>Gestión de Empleados</h2>
                
                <div class="acciones">
                    <a href="<?php echo URL_RAIZ; ?>admin/agregar-empleado" class="boton boton-primario">Agregar Empleado</a>
                    <a href="<?php echo URL_RAIZ; ?>reportes-lista/reporte-empleados" class="boton boton-info">Generar PDF</a>
                </div>

                <div class="tabla-contenedor">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombres</th>
                            <th>Correo</th>
                            <th>Cargo</th>
                            <th>Especialidad</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($empleados as $empleado): ?>
                            <tr>
                                <td><?php echo $empleado['id_empleado']; ?></td>
                                <td title="<?php echo Seguridad::escaparHTML($empleado['nombres'] . ' ' . $empleado['apellidos']); ?>" class="texto-ellipsis"><?php echo Seguridad::escaparHTML($empleado['nombres'] . ' ' . $empleado['apellidos']); ?></td>
                                <td title="<?php echo Seguridad::escaparHTML($empleado['correo']); ?>" class="texto-ellipsis"><?php echo Seguridad::escaparHTML($empleado['correo']); ?></td>
                                <td title="<?php echo Seguridad::escaparHTML($empleado['cargo'] ?? 'N/A'); ?>" class="texto-ellipsis"><?php echo Seguridad::escaparHTML($empleado['cargo'] ?? 'N/A'); ?></td>
                                <td title="<?php echo Seguridad::escaparHTML($empleado['especialidad'] ?? 'N/A'); ?>" class="texto-ellipsis"><?php echo Seguridad::escaparHTML($empleado['especialidad'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="etiqueta etiqueta-<?php echo strtolower($empleado['estado']); ?>">
                                        <?php echo $empleado['estado']; ?>
                                    </span>
                                </td>
                                <td class="acciones-celda">
                                    <a href="<?php echo URL_RAIZ; ?>admin/ver-empleado?id=<?php echo $empleado['id_empleado']; ?>" class="boton boton-pequeno boton-secundario">Ver</a>
                                    <a href="<?php echo URL_RAIZ; ?>admin/editar-empleado?id=<?php echo $empleado['id_empleado']; ?>" class="boton boton-pequeno boton-info">Editar</a>
                                    <?php if ($empleado['estado'] === ESTADO_ACTIVO): ?>
                                        <form method="post" action="<?php echo URL_RAIZ; ?>admin/cambiar-estado-empleado" style="display:inline-block;">
                                            <input type="hidden" name="id" value="<?php echo $empleado['id_empleado']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo Seguridad::escaparHTML(Sesion::generarTokenCSRF()); ?>">
                                            <button type="submit" class="boton boton-pequeno boton-peligro" onclick="return confirm('¿Desactivar empleado?');">Desactivar</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="post" action="<?php echo URL_RAIZ; ?>admin/cambiar-estado-empleado" style="display:inline-block;">
                                            <input type="hidden" name="id" value="<?php echo $empleado['id_empleado']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo Seguridad::escaparHTML(Sesion::generarTokenCSRF()); ?>">
                                            <button type="submit" class="boton boton-pequeno boton-primario">Activar</button>
                                        </form>
                                    <?php endif; ?>
                                    <a href="<?php echo URL_RAIZ; ?>admin/eliminar-empleado?id=<?php echo $empleado['id_empleado']; ?>" class="boton boton-pequeno boton-peligro" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>

                <?php if (empty($empleados)): ?>
                    <div class="vacio">
                        <p>No hay empleados registrados.</p>
                    </div>
                <?php endif; ?>
            </div>

<?php 
echo $vista->cargar('layout/pie-admin');
?>
