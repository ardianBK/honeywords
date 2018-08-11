<?php defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * 
 * @package		Honeywords Existing User / from Imran Erguler 2015
 * @version		1.0
 * @author		Ardian Budi Kusuma
 * @link		-
 */

require_once(APPPATH."random_compat/lib/random.php");

class Honeywords {


	/**
	 * CodeIgniter
	 *
	 * @access	private
	 */
	private $ci;


	/**
	 * Config items
	 *
	 * @access	private
	 */
	private $username_table;
	private $id1_field;
	private $username_field;
	private $indekshoney_field;

	private $password_table;
	private $id2_field;
	private $indekspass_field;
	private $password_field;
	private $asin_field;

	private $usernamedata;
	private $data_table;
	private $aktif;

	private $passpool_config;

	/**
	 * Constructor
	 */
	function __construct()
	{

		// Assign CodeIgniter object to $this->ci
		$this->ci =& get_instance();

		// Load config
		$this->ci->config->load('authentic');
				$satu_config = $this->ci->config->item('satu');
				// ini Set config items-nya
				$this->username_table = $satu_config['username_table'];
				$this->id1_field = $satu_config['identifier1_field'];
				$this->username_field = $satu_config['username_field'];
				$this->indekshoney_field = $satu_config['indekshoney_field'];

				$dua_config = $this->ci->config->item('dua');
				// ini Set config items-nya
				$this->password_table = $dua_config['password_table'];
				$this->password_field = $dua_config['password_field'];
				$this->indekspass_field = $dua_config['indekspass_field'];

				$tiga_config = $this->ci->config->item('tiga');
				$this->data_table = $tiga_config['data_table'];
				$this->usernamedata = $tiga_config['data_username_filed'];
				$this->aktif = $tiga_config['aktifgak']; //ambil nilai 0 / 1 aktif

		// Load libraries
		$this->ci->load->library('session');
		// Load database
		$this->ci->load->database();
		$this->ci->load->model('ci_ta/model_pertama');
		$this->ci->load->library(array('email'));
		
		//hash
		$this->ci->load->library('SHA3_256');
		//email
		$email_config = $this->ci->config->item('email_config', 'authentic');
		if ($this->ci->config->item('use_email', 'authentic') && isset($email_config) && is_array($email_config))
		{
			$this->ci->email->initialize($email_config);
		}
		
		//inisialisasi passpool
		//$passpool_config = $this->ci->config->item('banyak_password_pool', 'authentic');
	}


	/**
	 * Pembangkitan password-pool
	 *
	 * @access	public
	 * @return	boolean
	 */
	public function hasil_password_pool()
	{

		$kamus = '';
		$myfile = fopen("application/libraries/passwordpool.txt", "w") or die("Tidak bisa membuka file!");
		if(!file_exists("application/libraries/".$this->ci->config->item('nama_kamus').".txt")) {
			//die("Tidak bisa membuka file!");
			$this->ci->session->set_flashdata('err_message', 'Tidak bisa membuka file!');
			return false;
		} else {
			$kamus = file_get_contents('application/libraries/'.$this->ci->config->item('nama_kamus').'.txt');
		}
		$words = explode(PHP_EOL, $kamus);
		
		//hitung jumlah kata
		$total_kata = count($words);
		//$kata = '';
		//sleep(10);

	for($j=0;$j<$this->ci->config->item('banyak_password_pool');$j++)
	{

		$acak = random_int(0, $total_kata);
		$kata1 = $words[$acak];
		$acak = random_int(0, $total_kata);
		$kata2 = $words[$acak];

		$kata = $kata1.$kata2;

		//jika kurang dari 16 concat dgn angka
		while(strlen($kata)<16)
		{
			$angka = random_int(0, 999);
			$kata = $angka.$kata;

		} 

		if(strlen($kata)>19)
		{
			$kata = substr($kata, 0, 18);
		}

		//tulis di database
		$salt = $this->salt();
		//hash passwordnya
		$passbetul = SHA3_256::hash($kata.$salt);
		//pass.salt + salt PAK FAIZAL
		$hashpass = $passbetul.$salt;
					
			//random indeks pass
			$nilai = random_int(1, 2147483647);

			// Define data to insert
			$datatabel2 = array(
				$this->password_field => $hashpass,
				//$this->password_field => $hashpass
				$this->indekspass_field => $nilai
			);

			// If inserting data fails
			if ( ! $this->ci->db->insert($this->password_table, $datatabel2))
			{
				// Return false
				return FALSE;
			}
		//simpan file ke txt jika diinginkan
		if($this->ci->config->item('simpan_password_pool') == true)
		{
			$string = 'Password ke '.$nilai.' = '.$kata.''.PHP_EOL.'';
			//nilai hapus nanti
			fwrite($myfile, $string);
		}

	}

	return TRUE;
	}
	


