<?php
	global $project;
  	include $project->path . '/includes/menu.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Asociatia pentru Inovatie">
    <link rel="shortcut icon" href="favicon.ico">

    <title><?=$this->title?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?=$project->assets_url?>/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?=$project->assets_url?>/css-jquery-ui/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">

    

    <!-- Custom styles for this template -->
    <link href="<?=$project->assets_url?>/themes/default/dashboard.css" rel="stylesheet">

  
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

        <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="<?=$project->assets_url?>/jquery/jquery.min.js"></script>
    <script src="<?=$project->assets_url?>/jquery/jquery-ui.min.js"></script>
    <script src="<?=$project->assets_url?>/jquery/jquery.dataTables.min.js"></script>
    <script src="<?=$project->assets_url?>/jquery/jquery.dataTables.bootstrap.js"></script>
    <link  href="<?=$project->assets_url?>/jquery/jquery.dataTables.bootstrap.css" rel="stylesheet">

    <script src="<?=$project->assets_url?>/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>

	<script src="<?=$project->assets_url?>/js/dropdown-menu.js"></script>
	<script src="<?=$project->assets_url?>/js/ajax-crud.js"></script>
	<script src="<?=$project->assets_url?>/js/datepicker.js"></script>

   </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="#"><?=$project->name?></a>
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
         
         
				<ul class="nav nav-sidebar">
			<?php foreach ($_aiMenuSidebar as $mitem) { 
					if ($mitem[0] == '--') {
						?>
						</ul>
						<ul class="nav nav-sidebar">
						<?php
						continue;
					}
					if (!is_array($mitem[2])) { ?>
					<li><a href="<?php echo $project->url . '/' . $mitem[2] ?>"><span class="glyphicon <?php echo $mitem[1]; ?>"></span> <?php echo $mitem[0]; ?></a></li>
				<?php 	} else { ?>
					<li class="dropdown"><a href="#"><span class="glyphicon <?php echo $mitem[1]; ?>"></span> <?php echo $mitem[0]; ?></a>
						<ul>
						<?php foreach ($mitem[2] as $subitem) { ?>
							<li><a href="<?php echo $project->url . '/' . $subitem[1]; ?>"><?php echo $subitem[0]; ?></a></li>
						<?php } ?>
						</ul>
					</li>
				<?php	} ?>
			<?php } ?>
				</ul>
		
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

	<h1 class="page-header"><?php echo $this->title; ?></h1>

	<!-- Messages -->
	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<?php
				$m = $this->getMessages( 'error' );
				if (is_array($m) && (count($m) > 0)) {
			?>
				<div class="alert alert-danger alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php foreach ($m as $message) {
						echo $message;
						} 
					?>
				</div>
			<?php	} ?>
			<?php
				$m = $this->getMessages( 'warning' );
				if (is_array($m) && (count($m) > 0)) {
			?>
				<div class="alert alert-warning alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php foreach ($m as $message) {
						echo $message;
						} 
					?>
				</div>
			<?php	} ?>
			<?php
				$m = $this->getMessages( 'success' );
				if (is_array($m) && (count($m) > 0)) {
			?>
				<div class="alert alert-success alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php foreach ($m as $message) {
						echo $message;
						} 
					?>
				</div>
			<?php	} ?>

		</div>
	</div>
