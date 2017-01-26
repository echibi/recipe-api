<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 26/01/17
 */

namespace App\Middleware;


use App\Auth\Auth;
use Interop\Container\ContainerInterface;
use Slim\Flash\Messages;

class Middleware {
	/**
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * @var Messages
	 */
	protected $flash;

	/**
	 * @var Auth
	 */
	protected $auth;

	/**
	 * @param ContainerInterface $container
	 */
	public function __construct( ContainerInterface $container ) {
		$this->container = $container;
		$this->auth      = $container->get( 'auth' );
		$this->flash     = $container->get( 'flash' );
	}
}