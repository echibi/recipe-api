<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 26/01/17
 */

namespace App\Middleware;


use Slim\Http\Request;
use Slim\Http\Response;
use \Psr\Http\Message\ResponseInterface;

class GuestMiddleware extends Middleware {

	/**
	 * @param Request  $request
	 * @param Response $response
	 * @param          $next
	 *
	 * @return ResponseInterface
	 */
	public function __invoke( Request $request, Response $response, $next ) {

		if ( $this->auth->check() ) {

			return $response->withRedirect( $this->container->get( 'router' )->pathFor( 'admin.index' ), 403 );
		}

		$response = $next( $request, $response );

		return $response;
	}
}