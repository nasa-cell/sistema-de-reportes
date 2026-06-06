<?php
/**
 * Clase para gestionar las vistas
 */

class Vista {
    /**
     * Cargar una vista con datos
     */
    public function cargar($nombre, $datos = []) {
        // Extraer los datos para hacerlos disponibles en la vista
        extract($datos);

        $rutaVista = __DIR__ . '/../vistas/' . $nombre . '.php';

        if (!file_exists($rutaVista)) {
            die("La vista '$nombre' no existe en la ruta: $rutaVista");
        }

        ob_start();
        include $rutaVista;
        $contenido = ob_get_clean();

        return $contenido;
    }

    /**
     * Renderizar una vista completa
     */
    public function renderizar($nombre, $datos = []) {
        echo $this->cargar($nombre, $datos);
    }
}
