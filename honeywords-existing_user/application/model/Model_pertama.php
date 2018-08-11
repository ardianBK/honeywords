<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 *
 * @package	honeywords
 * @author	Ardian Budi Kusuma
 */
class Model_pertama extends CI_Model{

	private $lain;
	function __construct()
	{
		parent::__construct();

		$this->lain = $this->load->database('lainnya', TRUE);

		// Load config
		$this->config->load('authentic');
				$satu_config = $this->config->item('satu');
				// ini Set config items-nya
				$this->username_table = $satu_config['username_table'];
				$this->id1_field = $satu_config['identifier1_field'];
				$this->username_field = $satu_config['username_field'];
				$this->indekshoney_field = $satu_config['indekshoney_field'];

				$dua_config = $this->config->item('dua');
				// ini Set config items-nya
				$this->password_table = $dua_config['password_table'];
				$this->password_field = $dua_config['password_field'];
				$this->indekspass_field = $dua_config['indekspass_field'];

				$tiga_config = $this->config->item('tiga');
				$this->data_table = $tiga_config['data_table'];
				$this->usernamedata = $tiga_config['data_username_filed'];
				$this->dataemail = $tiga_config['data_email'];
				$this->IP = $tiga_config['data_IP'];
				$this->datanama = $tiga_config['data_nama'];
				$this->tokenreg = $tiga_config['data_tokenr'];
				$this->statusaktv = $tiga_config['data_status'];
				$this->token = $tiga_config['data_token'];
				$this->tgl_token = $tiga_config['data_tgl_token'];
				$this->tokencr = $tiga_config['data_tokenc'];
				$this->kpn_crack = $tiga_config['data_wktu_crack'];
				$this->konf_crack = $tiga_config['data_konf_crack'];
				$this->tgl_konf = $tiga_config['data_konf_tgl'];
				$this->tokenbr = $tiga_config['data_tokenbr'];
				$this->aktifs = $tiga_config['aktifgak']; //ambil nilai 0 / 1 aktifs


	}

	/**
	 * Cek kesesuaian untuk login
	 *
	 * @param	string
	 * @return	object
	 */
	function cek_indeks_login($indeks, $username)
	{
		$cek = $this->lain->select('h_user')
		->where('indeks_benar',$indeks)
		->get('hon_check');

		if($cek->num_rows()==0){
			return FALSE;
		} else {

			foreach ($cek->result() as $row)
			{
	    		$sure = $row->h_user;
	    		if($sure == $username){
				return TRUE;
				} else { return FALSE;}
			}
		//$user_details = $cek->row();
		//$sure = $user_details->h_user;
		}

	}

	//cocokkan cookies utk proses login (dapatkan data username)
	function get_cookies($cookies)
	{
		$get = $this->db->select($this->usernamedata)
		->where('cookies', $cookies)
		->get($this->data_table);

		foreach ($get->result() as $row)
			{
	    		$username = $row->username;
	    		return $username;
	    	}
	}

	function hapus_cookies($cookies) //kosongkan cookies
	{
		$this->db->set('cookies', null);
		$this->db->where('cookies', $cookies);
		$this->db->update($this->data_table);
	}

	//simpan cookies remember me
	function simpan_cookies($cookies, $username)
	{
		$this->db->set('cookies', $cookies);
		$this->db->where($this->usernamedata, $username);
		$this->db->update($this->data_table);
	}

	/**
	 * daftar/create username
	 *
	 * @access	public
	 * @return	integer|boolean Either the user ID or FALSEupon failure
	 */
	function daftar_username($username, $indeks_benar)
	{
		
		$data = array(
			'h_user' => $username,
			'indeks_benar' => $indeks_benar
		);

		if ( ! $data = $this->lain->insert('hon_check', $data))
		{
			// Return false
			return FALSE;
		}
	
	}

	/**
	 * daftar/pastikan indeks
	 *
	 * @access	public
	 * @return	integer|boolean Either the user ID or FALSEupon failure
	 */
	function pastikan($nilai)
	{
		$cek = $this->lain->select('h_user')
		->where('indeks_benar',$nilai)
		->get('hon_check');

		if($cek->num_rows()>0){
			return FALSE; //sudah ada
		} else { return TRUE; //belum ada
				}
	}


