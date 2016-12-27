<?php
/**
 * Created by Jonas Rensfeldt
 * Date: 20/11/16
 * Time: 15:21
 */

namespace App;

use App\Helpers\Utilities;
use \Interop\Container\ContainerInterface as ContainerInterface;
use App\Validation\RecipeValidator;

class RecipeMapper {

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

		$queryParams = $request->getQueryParams();

		$model = new RecipeModel( $this->db );

		$recipes = $model->getItems( $queryParams );

		echo "<xmp style=\"text-align:left;\">" . print_r( $recipes, true ) . "</xmp>";

		$uri     = $request->getUri();
		$baseUrl = $uri->getBaseUrl();
		$path    = $uri->getPath();

		// var_export( $path );

		// var_export( $uri->getBaseUrl() );


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

		$model = new RecipeModel( $this->db );

		$recipe = $model->getItem( $args['id'] );

		if ( false !== $recipe ) {
			$returnData['data']   = array(
				'id'          => $recipe->getId(),
				'title'       => $recipe->getTitle(),
				'description' => $recipe->getDescription(),
				'ingredients' => $recipe->getIngredients()
			);
			$returnData['status'] = 'ok';

			return $response->withJson( $returnData );

		} else {
			$returnData['error']  = 'ID did not match any recipes.';
			$returnData['status'] = 'failed';

			return $response->withJson( $returnData, 404 );
		}

	}

	/**
	 * Add recipe
	 *
	 * @param $request
	 * @param $response
	 * @param $args
	 */
	public function addRecipe( $request, $response, $args ) {

		$returnData = array();
		$data       = $request->getParsedBody();
		$validator  = new RecipeValidator();
		$status     = 200;

		if ( true === $validator->assert( $data ) ) {
			// Everything is fine.

			// Create our recipe entity
			$db      = $this->ci->get( 'db' );
			$model   = new RecipeModel( $db );
			$savedId = $model->create( new RecipeEntity( $data ) );

			if ( false !== $savedId ) {

				$uri        = $request->getUri();
				$createdUrl = $uri->getBaseUrl() . $uri->getPath() . '/' . $savedId;

				$response->withHeader( 'Location', $createdUrl );

				$status = 201;

				$returnData['itemId'] = $savedId;
				$returnData['status'] = 'ok';

				$this->logger->info( "added recipe" );
			} else {
				$returnData['status'] = 'failed';
				$this->logger->warning( "recipe-add failed inside model." );
			}

		} else {
			// Errors found in validator
			$errors     = $validator->errors();
			$returnData = $errors;

			$this->logger->info( "recipe-add validator failed" );

		}

		return $response->withJson( $returnData, $status );

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