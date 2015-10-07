<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site extends CI_Controller{
	function __construct(){
		parent::__construct();		
		setlocale (LC_TIME, 'INDONESIA');
		$this->auth = unserialize(base64_decode($this->session->userdata('calon_mah')));
		$this->host	= $this->config->item('base_url');
		
		$this->db2 = $this->load->database('second', TRUE); 
		
		$this->smarty->assign('host',$this->host);
		$host = $this->host;
		
		//$this->session->unset_userdata("status");
	}
	
	function index() {
			if(!$this->auth){
			$status ="";
			$status = $this->session->userdata("status");
				
			$this->smarty->assign("status",$status);
			$this->smarty->display('login_soft.html');
			
			}else{
				header("Location: " .  $this->host."portal");
			}
		}
		
	function login(){
		$this->load->library('encrypt');
		$username = $this->input->post("username");
		$pass = $this->input->post("password");
		//$pass = $this->encrypt->encode($pass);
		if (!$username OR !$pass){echo "<script>alert('Username dan Password tidak boleh kosong!');history.go(-1);</script>";}
		
		$sql = "SELECT * FROM adis_smb_usr WHERE kode = '$username'";
				
		$rs  = $this->db2->query($sql)->row();
		
		if ($rs){
			$row['password'] = $rs->password;
			$passdb = $this->encrypt->decode($row['password']);
			
			if($passdb != $pass){
				echo "<script>alert('Password Yang Anda Masukkan Salah!');history.go(-1);</script>";
				}
			else if($rs->validation_status == 0){
				$status ="notaktif";
				$this->session->set_userdata('status', $status);
				redirect ('/site', 'refresh');
			}else{
				$row['kode'] 		=  $rs->kode;
                $row['username'] 	=  $rs->username;
				$row['validation_status'] 		=  $rs->validation_status;
                $this->session->set_userdata('calon_mah', base64_encode(serialize($row)));
				$this->session->unset_userdata("status");
				header("Location: " . $this->host."portal");
				
				//redirect('/dashboard','refresh');
			}
        }else {
			echo "<script>alert('Password atau Username Yang Anda Masukkan Salah!');history.go(-1);</script>";
		}
		
	}
	
	function logout(){
        $this->session->unset_userdata("calon_mah");
        header("Location: " . $this->host."site");
    }

}
?>