	function simpan_data($username, $email, $namalengkap)
	{

		$data = array(
				$this->usernamedata	=> $username,
				$this->dataemail	=> $email,
				$this->IP	=> $this->input->ip_address(),
				$this->datanama	=> $namalengkap,
				$this->aktifs => 0
			);

		$this->db->insert($this->data_table, $data);
	}

	//tidak ada aktivasi
	function aktifakunotomatis($username)
	{
		$this->db->set($this->aktifs, 1);
		$this->db->where($this->usernamedata, $username);
		 $this->db->update($this->data_table);
	}


	//aktifskan setelah email -->$KEY nya COBA CEK, RAGU MD5
	function aktifkanakun($key)
	{	
		$nilai = '';
		$status = $this->db->select($this->statusaktv)
		->where($this->tokenreg, $key)
		->get($this->data_table);

		$get = $status->row();
		if($get == null){ //token gak ada di db
			return FALSE;
		} else {
		$nilai = $get->sudah_aktivasi;
		}

		if($nilai == null)
		{
			$y = 'yes';
			$this->db->set($this->aktifs, 1);
			$this->db->set($this->statusaktv,$y);
			 $this->db->where($this->tokenreg, $key);
			 $this->db->update($this->data_table);

			 return true;
		} else {
			return false; //tidak bisa aktivasi lewat link registrai
		}
	}

	function deaktifasiakun($key)
	{
			$this->db->set($this->aktifs, 0);
			$this->db->where($this->usernamedata, $key);
			$this->db->update($this->data_table);
			//return true;
	}


	//cocokkan token utk aktif dr deteksi brute
	function cocokkantoken($key)
	{
		$tok = $this->db->select('*')
		->where($this->tokenbr, $key)
		->get($this->data_table);

		$token = '';
		foreach ($tok->result() as $row)
		{
    		$token = $row->token_brute;
		}
		if($token == $key)
		{
			//token cocok aktifkan akun krn brute
			$this->db->set($this->aktifs, 1);
			 $this->db->where($this->tokenbr, $key);
			 $this->db->update($this->data_table);
			return TRUE;
		} else { return FALSE; }
	}


	//hitung salah login
	function salah_login($username)
	{

		$get = $this->db->select('id')
		->where('login',$username)
		->get('hitung_login');

		if($get->num_rows()>5){
			$this->deaktifasiakun($username);
			return FALSE; //berarti brtue force
		} else { return TRUE;}

	}

	//masukkan data salah login
	function tungsalah_login($username)
	{
		$date = date("Y-m-d H:i:s");
		$data = array(
			'IP' => $this->input->ip_address(),
			'login' => $username,
			'waktu' => $date,
		);
		$this->db->insert('hitung_login', $data);
	}


	//catat logging brute dan cracking
	function catat_log($username, $jenis)
	{
		$IP = $this->input->ip_address();
		$date = date("Y-m-d H:i:s");
		log_message('custom', "Telah terjadi kegiatan ".$jenis." pada akun = ".$username. " IP = ".$IP." waktu = ".$date);
	}

	//login benar? hapus data salah login
	function hapus_salah_login($username)
	{
		$this->db->where('login', $username);
		$this->db->delete('hitung_login');

		//tes
		$get = $this->db->select('IP')
		->where('login', $username)
		->get('hitung_login');

		foreach ($get->result() as $row)
		{
    		$date = $row->waktu;
    		if($date == null)
    		{ return true;}
    		else { return false;}
		}
	}

	//pengguna aktif atau gak
	function aktif($username)
	{
		$get = $this->db->select($this->aktifs)
		->where($this->usernamedata,$username)
		->get($this->data_table);

		$user_details = $get->row();
		$getdata = $user_details->aktif;

		if($getdata == 0)
		{
			return TRUE;
		} else { return FALSE;}
	}

