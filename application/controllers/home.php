<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller{
	function __construct(){
		parent::__construct();	
		date_default_timezone_set("Asia/Jakarta");
		$this->db2 = $this->load->database('second', TRUE);
		$this->auth = unserialize(base64_decode($this->session->userdata('cuti_parmad')));
		$this->host	= $this->config->item('base_url');
		$this->smarty->assign('host',$this->host);
		$host = $this->host;
	}
	
	function index() {
			if(!$this->auth){
				$this->smarty->display('login.html');
			}else{
				header("Location: " .  $this->host."dashboard");
			}
		}
		
	function login(){
		$this->load->library('encrypt');
		$username = $this->input->post("username");
		$pass = $this->input->post("password");
		//$pass = $this->encrypt->encode($pass);
		if (!$username OR !$pass){echo "Salah";}
		
		// $sql = "SELECT * FROM tbl_usrm_users LEFT JOIN tbl_usrm_level ON tbl_usrm_users.tbl_usrm_level_id = tbl_usrm_level.id
				// WHERE tbl_usrm_users.username = '".$username."' limit 1";
			
		$sql = "SELECT * FROM adis_sys_usr u LEFT JOIN tbl_usrm_level l ON l.id = u.id_level WHERE u.kode = '$username';";
		$rs  = $this->db2->query($sql)->row();
		
		if ($rs){
			$row['password'] = $rs->password;
			$passdb = $this->encrypt->decode($row['password']);
			
			if($passdb != $pass){
				echo "<script>alert('Password Yang Anda Masukkan Salah!');history.go(-1);</script>";
				}
			else if($rs->status == 0){
				echo "<script>alert('User Tidak Aktif!');history.go(-1);</script>";
			}else{
				// $row['id'] 			=  $rs->id;
                // $row['username'] 	=  $rs->username;
				// $row['status'] 		=  $rs->aktif;
				// $row['name'] 		=  $rs->name;
                // $row['tbl_usrm_level_id'] 	=  $rs->tbl_usrm_level_id;
				// $row['id_pegawai'] 		=  $rs->id_pegawai;
				
				$row['id'] 			=  $rs->kode;
                $row['name'] 	=  $rs->username;
				$row['status'] 		=  $rs->status;
                $row['level'] 	=  $rs->id_level;
				$row['email'] 		=  $rs->email;
				
                $this->session->set_userdata('cuti_parmad', base64_encode(serialize($row)));
				header("Location: " . $this->host."dashboard");
				
				//redirect('/dashboard','refresh');
			}
        }else {
			echo "<script>alert('Password atau Username Yang Anda Masukkan Salah!');history.go(-1);</script>";
		}
		
	}
	
	function logout(){
        $this->session->unset_userdata("cuti_parmad");
        header("Location: " . $this->host."home");
    }

}
?>