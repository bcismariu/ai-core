<?php
	/*
		Custom Session Handler methods
		
		- defines a dedicated directory for session storage
		- makes session encoding and decoding server configuration transparent
	
		!!! - hardcoded paths (should be verified and rewritten)
	
		2013 bogdan.cismariu@gmail.com	
	*/
	
	class LiteSessionHandler {
		private $savePath = '/var/www/php-commons/sessions';

		function open($savePath, $sessionName) {
			register_shutdown_function('session_write_close');
			return true;
		}
		
		function close() {
			return true;
		}
		
		function read($id) {
			$contents = (string)@file_get_contents("$this->savePath/sess_$id");
			
			if (strlen($contents) > 0) {
				$_SESSION = unserialize($contents);
				return session_encode();
			} else {
				return '';
			}
		}
		
		function write($id, $data) {
			$contents = serialize($_SESSION);
			return file_put_contents("$this->savePath/sess_$id", $contents) === false ? false : true;
		}
		
		function destroy($id) {

			$file = "$this->savePath/sess_$id";
			if (file_exists($file)) {
				unlink($file);
			}
			unset($_SESSION);

			return true;
		}
		
		function gc($maxlifetime) {
			foreach (glob("$this->savePath/sess_*") as $file) {
				if (file_exists($file) && (filemtime($file) + $maxlifetime < time())) {
					unlink($file);
				}
			}
			return true;
		}
		
		function __destruct() {
			return true;
		}
	}
	
	$sessHandler = new LiteSessionHandler();

	session_set_save_handler(
		array($sessHandler, 'open'),
		array($sessHandler, 'close'),
		array($sessHandler, 'read'),
		array($sessHandler, 'write'),
		array($sessHandler, 'destroy'),
		array($sessHandler, 'gc')
	);
	
	unset($sessHandler);
		

