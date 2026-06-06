<?php
/**
 * Controlador para Cliente
 */

class ControladorCliente extends Controlador {
    private $modeloCliente;
    private $modeloSolicitud;
    private $modeloProyecto;
    private $modeloAsignacion;
    private $modeloAvance;

    public function __construct() {
        parent::__construct();
        $this->modeloCliente = new ModeloCliente();
        $this->modeloSolicitud = new ModeloSolicitud();
        $this->modeloProyecto = new ModeloProyecto();
        $this->modeloAsignacion = new ModeloAsignacion();
        $this->modeloAvance = new ModeloAvance();
    }

    /**
     * Dashboard del cliente
     */
    public function inicio() {
        $this->verificarRol(ROL_CLIENTE);

        $cliente = $this->modeloCliente->obtenerPorIdUsuario($_SESSION['id_usuario']);

        if (!$cliente) {
            $this->redirigir('autenticacion/cerrar-sesion');
        }

        $datos = [
            'total_solicitudes' => count($this->modeloSolicitud->obtenerDelCliente($cliente['id_cliente'])),
            'total_proyectos' => count($this->modeloProyecto->obtenerDelCliente($cliente['id_cliente'])),
            'solicitudes_pendientes' => count(array_filter($this->modeloSolicitud->obtenerDelCliente($cliente['id_cliente']), function ($s) {
                return $s['estado'] === ESTADO_SOLICITUD_PENDIENTE;
            })),
        ];

        $this->vista('cliente/inicio', $datos);
    }

    /**
     * Crear nueva solicitud de proyecto
     */
    public function nuevaSolicitud() {
        $this->verificarRol(ROL_CLIENTE);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cliente = $this->modeloCliente->obtenerPorIdUsuario($_SESSION['id_usuario']);

            $datos = [
                'titulo' => $_POST['titulo'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'tipo_sistema' => $_POST['tipo_sistema'] ?? '',
                'urgencia' => $_POST['urgencia'] ?? URGENCIA_MEDIA,
            ];

            $validador = new Validador($datos);
            $validador->requerido('titulo', 'El título es requerido')
                      ->requerido('descripcion', 'La descripción es requerida')
                      ->requerido('tipo_sistema', 'El tipo de sistema es requerido');

            if ($validador->hayErrores()) {
                Sesion::crearMensaje('error', 'Error en los datos de la solicitud.');
                $this->redirigir('cliente/nueva-solicitud');
            }

            try {
                $datos['id_cliente'] = $cliente['id_cliente'];
                $id_solicitud = $this->modeloSolicitud->crear($datos);
                Sesion::crearMensaje('exito', 'Solicitud creada correctamente. Espera la cotización del admin.');
                $this->redirigir('cliente/mis-solicitudes');
            } catch (Exception $e) {
                Sesion::crearMensaje('error', 'Error: ' . $e->getMessage());
            }
        }

        $this->vista('cliente/solicitudes/nueva');
    }

    /**
     * Ver mis solicitudes
     */
    public function misSolicitudes() {
        $this->verificarRol(ROL_CLIENTE);

        $cliente = $this->modeloCliente->obtenerPorIdUsuario($_SESSION['id_usuario']);

        if (!$cliente) {
            $this->redirigir('autenticacion/cerrar-sesion');
        }

        $datos = [
            'solicitudes' => $this->modeloSolicitud->obtenerDelCliente($cliente['id_cliente']),
        ];

        $this->vista('cliente/solicitudes/listar', $datos);
    }

    /**
     * Ver detalle de solicitud y responder propuesta
     */
    public function verSolicitud() {
        $this->verificarRol(ROL_CLIENTE);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirigir('cliente/mis-solicitudes');
        }

        $solicitud = $this->modeloSolicitud->obtenerConDatos($id);

        if (!$solicitud) {
            Sesion::crearMensaje('error', 'Solicitud no encontrada.');
            $this->redirigir('cliente/mis-solicitudes');
        }

