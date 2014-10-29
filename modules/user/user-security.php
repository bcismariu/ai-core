<?php

	if (isset($_POST['_ai_action']) && ($_POST['_ai_action'] == 'update')) {
		if (($user->security->user_id != $_POST['_ai_user_id']) || ($user->security->username != $_POST['_ai_username'])) {
			$page->addMessage('Something is not right...', 'error');
			$page->redirect('user-security.php');
		}
		if ($user->security->password != $_POST['password_current']) {
			$page->addMessage('Wrong password!', 'error');
			$page->redirect('user-security.php');
		}
		if ($_POST['password_new'] != $_POST['password_confirm']) {
			$page->addMessage('Passwords do not match!', 'error');
			$page->redirect('user-security.php');
		}

		// all checks have been done by now
		$user->security->password = $_POST['password_new'];
		$user->security->update();
		$page->addMessage('Update successfull!', 'success');
		$page->redirect('user-security.php');
	}


?>
<div class="row">
	<div class="col-lg-6 col-md-9">
		<div class="panel panel-danger" id="_ai_user_form">
			<div class="panel-heading">
				<h3 class="panel-title">Update Security Details</h3>
			</div>
			<div class="panel-body">
				<form class="form-horizontal" role="form" method="post" autocomplete="off">
					<input type="hidden" name="_ai_user_id" value="<?=$user->security->_id?>">
					<input type="hidden" name="_ai_username" value="<?=$user->security->username?>">
					<div class="form-group">
						<label class="col-sm-5 control-label">Username</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" name="username" value="<?=$user->security->username?>" disabled>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-5 control-label">Current Password</label>
						<div class="col-sm-7">
							<input type="password" class="form-control" name="password_current" value="">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-5 control-label">New Password</label>
						<div class="col-sm-7">
							<input type="password" class="form-control" name="password_new" value="">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-5 control-label">Confirm New Password</label>
						<div class="col-sm-7">
							<input type="password" class="form-control" name="password_confirm" value="">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-6">
							<input type="hidden" name="_ai_action" value="update">
							<button type="submit" class="btn btn-danger">Update</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
