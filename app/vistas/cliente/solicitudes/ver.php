<?php 
$vista = new Vista();
echo $vista->cargar('layout/encabezado-cliente');
?>

            <div class="contenedor-pagina">
                <h2>Detalles de la Solicitud</h2>

                <div class="detalle-solicitud">
                    <h3><?php echo Seguridad::escaparHTML($solicitud['titulo']); ?></h3>
                    <p><strong>Estado:</strong> 
                        <span class="etiqueta etiqueta-<?php echo strtolower($solicitud['estado']); ?>">
                            <?php echo $solicitud['estado']; ?>
                        </span>
                    </p>
                    <p><strong>Tipo de Sistema:</strong> <?php echo Seguridad::escaparHTML($solicitud['tipo_sistema']); ?></p>
                    <p><strong>Urgencia:</strong> <?php echo $solicitud['urgencia']; ?></p>
                    <p><strong>Fecha Solicitud:</strong> <?php echo date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])); ?></p>
                    
                    <p><strong>Descripción:</strong></p>
                    <div class="descripcion-bloque">
                        <?php echo nl2br(Seguridad::escaparHTML($solicitud['descripcion'])); ?>
                    </div>

                    <?php if ($solicitud['estado'] === ESTADO_SOLICITUD_COTIZADO): ?>
                        <div class="alerta alerta-info">
                            <strong>Cotización Disponible:</strong> El admin ha revisado tu solicitud.
                        </div>
                        <p><strong>Precio Propuesto:</strong> $<?php echo number_format($solicitud['precio_propuesto'], 2, '.', ','); ?></p>
                    <?php elseif ($solicitud['estado'] === ESTADO_SOLICITUD_RECHAZADO): ?>
                        <div class="alerta alerta-error">
                            <strong>Solicitud Rechazada</strong>
                        </div>
                        <p><strong>Motivo del Rechazo:</strong></p>
                        <div class="descripcion-bloque">
                            <?php echo nl2br(Seguridad::escaparHTML($solicitud['motivo_rechazo'])); ?>
                        </div>
                    <?php elseif ($solicitud['estado'] === ESTADO_SOLICITUD_ACEPTADO): ?>
                        <div class="alerta alerta-exito">
                            <strong>¡Solicitud Aceptada!</strong> El admin está preparando tu proyecto.
                        </div>
                    <?php endif; ?>

                    <?php if ($solicitud['estado'] === ESTADO_SOLICITUD_COTIZADO): ?>
                        <form method="POST" action="<?php echo URL_RAIZ; ?>cliente/ver-solicitud?id=<?php echo $solicitud['id_solicitud']; ?>">
                            <div class="grupo-respuesta">
                                <h4>¿Qué deseas hacer?</h4>
                                
                                <button type="submit" name="accion" value="aceptar" class="boton boton-primario">
                                    ✓ Aceptar Propuesta
                                </button>

                                <button type="button" onclick="mostrarFormRechazo();" class="boton boton-peligro">
                                    ✗ Rechazar Propuesta
                                </button>
                            </div>

                            <div id="form-rechazo" style="display:none; margin-top: 20px; padding: 20px; background: #f9f9f9; border: 1px solid #ddd;">
                                <h4>¿Por qué rechazas la propuesta?</h4>
                                <textarea name="motivo_rechazo" class="campo-entrada campo-area" placeholder="Explica el motivo del rechazo..." disabled></textarea>
                                <div style="margin-top: 10px;">
                                    <button type="submit" name="accion" value="rechazar" class="boton boton-peligro">Confirmar Rechazo</button>
                                    <button type="button" onclick="ocultarFormRechazo();" class="boton boton-secundario">Cancelar</button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>

                <a href="<?php echo URL_RAIZ; ?>cliente/mis-solicitudes" class="boton boton-secundario">Volver a Solicitudes</a>
            </div>

<?php 
echo $vista->cargar('layout/pie-cliente');
?>
