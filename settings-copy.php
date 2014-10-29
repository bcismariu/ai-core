<?php
	/*
		General settings file.
		
		MAKE A COPY of the file into your project directory
		and edit that file.
		
		Edit original only if you know what you are doing.
		
		2014 bogdan.cismariu@gmail.com
	*/

	// GENERAL SETTINGS

	//////////////////////
	/// Database connection
	
	define('MYSQL_SERVER',	'mysql-server');
	define('MYSQL_USER',	'mysql-user');
	define('MYSQL_PASS',	'mysql-pass');
	define('MYSQL_DATABASE', 'mysql-db');

	//////////////////////
	/// Project Details
	define('PROJECT_NAME', 'API Project');
	define('PROJECT_PATH', '/var/www/html/root/');
	define('PROJECT_URL', 'http://www.apiproject.ro/');

	
	//////////////////////////
	//// Directory Structure
	
	define ('DIR_SEP', '/');
	define ('DIR_SERVER_ROOT', $_SERVER['DOCUMENT_ROOT'] . DIR_SEP);
	define ('DIR_ROOT', DIR_SERVER_ROOT);

	define ('DIR_PHP_COMMONS',	'/var/www/php-commons');
	define ('DIR_PHP_COMMONS_INCLUDES',	DIR_PHP_COMMONS . DIR_SEP . 'includes');
	
?>
