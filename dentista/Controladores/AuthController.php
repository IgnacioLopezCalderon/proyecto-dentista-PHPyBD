<?php

namespace Controladores;

use Clases\Request;
use Clases\Auth;
use Clases\Sesion;

class AuthController extends BaseController
{
    /**
     * @return void
     */
    public function login(): void
    {
        // con este if antes puedo ver si está logueado
        if (Auth::user()):
            Request::redirectToRoute("inicio");
        endif;


        if (Request::isMethod("get")):

            // Si hay error en la URL, lo mostramos (buscado en gemini)
            $error = Request::get("error") !== null ? "Credenciales incorrectas" : null;

            $this->render("login.twig", [
                "error"  => $error
            ]);

        else:
            // para loguearse solamente me hace falta gmail y contraseña
            $email = Request::get("email");
            $password = Request::get("password");

            //intentamos hacer login con los datos recogidos
            if (Auth::login($email, $password)):
                Request::redirectToRoute("inicio");
            else:
                // Si falla, recargamos con ?error
                //esto se hace así porque estoy usando twig en el proyecto
                Request::redirect("/login?error");
            endif;

        endif;
    }

    public function logout(): void
    {
        Sesion::close();
        Request::redirectToRoute("login");
    }
}