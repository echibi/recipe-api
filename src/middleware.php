<?php
// Application middleware

// This is leaked here in public/index.php
// $container = $app->getContainer();

$app->add( new \App\Middleware\CsrfViewMiddleware( $container ) );
