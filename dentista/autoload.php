<?php
error_reporting(E_ALL & ~E_WARNING);
session_start();

spl_autoload_register(function ($clase) {
    // Convertimos \ en / para que Linux entienda las carpetas
    $ruta = str_replace('\\', '/', $clase);

    // Buscamos el archivo
    $archivo = __DIR__ . '/' . $ruta . '.php';

    // Cargamos
    if (file_exists($archivo)) {
        require_once $archivo;
    }
});


//tengo entendido que si no se hace así aunque pueda ir en windows
//al estar usando uin contenedor docker (linux) no iría.
//como me estaba dando fallo he decicido recurrir a la IA para buscar el error
