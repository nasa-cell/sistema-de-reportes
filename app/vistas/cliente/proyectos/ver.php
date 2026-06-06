<?php 
$vista = new Vista();
echo $vista->cargar('layout/encabezado-cliente');
?>

            <div class="contenedor-pagina">
                <h2>Detalles del Proyecto</h2>

                <div class="detalle-proyecto">
                    <h3><?php echo Seguridad::escaparHTML($proyecto['titulo']); ?></h3>
                    
                    <div class="grid-info">
                        <div>
                            <p><strong>Estado:</strong> <?php echo $proyecto['estado']; ?></p>
                            <p><strong>Prioridad:</strong> <?php echo $proyecto['prioridad']; ?></p>
                            <p><strong>Precio:</strong> $<?php echo number_format($proyecto['precio'], 2, '.', ','); ?></p>
                        </div>
                        <div>
                            <p><strong>Fecha Inicio:</strong> <?php echo $proyecto['fecha_inicio'] ?? 'No especificada'; ?></p>
                            <p><strong>Fecha Entrega:</strong> <?php echo $proyecto['fecha_entrega'] ?? 'No especificada'; ?></p>
                        </div>
                    </div>

                    <p><strong>Avance General:</strong></p>
                    <div class="barra-progreso barra-grande">
                        <div class="progreso" style="width: <?php echo $proyecto['porcentaje_general']; ?>%"></div>
                    </div>
                    <p><?php echo $proyecto['porcentaje_general']; ?>% Completado</p>

                    <p><strong>Descripción:</strong></p>
                    <div class="descripcion-bloque">
                        <?php echo nl2br(Seguridad::escaparHTML($proyecto['descripcion'])); ?>
                    </div>
                </div>

                <h3>Empleados Asignados</h3>
                <?php if (!empty($asignaciones)): ?>
                    <table class="tabla-datos">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Cargo</th>
                                <th>Rol en Proyecto</th>
                                <th>Correo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($asignaciones as $asignacion): ?>
                                <tr>
                                    <td><?php echo Seguridad::escaparHTML($asignacion['nombres'] . ' ' . $asignacion['apellidos']); ?></td>
                                    <td><?php echo Seguridad::escaparHTML($asignacion['cargo'] ?? 'N/A'); ?></td>
                                    <td><?php echo Seguridad::escaparHTML($asignacion['rol_en_proyecto'] ?? 'N/A'); ?></td>
                                    <td><?php echo Seguridad::escaparHTML($asignacion['correo']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No hay empleados asignados aún.</p>
                <?php endif; ?>

                <h3>Historial de Avances</h3>
                <?php if (!empty($avances)): ?>
                    <table class="tabla-datos">
                        <thead>
                            <tr>
                                <th>Empleado</th>
                                <th>Porcentaje</th>
                                <th>Observación</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($avances as $avance): ?>
                                <tr>
                                    <td><?php echo Seguridad::escaparHTML($avance['nombres'] . ' ' . $avance['apellidos']); ?></td>
                                    <td><?php echo $avance['porcentaje']; ?>%</td>
                                    <td><?php echo Seguridad::escaparHTML($avance['observacion'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($avance['fecha_actualizacion'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No hay avances registrados aún.</p>
                <?php endif; ?>

                <div class="botones-formulario">
                    <a href="<?php echo URL_RAIZ; ?>cliente/mis-proyectos" class="boton boton-secundario">Volver a Proyectos</a>
                    <a href="<?php echo URL_RAIZ; ?>reportes/reporte-proyecto?id=<?php echo $proyecto['id_proyecto']; ?>" class="boton boton-info">Descargar Reporte PDF</a>
                </div>
            </div>

<?php 
echo $vista->cargar('layout/pie-cliente');
?>
