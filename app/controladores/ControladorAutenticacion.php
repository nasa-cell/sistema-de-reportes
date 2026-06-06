<?php
/**
 * Controlador para autenticación
 */

class ControladorAutenticacion extends Controlador {
    private $modeloUsuario;
    private $modeloCliente;
    private $modeloEmpleado;

    public function __construct() {
        parent::__construct();
        $this->modeloUsuario = new ModeloUsuario();
        $this->modeloCliente = new ModeloCliente();
        $this->modeloEmpleado = new ModeloEmpleado();
    }

    /**
     * Mostrar formulario de registro
     */
    public function registro() {
        Sesion::iniciar();
        
        if (isset($_SESSION['id_usuario']) && isset($_SESSION['rol'])) {
            $this->redirigir('inicio');
        }

        $datos = [
            'errores' => Sesion::flash('errores_registro') ?? [],
            'old' => Sesion::flash('old_registro') ?? []
        ];

        $this->vista('autenticacion/registro', $datos);
    }

    /**
     * Procesar registro
     */
    public function procesarRegistro() {
        Sesion::iniciar();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('autenticacion/registro');
        }

        $datos = [
            'nombres' => $_POST['nombres'] ?? '',
            'apellidos' => $_POST['apellidos'] ?? '',
            'usuario' => $_POST['usuario'] ?? '',
            'correo' => $_POST['correo'] ?? '',
            'password' => $_POST['password'] ?? '',
            'confirmar_password' => $_POST['confirmar_password'] ?? '',
            'empresa' => $_POST['empresa'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
        ];

        // Validar
        $validador = new Validador($datos);
        $validador->requerido('nombres', 'El nombre es requerido')
                  ->soloLetras('nombres', 'Los nombres solo pueden contener letras y espacios')
                  ->maximo('nombres', 50, 'Los nombres no pueden exceder 50 caracteres')
                  ->requerido('apellidos', 'El apellido es requerido')
                  ->soloLetras('apellidos', 'Los apellidos solo pueden contener letras y espacios')
                  ->maximo('apellidos', 50, 'Los apellidos no pueden exceder 50 caracteres')
                  ->requerido('usuario', 'El usuario es requerido')
                  ->minimo('usuario', 4, 'El usuario debe tener al menos 4 caracteres')
                  ->alfanumerico('usuario', 'El usuario solo puede contener letras, números y guiones bajos')
                  ->maximo('usuario', 30, 'El usuario no puede exceder 30 caracteres')
                  ->requerido('correo', 'El correo es requerido')
                  ->correo('correo', 'El correo no es válido')
                  ->maximo('empresa', 100, 'La empresa no puede exceder 100 caracteres')
                  ->telefonoExacto('telefono', 9, 'El teléfono debe contener exactamente 9 dígitos')
                  ->requerido('password', 'La contraseña es requerida')
                  ->minimo('password', 6, 'La contraseña debe tener al menos 6 caracteres')
                  ->contrasenaFuerte('password', 'La contraseña debe incluir letras y números')
                  ->igual('password', 'confirmar_password', 'Las contraseñas no coinciden');

        if ($validador->hayErrores()) {
            Sesion::crearMensaje('error', 'Error en los datos. Verifique los campos.');
            Sesion::crear('errores_registro', $validador->errores());
            Sesion::crear('old_registro', $datos);
            $this->redirigir('autenticacion/registro');
        }

        // Verificar si el usuario o correo ya existen
        if ($this->modeloUsuario->usuarioExiste($datos['usuario'])) {
            Sesion::crearMensaje('error', 'El usuario ya existe. Intente con otro.');
            $this->redirigir('autenticacion/registro');
        }

        if ($this->modeloUsuario->correoExiste($datos['correo'])) {
            Sesion::crearMensaje('error', 'El correo ya está registrado.');
            $this->redirigir('autenticacion/registro');
        }

        try {
            // Encriptar contraseña
            $datos['password'] = Seguridad::encriptarPassword($datos['password']);

            // Registrar usuario
            $id_usuario = $this->modeloUsuario->registrar($datos);

            // Crear perfil de cliente automáticamente
            $this->modeloCliente->crear([
                'id_usuario' => $id_usuario,
                'empresa' => $datos['empresa'],
                'telefono' => $datos['telefono'],
            ]);

            Sesion::crearMensaje('exito', 'Registro completado. Por favor inicia sesión.');
            $this->redirigir('autenticacion/iniciar-sesion');
        } catch (Exception $e) {
            Sesion::crearMensaje('error', 'Error al registrar: ' . $e->getMessage());
            $this->redirigir('autenticacion/registro');
        }
    }

