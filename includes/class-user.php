<?php
	/*
		Provides general User capablities like
		Authentication
		rigths management
	*/

	class aiUser {


		public function __construct($id = '') {
			global $mysql;
			$this->mysql = $mysql;

			$this->_credentials_table = 'ai_users';
		}

		public function login($username, $password) {
			$sql ="select
						user_id
					from
						$this->_credentials_table
					where
						username = '" . $this->mysql->real_escape_string($username) . "'
						and password = '" . $this->mysql->real_escape_string($password) . "'
						";
			$result = $this->mysql->query($sql);
			if ($result->num_rows != 1) {
				// something not right
				return false;
			}

			$row = $result->fetch_assoc();
			$this->Authenticate($row['user_id']);
			return true;
		}

		public function logout() {
			unset($_SESSION['_ai_user_id']);
		}

		private function Authenticate($user_id) {
			$_SESSION['_ai_user_id'] = $user_id;
		}

		public function isAuthenticated() {
			return isset($_SESSION['_ai_user_id']);

		}

		public function hasRight($right = '', $type = 'all') {

		}


	}