	/**
	 * Input fake-username
	 *
	 * @access	public
	 * @return	integer|boolean Either the user ID or FALSEupon failure
	 */
	public function masuk_fakeusername()
	{

		
		if($this->cek_inisialisasi() == FALSE)
		{
			return FALSE;
		}

		//buka file isi fakenamenya
		$data = '';
		if(!file_exists("application/libraries/".$this->ci->config->item('nama_file_fakeusername').".txt")) {
			//die("Tidak bisa membuka file!");
			$this->ci->session->set_flashdata('err_message', 'Tidak bisa membuka file!');
			return false;
		} else {
			$data = file_get_contents('application/libraries/'.$this->ci->config->item('nama_file_fakeusername').'.txt');
		}
		$username = explode(PHP_EOL, $data);
		
		//random untuk honey select dari indekspass
		//SELECT ID PALING TINGGI di tabel2
		
		
		//BANGKIT 20 NILAI RANDOM LAINNYA
		$p = 0; //indeks username

		do{

		$koma = ","; $rand = "";
		$k = 0; $gabung= null; $rand = null;

			do{
				//ambil indeks pass secara acak
				$this->ci->db->order_by($this->indekspass_field, 'RANDOM');
				$this->ci->db->limit(1);
				$query = $this->ci->db->get('tabel2');
				//$idmax = $query->result();
				$idmax ='';
				foreach ($query->result() as $row)
				{
		    		$idmax = $row->indeks_pass;
				}
				
				//ambil indeks
				$indeks = $this->ci->db
				->where($this->indekspass_field, $idmax)
				->get($this->password_table)
				->row($this->indekspass_field);

				if($indeks == null){
					$k = $k;
				} else {
					$gabung[$k] = $indeks;
					$k++;
				}
				
			} while ( count($gabung)<20);


			for($i=0;$i<20;$i++){ //jadikan barisan 20 indeks
				$rand = $gabung[$i].$koma.$rand;
			}

			$datatabel1 = array(
				$this->username_field => $username[$p],
				$this->indekshoney_field => $rand
			);

			// If inserting data fails
			if ( ! $this->ci->db->insert($this->username_table, $datatabel1))
			{
				// Return false
				return FALSE;
			}

			$p++;
		} while ($p < count($username)); //masukin ke username 1 per 1

		return TRUE;
	}

     //cek apakah password pool sudah ada
	function cek_inisialisasi()
	{
		$cek = $this->ci->db->select('indeks_pass')->get('tabel2');

		if($cek->num_rows() == null)
		{
			return FALSE; //belum ada pass palsu
		} else {

			return TRUE; //sudah ada pass palsu
		}

	}

	/**
	 * Cek apakah email sudah ada dalam database atau tidak
	 *
	 * @access	public
	 * @param	string [$email]
	 * @return	boolean
	 */
	public function cek_email($email)
	{
		if($this->ci->model_pertama->cekemail($email) == TRUE)
		{
			$this->ci->session->set_flashdata('err_message', 'Email tidak ada!');
			return FALSE;
		} else {
			return TRUE;
		}
	}


	/**
	 * Cek apakah username ada dalam database atau tidak
	 *
	 * @access	public
	 * @param	string [$username] The username to query
	 * @return	boolean
	 */
	public function cek_username($username)
	{

		// cari username terkait
		$query = $this->ci->db->where($this->username_field, $username)->get($this->username_table);
		// jika ada
		if ($query->num_rows() > 0)
		{
			// False -> username ada
			return FALSE;
		}
		// true -> tidak ada
		return TRUE;
	}


	/**
	 * Check password apakah masuk common list
	 *
	 * @access	public
	 * @param	string [$username] The username to query
	 * @return	boolean
	 */
	public function cek_password($password, $username)
	{

		$lispass = explode(PHP_EOL, file_get_contents('application/libraries/'.$this->ci->config->item('nama_common_pass').'.txt'));
		
		$jumlah = (count($lispass));

		$k = 0;
		do{
			$cek = strpos($password, $lispass[$k]);

			if($cek !== false){
				$this->ci->session->set_flashdata('err_message', 'Password anda mengandung kata yang lemah!');
				return FALSE; // lemah, masuk common
				$k = $jumlah;
			}

		$k++;
		} while ($k<$jumlah);

		//ada kaitan dengan username gak?
		$cari = strpos($password, $username);
		if($cari !== false){
			$this->ci->session->set_flashdata('err_message', 'Password dan username jangan ada kesamaan!');
				return FALSE; // lemah
		} else { return true; } //tidak ada kaitan

		return true; // no common
	}

