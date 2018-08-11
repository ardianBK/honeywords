<!DOCTYPE html>
<html>
<head>
	<title>honeywords</title>
</head>
<body>
	<h1>: ) </h1>
	<div id="infoMessage"><?php echo $this->session->flashdata('err_message'); ?></div>
   <table>
	selamat datang 
	<?php echo $this->session->userdata('username'); ?>
	
	<?php echo anchor('/honey/gantipass/', 'Ganti Password'); ?>
	<?php echo anchor('/honey/keluar', ' keluar'); ?>
</body>
</html>