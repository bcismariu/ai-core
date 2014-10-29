<?php
	/*
		login functionality
		called from within the Page class
	*/

	global $debug;
	global $user;
	global $project;

	if (isset($_POST['_ai_login_submit']) && ($_POST['_ai_login_submit'] == 'ai-submit')) {
		$result = $user->login($_POST['_ai_username'], $_POST['_ai_password']);
		if ($result) {
			$this->redirect($project->login_success_url);
		}
	}


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title><?=$this->title?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?=$project->assets_url?>/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?=$project->assets_url?>/themes/default/signin.css" rel="stylesheet">

  </head>

  <body>

    <div class="container">

      <form class="form-signin" role="form" method="post">
        <h2 class="form-signin-heading"><?=$project->name?></h2>
        <input type="hidden" name="_ai_login_submit" value="ai-submit">
        <input type="text" name="_ai_username" class="form-control" placeholder="username" required autofocus>
        <input type="password" name="_ai_password" class="form-control" placeholder="password" required>
        <!--
        <label class="checkbox">
          <input type="checkbox" value="remember-me"> Remember me
        </label>
        -->
        <button class="btn btn-lg btn-primary btn-block" type="submit">Log In</button>
      </form>

    </div> <!-- /container -->


  </body>
</html>