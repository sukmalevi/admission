<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Registrasi extends CUTI_Controller{
	function __construct(){
		parent::__construct();	
		setlocale (LC_TIME, 'INDONESIA');
		//$this->auth = unserialize(base64_decode($this->session->userdata('calon_mah')));
		$this->host	= $this->config->item('base_url');
		
		$this->db2 = $this->load->database('second', TRUE); 
		
		$modul = "pendaftaran/";
		$this->smarty->assign('modul',$modul);
		$this->smarty->assign('host',$this->host);
		$this->load->model('mregistrasi');
		$this->db2->query("SET lc_time_names = 'id_ID'");
		

	}
	
	function index() {
		$this->registrasiForm();
	}
		
	function faq(){
		$title = "FAQ";
		$page = "faq";
		$this->smarty->assign('title', $title);
		$this->smarty->assign('page', $page);
		$this->smarty->display("pendaftaran/index.html");		
	}

	function registrasiForm(){
		$this->mregistrasi->mSelectGender();
		$title = "FORM REGISTRASI";
		$page = "formPendaftaran";
		$this->smarty->assign('title', $title);
		$this->smarty->assign('page', $page);
		$this->smarty->display("pendaftaran/index.html");
	}
	
	function selectProdi(){
		$jenjang = $this->input->post("jenjang");
		
		$prodi ="SELECT * FROM adis_prodi WHERE erased = 0 AND jenjang = '$jenjang' ";
		$prodi = $this->db2->query($prodi)->result();			
		$view = '<option>--Silahkan Pilih--</option>
				{foreach name=lope from=$prodi item=row}
					<option value="{$row->kode}">{$row->nama}</option>
				{/foreach}';
					
		//INISIASI PERIODE YANG SEDANG AKTIF Berdasarkan Jenjang
		$periode = $this->db2->query("SELECT p.kode as id, m.jenjangType 
		from adis_periode p
		LEFT JOIN adis_periode_master m ON m.kode = p.idPeriodeMaster
		WHERE m.jenjangType = '$jenjang' AND p.status = 1 AND p.erased = 0")->row();
		$periode = $periode->id;
		$this->session->set_userdata('periode', $periode);
		
		//$periode = $this->db2->query("SELECT * from adis_periode WHERE jenjangType = '$jenjang' AND `status` = 1 AND erased = 0")->row();
		//$periode = $periode->kode;
		//$this->session->set_userdata('periode', $periode);
		
		////////////////////////////////////////////////////END
		
		$this->smarty->assign('prodi',$prodi);
		$this->smarty->display('string:'.$view);
	}
	
	function selectJalur(){
		
		$prodi = $this->input->post("prodi");
		$periode = $this->session->userdata('periode');
		
		$jalur ="SELECT j.nama as namajalur, p.kode , b.jalur, j.kode as kodejalur FROM adis_buka_smb b
				INNER JOIN adis_prodi p ON b.prodi = p.kode
				INNER JOIN adis_jalur_smb j ON b.jalur = j.kode
				WHERE b.periode = '$periode' AND b.prodi = '$prodi' AND stsBuka = 1 AND b.erased = 0";
		$jalur = $this->db2->query($jalur)->result();	
		
		$view = '{foreach name=lope from=$jalur item=row}
						<option value="{$row->kodejalur}">{$row->namajalur}</option>
				 {/foreach}';
		$this->smarty->assign('jalur',$jalur);
		$this->smarty->display('string:'.$view);
	}
	
	function formRegistrasi(){
		
		$prodi = $this->input->post("prodi");
		$jalur = $this->input->post("jalur");
		$names = $this->input->post("name");
		$sex = $this->input->post("sex");
		$ttl = $this->input->post("tempatLahir");
		$ocup = $this->input->post("occupation");
		$hp = $this->input->post("no_hp");
		$tlL = $this->input->post("tanggalLahir");
		
		require_once(APPPATH.'libraries/recaptchalib.php');
		$privatekey = "6Lcj_eoSAAAAADoqax2wXXYiskTaRz5YbWbSbCqF";
		$resp = recaptcha_check_answer ($privatekey,
										$_SERVER["REMOTE_ADDR"],
										$_POST["recaptcha_challenge_field"],
										$_POST["recaptcha_response_field"]);

		  if (!$prodi || !$jalur || !$names || !$sex || !$ttl || !$ocup || !$hp || !$tlL){
			echo '<script>alert("Kolom Tidak Boleh Kosong!");history.go(-1);</script>';
		  }
		  if (!$resp->is_valid) {
			// What happens when the CAPTCHA was entered incorrectly
			echo '<script>alert("The reCAPTCHA was not entered correctly. Go back and try it again.");history.go(-1);</script>';
			// die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
				 // "(reCAPTCHA said: " . $resp->error . ")");
		  } else {
			// Your code here to handle a successful verification	
			$email = $this->input->post("email");
			$count = $this->db2->query("SELECT * FROM adis_smb_usr WHERE kode = '$email'")->num_rows();
			
			if ($count > 0){
				echo '<script>alert("Email Sudah Terdaftar, Gunakan akun email yang lain.");history.go(-1);</script>';
			}else{
				$validation_key = md5(uniqid(rand()));
				$this->mregistrasi->mAddCalonMahasiswa($validation_key);
				
				// Validasi email terlebih dahulu.
				// Email configuration
					$config = Array(
						  'protocol' => 'smtp',
						  'smtp_host' => 'mail.paramadina.ac.id',
						  'smtp_port' => 25,
						  // 'smtp_user' => 'admin@students.paramadina.ac.id', // change it to yours
						  // 'smtp_pass' => 'v3mb424x1971', // change it to yours						  
						  'smtp_user' => 'update@paramadina.ac.id', // change it to yours
						  'smtp_pass' => 'updatesoftware', // change it to yours
						  'mailtype' => 'html',
						  'charset' => 'iso-8859-1',
						  'wordwrap' => TRUE
					);	
				
					$this->load->library('email', $config);
					$this->email->from('admin@admisission.paramadina.ac.id', "Humas Paramadina");
					$this->email->to($email);
					//$this->email->cc("rahmad.syalevi@paramadina.ac.id");
					$this->email->subject("Konfirmasi Pendaftaran Mahasiswa Baru Online Paramadina");
					$this->email->message("Klik tautan berikut untuk melakukan validasi Pendaftaran Online Masuk Universitas Paramadina: http://admission.paramadina.ac.id/registrasi/validasi/".$validation_key);
						
					$data['message'] = "Sorry Unable to send email...";	
					if($this->email->send()){					
						$data['message'] = "Mail sent...";			
					}
				//Memanggil fungsi notification
				$idActivity = "13.1";
				$this->activity($email, $idActivity, $email);
				
				$status = "notvalid";
				$this->session->set_userdata('status', $status);
				
				redirect ('/site', 'refresh');
			}
		  }

		
	}
	
	function validasi(){
		$validation_key = $this->uri->segment(3);
		$usr = $this->db2->query("SELECT * FROM adis_smb_usr WHERE validation_Key = '$validation_key'")->row();
		
		if ($validation_key != "" AND $usr->validation_status == 0){
		
			$this->db2->where("validation_key",$validation_key);
			$this->db2->update("adis_smb_usr", array("validation_status"=>1));
			
			$email = $usr->kode;
			$idActivity = "13.2";
			$this->activity($email, $idActivity, $email);
			
			$status = "valid";
			$this->session->set_userdata('status', $status);
			
			
			redirect ('/site', 'refresh');
			
		}else{
			header("Location: " .  $this->host."site");
		}
	}
	
	function activity($email, $idActivity, $user){
		$kode = uniqid();
		$date = date("Y-m-d H:i:s");
		$activity = $this->db2->query("SELECT nama, kode FROM adis_type WHERE kode = '$idActivity'")->row();
		$this->db2->insert('adis_activity', array('kode'=>$kode,
								'id_activity'=>$activity->kode,
								'created_date'=>$date,
								'created_user'=>$user,
								'nama_activity'=>$activity->nama,
								'status_activity'=>0,
								'id_cmb'=>$email
								));
	}
}
