<?php

    namespace Modelos ;
	
	use Clases\Database;
	use Clases\Sesion ;
	
	class Usuario
    {

        private int $id_usuario ; //me daba error porque debe llamarse igual que la columna de la BD
		public int $id {
			get => $this->id_usuario ;
		}
		
        public private(set) string $nombre;
        private string $apellido1;
        private ?string $apellido2; // "?" porque me daba error cuando era null

	    public private(set) string $email;
        private string $password;
        public private(set) string $tipo_usuario;







        /**
		 * @param string $password
		 * @return bool
		 */
		private function verify(string $password): bool
		{
			return password_verify($password, $this->password) ;
		}



	    /**
	     * @return array
	     */
/*		public function tareas(): array
		{
			return Tarea::getByUser($this) ;
		}*/



		/**
		 * @param string $email
		 * @param string $password
		 * @return Usuario|null
		 */
		public static function getByEmailAndPassword(string $email, string $password): Usuario|false
		{
            //informandome he visto que :correo sirve como alias
            //para que no se pueda escribir un correo falso con instrucciones dentro
			$pdo = Database::connect() ;
			$stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = :correo ;") ;
			$stmt->execute([ ":correo" => $email, ]) ;
			
			# recuperamos usuario
			$usuario = $stmt->fetchObject(Usuario::class) ;
			
			if (is_object($usuario)):
				if ($usuario->verify($password)):
					return $usuario ;
				endif ;
			endif ;
			
			return false ;
		}
		
		/**
		 * @param int $id
		 * @return Usuario|false
		 */
		public static function getById(int $id): Usuario|false
		{
			$pdo = Database::connect() ;
			$stmt = $pdo->prepare("SELECT * FROM usuario WHERE id_usuario = :id") ;
			$stmt->execute([ ":id" => $id ]) ;
			
			# devolvemos el usuario
			return $stmt->fetchObject(Usuario::class) ;
		}
	    
	    /**
         * @return string
         */
        public function __toString(): string
        {
            return "$this->id $this->nombre $this->apellido1, $this->email<br/>" ;
        }




        //Crear usuario

        public static function crearUsuario(array $datos)
        {
            //he decidido hacer un try por si falla el registro y para que se pueda ver cual es el fallo
            try {
                $pdo = Database::connect();

                $stmt = $pdo->prepare("INSERT INTO usuario (nombre, apellido1, apellido2, email, password, tipo_usuario)
                                    VALUES (:nombre, :apellido1, :apellido2, :email, :password, :tipo_usuario)");

                // Si el apellido2 está vacío, le ponemos null
                $apellido2 = !empty($datos['apellido2']) ? $datos['apellido2'] : null;

                // Encriptarmos la contraseña
                $passHash = password_hash($datos['password'], PASSWORD_BCRYPT);

                $stmt->execute([
                    ":nombre"       => $datos['nombre'],
                    ":apellido1"    => $datos['apellido1'],
                    ":apellido2"    => $apellido2,
                    ":email"        => $datos['email'],
                    ":password"     => $passHash,
                    ":tipo_usuario" => $datos['tipo_usuario'] ?? 'cliente'
                ]);

            } catch (\PDOException $e) {
                // Código 1062 significa "Entrada duplicada" (email repetido)
                //esta parte ha sido buscada en gemini
                //si el error es igual al 1062 significa que el email que se está usando ya está en la BD
                if ($e->errorInfo[1] == 1062) {
                    echo "<h3 style='color:red'>Error: El correo '" . $datos['email'] . "' ya está registrado.</h3>";
                    echo "<p><a href='/'>Volver al formulario de registro</a></p>";
                } else {
                    // Si el error es diferente he decidido poner un mensaje genérico
                    echo "<h3 style='color:red'>Error grave de Base de Datos:</h3>";
                    echo "<p>" . $e->getMessage() . "</p>";
                    die();
                }
            }
        }


        public function isAdmin(Usuario $usuario)
        {
            //si le pregunto el roll a $usuario se podría modificar, lo mejor es comprobarlo desde la base de datos.
            $usuarioDB = $this::getById($usuario->id);

            if ($usuarioDB->tipo_usuario === "administrador"){
                return true;
            }
            return false;
        }

    }
