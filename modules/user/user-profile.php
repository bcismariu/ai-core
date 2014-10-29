<?php



	if (isset($_POST['_ai_action']) && ($_POST['_ai_action'] == 'update')) {
		if ($user->security->user_id != $_POST['_ai_user_id']) {
			$page->addMessage('Something is not right...', 'error');
			$page->redirect('user-security.php');
		}

		// all checks have been done by now
		$user->profile = new aiUserProfile($_POST);
		$user->profile->user_id = $_POST['_ai_user_id'];
		$user->profile->update();
		$page->addMessage('Update successfull!', 'success');
		$page->redirect('user-profile.php');
	}
	

?>
<div class="row">
	<div class="col-lg-6 col-md-9">
		<div class="panel panel-success" id="_ai_user_form">
			<div class="panel-heading">
				<h3 class="panel-title">Update Profile Details</h3>
			</div>
			<div class="panel-body">
				<form class="form-horizontal" role="form" method="post" autocomplete="off">
					<input type="hidden" name="_ai_user_id" value="<?=$user->security->_id?>">
					<div class="form-group">
						<label class="col-sm-5 control-label">name</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" name="name" value="<?=$user->profile->name?>">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-6">
							<input type="hidden" name="_ai_action" value="update">
							<button type="submit" class="btn btn-success">Update</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
