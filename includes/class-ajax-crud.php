<?php

	class aiAjaxCrud {
		public $hide_form = true;

		public function __construct($class) {
			$this->class = $class;
		}

		public function capture() {
			if ($this->hasAction()) {
				if ($obj = $this->delete()) {
					return $this->outputJson($obj);
				}
				if ($obj = $this->returnData()) {
					return $this->outputJson($obj);
				}
				if ($obj = $this->save()) {
					return $this->outputJson($obj);
				}
			}
		}

		public function hasAction() {
			if (isset($_POST['_ai_ajax_crud_action'])) {
				return true;
			}
			if (isset($_POST['caller']) && ($_POST['caller'] == '_ai_ajax_crud')) {
				if (isset($_POST['edit']) || isset($_POST['delete'])) {
					return true;
				}
			}
			return false;
		}

		public function delete() {
			$obj = false;
			if (isset($_POST['delete'])) {
				$obj = new $this->class($_POST['delete']);
				$obj->delete();
				$obj->_ai_ajax_crud_action = 'delete';
			}
			return $obj;
		}

		public function returnData() {
			$obj = false;
			if (isset($_POST['edit'])) {
				$obj = new $this->class($_POST['edit']);
				$obj->_ai_ajax_crud_action = 'edit';
			}
			return $obj;
		}

		public function save() {
			$obj = false;
			if (isset($_POST['_ai_ajax_crud_action'])) {
				switch ($_POST['_ai_ajax_crud_action']) {
					case 'edit':
						$obj = new $this->class($_POST);
						$obj->update();
						$obj->_ai_ajax_crud_action = 'updated';
						return $obj;
						break;

					case 'insert':
						$obj = new $this->class($_POST);
						$obj->insert();
						$obj->_ai_ajax_crud_action = 'insert';
						return $obj;
						break;

					default:
						break;
				}
			}
			return $obj;
		}

		public function outputJson($obj) {
			die(json_encode($obj));
		}

		public function placeButtons($tableId) {
?>
	<script>

	var ajaxCrudDataTableId = '#<?=$tableId?>';

	ajaxCrudPlaceButtonsOnDataTable();


	</script>
<?php		
		}

		public function useForm($formSelector = 'form[name="ajax_crud"]') {
?>
	<script>
		ajaxCrudForm = '<?=$formSelector?>';

		var acbtns = '<div class="form-group">';
		acbtns += '	<input type="hidden" name="_ai_ajax_crud_action" value="insert">';
		acbtns += '		<div class="col-sm-offset-3">';
		acbtns += '			<button name="_ajax_crud_submit" type="button" class="btn btn-success">Adauga</button>';
		acbtns += '			<button name="_ajax_crud_cancel" type="button" class="btn btn-default">Anuleaza</button>';
		acbtns += '		</div>';
		acbtns += '</div>';

		$(ajaxCrudForm).append(acbtns);

		<?php if ($this->hide_form) { ?>
			$(ajaxCrudForm).hide();
		<?php } ?>
		ajaxCrudFormDefaults = ajaxCrudFormGetDefaults(ajaxCrudForm);

		$(ajaxCrudForm).submit(function (event){
			event.preventDefault();
		});
		$(ajaxCrudForm + ' button[name="_ajax_crud_submit"]').click(function() {
			$(ajaxCrudForm).unbind();
			$.post(location.pathname, $(ajaxCrudForm).serialize(), ajaxCrudCapture);
		});
		
		$(ajaxCrudForm + ' button[name="_ajax_crud_cancel"]').click(function() {
			ajaxCrudFillForm(ajaxCrudFormDefaults);
			<?php if ($this->hide_form) { ?>
				$(ajaxCrudForm).hide();
			<?php } ?>
			$(ajaxCrudDataTableId).find('tr').removeClass('warning');
		});


	</script>
<?php
		}
	}