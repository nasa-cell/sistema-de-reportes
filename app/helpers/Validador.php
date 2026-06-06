<?php
/**
 * Clase para validación de datos
 */

class Validador {
    private $errores = [];
    private $datos = [];

    public function __construct($datos = []) {
        $this->datos = $datos;
    }

    /**
     * Validar que un campo no esté vacío
     */
    public function requerido($campo, $mensaje = null) {
        if (empty($this->datos[$campo] ?? '')) {
            $this->errores[$campo] = $mensaje ?? ucfirst($campo) . ' es requerido';
        }
        return $this;
    }

    /**
     * Validar longitud mínima
     */
    public function minimo($campo, $minimo, $mensaje = null) {
        if (!empty($this->datos[$campo] ?? '') && strlen($this->datos[$campo]) < $minimo) {
            $this->errores[$campo] = $mensaje ?? ucfirst($campo) . ' debe tener al menos ' . $minimo . ' caracteres';
        }
        return $this;
    }

    /**
     * Validar longitud máxima
     */
    public function maximo($campo, $maximo, $mensaje = null) {
        if (!empty($this->datos[$campo] ?? '') && strlen($this->datos[$campo]) > $maximo) {
            $this->errores[$campo] = $mensaje ?? ucfirst($campo) . ' no puede exceder ' . $maximo . ' caracteres';
        }
        return $this;
    }

    /**
     * Validar que el campo contenga sólo letras y espacios
     */
    public function soloLetras($campo, $mensaje = null) {
        if (!empty($this->datos[$campo] ?? '') && !preg_match('/^[\p{L} ]+$/u', $this->datos[$campo])) {
            $this->errores[$campo] = $mensaje ?? ucfirst($campo) . ' solo puede contener letras y espacios';
        }
        return $this;
    }

    /**
     * Validar que el campo sea alfanumérico
     */
    public function alfanumerico($campo, $mensaje = null) {
        if (!empty($this->datos[$campo] ?? '') && !preg_match('/^[A-Za-z0-9_]+$/', $this->datos[$campo])) {
            $this->errores[$campo] = $mensaje ?? ucfirst($campo) . ' debe contener solo letras, números o guiones bajos';
        }
        return $this;
    }

    /**
     * Validar formato de teléfono opcional
     */
    public function telefono($campo, $mensaje = null) {
        if (!empty($this->datos[$campo] ?? '') && !preg_match('/^[0-9+\s()\-]+$/', $this->datos[$campo])) {
            $this->errores[$campo] = $mensaje ?? ucfirst($campo) . ' no es válido';
        }
        return $this;
    }

    /**
     * Validar teléfono con cantidad exacta de dígitos (solo números)
     */
    public function telefonoExacto($campo, $digitos = 9, $mensaje = null) {
        if (!empty($this->datos[$campo] ?? '')) {
            $valor = $this->datos[$campo];
            if (!preg_match('/^\d{' . intval($digitos) . '}$/', $valor)) {
                $this->errores[$campo] = $mensaje ?? ucfirst($campo) . ' debe contener exactamente ' . $digitos . ' dígitos y solo números';
            }
        }
        return $this;
    }

    /**
     * Validar contraseña con letras y números
     */
    public function contrasenaFuerte($campo, $mensaje = null) {
        if (!empty($this->datos[$campo] ?? '') && !preg_match('/^(?=.*[A-Za-z])(?=.*\d).+$/', $this->datos[$campo])) {
            $this->errores[$campo] = $mensaje ?? 'La contraseña debe incluir letras y números';
        }
        return $this;
    }

    /**
     * Validar formato de correo
     */
    public function correo($campo, $mensaje = null) {
        if (!empty($this->datos[$campo] ?? '') && !filter_var($this->datos[$campo], FILTER_VALIDATE_EMAIL)) {
            $this->errores[$campo] = $mensaje ?? 'El correo de ' . $campo . ' no es válido';
        }
        return $this;
    }

    /**
     * Validar que dos campos sean iguales
     */
    public function igual($campo, $campoCampo2, $mensaje = null) {
        if (($this->datos[$campo] ?? '') !== ($this->datos[$campoCampo2] ?? '')) {
            $this->errores[$campo] = $mensaje ?? ucfirst($campo) . ' no coincide';
        }
        return $this;
    }

    /**
     * Validar que un campo sea numérico
     */
    public function numerico($campo, $mensaje = null) {
        if (!empty($this->datos[$campo] ?? '') && !is_numeric($this->datos[$campo])) {
            $this->errores[$campo] = $mensaje ?? ucfirst($campo) . ' debe ser numérico';
        }
        return $this;
    }

    /**
     * Validar que un campo sea una fecha
     */
    public function fecha($campo, $formato = 'Y-m-d', $mensaje = null) {
        if (!empty($this->datos[$campo] ?? '')) {
            $d = DateTime::createFromFormat($formato, $this->datos[$campo]);
            if (!$d || $d->format($formato) != $this->datos[$campo]) {
                $this->errores[$campo] = $mensaje ?? 'El formato de ' . $campo . ' debe ser ' . $formato;
            }
        }
        return $this;
    }

    /**
     * Obtener los errores
     */
    public function errores() {
        return $this->errores;
    }

    /**
     * Verificar si hay errores
     */
    public function hayErrores() {
        return !empty($this->errores);
    }

    /**
     * Obtener un error específico
     */
    public function obtenerError($campo) {
        return $this->errores[$campo] ?? null;
    }
}
