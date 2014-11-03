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

			$this->security = new aiUserSecurity($id);
			$this->profile = new aiUserProfile($id);
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
			if (isset($_SESSION['_ai_user_id'])) {
				global $user;
				$user = new aiUser($_SESSION['_ai_user_id']);
				return true;
			}
			return false;

		}

		public function hasRight($right = '', $type = 'all') {

		}


	}

	class aiUserSecurity extends aiMySQLTable {
		public function __construct($id = '') {
			parent::__construct('ai_users');
			$this->init($id);
		}
	}

	class aiUserProfile extends aiMySQLTable {
		public function __construct($id = '') {
			parent::__construct('ai_user_details');
			$this->init($id);
		}
	}

	