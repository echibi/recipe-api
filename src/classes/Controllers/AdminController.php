<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 22/01/17
 */

namespace App\Controllers;


use App\Entities\RecipeEntity;
use App\Models\CategoryModel;
use App\Models\ImageModel;
use App\Models\RecipeModel;
use App\Models\UnitModel;
use App\Validation\RecipeValidator;
use App\Validation\Validator;
use Slim\Http\Request;
use Slim\Http\Response;

class AdminController extends Controller {
	/**
	 * @var Validator
	 */
	protected $validator;

	/**
	 * @var RecipeValidator
	 */
	protected $recipeValidator;

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return mixed
	 */
	public function index( Request $request, Response $response ) {

		/**
		 * @var RecipeModel $recipeModel
		 */
		$recipeModel = $this->ci->get( 'RecipeModel' );

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

		/**
		 * @var CategoryModel $categoryModel
		 */
		$categoryModel = $this->ci->get( 'CategoryModel' );
		$categories    = $categoryModel->getAll();

		/**
		 * @var UnitModel $unitModel
		 */
		$unitModel = $this->ci->get( 'UnitModel' );
		$units     = $unitModel->getAll();

		return $this->view->render( $response, 'admin/add-recipe.twig',
			array(
				'categories' => $categories,
				'units'      => $units
			)
		);
	}

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function postCreateRecipe( Request $request, Response $response ) {

		$this->recipeValidator = $this->ci->get( 'RecipeValidator' );
		$validation            = $this->recipeValidator->validate( $request );

		if ( $validation->failed() ) {
			return $response->withRedirect( $this->router->pathFor( 'admin.add-recipe' ) );
		}

		// Validation OK
		$recipeData = $request->getParams();

		$files = $request->getUploadedFiles();

		if ( isset( $files['image1'] ) ) {
			/**
			 * @var ImageModel $imageModel
			 */
			$imageModel = $this->ci->get( 'ImageModel' );
			$imageId    = $imageModel->create( $files['image1'] );
			if ( null !== $imageId ) {
				$recipeData = array_merge(
					$request->getParams(),
					array(
						'image1' => $imageModel->get( $imageId )
					)
				);
			}
		}

		// Create RecipeEntity
		$recipeEntity = new RecipeEntity( $recipeData );

		// Save Entity
		/**
		 * @var RecipeModel $recipeModel
		 */
		$recipeModel = $this->ci->get( 'RecipeModel' );

		$recipeModel->create( $recipeEntity );

		return $this->view->render(
			$response,
			$this->router->pathFor(
				'admin.edit-recipe',
				array( 'id' => 12 ) // tmp
			)
		);
	}

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function getEditRecipe( Request $request, Response $response ) {

		$id = $request->getAttribute( 'id' );

		/**
		 * @var RecipeModel $recipeModel
		 */
		$recipeModel = $this->ci->get( 'RecipeModel' );
		$recipe      = $recipeModel->get( $id );

		/**
		 * @var CategoryModel $categoryModel
		 */
		$categoryModel = $this->ci->get( 'CategoryModel' );

		/**
		 * @var UnitModel $unitModel
		 */
		$unitModel = $this->ci->get( 'UnitModel' );


		// Fill view with data
		$viewData           = array();
		$viewData['recipe'] = $recipe;

		// Check if we have any posted form data
		// If empty we fill it with the current items data.
		$globals = $this->view->getEnvironment()->getGlobals();
		if ( empty( $globals['old'] ) ) {
			$viewData['old'] = $recipe;
		}
		// Get Categories
		$viewData['categories'] = $categoryModel->getAll();
		// Get Units
		$viewData['units'] = $unitModel->getAll();

		$this->logger->addDebug( 'recipeEdit', array(
			'id'       => $id,
			'viewData' => $viewData,
		) );

		return $this->view->render(
			$response,
			'admin/edit-recipe.twig',
			$viewData
		);
	}

	/**
	 * Update recipe
	 *
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function postEditRecipe( Request $request, Response $response ) {

		$id = $request->getAttribute( 'id' );

		$this->recipeValidator = $this->ci->get( 'RecipeValidator' );
		$validation            = $this->recipeValidator->validate( $request );

		if ( $validation->failed() ) {
			return $response->withRedirect( $this->router->pathFor( 'admin.edit-recipe', array( 'id' => $id ) ) );
		}
		// Validation OK
		$recipeData = $request->getParams();

		$files = $request->getUploadedFiles();

		if ( isset( $files['image1'] ) ) {
			/**
			 * @var ImageModel $imageModel
			 */
			$imageModel = $this->ci->get( 'ImageModel' );
			$imageId    = $imageModel->create( $files['image1'] );
			if ( null !== $imageId ) {
				$recipeData = array_merge(
					$request->getParams(),
					array(
						'image1' => $imageModel->get( $imageId )
					)
				);
			}
		}
		// Create RecipeEntity
		$recipeEntity = new RecipeEntity( $recipeData );

		/**
		 * @var RecipeModel $recipeModel
		 */
		$recipeModel = $this->ci->get( 'RecipeModel' );
		$recipeModel->update( $id, $recipeEntity );

		return $response->withRedirect( $this->router->pathFor( 'admin.edit-recipe', array( 'id' => $id ) ) );

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
		$this->auth = $this->ci->get( 'auth' );

		$auth = $this->auth->attempt(
			$request->getParam( 'username' ),
			$request->getParam( 'password' )
		);

		if ( !$auth ) {

			return $response->withRedirect( $this->router->pathFor( 'admin.login' ) );
		}

		return $response->withRedirect( $this->router->pathFor( 'admin.index' ) );
	}

	/**
	 * Logout user
	 *
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return Response
	 */
	public function getSignOut( Request $request, Response $response ) {
		$this->auth = $this->ci->get( 'auth' );

		$this->auth->logout();

		return $response->withRedirect( $this->router->pathFor( 'admin.login' ) );
	}
}