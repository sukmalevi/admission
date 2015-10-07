<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CUTI_Controller{
	function __construct(){
		parent::__construct();		
		$this->auth = unserialize(base64_decode($this->session->userdata('cuti_parmad')));
		$this->host	= $this->config->item('base_url');
		
		$this->db2 = $this->load->database('second', TRUE); 
		
		$site = "";
		$modul = "";
		$this->smarty->assign('modul',$modul);
		$this->smarty->assign('site',$site);
		$this->smarty->assign('lokasi',"Dashboard");
		$this->smarty->assign('host',$this->host);
		
		$nama = $this->auth['name'];
		$this->smarty->assign('nama',$nama);
		
		$this->notification();
		$this->activity();
		
		$this->smarty->display('index.html');
	}
	
	function index() {
		
		}
	
	function notification(){
		$notif = $this->db2->query("SELECT t.nama as namaAct, a.kode as kodeActivity, a.id_activity, a.created_date,
				SUBSTRING(MONTHNAME(a.created_date),1,3) as bulan, DAYNAME(a.created_date) as hari, DAY(a.created_date) as tgl,
				HOUR(a.created_date) as jam, MINUTE(a.created_date) as menit
				FROM adis_activity a
				INNER JOIN adis_type t ON t.kode = a.id_activity
				WHERE a.status_activity = '0'
				ORDER BY a.created_date DESC
				");
		$notifRes = $notif->result();
		$notifNum = $notif->num_rows();
		$this->smarty->assign('notif',$notifRes);
		$this->smarty->assign('notifNum',$notifNum);
	}
	
	function activity(){
		$notif = $this->db2->query("SELECT t.nama as namaAct, a.kode, a.id_activity, a.created_date,
				SUBSTRING(MONTHNAME(a.created_date),1,3) as bulan, DAYNAME(a.created_date) as hari, DAY(a.created_date) as tgl,
				HOUR(a.created_date) as jam, MINUTE(a.created_date) as menit
				FROM adis_activity a
				INNER JOIN adis_type t ON t.kode = a.id_activity
				ORDER BY a.created_date DESC
				");
		$notifRes = $notif->result();
		$notifNum = $notif->num_rows();
		$this->smarty->assign('aktifiti',$notifRes);
		$this->smarty->assign('aktifitiNum',$notifNum);
	}
	
	function clickNotif(){
		$idActivity = $this->uri->segment(3);
		$kodeActivity = $this->uri->segment(4);
		
		$this->db2->where('kode', $kodeActivity);
		$this->db2->update('adis_activity',array('status_activity'=>1));
		
		if ($idActivity == '13.1'){
			redirect ('/smb/smbCalon', 'location');
		}else if ($idActivity == '13.2'){
			redirect ('/smb/smbCalon', 'location');
		}else if ($idActivity == '13.3'){
			redirect ('/smb/smbPay', 'location');
		}else if ($idActivity == '13.4'){
			redirect ('/smb/smbCalon', 'location');
		}else if ($idActivity == '13.5'){
			redirect ('/smb/smbSeleksi', 'location');
		}else if ($idActivity == '13.9'){
			redirect ('/smb/smbDaftarUlang', 'location');
		}
		
	}
}