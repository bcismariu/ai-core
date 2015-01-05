<?php
	/*
		set up defaults and load classes
	*/

	// you can change this variables in your project files
	if (!isset($aiTaggerMySQL)) {
		$aiTaggerMySQL = $mysql;
	}
	if (!isset($aiTaggerProjectId)) {
		$aiTaggerProjectId = $user->_project_id;
	}

	$defines = array(
			'AI_TAGGER_TABLE_TAGS' => 'ai_tags',
			'AI_TAGGER_TABLE_BINDS' => 'ai_tag_binds',
			'AI_TAGGER_SEPARATOR' => ', '
		);

	foreach ($defines as $key => $value) {
		if (!defined($key)) {
			define($key, $value);
		}
	}

	require_once	'class-tag.php';
	require_once	'class-tagger.php';