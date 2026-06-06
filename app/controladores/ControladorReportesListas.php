<?php
/**
 * Controlador para generación de reportes PDF de listas de clientes y empleados
 */

class ControladorReportesListas extends Controlador {
    private $modeloCliente;
    private $modeloEmpleado;

    public function __construct() {
        parent::__construct();
        $this->modeloCliente = new ModeloCliente();
        $this->modeloEmpleado = new ModeloEmpleado();
    }

    /**
     * Generar reporte PDF de clientes
     */
    public function reporteClientes() {
        $this->verificarRol(ROL_ADMIN);
        // Cargar autoload de composer para Dompdf
        require_once __DIR__ . '/../../librerías/autoload.php';
        $clientes = $this->modeloCliente->obtenerTodosConDatos();

        if (class_exists('Dompdf\Dompdf')) {
            $html = $this->generarHTMLClientes($clientes);
            $this->generarPDF($html, 'reporte_clientes_' . date('Y-m-d_His'));
        } else {
            Sesion::crearMensaje('error', 'Error: Dompdf no está instalado.');
            $this->redirigir('admin/clientes');
        }
    }

    /**
     * Generar reporte PDF de empleados
     */
    public function reporteEmpleados() {
        $this->verificarRol(ROL_ADMIN);
        // Cargar autoload de composer para Dompdf
        require_once __DIR__ . '/../../librerías/autoload.php';
        $empleados = $this->modeloEmpleado->obtenerTodosConDatos();

        if (class_exists('Dompdf\Dompdf')) {
            $html = $this->generarHTMLEmpleados($empleados);
            $this->generarPDF($html, 'reporte_empleados_' . date('Y-m-d_His'));
        } else {
            Sesion::crearMensaje('error', 'Error: Dompdf no está instalado.');
            $this->redirigir('admin/empleados');
        }
    }

    /**
     * Generar HTML para reporte de clientes
     */
    private function generarHTMLClientes($clientes) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Reporte de Clientes</title>
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
                <p class="subtitle">Reporte de Clientes</p>
                <p class="meta">Generado: ' . date('d/m/Y H:i:s') . '</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombres</th>
                        <th>Correo</th>
                        <th>Empresa</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($clientes as $cliente) {
            $html .= '<tr>
                <td>' . $cliente['id_cliente'] . '</td>
                <td>' . Seguridad::escaparHTML($cliente['nombres'] . ' ' . $cliente['apellidos']) . '</td>
                <td>' . Seguridad::escaparHTML($cliente['correo']) . '</td>
                <td>' . Seguridad::escaparHTML($cliente['empresa'] ?? 'N/A') . '</td>
                <td>' . Seguridad::escaparHTML($cliente['telefono'] ?? 'N/A') . '</td>
                <td>' . Seguridad::escaparHTML($cliente['direccion'] ?? 'N/A') . '</td>
                <td>' . $cliente['estado'] . '</td>
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
     * Generar HTML para reporte de empleados
     */
    private function generarHTMLEmpleados($empleados) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Reporte de Empleados</title>
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
                <p class="subtitle">Reporte de Empleados</p>
                <p class="meta">Generado: ' . date('d/m/Y H:i:s') . '</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombres</th>
                        <th>Correo</th>
                        <th>Cargo</th>
                        <th>Especialidad</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($empleados as $empleado) {
            $html .= '<tr>
                <td>' . $empleado['id_empleado'] . '</td>
                <td>' . Seguridad::escaparHTML($empleado['nombres'] . ' ' . $empleado['apellidos']) . '</td>
                <td>' . Seguridad::escaparHTML($empleado['correo']) . '</td>
                <td>' . Seguridad::escaparHTML($empleado['cargo'] ?? 'N/A') . '</td>
                <td>' . Seguridad::escaparHTML($empleado['especialidad'] ?? 'N/A') . '</td>
                <td>' . $empleado['estado'] . '</td>
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
            $this->redirigir('admin/clientes');
        }
        ob_end_clean();

        ini_set('display_errors', $oldDisplayErrors);
        error_reporting($oldErrorReporting);

        $ruta_pdf = RUTA_ALMACENAMIENTO_PDF . $nombre_archivo . '.pdf';

        if (!file_exists(RUTA_ALMACENAMIENTO_PDF)) {
            mkdir(RUTA_ALMACENAMIENTO_PDF, 0777, true);
        }

        file_put_contents($ruta_pdf, $output);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $nombre_archivo . '.pdf"');
        header('Content-Length: ' . filesize($ruta_pdf));

        readfile($ruta_pdf);
        exit;
    }
}
