<?php 
$vista = new Vista();
echo $vista->cargar('layout/encabezado-cliente');
?>

            <div class="contenedor-pagina">
                <h2>Mis Solicitudes</h2>

                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Tipo de Sistema</th>
                            <th>Urgencia</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitudes as $solicitud): ?>
                            <tr>
                                <td><?php echo $solicitud['id_solicitud']; ?></td>
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
                                <td><?php echo date('d/m/Y', strtotime($solicitud['fecha_solicitud'])); ?></td>
                                <td class="acciones-celda">
                                    <a href="<?php echo URL_RAIZ; ?>cliente/ver-solicitud?id=<?php echo $solicitud['id_solicitud']; ?>" class="boton boton-pequeno boton-info">Ver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if (empty($solicitudes)): ?>
                    <div class="vacio">
                        <p>No tienes solicitudes. <a href="<?php echo URL_RAIZ; ?>cliente/nueva-solicitud">Crea una nueva</a></p>
                    </div>
                <?php endif; ?>
            </div>

<?php 
echo $vista->cargar('layout/pie-cliente');
?>
