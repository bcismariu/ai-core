<?php
	/*
		General init file.
		
		MAKE A COPY of the file into your project directory
		and edit that file.
		
		Edit original only if you know what you are doing.
		
		2014 bogdan.cismariu@gmail.com
	*/
	
	require	'settings.php';
	
	require_once	DIR_PHP_COMMONS_INCLUDES . DIR_SEP . 'session-handler.php';
	
	require_once	DIR_PHP_COMMONS_INCLUDES . DIR_SEP . 'class-project.php';
	$project = new AIProject(PROJECT_NAME, PROJECT_PATH, PROJECT_URL);


	require_once	DIR_PHP_COMMONS_INCLUDES . DIR_SEP . 'class-mysql.php';
	$mysql	= new AIMySQL(MYSQL_SERVER, MYSQL_USER, MYSQL_PASS, MYSQL_DATABASE);	

	require_once	DIR_PHP_COMMONS_INCLUDES . DIR_SEP . 'class-mysql-table.php';
	require_once	DIR_PHP_COMMONS_INCLUDES . DIR_SEP . 'class-crud.php';
	$crud = new AICRUD();

	require_once	DIR_PHP_COMMONS_INCLUDES . DIR_SEP . 'class-page.php';
	$page = new AIPage();
	
	require_once	DIR_PHP_COMMONS_INCLUDES . DIR_SEP . 'class-debug.php';
	$debug = new AIDebug();
?>