	/**
	 * Check password apakah pass lama benar
	 *
	 * @access	public
	 * @param	string [$username] username yg dicek
	 * @param	string [$password] password yg dicek
	 * @return	boolean true -> cocok , false -> tidak cocok
	 */
	//cek untuk pass lama sama ngga -> ganti pass
	public function cekpasslama($password, $username)
	{
		
		// Select user details
		$user = $this->ci->db
			->select($this->id1_field.' as identifier, '.$this->username_field.' as username, '.$this->indekshoney_field.' as indeks')
			->where($this->username_field, $username)
			->get($this->username_table);


		// Set the user details
		$user_details = $user->row();
		$honeyindeks = $user_details->indeks;
		
		//PECAHKAN INDEKS BR TANDA ","
		$indekss = explode(",",$honeyindeks);
		//CARI PANJANGNYA
		$pjgarai = count($indekss);

				
		$j=0;
		while ($j<$pjgarai)
			{
					//CARI INDEKS YG SESUAI PASS
				$honeypass = $this->ci->db
					->select($this->password_field.' as password')
					->where($this->indekspass_field, $indekss[$j])
					->get($this->password_table);

				//AMBIL NILAI PASS-nya
				foreach ($honeypass->result() as $row) {
					$honeyindeks = $row->password;
					$index = $indekss[$j]; //indeks pass yang diambil
					$garam = substr($honeyindeks, -16);
					$pass = substr($honeyindeks, 0, 64);
				}

				$hashpass = SHA3_256::hash($password.$garam);

				if ($hashpass == $pass)
				{
					//ini perbandingan loginnya --> honeychecker bekerja disini -> MEMBANDINGKAN nilai honeyindeks dg indeks_real
					if($this->ci->model_pertama->cek_indeks_login($index, $username) == TRUE)
					{ 
						$j = $j + $pjgarai;

						return TRUE;

					} else {
						return FALSE;
					}
				// passwords tidak cocok
				}
				$j++;
			}
		if ($hashpass != $pass)
		{
			// gak ada yg cocok
			return FALSE;
		}

	}

	/**
	 * Login
	 *
	 * @access	public
	 * @param	string [$username] username yg diautentikasi
	 * @param	string [$password] password yg dimasukkan
	 * @param	string [$remember] nilai untuk remember
	 * @return	boolean true -> berhasil autentikasi , false -> gagal
	 */
	public function login($username, $password, $remember)
	{

		// ambil data username di tabel 1
		$user = $this->ci->db
			->select($this->id1_field.' as identifier, '.$this->username_field.' as username, '.$this->indekshoney_field.' as indeks')
			->where($this->username_field, $username)
			->get($this->username_table);

		// CARI TAHU EXIST NGGAK USERNAME
		if ($user->num_rows() == 0)
		{
			// tidak ada username tersebut
			$this->ci->session->set_flashdata('err_message', 'Login gagal!');
			return FALSE;

		}

		//CARI TAHU AKUN AKTIF NGGAK
		if($akunaktif == true)// true -> tidak aktif
		{
			$this->ci->session->set_flashdata('err_message', 'Usernamee tidak aktif!');
			return FALSE;
		}

		// data honeyindeks username diambil
		$user_details = $user->row();
		$honeyindeks = $user_details->indeks;
		
		//PECAHKAN INDEKS BRDSAR TANDA ","
		$indekss = explode(",",$honeyindeks);
		//CARI PANJANGNYA
		$pjgarai = count($indekss);

				
		$j=0;
		while ($j<$pjgarai)
			{
					//CARI INDEKS YG SESUAI PASS
				$honeypass = $this->ci->db
					->select($this->password_field.' as password')
					->where($this->indekspass_field, $indekss[$j])
					->get($this->password_table);

				//AMBIL NILAI PASS-nya
				foreach ($honeypass->result() as $row) {
					$honeyindeks = $row->password;
					$index = $indekss[$j]; //indeks pass yang diambil
					$garam = substr($honeyindeks, -16);
					$pass = substr($honeyindeks, 0, 64);
				}

				$hashpass = SHA3_256::hash($password.$garam);

				//ada cocokan
				if ($hashpass == $pass)
				{
					//ini perbandingan loginnya --> honeychecker bekerja disini -> MEMBANDINGKAN nilai honeyindeks dg indeks_real
					if($this->ci->model_pertama->cek_indeks_login($index, $username) == TRUE)
					{ $j = $j + $pjgarai;

							$this->ci->session->set_userdata(array(
								'user_id'	=> $user_details->identifier,
								'username'	=> $username,
							));
						if($remember)
						{ //jika remember dipilih

							$cookies = substr(sha1(rand()), 0, 32);
							$this->ci->input->set_cookie($this->ci->config->item('nama_cookies'), $cookies, $this->ci->config->item('cookies_expire'));
							//simpan ke db utk remember
							$this->ci->model_pertama->simpan_cookies($cookies, $username);

						}
					
					//benar bisa masuk
					$this->hapus_salah_login($username);
					return TRUE;

					} else {

						//akun dinonaktifkan
						$this->ci->model_pertama->deaktifasiakun($username);

						//catat logging
						if($this->ci->config->item("catat_logging") == true)
						{
							$jenis_log = "cracking-password";
							$this->ci->model_pertama->catat_log($username, $jenis_log);
						}

						//lakukan cracking EMAIL & link reset pass
						$crack = $this->cracking($username, $hashpass.$garam);
	
						//hapus kesalahan
						$this->hapus_salah_login($username);

						return FALSE;
					}
				// passwordd tidak cocok
				}
				$j++;
			}
		/// COUNT JIKA TIDAK SAMA PASS YG MASUKIN, satu saja
		if ($hashpass != $pass) {
						$this->maks_login($username);
						return FALSE;
				}

	}


