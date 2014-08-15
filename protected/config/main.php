<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Duolci',
	// preloading 'log' component
		'preload'=>array('log'),
	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.modules.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>FALSE,
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('*'),
		),
		
	),

	// application components
	'components'=>array(
		'session' => array(
           		'sessionName' => 'Duolci API',
		),
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		// uncomment the following to use a MySQL database
		'db'=>array(
			'class'=>'CDbConnection', 
			'connectionString' => 'mysql:host=localhost;dbname=duolci',
			'emulatePrepare' => true,
			'username' => 'duolci',
			'password' => 'KL4BbEXKW9dTCtWD',
			'charset' => 'utf8',
			'schemaCachingDuration'=>3600,
			'enableProfiling'=>true, 
			),
      // Enabled Memcache for future use
		'memcache'=>array(
            'class'=>'system.caching.CMemCache',
            'servers'=>array(
                array('host'=>'127.0.0.1', 'port'=>11211, 'weight'=>60),
      			),
      ),
      // Enabled Alternative Access to APCCache
		'apccache'=>array(
			'class'=>'CApcCache',
      ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'trace,error,warning,notice',
				),
				// uncomment the following to show log messages on web pages
				
				array(
					'class'=>'CWebLogRoute',
				),
			
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
	        'encryptionKey'=>'c2438e672cacd8c387ede45556edd125ca7956aec4d755470ee3b5c387807e9afeeb23b390014bf18499d2512a3427adf6b47062b1974e9d7d9d2dc6f8d1617c'
	),
);
