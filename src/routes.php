<?php
/**
 * @var Slim\App $app
 */
// Routes

// Redirect / to default language.
$app->get( '/', function ( \Slim\Http\Request $request, \Slim\Http\Response $response ) {
	/**
	 * @var \Slim\Router $router
	 */
	$router = $this->get( 'router' );

	return $response->withRedirect( $router->pathFor( 'home', array( 'lang' => 'sv' ) ) );
} );

$app->group( '/{lang:sv|en}', function () use ( $app ) {

	$app->get( '', '\App\Controllers\HomeController:index' )->setName( 'home' );
	$app->get( '/search', '\App\Controllers\RecipeController:search' )->setName( 'recipe-search' );
	$app->get( '/recipe/{id}', '\App\Controllers\RecipeController:single' )->setName( 'single-recipe' );

} )->add( new \App\Middleware\LanguageMiddleware( $app->getContainer() ) );

// API
$app->group( '/v1', function () {
	// Get a list of recipes
	// $this->get( '/recipes', '\App\RecipeMapper:getList' );
	// Get a single recipe by id
	// $this->get( '/recipes/{id}', '\App\RecipeMapper:getRecipe' );
	// Get all ingredients
	// $this->get( '/ingredients', '\App\IngredientMapper:getIngredients' );
	// Get single ingredient
	// $app->get('/ingredients/{id}', '\App\IngredientMapper:getIngredients');
	// Get all units
	// $this->get( '/units', '\App\IngredientMapper:getUnits' );

} );

// Protected routes
$app->group( '', function () {
	//$this->post( '/recipes', '\App\RecipeMapper:addRecipe' );

	// Admin
	$this->group( '/admin', function () {
		$this->get( '', '\App\Controllers\AdminController:index' )->setName( 'admin.index' );

		// Admin recipes
		$this->get( '/recipes', '\App\Controllers\AdminController:index' )->setName( 'admin.list-recipes' );
		$this->get( '/recipes/{id}', '\App\Controllers\AdminController:getEditRecipe' )->setName( 'admin.edit-recipe' );
		$this->post( '/recipes/{id}', '\App\Controllers\AdminController:postEditRecipe' )->setName( 'admin.post-save-recipe' );

		// Delete recipe
		$this->delete( '/recipes/{id}', '\App\Controllers\AdminController:deleteRecipe' )->setName( 'admin.delete-recipe' );

		// Admin Categories
		$this->get( '/categories', '\App\Controllers\AdminController:listCategories' )->setName( 'admin.list-categories' );
		$this->get( '/categories/{id}', '\App\Controllers\AdminController:getEditCategory' );
		$this->post( '/categories/{id}', '\App\Controllers\AdminController:postEditCategory' )->setName( 'admin.edit-category' );

		$this->delete( '/categories/{id}', '\App\Controllers\AdminController:deleteCategory' )->setName( 'admin.delete-category' );

		// Admin Ingredients
		$this->get( '/ingredients', '\App\Controllers\AdminController:listIngredients' )->setName( 'admin.list-ingredients' );
		$this->get( '/ingredients/{id}', '\App\Controllers\AdminController:getEditIngredient' );
		$this->post( '/ingredients/{id}', '\App\Controllers\AdminController:postEditIngredient' )->setName( 'admin.edit-ingredient' );


	} );

	$this->get( '/logout', '\App\Controllers\AdminController:getSignOut' )->setName( 'admin.logout' );

} )->add( new \App\Middleware\AuthMiddleware( $container ) );

// Guest Routes
// Only accessible when not logged in
$app->group( '', function () {
	$this->get( '/login', '\App\Controllers\AdminController:login' )->setName( 'admin.login' );
	$this->post( '/login', '\App\Controllers\AdminController:loginAttempt' );

} )->add( new \App\Middleware\GuestMiddleware( $container ) );
