<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 22/01/17
 */

namespace App\Models;


use Interop\Container\ContainerInterface;
use Monolog\Logger;
use Pixie\QueryBuilder\QueryBuilderHandler;

class Model {
	/**
	 * @var QueryBuilderHandler
	 */
	protected $db;

	/**
	 * @var Logger
	 */
	protected $logger;

	/**
	 * @param ContainerInterface $container
	 */
	function __construct( ContainerInterface $container ) {
		$this->db     = $container->get( 'db' );
		$this->logger = $container->get( 'logger' );
	}
}