	public function remember()
	{
		// ambil cookie
			$cookie = get_cookie($this->ci->config->item('nama_cookies'));
			//gak ada session -> cek cokkies
			$kukis = $this->ci->model_pertama->get_cookies($cookie); //dapet nilai username
			if(!$kukis == null){
				//buat session
				$this->ci->session->set_userdata(array(
								'user_id'	=> '1',
								'username'	=> $kukis,
							));
				return true;

			} else {
				//cookies expired atau gak ada
				return false;
			}
	}

	/**
	 * Ketika terdeteksi cracking
	 *
	 * @access	public
	 * @param	string [$username] username yg diautentikasi
	 * @param	string [$password] password yg dimasukkan -> yg terdeteksi cracking
	 * @return	
	 */
	public function cracking($username, $password)
	{
		//get uindekx dr pass yg di crack
		$indeks_pass = $this->ci->db->select($this->indekspass_field.' as indeks_pass')
		->where($this->password_field, $password)
		->get($this->password_table);

		$details = $indeks_pass->row();
		$idx = $details->indeks_pass;

		//get username from indeks benar
		$username2 = $this->ci->model_pertama->get_usr_idx($idx);
		
		//get email from username
		$email = $this->ci->model_pertama->get_email_username($username);
		//get email user2
		$email2 = $this->ci->model_pertama->get_email_username($username2);
		/*
		//hapus data di honeychecker user1..
		$this->ci->model_pertama->delete_resetpass($username);
		//hapus data di honeychecker user2
		$this->ci->model_pertama->delete_resetpass($username2);
		*/
		//get indeks benar from user1, hapus di tabel2
		//$indeks = $this->ci->model_pertama->get_idx_usr($username);
		/*$this->ci->db->where($this->indekspass_field, $indeks)
    	->delete($this->password_table);

    	//get indeks benar from user2, hapus di tabel2
		$this->ci->db->where($this->indekspass_field, $idx)
    	->delete($this->password_table);*/


   		//TRY KIRIM EMAIL
 			$email_array = array($email, $email2);
 			$username_array = array($username, $username2);

 			for($i=0;$i<2;$i++)
 			{
 				$token_qstring = $this->Token($email_array[$i], 40);

 				$usernamenya = $username_array[$i];
 				//data variabel utk di email (username dan token_qstring)
				$data = array(
					'username' => $usernamenya,
					'token' => $token_qstring
				);

				//load pesan utk email
				$message = $this->ci->load->view('/email/cracking', $data, TRUE);

				$this->ci->email->set_newline("\r\n");  
				$this->ci->email->from('Honeywords@genesis.com', 'Admin');   
				$this->ci->email->to($email_array[$i]);   
				$this->ci->email->subject('DETEKSI CRACKING !!');

				$this->ci->email->message($message);
	            //echo $message; //send this through mail
	             //exit;

			    if (!$this->ci->email->send()) {  
			    show_error($this->ci->email->print_debugger());
			    
			   	}else{  
			    //echo 'Success to send email'; 
			    return true;  
			   }

 			}
		   
	}

