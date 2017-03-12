<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 10/03/17
 */

namespace App\Controllers;


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
}