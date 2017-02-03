<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 22/01/17
 */

namespace App\Controllers;


use App\Auth\Auth;
use Interop\Container\ContainerInterface;
use Monolog\Logger;
use Pixie\QueryBuilder\QueryBuilderHandler;
use Slim\Flash\Messages;
use Slim\Router;
use Slim\Views\Twig;

class Controller {
	/**
	 * @var ContainerInterface
	 */
	protected $ci;

	/**
	 * @var QueryBuilderHandler
	 */
	protected $db;

	/**
	 * @var Twig
	 */
	protected $view;

	/**
	 * @var Messages
	 */
	protected $flash;

	/**
	 * @var Auth
	 */
	protected $auth;

	/**
	 * @var Logger
	 */
	protected $logger;

	/**
	 * @var Router
	 */
	protected $router;

	/**
	 * Constructor
	 *
	 * @param ContainerInterface $ci
	 */
	public function __construct( ContainerInterface $ci ) {
		$this->ci     = $ci;
		$this->logger = $ci->get( 'logger' );
		$this->db     = $ci->get( 'db' );
		$this->view   = $ci->get( 'view' );
		$this->flash  = $ci->get( 'flash' );
		$this->auth   = $ci->get( 'auth' );
		$this->router = $ci->get( 'router' );
	}
}