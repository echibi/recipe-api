<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 10/03/17
 */

namespace App\Controllers;


use App\Models\IngredientModel;
use App\Models\RecipeModel;
use Slim\Http\Request;
use Slim\Http\Response;

class RecipeController extends Controller {
	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function single( Request $request, Response $response ) {
		/**
		 * @var RecipeModel $recipeModel
		 */
		$id          = $request->getAttribute( 'id' );
		$recipeModel = $this->ci->get( 'RecipeModel' );

		return $this->view->render( $response, 'frontend/single-recipe.twig',
			array(
				'recipe' => $recipeModel->get( $id ),
			)
		);
	}

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function search( Request $request, Response $response ) {
		/**
		 * @var RecipeModel     $recipeModel
		 * @var IngredientModel $ingredientModel
		 */
		$searchString        = $request->getParam( 'q' );
		$ingredientsIdString = $request->getParam( 'ingredients' );

		$recipeModel     = $this->ci->get( 'RecipeModel' );
		$ingredientModel = $this->ci->get( 'IngredientModel' );

		$ingredientQuery = $recipeQuery = array();

		if ( !empty( $searchString ) ) {
			$ingredientQuery['q'] = $searchString;
			$recipeQuery['q']     = $searchString;
		}
		if ( !empty( $ingredientsIdString ) ) {
			$recipeQuery['ingredients'] = $ingredientsIdString;
		}

		return $this->view->render( $response, 'frontend/search.twig',
			array(
				'ingredients' => $ingredientModel->getList( $ingredientQuery ),
				'recipes'     => $recipeModel->getList( $recipeQuery ),
			)
		);
	}
}