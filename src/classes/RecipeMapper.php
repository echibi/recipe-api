<?php
/**
 * Created by Jonas Rensfeldt
 * Date: 20/11/16
 * Time: 15:21
 */

namespace App;

use \Interop\Container\ContainerInterface as ContainerInterface;
use App\Validation\RecipeValidator;

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

		$returnData = array();

		$data = $request->getParsedBody();

		$validator = new RecipeValidator();

		if ( true === $validator->assert( $data ) ) {
			// Everything is fine.

			// Create our recipe entity
			$recipe        = new RecipeEntity( $data );

			// Connect to db and prepare inserts.
			$db            = $this->ci->get( 'db' );
			$prepareRecipeInsert = $db->prepare(
				'INSERT INTO recipes ( title, description, created, updated )
				 VALUES (:title, :description, :created, :updated)'
			);

			$nowDatetime = date( 'Y-m-d H:i:s' );
			$prepareRecipeInsert->execute(
				array(
					'title'       => $recipe->getTitle(),
					'description' => $recipe->getDescription(),
					'created'     => $nowDatetime,
					'updated'     => $nowDatetime
				)
			);

			$recipeId = $db::lastInsertId();

			$prepareIngredientInsert = $db->prepare(
				'INSERT INTO ingredients ( name, slug )
				 VALUES (:name, :slug)'
			);
			$prepareIngredientRelInsert = $db->prepare(
				'INSERT INTO ingredients_rel ( recipe_id, ingredient_id, value, unit )
				 VALUES (:recipe_id, :ingredient_id, :value, :unit)'
			);

			// TODO:: Fetch ingredients from RecipeEntity
			$ingredients = array();
			if( ! empty( $ingredients ) ) {
				foreach( $ingredients as $ingredient ){
					// Run query

					$ingredientId = $db::lastInsertId();
					// Run rel query
				}
			}

			$returnData['status'] = 'ok';
		} else {
			// Errors found in validator
			$errors     = $validator->errors();
			$returnData = $errors;
		}

		// $db = $this->ci->get( 'db' );

		return $response->withJson( $returnData );

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