	//BIKIN TOKEN GANTI PASSWORD
	function buatToken($email, $token, $tanggal, $panjang)  
    {    

		if($panjang == 30) //forget
		{
			$data = array(
				$this->token	=> $token,
				$this->tgl_token => $tanggal
			);

			$this->db->set($this->token, $token)->where($this->dataemail,$email)->update($this->data_table, $data);
		}

		if($panjang == 35) //brute
		{
			$this->db->set($this->tokenbr, $token)->where($this->dataemail,$email)->update($this->data_table);
		}

		if($panjang == 20) //regis
		{

			$this->db->set($this->tokenreg, $token)->where($this->dataemail,$email)->update($this->data_table);
		}

		if($panjang == 40) //crack
		{
			$data = array(
				$this->tokencr	=> $token,
				$this->kpn_crack => $tanggal
			);

			$this->db->set($this->tokencr, $token)->where($this->dataemail,$email)->update($this->data_table, $data);
    		
    		//hapus tgal konfirmasi jika udah pernah deteksi crack sbelumnya
    		$cek = $this->cek_crack($token, $email);
    		if($cek == false){

    		$this->db->set($this->konf_crack, null)->where($this->dataemail,$email)->update($this->data_table);	
    		$this->db->set($this->tgl_konf, null)->where($this->dataemail,$email)->update($this->data_table);
    		}
		}

    }  

    //ambil date token
    function get_date_token($token)  
    {
    	$get = $this->db->select($this->tgl_token.' as date_token')
		->where($this->token,$token)
		->get($this->data_table);

		foreach ($get->result() as $row)
		{
    		$date = $row->date_token;
    		return $date;
		}

		//$get_details = $get->row();
		//$date = $get_details->date_token;
		//return $date;
    }

    function get_name_user($token) //dari token
    {
    	$get = $this->db->select($this->usernamedata)
    	->where($this->token,$token)
    	->get($this->data_table);

    	foreach ($get->result() as $row)
		{
    		$user = $row->username;
    		return $user;
		}

    	//$get_details = $get->row();
		//$user = $get_details->username;
		//return $user;
    }

    function get_email_username($username) //from username
    {
    	$get = $this->db->select($this->dataemail.' as email')
    	->where($this->usernamedata,$username)
    	->get($this->data_table);

    	foreach ($get->result() as $row)
		{
    		$eml = $row->email;
    		return $eml;
		}


    	//$get_details = $get->row();
		//$eml = $get_details->email;
		//return $eml;
    }

    //hapus honey akun ket reset pass
    function delete_resetpass($username)
    {
    	$this->lain->where('h_user', $username)
    	->delete('hon_check');
    }

    //cek apakah email exist
    function cekemail($email)
    {
    	$get = $this->db->where($this->dataemail, $email)
    	->get($this->data_table);

    	if ($get->num_rows() > 0)
		{
			// False -> email ada
			return FALSE;
		}
		// true -> tidak ada
		return TRUE;
    }

    //ambil indeks benar dr username
    function get_idx_usr($username)
    {
    	$get = $this->lain->select('indeks_benar')
    	->where('h_user',$username)
    	->get('hon_check');

    	$get_details = $get->row();
		$indexx = $get_details->indeks_benar;

		return $indexx;
    }

    //ambil username dr index benar
    function get_usr_idx($indeks)
    {
    	$get = $this->lain->select('h_user')
    	->where('indeks_benar',$indeks)
    	->get('hon_check');

    	foreach ($get->result() as $row)
		{
    		$user = $row->h_user;
    		return $user;
		}
		
    }


    //cek konfimasi crack udah belum
    function cek_crack($token, $username)
    {
    	//ambil data berdasar token
    	$get = $this->db->select($this->konf_crack.' as konfirm, '.$this->usernamedata.' as username')
    	->where($this->tokencr,$token)
    	->get($this->data_table);

    	$sure = ''; $eml = ''; $user = '';
    	foreach ($get->result() as $row)
		{
    		$sure = $row->konfirm;
			$user = $row->username;

			if($user == $username)
			{
				if($sure == null){
					return TRUE; 
				} else {
					return FALSE; //udah pernah konfirmasi
				}
			} else {
				return FALSE; //username yg dimasukkan gak sama
			}
		}


    	//$status = $get->row();
		//$sure = $status->konfirm;
		//$eml = $status->emaill;
		
    }

    //isi info konfirmasi crack
    function isi_crack($usr)
    {
    	$a = 'yes';
    	$this->db->set($this->konf_crack, $a);
		$this->db->where($this->usernamedata, $usr);
		$this->db->update($this->data_table);

		$d = date("Y-m-d H:i:s");
		$this->db->set($this->tgl_konf, $d);
		$this->db->where($this->usernamedata, $usr);
		$this->db->update($this->data_table);

		$this->db->set($this->aktifs, 1);
		$this->db->where($this->usernamedata, $usr);
		$this->db->update($this->data_table);
    }

}