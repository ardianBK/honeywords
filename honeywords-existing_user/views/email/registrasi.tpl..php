<html>
<body>
	<h1>Hello, <?php echo $username;?></h1>
	<p>
		<strong>Hai, <?php echo $username;?> terima kasih sudah mendaftar. Silahkan klik link berikut untuk mengaktifkan akun anda.</strong><br>
		<strong>Silakan klik link ini:</strong><br>
		<?php echo anchor(site_url() . '/honey/aktivasi/tk/' . $token_qstring);?></p>
</body>
</html>