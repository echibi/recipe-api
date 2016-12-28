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

		$recipes = $model->getList( $queryParams );

		$uri     = $request->getUri();
		$baseUrl = $uri->getBaseUrl();
		$path    = $uri->getPath();
		$query   = $uri->getQuery();

		echo "<xmp style=\"text-align:left;\">" . print_r( $baseUrl, true ) . "</xmp>";

		echo "<xmp style=\"text-align:left;\">" . print_r( $query, true ) . "</xmp>";
		echo "<xmp style=\"text-align:left;\">" . print_r( $path, true ) . "</xmp>";

		echo "<xmp style=\"text-align:left;\">" . print_r( $recipes, true ) . "</xmp>";
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

		$model = new RecipeModel();

		$recipe = $model->get( $args['id'] );

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
			// $db      = $this->ci->get( 'db' );
			$model   = new RecipeModel();
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
		$returnData = array();
		$data       = $request->getParsedBody();
		$validator  = new RecipeValidator();
		$status     = 200;

		if ( true === $validator->assert( $data ) ) {
			// Everything is fine.

			// Create our recipe entity
			$model = new RecipeModel();
			$model->update( $data );

		} else {
			// Errors found in validator
			$errors     = $validator->errors();
			$returnData = $errors;

			$this->logger->info( "recipe-update validator failed" );

		}

		return $response->withJson( $returnData, $status );
	}

	public function removeRecipe( $request, $response, $args ) {
		$this->logger->info( "deleted recipe" );
		$returnData = array();
		$status     = 200;
		$model      = new RecipeModel();
		$success    = $model->remove( $args['id'] );
		if ( true === $success ) {
			$returnData['status'] = 'ok';

		} else {
			$returnData['status']  = 'failed';
			$returnData['message'] = 'No record found.';
			$status                = 404;
		}

		return $response->withJson( $returnData, $status );

	}
}