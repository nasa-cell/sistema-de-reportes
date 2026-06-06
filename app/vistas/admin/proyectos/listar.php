<?php 
$vista = new Vista();
echo $vista->cargar('layout/encabezado-admin');
?>

            <div class="contenedor-pagina">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>Proyectos</h2>
                    <a href="<?php echo URL_RAIZ; ?>reportes/reporteProyectos" class="boton boton-primario" target="_blank">
                        📥 Descargar PDF
                    </a>
                </div>

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
                                    <a href="<?php echo URL_RAIZ; ?>admin/ver-proyecto?id=<?php echo $proyecto['id_proyecto']; ?>" class="boton boton-pequeno boton-info">Ver</a>
                                    <?php if ($proyecto['estado'] === ESTADO_PROYECTO_EN_ESPERA): ?>
                                        <a href="<?php echo URL_RAIZ; ?>admin/asignar-empleados?id_proyecto=<?php echo $proyecto['id_proyecto']; ?>" class="boton boton-pequeno boton-primario">Asignar</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if (empty($proyectos)): ?>
                    <div class="vacio">
                        <p>No hay proyectos.</p>
                    </div>
                <?php endif; ?>
            </div>

<?php 
echo $vista->cargar('layout/pie-admin');
?>
