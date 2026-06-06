<?php
/**
 * Controlador para Admin
 */

class ControladorAdmin extends Controlador {
    private $modeloUsuario;
    private $modeloCliente;
    private $modeloEmpleado;
    private $modeloSolicitud;
    private $modeloProyecto;
    private $modeloAsignacion;
    private $modeloAvance;

    public function __construct() {
        parent::__construct();
        $this->modeloUsuario = new ModeloUsuario();
        $this->modeloCliente = new ModeloCliente();
        $this->modeloEmpleado = new ModeloEmpleado();
        $this->modeloSolicitud = new ModeloSolicitud();
        $this->modeloProyecto = new ModeloProyecto();
        $this->modeloAsignacion = new ModeloAsignacion();
        $this->modeloAvance = new ModeloAvance();
    }

    /**
     * Dashboard del admin
     */
    public function inicio() {
        $this->verificarRol(ROL_ADMIN);

        $datos = [
            'total_clientes' => $this->modeloCliente->contar('clientes'),
            'total_empleados' => $this->modeloEmpleado->contar('empleados'),
            'total_proyectos' => $this->modeloProyecto->contar('proyectos'),
            'solicitudes_pendientes' => count($this->modeloSolicitud->obtenerPendientesCotizacion()),
            'proyectos_en_progreso' => count($this->modeloProyecto->obtenerEnProgreso()),
        ];

        $this->vista('admin/inicio', $datos);
    }

    /**
     * Listar clientes
     */
    public function clientes() {
        $this->verificarRol(ROL_ADMIN);

        $datos = [
            'clientes' => $this->modeloCliente->obtenerTodosConDatos(),
        ];

        $this->vista('admin/clientes/listar', $datos);
    }
    
