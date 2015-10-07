<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Mmaster extends CUTI_Model{
	
	function __construct(){
		parent::__construct();
		
		$this->db2 = $this->load->database('second', TRUE);
		
	}
	
	function Mmaster(){
		parent::__construct();
	}
	
	function mpegawai(){
		//$sql = "SELECT * FROM tbl_master_personal ORDER BY id";
		$sql ="SELECT id, nama, inisial, personal_NIP, alamat_skr, email, phone, mobile_phone1,
			d.acadept_uid as d_id, d.description as depart, j.acajbt_uid as j_id, j.description as jabatan 
			FROM tbl_master_personal p
			INNER JOIN tbl_master_department d ON p.id_depart = d.acadept_uid
			INNER JOIN tbl_master_jabatan j ON p.id_jabatan = j.acajbt_uid
			GROUP BY id
			ORDER BY id";
		$sql = $this->db->query($sql)->result();
		$this->smarty->assign('pegawai',$sql);
		
		return $sql;
	}
	
	function mAddUser($pass){
		$nama = $this->auth['name'];
		$date = date("Y-m-d H:i:s");
		$inisial = $this->input->post("inisial");
		$person = $this->db2->query("SELECT nama FROM adis_personal WHERE kode ='$inisial'")->row();
		$this->db2->insert("adis_sys_usr", array(
				"createUser"=>$nama,
				"createTime"=>$date,
				"kode"=>$inisial,
				"username"=>$person->nama,
				"email"=>$this->input->post("email"),
				"password"=>$pass,
				"id_level"=>$this->input->post("level")
			));
	}
	
	function mEditUser($pass){
		$nama = $this->auth['name'];
		$date = date("Y-m-d H:i:s");
		$inisial = $this->input->post("inisial");
		$person = $this->db2->query("SELECT nama FROM adis_personal WHERE kode ='$inisial'")->row();
		$this->db2->where("kode",$inisial);
		$this->db2->update("adis_sys_usr", array(
				"updateUser"=>$nama,
				"updateTime"=>$date,
				"kode"=>$inisial,
				"username"=>$person->nama,
				"email"=>$this->input->post("email"),
				"password"=>$pass,
				"id_level"=>$this->input->post("level")
			));
	}
	
	function mAddWil(){
		$this->db2->insert("adis_wil", array(
				"kode"=>$this->input->post("kode"),
				"parent"=>$this->input->post("prop"),
				"nama"=>$this->input->post("kab")
			));
	}
	
	function mGedung(){
		
		
		return $data;
	}
	
	function mPerson(){
		$sql = "SELECT kode, nama FROM adis_personal ORDER BY nama;";
		$sql = $this->db2->query($sql)->result();
		$this->smarty->assign('person', $sql);
	}
	
	function mLevel(){
		$sql = "SELECT * FROM tbl_usrm_level WHERE status = 1";
		$sql = $this->db2->query($sql)->result();
		$this->smarty->assign('level', $sql);
	}
	
	function mProp(){
		$sql="SELECT * FROM adis_wil WHERE parent IS NULL AND erased = 0";
		$sql = $this->db2->query($sql)->result();	
		$this->smarty->assign('prop',$sql);					
	}
	
	function mAddGedung(){
		$this->db2->insert("adis_gedung", array(
				"kode"=>$this->input->post("kode"),
				"nama"=>$this->input->post("gedung"),
				"alamat"=>$this->input->post("alamat"),
				"kabKota"=>$this->input->post("kab"),
				"prop"=>$this->input->post("prop"),
				"total_ruang"=>$this->input->post("ruang"),
			));
	}
	
	function mAddRuang(){
		$this->db2->insert("adis_ruang", array(
				"kode"=>$this->input->post("kode"),
				"nama"=>$this->input->post("ruang"),
				"lantai"=>$this->input->post("lantai"),
				"kursiUmum"=>$this->input->post("umum"),
				"kursiTes"=>$this->input->post("tes"),
				"gedung"=>$this->input->post("gedung")
			));
	}
	
	function mJabatanS()
	{
		$sql ="SELECT * FROM adis_jabatan WHERE erased=0 ORDER BY kode";
		$sql = $this->db2->query($sql)->result();
			
		$this->smarty->assign('jabatan',$sql);	
	}
	
	function mAddPanitia($status){
		if ($status == 1){
			$dosen = 1;
			$kar = 0;
			$nidn = $this->input->post('nip');
			$nip ="";
		}else{
			$dosen = 0;
			$kar = 1;
			$nip = $this->input->post('nip');
			$nidn ="";
		}
		$this->db2->insert("adis_personal", array(
				"kode"=>$this->input->post("kode"),
				"nama"=>$this->input->post("nama"),
				"stsDosen"=>$dosen,
				"stsKaryawan"=>$kar,
				'nip'=>$nip,
				'nidn'=>$nidn,
				"jabatan"=>$this->input->post("jabatan")
			));
	}
	
	function mEditPanitia($status, $kode){
	
		if ($status == 1){
			$dosen = 1;
			$kar = 0;
			$nidn = $this->input->post('nip');
			$nip ="";
		}else{
			$dosen = 0;
			$kar = 1;
			$nip = $this->input->post('nip');
			$nidn ="";
		}
		
		$this->db2->where("kode",$kode);
		$this->db2->update("adis_personal", array(
				"nama"=>$this->input->post("nama"),
				"stsDosen"=>$dosen,
				"stsKaryawan"=>$kar,
				'nip'=>$nip,
				'nidn'=>$nidn,
				"jabatan"=>$this->input->post("jabatan")
			));
	}
	
	function mAddJalur($file_name, $full_path){
		$this->db2->insert("adis_jalur_smb", array(
				"kode"=>$this->input->post("kode"),
				"nama"=>$this->input->post("nama"),
				"nomorSk"=>$this->input->post("nosk"),
				"fileSk"=>$file_name,
				"filePath"=>$full_path
			));
	}
	
	function mEditJalur($kode, $uploadFile){
	
			
			
			if (!$uploadFile){
				$this->db2->where("kode",$kode);
				$this->db2->update("adis_jalur_smb", array(
					"kode"=>$this->input->post("kode"),
					"nama"=>$this->input->post("nama"),
					"nomorSk"=>$this->input->post("nosk")
				));
				echo "<script>alert('Data Berhasil Disimpan! NonFile');</script>";
			
			}else{
			
				$data = $this->upload->data();
				$file_name = $data['file_name'];				
				$full_path = $data['full_path'];				
				
				$this->db2->where("kode",$kode);
				$this->db2->update("adis_jalur_smb", array(
					"kode"=>$this->input->post("kode"),
					"nama"=>$this->input->post("nama"),
					"nomorSk"=>$this->input->post("nosk"),
					"fileSk"=>$file_name,
					"filePath"=>$full_path
				));
				echo "<script>alert('Data Berhasil Disimpan! WithFile');</script>";
			}
		
		
	}
	
	function mPeriode($kode, $uri){
		if ($uri == 'add'){
			$tahun = $this->input->post("startYear");
			$jenjang = $this->input->post("jenjang");
			$semester = $this->input->post("semester");
			
			$kode = array($tahun,$jenjang,$semester);
			$kode = implode (".",$kode);
			$this->db2->insert("adis_periode_master", array(
					"kode"=>$kode,
					"nama"=>$this->input->post("nama"),
					"tahun"=>$tahun ,
					"tahunSelesai"=>$this->input->post("endYear"),
					"tanggalMulai"=>$this->input->post("from"),
					"tanggalSelesai"=>$this->input->post("to"),
					"jenjangType"=>$jenjang,
					"semester"=>$semester,
					"status"=>$this->input->post("status")				
				));
		}else if ($uri == 'edit'){
			$tahun = $this->input->post("startYear");
			$jenjang = $this->input->post("jenjang");
		
			$this->db2->where("kode",$kode);
			$this->db2->update("adis_periode_master", array(
					"nama"=>$this->input->post("nama"),
					"tahun"=>$tahun ,
					"tahunSelesai"=>$this->input->post("endYear"),
					"tanggalMulai"=>$this->input->post("from"),
					"tanggalSelesai"=>$this->input->post("to"),
					"jenjangType"=>$jenjang,
					"semester"=>$this->input->post("semester"),
					"status"=>$this->input->post("status")				
				));
		}
	}
	
	function mAddPeriode(){
		$periode = $this->input->post("periode");
		$sesi = $this->input->post("sesi");
		$kode = implode ('.',array($periode,$sesi));
		$this->db2->insert("adis_periode", array(
				"kode"=>$kode,
				"nama"=>$this->input->post("nama"),
				"tanggalMulai"=>$this->input->post("from"),
				"tanggalSelesai"=>$this->input->post("to"),
				"sesi"=>$sesi,
				"status"=>$this->input->post("status"),
				"idPeriodeMaster"=>$this->input->post("periode")	
			));
	}
	
	function mEditPeriode($kode){
		$sesi = $this->input->post("sesi");
		
		$this->db2->where("kode",$kode);
		$this->db2->update("adis_periode", array(
				"nama"=>$this->input->post("nama"),
				"tanggalMulai"=>$this->input->post("from"),
				"tanggalSelesai"=>$this->input->post("to"),
				"sesi"=>$sesi,
				"status"=>$this->input->post("status"),
				"idPeriodeMaster"=>$this->input->post("periode")					
			));
	}
	
	function mAddPembayaranDU(){
	
		$date = date("Y-m-d H:i:s");
		
		$jalur = $this->input->post('jalur');
		$prodi = $this->input->post('prodi');
		$pembayaran = $this->input->post('pembayaran');
		//$peringkat = $this->input->post('peringkat');
		
		$d_date1 = $this->input->post('d_angsur1');
		$d_date2 = $this->input->post('d_angsur2');
		$d_date3 = $this->input->post('d_angsur3');
		$d_date4 = $this->input->post('d_angsur4');
		$d_date5 = $this->input->post('d_angsur5');
		
		$kode = implode (".",array($prodi,$pembayaran,$jalur));
		
		$um = str_replace(' ', '',str_replace('.', '', $this->input->post('um')));	
		
		$um1 = str_replace(' ', '',str_replace('.', '', $this->input->post('um1')));		
		$um2 = str_replace(' ', '', str_replace('.', '', $this->input->post('um2')));
		$um3 = str_replace(' ', '', str_replace('.', '', $this->input->post('um3'))); 
		
		$bpp = str_replace(' ', '', str_replace('.', '', $this->input->post('bpp')));  
		$sks = str_replace(' ', '', str_replace('.', '', $this->input->post('sks'))); 
		$spp = str_replace(' ', '', str_replace('.', '', $this->input->post('spp')));		
		
		$ang1 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran1')));
		$ang2 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran2')));
		$ang3 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran3')));	
		$ang4 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran4')));
		$ang5 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran5')));
		
		$angsuran1_1 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran1_1')));
		$angsuran1_2 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran1_2')));
		$angsuran1_3 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran1_3')));
		
		$angsuran2_1 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran2_1')));
		$angsuran2_2 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran2_2')));
		$angsuran2_3 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran2_3')));
		
		$angsuran3_1 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran3_1')));
		$angsuran3_2 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran3_2')));
		$angsuran3_3 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran3_3')));
		
		$totalA = str_replace(' ', '', str_replace('.', '', $this->input->post('total')));
		
		$total = array_sum(array($um,$bpp,$sks,$spp));
		
		$total1 = array_sum(array($um1,$bpp,$sks,$spp));
		$total2 = array_sum(array($um2,$bpp,$sks,$spp));
		$total3 = array_sum(array($um3,$bpp,$sks,$spp));
		
		if ($jalur != 'KP' ){
			$this->db2->insert("adis_pembayaran", array(
					"kode"=>$kode,
					"dateCreated"=>$date,
					"nama"=>$this->input->post('nama'),
					"prodiTipe"=>$prodi,
					//"peringkat"=>$peringkat,
					"pembayaran"=>$pembayaran,				
					"uangMasuk1"=>$um1,
					"uangMasuk2"=>$um2,
					"uangMasuk3"=>$um3,
					"bpp"=>$bpp,
					"sks"=>$sks,
					"jumlahSks"=>$this->input->post('jumlahSks'),
					"spp"=>$spp,				
					"total1"=>$total1,
					"total2"=>$total2,
					"total3"=>$total3,
					"status"=>$this->input->post('status'),
					"tempoAngsuran"=>$this->input->post('angsurCount'),
					"jalur"=>$jalur
					));
			if ($pembayaran == '69'){
				$this->db2->insert("adis_cicilan", array(
						'id_pembayaran'=>$kode,
						'angsuran1_1'=>$angsuran1_1,
						'angsuran1_2'=>$angsuran1_2,
						'angsuran1_3'=>$angsuran1_3,
						'due_date1'=>date("Y-m-d", strtotime($this->input->post('d_angsur1'))),
						'angsuran2_1'=>$angsuran2_1,
						'angsuran2_2'=>$angsuran2_2,
						'angsuran2_3'=>$angsuran2_3,
						'due_date2'=>date("Y-m-d", strtotime($this->input->post('d_angsur2'))),
						'angsuran3_1'=>$angsuran3_1,
						'angsuran3_2'=>$angsuran3_2,
						'angsuran3_3'=>$angsuran3_3,
						'due_date3'=>date("Y-m-d", strtotime($this->input->post('d_angsur3')))
					));
			}
		}else{
			if ($prodi == '1208'){$kode = $kode.'.'.$this->input->post('grade');}
			$this->db2->insert("adis_pembayaran_paralel", array(
					"kode"=>$kode,
					"dateCreated"=>$date,
					"nama"=>$this->input->post('nama'),
					"prodiTipe"=>$prodi,
					"pembayaran"=>$pembayaran,				
					"uangMasuk"=>$um,
					"bpp"=>$bpp,
					"sks"=>$sks,
					"spp"=>$spp,				
					"total"=>$total,
					"status"=>$this->input->post('status'),
					"tempoAngsuran"=>$this->input->post('angsurCount'),
					"totalAngsuran"=>$totalA,
					"angsuran1"=>$ang1,
					"angsuran2"=>$ang2,
					"angsuran3"=>$ang3,
					"angsuran4"=>$ang4,
					"angsuran5"=>$ang5,
					"jumlahSks"=>$this->input->post('jumlahSks'),
					"jalur"=>$jalur,
					"due_date1"=>date("Y-m-d", strtotime($d_date1)),
					"due_date2"=>date("Y-m-d", strtotime($d_date2)),
					"due_date3"=>date("Y-m-d", strtotime($d_date3)),
					"due_date4"=>date("Y-m-d", strtotime($d_date4)),
					"due_date5"=>date("Y-m-d", strtotime($d_date5))
					));
		}
	}
	
	function mEditPembayaran($kode){
		
		$date = date("Y-m-d H:i:s");
		$d_date1 = $this->input->post('d_angsur1');
		$d_date2 = $this->input->post('d_angsur2');
		$d_date3 = $this->input->post('d_angsur3');
		
		$var = 0;
				
		$um1 = str_replace(' ', '',str_replace('.', '', $this->input->post('um1')));		
		$um2 = str_replace(' ', '', str_replace('.', '', $this->input->post('um2')));
		$um3 = str_replace(' ', '', str_replace('.', '', $this->input->post('um3'))); 
		
		$bpp = str_replace(' ', '', str_replace('.', '', $this->input->post('bpp')));  
		$sks = str_replace(' ', '', str_replace('.', '', $this->input->post('sks'))); 
		$spp = str_replace(' ', '', str_replace('.', '', $this->input->post('spp')));		
		
		$ang1 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran1')));
		$ang2 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran2')));
		$ang3 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran3')));	
		$ang4 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran4')));
		$ang5 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran5')));
		$ang6 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran6')));	
		$ang7 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran7')));
		$ang8 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran8')));
		
		
		$angsuran1_1 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran1_1')));
		$angsuran1_2 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran1_2')));
		$angsuran1_3 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran1_3')));
		
		$angsuran2_1 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran2_1')));
		$angsuran2_2 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran2_2')));
		$angsuran2_3 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran2_3')));
		
		$angsuran3_1 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran3_1')));
		$angsuran3_2 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran3_2')));
		$angsuran3_3 = str_replace(' ', '', str_replace('.', '', $this->input->post('angsuran3_3')));
		
		
		$angsurCount = $this->input->post('angsurCount');
		$totalA = $this->input->post('total');
		
		$totalA = str_replace('.', '', $totalA); 
		$totalA = str_replace(' ', '', $totalA);	
		
		
		$total1 = array_sum(array($um1,$bpp,$sks,$spp));
		$total2 = array_sum(array($um2,$bpp,$sks,$spp));
		$total3 = array_sum(array($um3,$bpp,$sks,$spp));
		
		$tipePemb = $this->input->post('tipePemb');
		$jalur = $this->input->post('jalur');
		$kodecil = $this->input->post('kodecil');
		
		if ($tipePemb != 'KP'){
			$this->db2->where("kode",$kode);
			$this->db2->update("adis_pembayaran", array(
					"dateUpdated"=>$date,
					"nama"=>$this->input->post('nama'),			
					"uangMasuk1"=>$um1,
					"uangMasuk2"=>$um2,
					"uangMasuk3"=>$um3,
					"bpp"=>$bpp,
					"sks"=>$sks,
					"spp"=>$spp,	
					"jumlahSks"=>$this->input->post('jumlahSks'),			
					"total1"=>$total1,
					"total2"=>$total2,
					"total3"=>$total3,
					"status"=>$this->input->post('status'),
					"tempoAngsuran"=>$angsurCount
					));
				if ($tipePemb == '69'){
					$this->db2->where("kode",$kodecil);
					$this->db2->update("adis_cicilan", array(
						'angsuran1_1'=>$angsuran1_1,
						'angsuran1_2'=>$angsuran1_2,
						'angsuran1_3'=>$angsuran1_3,
						'due_date1'=>date("Y-m-d", strtotime($this->input->post('d_angsur1'))),
						'angsuran2_1'=>$angsuran2_1,
						'angsuran2_2'=>$angsuran2_2,
						'angsuran2_3'=>$angsuran2_3,
						'due_date2'=>date("Y-m-d", strtotime($this->input->post('d_angsur2'))),
						'angsuran3_1'=>$angsuran3_1,
						'angsuran3_2'=>$angsuran3_2,
						'angsuran3_3'=>$angsuran3_3,
						'due_date3'=>date("Y-m-d", strtotime($this->input->post('d_angsur3')))
					));
				}
		}else{		
			$this->db2->where("kode",$kode);
			$this->db2->update("adis_pembayaran", array(
					"dateUpdated"=>$date,
					"nama"=>$this->input->post('nama'),					
					"uangMasuk1"=>$um1,
					"uangMasuk2"=>$um2,
					"uangMasuk3"=>$um3,
					"bpp"=>$bpp,
					"sks"=>$sks,
					"spp"=>$spp,				
					"total1"=>$total1,
					"total2"=>$total2,
					"total3"=>$total3,
					"status"=>$this->input->post('status'),
					"tempoAngsuran"=>$angsurCount,
					"totalAngsuran"=>$totalA,
					"angsuran1"=>$ang1,
					"angsuran2"=>$ang2,
					"angsuran3"=>$ang3,
					"due_date1"=>$d_date1,
					"due_date2"=>$d_date2,
					"due_date3"=>$d_date3
					));
		}
	}
	
	function mKab($wil){
		$sql="SELECT kode, nama FROM adis_wil WHERE parent = ".$wil." AND erased = 0 ORDER BY nama ASC";
		$sql = $this->db2->query($sql)->result();	
		$this->smarty->assign('kab',$sql);
	}
	
	function mjabatan(){
		$sql = "SELECT * FROM tbl_master_jabatan ORDER BY description";
		$sql = $this->db->query($sql)->result();
		$this->smarty->assign('jabatan',$sql);
		
		return $sql;
	}
	
	function mdepart(){
		$sql = "SELECT * FROM tbl_master_department ORDER BY description";
		$sql = $this->db->query($sql)->result();
		$this->smarty->assign('depart',$sql);
		
		return $sql;
	}
	
	function mAddPeg(){
		$this->db->insert("tbl_master_personal", array(
				"nama"=>$this->input->post("nama"),
				"inisial"=>$this->input->post("inisial"),
				"alamat_skr"=>$this->input->post("alamat"),
				"personal_NIP"=>$this->input->post("nip"),
				"email"=>$this->input->post("email"),
				"phone"=>$this->input->post("telp"),
				"mobile_phone1"=>$this->input->post("hp"),
				"id_depart"=>$this->input->post("depart"),
				"id_jabatan"=>$this->input->post("jabatan")
			));
	}
	function mDelPeg($id){
		$sql = "DELETE FROM tbl_master_personal WHERE id = ".$id."";
		$query = $this->db->query($sql);
		$sql = "DELETE FROM tbl_usrm_users WHERE id_pegawai = ".$id."";
		$deluser = $this->db->query($sql);
		return $query;
		
	}
	function mEditPeg($nip){
		$sql ="SELECT id, nama, inisial, personal_NIP, alamat_skr, email, phone, mobile_phone1,
			d.acadept_uid as d_id, d.description as depart, j.acajbt_uid as j_id, j.description as jabatan 
			FROM tbl_master_personal p
			INNER JOIN tbl_master_department d ON p.id_depart = d.acadept_uid
			INNER JOIN tbl_master_jabatan j ON p.id_jabatan = j.acajbt_uid
			where id=".$nip." 
			GROUP BY id";
		$row = $this->db->query($sql)->row();
		$this->smarty->assign("pegawai",$row);
		$this->mjabatan();
		$this->mdepart();
		return $row;
	}
	
	function mConfUser($id_user, $pass, $pid, $tombol, $nama){
	
		if($id_user != "" && $tombol == "update"){			
			$this->db->where("id",$id_user);
			$this->db->update("tbl_usrm_users", array(
				"username"=>$this->input->post("username"),
				"password"=>$pass,
				"name"=>$nama,
				"tbl_usrm_level_id"=>$this->input->post("level_id"),
				"id_pegawai"=> $pid
			));
		}else{
			$this->db->insert("tbl_usrm_users", array(
				"username"=>$this->input->post("username"),
				"password"=>$pass,
				"name"=>$nama,
				"tbl_usrm_level_id"=>$this->input->post("level_id"),
				"id_pegawai"=> $pid
			));
		}
	}
	
	function mCutiBer(){
		$sql ="SELECT * FROM tbl_cuti_bersama order by tgl_cuti ASC";
		$sql = $this->db->query($sql)->result();
		$this->smarty->assign("cuti_ber", $sql);
	}
	
	function mAddCutiBer($nama, $days){
		$this->db->insert("tbl_cuti_bersama", array(
				"ket_cuti_bersama"=>$this->input->post("cuti_ber"),
				"tgl_cuti"=>$this->input->post("tgl_cuti"),
				"tgl_akhir_cuti"=>$this->input->post("tgl_selesai_cuti"),
				"status"=>$this->input->post("status_cuti"),
				"total_cuti"=>$days,
				"input_by"=>$nama
				));
	}
	
	function mEditCuti($id){
		$sql ="SELECT * FROM tbl_cuti_bersama WHERE id=".$id." LIMIT 1";
		$sql = $this->db->query($sql)->row();
		$this->smarty->assign("cuti_ber", $sql);
	}
	
	function mUpdCuti($nama, $days, $id){
		$this->db->where("id",$id);
		$this->db->update("tbl_cuti_bersama", array(
				"ket_cuti_bersama"=>$this->input->post("cuti_ber"),
				"tgl_cuti"=>$this->input->post("tgl_cuti"),
				"tgl_akhir_cuti"=>$this->input->post("tgl_selesai_cuti"),
				"status"=>$this->input->post("status_cuti"),
				"total_cuti"=>$days,
				"input_by"=>$nama
				));
	}
	
	function mDelCuti($id){
		$sql = "DELETE FROM tbl_cuti_bersama WHERE id = ".$id."";
		$query = $this->db->query($sql);
		return $query;
		
	}
	
	function mPeriodeCuti(){
		$sql ="SELECT * FROM tbl_periode_cuti order by periode_cuti DESC";
		$sql = $this->db->query($sql)->result();
		$this->smarty->assign("periode_cuti", $sql);
	}
	
	function mAddPeriodes($nama, $date1, $date2, $periode){
		$this->db->insert("tbl_periode_cuti", array(
				"periode_cuti"=>$periode,
				"tahun"=>date("Y"),
				"tgl_mulai"=>$date1,
				"tgl_akhir"=>$date2,
				"isActive"=>$this->input->post("status_periode")
				));
	}
	
	
	
}
