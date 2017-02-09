<?php
use Interop\Container\ContainerInterface;
use Respect\Validation\Validator as v;

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
			'password' => $db['pass'],
			'charset'   => 'utf8', // Optional
			'collation' => 'utf8_swedish_ci', // Optional
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

	$view->getEnvironment()->addGlobal( 'auth', array(
		'check' => $c->get( 'auth' )->check(),
		'user'  => $c->get( 'auth' )->currentUser()
	) );

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

/**
 * @return \App\Language\Language
 */
$container['language'] = function () {
	return new \App\Language\Language();
};

$container['validator'] = function () {
	return new \App\Validation\Validator();
};

$container['RecipeValidator'] = function () {
	return new \App\Validation\RecipeValidator();
};


// Include custom AbstractRules
v::with('\\App\\Validation\\Rules\\');

// Include models in our Container
$container['RecipeModel'] = function ( ContainerInterface $c ) {
	return new \App\Models\RecipeModel( $c );
};
$container['CategoryModel'] = function ( ContainerInterface $c ) {
	return new \App\Models\CategoryModel( $c );
};
$container['UnitModel'] = function ( ContainerInterface $c ) {
	return new \App\Models\UnitModel( $c );
};
$container['IngredientModel'] = function ( ContainerInterface $c ) {
	return new \App\Models\IngredientModel( $c );
};
$container['UserModel'] = function ( ContainerInterface $c ) {
	return new \App\Models\UserModel( $c );
};
$container['ImageModel'] = function ( ContainerInterface $c ) {
	return new \App\Models\ImageModel( $c );
};

// Other classes
$container['ImageUpload'] = function ( ContainerInterface $c ) {
	return new \App\Upload\ImageUpload( $c );
};

