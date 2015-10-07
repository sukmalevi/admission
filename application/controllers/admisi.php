<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admisi extends CUTI_Controller{
	function __construct(){
		parent::__construct();	
		//setlocale (LC_TIME, 'id_ID');
		setlocale (LC_TIME, 'INDONESIA');
		$this->auth = unserialize(base64_decode($this->session->userdata('cuti_parmad')));
		$this->host	= $this->config->item('base_url');
		
		if(! $this->auth) {header("Location: " . $this->host);}	
		
		$this->db2 = $this->load->database('second', TRUE); 
		
		$modul = "admisi/";
		$this->smarty->assign('modul',$modul);
		$this->smarty->assign('host',$this->host);
		$this->load->model('madmisi');
		$this->db2->query("SET lc_time_names = 'id_ID'");
		
		$nama = $this->auth['name'];
		$this->smarty->assign('nama',$nama);
		
		if ($this->auth['level'] != "99" && $this->auth['level'] != "44"){ redirect('/home','refresh');}
	}
	
	function index() {
		
		}
		
	function admisiBukaSeleksi(){
		$periode = $this->input->post("periode");
		$jalur = $this->input->post('jalur');
		$uri = $this->uri->segment(3);
		
		if (!$periode && !$uri){
			$this->madmisi->mSelectPeriode();
			$this->madmisi->mSelectProdi();
			$this->madmisi->mSelectJalur();
			
			$site = "Jadwal";			
			
			$this->smarty->assign('site',$site);
			$this->smarty->assign('lokasi',"Buka ");	
			$this->smarty->display('index.html');
		
		}else if ($periode != "" && $uri == "table"){
			
			$this->madmisi->mBukaSeleksi($periode, $jalur);
			
			$period = $this->db2->query("SELECT nama,kode from adis_periode WHERE kode = '$periode'")->row();
			$route = $this->db2->query("SELECT nama, kode from adis_jalur_smb WHERE kode = '$jalur'")->row();
			
			$this->smarty->assign('periode',$period);
			$this->smarty->assign('jalur',$route);
			$this->smarty->display('admisi/tblBuka.html');
		} 
	
		
			
	}
	
	function admisiOptBuka(){
		$uri = $this->uri->segment(3);
		$uri2 = $this->uri->segment(4);
		$opt = $this->input->post("opt");
		$kode = $this->input->post("val");
		
		if ($uri == "add" && $opt =="add"){
			
			$this->madmisi->mAddBuka();
			
			redirect('/admisi/admisiBukaSeleksi','refresh');
		
		}else if ($uri == "edit" && $opt =="edit"){
			
			if (!$uri2){
				$this->madmisi->mSelectPeriode();
				$this->madmisi->mSelectProdi();
				$this->madmisi->mSelectJalur();
				
				$this->madmisi->mDataBuka($kode);
				
				$this->smarty->display('admisi/formBuka.html');	
			}else{
				
				$kode = $this->input->post("kode");
				$this->madmisi->mEditBuka($kode);
				
				redirect('/admisi/admisiBukaSeleksi','refresh');
			}
		
		}else if ($uri == "delete" && $opt =="delete"){
			$id = $this->input->post("id");
			if($id == "aktif"){
				$value = $this->input->post("val");
				$this->db2->where("kode",$value);
				$this->db2->update("adis_buka_smb", array("stsBuka"=>'0'));
				
			}else if($id == "nonaktif"){
				$value = $this->input->post("val");
				$this->db2->where("kode",$value);
				$this->db2->update("adis_buka_smb", array("stsBuka"=>'1'));
				
			}else if($id == "confirm"){
				$value = $this->input->post("value");
				$this->db2->where("kode",$value);
				$this->db2->update("adis_buka_smb", array("erased"=>'1'));
					
				redirect('/admisi/admisiBukaSeleksi','refresh');
			}else{
				$fungsi = "admisiOptBuka";
				$val = $this->input->post("val");
				$this->smarty->assign("value",$val);
				$this->smarty->assign("fungsi",$fungsi);
				$this->smarty->display("admisi/konfirmasiDel.html");
			}
		}
	}
	
	function admisiPeriode(){
		$sql ="SELECT * FROM adis_periode WHERE status = 1 AND erased = 0";
		$sql = $this->db2->query($sql)->result();		
		$site = "Event";			
		$this->smarty->assign('site',$site);	
		$this->smarty->assign('sql',$sql);	
		$this->smarty->assign('lokasi',"Periode ");
		$this->smarty->display('index.html');	
	}
	
	function admisiJalur(){		
		$periode = $this->input->post('periode');
		$function = $this->input->post('fungsi');
		
		$sql ="SELECT * FROM adis_jalur_smb WHERE erased = 0";
		$sql = $this->db2->query($sql)->result();
		
		$this->smarty->assign('fungsi',$function);
		$this->smarty->assign('periode',$periode);	
		$this->smarty->assign('jalur',$sql);	
		$this->smarty->display('admisi/selectJalur.html');	
	}
	
	function admisiJadwalSeleksi(){
		$uri = $this->uri->segment(3);
		$uri2 = $this->uri->segment(4);
		$periode = $this->input->post('periode');
		$jalur = $this->input->post('jalur');	
		$opt = $this->input->post('opt');	
		
		$this->madmisi->mSelectPeriode();
		$this->madmisi->mSelectJalur();
		$this->madmisi->mSelectRuang();
		$this->madmisi->mSelectPetugas();
		
		
		if ($uri == "table" && $periode != ""){
			$this->madmisi->mDataEvent($jalur, $periode);
			$this->smarty->display('admisi/tblJadwal.html');	
		}else if ($uri == "add" && $opt == "add"){
			
			$this->madmisi->mAddEvent();
			redirect('/admisi/admisiJadwalSeleksi','refresh');
			
		}else if ($uri == "edit" && $opt == "edit"){			
			$kode = $this->input->post('val');	
		
			if (!$uri2){
				$this->madmisi->mSelectEvent($kode);
				
				$this->smarty->display("admisi/formEvent.html");
				
			}else{
				$this->madmisi->mEditEvent();
				
				redirect('/admisi/admisiJadwalSeleksi','refresh');
			}
		}else if ($uri == "delete" && $opt == "delete"){			
			$id = $this->input->post("id");
			if($id == "aktif"){
				$value = $this->input->post("val");
				$this->db2->where("kode",$value);
				$this->db2->update("adis_event_smb", array("statusJadwal"=>'0'));
				
			}else if($id == "nonaktif"){
				$value = $this->input->post("val");
				$this->db2->where("kode",$value);
				$this->db2->update("adis_event_smb", array("statusJadwal"=>'1'));
				
			}else if($id == "confirm"){
				$value = $this->input->post("value");
				$this->db2->where("kode",$value);
				$this->db2->update("adis_event_smb", array("erased"=>'1'));
					
				redirect('/admisi/admisiJadwalSeleksi','refresh');
			}else{
				$fungsi = "admisiJadwalSeleksi";
				$val = $this->input->post("val");
				$this->smarty->assign("value",$val);
				$this->smarty->assign("fungsi",$fungsi);
				$this->smarty->display("admisi/konfirmasiDel.html");
			}
		}else{
			$sql ="SELECT * FROM adis_periode WHERE status = 1 AND erased = 0";
			$sql = $this->db2->query($sql)->result();		
			$site = "Event";			
			$this->smarty->assign('site',$site);	
			$this->smarty->assign('sql',$sql);	
			$this->smarty->assign('lokasi',"Jadwal Seleksi ");
			$this->smarty->display('index.html');
		}
	}
	
	function absen(){
		$kode = $this->uri->segment(3);
		$opt = $this->uri->segment(4);
		
		if ($kode != ""){
			$this->madmisi->mAbsen($kode);
			$site = "Absen";			
			$this->smarty->assign('site',$site);	
			$this->smarty->assign('lokasi',"Absensi");	
			$this->smarty->display('index.html');
		}
		
	}
	
	function mahasiswa(){
		$kode = $this->uri->segment(3);
		$prodi = $this->uri->segment(4);
		$opt = $this->uri->segment(5);
		
		$prodi = $this->db2->query("SELECT nama FROM adis_prodi WHERE kode = '$prodi'")->row();
		
		if ($kode != ""){
			$ps  = "";
			//$kodeSmb =  implode(".",array($kode,$prodi));
			$this->madmisi->mMahasiswa($kode, $ps);
			$site = "Mahasiswa";			
			$this->smarty->assign('site',$site);
			$this->smarty->assign('prodi',$prodi);
			$this->smarty->assign('kodeSmb', $kode);
			$this->smarty->assign('lokasi',"Calon Mahasiswa");	
			$this->smarty->display('index.html');
		}
		
	}
	
	function exportToExcel(){
		$kodeSmb = $this->uri->segment(3);
		$prodi = $this->uri->segment(4);
		$sql = $this->madmisi->mMahasiswa($kodeSmb, $prodi);
		$this->export->to_excel($sql, 'Calon_Mahasiswa_Prodi_'.$prodi.''); 
	}
	
}
