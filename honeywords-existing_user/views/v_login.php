<!DOCTYPE html>
<html>
<head>
	<title>honeywords</title>
</head>
<body>
	<h1>Login Dengan H O N E Y W O R D S<br/> existing user</h1>
	<?php
$username = array(
		'name'	=> 'username',
		'id'	=> 'username',
		'value' => set_value('username'),
	'maxlength'	=> 80,
	'size'	=> 30,
);

$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'size'	=> 30,
);
$remember = array(
	'name'	=> 'remember',
	'id'	=> 'remember',
	'value'	=> 1,
	'checked'	=> set_value('remember'),
	'style' => 'margin:0;padding:0',
);
?>
<?php echo form_open($this->uri->uri_string()); ?>
<div id="infoMessage"><?php echo $this->session->flashdata('err_message'); ?></div>
<table>
	<tr>
		<td><?php echo form_label('Username', $username['id']); ?></td>
		<td><?php echo form_input($username); ?></td>
		<td style="color: red;"><?php echo form_error($username['name']); ?><?php echo isset($errors[$username['name']])?$errors[$username['name']]:''; ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('Password', $password['id']); ?></td>
		<td><?php echo form_password($password); ?></td>
		<td style="color: red;"><?php echo form_error($password['name']); ?><?php echo isset($errors[$password['name']])?$errors[$password['name']]:''; ?></td>
	</tr>

	<tr>
		<td colspan="3">
			<?php echo form_checkbox($remember); ?>
			<?php echo form_label('Remember me', $remember['id']); ?>
			<?php echo anchor('/honey/email', 'Lupa password'); ?>
			<?php echo anchor('/honey/daftar_register/', 'Register'); ?>
		</td>
	</tr>
</table>
<?php echo form_submit('submit', 'L O G I N'); ?>
<?php echo form_close(); ?>
</body>
</html>