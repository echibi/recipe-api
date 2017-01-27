<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 27/01/17
 */

namespace App\Middleware;


use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Csrf\Guard;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

class CsrfViewMiddleware extends Middleware {
	/**
	 * @var Twig
	 */
	protected $view;

	/**
	 * @var Guard
	 */
	protected $csrf;

	public function __construct( ContainerInterface $container ) {
		parent::__construct( $container );
		$this->view = $this->container->get( 'view' );
		$this->csrf = $this->container->get( 'csrf' );
	}

	/**
	 * @param Request  $request
	 * @param Response $response
	 * @param          $next
	 *
	 * @return ResponseInterface
	 */
	public function __invoke( Request $request, Response $response, $next ) {

		// Generate new token and update request
		$request = $this->csrf->generateNewToken( $request );

		// Build Header Token
		$nameKey  = $this->csrf->getTokenNameKey();
		$valueKey = $this->csrf->getTokenValueKey();
		$name     = $request->getAttribute( $nameKey );
		$value    = $request->getAttribute( $valueKey );

		$this->view->getEnvironment()->addGlobal( 'csrf', array(
			'keys'  => array(
				'name'  => $nameKey,
				'value' => $valueKey,
			),
			'name'  => $name,
			'value' => $value,
		) );

		$response = $next( $request, $response );

		return $response;
	}
}