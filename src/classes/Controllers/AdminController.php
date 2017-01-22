<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 22/01/17
 */

namespace App\Controllers;


use App\Auth\Auth;
use Slim\Http\Request;
use Slim\Http\Response;

class AdminController extends Controller {

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return mixed
	 */
	public function index( Request $request, Response $response ) {
		return $this->view->render( $response, 'admin/list-recipes.twig' );
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

	public function loginAttempt( Request $request, Response $response ) {
		$authentication = new Auth( $this->ci );
		$auth           = $authentication->attempt(
			$request->getParam( 'username' ),
			$request->getParam( 'password' )
		);
		if ( !$auth ) {

			return $response->withRedirect( $this->ci->get( 'router' )->pathFor( 'admin.login' ) );
		}

		return $response->withRedirect( $this->ci->get( 'router' )->pathFor( 'admin.index' ) );
	}
}