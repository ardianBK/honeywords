
	<h1>Hello, <?php echo $username;?></h1>
	<p>
		<strong>Hai, <?php echo $username;?> anda menerima email ini karena 
		   terdapat seseorang yang terduga telah melakukan cracking dan mengetahui password anda. Segera ganti password anda!!.</strong><br>
		<strong>Silakan klik link ini:</strong><br>
		<?php echo anchor('/honey/aktivasi_cracking/' . $token);?></p>