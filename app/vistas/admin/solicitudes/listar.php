<?php 
$vista = new Vista();
echo $vista->cargar('layout/encabezado-admin');
?>

            <div class="contenedor-pagina">
                <h2>Solicitudes de Proyectos</h2>

                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Título</th>
                            <th>Tipo de Sistema</th>
                            <th>Urgencia</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitudes as $solicitud): ?>
                            <tr>
                                <td><?php echo $solicitud['id_solicitud']; ?></td>
                                <td><?php echo Seguridad::escaparHTML($solicitud['nombres'] . ' ' . $solicitud['apellidos']); ?></td>
                                <td><?php echo Seguridad::escaparHTML($solicitud['titulo']); ?></td>
                                <td><?php echo Seguridad::escaparHTML($solicitud['tipo_sistema']); ?></td>
                                <td>
                                    <span class="etiqueta etiqueta-urgencia-<?php echo strtolower($solicitud['urgencia']); ?>">
                                        <?php echo $solicitud['urgencia']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="etiqueta etiqueta-<?php echo strtolower($solicitud['estado']); ?>">
                                        <?php echo $solicitud['estado']; ?>
                                    </span>
                                </td>
                                <td class="acciones-celda">
                                    <?php if ($solicitud['estado'] === ESTADO_SOLICITUD_PENDIENTE): ?>
                                        <a href="<?php echo URL_RAIZ; ?>admin/cotizar-solicitud?id=<?php echo $solicitud['id_solicitud']; ?>" class="boton boton-pequeno boton-info">Cotizar</a>
                                    <?php elseif ($solicitud['estado'] === ESTADO_SOLICITUD_ACEPTADO): ?>
                                        <?php if (empty($solicitud['id_proyecto'])): ?>
                                            <a href="<?php echo URL_RAIZ; ?>admin/crear-proyecto?id_solicitud=<?php echo $solicitud['id_solicitud']; ?>" class="boton boton-pequeno boton-primario">Crear Proyecto</a>
                                        <?php else: ?>
                                            <a href="<?php echo URL_RAIZ; ?>admin/ver-proyecto?id=<?php echo $solicitud['id_proyecto']; ?>" class="boton boton-pequeno boton-secundario">Ver Proyecto</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if (empty($solicitudes)): ?>
                    <div class="vacio">
                        <p>No hay solicitudes de proyectos.</p>
                    </div>
                <?php endif; ?>
            </div>

<?php 
echo $vista->cargar('layout/pie-admin');
?>
