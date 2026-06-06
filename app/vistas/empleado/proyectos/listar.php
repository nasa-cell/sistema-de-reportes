<?php 
$vista = new Vista();
echo $vista->cargar('layout/encabezado-empleado');
?>

            <div class="contenedor-pagina">
                <h2>Mis Proyectos Asignados</h2>

                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Cliente</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Avance</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proyectos as $proyecto): ?>
                            <tr>
                                <td><?php echo $proyecto['id_proyecto']; ?></td>
                                <td><?php echo Seguridad::escaparHTML($proyecto['titulo']); ?></td>
                                <td><?php echo Seguridad::escaparHTML($proyecto['cliente_nombres'] . ' ' . $proyecto['cliente_apellidos']); ?></td>
                                <td>
                                    <span class="etiqueta etiqueta-prioridad-<?php echo strtolower($proyecto['prioridad']); ?>">
                                        <?php echo $proyecto['prioridad']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="etiqueta etiqueta-<?php echo strtolower($proyecto['estado']); ?>">
                                        <?php echo $proyecto['estado']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="barra-progreso">
                                        <div class="progreso" style="width: <?php echo $proyecto['porcentaje_general']; ?>%"></div>
                                    </div>
                                    <span><?php echo $proyecto['porcentaje_general']; ?>%</span>
                                </td>
                                <td class="acciones-celda">
                                    <a href="<?php echo URL_RAIZ; ?>empleado/ver-proyecto?id=<?php echo $proyecto['id_proyecto']; ?>" class="boton boton-pequeno boton-info">Ver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if (empty($proyectos)): ?>
                    <div class="vacio">
                        <p>No tienes proyectos asignados.</p>
                    </div>
                <?php endif; ?>
            </div>

<?php 
echo $vista->cargar('layout/pie-empleado');
?>
