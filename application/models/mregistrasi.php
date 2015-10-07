<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Mregistrasi extends CUTI_Model{
	
	function __construct(){
		parent::__construct();
	}
	
	function Mregistrasi(){
		parent::__construct();
	}
	
	
	function mSelectPeriode(){
		$periode ="SELECT * FROM adis_periode WHERE status = 1 AND erased = 0";
		$periode = $this->db2->query($periode)->result();			
			
		$this->smarty->assign('periode',$periode);	
	
	}
	
	function mSelectGender(){		
		$gender = $this->db2->query("SELECT * FROM adis_type WHERE kode LIKE '03.%' AND erased = 0")->result();
		$this->smarty->assign("gender", $gender);
	}
	
	function mSelectProdi(){
		$prodi ="SELECT * FROM adis_prodi WHERE erased = 0";
		$prodi = $this->db2->query($prodi)->result();			
			
		$this->smarty->assign('prodi',$prodi);	
	
	}
	
	function mSelectJalur($prodi){
		$jalur ="SELECT j.nama as namajalur, p.kode , b.jalur, j.kode as kodejalur FROM adis_buka_smb b
				INNER JOIN adis_prodi p ON b.prodi = p.kode
				INNER JOIN adis_jalur_smb j ON b.jalur = j.kode
				WHERE b.prodi = '$prodi' AND stsBuka = 1 AND b.erased = 0";
		$jalur = $this->db2->query($jalur)->result();			
			
		$this->smarty->assign('jalur',$jalur);	
	
	}
	
	function mSelectRuang(){
		$ruang ="SELECT * FROM adis_ruang WHERE erased = 0";
		$ruang = $this->db2->query($ruang)->result();			
			
		$this->smarty->assign('ruang',$ruang);	
	
	}
	
	function mSelectPetugas(){
		$person ="SELECT * FROM adis_personal WHERE erased = 0";
		$person = $this->db2->query($person)->result();			
			
		$this->smarty->assign('petugas',$person);	
	
	}
	
	function mAddBuka(){
		$periode = $this->input->post("periode");
		$jalur = $this->input->post("jalur");
		$prodi = $this->input->post("prodi");
		
		$nama = array($periode,$jalur,$prodi);
		$nama = implode (" - ",$nama);
		
		$this->db2->insert("adis_buka_smb", array(
				"kode"=>implode (".",array($periode,$jalur,$prodi)),
				"nama"=>$nama,
				"periode"=>$periode,
				"jalur"=>$jalur,
				"prodi"=>$prodi,
				"stsBuka"=>$this->input->post("status"),
				"tanggalBuka"=>$this->input->post("from"),
				"tanggalTutup"=>$this->input->post("to")
			));
	}
	
	function mEditBuka($kode){
		$periode = $this->input->post("periode");
		$jalur = $this->input->post("jalur");
		$prodi = $this->input->post("prodi");
				
		$nama = array($periode,$jalur,$prodi);
		$nama = implode (" - ",$nama);
		$this->db2->where("kode", $kode);
		$this->db2->update("adis_buka_smb", array(
				"nama"=>$nama,
				"periode"=>$periode,
				"jalur"=>$jalur,
				"prodi"=>$prodi,
				"stsBuka"=>$this->input->post("status"),
				"tanggalBuka"=>$this->input->post("from"),
				"tanggalTutup"=>$this->input->post("to")
			));
	}
	
	function mAddCalonMahasiswa($validation_key){
		
		$this->load->library("encrypt");
	
		$periode = $this->session->userdata('periode');
		$prodi = $this->input->post("prodi");
		$jalur = $this->input->post("jalur");
		$email = $this->input->post("email");				 
		$pass = $this->encrypt->encode($this->input->post("password"));
		$bukaSmb = implode (".", array($periode, $jalur, $prodi));
		$nomor = uniqid();
		$datetime = date("Y-m-d H:i:s"); 
		$createUser = "console";
		$tanggalLahir = date("Y-m-d", strtotime($this->input->post("tanggalLahir")));
			
		$this->db2->insert("adis_smb_usr", array(
				"kode"=>$email,
				"username"=>$this->input->post("name"),
				"password"=>$pass,
				"email"=>$email,
				"validation_key"=>$validation_key,
				"validation_status"=>0,
				"createUser"=>$createUser,
				"createTime"=>$datetime
			));
		
		$this->db2->insert("adis_smb_form", array(
				"kode"=>$email,
				"bukaSmb"=>$bukaSmb,
				"nomor"=>$nomor,
				"createTime"=>$datetime,
				"createUser"=>$createUser
			));
			
		$this->db2->insert("adis_smb_usr_edu", array(
				"kode"=>$nomor,
				"smbUsr"=>$email,
				"nama"=>$this->input->post("sekolah"),
				"jurusan"=>$this->input->post("jurusan"),
				"tahunLulus"=>$this->input->post("tahunLulus"),
				"createUser"=>$createUser,
				"createTime"=>$datetime
			));
			
		$this->db2->insert("adis_smb_usr_pribadi", array(
				"kode"=>$email,
				"nama"=>$this->input->post("name"),
				"genderType"=>$this->input->post("sex"),
				"tempatLahir"=>$this->input->post("tempatLahir"),
				"tanggalLahir"=>$tanggalLahir,
				"rumahAlamat"=>$this->input->post("occupation"),
				"rumahCell"=>$this->input->post("no_hp"),
				"stsPribadi"=>0,
				"stsPribadiConfirm"=>0,
				"createUser"=>$createUser,
				"createTime"=>$datetime
			));
	}
	
	
	
}