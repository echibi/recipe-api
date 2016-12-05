<?php

// Routes

$app->get('/', function ($request, $response, $args) {
    // Sample log message
    //$this->logger->info("Recept-API '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

// Get a list of recipes
$app->get('/recipes', '\App\RecipeMapper:getList');
// Insert recipe
$app->post('/recipes', '\App\RecipeMapper:addRecipe');
// Get a single recipe by id
$app->get('/recipes/{id}', '\App\RecipeMapper:getRecipe');
// Update recipe
$app->put('/recipes/{id}', '\App\RecipeMapper:updateRecipe');
// Delete recipe
$app->delete('/recipes/{id}', '\App\RecipeMapper:removeRecipe');
// Get all ingredients
$app->get('/ingredients', '\App\IngredientMapper:getIngredients');
// Get single ingredient
// $app->get('/ingredients/{id}', '\App\IngredientMapper:getIngredients');
// Get all units
$app->get('/units', '\App\IngredientMapper:getUnits');