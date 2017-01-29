<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 22/01/17
 */

namespace App\Controllers;


use App\Models\Recipe;
use App\Validation\Validator;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as v;

class AdminController extends Controller {
	/**
	 * @var Validator
	 */
	protected $validator;

	/**
	 * Logout user
	 *
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return Response
	 */
	public function getSignOut( Request $request, Response $response ) {
		$this->auth->logout();

		return $response->withRedirect( $this->ci->get( 'router' )->pathFor( 'admin.login' ) );
	}

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return mixed
	 */
	public function index( Request $request, Response $response ) {

		$recipeModel = new Recipe( $this->db );

		$recipes = $recipeModel->getList( array(
			'sort' => '-created',
		) );

		return $this->view->render( $response, 'admin/list-recipes.twig',
			array(
				'recipes' => $recipes,
			)
		);
	}

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function getCreateRecipe( Request $request, Response $response ) {
		return $this->view->render( $response, 'admin/add-recipe.twig' );
	}

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function postCreateRecipe( Request $request, Response $response ) {
		$this->validator = $this->ci->get( 'validator' );

		$validation = $this->validator->validate( $request, array(
			'title'       => v::notEmpty(),
			// 'description' => '',
			'portions'    => v::notEmpty(),
			// 'ingredients' => '',
			'category'    => v::notEmpty(),
			'image1'      => v::optional( v::image() ),
		) );

		if ( $validation->failed() ) {
			return $response->withRedirect( $this->ci->get( 'router' )->pathFor( 'admin.add-recipe' ) );
		}

		return $this->view->render( $response, 'admin/edit-recipe.twig' );
	}

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function editRecipe( Request $request, Response $response ) {
		return $this->view->render( $response, 'admin/edit-recipe.twig' );
	}


	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return mixed
	 */
	public function login( Request $request, Response $response ) {
		return $this->view->render( $response, 'admin/login.twig' );
	}

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return Response
	 */
	public function loginAttempt( Request $request, Response $response ) {
		$auth = $this->auth->attempt(
			$request->getParam( 'username' ),
			$request->getParam( 'password' )
		);

		if ( !$auth ) {

			return $response->withRedirect( $this->ci->get( 'router' )->pathFor( 'admin.login' ) );
		}

		return $response->withRedirect( $this->ci->get( 'router' )->pathFor( 'admin.index' ) );
	}
}