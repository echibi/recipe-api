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
// Get a single recipe by id
$app->get('/recipes/{id}', '\App\RecipeMapper:getRecipe');
// Insert recipe
$app->post('/recipes/new', '\App\RecipeMapper:addRecipe');
// Update recipe
$app->put('/recipes/{id}', '\App\RecipeMapper:updateRecipe');