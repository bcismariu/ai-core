<?php
	/*
		Generic MySQL Table Model

		!!!	it uses a global $mysql instance of $mysql

		https://gitlab.com/ai-devel/ai-core/wikis/class-mysql-table

		2014 bogdan.cismariu@gmail.com
	*/


	class aiMySQLTable {
	
		private		$_table;
		private 	$_table_locked = false;

		public		$_cols = array();
		public		$_keys = array();
		protected	$_ai_col = '';
		protected	$_ai_mode = 'default';


		/*
			$descriptor = array(	'_table' => 'table_name',
						'_cols'	 => array( 'col1', 'col2', 'col3'),
						'_keys'  => array( 'key1', 'key2'),
						'_ai_col' => 'auto_increment_column',
						'_ai_mode' => 'custom'
					);
		*/

		public function __construct($descriptor = '') {
			if (!is_array($descriptor)) {
				// received only the table name
				$this->_table = $descriptor;
			} else {
				// received other details also
				foreach ($descriptor as $key => $value) {
					$this->$key = $value;
				}
			}
			// making sure our descriptor is valid
			$this->validateDescriptor();

			if (count($this->_cols) == 0) {
				// retreiving table description
				$this->setDescriptor();
			}
		}

		public function init($input = '') {
			global $debug;
			$input = $this->parseKey($input);	
			// is it a key?
			if ((count($input) == count($this->_keys)) && (count(array_diff($this->_keys, array_keys($input))) == 0)) {
				// yes, it is
				$result = $this->readFromDatabase($input);
				if (count($result) > 0) {
					// we found an entry
					$input = $result;
				}
			}
			$this->readFromData($input);

			$this->sanitizeKeys();

			$this->_id = '';
			if (count($this->_keys) == 1) {
				// for easier access
				if (isset($this->{$this->_keys[0]})) {
					$this->_id = $this->{$this->_keys[0]};
				}
			}
			if (($this->_ai_col != '') && (isset($this->{$this->_ai_col}))) {
				$this->_id = $this->{$this->_ai_col};
			}

		}

		public function save() {
			/*
				educated guess between insert and update
			*/
			global $mysql;

			$insert = false;
			foreach ($this->_keys as $key) {
				if (!isset($this->$key)) {
					// key element missing
					if ($this->_ai_col == $key) {
						$insert = true;	// auto_increment requested
					} else {
						// key incomplete. cannot perform update
						// insert might still be valid, but will put a default value on the key column
						// aborting request
						return false;
					}
					continue;
				}
				if (!$this->$key) { // '' or 0
					// most probably invalid key value
					if ($this->_ai_col == $key) {
						unset($this->$key);
						$insert = true;
					}
				}
			}

			if ($insert) {
				// pretty confident that insert was requested
				return $this->insert();
			}

			if ($this->_ai_col != '') {
				// pretty confident that update was requested
				return $this->update();
			}

			// yet unsure. performing insert on key duplicate update
			// table must be properly configured with a unique key

			return $this->insertUpdate();


			return false; 
		}

		protected function lock() {
			global $mysql;
			$mysql->query("lock tables $this->_table write");
			$this->_table_locked = true;
		}

		protected function unlock() {
			global $mysql;
			$mysql->query("unlock tables");
			$this->_table_locked = false;
		}

		protected function getCustomAI() {
			global $mysql;	

			if (isset($this->{$this->_ai_col}) && ($this->{$this->_ai_col} > 0)) {
				return $this->{$this->_ai_col};
			}

			$vals = array_intersect_key(get_object_vars($this), array_flip(array_diff($this->_keys, array($this->_ai_col))));
			$sql = "select
						max($this->_ai_col) as max
					from
						$this->_table
					where
						" . $this->getStatements($vals, ' and ');
			$result = $mysql->query($sql);
			$row = $result->fetch_assoc();
			$id = $row['max'] + 1;
			$this->{$this->_ai_col} = $id;
			return $id;
		}

		protected function reserveCustomAI($additionalRequiredFields = '') {
			global $mysql;

			$a = $additionalRequiredFields;
			if ($a == '') {
				$a = array();
			} else {
				if (!is_array($a)) {
					$a = explode(', ', $a);
				}
			}

			
			$this->lock();
			$id = $this->getCustomAI();
			$vals = array_intersect_key(get_object_vars($this), array_flip(array_merge($this->_keys, $a)));
			$mysql->query("insert into $this->_table set " . $this->getStatements($vals, ', '));
			$this->unlock();
			$this->_id = $id;
			return $id;
		}

		public function insert() {
			/*
				no data validation performed.
				this responsability belongs to the user if specifically calls this method.
				use save() for data validation
			*/
			global $mysql;

			if (($this->_ai_col != '') && ($this->_ai_mode == 'custom')) {
				$this->lock();
				$id = $this->getCustomAI();
				$this->_id = $id;
			}

			$values = array_intersect_key(get_object_vars($this), array_flip($this->_cols));
			$sql = "insert into $this->_table set " . $this->getStatements($values, ', ');
			$result = $mysql->query($sql);
			if ($this->_ai_mode == 'default') {
				$id = $mysql->insert_id;
			}
			if ($this->_table_locked) {
				$this->unlock();
			}

			if ($this->_ai_col != '') {
				$this->{$this->_ai_col} = $id;
				$this->_id = $id;	// used for easier access;
			}
			return $id;
		}

		public function update() {
			/*
				no data validation performed.
				this responsability belongs to the user if specifically calls this method.
				use save() for data validation
			*/
			global $mysql;

			foreach ($this->_keys as $key) {
				if (!isset($this->$key) || (!$this->$key)) {
					// invalid or incomplete key
					// this update is dangerous as it would alter multiple rows
					echo 'expected key unset: ' . $key;
					return false;
				}
			}
			$values = array_intersect_key(get_object_vars($this), array_flip(array_diff($this->_cols, $this->_keys)));
			$keys = array_intersect_key(get_object_vars($this), array_flip($this->_keys));
			$sql = "update $this->_table set " . $this->getStatements($values, ', ') . " where " . $this->getStatements($keys, ' and ');
			$result = $mysql->query($sql);
			return $result;
		}

		public function insertUpdate() {
			/*
				no data validation performed.
				this responsability belongs to the user if specifically calls this method.
				use save() for data validation
			*/
			global $mysql;

			$insert = array_intersect_key(get_object_vars($this), array_flip($this->_cols));
			$update = array_intersect_key(get_object_vars($this), array_flip(array_diff($this->_cols, $this->_keys)));

			$sql = "insert into $this->_table set
					" . $this->getStatements($insert, ', ') . "
				on duplicate key update " . $this->getStatements($update, ', ');
			$result = $mysql->query($sql);
			return $result;
		}

		public function delete($id = '') {
			/*
				for safety reasons please try to call this method
				from a properly instantiated object and not using
				the $id parameter
			*/
			global $mysql;

			$id = $this->parseKey($id);
			if (count($id) == 0) {
				foreach ($this->_keys as $key) {
					if (!isset($this->$key)) {
						/*
							obiectului instantiat ii lipsesc bucati din cheie
							daca continui risc sa sterg si alte randuri din tabela
						*/
						return false;
					}
					$id[$key] = $this->$key;
				}
			}
			$sql = "delete from $this->_table where " . $this->getStatements($id, ' and ');
			$mysql->query($sql);
			return true;
		}

		public function select($filter = '1', $fields = '*', $table = '') {
			global $mysql;

			// preparing fields;
			$selectFields = $this->prepareFields($fields, $table);

			// preparing table
			if ($table == '') {
				$table = $this->_table;
			}

			$data = array();
			$sql = "select
					" . implode(', ', $selectFields) . "
				from
					$table
				where
					$filter
				";
			$result = $mysql->query($sql);
			while ($row = $result->fetch_assoc()) {
				// this ads a limitation on non unique key tables
				$k = $this->compactKeyValues($row);
				$data[$k] = $row;
			}
			return $data;

		}

		public function num($filter = '1', $table = '') {
			global $mysql;

			if ($table == '') {
				$table = $this->_table;
			}

			$sql = "select
					count(*) as num
				from
					$table
				where
					$filter
				";
			$result = $mysql->query($sql);
			$row = $result->fetch_assoc();
			return $row['num'];
		}

		private function prepareFields($fields, $table = '') {
			// merges requested fields with the key
			// @return array();
			if ($fields == '*') {
				return array('*');
			}
			if (!is_array($fields)) {
				$fields = explode(',', $fields);
				foreach ($fields as &$f) {
					$f = trim($f);
				}
				unset($f);
			}
			// identifing table alias
			$talias = $this->_table;
			if (trim($table) != '') {
				if (preg_match("/$this->_table (\w+)/", $table, $matches)) {
					$m = $matches[1];
					if (!in_array($m, array('inner', 'where'))) {
						$talias = $m;
					}
				}
			}
			// preparing key
			$keys = array();
			foreach ($this->_keys as $k) {
				$keys[] = $talias . '.' . $k;
			}
			return array_merge($keys, $fields);
		}

		private function parseKey($input = '') {
			if (!$input) { // '' or 0
				return array();
			}

			if (!is_array($input)) {
				// compacted key value pairs?
				$extracted = $this->extractKeyValues($input);
				if (count($extracted) > 0) {
					return $extracted;
				}
			}
		
			if (!is_array($input)) {
				// maybe a single key table?
				if (count($this->_keys) == 1) {
					return array($this->_keys[0] => $input);
				} else {
					// something is wrong...
					// multiple key expected but only one value
					return array();
				}
			}
			return $input;
		}

		private function compactKeyValues($data) {
			/*
				identifies the keys values in $data array
				and returns an imploded string
			*/
			$kvalues = array_intersect_key($data, array_flip($this->_keys));
			$pairs = array();
			foreach ($kvalues as $key => $value) {
				$pairs[] = $key . '_=_' . $value;
			}
			return implode('_;_', $pairs);
		}

		private function extractKeyValues($string) {
			/*
				extracts a key value pairs from a compacted string
				using compactKeyValues
			*/
			if (strpos($string, '_=_') === false) {
				return array();
			}
			$array = explode('_;_', $string);
			$pairs = array();
			foreach ($array as $pair) {
				list($key, $value) = explode('_=_', $pair);
				$pairs[$key] = $value;
			}
			return $pairs;
		}

		private function sanitizeKeys() {
			foreach ($this->_keys as $k) {
				if (isset($this->$k)) {
					if (($this->$k == '') || ($this->$k == 0)) {
						unset ($this->$k);
					}
				}
			}
		}

		protected function getStatements($array, $join = ', ') {
			global $mysql;
	
			$statements = array();
			foreach ($array as $key => $value) {
				$statements[] = "$key = '" . $mysql->real_escape_string($value) . "'";
			}
			return ' ' . implode($join, $statements) . ' ';
		}

		protected function readFromDatabase($key) {
			global $mysql;

			$sql = "select
					*
				from
					$this->_table
				where
					" . $this->getStatements($key, ' and ');
			
			$result = $mysql->query($sql);
			if ($result->num_rows == 0) {
				return array();
			}
			return $result->fetch_assoc();
		}

		protected function readFromData($data) {
			$values = array_intersect_key($data, array_flip($this->_cols));
			foreach ($values as $name => $value) {
				$this->$name = $value;
			}
		}

		private function setDescriptor() {
			/*
				discovers and sets table columns
				auto_increment primary key
				multiple key if no auto_increment
			*/
			global $mysql;

			$columns = array();
			$ai = '';
			$keys = array();

			
			$sql = "describe $this->_table";
			$result = $mysql->query($sql);
			while ($row = $result->fetch_assoc()){
				$columns[] = $row['Field'];
				if ($row['Extra'] == 'auto_increment') {
					$ai = $row['Field'];
				}
				if ($row['Key'] !== '') {
					$keys[] = $row['Field'];
				}
			}

			if (($ai != '') && ($this->_ai_col == '')) {
				$this->_ai_col = $ai;
			}
			if (!count($this->_keys)) {
				$this->_keys = $keys;
			}

			$this->_cols = $columns;
			return true;
		}	

		private function validateDescriptor() {
			/*
				the user may have set up a custom descriptor
				for the table. if yes, we validate it. if not, we build it
			*/

			if (!is_array($this->_cols)) {	// only one column set (highly unprobable);
				if ($this->_cols) {	// avoid '' or 0
					$this->_cols = array($this->_cols);
				} else {
					$this->_cols = array();
				}
			}

			if (!is_array($this->_keys)) {	// only one key column is set
				if ($this->_keys) {	// avoid '' or 0
					$this->_keys = array($this->_keys);
				} else {
					$this->_keys = array();
				}
			}
		}

	}
	

