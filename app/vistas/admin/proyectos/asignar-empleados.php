<?php 
$vista = new Vista();
echo $vista->cargar('layout/encabezado-admin');
?>

            <div class="contenedor-pagina">
                <h2>Asignar Empleados a Proyecto</h2>

                <div class="detalle-proyecto">
                    <h3><?php echo Seguridad::escaparHTML($proyecto['titulo']); ?></h3>
                    <p><strong>Cliente:</strong> <?php echo Seguridad::escaparHTML($proyecto['cliente_nombres'] . ' ' . $proyecto['cliente_apellidos']); ?></p>
                    <p><strong>Prioridad:</strong> <?php echo $proyecto['prioridad']; ?></p>
                    <p><strong>Fecha de Entrega:</strong> <?php echo $proyecto['fecha_entrega'] ?? 'No especificada'; ?></p>
                </div>

                <?php if (empty($maxAsignados)): ?>
                <form class="formulario" method="POST" action="<?php echo URL_RAIZ; ?>admin/asignar-empleados?id_proyecto=<?php echo $proyecto['id_proyecto']; ?>">
                    <fieldset>
                        <legend>Asignar Empleado</legend>
                        
                        <div class="grupo-formulario">
                            <label for="id_empleado">Empleado:</label>
                            <select id="id_empleado" name="id_empleado" required class="campo-entrada">
                                <option value="">Selecciona un empleado</option>
                                <?php
                                    // Construir lista de empleados ya asignados para deshabilitarlos
                                    $empleadosAsignados = [];
                                    if (!empty($asignaciones)) {
                                        foreach ($asignaciones as $a) {
                                            $empleadosAsignados[] = $a['id_empleado'];
                                        }
                                    }

                                    foreach ($empleados as $empleado):
                                        $disabled = in_array($empleado['id_empleado'], $empleadosAsignados) ? 'disabled' : '';
                                ?>
                                    <option value="<?php echo $empleado['id_empleado']; ?>" <?php echo $disabled; ?>>
                                        <?php echo Seguridad::escaparHTML($empleado['nombres'] . ' ' . $empleado['apellidos'] . ' - ' . ($empleado['cargo'] ?? 'Sin cargo')); ?>
                                        <?php if ($disabled): ?> (ya asignado)<?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="grupo-formulario">
                            <label for="rol_en_proyecto">Rol en el Proyecto:</label>
                            <input type="text" id="rol_en_proyecto" name="rol_en_proyecto" class="campo-entrada" placeholder="ej: Desarrollador, Diseñador, etc.">
                        </div>
                    </fieldset>

                    <button type="submit" class="boton boton-primario">Asignar Empleado</button>
                </form>
                <?php else: ?>
                    <div class="mensaje advertencia" style="margin: 15px 0; padding: 12px; background:#fff4e5; border:1px solid #ffd8a8; color:#8a6d00;">
                        Límite alcanzado: este proyecto ya tiene 3 empleados asignados. Remueve una asignación para añadir otro.
                    </div>
                <?php endif; ?>

                <h3>Empleados Asignados</h3>
                <?php if (!empty($asignaciones)): ?>
                    <table class="tabla-datos">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Cargo</th>
                                <th>Rol en Proyecto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($asignaciones as $asignacion): ?>
                                <tr>
                                    <td><?php echo Seguridad::escaparHTML($asignacion['nombres'] . ' ' . $asignacion['apellidos']); ?></td>
                                    <td><?php echo Seguridad::escaparHTML($asignacion['cargo'] ?? 'N/A'); ?></td>
                                    <td><?php echo Seguridad::escaparHTML($asignacion['rol_en_proyecto'] ?? 'N/A'); ?></td>
                                    <td>
                                        <a href="<?php echo URL_RAIZ; ?>admin/remover-asignacion?id=<?php echo $asignacion['id_asignacion']; ?>&id_proyecto=<?php echo $proyecto['id_proyecto']; ?>" class="boton boton-pequeno boton-peligro" onclick="return confirm('¿Estás seguro?');">Remover</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No hay empleados asignados aún.</p>
                <?php endif; ?>

                <a href="<?php echo URL_RAIZ; ?>admin/proyectos" class="boton boton-secundario">Volver a Proyectos</a>
            </div>

<?php 
echo $vista->cargar('layout/pie-admin');
?>
