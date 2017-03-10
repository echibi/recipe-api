<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 10/03/17
 */

namespace App\Controllers;


use App\Models\RecipeModel;
use Slim\Http\Request;
use Slim\Http\Response;

class HomeController extends Controller {
	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function index( Request $request, Response $response ) {
		/**
		 * @var RecipeModel $recipeModel
		 */
		$recipeModel = $this->ci->get( 'RecipeModel' );

		return $this->view->render( $response, 'frontend/home.twig',
			array(
				'recipes' => $recipeModel->getList( array( 'limit' => 25 ) ),
			)
		);
	}
}