<?php
/**
 * Controlador para inicio
 */

class ControladorInicio extends Controlador {
    public function index() {
        Sesion::iniciar();
        
        if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol'])) {
            Sesion::destruir();
            $this->redirigir('autenticacion/iniciar-sesion');
        }

        // Redirigir según el rol
        switch ($_SESSION['rol']) {
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
                $this->redirigir('autenticacion/iniciar-sesion');
        }
    }
}
