<?php
	/*
		Project related properties

		2014 bogdancismariu@gmail.com
	*/
	class AIProject{
	
		public $name = 'API Project';
		public $path = '/var/www/html/root/';
		public $url = 'http://www.apiproject.ro/';

		public function __construct($name = '', $path = '', $url = '') {
			$this->name = $name;
			$this->path = $path;
			$this->url = $url;
		}
	}

?>
