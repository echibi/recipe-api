<?php

// Routes

$app->get( '/', function ( $request, $response, $args ) {
	// Sample log message
	//$this->logger->info("Recept-API '/' route");

	// Render index view
} );

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

$app->group( '/admin', function () {
	$this->get( '', '\App\Controllers\AdminController:index' )->setName( 'admin.index' );


	$this->get( '/recipes', '\App\Controllers\AdminController:index' );
	// Update recipe
	// $this->put( '/recipes/{id}', '\App\RecipeMapper:updateRecipe' );
	// Delete recipe
	// $this->delete( '/recipes/{id}', '\App\RecipeMapper:removeRecipe' );
	// Insert recipe
	//$this->post( '/recipes', '\App\RecipeMapper:addRecipe' );

} );

$app->get( '/login', '\App\Controllers\AdminController:login' )->setName( 'admin.login' );
$app->post( '/login', '\App\Controllers\AdminController:loginAttempt' );

