<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 26/01/17
 */

namespace App\Middleware;


use Slim\Http\Request;
use Slim\Http\Response;
use \Psr\Http\Message\ResponseInterface;

class AuthMiddleware extends Middleware {

	/**
	 * @param Request  $request
	 * @param Response $response
	 * @param          $next
	 *
	 * @return ResponseInterface
	 */
	public function __invoke( Request $request, Response $response, $next ) {

		if ( !$this->auth->check() ) {

			$this->container->get( 'logger' )->addDebug( 'Tried accessing route without auth.', array( $request->getRequestTarget() ) );
			$this->flash->addMessage( 'error', 'Authorization needed to access ' . $request->getRequestTarget() );

			$response = $response->withStatus( 403 );

			return $response->withRedirect( $this->container->get( 'router' )->pathFor( 'admin.login' ), 403 );
		}

		$response = $next( $request, $response );

		return $response;
	}
}