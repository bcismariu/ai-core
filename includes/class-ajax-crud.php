<?php

	class aiAjaxCrud {
		public $hide_form = true;

		public function __construct($class) {
			$this->class = $class;
		}

		public function capture() {
			if (isset($_POST['caller']) && ($_POST['caller'] == '_ai_ajax_crud')) {
				if (isset($_POST['edit'])) {
					$obj = new $this->class($_POST['edit']);
					$obj->_ai_ajax_crud_action = 'edit';
					echo json_encode($obj);
					die();
				}
				if (isset($_POST['delete'])) {
					$obj = new $this->class($_POST['delete']);
					$obj->delete();
					$obj = (object) array('_ai_ajax_crud_action' => 'delete');
					echo json_encode($obj);
					die();
				}
			}

			if (isset($_POST['_ai_ajax_crud_action'])) {
				switch ($_POST['_ai_ajax_crud_action']) {
					case 'edit':
						global $debug;
					//	$debug->show($_POST);
						$obj = new $this->class($_POST);
						$obj->update();
						$obj->_ai_ajax_crud_action = 'updated';
						echo json_encode($obj);
						die();
						break;

					case 'insert':
						$obj = new $this->class($_POST);
						$obj->insert();
						$obj->_ai_ajax_crud_action = 'insert';
						echo json_encode($obj);
						die();
						break;

					default:
						break;
				}
			}
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