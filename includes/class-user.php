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

			$this->_id = '';
			$this->_name = '';
			if (isset($this->security->user_id)) {
				$this->_id = $this->security->user_id;
			}
			if (isset($this->profile->name)) {
				$this->_name = $this->profile->name;
			}

			$this->_rights = array();
			$this->_roles = array();
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
			unset($_SESSION);
			session_destroy();
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

		public function setCurrentProject($projectId) {
			$_SESSION['_ai_user_project'] = $projectId;
			$this->_project_id = $projectId;
		}

		public function hasProject() {
			if (isset($_SESSION['_ai_user_project'])) {
				$this->_project_id = $_SESSION['_ai_user_project'];
				return true;
			}
			return false;
		}

		public function getRights($project_id = 0, $user_id = '') {
			if ($user_id == '') {
				$user_id = $this->_id;
			}
			if (($project_id == 0) && isset($this->_project_id)) {
				$project_id = $this->_project_id;
			}
			$key = array('user_id' => $user_id, 'project_id' => $project_id);
			$rights = new aiUserRights($key);
			if (!isset($rights->rights)) {
				$rights->rights = '';
			}
			if (!isset($rights->roles)) {
				$rights->roles = '';
			}
			$this->_rights = $this->parseRights($rights->rights);
			$this->_roles = $this->parseRights($rights->roles);
		}

		public function hasRole($role = '') {
			return $this->hasRight($role, 'all', 'role');
		}

		public function hasRight($right = '', $type = 'all', $role = 'right') {
			// make sure you call getRights before
			if (!is_array($right)) {
				$right = $this->parseRights($right);
			}
			$haystack = ($role == 'role') ? $this->_roles : $this->_rights;
			$intersect = array_intersect($haystack, $right);
			if ($type = 'all') {
				return (count($right) == count($intersect));
			} else {
				return (count($intersect) > 1);
			}
		}

		private function parseRights($string = '') {
			$string = trim($string);
			if ($string == '') {
				return array();
			}
			$rights = explode(',', $string);
			foreach ($rights as &$r) {
				$r = trim($r);
			}
			unset($r);
			return $rights;
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

	class aiUserRights extends aiMySQLTable {
		public function __construct($id = '') {
			$descriptor = array(	'_table' => 'ai_user_rights',
									'_keys'  => array('user_id', 'project_id')
								);
			parent::__construct($descriptor);
			$this->init($id);
		}
	}

