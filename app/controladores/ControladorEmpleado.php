<?php
/**
 * Controlador para Empleado
 */

class ControladorEmpleado extends Controlador {
    private $modeloEmpleado;
    private $modeloProyecto;
    private $modeloAsignacion;
    private $modeloAvance;

    public function __construct() {
        parent::__construct();
        $this->modeloEmpleado = new ModeloEmpleado();
        $this->modeloProyecto = new ModeloProyecto();
        $this->modeloAsignacion = new ModeloAsignacion();
        $this->modeloAvance = new ModeloAvance();
    }

    /**
     * Dashboard del empleado
     */
    public function inicio() {
        $this->verificarRol(ROL_EMPLEADO);

        $empleado = $this->modeloEmpleado->obtenerPorIdUsuario($_SESSION['id_usuario']);

        if (!$empleado) {
            $this->redirigir('autenticacion/cerrar-sesion');
        }

        $proyectos = $this->modeloProyecto->obtenerDelEmpleado($empleado['id_empleado']);

        $datos = [
            'total_proyectos' => count($proyectos),
            'proyectos_en_progreso' => count(array_filter($proyectos, function ($p) {
                return $p['estado'] === ESTADO_PROYECTO_EN_PROGRESO;
            })),
            'proyectos_finalizados' => count(array_filter($proyectos, function ($p) {
                return $p['estado'] === ESTADO_PROYECTO_FINALIZADO;
            })),
        ];

        $this->vista('empleado/inicio', $datos);
    }

    /**
     * Ver mis proyectos asignados
     */
    public function misProyectos() {
        $this->verificarRol(ROL_EMPLEADO);

        $empleado = $this->modeloEmpleado->obtenerPorIdUsuario($_SESSION['id_usuario']);

        if (!$empleado) {
            $this->redirigir('autenticacion/cerrar-sesion');
        }

        $datos = [
            'proyectos' => $this->modeloProyecto->obtenerDelEmpleado($empleado['id_empleado']),
        ];

        $this->vista('empleado/proyectos/listar', $datos);
    }

    /**
     * Ver detalles del proyecto
     */
    public function verProyecto() {
        $this->verificarRol(ROL_EMPLEADO);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirigir('empleado/mis-proyectos');
        }

        $empleado = $this->modeloEmpleado->obtenerPorIdUsuario($_SESSION['id_usuario']);
        $proyecto = $this->modeloProyecto->obtenerConDatos($id);

        if (!$proyecto) {
            Sesion::crearMensaje('error', 'Proyecto no encontrado.');
            $this->redirigir('empleado/mis-proyectos');
        }

        // Verificar que esté asignado
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

        $datos = [
            'proyecto' => $proyecto,
            'asignaciones' => $asignaciones,
            'avances' => $this->modeloAvance->obtenerDelProyecto($id),
            'id_empleado' => $empleado['id_empleado'],
        ];

        $this->vista('empleado/proyectos/ver', $datos);
    }

    /**
     * Actualizar progreso del proyecto
     */
    public function actualizarProgreso() {
        $this->verificarRol(ROL_EMPLEADO);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('empleado/mis-proyectos');
        }

        $id_proyecto = $_POST['id_proyecto'] ?? null;
        $porcentaje = $_POST['porcentaje'] ?? 0;
        $observacion = $_POST['observacion'] ?? '';

        if (!$id_proyecto) {
            Sesion::crearMensaje('error', 'Proyecto no especificado.');
            $this->redirigir('empleado/mis-proyectos');
        }

        $empleado = $this->modeloEmpleado->obtenerPorIdUsuario($_SESSION['id_usuario']);
        $proyecto = $this->modeloProyecto->obtenerConDatos($id_proyecto);

        if (!$proyecto) {
            Sesion::crearMensaje('error', 'Proyecto no encontrado.');
            $this->redirigir('empleado/mis-proyectos');
        }

        // Verificar que esté asignado
        $asignaciones = $this->modeloAsignacion->obtenerDelProyecto($id_proyecto);
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

        $validador = new Validador(['porcentaje' => $porcentaje]);
        $validador->requerido('porcentaje', 'El porcentaje es requerido')
                  ->numerico('porcentaje', 'El porcentaje debe ser un número');

        if ($validador->hayErrores()) {
            Sesion::crearMensaje('error', 'Datos no válidos.');
            $this->redirigir('empleado/ver-proyecto?id=' . $id_proyecto);
        }

        try {
            // Registrar avance
            $this->modeloAvance->registrar([
                'id_proyecto' => $id_proyecto,
                'id_empleado' => $empleado['id_empleado'],
                'porcentaje' => $porcentaje,
                'observacion' => $observacion,
            ]);

            // Actualizar porcentaje promedio del proyecto
            $porcentaje_promedio = $this->modeloAvance->obtenerPorcentajePromedio($id_proyecto);
            $this->modeloProyecto->actualizarPorcentaje($id_proyecto, $porcentaje_promedio);

            // Si llega al 100%, marcar como finalizado
            if ($porcentaje_promedio >= 100) {
                $this->modeloProyecto->actualizarEstado($id_proyecto, ESTADO_PROYECTO_FINALIZADO);
            }

            Sesion::crearMensaje('exito', 'Progreso actualizado correctamente.');
            $this->redirigir('empleado/ver-proyecto?id=' . $id_proyecto);
        } catch (Exception $e) {
            Sesion::crearMensaje('error', 'Error: ' . $e->getMessage());
            $this->redirigir('empleado/ver-proyecto?id=' . $id_proyecto);
        }
    }
}
