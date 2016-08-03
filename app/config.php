<?php
	//Constantes
	$configs = new HXPHP\System\Configs\Config;

	$configs->env->add('development');

	$configs->env->development->baseURI = '/login/';

	$configs->env->development->database->setConnectionData(array(
		'driver' => 'mysql',
		'host' => 'localhost',
		'user' => 'root',
		'password' => 'vertrigo',
		'dbname' => 'sistemalogin',
		'charset' => 'utf8'
	));

	return $configs;
