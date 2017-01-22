<?php
use Interop\Container\ContainerInterface;
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ( ContainerInterface $c ) {
	$settings = $c->get( 'settings' )['renderer'];

	return new Slim\Views\PhpRenderer( $settings['template_path'] );
};

// monolog
$container['logger'] = function ( ContainerInterface $c ) {
	$settings = $c->get( 'settings' )['logger'];
	$logger   = new Monolog\Logger( $settings['name'] );

	$logger->pushProcessor( new Monolog\Processor\UidProcessor() );
	$logger->pushHandler( new \Monolog\Handler\RotatingFileHandler( $settings['path'], $settings['max_files'], $settings['level'] ) );

	// $logger->pushHandler( new Monolog\Handler\StreamHandler( $settings['path'], $settings['level'] ) );

	return $logger;
};

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
