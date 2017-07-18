<?php
return [
	'settings' => [
		'displayErrorDetails'    => true, // set to false in production
		'addContentLengthHeader' => false, // Allow the web server to send the content-length header

		// Database
		'db'                     => [
			'host'   => '127.0.0.1',
			'user'   => 'root',
			'pass'   => '',
			'dbname' => 'recipe-manager',
			'prefix' => 'rm_'
		],
		'paths'                  => [
			'web_root'            => __DIR__ . '/../public',
			'template_path'       => __DIR__ . '/../templates/',
			'upload_dir'          => __DIR__ . '/../public/uploads',
			'upload_dir_relative' => 'uploads'
		],
		// Monolog settings
		'logger'                 => [
			'name'      => 'recipe-manager',
			'path'      => __DIR__ . '/../logs/recipe.log',
			'level'     => \Monolog\Logger::DEBUG,
			'max_files' => 60
		],
		'lang'                   => [
			'default'   => 'sv',
			'languages' => [
				'en',
				'sv',
			]
		],
		'image_manager'          => [
			'driver'          => 'gd',
			'quality'         => 70,
			'thumbnail_sizes' => [
				'square' => [
					'w'      => 250,
					'h'      => 250,
					'square' => true
				],
				'medium' => [
					'w' => null,
					'h' => 400
				],
				'large'  => [
					'w' => null,
					'h' => 1080
				]
			]
		]
	],
];