	/**
	 * Register pengguna
	 * @access	public
	 * @param	string [$username] username yg nanti digunakan utk autentikasi -> tidak boleh ada yg sama
	 * @param	string [$password] password yg nanti digunakan utk autentikasi
	 * @param	string [$email] email untuk proses aktivasi, lupa password, deteksi cracking dll
	 * @param	string [$namalengkap] data dari pengguna
	 * PENGEMBANG BISA MENAMBAHKAN KOMPONEN DATA PENGGUNA SESUAI KEBUTUHAN
	 * @return	boolean, true -> berhasil daftar , false -> gagal daftar
	 */
	public function registrasi($username, $password, $email, $namalengkap)
	{
		// cek username bisa digunakan atau tidak
		if ($this->cek_username($username) == false )
		{
			// Username tidak bisa digunakan
			$this->ci->session->set_flashdata('err_message', 'Username is used!');
			return FALSE;
		}

		// cek email bisa digunakan atau tidak

		if ( $this->cek_email($email) == TRUE)
		{
			// email tidak bisa digunakan
			$this->ci->session->set_flashdata('err_message', 'email is used!');
			return FALSE;
		}
		
		// cek password ada common atau tidak
		if ( $this->cek_password($password, $username) == FALSE)
		{
			// password tidak bisa digunakan
			return FALSE;
		}

		//simpan data pengguna ke DB
		$id = $this->ci->model_pertama->simpan_data($username, $email, $namalengkap);

		// salt untuk pass
		$salt = $this->salt();
		//hash passwordnya
		$hashpass = SHA3_256::hash($password.$salt);

		
		//BANGKIT 20 NILAI RANDOM LAINNYA
		$koma = ","; $rand = "";
		$k = 0; $gabung= null; $rand = null;


			do{
				//ambil indeks pass secara acak
				$this->ci->db->order_by($this->indekspass_field, 'RANDOM');
				$this->ci->db->limit(1);
				$query = $this->ci->db->get('tabel2');
				//$idmax = $query->result();
				$idmax ='';
				foreach ($query->result() as $row)
				{
		    		$idmax = $row->indeks_pass;
				}
				
				//ambil indeks
				$indeks = $this->ci->db
				->where($this->indekspass_field, $idmax)
				->get($this->password_table)
				->row($this->indekspass_field);

				if($indeks == null){
					$k = $k;
				} else {
					$gabung[$k] = $indeks;
					$k++;
				}
				
			} while ( count($gabung)<20);

		
		$random_benar = random_int(0, 2147483647);

		do {
			//pastikan random benar belum exist di honeychceker
			while(! $this->ci->model_pertama->pastikan($random_benar))
				{
					$random_benar = random_int(0, 2147483647);
				}
			//pastikan random benar tidak sama dengan indeks 20 yg sudah didapat
			do {
				 $l++;
				if($random_benar == $gabung[$l]) { $random_benar = random_int(0, 2147483647); }
			} while ($l < 20);

		} while (! $this->ci->model_pertama->pastikan($random_benar));

		//masukkan indeks benar ke barisan indeks
		$r = random_int(0, 19);
		$gabung[$r] = $random_benar;

		for($i=0;$i<20;$i++){ //jadikan barisan 20 indeks
				$rand = $gabung[$i].$koma.$rand;
			}

		$datatabel1 = array(
			$this->username_field => $username,
			$this->indekshoney_field => $rand
		);
		
		// If inserting USERNAME dan 20 NILAI ACAK ke tabel1
		if ( ! $this->ci->db->insert($this->username_table, $datatabel1))
		{
			// Return false
			return FALSE;
		}

		//inserting data ke honeychecker
		$this->ci->model_pertama->daftar_username($username, $random_benar);

		//pass.salt + salt PAK FAIZAL
		$passbetul = $hashpass.$salt; //16 digit salt
		// Define data to insert tabel2
		$datatabel2 = array(
			$this->password_field => $passbetul,
			$this->indekspass_field => $random_benar
			//$this->laut => $salt
		);

		// If inserting RANDOM BENAR ke tabel2
		if ( ! $this->ci->db->insert($this->password_table, $datatabel2))
		{
			// Return false
			return FALSE;
		}


		if($this->ci->config->item('need_aktivasi') == true)
		{
			//BANGKITKAN TOKEN AKTIVASI email
			$token_qstring = $this->Token($email, 20);

			//data variabel utk di email (username dan token_qstring)
			$data = array(
						'username' => $username,
						'token' => $token_qstring
					);

			//load pesan utk email
			$message = $this->ci->load->view('/email/registrasi', $data, TRUE);

			$this->ci->email->set_newline("\r\n");  
			   $this->ci->email->from('Honeywords@genesis.com', 'Admin');   
			   $this->ci->email->to($email);   
			   $this->ci->email->subject('Registrasi Honeywords');


			$this->ci->email->message($message);
	            //echo $message; //send this through mail
	             //exit;

			    if (!$this->ci->email->send()) {  
			    show_error($this->ci->email->print_debugger());   
			   }else{  
			    //echo 'Success to send email';
			    $this->ci->session->set_flashdata('err_message', 'Akun telah dibuat. Silakan cek email untuk aktivasi.');
			    return TRUE;
			   }
		} else {
			//tanpa perlu aktivasi
			$this->ci->model_pertama->aktifakunotomatis($username);
			$this->ci->session->set_flashdata('err_message', 'Akun telah berhasil dibuat !!');
		}
		
		//Return user ID
		return (int) $this->ci->db->insert_id();

	}

	/*
	* FOR THIS YOU can use $url = $this->uri->segment(x) to parse variable in aktivasi_registrasi($url)
	*
	*/
	public function aktivasi_registrasi($url)
	{
		$token = $this->base64url_decode($url);
		$cleanToken = $this->ci->security->xss_clean($token);
		if($this->ci->model_pertama->aktifkanakun($cleanToken) == TRUE)
		{
			$this->ci->session->set_flashdata('err_message', 'Selamat aktivasi berhasil!');
			return TRUE; //berhasil aktivasi registrasi
		} else {
			$this->ci->session->set_flashdata('err_message', 'Anda tidak bisa mengakses laman ini!');
			return FALSE; //gagal aktivasi registrasi
		}
	}


