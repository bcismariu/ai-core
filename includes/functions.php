<?php
	/*
		general purpose functions
	*/

	function d($var) {
		require_once	'class-debug.php';
		aiDebug::show($var);
	}

	function dd($var) {
		d($var);
		die();
	}