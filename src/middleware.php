<?php
// Application middleware

// This is leaked here in public/index.php
// $container = $app->getContainer();

$app->add( new \App\Middleware\CsrfViewMiddleware( $container ) );
$app->add( new \App\Middleware\ValidationErrorMiddleware( $container ) );
$app->add( new \App\Middleware\OldInputMiddleware( $container ) );


