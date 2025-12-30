<?php
	
	namespace Controladores ;
	
	use Clases\Request ;
	
	abstract class BaseController
	{
		private \Twig\Environment $twig ;
		
		public function __construct()
		{
			# generar la vista
			require_once "./vendor/autoload.php" ;
			
			# Configura opciones para TWIG, concretamente la carpeta raíz donde
			# colgarán las vistas.
			$loader = new \Twig\Loader\FilesystemLoader("./vistas") ;
			
			# Creamos el entorno TWIG
			$this->twig = new \Twig\Environment($loader) ;
			
			# comprueba si hay una sesión activa
			$this->twig->addFunction( new \Twig\TwigFunction( "active", function()
														{
															return \Clases\Sesion::active()   ;
														} ) ) ;
			
			# comprueba si hay algún error en la petición HTTP
			$this->twig->addFunction( new \Twig\TwigFunction( "error", function()
														{
															return isset($_GET["error"]) ;
														}));
			
			# comprueba si hay algún error en la petición HTTP
			$this->twig->addFunction( new \Twig\TwigFunction( "route", function($name)
														{
															return Request::route($name) ;
														}));
			
		}
		
		/**
		 * @param string $vista
		 * @param array $args
		 * @return void
		 * @throws \Twig\Error\LoaderError
		 * @throws \Twig\Error\RuntimeError
		 * @throws \Twig\Error\SyntaxError
		 */
		public function render(string $vista, array $args = []): void
		{
			echo $this->twig->render($vista, $args) ;
		}
		
	}
