<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 22/01/17
 */

namespace App\Controllers;


use App\Models\Recipe;
use Slim\Http\Request;
use Slim\Http\Response;

class AdminController extends Controller {

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