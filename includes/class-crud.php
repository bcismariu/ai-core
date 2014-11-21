<?php
	/*
		Create a CRUD interface from an extended aiMySQLTable object


		limitations:
			- only works on unique key tables
				- class-mysql-table has a limitation on select
	
		2014 bogdan.cismariu@gmail.com
	*/

	class aiCRUD {

		public function __construct() {
			$this->search = '';
			$this->page = '1';
			$this->step = '20';
			$this->orderby = '';
			$this->ordertype = 'asc';

			$this->delete = '';
			$this->edit = '';

			$this->url = $_SERVER['SCRIPT_NAME'];

			foreach (get_object_vars($this) as $name => $value) {
				if (isset($_GET[$name])) $this->$name = $_GET[$name];
			}
		}


		public function create($class) {
			global $debug;
			global $page;
			

			if (isset($_POST['_crud_action'])) {
				if ($_POST['_crud_action'] == 'edit') {
					$this->object = new $class($_POST);
					$this->object->update();
					$page->addMessage('Entry updated', 'success');
				}
				if ($_POST['_crud_action'] == 'insert') {
					$this->object = new $class($_POST);
					$this->object->insert();
					$page->addMessage('Entry inserted', 'success');
				}
				$page->redirect($this->buildUrlForPage());
			}

			if ($this->delete != '') {
				$this->object = new $class($this->delete);
				$this->object->delete();
				$page->addMessage('Entry deleted', 'success');
				$page->redirect($this->buildUrlForPage());
			}

			if (!isset($this->object)) {
				if ($this->edit != '') {
					$this->object = new $class($this->edit);
				} else {
					$this->object = new $class();
				}
			}


			
			$data = $this->object->select($this->buildQuery());

			$this->num = $this->object->num($this->buildSearch());

//			$debug->show($this);
//			$debug->show($_POST);
			$this->form();
			$this->entries($data);
		}

		public function buildQuery() {
			$sql = '';
				
			$sql .= $this->buildSearch();

			$sql .= $this->buildOrder();
			$sql .= $this->buildLimit();

			return $sql;
		}

		public function buildSearch() {
			global $mysql;

			if ($this->search == '') {
				return '1 ';
			}

			$conditions = array();

			foreach ($this->object->_cols as $column) {
				$conditions[] = "lower(`$column`) like '%" . $mysql->real_escape_string(strtolower($this->search)) . "%'";
			}
			return implode(" or ", $conditions);
		}

		public function buildOrder() {
			if ($this->orderby == '') {
				return '';
			}
			return " order by $this->orderby $this->ordertype ";
		}

		public function buildLimit() {
			return " limit " . (($this->page - 1) * $this->step) . ", $this->step";	
		}

		public function buildUrlForPage($page = '') {
			if ($page == '') {
				$page = $this->page;
			}
			return $this->url . "?search=$this->search&orderby=$this->orderby&ordertype=$this->ordertype&step=$this->step&page=$page";
		}

		public function entries($data) {



			$this->filter();
			?>

	<div class="table-responsive">
		<table class="table table-striped table-hover">
			<tr>
		<?php foreach ($this->object->_cols as $name) { ?>
				<th><?php echo $name; ?>
		<?php } ?>
				<th>Actions</th>
			</tr>
		<?php foreach ($data as $id => $line) { ?>
			<tr data-id="<?php echo $id; ?>">
			<?php foreach ($this->object->_cols as $name) { ?>
				<td><?php echo $line[$name]; ?></td>
			<?php } ?>
				<td class="text-center">
					<a href="#" class="text-info" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>
					<a href="#" class="text-danger" title="Delete"><span class="glyphicon glyphicon-remove"></span></a>
				</td>
			</tr>
		<?php } ?>
		</table>
	</div>
	<script>
		$('a[title=Delete]').click(function(event) {
			event.preventDefault();
			var id = $(this).parent().parent().attr('data-id');
			var deleteUrl = '<?php echo $this->buildUrlForPage($this->page); ?>' + '&delete=' + encodeURIComponent(id);
			var response = window.confirm('Confirmati stergerea campului ' + id + '?');
			if (response) {
				window.location.href = deleteUrl;
			}
		});
		$('a[title=Edit]').click(function(event) {
			event.preventDefault();
			var id = $(this).parent().parent().attr('data-id');
			var editUrl = '<?php echo $this->buildUrlForPage($this->page); ?>' + '&edit=' + encodeURIComponent(id);
			window.location.href = editUrl;
		});
	</script>
			<?php
			$this->filter();
		}

		public function filter() {
			?>
	<div clas="row">
		<div class="col-md-6">
			<form class="form-inline" role="form">
				<div class="form-group has-feedback">
					<input type="text" name="search" class="form-control" value="<?php echo $this->search; ?>" placeholder="Search...">
					<span class="glyphicon glyphicon-search form-control-feedback"></span>
				</div>
			</form>
		</div>
		<div class="col-md-6 text-right">
			<?php $this->pagination(); ?>
			<?php echo $this->num; ?> results
		</div>
	</div>
			<?php
		}

		public function pagination() {
			$maxPages = ceil($this->num / $this->step);
			// paginile care vor fi afisate
			$pages = array();
			// prima si ultima vor fi afisate mereu
			$pages[] = 1;
			$pages[] = $maxPages;
			// jumatatile vor fi afisate mereu
			$pages[] = floor(($this->page + 1) / 2);
			$pages[] = ceil(($maxPages  + $this->page) / 2);
			// doua pagini inainte si doua in fata
			for ($i = $this->page - 2; $i <= $this->page + 2; $i ++) {
				$pages[] = $i;
			}
			$filtered = array();
			foreach ($pages as $page) {
				if (($page >=1) && ($page <= $maxPages)) {
					$filtered[] = $page;
				}
			}
			$pages = array_unique($filtered);
			sort($pages);

			?>
	<ul class="pagination">
	<?php if ($this->page > 1) { ?>
		<li><a href="<?php echo $this->buildUrlForPage($this->page - 1); ?>">&laquo;</a></li>
	<?php } ?>
	<?php foreach ($pages as $page) { ?>
		<li <?php if ($page == $this->page) { echo 'class="active"'; } ?>><a href="<?php echo $this->buildUrlForPage($page); ?>"><?php echo $page; ?></a></li>
	<?php } ?>
	<?php if ($this->page < $maxPages) { ?>
		<li><a href="<?php echo $this->buildUrlForPage($this->page + 1); ?>">&raquo;</a></li>
	<?php } ?>
	</ul>
			<?php
		}

		public function form() {
			$cssClass = ($this->edit != '') ? 'info' : 'success';
			?>

	<div class="row">
		<div class="col-lg-6 col-md-8">
	<div class="panel panel-<?=$cssClass?>" id="_crud_form">
		<div class="panel-heading"><h3 class="panel-title"><?=($this->edit != '') ? 'Edit' : 'Insert'?> entry <?php echo $this->edit; ?></h3></div>
		<div class="panel-body">
		<form class="form-horizontal" role="form" method="post" autocomplete="off">
			<?php foreach ($this->object->_cols as $column) { ?>
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo $column; ?></label>
					<div class="col-sm-10">
					<?php if ($this->edit != '') { ?>
						<?php if (in_array($column, $this->object->_keys)) { ?>
							<input type="text" class="form-control"
								name="_disabled_<?php echo $column; ?>"
								value="<?=$this->object->$column?>"
								disabled>
							<input type="hidden"
								name="<?php echo $column; ?>"
								value="<?=$this->object->$column?>">
						<?php } else { ?>
							<input type="text" class="form-control"
								name="<?php echo $column; ?>"
								value="<?=$this->object->$column?>">
						<?php } ?>
					<?php } else { ?>
						<input type="text" class="form-control"
							name="<?php echo $column; ?>"
							value="">
					<?php } ?>
					</div>
				</div>
			<?php } ?>
			<?php if ($this->edit != '') { ?>
				<div class="form-group">
					<div class="col-sm-offset-4">
						<input type="hidden" name="_crud_action" value="edit">
						<button type="submit" class="btn btn-<?=$cssClass?>">Update</button>
						<button type="button" class="btn btn-default" onclick="window.location.href='<?=$this->buildUrlForPage()?>'">Cancel</button>
					</div>
				</div>
			<?php } else { ?>
				<div class="form-group">
					<div class="col-sm-offset-4">
						<input type="hidden" name="_crud_action" value="insert">
						<button type="submit" class="btn btn-<?=$cssClass?>">Add</button>
					</div>
				</div>
			<?php } ?>
		</form>
		</div>
	</div>
	<?php if ($this->edit == '') { ?>
		<a href="#" id="_insert_entry">insert entry</a><br><br>
		<script>
			$('#_crud_form').hide();
			$('#_insert_entry').click(function (event) {
				event.preventDefault();
				$('#_crud_form').toggle();
			});
		</script>
	<?php } ?>
		</div>
	</div>
			<?php
		}
	}



