<?php
/**
 * Created by Jonas Rensfeldt
 * Date: 20/11/16
 * Time: 15:21
 */

namespace App;

use \Interop\Container\ContainerInterface as ContainerInterface;


class RecipeMapper {

	/**
	 * @var ContainerInterface
	 */
	protected $ci;

	/**
	 * Constructor
	 *
	 * @param ContainerInterface $ci
	 */
	public function __construct( ContainerInterface $ci ) {
		$this->ci     = $ci;
		$this->logger = $ci->get( 'logger' );
	}

	/**
	 * Return a list with recipes
	 *
	 * @param $request
	 * @param $response
	 * @param $args
	 */
	public function getList( $request, $response, $args ) {
		// Log access
		$this->logger->info( "getList started" );

		$page  = $request->getQueryParam( 'page' );
		$limit = $request->getQueryParam( 'limit', 10 );

		//echo '<xmp style="text-align:left;">'. print_r( $response, true ) .'</xmp>';
	}

	/**
	 * Return a recipe if id is found.
	 *
	 * @param $request
	 * @param $response
	 * @param $args
	 */
	public function getRecipe( $request, $response, $args ) {
		$this->logger->info( "getRecipe started" );
	}

	/**
	 * Add recipe
	 *
	 * @param $request
	 * @param $response
	 * @param $args
	 */
	public function addRecipe( $request, $response, $args ) {
		$this->logger->info( "add recipe" );
	}

	/**
	 * Update recipe
	 *
	 * @param $request
	 * @param $response
	 * @param $args
	 */
	public function updateRecipe( $request, $response, $args ) {
		$this->logger->info( "update recipe" );
	}
}