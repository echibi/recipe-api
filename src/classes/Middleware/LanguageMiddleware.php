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

class LanguageMiddleware extends Middleware {

	/**
	 * @param Request  $request
	 * @param Response $response
	 * @param          $next
	 *
	 * @return ResponseInterface
	 */
	public function __invoke( Request $request, Response $response, $next ) {

		$langSettings = $this->container->get( 'settings' )['lang'];
		$route        = $request->getAttribute( 'route' );
		$currentLang  = $route->getArgument( 'lang' );
		if ( null === $currentLang ) {
			$currentLang = $langSettings['default'];
		}
		// Save current language to session.
		/**
		 * @var Language $langHandler
		 */
		$langHandler = $this->container->get( 'language' );
		$langHandler->set( $currentLang );

		$response = $next( $request, $response );

		return $response;
	}
}