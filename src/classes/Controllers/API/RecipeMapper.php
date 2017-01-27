<?php
/**
 * Created by Jonas Rensfeldt
 * Date: 20/11/16
 * Time: 15:21
 */

namespace App\Controllers\API;

use App\Controllers\Controller;
use \Interop\Container\ContainerInterface as ContainerInterface;
use App\Validation\RecipeValidator;
use App\Models\Recipe;
use App\Entities\RecipeEntity;
use Slim\Http\Request;
use Slim\Http\Response;

class RecipeMapper extends Controller {

	/**
	 * Return a list with recipes
	 *
	 * @param $request
	 * @param $response
	 * @param $args
	 *
	 * @return Response
	 */
	public function getList( Request $request, Response $response, $args ) {
		// Log access
		$this->logger->info( "getList started" );

		$queryParams = $request->getQueryParams();

		$model = new Recipe( $this->db );

		$recipes = $model->getList( $queryParams );

		$uri = $request->getUri();

		$path  = $uri->getPath();
		$query = $uri->getQuery();

		return $response->withJson( $recipes );
	}

	/**
	 * Return a recipe if id is found.
	 *
	 * @param $request
	 * @param $response
	 * @param $args
	 *
	 * @return Response
	 */
	public function getRecipe( Request $request, Response $response, $args ) {

		$this->logger->info( "getRecipe started" );

		$model = new Recipe( $this->db );

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
	 *
	 * @return Response
	 */
	public function addRecipe( Request $request, Response $response, $args ) {

		$returnData = array();
		$data       = $request->getParsedBody();
		$validator  = new RecipeValidator();
		$status     = 200;

		if ( true === $validator->assert( $data ) ) {
			// Everything is fine.

			// Create our recipe entity
			// $db      = $this->ci->get( 'db' );
			$model   = new Recipe( $this->db );
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
	 *
	 * @return Response
	 */
	public function updateRecipe( Request $request, Response $response, $args ) {
		$returnData = array();
		$data       = $request->getParsedBody();
		$validator  = new RecipeValidator();
		$status     = 200;

		$model  = new Recipe( $this->db );
		$recipe = $model->get( $args['id'] );

		if ( false !== $recipe ) {

			if ( true === $validator->assert( $data ) ) {
				// Everything is fine.

				// Create our recipe entity
				$model->update( $args['id'], $data );

				$this->logger->info(
					"updated recipe",
					array(
						'args' => $args,
						'data' => $data
					)
				);

			} else {
				// Errors found in validator
				$errors     = $validator->errors();
				$returnData = $errors;

				$this->logger->info( "recipe-update validator failed" );

			}
		}

		return $response->withJson( $returnData, $status );
	}

	public
	function removeRecipe( Request $request, $response, $args ) {
		$this->logger->info( "deleted recipe" );
		$returnData = array();
		$status     = 200;
		$model      = new Recipe( $this->db );
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