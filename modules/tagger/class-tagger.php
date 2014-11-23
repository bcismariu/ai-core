<?php
	/*
		A custom class that handles tagging for various elements

		2014 bogdan.cismariu@gmail.com

	*/

	class aiTagger {

		/*
			if this var is not set the class will fail
		*/
		protected $project_id;

		public function __construct() {
			global $aiTaggerMySQL, $aiTaggerProjectId;
			$this->mysql = $aiTaggerMySQL;
			$this->project_id = $aiTaggerProjectId;
			$this->table_tags = AI_TAGGER_TABLE_TAGS;
			$this->table_binds = AI_TAGGER_TABLE_BINDS;
			$this->tag_separator = AI_TAGGER_SEPARATOR;
		}

		public function bindTags($tagList = '', $element_id = '', $description = '') {
			// identify all tags - explode list
			// recover tag ids
			// identify new tags
			// insert new tags
			// recover new tags id
			// remove from table_binds existing binds
			// insert into table_binds new binds

			if (($tagList == '') || ($element_id == '')) {
				return false;
			}

			$tags = explode($this->tag_separator, $tagList);
			$search = array();
			foreach ($tags as &$tag) {
				$tag = trim($tag);
				if ($tag == '') {
					unset($tag);
					continue;
				}
				$search[] = $this->mysql->real_escape_string($tag);
			}
			unset($tag);

			$ids = array();
			$found = array();
			$sql = "select
						tag_id,
						tag_name
					from
						$this->table_tags
					where
						project_id = $this->project_id and
						tag_name in ('" . implode("', '", $search) . "')
					";
			$result = $this->mysql->query($sql);
			while ($row = $result->fetch_assoc()) {
				$ids[] = $row['tag_id'];
				$found[] = $row['tag_name'];
			}

			$new = array_diff($search, $found);
			foreach ($new as $tag_name) {
				$tag = array(
							'project_id' => $this->project_id,
							'tag_name' => $tag_name
						);
				$tag = new aiTag($tag);
				$tag->insert();
				$ids[] = $tag->_id;
			}

			$sql = "delete from 
						$this->table_binds
					where
						project_id = $this->project_id
						and description = '$description'
						and element_id = $element_id
					";
			$result = $this->mysql->query($sql);

			$values = array();
			foreach ($ids as $id) {
				$values[] = "($this->project_id, $element_id, $id, '$description')";
			}
			$sql = "insert into 
						$this->table_binds (project_id, element_id, tag_id, description)
						values " . implode(', ', $values);
			$result = $this->mysql->query($sql);

		}

		public function getTagListFor($element_id = '', $description = '') {
			if ($element_id == '') {
				return '';
			}
			$list = array();
			$sql = "select
						tag_name
					from
						$this->table_binds b
						inner join
							$this->table_tags t
						on
							b.project_id = t.project_id
							and b.tag_id = t.tag_id
					where
						b.element_id = $element_id
						and b.description = '$description'
						";
			$result = $this->mysql->query($sql);
			while ($row = $result->fetch_assoc()) {
				$list[] = $row['tag_name'];
			}
			return implode($this->tag_separator, $list);
		}


		public function editFor($element_id = '', $description = '') {
			$list = $this->getTagListFor($element_id, $description);
			?>
			<input type="text" name="_ai_tagger_input" class="form-control" value="<?=$list?>">
			<?php
		}	

		public function captureFor($element_id = '', $description = '') {
			if (!isset($_POST['_ai_tagger_input'])) {
				return false;
			}
			$this->bindTags($_POST['_ai_tagger_input'], $element_id, $description);
		}


	}





















