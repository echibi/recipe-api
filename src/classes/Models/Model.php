<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 22/01/17
 */

namespace App\Models;


use Interop\Container\ContainerInterface;
use Monolog\Logger;
use Pixie\QueryBuilder\QueryBuilderHandler;

/**
 * Class Model
 * @package App\Models
 */
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
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * @param ContainerInterface $container
	 */
	function __construct( ContainerInterface $container ) {
		$this->container = $container;
		$this->db     = $container->get( 'db' );
		$this->logger = $container->get( 'logger' );
	}
}