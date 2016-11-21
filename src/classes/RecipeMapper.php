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

		$offset = $request->getQueryParam( 'offset' );
		$limit  = $request->getQueryParam( 'limit', 10 );

		$db = $this->ci->get( 'db' );

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

		// Define if fields is required and what type we should check for.
		$fieldDefinition = array(
			'title'       => array(
				'required' => true,
				'type'     => 'text'
			),
			'description' => array(
				'required' => false,
				'type'     => 'text'
			),
			'ingredients' => array(
				'required' => true,
				'type'     => 'array'
			),
			'image1'      => array(
				'required' => false,
				'type'     => 'file'
			)
		);

		$data        = $request->getParsedBody();
		$recipe_data = array();
		if ( isset( $data['title'] ) ) {
			$recipe_data['title'] = filter_var( $data['title'], FILTER_SANITIZE_STRING );
		}
		if ( isset( $data['description'] ) ) {
			$recipe_data['description'] = filter_var( $data['description'], FILTER_SANITIZE_STRING );
		}

		// echo '<xmp style="text-align:left;">' . print_r( $recipe_data, true ) . '</xmp>';
		// $newResponse = $response->withStatus(403);

		// $db = $this->ci->get( 'db' );

		return $response->withJson( $recipe_data );

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