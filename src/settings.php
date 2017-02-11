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
			'dbname' => 'recept-api'
		],

		// Renderer settings
		'renderer'               => [
			'template_path' => __DIR__ . '/../templates/',
		],

		// Monolog settings
		'logger'                 => [
			'name'      => 'recipe-api',
			'path'      => __DIR__ . '/../logs/recipe.log',
			'level'     => \Monolog\Logger::DEBUG,
			'max_files' => 60
		],
		'upload'                 => [
			'dir' => __DIR__ . '/../public/uploads'
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
				'250x250' => [
					'w'      => 250,
					'h'      => 250,
					'square' => true
				],
				'400'     => [
					'w' => null,
					'h' => 400
				],
				'1080'    => [
					'w' => null,
					'h' => 1080
				]
			]
		]
	],
];

