<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 26/01/17
 */

namespace App\Middleware;


use App\Language\Language;
use Slim\Http\Request;
use Slim\Http\Response;
use \Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;

class LanguageMiddleware extends Middleware {

	/**
	 * @var Twig
	 */
	protected $view;

	/**
	 * @var Language
	 */
	protected $langHandler;

	/**
	 * @param Request  $request
	 * @param Response $response
	 * @param          $next
	 *
	 * @return ResponseInterface
	 */
	public function __invoke( Request $request, Response $response, $next ) {

		$langSettings      = $this->container->get( 'settings' )['lang'];
		$this->langHandler = $this->container->get( 'language' );
		$route             = $request->getAttribute( 'route' );
		$currentLang       = $route->getArgument( 'lang' );
		if ( null === $currentLang ) {
			$currentLang = $langSettings['default'];
		}
		// Save current language to session.
		$this->view = $this->container->get( 'view' );
		$this->view->getEnvironment()->addGlobal( 'lang', $currentLang );
		$this->langHandler->set( $currentLang );

		$response = $next( $request, $response );

		return $response;
	}
}