<?php
	global $user;
	global $project;
	
	$user->logout();
	$this->redirect($project->login_url);