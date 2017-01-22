<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 22/01/17
 */

namespace App\Controllers;


use Interop\Container\ContainerInterface;

class Controller {
	/**
	 * @var ContainerInterface
	 */
	protected $ci;

	/**
	 * @var \PDO
	 */
	protected $db;


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
	}
}