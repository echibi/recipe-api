<?php
use Interop\Container\ContainerInterface;

// DIC configuration

$container = $app->getContainer();

/**
 * @param ContainerInterface $c
 *
 * @return \Monolog\Logger
 */
$container['logger'] = function ( ContainerInterface $c ) {
	$settings = $c->get( 'settings' )['logger'];
	$logger   = new Monolog\Logger( $settings['name'] );

	$logger->pushProcessor( new Monolog\Processor\UidProcessor() );
	$logger->pushHandler( new \Monolog\Handler\RotatingFileHandler( $settings['path'], $settings['max_files'], $settings['level'] ) );

	// $logger->pushHandler( new Monolog\Handler\StreamHandler( $settings['path'], $settings['level'] ) );

	return $logger;
};

/**
 * @param ContainerInterface $c
 *
 * @return bool|\Pixie\QueryBuilder\QueryBuilderHandler
 */
$container['db'] = function ( ContainerInterface $c ) {
	$db = $c['settings']['db'];
	$qb = false;

	try {
		$config = array(
			'driver'   => 'mysql', // Db driver
			'host'     => $db['host'],
			'database' => $db['dbname'],
			'username' => $db['user'],
			'password' => $db['pass']
		);
		// QB is the new alias for accessing the DB
		$connection = new \Pixie\Connection( 'mysql', $config );
		$qb         = new \Pixie\QueryBuilder\QueryBuilderHandler( $connection );

	} catch ( PDOException $e ) {
		$c->get( 'logger' )->alert( 'Database connection failed: ' . $e->getMessage() );
	}

	return $qb;
};

/**
 * @param ContainerInterface $c
 *
 * @return \Slim\Views\Twig
 */
$container['view'] = function ( ContainerInterface $c ) {
	$settings = $c->get( 'settings' )['renderer'];
	$view     = new \Slim\Views\Twig( $settings['template_path'], [
		'cache' => false
	] );

	$view->addExtension( new Slim\Views\TwigExtension(
			$c['router'],
			$c['request']->getUri()
		)
	);
	// Allow flash messages inside views.
	$view->getEnvironment()->addGlobal( 'flash', $c['flash'] );

	return $view;
};

/**
 * @return \Slim\Flash\Messages
 */
$container['flash'] = function () {
	return new \Slim\Flash\Messages();
};

/**
 * @param ContainerInterface $c
 *
 * @return \App\Auth\Auth
 */
$container['auth'] = function ( ContainerInterface $c ) {
	return new \App\Auth\Auth( $c );
};

/**
 * @return \Slim\Csrf\Guard
 */
$container['csrf'] = function () {
	return new \Slim\Csrf\Guard();
};
