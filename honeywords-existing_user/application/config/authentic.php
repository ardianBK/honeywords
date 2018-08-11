<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * User table
 *
 * This table should contain the username and password fields specified below. It can contain any other fields, such as "first_name"
 */
//$config['authentication']['user_table'] = 'pengguna';
//$config['dua']['user_table'] = 'users';

/**
 * User identifier field
 *
 * This field will usually be "id" or "user_id" but you could use something like "username"
 */
//$config['authentication']['identifier_field'] = 'id';


/**
 * Username field
 *
 * This field can be named what ever you like, an example would be "email"
 */
//$config['authentication']['username_field'] = 'email';


/**
 * Password field
 */
//$config['authentication']['password_field'] = 'password';


/* End of file authentication.php */
/* Location: ./application/config/authentication.php */

//tabel -> | username | | honeyindeks |
$config['satu']['username_table'] = 'tabel1';
$config['satu']['identifier1_field'] = 'no1';
$config['satu']['username_field'] = 'username';
$config['satu']['indekshoney_field'] = 'honeyindeks';

//tabel -> | password || indeks_password |
$config['dua']['password_table'] = 'tabel2';
$config['dua']['password_field'] = 'password';
$config['dua']['indekspass_field'] = 'indeks_pass';

//tabel -> data user
$config['tiga']['data_table'] = 'data_user';
$config['tiga']['id3_field'] = 'no';
$config['tiga']['data_username_filed'] = 'username';
$config['tiga']['aktifgak'] = 'aktif';
$config['tiga']['data_email'] = 'email';
$config['tiga']['data_IP'] = 'IP_terakhir'; 
$config['tiga']['data_nama'] = 'nama_lengkap'; 
$config['tiga']['data_tokenr'] = 'token_reg';
$config['tiga']['data_status'] = 'sudah_aktivasi';
$config['tiga']['data_token'] = 'token_forgot';
$config['tiga']['data_tgl_token'] = 'date_forgot';
$config['tiga']['data_tokenc'] = 'token_crack';
$config['tiga']['data_wktu_crack'] = 'date_cracking'; 
$config['tiga']['data_konf_crack'] = 'konfirmasi';
$config['tiga']['data_konf_tgl'] = 'tgl_konfirmasi'; 
$config['tiga']['data_tokenbr'] = 'token_brute';


$config['username_min_length'] = 2;
$config['username_max_length'] = 20;
$config['min_password_length'] = 16; //kalau admin gak mau pake ---- ini jadi wajib
$config['password_max_length'] = 24;

//logging laporan brute dan cracking deteksi
$config['catat_logging'] = true; //true utk mencatat di file log

//aktivasi register
$config['need_aktivasi'] = true; //true = iya, false = tidak
//email
$config['email_templates'] = 'email/'; //folder template email
$config['email_registrasi'] = 'email/registrasi.php'; 
$config['use_email'] = TRUE; // Send Email using the builtin CI email class, if false it will return the code and the identity
$config['email_config'] = array(
						    'protocol' => 'smtp',  
						    'smtp_host' => 'mail.smtp2go.com',  
						    'smtp_port' => 465,  
						    'smtp_user' => 'matkolamcing@gmail.com',   
						    'smtp_pass' => 'CwDcbTChd1dj',   
						    'mailtype' => 'html',   
						    'charset' => 'iso-8859-1'  
						   ); 

//inisialisasi
$config['banyak_password_pool'] = 100; //standar min = 100
$config['nama_kamus'] = 'Kamus_Bahasa'; //masukkan nama .txt dari nama KAMUS BAHASA utk PASSWORD
$config['simpan_password_pool'] = true; //simpan password ke txt file = true, tidak simpan =false
$config['nama_file_fakeusername'] = 'fakename'; //masukkan nama .txt dari nama file berisi USERNAME PALSU
$config['nama_common_pass'] = 'password_list'; //masukkan nama .txt dari nama file COMMON PASSWORD

//cookies
$config['cookies_expire'] = 900;
$config['nama_cookies'] = 'cookies_remember_honeywords';

