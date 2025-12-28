<?php

namespace Controladores;

use Clases\Auth;
use Clases\Request;
use Modelos\Cita;

class CitaController extends BaseController
{
    public function listar(): void
    {
        $usuario = Auth::user();

        if (!$usuario) {
            Request::redirectToRoute("login");
            return;
        }

        $citas = Cita::getByUser($usuario);

        $this->render("ver.twig", [
            "citas"   => $citas,
            "usuario" => $usuario
        ]);
    }
}