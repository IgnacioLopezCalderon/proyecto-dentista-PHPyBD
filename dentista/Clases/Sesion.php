<?php

namespace Clases;

use Modelos\Usuario;

final class Sesion
{
    const MAX_TIEMPO = 1800;


    /**
     * @return void
     * buscado en gemini, función para asegurarme de que
     * la sesión siempre esté iniciada
     */
    private static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function update(): void
    {
        self::set("tiempo", time());
    }

    public static function init(Usuario $usuario): void
    {

        self::set("id", $usuario->id);
        self::set("email", $usuario->email);

        // Tenia el mensamiento de guardar el nombre tambien
        // Al principio me daba fallo
        // Al poner el ratón encima de nombre me decia que la propiedad era privada
        // He decidido poner visibilidad pública, pero private(set)
        self::set("nombre", $usuario->nombre);

        self::update();
    }

    public static function active(): bool
    {

        self::start();

        //Al tener self:start() en get podría no usarlo en esta funcion
        //pero además de tener que hacer un if muy lioso habría 2 return
        return (session_status() === PHP_SESSION_ACTIVE) &&
            (self::get("id")) &&
            ((time() - self::get("tiempo")) <= self::MAX_TIEMPO);
    }

    public static function set(string $clave, mixed $valor): void
    {
        self::start();
        $_SESSION[$clave] = $valor;
    }

    public static function get(string $clave): mixed
    {
        self::start();
        return $_SESSION[$clave] ?? null;
    }

    public static function close(): void
    {
        // cerramos la sesión
        $_SESSION = [] ;
        session_destroy() ;
    }
}