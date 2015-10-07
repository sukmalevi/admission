<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Master extends CUTI_Controller{
	function __construct(){
		parent::__construct();
		$this->auth = unserialize(base64_decode($this->session->userdata('cuti_parmad')));
		if(! $this->auth) {redirect('/home','refresh');}//header("Location: " . $this->host);}	
		
		$this->db2 = $this->load->database('second', TRUE);
		$this->db = $this->load->database('default', TRUE); 
		$this->load->model('mmaster');
		$modul = "master/";
		$this->smarty->assign('modul',$modul);		
		
		$nama = $this->auth['name'];
		$this->smarty->assign('nama',$nama);
		
		$level = $this->auth['level'];
		$this->smarty->assign('level',$level);
		
		//$host = $this->host;
		
		if ($this->auth['level'] != "99" && $this->auth['level'] != "44" && $this->auth['level'] != "66" && $this->auth['level'] != "77" )
		{ redirect('/home','refresh');}
	}
	
	function index(){
	}
	
//FUNGSI UNTUK MASTER USER	
	function masterUser(){
		$uri = $this->uri->segment(3);
		$this->load->library('encrypt');
		$opt = $this->input->post("save");
		$method = $this->input->post("opt");
		$kode = $this->input->post("val");
						
		$row["pass"] = $this->input->post("pass");
		$pass = $this->encrypt->encode($row["pass"]);
				
		if (!$uri){
			$this->mmaster->mPerson();
			$this->mmaster->mLevel();
			$sql="SELECT username, password, email, id_level, kode, l.id, l.level_name as level FROM adis_sys_usr u
				 INNER JOIN tbl_usrm_level l ON l.id = u.id_level WHERE u.erased = 0";
			$sql = $this->db2->query($sql)->result();	
			$this->smarty->assign('sql',$sql);
			$site = "User";
			$this->smarty->assign('site',$site);
			$this->smarty->assign('lokasi',"Master User");
			$this->smarty->display('index.html');
				
		}else if ($uri == "save" && $opt == "save"){
		
			$this->mmaster->mAddUser($pass);
			redirect('/master/masterUser','refresh');
			
		}else if ($uri == "update" && $method == "update"){
		
			$this->mmaster->mEditUser($pass);
			redirect('/master/masterUser','refresh');
			
		}else if($uri == "edit" && $method == "edit"){			
			$sql="SELECT username, password, email, id_level, kode, l.id, l.level_name as level FROM adis_sys_usr u 
				 INNER JOIN tbl_usrm_level l ON l.id = u.id_level
				 WHERE kode='$kode'";
			$sql = $this->db2->query($sql)->row();	
			
			$pass = $this->encrypt->decode($sql->password);
			
			$this->mmaster->mPerson();
			$this->mmaster->mLevel();
			
			$this->smarty->assign('userp',$pass);
			$this->smarty->assign('user',$sql);
			$this->smarty->display('master/formUser.html');
		}else if ($uri == "delete" && $method == "delete"){
			if($this->input->post("id")=="confirm")	{	
				$this->db2->where("kode",$kode);
				$this->db2->update("adis_sys_usr", array("erased"=>'1'));
				//$sql = "UPDATE kode FROM adis_sys_usr WHERE kode = '$kode'";
				
				redirect('/master/masterUser','refresh');
			}else{
				$this->smarty->assign("value",$kode);
				$this->smarty->display("master/formVerifikasi.html");
			}
		}
			
			
			
	}
	
	function masterWilayah(){
		$uri = $this->uri->segment(3);
		$uri2 = $this->uri->segment(4);
		$wil = $this->input->post('wil');
		$opt = $this->input->post('opt');
		$val = $this->input->post('val');
		$kode = $this->input->post('value');
		
		if ($uri == "add" && $opt == "save"){
			$this->mmaster->mAddWil();
			redirect('/master/masterWilayah','refresh');
		}else if ($uri == "delete" && $opt == "delete"){
			
			$fungsi = "masterWilayah";
			$this->smarty->assign("fungsi",$fungsi);
			
			$this->smarty->assign("value",$val);
			$this->smarty->display("master/verifikasiWil.html");
			if($this->input->post("id")=="confirm")	{	
				
				$this->db2->where("kode",$kode);
				$this->db2->update("adis_wil", array("erased"=>'1'));
				
				redirect('/master/masterWilayah','refresh');
				
			}
		}else if ($uri == "edit" && $opt == "edit"){
		
			if ($uri2 != ""){
				$id = $this->input->post("kode");
				$this->db2->where("kode",$id);
				$this->db2->update("adis_wil", array(
					"kode"=>$this->input->post("kode"),
					"parent"=>$this->input->post("prop"),
					"nama"=>$this->input->post("kab")
				));
				
				redirect('/master/masterWilayah','refresh');
			
			}else{
					
				$data = $this->db2->query("SELECT * FROM adis_wil WHERE kode = '$val'")->row();
				$parent = $this->db2->query("SELECT * FROM adis_wil WHERE parent IS NULL AND kode = ".$data->parent."")->row();
				
				$sql="SELECT * FROM adis_wil WHERE parent IS NULL AND erased = 0";
				$sql = $this->db2->query($sql)->result();	
				$this->smarty->assign('sql',$sql);
				
				$this->smarty->assign("data",$data);
				$this->smarty->assign("prop",$parent);
				$this->smarty->display("master/formWilayah.html");
			
			}
			
		
		}else if ($uri == "table" && $wil != ""){
			$sql="SELECT * FROM adis_wil WHERE parent IS NULL AND erased = 0";
			$sql = $this->db2->query($sql)->result();	
			$this->smarty->assign('sql',$sql);			
		
			$sql="SELECT kode, nama FROM adis_wil WHERE parent = ".$wil." AND erased = 0 ORDER BY nama ASC";
			$sql = $this->db2->query($sql)->result();	
			$this->smarty->assign('kab',$sql);	
			$this->smarty->display('master/tblWilayah.html');
		}else{
			$sql="SELECT * FROM adis_wil WHERE parent IS NULL";
			$sql = $this->db2->query($sql)->result();
			$site = "Wilayah";
			$this->smarty->assign('site',$site);	
			$this->smarty->assign('sql',$sql);	
			$this->smarty->assign('lokasi',"Master Wilayah");
			$this->smarty->display('index.html');
		}
	}
	
	function masterUPM(){
		$sql ="SELECT * FROM adis_perguruan";
		$sql = $this->db2->query($sql)->result();
		$site = "UPM";
		$this->smarty->assign('site',$site);			
		$this->smarty->assign('sql',$sql);	
		$this->smarty->assign('lokasi',"Data Perguruan Tinggi");
		$this->smarty->display('index.html');
	}
	
	function masterProdi(){
		$sql ="SELECT * FROM adis_prodi WHERE erased = 0 ORDER BY kode";
		$sql = $this->db2->query($sql)->result();
		$site = "Prodi";
		$this->smarty->assign('site',$site);			
		$this->smarty->assign('sql',$sql);	
		$this->smarty->assign('lokasi',"Master Prodi");
		$this->smarty->display('index.html');
	}
	
	function masterGedung(){	
	
		$uri = $this->uri->segment(3);
		$uri2 = $this->uri->segment(4);
		$opt = $this->input->post("opt");
		if (!$uri){	
			$this->mmaster->mProp();
			$data = array();
			$sql="SELECT g.kode as kode, g.nama as nama, alamat, kabKota, prop, kodePos, total_ruang, w.nama as kabupaten, wa.nama as propinsi
				 FROM adis_gedung g
				 LEFT JOIN adis_wil w ON w.kode = g.kabKota
				 LEFT JOIN adis_wil wa ON wa.kode = g.prop
				 WHERE g.erased = 0";
			$sql = $this->db2->query($sql)->result();
					
			$site = "Gedung";
			$this->smarty->assign('site',$site);			
			$this->smarty->assign('gedung',$sql);				
			$this->smarty->assign('ruang',$data);
			$this->smarty->assign('lokasi',"Master Gedung");	
			$this->smarty->display('index.html');
			
		}else if ($uri == "prop"){
			$wil = $this->input->post("prop");
			$this->mmaster->mKab($wil);
			$this->smarty->display("master/selectKabs.html");
			
		}else if ($uri == "add" && $opt == "save"){
			$this->mmaster->mAddGedung();
			redirect('/master/masterGedung','refresh');
			
		}else if ($uri == "edit" && $opt == "edit"){
		
			if ($uri2 != ""){
				$id = $this->input->post("kode");
				$this->db2->where("kode",$id);
				$this->db2->update("adis_gedung", array(
					"kode"=>$this->input->post("kode"),
					"nama"=>$this->input->post("gedung"),
					"alamat"=>$this->input->post("alamat"),
					"kabKota"=>$this->input->post("kab"),
					"prop"=>$this->input->post("props"),
					"total_ruang"=>$this->input->post("ruang")
				));
				
				redirect('/master/masterGedung','refresh');
			}else{
		
			$this->mmaster->mProp();
			$kode = $this->input->post("val");
			$sql="SELECT g.kode as id, g.nama as gedung, alamat, kabKota, prop, total_ruang, w.nama as kabupaten, wa.nama as propinsi
				 FROM adis_gedung g
				 LEFT JOIN adis_wil w ON w.kode = g.kabKota
				 LEFT JOIN adis_wil wa ON wa.kode = g.prop
				 WHERE g.kode = '$kode'";
			$sql = $this->db2->query($sql)->row();
			$this->smarty->assign("gedung", $sql);
			$this->smarty->display("master/formGedung.html");
			}
		}
	}
	
	function masterRuang(){
		$uri = $this->uri->segment(3);
		$uri2 = $this->uri->segment(4);
		$gedung = $this->input->post('gedung');
		$opt = $this->input->post('opt');
		if ($uri == "table" && $gedung != ""){
			
			$sql2="SELECT * FROM adis_gedung WHERE erased = 0";
			$sql2 = $this->db2->query($sql2)->result();	
			$this->smarty->assign('sql',$sql2);	
		
			$sql="SELECT kode, nama, lantai, kursiUmum, kursiTes, gedung FROM adis_ruang WHERE gedung = '$gedung' AND erased = 0 ORDER BY nama ASC";
			$gdg = $this->db2->query("SELECT nama FROM adis_gedung WHERE kode = '$gedung'")->row();
			$sql = $this->db2->query($sql)->result();
			
			$this->smarty->assign('gdg',$gdg);	
			$this->smarty->assign('ruang',$sql);	
			$this->smarty->display('master/tblRuang.html');
			
		}else if($uri == "add" && $opt == "save"){
			
			$this->mmaster->mAddRuang();
			redirect('/master/masterRuang','refresh');
			
		}else if($uri == "delete" && $opt == "delete"){
			$id = $this->input->post("id");
				
			$fungsi = "masterRuang";
			$val = $this->input->post("val");
			$this->smarty->assign("value",$val);
			$this->smarty->assign("fungsi",$fungsi);
			$this->smarty->display("master/verifikasiWil.html");
			
				if ($id == "confirm"){
					$kode = $this->input->post("value");
					$this->db2->where("kode",$kode);
					$this->db2->update("adis_ruang", array("erased"=>'1'));
					
					redirect('/master/masterRuang','refresh');
				}
				
			
		}else if($uri == "edit" && $opt == "edit"){
			
			if (!$uri2){
				
				$val = $this->input->post("val");
				
				$sql2="SELECT r.kode as kode_r, r.nama as nama_r, lantai, kursiUmum, kursiTes, gedung, g.nama as nama_gedung
					   FROM adis_ruang r 
					   INNER JOIN adis_gedung g ON g.kode = r.gedung
					   WHERE r.kode = '$val'";
				$sql2 = $this->db2->query($sql2)->row();
				
				$sql="SELECT * FROM adis_gedung";
				$sql = $this->db2->query($sql)->result();	
				$this->smarty->assign('sql',$sql);
			
				$this->smarty->assign("ruang",$sql2);
				$this->smarty->assign("value",$val);
				$this->smarty->display("master/formRuang.html");
			}else{
				$id = $this->input->post("kode");
				$this->db2->where("kode",$id);
				$this->db2->update("adis_ruang", array(
					"kode"=>$this->input->post("kode"),
					"nama"=>$this->input->post("ruang"),
					"lantai"=>$this->input->post("lantai"),
					"kursiUmum"=>$this->input->post("umum"),
					"kursiTes"=>$this->input->post("tes"),
					"gedung"=>$this->input->post("gedung")
				));			
			}
			
			redirect('/master/masterRuang','refresh');
			
		}else{
			$sql="SELECT * FROM adis_gedung WHERE erased = 0";
			$sql = $this->db2->query($sql)->result();
			$site = "Ruang";
			$this->smarty->assign('site',$site);	
			$this->smarty->assign('sql',$sql);	
			$this->smarty->assign('lokasi',"Master Ruang");
			$this->smarty->display('index.html');
		}
	}
	
	function masterJabatan(){
		$uri = $this->uri->segment(3);
		$uri2 = $this->uri->segment(4);
		$gedung = $this->input->post('gedung');
		$opt = $this->input->post('opt');
		
		if ($uri == "add" && $opt == "save"){
			$this->db2->insert("adis_jabatan", array(
				"kode"=>$this->input->post("kode"),
				"nama"=>$this->input->post("jab"),
				"singkatan"=>$this->input->post("singkatan")
				));
			redirect('/master/masterJabatan','refresh');
			
		}else if ($uri == "edit" && $opt == "edit"){
			$value = $this->input->post("val");	
			
			if (!$uri2){			
				$sql ="SELECT * FROM adis_jabatan WHERE kode = '$value' AND erased=0 ORDER BY kode";
				$jabatan = $this->db2->query($sql)->row();
				
				$this->smarty->assign('jabatan',$jabatan);	
				$this->smarty->display('master/formJabatan.html');
			}else{
				$kode = $this->input->post("kode");
				$this->db2->where("kode",$kode);
				$this->db2->update("adis_jabatan", array(
					"kode"=>$this->input->post("kode") ,
					"nama"=>$this->input->post("jab"),
					"singkatan"=>$this->input->post("singkatan")
				));
				
				redirect('/master/masterJabatan','refresh');
			}
		
		}else if ($uri == "delete" && $opt == "delete"){
		
			$id = $this->input->post("id");
				
			$fungsi = "masterJabatan";
			$val = $this->input->post("val");
			$this->smarty->assign("value",$val);
			$this->smarty->assign("fungsi",$fungsi);
			$this->smarty->display("master/verifikasiWil.html");
			
				if ($id == "confirm"){
					$kode = $this->input->post("value");
					$this->db2->where("kode",$kode);
					$this->db2->update("adis_jabatan", array("erased"=>'1'));
					
					redirect('/master/masterJabatan','refresh');
				}
		
		
		}else{
	
			$sql ="SELECT * FROM adis_jabatan WHERE erased=0 ORDER BY kode";
			$sql = $this->db2->query($sql)->result();
			$site = "Jabatan";
			$this->smarty->assign('site',$site);			
			$this->smarty->assign('sql',$sql);	
			$this->smarty->assign('lokasi',"Master Jabatan");
			$this->smarty->display('index.html');
		}
	}
	
	function masterPanitia(){
		
		$uri = $this->uri->segment(3);
		$uri2 = $this->uri->segment(4);
		$gedung = $this->input->post('gedung');
		$opt = $this->input->post('opt');
			
		$status = $this->input->post('optionsRadios');
		
		if ($uri == "add" && $opt == "save"){
			
			$this->mmaster->mAddPanitia($status);
			
			redirect('/master/masterPanitia','refresh');
			
		}else if ($uri == "edit" && $opt == "edit"){
			$val = $this->input->post("val");
			if (!$uri2){			
				$this->mmaster->mJabatanS();
				
				$qry = "SELECT p.erased as status, p.kode as inisial, p.nama as panitia, p.stsDosen, p.nidn, p.nip,
				p.stsKaryawan, p.prodi, p.jabatan, r.nama as ps, j.nama as jab 
				FROM adis_personal p 
				LEFT JOIN adis_prodi r ON r.kode = p.prodi
				LEFT JOIN adis_jabatan j ON j.kode = p.jabatan
				WHERE p.kode = '$val'";
				$person = $this->db2->query($qry)->row();
				
				$this->smarty->assign('person',$person);	
				$this->smarty->display('master/formPanitia.html');
			}else{
				
				$kode = $this->input->post("kode");
				$this->mmaster->mEditPanitia($status, $kode);
				
				redirect('/master/masterPanitia','refresh');
			}
			
		}else if ($uri == "delete" && $opt == "delete"){
			
			$id = $this->input->post("id");
			$fungsi = "masterPanitia";
			$val = $this->input->post("val");
			$this->smarty->assign("value",$val);
			$this->smarty->assign("fungsi",$fungsi);
			$this->smarty->display("master/verifikasiWil.html");
			if ($id == "confirm"){
					$kode = $this->input->post("value");
					$this->db2->where("kode",$kode);
					$this->db2->update("adis_personal", array("erased"=>'1'));
					
					redirect('/master/masterPanitia','refresh');
				}
			
		}else{	
			$this->mmaster->mJabatanS();
			$sql ="SELECT p.erased as status, p.kode as inisial, p.nama as panitia, p.stsDosen, p.stsKaryawan, p.prodi, p.jabatan, r.nama as ps, j.nama as jab FROM adis_personal p 
				LEFT JOIN adis_prodi r ON r.kode = p.prodi
				LEFT JOIN adis_jabatan j ON j.kode = p.jabatan
				ORDER BY p.nama ASC";
			$sql = $this->db2->query($sql)->result();
			$site = "Panitia";
			$this->smarty->assign('site',$site);			
			$this->smarty->assign('sql',$sql);	
			$this->smarty->assign('lokasi',"Master Panitia");
			$this->smarty->display('index.html');
		}
	}
	
	function masterJalur(){
	
		$uri = $this->uri->segment(3);
		$uri2 = $this->uri->segment(4);
		$opt = $this->input->post('opt');
		
		if ($uri == "add" && $opt == "save"){
			// UPLOAD File menggunakan library upload CI
			$config['upload_path'] = './assets/upload/';
			$config['allowed_types'] = 'gif|jpg|png|pdf';
			$config['max_size']    = '10000';
			$config['max_width']  = '1024';
			$config['max_height']  = '768';
			
			$this->load->library('upload', $config);
			
			$this->upload->initialize($config);
			
			$this->upload->set_allowed_types('*');
			
			$data['upload_data'] = '';
			
			$uploadFile = $this->upload->do_upload('filesk');
			
			if (!$uploadFile){
				
				echo "<script>alert('Upload GAGAL!! Error :".$this->upload->display_errors()."');</script>";
			
			}else{
			
				$data = $this->upload->data();
				$file_name = $data['file_name'];				
				$full_path = $data['full_path'];
				$this->mmaster->mAddJalur($file_name, $full_path);
				echo "<script>alert('Data Berhasil Disimpan!');</script>";
			}
			
			
			
			redirect('/master/masterJalur','refresh');
			
		}else if ($uri == "edit" && $opt == "edit"){
		
			$val = $this->input->post("val");
			if (!$uri2){
			
				$qry = "SELECT * from adis_jalur_smb where kode = '$val';";
				$jalur = $this->db2->query($qry)->row();
				
				$this->smarty->assign('jalur',$jalur);	
				$this->smarty->display('master/formJalur.html');
			}else{
				
				//$uploadFile = $this->upload->do_upload('filesk');
				
				$kode = $this->input->post("kode");
				
				$config['upload_path'] = './assets/upload/';
				$config['allowed_types'] = 'gif|jpg|png|pdf';
				$config['max_size']    = '10000';
				$config['max_width']  = '1024';
				$config['max_height']  = '768';
				
				$this->load->library('upload', $config);
				
				$this->upload->initialize($config);
				
				$this->upload->set_allowed_types('*');
				
				$uploadFile = $this->upload->do_upload('filesk');
				
				$data['upload_data'] = '';
				
				$this->mmaster->mEditJalur($kode, $uploadFile);
				
				redirect('/master/masterJalur','refresh');
			}
			
		}else if ($uri == "delete" && $opt == "delete"){
			
			$id = $this->input->post("id");
			$fungsi = "masterJalur";
			$val = $this->input->post("val");
			$this->smarty->assign("value",$val);
			$this->smarty->assign("fungsi",$fungsi);
			$this->smarty->display("master/verifikasiWil.html");
			if ($id == "confirm"){
					$kode = $this->input->post("value");
					$this->db2->where("kode",$kode);
					$this->db2->update("adis_jalur_smb", array("erased"=>'1'));
					
					redirect('/master/masterJalur','refresh');
				}
			
		}else{
			
			$uriFile = $this->host.'assets/upload/';			
			
			$sql ="SELECT * FROM adis_jalur_smb WHERE erased = 0 ORDER BY kode";
			$sql = $this->db2->query($sql)->result();
			$site = "Jalur";
			$this->smarty->assign('site',$site);			
			$this->smarty->assign('sql',$sql);			
			$this->smarty->assign('uri',$uriFile);	
			$this->smarty->assign('lokasi',"Master Jalur");
			$this->smarty->display('index.html');
		}
	}
	
	function periode(){
		$uri = $this->uri->segment(3);
		$uri2 = $this->uri->segment(4);
		$opt = $this->input->post('opt');
		
		
		if ($uri == "add" && $opt == "save"){
			$kode = "";
			$this->mmaster->mPeriode($kode, $uri);
			
			redirect('/master/periode','refresh');
			
		}else if ($uri == "edit" && $opt == "edit"){
			
			$val = $this->input->post("val");
			if (!$uri2){			
				
				$qry = "SELECT * FROM adis_periode_master WHERE kode = '$val'";
				$periode = $this->db2->query($qry)->row();
				
				$this->smarty->assign('periode',$periode);	
				$this->smarty->display('master/formMasterPeriode.html');
			}else{
				
				$kode = $this->input->post("kode");
				$this->mmaster->mPeriode($kode,$uri);
				
				redirect('/master/periode','refresh');
			
			}
			
			
		}else if ($uri == "delete" && $opt == "delete"){
			
			$id = $this->input->post("id");
			
			if ($id == "confirm"){
					$kode = $this->input->post("value");
					$this->db2->where("kode",$kode);
					$this->db2->update("adis_periode_master", array("erased"=>'1',"status"=>'0'));
					
					redirect('/master/periode','refresh');
				}
			else if ($id == "nonaktif"){
					$kode = $this->input->post("val");
					$this->db2->where("kode",$kode);
					$this->db2->update("adis_periode_master", array("status"=>'0'));
				}
			else if ($id == "aktif"){
					$kode = $this->input->post("val");
					$this->db2->where("kode",$kode);
					$this->db2->update("adis_periode_master", array("status"=>'1'));
				}
			else {
				$fungsi = "periode";
				$val = $this->input->post("val");
				$this->smarty->assign("value",$val);
				$this->smarty->assign("fungsi",$fungsi);
				$this->smarty->display("master/verifikasiWil.html");
			
			}
			
		}
		else{		
			$sql ="SELECT * FROM adis_periode_master WHERE erased = 0 ORDER BY kode";
			$sql = $this->db2->query($sql)->result();
			$site = "Periode";
			$this->smarty->assign('site',$site);			
			$this->smarty->assign('sql',$sql);	
			$this->smarty->assign('lokasi',"Master Periode");
			$this->smarty->display('index.html');
		}
	}
	
	function masterPeriode(){
		$uri = $this->uri->segment(3);
		$uri2 = $this->uri->segment(4);
		$opt = $this->input->post('opt');
		
		$periodeMaster = "SELECT * FROM adis_periode_master WHERE erased = 0 and status = 1";
		$periodeMaster = $this->db2->query($periodeMaster)->result();
				
		$this->smarty->assign('masterPeriode',$periodeMaster);	
		
		if ($uri == "add" && $opt == "save"){
			
			$this->mmaster->mAddPeriode();
			
			redirect('/master/masterPeriode','refresh');
			
		}else if ($uri == "edit" && $opt == "edit"){
			
			$val = $this->input->post("val");
			if (!$uri2){			
				
				$qry = "SELECT p.kode as id, p.nama, p.tanggalMulai, p.tanggalSelesai, p.sesi, m.kode as idMaster,
						 m.tahun, m.jenjangType, 
						 p.status, m.semester, m.nama as periodeMaster
						 FROM adis_periode p
						 LEFT JOIN adis_periode_master m ON m.kode = p.idPeriodeMaster
						 WHERE p.kode = '$val' AND p.erased = 0 ORDER BY p.kode";
				$periode = $this->db2->query($qry)->row();
				
				$this->smarty->assign('periode',$periode);	
				$this->smarty->display('master/formPeriode.html');
			}else{
				
				$kode = $this->input->post("kode");
				$this->mmaster->mEditPeriode($kode);
				
				redirect('/master/masterPeriode','refresh');
			
			}
			
			
		}else if ($uri == "delete" && $opt == "delete"){
			
			$id = $this->input->post("id");
			
			if ($id == "confirm"){
					$kode = $this->input->post("value");
					$this->db2->where("kode",$kode);
					$this->db2->update("adis_periode", array("erased"=>'1',"status"=>'0'));
					
					redirect('/master/masterPeriode','refresh');
				}
			else if ($id == "nonaktif"){
					$kode = $this->input->post("val");
					$this->db2->where("kode",$kode);
					$this->db2->update("adis_periode", array("status"=>'0'));
				}
			else if ($id == "aktif"){
					$kode = $this->input->post("val");
					$this->db2->where("kode",$kode);
					$this->db2->update("adis_periode", array("status"=>'1'));
				}
			else {
				$fungsi = "masterPeriode";
				$val = $this->input->post("val");
				$this->smarty->assign("value",$val);
				$this->smarty->assign("fungsi",$fungsi);
				$this->smarty->display("master/verifikasiWil.html");
			
			}
			
		}
		else{		
			$sql ="SELECT p.kode as id, p.nama, p.tanggalMulai, p.tanggalSelesai, p.sesi, m.kode, m.tahun, m.jenjangType, 
			 p.status, m.semester
			 FROM adis_periode p
			 LEFT JOIN adis_periode_master m ON m.kode = p.idPeriodeMaster
			 WHERE p.erased = 0 ORDER BY p.kode";
			$sql = $this->db2->query($sql)->result();
			$site = "MasterPeriode";
			$this->smarty->assign('site',$site);			
			$this->smarty->assign('sql',$sql);	
			$this->smarty->assign('lokasi',"Master Periode Per Gelombang");
			$this->smarty->display('index.html');
		}
	}
	
	function mDaftarUlang(){
		$uri = $this->uri->segment(3);
		$uri2 = $this->uri->segment(4);
		$opt = $this->input->post('opt');
		$jalur = $this->input->post('jalur');
			
		
		if ($uri == "add" && $opt == "save"){
			
			$this->mmaster->mAddPembayaranDU();
			
			redirect('/master/masterUM','refresh');
			
		}else if ($uri == "edit" && $opt == "edit"){
			
			$val = $this->input->post("val");
			if (!$uri2){			
				
				$qry = "SELECT p.*, pr.singkatan as alias_prodi, pr.nama as prodi, p.kode as code, c.*, c.kode as kodecil
				FROM adis_pembayaran p
				LEFT JOIN adis_prodi pr ON p.prodiTipe = pr.kode
				LEFT JOIN adis_cicilan c ON c.id_pembayaran = p.kode
				WHERE p.kode = '$val'";
				$pembayaran = $this->db2->query($qry)->row();
				$this->smarty->assign('pembayaran',$pembayaran);	
				
				if (strpos($val, '69') !== FALSE){
					if (strpos($val, 'KP') !== FALSE){						
						$this->smarty->display('master/formEditAngsurKP.html');
					}else{
						$this->smarty->display('master/formPembayaranAngsur.html');
					}
				}else{
					if (strpos($val, 'KP') !== FALSE){
						$this->smarty->display('master/formEditLunasKP.html');
					}else{
						$this->smarty->display('master/formPembayaran.html');
					}
				}
			}else{
				
				$kode = $this->input->post("kode");
				$this->mmaster->mEditPembayaran($kode);
				
				redirect('/master/masterUM','refresh');
			
			}
			
			
		}else if ($uri == "delete" && $opt == "delete"){
			
			$id = $this->input->post("id");
			
			if ($id == "confirm"){
					$kode = $this->input->post("value");
					$this->db2->where("kode",$kode);
					$this->db2->update("adis_pembayaran", array("erased"=>'1',"status"=>'0'));
					
					redirect('/master/mDaftarUlang','refresh');
				}
			else if ($id == "nonaktif"){
					$kode = $this->input->post("val");
					$this->db2->where("kode",$kode);
					$this->db2->update("adis_pembayaran", array("status"=>'0'));
				}
			else if ($id == "aktif"){
					$kode = $this->input->post("val");
					$this->db2->where("kode",$kode);
					$this->db2->update("adis_pembayaran", array("status"=>'1'));
				}
			else {
				$fungsi = "mDaftarUlang";
				$val = $this->input->post("val");
				$this->smarty->assign("value",$val);
				$this->smarty->assign("fungsi",$fungsi);
				$this->smarty->display("master/verifikasiWil.html");
			
			}
			
		}else if($uri == 'table'){
			$jlr = "SELECT * FROM adis_jalur_smb WHERE erased = 0";
			$jlr = $this->db->query($jlr)->result();
			
			switch ($jalur) {
				case 'KP':
					$sql = $this->db2->query("SELECT p.*, pr.singkatan as prodi
						FROM adis_pembayaran_paralel p
						LEFT JOIN adis_prodi pr ON p.prodiTipe = pr.kode
						WHERE p.erased = 0 AND p.pembayaran = 1 ORDER BY kode")->result();
					$sql2 = $this->db2->query("SELECT p.*, p.kode as code, pr.singkatan as prodi
						FROM adis_pembayaran_paralel p
						LEFT JOIN adis_prodi pr ON p.prodiTipe = pr.kode
						WHERE p.erased = 0 AND p.pembayaran = 69 ORDER BY p.kode")->result();
						
						$this->smarty->assign('sql',$sql);				
						$this->smarty->assign('sql2',$sql2);
						$this->smarty->assign('jlr',$jlr);
						
						$this->smarty->display('master/kontenPembayaranParalel.html');
				break;
				case '01':
					$sql = $this->db2->query("SELECT p.*, pr.singkatan as prodi
						FROM adis_pembayaran p
						LEFT JOIN adis_prodi pr ON p.prodiTipe = pr.kode
						WHERE p.erased = 0 AND p.pembayaran = 1 
						AND p.jalur = '$jalur' ORDER BY kode")->result();
					$sql2 = $this->db2->query("SELECT p.*, p.kode as code, pr.singkatan as prodi, c.*
						FROM adis_pembayaran p
						LEFT JOIN adis_prodi pr ON p.prodiTipe = pr.kode
						LEFT JOIN adis_cicilan c ON c.id_pembayaran = p.kode
						WHERE p.erased = 0 AND p.pembayaran = 69
						AND p.jalur = '$jalur' ORDER BY p.kode")->result();	
						
						$this->smarty->assign('sql',$sql);				
						$this->smarty->assign('sql2',$sql2);
						$this->smarty->assign('jlr',$jlr);
						
						$this->smarty->display('master/kontenPembayaran.html');
				break;

			}
			
					
		
		}else{
		
			$sql = $this->db2->query("SELECT * FROM adis_pembayaran WHERE erased = 0 AND pembayaran = 1 ORDER BY kode")->result();
			$sql2 = $this->db2->query("SELECT * FROM adis_pembayaran WHERE erased = 0 AND pembayaran = 69 ORDER BY kode")->result();
			
			$site = "Pembayaran";
			$this->smarty->assign('site',$site);			
			$this->smarty->assign('sql',$sql);				
			$this->smarty->assign('sql2',$sql2);
			$this->smarty->assign('lokasi',"Master Uang Masuk");
			$this->smarty->display('index.html');
		}
	}
	
	function masterUM(){
		$sql = "SELECT * FROM adis_jalur_smb WHERE erased = 0";
		$sql = $this->db->query($sql)->result();
		
		$site = "SelectJalur";
		$this->smarty->assign('site',$site);			
		$this->smarty->assign('lokasi',"Master Uang Masuk");
		$this->smarty->assign('jalur', $sql);
		$this->smarty->display('index.html');
	}

	function mJalurUM(){
		$kode = $this->input->post('val');
		$prodi = $this->input->post('prodi');
		$uri = $this->uri->segment(3);
		if ($uri == 'jalurMasuk' && $kode != ''){			
			$prodi = $this->db->query("SELECT * FROM adis_prodi WHERE erased = 0")->result();
			$this->smarty->assign('prodi',$prodi);
			
			if ($kode == 'KP'){
				$this->smarty->display('master/formPembayaranParalel.html');
			}else{ 
				$this->smarty->display('master/formPembayaranReguler.html');
			}
		}else if ($uri == 'metodBayar' && $kode != ''){
			$this->smarty->assign('prodi',$prodi);
			if ($kode == '1'){
				$this->smarty->display('master/formMetodeLunas.html');
			}else{ 
				$this->smarty->assign('prodi',$prodi);
				$this->smarty->display('master/formMetodeAngsuran.html');
			}
		}else if ($uri == 'metodReguler' && $kode != ''){
			$this->smarty->assign('metod',$kode);
			$this->smarty->display('master/formLunasReguler.html');
		}
	}
	
	
	function masterPegawai(){
		$this->mmaster->mpegawai();
		$this->mmaster->mjabatan();
		$this->mmaster->mdepart();
		$this->smarty->display('index.html');	
		$this->smarty->display("master/tbl_staff.html");
	}
	
	/*function masterJabatan(){
		$this->mmaster->mjabatan();
		$this->smarty->display('index.html');	
		$this->smarty->display("master/tbl_jabatan.html");
	} */
	
	function masterDepart(){
		$this->mmaster->mdepart();
		$this->smarty->display('index.html');	
		$this->smarty->display("master/tbl_departemen.html");
	}
	
	function addPegawai(){	
		$uri = $this->uri->segment(3);
		$nama = $this->input->post('nama');
		if ($uri == "save" && $nama != ""){
			$this->mmaster->mAddPeg();
		}
		$this->masterPegawai();
	}
	
	function delPegawai(){
		$uri = $this->uri->segment(3);
		$id = $this->input->post("id");
		if ($uri == "delete" && $id != ""){
			$this->mmaster->mDelPeg($id);
			$this->masterPegawai();
		}
		$this->masterPegawai();
	}
	
	function editMaster(){
		$uri = $this->uri->segment(3);
		$nip = $this->input->post("id");
		if ($uri == "form"){
			$this->mmaster->mEditPeg($nip);			
			$this->smarty->display("master/editStaffForm.html");
		}else{
						
			//$this->db->query("UPDATE tbl_master_personal SET nama="..", inisial="..", alamat_skr="..", personal_NIP=".." email="..", telp="..", mobile_phone1=".." WHERE ");
			
			$this->db->where('id', $nip);
			$this->db->update("tbl_master_personal", array(
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
		$this->masterPegawai();
		}
	}
	
	function ubahUser(){
		$this->load->library('encrypt');
		$uri = $this->uri->segment(3);
		$pid = $this->input->post("id");
		$nama = $this->input->post("nama");
		
		$sql = "SELECT u.id as user_id, p.id as peg_id, p.nama as nama, username, `password` as pass, tbl_usrm_level_id as level_id, 
				u.id_pegawai as id_pgw, aktif, l.id as level_id, l.level_name  
				FROM tbl_usrm_users u
				INNER JOIN tbl_master_personal p ON u.id_pegawai = p.id
				INNER JOIN tbl_usrm_level l ON l.id = u.tbl_usrm_level_id
				WHERE p.id =".$pid." GROUP BY user_id";
		
		$query = $this->db->query($sql);
		$qry = $query->row();
		$num = $query->num_rows();
		if ($num>0){
			$meth = "edit";
			$pass = $this->encrypt->decode($qry->pass);
			//$pass = $qry->pass;
			$this->smarty->assign("pass",$pass);
			$this->smarty->assign("meth",$meth);			
			$this->smarty->assign("user", $qry);
		}
		else{
			$meth = "add";
			$this->smarty->assign("meth",$meth);
		}
		$this->mmaster->mLevel();	
		$this->smarty->assign("pid",$pid);
		$this->smarty->assign("nama",$nama);
		$this->smarty->display("master/form_user.html");
		
		
	}
	
	function confUser(){
		$this->load->library('encrypt');
		$id_user = "";
		$id_user = $this->input->post("id");
		$nama = $this->input->post("nama");
		
		$oper = $this->uri->segment(3);
		$pid = $this->input->post("pid");
		
		$tombol = $this->input->post("button");
		
		$row["pass"] = $this->input->post("pass");
		$pass = $this->encrypt->encode($row["pass"]);
		
		if($tombol == "save" && $id_user == ""){
			$this->mmaster->mConfUser($id_user, $pass, $pid, $tombol, $nama);
		}else{			
			$id_user = $this->input->post("id");
			$this->mmaster->mConfUser($id_user, $pass, $pid, $tombol, $nama);
		}
		$this->masterPegawai();
	}
	
	function masterCuti(){
		$uri = $this->uri->segment(3);
		$id = $this->input->post("id");
		if($uri == "edit" && $id != ""){
			$this->mmaster->mEditCuti($id);
			$this->smarty->display("master/edit_cutiBer.html");
		}
		else{
			$this->mmaster->mCutiBer();
			$this->smarty->display('index.html');
			$this->smarty->display("master/master_cuti.html");
		}
	}
	
	function addCutiBer(){
		$nama = $this->auth['name'];
		$save = $this->input->post("save");
		
		$date1 = $this->input->post("tgl_cuti");
		$date2 = $this->input->post("tgl_selesai_cuti");		
		
		$diff = abs(strtotime($date2) - strtotime($date1));
		
		$years = floor($diff / (365*60*60*24));
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
		$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24)) + 1;
		
		//printf("%d years, %d months, %d days\n", $years, $months, $days);
		
		if ($save != ""){
			$this->mmaster->mAddCutiBer($nama, $days);
		}
		
		redirect('/master/masterCuti','refresh');
	}
	function editCuti(){
		$uri = $this->uri->segment(3);
		$save = $this->input->post("save");
		$id = $this->input->post("id");
		
		$nama = $this->auth['name'];
		
		$date1 = $this->input->post("tgl_cuti");
		$date2 = $this->input->post("tgl_selesai_cuti");		
		
		$diff = abs(strtotime($date2) - strtotime($date1));
		
		$years = floor($diff / (365*60*60*24));
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
		$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24)) + 1;
		
		
		if($save == "save"){
			$this->mmaster->mUpdCuti($nama, $days, $id);
		}else{
			$this->masterCuti();
		}
		$this->masterCuti();
	}
	
	function delCuti(){
		$uri = $this->uri->segment(3);
		$id = $this->input->post("id");
		if ($uri == "delete" && $id != ""){
			$this->mmaster->mDelCuti($id);
		}else{
		//$this->masterCuti();
		}
	}
	
	function master_periode_cuti(){
		$uri = $this->uri->segment(3);
		$id = $this->input->post("id");
		if($uri == "edit" && $id != ""){
			$this->mmaster->mEditCuti($id);
			$this->smarty->display("master/edit_cutiBer.html");
		}
		else{
			$this->mmaster->mPeriodeCuti();
			$this->smarty->display('index.html');
			$this->smarty->display("master/tbl_period_kerja.html");
		}
	}
	
	function addPeriode(){
		$nama = $this->auth['name'];
		$save = $this->input->post("save");
		
		$periode = $this->input->post("periode");
		$date1 = $this->input->post("tgl_cuti");
		$date2 = $this->input->post("tgl_selesai_cuti");		
		
		if ($save != ""){
			$this->mmaster->mAddPeriode($nama, $date1, $date2, $periode);
		}
		
		redirect('/master/master_periode_cuti','refresh');
	}
	
	function selectKabKota(){
		$propinsi = $this->input->post("propinsi");
		
		$prop = $this->db2->get_where('adis_wil', array('parent'=>$propinsi))->result();		
		$view = '	{foreach name=lope from=$prop item=row}
						<option value="{$row->kode}">{$row->nama}</option>
					{/foreach}';
		
		$this->smarty->assign('prop',$prop);
		$this->smarty->display('string:'.$view);
	}
	
	function selectKodePos(){
		$kab = $this->input->post("kabkota");
		
		$kab = $this->db2->query("SELECT * FROM adis_kodepos WHERE kabupatenkota = '".$kab."' ORDER BY kodepos ASC;")->result();		
		$view = '	{foreach name=lope from=$kab item=row}
						<option value="{$row->kode}">{$row->kodepos}</option>
					{/foreach}';
		
		$this->smarty->assign('kab',$kab);
		$this->smarty->display('string:'.$view);
	}
	
	
}