        // Verificar que sea del usuario actual
        $cliente = $this->modeloCliente->obtenerPorIdUsuario($_SESSION['id_usuario']);
        if ($solicitud['id_cliente'] != $cliente['id_cliente']) {
            Sesion::crearMensaje('error', 'No tienes acceso a esta solicitud.');
            $this->redirigir('cliente/mis-solicitudes');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';

            // Logging para diagnosticar problemas con aceptar/rechazar
            $logDir = __DIR__ . '/../../storage/logs/';
            if (!file_exists($logDir)) {
                @mkdir($logDir, 0777, true);
            }
            $logFile = $logDir . 'solicitud_actions.log';
            $logEntry = date('Y-m-d H:i:s') . " | usuario=" . ($_SESSION['id_usuario'] ?? 'anon') . " | solicitud={$id} | accion=" . $accion . " | POST=" . json_encode($_POST) . "\n";
            @file_put_contents($logFile, $logEntry, FILE_APPEND);

            if ($accion === 'aceptar') {
                try {
                    $this->modeloSolicitud->aceptar($id);
                    Sesion::crearMensaje('exito', 'Propuesta aceptada. El admin asignará empleados.');
                    $this->redirigir('cliente/mis-solicitudes');
                } catch (Exception $e) {
                    Sesion::crearMensaje('error', 'Error: ' . $e->getMessage());
                }
            } elseif ($accion === 'rechazar') {
                $motivo = $_POST['motivo_rechazo'] ?? '';

                $validador = new Validador(['motivo_rechazo' => $motivo]);
                $validador->requerido('motivo_rechazo', 'El motivo del rechazo es requerido');

                if ($validador->hayErrores()) {
                    Sesion::crearMensaje('error', 'Debes escribir un motivo para rechazar.');
                    $this->redirigir('cliente/ver-solicitud?id=' . $id);
                }

                try {
                    $this->modeloSolicitud->rechazar($id, $motivo);
                    Sesion::crearMensaje('exito', 'Propuesta rechazada.');
                    $this->redirigir('cliente/mis-solicitudes');
                } catch (Exception $e) {
                    Sesion::crearMensaje('error', 'Error: ' . $e->getMessage());
                }
            }
        }

        $datos = ['solicitud' => $solicitud];
        $this->vista('cliente/solicitudes/ver', $datos);
    }

    /**
     * Ver mis proyectos
     */
    public function misProyectos() {
        $this->verificarRol(ROL_CLIENTE);

        $cliente = $this->modeloCliente->obtenerPorIdUsuario($_SESSION['id_usuario']);

        if (!$cliente) {
            $this->redirigir('autenticacion/cerrar-sesion');
        }

        $datos = [
            'proyectos' => $this->modeloProyecto->obtenerDelCliente($cliente['id_cliente']),
        ];

        $this->vista('cliente/proyectos/listar', $datos);
    }

    /**
     * Ver detalles del proyecto
     */
    public function verProyecto() {
        $this->verificarRol(ROL_CLIENTE);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirigir('cliente/mis-proyectos');
        }

        $proyecto = $this->modeloProyecto->obtenerConDatos($id);

        if (!$proyecto) {
            Sesion::crearMensaje('error', 'Proyecto no encontrado.');
            $this->redirigir('cliente/mis-proyectos');
        }

        // Verificar que sea del usuario actual
        $cliente = $this->modeloCliente->obtenerPorIdUsuario($_SESSION['id_usuario']);
        if ($proyecto['id_cliente'] != $cliente['id_cliente']) {
            Sesion::crearMensaje('error', 'No tienes acceso a este proyecto.');
            $this->redirigir('cliente/mis-proyectos');
        }

        $datos = [
            'proyecto' => $proyecto,
            'asignaciones' => $this->modeloAsignacion->obtenerDelProyecto($id),
            'avances' => $this->modeloAvance->obtenerDelProyecto($id),
        ];

        $this->vista('cliente/proyectos/ver', $datos);
    }
}