	/*
	* FOR THIS YOU can use $url = $this->uri->segment(x) to parse variable in aktivasi_cracking($url)
	*
	*/
	public function aktivasi_cracking($url, $username, $password)
	{
		$token = $this->base64url_decode($url);
		$cleanToken = $this->ci->security->xss_clean($token);
		if($this->ci->model_pertama->cek_crack($cleanToken, $username) == TRUE)
		{
			//jika belum konfirmasi
			$this->updatePassword($username,$password);

			//isi konfirmasi dan aktifkan user
			$this->ci->model_pertama->isi_crack($username);
			$this->ci->session->set_flashdata('err_message', 'Selamat aktivasi berhasil!');
			return true;
		} else { 
			$this->ci->session->set_flashdata('err_message', 'Anda tidak bisa mengakses laman ini!');
			return false;
		}
	}


	/*
	* FOR THIS YOU can use $url = $this->uri->segment(x) to parse variable in aktivasi_brute($url)
	*
	*/
	public function aktivasi_brute($url)
	{
		$token = $this->base64url_decode($url);
		$cleanToken = $this->ci->security->xss_clean($token);
    	if($this->ci->model_pertama->cocokkantoken($cleanToken) == TRUE)
		{
			$this->ci->session->set_flashdata('err_message', 'Selamat aktivasi berhasil!');
			return true;
		} else {
			$this->ci->session->set_flashdata('err_message', 'Anda tidak bisa mengakses laman ini!');
			return false;
		}
	}


	/*
	*
	*
	*/
	public function ganti_password($lama_pass, $baru_pass, $username)
	{
		if ($this->ci->session->has_userdata('username'))
		{
			if($this->cekpasslama($lama_pass, $username) == TRUE){

				//cek apakah pass common atau tidak
				if ( ! $this->cek_password($baru_pass, $username))
				{
					// pass tidak bisa digunakan
					$this->ci->session->set_flashdata('err_message', 'Password is weak!');
					return FALSE;
				} else {

					if(!$this->updatePassword($username, $baru_pass))
						{  
		           			$this->session->set_flashdata('err_message', 'Update password gagal.');
		           			return false;

		         		}else{  
		           			$this->ci->session->set_flashdata('err_message', 'Password anda sudah diperbaharui. Silakan login.');  
		           			return true;
						}   						
				}
			}
			else {
				$this->ci->session->set_flashdata('err_message', 'Password lama tidak benar!');
				return false;
			}
		} else {

			return false; //harus login dulu
		}
	}


	/*
	*
	*
	*/
	public function hapus_salah_login($login){

		$this->ci->model_pertama->hapus_salah_login($login);
	}


	/*
	*
	*
	*/
	//Hitung JIKA TERJADI GAGAL LOGIN -> CEGAH BRUTE FORCE
	public function maks_login($login)
	{
		if($this->ci->model_pertama->salah_login($login) == TRUE)
		{
			$this->ci->model_pertama->tungsalah_login($login);
			return true; // return truenya double
		}
		else {
			//hapus salah login dulu
			//$this->ci->model_pertama->hapus_salah_login($login);
			//echo "AKUN ANDA DIBANNED KIRIM EMAIL";
			// BACA LAGI PERBEDAAN BRTE FORCE N DOS dan akibatnya --> kalo brute force 7 kali tapi yg ke 6 betul ia akan reset, tapi kirim email terus preventnya gimana?

			//catat logging
			if($this->ci->config->item("catat_logging") == true)
			{
				$jenis_log = "Brute-Force attack";
				$this->ci->model_pertama->catat_log($login, $jenis_log);
			}

			$email = $this->ci->model_pertama->get_email_username($login);	
			$tanggal = date("Y-m-d H:i:s");
			$token = $this->Token($email, 35);
			//data variabel utk di email (username dan token_qstring)
				$data = array(
					'username' => $login,
					'waktu' => $tanggal,
					'token' => $token
				);
				//load pesan utk email
				$message = $this->ci->load->view('/email/bruteforce', $data, TRUE);
				$this->ci->email->set_newline("\r\n");  
				$this->ci->email->from('Honeywords@genesis.com', 'Admin');   
				$this->ci->email->to($email);   
				$this->ci->email->subject('Deteksi Adanya Perlakuan Brute-Force');
				$this->ci->email->message($message);

				$this->ci->unit->run('ok', 'ok', 'deteksi brute force');
            //echo $message; //send this through mail
             //exit;

		    if (!$this->ci->email->send()) {  
		    show_error($this->ci->email->print_debugger());   
		  	} else { return TRUE;}
		}
			
	}


	/**
	 * Logout
	 *
	 */
	public function logout()
	{
		$this->ci->session->sess_destroy();

		// ambil cookie
		$cookie = get_cookie($this->ci->config->item('nama_cookies'));
		if($cookie != null)
		{
			//hapus
			$this->ci->model_pertama->hapus_cookies($cookie);
		}
		return TRUE;
		
	}



