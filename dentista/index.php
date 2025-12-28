<?php

//buscado en gemini
require_once 'autoload.php';
require_once __DIR__ . '/vendor/autoload.php';

use Clases\Request;

// iniciamos sesión
// Clases\Sesion::init(); // Descomenta esto si ya tienes la clase Sesion

// Recogemos las variables que nos manda el .htaccess
// Si no hay nada, asumimos que estamos en Registro
$controlador = Request::get('controlador') ?? 'Register';
$metodo      = Request::get('metodo') ?? 'mostrar';

// 3. Formamos el nombre completo de la clase
// Ej: "Controladores\RegisterController"
$clase = "Controladores\\" . ucfirst($controlador) . "Controller";

// 4. Cargamos el controlador
if (class_exists($clase)) {
    $instancia = new $clase();

    if (method_exists($instancia, $metodo)) {
        $instancia->$metodo();
    } else {
        // Fallback simple
        echo "Error 404: El método '$metodo' no existe en '$controlador'.";
    }
} else {
    // Fallback simple
    echo "Error 404: El controlador '$clase' no existe.";
}