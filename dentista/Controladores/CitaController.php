<?php

namespace Controladores;

use Clases\Auth;
use Clases\Request;
use Modelos\Cita;
use Modelos\Servicio;

class CitaController extends BaseController
{
    public function listar(): void
    {
        $usuario = Auth::user();

        if (!$usuario) {
            Request::redirectToRoute("login");
            //el return es necesario, sino me da error porque siguen ejecutandose las lineas de abajo
            return;
        }

        $citas = Cita::getByUser($usuario);

        $this->render("ver.twig", [
            "citas"   => $citas,
            "usuario" => $usuario
        ]);
    }



    public function pedirCita()
    {
        $usuario = Auth::user();

        if ($usuario) {



            //comprueba si el post está vacio.
            //si no lo está guardamos la información y creamos la tarea
            if (!empty($_POST)) {

                $fecha = $_POST['fecha'];
                $hora  = $_POST['hora'];
                $idServicio = $_POST['servicio'];

                //como en la base de datos la fecha lo abarca todo...
                $fechaCompleta = $fecha . ' ' . $hora . ':00';

                Cita::create([
                    'id_usuario'  => $usuario->id,
                    'fecha_cita'  => $fechaCompleta,
                    'id_servicio' => $idServicio
                ]);

                Request::redirectToRoute("citas/ver");
                return;
            } else {
                $servicios = Servicio::obtenerServicios();

                $this->render("pedirCita.twig", [
                    "usuario" => $usuario,
                    "servicios" => $servicios
                ]);
            }
        } else {
            request::redirectToRoute("login");
        }
    }





    public function cancelar(): void
    {
        $usuario = Auth::user();

        if (!$usuario) {
            Request::redirectToRoute("login");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $idCita = $_POST['id_cita'];
            Cita::delete($idCita, $usuario->id);
        }

        Request::redirectToRoute("citas/ver");
    }





    public function paginaPrincipal(): void
    {
        $usuario = Auth::user();

        if (!$usuario) {
            Request::redirectToRoute("login");
            return;
        }

        $this->render("cliente.twig", [
            "usuario" => $usuario
        ]);
    }


}