	/**
	 * Delete user account
	 *
	 * @access	public
	 * @param	string [$user_identifier] The identifier of the user to delete
	 * @return	boolean Either TRUE or FALSE depending upon successful login
	 */
	public function delete_user($user_identifier)
	{

		// Update the users password
		if ($this->ci->db->where($this->identifier_field, $user_identifier)->delete($this->user_table))
		{
			return TRUE;
		// There was an error deleting the user
		} else {
			return FALSE;
		}

	}

	
	/**
	 * Bangkitkan token
	 * @access	public
	 * @param string [$email] email pengguna yg didaftarkan
	 * @param string [$panjang] panjang dari token yg ingin dibangkitkan
	 * @return $encode -> nilai token dalam bentuk enkoding dalam base64 url
	 */
	//BUAT TOKEN utk REG / LUPA PASSWORD
	public function Token($email, $panjang)  
    {
    	$token = substr(sha1(rand()), 0, $panjang);   
     	$tanggal = date('Y-m-d');

     	//simpan ke db
    	$this->ci->model_pertama->buatToken($email, $token, $tanggal, $panjang);

    	$encode = $this->base64url_encode($token);

    	return $encode;
    }


    /**
	 * Cek Token Valid nggak (UNTUK FITUR LUPA PASSWORD) -> token hanya berlaku di tanggal yg sama
	 * @access	public
	 * @param	string [$token] token yg akan dicek
	 * @return boolean, true -> valid , false -> token tidak valid / tidak sesuai
	 */
    public function TokenValid($token)  
   {      
     //dapatkan waktu token dibuat
     $date = $this->ci->model_pertama->get_date_token($token);             
     $date = strtotime($date);      
     $today = date('Y-m-d');   
     $todayTS = strtotime($today);

     // valid atau tidak
       if($date != $todayTS){  
         return false; //salah 
       }  else { return true; //benar
       }
       
   }


   /**
	 * Update password baru -> untuk fitur lupa dan ganti password
	 * @access	public
	 * @param	string [$token] token yg digunakan untuk melakukan fitur lupa / ganti password
	 * @param	string [$pass] password baru yang akan disimpan
	 * @return boolean, true -> valid , false -> token tidak valid / tidak sesuai
	 */
    public function updatePassword($token, $pass)  
   {    
   		// salt untuk pass
		$salt = $this->salt();
		//hash passwordnya
		$hashpass = SHA3_256::hash($pass.$salt);
		
		//BANGKIT 20 NILAI RANDOM LAINNYA
		$koma = ","; $rand = "";
		$k = 0; $gabung= null; $rand = null;

			do{
				//ambil indeks pass secara acak
				$this->ci->db->order_by($this->indekspass_field, 'RANDOM');
				$this->ci->db->limit(1);
				$query = $this->ci->db->get('tabel2');
				//$idmax = $query->result();
				$idmax ='';
				foreach ($query->result() as $row)
				{
		    		$idmax = $row->indeks_pass;
				}
				
				//ambil indeks
				$indeks = $this->ci->db
				->where($this->indekspass_field, $idmax)
				->get($this->password_table)
				->row($this->indekspass_field);

				if($indeks == null){
					$k = $k;
				} else {
					$gabung[$k] = $indeks;
					$k++;
				}
				
			} while ( count($gabung)<20);

		
		$random_benar = random_int(0, 2147483647);

		//pastikan random benar belum exist di honeychceker
		do {
			while(! $this->ci->model_pertama->pastikan($random_benar))
				{
					$random_benar = random_int(0, 2147483647);
				}
		} while (! $this->ci->model_pertama->pastikan($random_benar));

		//masukkan indeks benar ke barisan indeks
		$r = random_int(0, 19);
		$gabung[$r] = $random_benar;

		for($i=0;$i<20;$i++){ //jadikan barisan 20 indeks
				$rand = $gabung[$i].$koma.$rand;
			}

		$username = '';
		$if = strlen($token);

		//jika length = 30 ia token, jika tidak ia username
		if($if == 30){
			$username = $this->ci->model_pertama->get_name_user($token);
		} else { $username = $token; } 													//fikirkan jika orang iseng
		
		$datatabel1 = array(
			$this->username_field => $username,
			$this->indekshoney_field => $rand
		);

		//update tabel1 username dan indeks
		$this->ci->db->set($datatabel1);
		$this->ci->db->where($this->username_field, $username);
		$this->ci->db->update($this->username_table, $datatabel1);

		//hapus username dgn token lawas di honeychecker
			$this->ci->model_pertama->delete_resetpass($username);	
		
		//inserting data ke honeychecker
		$this->ci->model_pertama->daftar_username($username, $random_benar);

		$passbetul = $hashpass.$salt; //16 digit salt
		// Define data to insert tabel2
		$datatabel2 = array(
			$this->password_field => $passbetul,
			$this->indekspass_field => $random_benar
			//$this->laut => $salt
		);

		// If inserting RANDOM BENAR ke tabel2
		if ( ! $this->ci->db->insert($this->password_table, $datatabel2))
		{
			// Return false
			return FALSE;
		}
		return true;

   }  