    /**
     * Agregar cliente
     */
    public function agregarCliente() {
        $this->verificarRol(ROL_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datosUsuario = [
                'nombres' => $_POST['nombres'] ?? '',
                'apellidos' => $_POST['apellidos'] ?? '',
                'usuario' => $_POST['usuario'] ?? '',
                'correo' => $_POST['correo'] ?? '',
                'password' => $_POST['password'] ?? '',
                'rol' => ROL_CLIENTE,
                'empresa' => $_POST['empresa'] ?? '',
                'telefono' => $_POST['telefono'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
            ];

            $validador = new Validador($datosUsuario);
            $validador->requerido('nombres', 'Los nombres son requeridos')
                      ->soloLetras('nombres', 'Los nombres solo pueden contener letras y espacios')
                      ->maximo('nombres', 50, 'Los nombres no pueden exceder 50 caracteres')
                      ->requerido('apellidos', 'Los apellidos son requeridos')
                      ->soloLetras('apellidos', 'Los apellidos solo pueden contener letras y espacios')
                      ->maximo('apellidos', 50, 'Los apellidos no pueden exceder 50 caracteres')
                      ->requerido('usuario', 'El usuario es requerido')
                      ->minimo('usuario', 4, 'El usuario debe tener al menos 4 caracteres')
                      ->alfanumerico('usuario', 'El usuario solo puede contener letras, números y guiones bajos')
                      ->maximo('usuario', 30, 'El usuario no puede exceder 30 caracteres')
                      ->requerido('correo', 'El correo es requerido')
                      ->correo('correo', 'El correo no es válido')
                      ->requerido('password', 'La contraseña es requerida')
                      ->minimo('password', 6, 'La contraseña debe tener al menos 6 caracteres')
                      ->contrasenaFuerte('password', 'La contraseña debe incluir letras y números')
                      ->requerido('empresa', 'La empresa es requerida')
                      ->maximo('empresa', 100, 'La empresa no puede exceder 100 caracteres')
                      ->requerido('telefono', 'El teléfono es requerido')
                      ->telefonoExacto('telefono', 9, 'El teléfono debe contener exactamente 9 dígitos')
                      ->requerido('direccion', 'La dirección es requerida')
                      ->maximo('direccion', 200, 'La dirección no puede exceder 200 caracteres');

            if ($validador->hayErrores()) {
                Sesion::crearMensaje('error', 'Error en los datos del cliente. Revise los campos.');
                Sesion::crear('errores_agregar_cliente', $validador->errores());
                Sesion::crear('old_agregar_cliente', $datosUsuario);
                $this->redirigir('admin/agregar-cliente');
            }

            if ($this->modeloUsuario->usuarioExiste($datosUsuario['usuario'])) {
                Sesion::crearMensaje('error', 'El usuario ya existe.');
                Sesion::crear('old_agregar_cliente', $datosUsuario);
                $this->redirigir('admin/agregar-cliente');
            }

            if ($this->modeloUsuario->correoExiste($datosUsuario['correo'])) {
                Sesion::crearMensaje('error', 'El correo ya está registrado.');
                Sesion::crear('old_agregar_cliente', $datosUsuario);
                $this->redirigir('admin/agregar-cliente');
            }

            try {
                $datosUsuario['password'] = Seguridad::encriptarPassword($datosUsuario['password']);
                $id_usuario = $this->modeloUsuario->registrar($datosUsuario);

                $this->modeloCliente->crear([
                    'id_usuario' => $id_usuario,
                    'empresa' => $datosUsuario['empresa'],
                    'telefono' => $datosUsuario['telefono'],
                    'direccion' => $datosUsuario['direccion'],
                ]);

                Sesion::crearMensaje('exito', 'Cliente agregado correctamente.');
                $this->redirigir('admin/clientes');
            } catch (Exception $e) {
                Sesion::crearMensaje('error', 'Error al agregar el cliente: ' . $e->getMessage());
                Sesion::crear('old_agregar_cliente', $datosUsuario);
                $this->redirigir('admin/agregar-cliente');
            }
        }

        $datos = [
            'errores' => Sesion::flash('errores_agregar_cliente') ?? [],
            'old' => Sesion::flash('old_agregar_cliente') ?? []
        ];

        $this->vista('admin/clientes/agregar', $datos);
    }

    /**
     * Editar cliente
     */
    public function editarCliente() {
        $this->verificarRol(ROL_ADMIN);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirigir('admin/clientes');
        }

        $cliente = $this->modeloCliente->obtenerConDatos($id);

        if (!$cliente) {
            Sesion::crearMensaje('error', 'Cliente no encontrado.');
            $this->redirigir('admin/clientes');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datosCliente = [
                'nombres' => $_POST['nombres'] ?? '',
                'apellidos' => $_POST['apellidos'] ?? '',
                'correo' => $_POST['correo'] ?? '',
                'empresa' => $_POST['empresa'] ?? '',
                'telefono' => $_POST['telefono'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'estado' => $_POST['estado'] ?? ESTADO_ACTIVO,
            ];

            $validador = new Validador($datosCliente);
            $validador->requerido('nombres', 'Los nombres son requeridos')
                      ->soloLetras('nombres', 'Los nombres solo pueden contener letras y espacios')
                      ->maximo('nombres', 50, 'Los nombres no pueden exceder 50 caracteres')
                      ->requerido('apellidos', 'Los apellidos son requeridos')
                      ->soloLetras('apellidos', 'Los apellidos solo pueden contener letras y espacios')
                      ->maximo('apellidos', 50, 'Los apellidos no pueden exceder 50 caracteres')
                      ->requerido('correo', 'El correo es requerido')
                      ->correo('correo', 'El correo no es válido')
                      ->requerido('empresa', 'La empresa es requerida')
                      ->maximo('empresa', 100, 'La empresa no puede exceder 100 caracteres')
                      ->requerido('telefono', 'El teléfono es requerido')
                      ->telefonoExacto('telefono', 9, 'El teléfono debe contener exactamente 9 dígitos')
                      ->requerido('direccion', 'La dirección es requerida')
                      ->maximo('direccion', 200, 'La dirección no puede exceder 200 caracteres');

            if ($validador->hayErrores()) {
                Sesion::crearMensaje('error', 'Error en los datos del cliente. Revise los campos.');
                Sesion::crear('errores_editar_cliente', $validador->errores());
                Sesion::crear('old_editar_cliente', $datosCliente);
                $this->redirigir('admin/editar-cliente?id=' . $id);
            }

            try {
                $this->modeloCliente->actualizar($id, $datosCliente);
                Sesion::crearMensaje('exito', 'Cliente actualizado correctamente.');
                $this->redirigir('admin/clientes');
            } catch (Exception $e) {
                Sesion::crearMensaje('error', 'Error al actualizar: ' . $e->getMessage());
            }
        }

        $datos = ['cliente' => $cliente];
        $this->vista('admin/clientes/editar', $datos);
    }

    /**
     * Ver cliente
     */
    public function verCliente() {
        $this->verificarRol(ROL_ADMIN);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirigir('admin/clientes');
        }

        $cliente = $this->modeloCliente->obtenerConDatos($id);

        if (!$cliente) {
            Sesion::crearMensaje('error', 'Cliente no encontrado.');
            $this->redirigir('admin/clientes');
        }

        $datos = ['cliente' => $cliente];
        $this->vista('admin/clientes/ver', $datos);
    }

