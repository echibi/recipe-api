<?php
/**
 * @var Slim\App $app
 */
// Routes
$app->group( '/[{lang:sv|en}]', function () use ( $app ) {

	$app->get( '', function ( \Slim\Http\Request $request, \Slim\Http\Response $response, $args ) {

		// echo "<xmp style=\"text-align:left;\">" . print_r( $request->getAttribute( 'lang' ), true ) . "</xmp>";
		// Sample log message
		//$this->logger->info("Recept-API '/' route");

		// Render index view
	} )->add( new \App\Middleware\LanguageMiddleware( $app->getContainer() ) );
} );

// API
$app->group( '/v1', function () {
	// Get a list of recipes
	$this->get( '/recipes', '\App\RecipeMapper:getList' );
	// Get a single recipe by id
	$this->get( '/recipes/{id}', '\App\RecipeMapper:getRecipe' );
	// Get all ingredients
	$this->get( '/ingredients', '\App\IngredientMapper:getIngredients' );
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

		$this->get( '/recipes', '\App\Controllers\AdminController:index' )->setName( 'admin.list-recipes' );
		$this->get( '/recipes/{id}', '\App\Controllers\AdminController:getEditRecipe' )->setName( 'admin.edit-recipe' );
		$this->post( '/recipes/{id}', '\App\Controllers\AdminController:postEditRecipe' )->setName( 'admin.post-save-recipe' );

		// Delete recipe
		// $this->delete( '/recipes/{id}', '\App\Controllers\AdminController:deleteRecipe' )->setName('admin.delete-recipe');

	} );

	$this->get( '/logout', '\App\Controllers\AdminController:getSignOut' )->setName( 'admin.logout' );

} )->add( new \App\Middleware\AuthMiddleware( $container ) );

// Guest Routes
// Only accessible when not logged in
$app->group( '', function () {
	$this->get( '/login', '\App\Controllers\AdminController:login' )->setName( 'admin.login' );
	$this->post( '/login', '\App\Controllers\AdminController:loginAttempt' );

} )->add( new \App\Middleware\GuestMiddleware( $container ) );
