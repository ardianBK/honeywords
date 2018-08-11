<!DOCTYPE html>
<html>
<head>
	<title>honeywords</title>
</head>
<body>
	<h1>Inisialisasi Sistem <br/> H O N E Y W O R D S</h1>
	<div id="infoMessage"><?php echo $this->session->flashdata('err_message'); ?></div>
	<form action="passpool" method="post">		
		<table>
			<tr>
				<td></td>
				<td><input type="submit" value="Bangkitkan PasswordPool"></td>
			</tr>
		</table>
	</form>

	<form action="fakeuser" method="post">		
		<table>
			<tr>
				<td></td>
				<td><input type="submit" value="Input Fake Username"></td>
			</tr>
		</table>
	</form>

</body>
</html>