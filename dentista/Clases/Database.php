<?php

namespace Clases;

final class Database
{
    private const DBHOST = "db";
    private const DBUSER = "root";
    private const DBPASS = "root";
    private const DBNAME = "clinica";

    private function __clone() {}
    private function __construct() {}

    /**
     * @return \PDO|null
     */
    public static function connect(): ?\PDO
    {
        try {
            $dsn = "mysql:host=".self::DBHOST.";dbname=".self::DBNAME.";charset=utf8mb4";

            return \PDO::connect($dsn, self::DBUSER, self::DBPASS);

        } catch(\PDOException $pdoe) {
            die("**ERROR: " . $pdoe->getMessage());
        }
    }
}
