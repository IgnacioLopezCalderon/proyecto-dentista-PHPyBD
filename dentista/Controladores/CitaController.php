<?php

namespace Controladores;

use Clases\Auth;
use Clases\Request;
use Modelos\Cita;
use Modelos\Servicio;
use Modelos\Estado;

class CitaController extends BaseController
{
    public function listar(): void
    {
        $usuario = Auth::user();

        if ($usuario) {
            $citas = Cita::getByUser($usuario);

            $this->render("ver.twig", [
                "citas"   => $citas,
                "usuario" => $usuario
            ]);
        } else {
            Request::redirectToRoute("login");
        }
    }



    public function pedirCita()
    {
        $usuario = Auth::user();

        //comprueba si el post está vacio.
        //si no lo está guardamos la información y creamos la tarea
        if ($usuario) {
            if (!empty($_POST)) {
                $fecha = $_POST['fecha'];
                $hora  = $_POST['hora'];
                $idServicio = $_POST['servicio'];

                $fechaCompleta = $fecha . ' ' . $hora . ':00';

                Cita::create([
                    'id_usuario'  => $usuario->id,
                    'fecha_cita'  => $fechaCompleta,
                    'id_servicio' => $idServicio
                ]);

                Request::redirectToRoute("citas/ver");
            } else {
                $servicios = Servicio::obtenerServicios();

                $this->render("pedirCita.twig", [
                    "usuario"   => $usuario,
                    "servicios" => $servicios
                ]);
            }
        } else {
            Request::redirectToRoute("login");
        }
    }





    public function cancelar(): void
    {
        $usuario = Auth::user();

        if ($usuario) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $idCita = $_POST['id_cita'];
                Cita::delete($idCita, $usuario->id);
            }

            Request::redirectToRoute("citas/ver");
        } else {
            Request::redirectToRoute("login");
        }
    }





    public function paginaPrincipal(): void
    {
        $usuario = Auth::user();

        if ($usuario) {
            $this->render("cliente.twig", [
                "usuario" => $usuario
            ]);
        } else {
            Request::redirectToRoute("login");
        }
    }












    //PARTE DEL ADMINISTRADOR




//Funciones de administrador
    // Listamos las citas que están pendiente de confirmación
    public function listarPendientes(): void
    {
        $usuario = Auth::user();

        if ($usuario && $usuario->isAdmin($usuario)) {
            $citas = Cita::obtenerPendientes();

            $this->render("admin_citas/pendientes.twig", [
                "citas" => $citas
            ]);
        } else {
            Request::redirectToRoute("login");
        }
    }



    //aceptamos una cita que esté pendiente
    public function aceptarCita(): void
    {
        $usuario = Auth::user();

        if ($usuario && $usuario->isAdmin($usuario)) {
            $idCita = Request::get('id');

            if ($idCita) {
                // El 2 corresponde a 'Confirmada' según tu baseDatos.sql
                Cita::cambiarEstado((int)$idCita, 2);
            }

            Request::redirect("/admin/citas/pendientes");
        } else {
            Request::redirectToRoute("login");
        }
    }



    public function rechazarCita(): void
    {
        $usuario = Auth::user();

        if ($usuario && $usuario->isAdmin($usuario)) {
            $idCita = Request::get('id');

            if ($idCita) {
                Cita::cambiarEstado((int)$idCita, 3);
            }

            Request::redirect("/admin/citas/pendientes");
        } else {
            Request::redirectToRoute("login");
        }
    }




//Funciones de listado para el administrador

    public function listarAceptadas(): void
    {
        $usuario = Auth::user();
        if ($usuario && $usuario->isAdmin($usuario)) {

            $fechaSeleccionada = Request::get('fecha') ?? date('Y-m-d');

            $idConfirmada = Estado::getId(Estado::CONFIRMADA);
            $idFinalizada = Estado::getId(Estado::FINALIZADA);

            $citas = Cita::obtenerPorEstadoYFecha([$idConfirmada, $idFinalizada], $fechaSeleccionada);

            $this->render("admin_citas/aceptadas.twig", [
                "citas"             => $citas,
                "fechaSeleccionada" => $fechaSeleccionada,
                "fechaHoy"          => date('Y-m-d')
            ]);
        } else {
            Request::redirectToRoute("login");
        }
    }





    public function listarRechazadas(): void
    {
        $usuario = Auth::user();

        if ($usuario && $usuario->isAdmin($usuario)) {

            $fechaSeleccionada = Request::get('fecha') ?? date('Y-m-d');


            //usamos el mismo metodo que en listarAceptadas
            $citas = Cita::obtenerPorEstadoYFecha(3, $fechaSeleccionada);

            $this->render("admin_citas/rechazadas.twig", [
                "citas"             => $citas,
                "fechaSeleccionada" => $fechaSeleccionada,
                "fechaHoy"          => date('Y-m-d')
            ]);

        } else {
            Request::redirectToRoute("login");
        }
    }



    //me he ayudado con gemini para plantear la idea.
    public function verDetalle(): void
    {
        $usuario = Auth::user();

        if ($usuario && $usuario->isAdmin($usuario)) {

            $idCita = Request::get('id');

            $cita = Cita::obtenerPorId((int)$idCita);

            if ($cita) {
                $serviciosRealizados = Servicio::obtenerPorCita((int)$idCita);

                $catalogoServicios = Servicio::obtenerServicios();

                $this->render("admin_citas/detalle.twig", [
                    "cita"                => $cita,
                    "serviciosRealizados" => $serviciosRealizados,
                    "catalogoServicios"   => $catalogoServicios
                ]);
            } else {
                echo "Cita no encontrada";
            }
        } else {
            Request::redirectToRoute("login");
        }
    }



    //hacemos que el administrador pueda dar como finalizada la cita
    public function finalizarCita(): void
    {
        $usuario = Auth::user();

        if ($usuario && $usuario->isAdmin($usuario)) {
            $idCita = Request::get('id');

            if ($idCita) {

                //el  "4" es el estado "finalizada"
                Cita::cambiarEstado((int)$idCita, 4);
            }

            Request::redirect("/admin/citas/detalle?id=" . $idCita);
        } else {
            Request::redirectToRoute("login");
        }
    }



    //si no l ohago me aparece el html por pantalla
    public function mostrarMenuAdmin(): void
    {
        $usuario = Auth::user();

        if ($usuario && $usuario->isAdmin($usuario)) {
            $this->render("admin.twig", []);
        } else {
            Request::redirectToRoute("login");
        }
    }


}