    /**
     * Eliminar cliente
     */
    public function eliminarCliente() {
        $this->verificarRol(ROL_ADMIN);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirigir('admin/clientes');
        }

        $cliente = $this->modeloCliente->obtenerConDatos($id);

        if (!$cliente) {
            Sesion::crearMensaje('error', 'Cliente no encontrado.');
            $this->redirigir('admin/clientes');
        }

        try {
            // Eliminar primero los proyectos y solicitudes del cliente
            $this->modeloProyecto->eliminarPorCliente($id);
            $this->modeloSolicitud->eliminarPorCliente($id);
            $this->modeloUsuario->eliminar($cliente['id_usuario']);

            Sesion::crearMensaje('eliminado', 'Cliente y sus proyectos eliminados correctamente.');
            $this->redirigir('admin/clientes');
        } catch (Exception $e) {
            Sesion::crearMensaje('error', 'Error al eliminar: ' . $e->getMessage());
            $this->redirigir('admin/clientes');
        }
    }

    /**
     * Listar empleados
     */
    public function empleados() {
        $this->verificarRol(ROL_ADMIN);

        $datos = [
            'empleados' => $this->modeloEmpleado->obtenerTodosConDatosIncluyeInactivos(),
        ];

        $this->vista('admin/empleados/listar', $datos);
    }

    /**
     * Activar / Desactivar empleado
     */
    public function cambiarEstadoEmpleado() {
        $this->verificarRol(ROL_ADMIN);
        // Preferir POST con CSRF; de lo contrario intentar GET por compatibilidad
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $token = $_POST['csrf_token'] ?? '';

            if (!$id || !Sesion::verificarTokenCSRF($token)) {
                Sesion::crearMensaje('error', 'Solicitud inválida.');
                $this->redirigir('admin/empleados');
            }
        } else {
            $id = $_GET['id'] ?? null;
        }

        if (!$id) {
            $this->redirigir('admin/empleados');
        }

        $empleado = $this->modeloEmpleado->obtenerConDatos($id);

        if (!$empleado) {
            Sesion::crearMensaje('error', 'Empleado no encontrado.');
            $this->redirigir('admin/empleados');
        }

        $nuevoEstado = ($empleado['estado'] === ESTADO_ACTIVO) ? ESTADO_INACTIVO : ESTADO_ACTIVO;

        try {
            $this->modeloEmpleado->actualizar($id, ['estado' => $nuevoEstado]);
            $mensaje = ($nuevoEstado === ESTADO_ACTIVO) ? 'Empleado activado.' : 'Empleado desactivado.';
            Sesion::crearMensaje('exito', $mensaje);
        } catch (Exception $e) {
            Sesion::crearMensaje('error', 'Error al cambiar estado: ' . $e->getMessage());
        }

        $this->redirigir('admin/empleados');
    }

    /**
     * Agregar empleado
     */
    public function agregarEmpleado() {
        $this->verificarRol(ROL_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datosUsuario = [
                'nombres' => $_POST['nombres'] ?? '',
                'apellidos' => $_POST['apellidos'] ?? '',
                'usuario' => $_POST['usuario'] ?? '',
                'correo' => $_POST['correo'] ?? '',
                'password' => $_POST['password'] ?? '',
                'rol' => ROL_EMPLEADO,
                'cargo' => $_POST['cargo'] ?? '',
                'especialidad' => $_POST['especialidad'] ?? '',
            ];

            $validador = new Validador($datosUsuario);
            $validador->requerido('nombres', 'Los nombres son requeridos')
                      ->soloLetras('nombres', 'Los nombres solo pueden contener letras y espacios')
                      ->maximo('nombres', 50, 'Los nombres no pueden exceder 50 caracteres')
                      ->requerido('apellidos', 'Los apellidos son requeridos')
                      ->soloLetras('apellidos', 'Los apellidos solo pueden contener letras y espacios')
                      ->maximo('apellidos', 50, 'Los apellidos no pueden exceder 50 caracteres')
                      ->requerido('usuario', 'El usuario es requerido')
                      ->minimo('usuario', 4, 'El usuario debe tener al menos 4 caracteres')
                      ->alfanumerico('usuario', 'El usuario solo puede contener letras, números y guiones bajos')
                      ->requerido('correo', 'El correo es requerido')
                      ->correo('correo', 'El correo no es válido')
                      ->requerido('password', 'La contraseña es requerida')
                      ->minimo('password', 6, 'La contraseña debe tener al menos 6 caracteres')
                      ->contrasenaFuerte('password', 'La contraseña debe incluir letras y números')
                      ->requerido('cargo', 'El cargo es requerido')
                      ->soloLetras('cargo', 'El cargo solo puede contener letras y espacios')
                      ->maximo('cargo', 100, 'El cargo no puede exceder 100 caracteres')
                      ->requerido('especialidad', 'La especialidad es requerida')
                      ->soloLetras('especialidad', 'La especialidad solo puede contener letras y espacios');
                      

            if ($validador->hayErrores()) {
                Sesion::crearMensaje('error', 'Error en los datos del empleado. Revise los campos.');
                Sesion::crear('errores_agregar_empleado', $validador->errores());
                Sesion::crear('old_agregar_empleado', $datosUsuario);
                $this->redirigir('admin/agregar-empleado');
            }

            if ($this->modeloUsuario->usuarioExiste($datosUsuario['usuario'])) {
                Sesion::crearMensaje('error', 'El usuario ya existe.');
                Sesion::crear('old_agregar_empleado', $datosUsuario);
                $this->redirigir('admin/agregar-empleado');
            }

            if ($this->modeloUsuario->correoExiste($datosUsuario['correo'])) {
                Sesion::crearMensaje('error', 'El correo ya está registrado.');
                Sesion::crear('old_agregar_empleado', $datosUsuario);
                $this->redirigir('admin/agregar-empleado');
            }

            try {
                $datosUsuario['password'] = Seguridad::encriptarPassword($datosUsuario['password']);
                $id_usuario = $this->modeloUsuario->registrar($datosUsuario);

                $this->modeloEmpleado->crear([
                    'id_usuario' => $id_usuario,
                    'cargo' => $datosUsuario['cargo'],
                    'especialidad' => $datosUsuario['especialidad'],
                ]);

                Sesion::crearMensaje('exito', 'Empleado agregado correctamente.');
                $this->redirigir('admin/empleados');
            } catch (Exception $e) {
                Sesion::crearMensaje('error', 'Error al agregar el empleado: ' . $e->getMessage());
                Sesion::crear('old_agregar_empleado', $datosUsuario);
                $this->redirigir('admin/agregar-empleado');
            }
        }

        $datos = [
            'errores' => Sesion::flash('errores_agregar_empleado') ?? [],
            'old' => Sesion::flash('old_agregar_empleado') ?? []
        ];

        $this->vista('admin/empleados/agregar', $datos);
    }

    /**
     * Editar empleado
     */
    public function editarEmpleado() {
        $this->verificarRol(ROL_ADMIN);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirigir('admin/empleados');
        }

        $empleado = $this->modeloEmpleado->obtenerConDatos($id);

        if (!$empleado) {
            Sesion::crearMensaje('error', 'Empleado no encontrado.');
            $this->redirigir('admin/empleados');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datosEmpleado = [
                'nombres' => $_POST['nombres'] ?? '',
                'apellidos' => $_POST['apellidos'] ?? '',
                'correo' => $_POST['correo'] ?? '',
                'cargo' => $_POST['cargo'] ?? '',
                'especialidad' => $_POST['especialidad'] ?? '',
                'estado' => $_POST['estado'] ?? ESTADO_ACTIVO,
            ];

            $validador = new Validador($datosEmpleado);
            $validador->requerido('nombres', 'Los nombres son requeridos')
                      ->soloLetras('nombres', 'Los nombres solo pueden contener letras y espacios')
                      ->maximo('nombres', 50, 'Los nombres no pueden exceder 50 caracteres')
                      ->requerido('apellidos', 'Los apellidos son requeridos')
                      ->soloLetras('apellidos', 'Los apellidos solo pueden contener letras y espacios')
                      ->maximo('apellidos', 50, 'Los apellidos no pueden exceder 50 caracteres')
                      ->requerido('correo', 'El correo es requerido')
                      ->correo('correo', 'El correo no es válido')
                      ->requerido('cargo', 'El cargo es requerido')
                      ->soloLetras('cargo', 'El cargo solo puede contener letras y espacios')
                      ->maximo('cargo', 100, 'El cargo no puede exceder 100 caracteres')
                      ->requerido('especialidad', 'La especialidad es requerida')
                      ->soloLetras('especialidad', 'La especialidad solo puede contener letras y espacios')
                      ->maximo('especialidad', 100, 'La especialidad no puede exceder 100 caracteres');

            if ($validador->hayErrores()) {
                Sesion::crearMensaje('error', 'Error en los datos del empleado. Revise los campos.');
                Sesion::crear('errores_editar_empleado', $validador->errores());
                Sesion::crear('old_editar_empleado', $datosEmpleado);
                $this->redirigir('admin/editar-empleado?id=' . $id);
            }

            try {
                $this->modeloEmpleado->actualizar($id, $datosEmpleado);
                Sesion::crearMensaje('exito', 'Empleado actualizado correctamente.');
                $this->redirigir('admin/empleados');
            } catch (Exception $e) {
                Sesion::crearMensaje('error', 'Error al actualizar: ' . $e->getMessage());
            }
        }

        $datos = ['empleado' => $empleado];
        $this->vista('admin/empleados/editar', $datos);
    }

    /**
     * Ver empleado
     */
    public function verEmpleado() {
        $this->verificarRol(ROL_ADMIN);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirigir('admin/empleados');
        }

        $empleado = $this->modeloEmpleado->obtenerConDatos($id);

        if (!$empleado) {
            Sesion::crearMensaje('error', 'Empleado no encontrado.');
            $this->redirigir('admin/empleados');
        }

        $datos = ['empleado' => $empleado];
        $this->vista('admin/empleados/ver', $datos);
    }

    /**
     * Eliminar empleado
     */
    public function eliminarEmpleado() {
        $this->verificarRol(ROL_ADMIN);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirigir('admin/empleados');
        }

        $empleado = $this->modeloEmpleado->obtenerConDatos($id);

        if (!$empleado) {
            Sesion::crearMensaje('error', 'Empleado no encontrado.');
            $this->redirigir('admin/empleados');
        }

        try {
            $this->modeloUsuario->eliminar($empleado['id_usuario']);
            Sesion::crearMensaje('eliminado', 'Empleado eliminado correctamente.');
            $this->redirigir('admin/empleados');
        } catch (Exception $e) {
            Sesion::crearMensaje('error', 'Error al eliminar: ' . $e->getMessage());
            $this->redirigir('admin/empleados');
        }
    }

    /**
     * Listar solicitudes de proyectos
     */
    public function solicitudes() {
        $this->verificarRol(ROL_ADMIN);

        $datos = [
            'solicitudes' => $this->modeloSolicitud->obtenerTodas(),
        ];

        $this->vista('admin/solicitudes/listar', $datos);
    }

    /**
     * Revisar y cotizar solicitud
     */
    public function cotizarSolicitud() {
        $this->verificarRol(ROL_ADMIN);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirigir('admin/solicitudes');
        }

        $solicitud = $this->modeloSolicitud->obtenerConDatos($id);

        if (!$solicitud) {
            Sesion::crearMensaje('error', 'Solicitud no encontrada.');
            $this->redirigir('admin/solicitudes');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $precio = $_POST['precio'] ?? 0;

            $validador = new Validador(['precio' => $precio]);
            $validador->requerido('precio', 'El precio es requerido')
                      ->numerico('precio', 'El precio debe ser un número válido');

            if ($validador->hayErrores()) {
                Sesion::crearMensaje('error', 'Error: precio no válido.');
                $this->redirigir('admin/cotizar-solicitud?id=' . $id);
            }

            try {
                $this->modeloSolicitud->cotizar($id, $precio);
                Sesion::crearMensaje('exito', 'Solicitud cotizada. El cliente recibirá la propuesta.');
                $this->redirigir('admin/solicitudes');
            } catch (Exception $e) {
                Sesion::crearMensaje('error', 'Error: ' . $e->getMessage());
            }
        }

        $datos = ['solicitud' => $solicitud];
        $this->vista('admin/solicitudes/cotizar', $datos);
    }

    /**
     * Listar proyectos
     */
    public function proyectos() {
        $this->verificarRol(ROL_ADMIN);

        $datos = [
            'proyectos' => $this->modeloProyecto->obtenerTodosConDatos(),
        ];

        $this->vista('admin/proyectos/listar', $datos);
    }

    /**
     * Crear proyecto desde solicitud aceptada
     */
    public function crearProyecto() {
        $this->verificarRol(ROL_ADMIN);
        $id_solicitud = $_GET['id_solicitud'] ?? null;

        if (!$id_solicitud) {
            $this->redirigir('admin/solicitudes');
        }

        $solicitud = $this->modeloSolicitud->obtenerConDatos($id_solicitud);

        if (!$solicitud || $solicitud['estado'] !== ESTADO_SOLICITUD_ACEPTADO) {
            Sesion::crearMensaje('error', 'No se puede crear proyecto de esta solicitud.');
            $this->redirigir('admin/solicitudes');
        }

        if (!empty($solicitud['id_proyecto'])) {
            Sesion::crearMensaje('error', 'Ya existe un proyecto creado para esta solicitud.');
            $this->redirigir('admin/ver-proyecto?id=' . $solicitud['id_proyecto']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'id_solicitud' => $id_solicitud,
                'titulo' => $solicitud['titulo'],
                'descripcion' => $solicitud['descripcion'],
                'prioridad' => $_POST['prioridad'] ?? PRIORIDAD_MEDIA,
                'precio' => $solicitud['precio_propuesto'],
                'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
                'fecha_entrega' => $_POST['fecha_entrega'] ?? null,
            ];

            try {
                $id_proyecto = $this->modeloProyecto->crear($datos);
                Sesion::crearMensaje('exito', 'Proyecto creado. Ahora asigna empleados.');
                $this->redirigir('admin/asignar-empleados?id_proyecto=' . $id_proyecto);
            } catch (Exception $e) {
                Sesion::crearMensaje('error', 'Error: ' . $e->getMessage());
            }
        }

        $datos = ['solicitud' => $solicitud];
        $this->vista('admin/proyectos/crear', $datos);
    }

    /**
     * Asignar empleados a proyecto
     */
    public function asignarEmpleados() {
        $this->verificarRol(ROL_ADMIN);
        $id_proyecto = $_GET['id_proyecto'] ?? null;

        if (!$id_proyecto) {
            $this->redirigir('admin/proyectos');
        }

        $proyecto = $this->modeloProyecto->obtenerConDatos($id_proyecto);

        if (!$proyecto) {
            Sesion::crearMensaje('error', 'Proyecto no encontrado.');
            $this->redirigir('admin/proyectos');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_empleado = $_POST['id_empleado'] ?? null;

            if (!$id_empleado) {
                Sesion::crearMensaje('error', 'Selecciona un empleado.');
                $this->redirigir('admin/asignar-empleados?id_proyecto=' . $id_proyecto);
            }

            try {
                $this->modeloAsignacion->asignar([
                    'id_proyecto' => $id_proyecto,
                    'id_empleado' => $id_empleado,
                    'rol_en_proyecto' => $_POST['rol_en_proyecto'] ?? '',
                ]);

                // Actualizar estado del proyecto
                $this->modeloProyecto->actualizarEstado($id_proyecto, ESTADO_PROYECTO_EN_PROGRESO);

                Sesion::crearMensaje('exito', 'Empleado asignado correctamente.');
                $this->redirigir('admin/asignar-empleados?id_proyecto=' . $id_proyecto);
            } catch (Exception $e) {
                Sesion::crearMensaje('error', 'Error: ' . $e->getMessage());
            }
        }

        // Contar asignaciones actuales para esta vista
        $totalAsignados = $this->modeloAsignacion->obtenerEmpleadosDelProyecto($id_proyecto);

        $datos = [
            'proyecto' => $proyecto,
            'empleados' => $this->modeloEmpleado->obtenerTodosConDatos(),
            'asignaciones' => $this->modeloAsignacion->obtenerDelProyecto($id_proyecto),
            'maxAsignados' => ($totalAsignados >= 3),
        ];

        $this->vista('admin/proyectos/asignar-empleados', $datos);
    }

    /**
     * Remover asignación
     */
    public function removerAsignacion() {
        $this->verificarRol(ROL_ADMIN);
        $id_asignacion = $_GET['id'] ?? null;
        $id_proyecto = $_GET['id_proyecto'] ?? null;

        if (!$id_asignacion || !$id_proyecto) {
            $this->redirigir('admin/proyectos');
        }

        try {
            $this->modeloAsignacion->eliminar($id_asignacion);
            Sesion::crearMensaje('exito', 'Asignación removida.');
            $this->redirigir('admin/asignar-empleados?id_proyecto=' . $id_proyecto);
        } catch (Exception $e) {
            Sesion::crearMensaje('error', 'Error: ' . $e->getMessage());
            $this->redirigir('admin/asignar-empleados?id_proyecto=' . $id_proyecto);
        }
    }

    /**
     * Ver detalles del proyecto
     */
    public function verProyecto() {
        $this->verificarRol(ROL_ADMIN);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirigir('admin/proyectos');
        }

        $proyecto = $this->modeloProyecto->obtenerConDatos($id);

        if (!$proyecto) {
            Sesion::crearMensaje('error', 'Proyecto no encontrado.');
            $this->redirigir('admin/proyectos');
        }

        $datos = [
            'proyecto' => $proyecto,
            'asignaciones' => $this->modeloAsignacion->obtenerDelProyecto($id),
            'avances' => $this->modeloAvance->obtenerHistorial($id),
        ];

        $this->vista('admin/proyectos/ver', $datos);
    }
}
