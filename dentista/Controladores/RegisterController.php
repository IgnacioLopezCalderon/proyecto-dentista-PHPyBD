<?php

namespace Controladores ;

use Clases\Request ;
use Clases\Auth;
use Modelos\Usuario ;

class RegisterController extends BaseController
{
    /**
     * Muestra el formulario de registro
     * O muestra el menú
     * @return void
     */
    public function mostrar(): void
    {
        // si el usuario está registrado lo enviamos a la página principal
        // la página va a depender de si es un cliente o un administrador
        if ($usuario = Auth::user()) {

            // si el usuario es administrador
            if ($usuario->isAdmin($usuario)) {
                // No pasamos argumentos, porque el texto es fijo ("Administrador")
                $this->render("admin.twig");
            }
            // si el usuario es cliente
            else {
                // Pasamos $usuario SOLO para poner "Bienvenido Pepe"
                $this->render("cliente.twig", [
                    "usuario" => $usuario
                ]);
            }

        }
        // si no está logueado le mandamos a registro
        else {
            $this->render("register.twig");
        }
    }

    /**
     * Procesa el formulario y guarda el usuario
     * @return void
     */
    public function guardar(): void
    {
        // Solo procesamos si viene por POST
        // Sino se vería la contraseña en la URL
        if (Request::isMethod("POST")):



            $datos = [
                "nombre"       => Request::get("nombre"),
                "apellido1"    => Request::get("apellido1"),
                "apellido2"    => Request::get("apellido2"),
                "email"        => Request::get("email"),
                "password"     => Request::get("password"),
                "tipo_usuario" => Request::get("tipo_usuario"),
            ] ;

            Usuario::crearUsuario($datos) ;

            // Si todo va bien vamos a login.
            Request::redirectToRoute("login") ;

        endif ;
    }
}