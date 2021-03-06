<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 22/01/17
 */

namespace App\Controllers;


use App\Entities\CategoryEntity;
use App\Entities\IngredientEntity;
use App\Entities\RecipeEntity;
use App\Models\CategoryModel;
use App\Models\ImageModel;
use App\Models\IngredientModel;
use App\Models\RecipeModel;
use App\Models\UnitModel;
use App\Validation\RecipeValidator;
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
	public function getEditRecipe( Request $request, Response $response ) {

		$id = $request->getAttribute( 'id' );

		// Fill view with data
		$viewData = array();

		// Get Categories
		/**
		 * @var CategoryModel $categoryModel
		 */
		$categoryModel          = $this->ci->get( 'CategoryModel' );
		$viewData['categories'] = $categoryModel->getList();

		// Get Units
		/**
		 * @var UnitModel $unitModel
		 */
		$unitModel         = $this->ci->get( 'UnitModel' );
		$viewData['units'] = $unitModel->getAll();

		// Check if we are creating a new recipe
		if ( 'create' === $id ) {
			$viewData['title']        = 'Skapa Recept';
			$viewData['recipe']['id'] = $id;
		} else {

			$viewData['title'] = 'Redigera Recept';
			/**
			 * @var RecipeModel $recipeModel
			 */
			$recipeModel = $this->ci->get( 'RecipeModel' );
			$recipe      = $recipeModel->get( $id );

			$viewData['recipe'] = $recipe;

			// Check if we have any posted form data
			// If empty we fill it with the current items data.
			$globals = $this->view->getEnvironment()->getGlobals();
			if ( empty( $globals['old'] ) ) {
				$viewData['old'] = $recipe;
			}
		}

		return $this->view->render(
			$response,
			'admin/save-recipe.twig',
			$viewData
		);

	}

	/**
	 * Update/ Insert recipe
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

		// Save new image.
		$files = $request->getUploadedFiles();

		if ( isset( $files['image1'] ) && '' !== $files['image1']->file ) {
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

		if ( 'create' === $id ) {
			$id = $recipeModel->create( $recipeEntity );
		} else {
			$recipeModel->update( $id, $recipeEntity );
		}

		return $response->withRedirect( $this->router->pathFor( 'admin.edit-recipe', array( 'id' => $id ) ) );

	}

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return bool
	 */
	public function deleteRecipe( Request $request, Response $response ) {
		/**
		 * @var RecipeModel $recipeModel
		 */
		$recipeModel = $this->ci->get( 'RecipeModel' );
		$id          = $request->getAttribute( 'id' );

		$this->logger->addInfo( 'Deleted Recipe', array( 'id' => $id ) );

		return $recipeModel->remove( $id );
	}

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function listCategories( Request $request, Response $response ) {
		/**
		 * @var CategoryModel $categoryModel
		 */
		$categoryModel = $this->ci->get( 'CategoryModel' );

		return $this->view->render( $response, 'admin/list-categories.twig', array(
			'categories' => $categoryModel->getList(),
		) );
	}

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function getEditCategory( Request $request, Response $response ) {

		/**
		 * @var CategoryModel $categoryModel
		 */

		$id = $request->getAttribute( 'id' );

		// Fill view with data
		$viewData = array();

		// Get Categories
		$categoryModel = $this->ci->get( 'CategoryModel' );

		// Check if we are creating a new recipe
		if ( 'create' === $id ) {
			$viewData['title']          = 'Skapa Kategori';
			$viewData['category']['id'] = $id;
		} else {

			$viewData['title'] = 'Redigera Kategori';
			$category          = $categoryModel->get( $id );

			$viewData['category'] = $category;

			// Check if we have any posted form data
			// If empty we fill it with the current items data.
			$globals = $this->view->getEnvironment()->getGlobals();
			if ( empty( $globals['old'] ) ) {
				$viewData['old'] = $category;
			}
		}

		return $this->view->render(
			$response,
			'admin/save-category.twig',
			$viewData
		);
	}

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return Response
	 */
	public function postEditCategory( Request $request, Response $response ) {
		$id = $request->getAttribute( 'id' );

		$this->validator = $this->ci->get( 'validator' );
		$validation      = $this->validator->validate( $request, array(
			'name' => v::notEmpty()
		) );

		if ( $validation->failed() ) {
			return $response->withRedirect( $this->router->pathFor( 'admin.edit-category', array( 'id' => $id ) ) );
		}
		// Validation OK
		$categoryData = $request->getParams();

		// Create CategoryEntity
		$categoryEntity = new CategoryEntity( $categoryData );

		/**
		 * @var CategoryModel $categoryModel
		 */
		$categoryModel = $this->ci->get( 'CategoryModel' );

		if ( 'create' === $id ) {
			$id = $categoryModel->create( $categoryEntity );
		} else {
			$categoryModel->update( $id, $categoryEntity );
		}

		return $response->withRedirect( $this->router->pathFor( 'admin.edit-category', array( 'id' => $id ) ) );

	}

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function listIngredients( Request $request, Response $response ) {
		/**
		 * @var IngredientModel $ingredientModel
		 */
		$ingredientModel = $this->ci->get( 'IngredientModel' );

		return $this->view->render( $response, 'admin/list-ingredients.twig', array(
			'categories' => $ingredientModel->getList(),
		) );
	}

	public function getEditIngredient( Request $request, Response $response ) {

		/**
		 * @var IngredientModel $ingredientModel
		 */

		$id = $request->getAttribute( 'id' );

		// Fill view with data
		$viewData = array();

		// Get Ingredient
		$ingredientModel = $this->ci->get( 'IngredientModel' );

		// Check if we are creating a new recipe
		if ( 'create' === $id ) {
			$viewData['title']            = 'Skapa Ingredient';
			$viewData['ingredient']['id'] = $id;
		} else {

			$viewData['title'] = 'Redigera Ingredient';
			$ingredient        = $ingredientModel->get( $id );

			$viewData['ingredient'] = $ingredient;

			// Check if we have any posted form data
			// If empty we fill it with the current items data.
			$globals = $this->view->getEnvironment()->getGlobals();
			if ( empty( $globals['old'] ) ) {
				$viewData['old'] = $ingredient;
			}
		}

		return $this->view->render(
			$response,
			'admin/save-ingredient.twig',
			$viewData
		);
	}

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return Response
	 */
	public function postEditIngredient( Request $request, Response $response ) {
		$id = $request->getAttribute( 'id' );

		$this->validator = $this->ci->get( 'validator' );
		$validation      = $this->validator->validate( $request, array(
			'name' => v::notEmpty()
		) );

		if ( $validation->failed() ) {
			return $response->withRedirect( $this->router->pathFor( 'admin.edit-ingredient', array( 'id' => $id ) ) );
		}
		// Validation OK
		$ingredientData = $request->getParams();

		/**
		 * @var IngredientModel $ingredientModel
		 */
		$ingredientEntity = new IngredientEntity( $ingredientData );

		$ingredientModel = $this->ci->get( 'IngredientModel' );

		if ( 'create' === $id ) {
			$id = $ingredientModel->create( $ingredientEntity );
		} else {
			$ingredientModel->update( $id, $ingredientEntity );
		}

		return $response->withRedirect( $this->router->pathFor( 'admin.edit-ingredient', array( 'id' => $id ) ) );

	}

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return bool
	 */
	public function deleteIngredient( Request $request, Response $response ) {
		/**
		 * @var IngredientModel $ingredientModel
		 */
		$ingredientModel = $this->ci->get( 'IngredientModel' );
		$id          = $request->getAttribute( 'id' );

		$this->logger->addInfo( 'Deleted Ingredient', array( 'id' => $id ) );

		return $ingredientModel->delete( $id );
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