<?php
/**
 * Controlador para generación de reportes PDF
 */

class ControladorReportes extends Controlador {
    private $modeloProyecto;
    private $modeloCliente;
    private $modeloEmpleado;
    private $modeloAsignacion;
    private $modeloAvance;

    public function __construct() {
        parent::__construct();
        $this->modeloProyecto = new ModeloProyecto();
        $this->modeloCliente = new ModeloCliente();
        $this->modeloEmpleado = new ModeloEmpleado();
        $this->modeloAsignacion = new ModeloAsignacion();
        $this->modeloAvance = new ModeloAvance();
    }

    /**
     * Generar reporte PDF de todos los proyectos
     */
    public function reporteProyectos() {
        $this->verificarRol(ROL_ADMIN);

        // Cargar autoload de composer para Dompdf
        require_once __DIR__ . '/../../librerías/autoload.php';

        $proyectos = $this->modeloProyecto->obtenerTodosConDatos();

        // Usar Dompdf si está disponible
        if (class_exists('Dompdf\Dompdf')) {
            $html = $this->generarHTMLProyectos($proyectos);
            $this->generarPDF($html, 'reporte_proyectos_' . date('Y-m-d_His'));
        } else {
            Sesion::crearMensaje('error', 'Error: Dompdf no está instalado.');
            $this->redirigir('admin/proyectos');
        }
    }

