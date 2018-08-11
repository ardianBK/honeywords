<h1>Hello, <?php echo $username;?></h1>
	<p>
		<strong>Hai, <?php echo $username;?> telah terjadi aktivitas/perlakuan yang diduga merupakan kegiatan Brute-Force terhadap password akun anda pada <?php echo $waktu;?>. Kami sarankan, untuk segera login kedalam sistem dan mengubah password lama anda ke password yang baru. <br>
		Terima Kasih</strong><br>
		<strong>Silakan klik link ini:</strong><br>
		<?php echo anchor('/honey/aktivasibrute/tk/' . $token);?></p>