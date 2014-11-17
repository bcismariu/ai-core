<?php
	/*
		creating a datatable for a class extended from mysql-table
	*/


	class aiDataTable {
		public $settings;
		public $columns;
		public $header = '';
		public $table = '';
		public $filter = '1';
		public $ajax_url = '';
		
		public function __construct($class) {
			$this->class = $class;
			$this->ajax_url = $_SERVER['PHP_SELF'];
		}

		public function ajax() {
			/*
				uses the data received through POST
				returns json econded format expected by datatables
			*/
			
			if (!$this->validatePost()) {
				// no request was made
				return false;
			}

			if (!$this->validateColumns()) {
				$this->error('aiDataTable::columns has to be an array');
			}

			$obj = new $this->class();
			$sql = $this->buildFilter() . ' ' . $this->buildOrder() . ' ' . $this->buildLimit();
			$intrari = $obj->select($sql, $this->columns, $this->table);

			$data = array();
			foreach ($intrari as $k => $i) {
				$i['DT_RowData'] = (object) array('id' => $k);
				array_push($data, (object) $i);
			}

			$result = array(
				'draw' => $_POST['draw'],
				'recordsTotal' => $obj->num($this->filter, $this->table),
				'recordsFiltered' => $obj->num($this->buildFilter(), $this->table),
				'data' => $data
				);

			$this->respond($result);
		}

		public function draw(){
			// prints the table
			// @return javascript variable name that refers to the table
			if ($this->header == '') {
				$this->header = $this->columns;
			}
			$cols = array_slice($this->columns, 0, count($this->header));
			$id = $this->class . '_datatable';
?>
			<table class="table table-striped table-hover" id="<?=$id?>">
			 	<thead>
				<tr>
				<?php foreach ($this->header as $h) { ?>
					<th><?=$h?></th>
				<?php } ?>
					<th><!-- pentru actiuni --></th>
				</tr>
				</thead>

			</table>

	  <script>
$(document).ready(function() {
    <?=$id?> = $('#<?=$id?>').DataTable({
    	'processing': true,
    	'serverSide': true,
    	'dom': 	'<"row"<"#datatable_corner.col-sm-6"><"col-sm-6"f>>' +
    			'<"row"<"col-xs-12"t>>' +
    			'<"row"<"col-sm-4"l><"col-sm-4"i><"col-sm-4"p>>',
    	'ajax': {
    		'url': '<?=$this->ajax_url?>',
    		'type': 'POST'
    	},
    	'columns': [
    	<?php foreach ($cols as $c) { ?>
    		{ 'data': '<?=$c?>' },
    	<?php } ?>
    		{
    			'class': 'hidden',
    			'orderable': false,
    			'data': null,
    			'defaultContent': '',
    			'width': '50px'
    		}
    	],
    	'language': {
				    "emptyTable":     "Nu exista inregistrari. Foloseste butonul + pentru a adauga.",
				    "info":           "Arat de la _START_ la _END_ din _TOTAL_ inregistrari",
				    "infoEmpty":      "Nu am ce arata",
				    "infoFiltered":   "<br>(filtrat din totalul de _MAX_ inregistrari)",
				    "infoPostFix":    "",
				    "thousands":      ",",
				    "lengthMenu":     "Arata _MENU_ inregistrari",
				    "loadingRecords": "Se incarca...",
				    "processing":     "Se proceseaza...",
				    "search":         "Cauta:",
				    "zeroRecords":    "Nu am gasit inregistrari care sa corespunda criteriilor de cautare.",
				    "paginate": {
				        "first":      "Prima",
				        "last":       "Ultima",
				        "next":       "Inainte",
				        "previous":   "Inapoi"
				    },
				    "aria": {
				        "sortAscending":  ": activate to sort column ascending",
				        "sortDescending": ": activate to sort column descending"
				    }
				}
    });
});
	  </script>
<?php
			return $id;
		}

		private function buildFilter() {
			// @return string
			if ($this->filter == '') {
				$this->filter = 1;
			}
			if ($_POST['search']['value'] == '') {
				return ' ' . $this->filter;
			}
			$filter = array();
			foreach ($this->columns as $c) {
				$filter[] = $c . ' like "%' . $_POST['search']['value'] . '%"';
			}
			return  ' ' . $this->filter . ' and (' . implode(' or ', $filter) . ') ';
		}

		private function buildOrder() {
			// @return string
			$order = array();
			foreach ($_POST['order'] as $i => $v) {
				$order[] = $_POST['columns'][$v['column']]['data'] . ' ' . $v['dir'];
			}
			if (count($order)) {
				return " order by " . implode(', ', $order);
			}
			return '';
		}

		private function buildLimit() {
			// @return string
			return " limit $_POST[start], $_POST[length]";
		}

		private function validateColumns() {
			// @return boolean
			return is_array($this->columns);
		}

		private function validatePost() {
			// @return boolean
			$expectedKeys = array('draw', 'start', 'length', 'search', 'order', 'columns');
			return (array_intersect($expectedKeys, array_keys($_POST)) == $expectedKeys);
		}

		private function respond($object) {
			echo json_encode($object);
			die();
		}

		private function error($message) {
			$this->respond(array('error' => $message));
		}
	}