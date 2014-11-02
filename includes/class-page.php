<?php

	class aiPage {
	
		public	$title = '';
		public	$header = '';
		public	$subtitle = '';
		public	$icon = '';
		
		public	$url;
		
		public function __construct() {
			$this->url = basename(@$_SERVER['REQUEST_URI']);
		}
		
		
		public function setTitle($title) {
			$this->title = $title;
		}
		
		public function setHeader($header) {
			$this->header = $header;
		}
		
		public function setSubtitle($subtitle) {
			$this->subtitle	= $subtitle;
		}
		
		public function setIcon($icon) {
			$this->icon = $icon;
		}
		
		public function setUrl($url) {
			$this->url = $url;
		}
		
		public function redirect($newUrl) {
			header('Location: ' . $newUrl);
			exit();
		}
	
		// types: error, warning, success	
		public function addMessage( $message = '', $type = 'default') {
			$_SESSION['pageMessages'][$type][] = $message;
		}
		
		public function getMessages( $type = 'default', $unset = true ) {
			if (!isset($_SESSION['pageMessages'][$type])) {
				return array();
			}
			$messages = $_SESSION['pageMessages'][$type];
			if ($unset) {
				unset($_SESSION['pageMessages'][$type]);
			}
			return $messages;
		}
		
		public function hasMessages() {
			if (isset($_SESSION['pageMessages']) && (count($_SESSION['pageMessages']) > 0)) {
				return true;
			}
			return false;
		}

		public function header() {
			// loads page header

 
			include DIR_PHP_COMMONS . '/interface/default/header.php';
		}

		public function footer() {
			// loads page footer
			include DIR_PHP_COMMONS . '/interface/default/footer.php';
		}

		public function login() {
			// loads page login
			include DIR_PHP_COMMONS . '/interface/default/login.php';
		}

		public function logout() {
			// loads page logout
			include DIR_PHP_COMMONS . '/interface/default/logout.php';
		}
	}

?>
