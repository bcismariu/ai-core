<?php
	/*
		custom debugging class
		
		Asociatia pentru Inovatie
		
		2014 bogdan.cismariu@gmail.com
	*/
	class aiDebug {
		public function show($var) {
			echo '<pre>' . print_r($var, true) . '</pre>';
		}
	}
?>
