
	<h1>Hello, <?php echo $username;?></h1>
	<p>
		<strong>Hai, <?php echo $username;?> anda menerima email ini karena ada permintaan <br>untuk memperbaharui  
		                 password anda lupa.</strong><br>
		<strong>Silakan klik link ini untuk mengganti password :</strong><br>
		<?php echo anchor('/honey/verifikasi_lupa_password/tk/' . $token);?></p>