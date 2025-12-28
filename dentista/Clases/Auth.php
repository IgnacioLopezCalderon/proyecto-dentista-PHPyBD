<?php
//este archivo lo he dejado igual al original, porque no es necesario cambiar nada
namespace Clases;

use Clases\Sesion;
use Modelos\Usuario;

final class Auth
{
    /**
     *
     * @param string $email
     * @param string $pass
     * @return bool
     */
    public static function login(string $email, string $pass): bool
    {
        $usuario = Usuario::getByEmailAndPassword($email, $pass);

        # Si es un objeto es porque el usuario y contraseña son correctos
        # iniciamos la sesión
        if (is_object($usuario)) {
            Sesion::init($usuario);
        }

        return is_object($usuario);
    }

    /**
     * @return Usuario|false
     */
    public static function user(): Usuario|false
    {
        # Si hay una sesión activa recuperamos el usuario de la BD
        if (Sesion::active()):
            return Usuario::getById(Sesion::get("id"));
        endif;

        return false;
    }
}