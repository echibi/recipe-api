<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 29/01/17
 */

namespace App\Middleware;


use Slim\Http\Request;
use Slim\Http\Response;
use \Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;

class OldInputMiddleware extends Middleware {
	/**
	 * @var Twig
	 */
	protected $view;

	/**
	 * @param Request  $request
	 * @param Response $response
	 * @param          $next
	 *
	 * @return ResponseInterface
	 */
	public function __invoke( Request $request, Response $response, $next ) {

		if ( isset( $_SESSION['old'] ) ) {
			$this->view = $this->container->get( 'view' );
			$this->view->getEnvironment()->addGlobal( 'old', $_SESSION['old'] );
		}
		$_SESSION['old'] = $request->getParams();
		$response        = $next( $request, $response );

		return $response;
	}
}