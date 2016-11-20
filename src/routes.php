<?php

// Routes

$app->get('/', function ($request, $response, $args) {
    // Sample log message
    //$this->logger->info("Recept-API '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->get('/recipes', '\App\RecipeMapper:getList');

$app->get('/recipes/{slug}', function ($request, $response, $args) {
	// Sample log message
	$this->logger->info("accessed '/recipes/{slug}' route");

	// Render index view
	// return $this->renderer->render($response, 'index.phtml', $args);
});