    /**
     * Generar reporte PDF de un proyecto específico
     */
    public function reporteProyecto() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            Sesion::crearMensaje('error', 'Proyecto no especificado.');
            $this->redirigir('admin/proyectos');
        }

        $proyecto = $this->modeloProyecto->obtenerConDatos($id);

        if (!$proyecto) {
            Sesion::crearMensaje('error', 'Proyecto no encontrado.');
            $this->redirigir('admin/proyectos');
        }

        // Verificar acceso según rol
        if ($_SESSION['rol'] === ROL_CLIENTE) {
            $cliente = $this->modeloCliente->obtenerPorIdUsuario($_SESSION['id_usuario']);
            if ($proyecto['id_cliente'] != $cliente['id_cliente']) {
                Sesion::crearMensaje('error', 'No tienes acceso a este proyecto.');
                $this->redirigir('cliente/mis-proyectos');
            }
        } elseif ($_SESSION['rol'] === ROL_EMPLEADO) {
            $empleado = $this->modeloEmpleado->obtenerPorIdUsuario($_SESSION['id_usuario']);
            $asignaciones = $this->modeloAsignacion->obtenerDelProyecto($id);
            $asignado = false;
            foreach ($asignaciones as $a) {
                if ($a['id_empleado'] == $empleado['id_empleado']) {
                    $asignado = true;
                    break;
                }
            }
            if (!$asignado) {
                Sesion::crearMensaje('error', 'No tienes acceso a este proyecto.');
                $this->redirigir('empleado/mis-proyectos');
            }
        } else {
            $this->verificarRol(ROL_ADMIN);
        }

        $asignaciones = $this->modeloAsignacion->obtenerDelProyecto($id);
        $avances = $this->modeloAvance->obtenerDelProyecto($id);

        // Cargar autoload de composer para Dompdf
        require_once __DIR__ . '/../../librerías/autoload.php';

        if (class_exists('Dompdf\Dompdf')) {
            $html = $this->generarHTMLProyecto($proyecto, $asignaciones, $avances);
            $this->generarPDF($html, 'reporte_proyecto_' . $id . '_' . date('Y-m-d_His'));
        } else {
            Sesion::crearMensaje('error', 'Error: Dompdf no está instalado.');
            if ($_SESSION['rol'] === ROL_CLIENTE) {
                $this->redirigir('cliente/mis-proyectos');
            } elseif ($_SESSION['rol'] === ROL_EMPLEADO) {
                $this->redirigir('empleado/mis-proyectos');
            } else {
                $this->redirigir('admin/proyectos');
            }
        }
    }

    /**
     * Generar HTML para reporte de proyectos
     */
    private function generarHTMLProyectos($proyectos) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Reporte de Proyectos</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; color: #333; line-height: 1.4; }
                .header { margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e1e8f2; }
                .header .title { color: #003d82; margin: 0 0 5px; font-size: 24px; }
                .header .subtitle { color: #0056b3; margin: 0; font-size: 14px; }
                .meta { margin-top: 10px; color: #555; font-size: 11px; }
                table { width: 100%; border-collapse: collapse; margin-top: 15px; table-layout: fixed; }
                th, td { padding: 10px 8px; text-align: left; vertical-align: top; overflow-wrap: break-word; word-wrap: break-word; }
                th { background-color: #003d82; color: white; font-weight: normal; font-size: 11px; }
                td { border-bottom: 1px solid #ddd; font-size: 10pt; }
                tr:nth-child(even) { background-color: #f7f9fc; }
                .empresa { font-size: 10px; margin-top: 30px; text-align: center; color: #777; }
                .page-footer { margin-top: 25px; padding-top: 10px; border-top: 1px solid #e1e8f2; color: #777; font-size: 10px; text-align: center; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1 class="title">' . NOMBRE_APP . '</h1>
                <p class="subtitle">Reporte de Proyectos</p>
                <p class="meta">Generado: ' . date('d/m/Y H:i:s') . '</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Cliente</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Precio</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Entrega</th>
                        <th>Avance</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($proyectos as $proyecto) {
            $html .= '<tr>
                <td>' . $proyecto['id_proyecto'] . '</td>
                <td>' . Seguridad::escaparHTML($proyecto['titulo']) . '</td>
                <td>' . Seguridad::escaparHTML($proyecto['cliente_nombres'] . ' ' . $proyecto['cliente_apellidos']) . '</td>
                <td>' . $proyecto['prioridad'] . '</td>
                <td>' . $proyecto['estado'] . '</td>
                <td>$' . number_format($proyecto['precio'], 2, '.', ',') . '</td>
                <td>' . (!empty($proyecto['fecha_inicio']) && strtotime($proyecto['fecha_inicio']) ? date('d/m/Y', strtotime($proyecto['fecha_inicio'])) : 'N/A') . '</td>
                <td>' . (!empty($proyecto['fecha_entrega']) && strtotime($proyecto['fecha_entrega']) ? date('d/m/Y', strtotime($proyecto['fecha_entrega'])) : 'N/A') . '</td>
                <td>' . $proyecto['porcentaje_general'] . '%</td>
            </tr>';
        }

        $html .= '</tbody>
            </table>
            <div class="empresa">
                <p>' . NOMBRE_APP . ' &copy; ' . date('Y') . '</p>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Generar HTML para reporte de un proyecto específico
     */
    private function generarHTMLProyecto($proyecto, $asignaciones, $avances) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Reporte del Proyecto</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; color: #333; line-height: 1.5; }
                .header { margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e1e8f2; }
                .header .title { color: #003d82; margin: 0 0 5px; font-size: 24px; }
                .header .subtitle { color: #0056b3; margin: 0; font-size: 14px; }
                .meta { margin-top: 10px; color: #555; font-size: 11px; }
                .info-bloque { background-color: #f7f9fc; padding: 15px; margin: 10px 0; border-left: 4px solid #00a8e8; }
                .info-bloque p { margin: 5px 0; font-size: 11pt; }
                strong { color: #003d82; }
                table { width: 100%; border-collapse: collapse; margin-top: 15px; table-layout: fixed; }
                th, td { padding: 10px 8px; text-align: left; vertical-align: top; overflow-wrap: break-word; word-wrap: break-word; }
                th { background-color: #003d82; color: white; font-weight: normal; font-size: 11px; }
                td { border-bottom: 1px solid #ddd; font-size: 10pt; }
                tr:nth-child(even) { background-color: #f7f9fc; }
                .empresa { font-size: 10px; margin-top: 30px; text-align: center; color: #777; }
                .descripcion { background-color: white; padding: 12px; border: 1px solid #ddd; margin: 10px 0; font-size: 10pt; white-space: pre-wrap; }
                h3 { color: #003d82; margin-top: 20px; font-size: 14pt; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1 class="title">' . NOMBRE_APP . '</h1>
                <p class="subtitle">Reporte del Proyecto</p>
                <p class="meta">Generado: ' . date('d/m/Y H:i:s') . '</p>
            </div>
            <div class="info-bloque">
                <p><strong>Título:</strong> ' . Seguridad::escaparHTML($proyecto['titulo']) . '</p>
                <p><strong>Cliente:</strong> ' . Seguridad::escaparHTML($proyecto['cliente_nombres'] . ' ' . $proyecto['cliente_apellidos']) . '</p>
                <p><strong>Correo Cliente:</strong> ' . Seguridad::escaparHTML($proyecto['cliente_correo']) . '</p>
                <p><strong>Estado:</strong> ' . $proyecto['estado'] . '</p>
                <p><strong>Prioridad:</strong> ' . $proyecto['prioridad'] . '</p>
                <p><strong>Precio:</strong> $' . number_format($proyecto['precio'], 2, '.', ',') . '</p>
                <p><strong>Fecha Inicio:</strong> ' . (!empty($proyecto['fecha_inicio']) && strtotime($proyecto['fecha_inicio']) ? date('d/m/Y', strtotime($proyecto['fecha_inicio'])) : 'N/A') . '</p>
                <p><strong>Fecha Entrega:</strong> ' . (!empty($proyecto['fecha_entrega']) && strtotime($proyecto['fecha_entrega']) ? date('d/m/Y', strtotime($proyecto['fecha_entrega'])) : 'N/A') . '</p>
                <p><strong>Avance General:</strong> ' . $proyecto['porcentaje_general'] . '%</p>
            </div>

            <h3>Descripción del Proyecto</h3>
            <div class="descripcion">
                ' . nl2br(Seguridad::escaparHTML($proyecto['descripcion'])) . '
            </div>';

        if (!empty($asignaciones)) {
            $html .= '<h3>Empleados Asignados</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cargo</th>
                        <th>Rol en Proyecto</th>
                        <th>Correo</th>
                    </tr>
                </thead>
                <tbody>';

            foreach ($asignaciones as $asignacion) {
                $html .= '<tr>
                    <td>' . Seguridad::escaparHTML($asignacion['nombres'] . ' ' . $asignacion['apellidos']) . '</td>
                    <td>' . Seguridad::escaparHTML($asignacion['cargo'] ?? 'N/A') . '</td>
                    <td>' . Seguridad::escaparHTML($asignacion['rol_en_proyecto'] ?? 'N/A') . '</td>
                    <td>' . Seguridad::escaparHTML($asignacion['correo']) . '</td>
                </tr>';
            }

            $html .= '</tbody>
            </table>';
        }

        if (!empty($avances)) {
            $html .= '<h3>Historial de Avances</h3>
            <table>
                <thead>
                    <tr>
                        <th>Empleado</th>
                        <th>Porcentaje</th>
                        <th>Observación</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>';

            foreach ($avances as $avance) {
                $html .= '<tr>
                    <td>' . Seguridad::escaparHTML($avance['nombres'] . ' ' . $avance['apellidos']) . '</td>
                    <td>' . $avance['porcentaje'] . '%</td>
                    <td>' . Seguridad::escaparHTML($avance['observacion'] ?? 'N/A') . '</td>
                    <td>' . date('d/m/Y H:i', strtotime($avance['fecha_actualizacion'])) . '</td>
                </tr>';
            }

            $html .= '</tbody>
            </table>';
        }

        $html .= '<div class="empresa">
                <p>Generado: ' . date('d/m/Y H:i:s') . '</p>
                <p>' . NOMBRE_APP . ' &copy; ' . date('Y') . '</p>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Generar el PDF usando Dompdf
     */
    private function generarPDF($html, $nombre_archivo) {
        require_once __DIR__ . '/../../librerías/autoload.php';

        $oldDisplayErrors = ini_get('display_errors');
        $oldErrorReporting = error_reporting();
        ini_set('display_errors', '0');
        error_reporting($oldErrorReporting & ~E_DEPRECATED & ~E_USER_DEPRECATED & ~E_NOTICE & ~E_WARNING);

        ob_start();
        try {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $output = $dompdf->output();
        } catch (\Throwable $e) {
            ob_end_clean();
            ini_set('display_errors', $oldDisplayErrors);
            error_reporting($oldErrorReporting);

            Sesion::crearMensaje('error', 'Error al generar el PDF: ' . $e->getMessage());
            $this->redirigir('admin/proyectos');
        }
        ob_end_clean();

        ini_set('display_errors', $oldDisplayErrors);
        error_reporting($oldErrorReporting);

        $ruta_pdf = RUTA_ALMACENAMIENTO_PDF . $nombre_archivo . '.pdf';

        if (!file_exists(RUTA_ALMACENAMIENTO_PDF)) {
            mkdir(RUTA_ALMACENAMIENTO_PDF, 0777, true);
        }

        file_put_contents($ruta_pdf, $output);

        // Descargar el PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $nombre_archivo . '.pdf"');
        header('Content-Length: ' . filesize($ruta_pdf));

        readfile($ruta_pdf);
        exit;
    }
}
