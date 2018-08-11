
	<h1>Hello, <?php echo $username;?></h1>
	<p>
		<strong>Hai, <?php echo $username;?> terima kasih sudah mendaftar. Silahkan klik link berikut untuk mengaktifkan akun anda.</strong><br>
		<strong>Silakan klik link ini:</strong><br>
		<?php echo anchor('/honey/aktivasireg/tk/' . $token);?></p>