   /*
   *
   *
   */
   public function lupa_pass($email)
   {
   		//cek email
		if($this->ci->honeywords->cek_email($email) == TRUE)
        {
            if($this->ci->honeywords->lupa_pass_email($email) == TRUE)
            {
				$this->ci->session->set_flashdata('err_message', 'Email is send!');
            } else { 
            	$this->ci->session->set_flashdata('err_message', 'Email is send!');
            }

        } else {
			//echo "email salah";
			return false;
        }

   }


   /*
	* FOR THIS YOU can use $url = $this->uri->segment(x) to parse variable in verifikasi_lupa_pass($url)
	*
	*/
	public function verifikasi_lupa_pass($url, $password, $username)
	{
		$token = $this->base64url_decode($url);
		$cleanToken = $this->ci->security->xss_clean($token);
		$user_info = $this->TokenValid($cleanToken);

		if(!$user_info){
		$this->ci->session->set_flashdata('err_message', 'Token tidak valid atau kadaluarsa');
		return false;  
		}

		//cek uasername
		if($this->cek_username($username) == true)
		{
			return false;
		}

		//cek apakah pass common atau tidak
		if ( ! $this->cek_password($password, $username))
		{
			return FALSE;
		} else {
                          
			if(!$this->updatePassword($cleanToken,  $password))
			{
			        return false;  
			}else{  
			        $this->ci->session->set_flashdata('err_message', 'Password anda sudah diperbaharui. Silakan login.');  
			}			   
		}

	}


   //forget PASSWORD build token dan kirim email
   public function lupa_pass_email($email)
   {
   		$qstring = $this->Token($email, 30);

   		//TRY KIRIM EMAIL
   		$data = array(
					'username' => $this->ci->model_pertama->get_name_user($qstring),
					'token' => $qstring
				);
 		//load pesan utk email
		$message = $this->ci->load->view('/email/forget_pass', $data, TRUE);

		$this->ci->email->set_newline("\r\n");  
		$this->ci->email->from('Honeywords@genesis.com', 'Admin');   
		$this->ci->email->to($email);   
		$this->ci->email->subject('Permintaan Lupa Password');

			//ISI PESAN
             $this->ci->email->message($message);
            //echo $message; //send this through mail
             //exit;

		    if (!$this->ci->email->send()) {  
		    show_error($this->ci->email->print_debugger());   
		   }else{  
		    //echo 'Success to send email';
		    return true;  
		   }

   }
   //UNTUK TOKEN -> URL
   public function base64url_encode($data) {   
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');   
   }   
   
   public function base64url_decode($data) {   
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));   
   }


   /**
	 * Generates a random salt value.
	 *
	 * Salt generation code taken from https://github.com/ircmaxell/password_compat/blob/master/lib/password.php
	 *
	 * @return bool|string
	 * @author Anthony Ferrera
	 */
	public function salt()
	{
		$raw_salt_len = 16;

		$buffer = '';
		$buffer_valid = FALSE;

		if (function_exists('random_bytes'))
		{
			$buffer = random_bytes($raw_salt_len);
			if ($buffer)
			{
				$buffer_valid = TRUE;
			}
		}

		if (!$buffer_valid && function_exists('mcrypt_create_iv') && !defined('PHALANGER'))
		{
			$buffer = mcrypt_create_iv($raw_salt_len, MCRYPT_DEV_URANDOM);
			if ($buffer)
			{
				$buffer_valid = TRUE;
			}
		}

		if (!$buffer_valid && function_exists('openssl_random_pseudo_bytes'))
		{
			$buffer = openssl_random_pseudo_bytes($raw_salt_len);
			if ($buffer)
			{
				$buffer_valid = TRUE;
			}
		}

		if (!$buffer_valid && @is_readable('/dev/urandom'))
		{
			$f = fopen('/dev/urandom', 'r');
			$read = strlen($buffer);
			while ($read < $raw_salt_len)
			{
				$buffer .= fread($f, $raw_salt_len - $read);
				$read = strlen($buffer);
			}
			fclose($f);
			if ($read >= $raw_salt_len)
			{
				$buffer_valid = TRUE;
			}
		}

		if (!$buffer_valid || strlen($buffer) < $raw_salt_len)
		{
			$bl = strlen($buffer);
			for ($i = 0; $i < $raw_salt_len; $i++)
			{
				if ($i < $bl)
				{
					$buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
				}
				else
				{
					$buffer .= chr(mt_rand(0, 255));
				}
			}
		}

		$salt = $buffer;

		// encode string with the Base64 variant used by crypt
		$base64_digits = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
		$bcrypt64_digits = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$base64_string = base64_encode($salt);
		$salt = strtr(rtrim($base64_string, '='), $base64_digits, $bcrypt64_digits);

		$salt = substr($salt, 0, 8); //berapa ya panjang
		$salt = bin2hex($salt); //biar sama format pass nya
		return $salt;
	}

}

/* End of file */
/* Lokasi: ./application/libraries/Honeywords.php */