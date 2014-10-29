<?php
	/*
		this should be completely rewritten
		
		2013 bogdan.cismariu@gmail.com
	*/
	class AIMySQL extends mysqli {
	
		private $query = '';
		private $result = '';

	
		public function query($query) {
			$this->query = $query;
			$this->result = @parent::query($this->query) or die($this->query . $this->error);
			return $this->result;
		}
		
		public function get($sql) {
			$rows = array();
			$result = $this->query($sql);
			while ($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			return $rows;
		}

	}


?>