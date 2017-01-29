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

class ValidationErrorMiddleware extends Middleware {
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

		$this->view = $this->container->get( 'view' );

		if ( isset( $_SESSION['form_errors'] ) ) {
			$this->view->getEnvironment()->addGlobal( 'form_errors', $_SESSION['form_errors'] );
			unset( $_SESSION['form_errors'] );
		}

		$response = $next( $request, $response );

		return $response;
	}
}