    /**
     * Mostrar formulario de login
     */
    public function iniciarSesion() {
        Sesion::iniciar();
        
        if (isset($_SESSION['id_usuario']) && isset($_SESSION['rol'])) {
            $this->redirigir('inicio');
        }

        $datos = [];

        $this->vista('autenticacion/iniciar-sesion', $datos);
    }

    /**
     * Procesar login
     */
    public function procesarLogin() {
        Sesion::iniciar();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('autenticacion/iniciar-sesion');
        }

        $usuario = $_POST['usuario'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($usuario) || empty($password)) {
            Sesion::crearMensaje('error', 'Usuario y contraseña requeridos.');
            $this->redirigir('autenticacion/iniciar-sesion');
        }

        $usuarioEncontrado = $this->modeloUsuario->obtenerPorUsuario($usuario);
        $rolSeleccionado = isset($_POST['rol']) ? strtoupper(trim($_POST['rol'])) : ROL_CLIENTE;

        if (!$usuarioEncontrado || !Seguridad::verificarPassword($password, $usuarioEncontrado['password'])) {
            Sesion::crearMensaje('error', 'Usuario o contraseña incorrectos.');
            $this->redirigir('autenticacion/iniciar-sesion');
        }

        if ($usuarioEncontrado['estado'] !== ESTADO_ACTIVO) {
            Sesion::crearMensaje('error', 'Tu cuenta ha sido desactivada.');
            $this->redirigir('autenticacion/iniciar-sesion');
        }

        if ($usuarioEncontrado['rol'] !== $rolSeleccionado) {
            $etiquetaRol = $rolSeleccionado === ROL_EMPLEADO ? 'Empleado' : ($rolSeleccionado === ROL_ADMIN ? 'Admin' : 'Cliente');
            Sesion::crearMensaje('error', 'Este usuario no puede iniciar sesión como ' . $etiquetaRol . '. Selecciona el rol correcto.');
            $this->redirigir('autenticacion/iniciar-sesion');
        }

        // Crear sesión
        $_SESSION['id_usuario'] = $usuarioEncontrado['id_usuario'];
        $_SESSION['usuario'] = $usuarioEncontrado['usuario'];
        $_SESSION['rol'] = $usuarioEncontrado['rol'];
        $_SESSION['nombres'] = $usuarioEncontrado['nombres'];
        $_SESSION['apellidos'] = $usuarioEncontrado['apellidos'];
        $_SESSION['correo'] = $usuarioEncontrado['correo'];

        // Redirigir según rol
        switch ($usuarioEncontrado['rol']) {
            case ROL_ADMIN:
                $this->redirigir('admin/inicio');
                break;
            case ROL_EMPLEADO:
                $this->redirigir('empleado/inicio');
                break;
            case ROL_CLIENTE:
                $this->redirigir('cliente/inicio');
                break;
            default:
                $this->redirigir('inicio');
        }
    }

    /**
     * Cerrar sesión
     */
    public function cerrarSesion() {
        Sesion::iniciar();
        Sesion::destruir();
        $this->redirigir('autenticacion/iniciar-sesion');
    }
}
