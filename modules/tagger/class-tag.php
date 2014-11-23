<?php
	/*
		a custom class that manages tags table

	*/

	class aiTag extends aiMySQLTable {

		public function __construct($id = '') {
			parent::__construct(AI_TAGGER_TABLE_TAGS);
			$this->_ai_col = 'tag_id';
			$this->_ai_mode = 'custom';
			$this->_keys = array('project_id', 'tag_id');
			$this->init($id);
		}

	}