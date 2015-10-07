<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cuti extends CUTI_Controller{
	function __construct(){
		parent::__construct();
		
		$this->auth = unserialize(base64_decode($this->session->userdata('cuti_parmad')));	
		if(! $this->auth) {header("Location: " . $this->host);}	
		
		$this->load->model('mcuti');
		$modul = "cuti/";
		
		$nama = $this->auth['name'];
		$this->smarty->assign('nama',$nama);
		
		$this->smarty->assign('modul',$modul);		
		
	}
	
	function index(){
	
	}
	
	function formCuti(){
		$id = $this->auth['id_pegawai'];
		$this->mcuti->mpegawai($id);
		$this->mcuti->mjCuti();
		
		$this->smarty->display('index.html');	
		$this->smarty->display("cuti/form_cuti.html");
	}
	
	function addCuti(){
		$uri = $this->uri->segment(3);
		$meth = $this->input->post("save");
		$id_peg = $this->input->post("id_peg");
		$tgl_mulai = $this->input->post("tgl_mulai");
		$sql = "SELECT tgl_cuti, isApproved FROM tbl_trans_cuti WHERE tgl_cuti='".$tgl_mulai."' AND id_pegawai =".$id_peg."";
		$cekRow = $this->db->query($sql);
		$num = $cekRow->num_rows();
		
		if ($uri == "save" && $meth == "save"){
			if ($num>0){
				echo "<script>alert('Tanggal Yang Anda Masukkan Salah!');</script>";
				$this->formCuti();
			}else{
				$this->mcuti->mAddCuti($id_peg);
				$this->pageCuti();
			}
		}
		
	}
	
	function delCuti(){
		$uri = $this->uri->segment(3);
		$id = $this->input->post("id");
		if ($uri == "delete" && $id != ""){
			$this->mcuti->mDelCuti($id);
		}else{
		$this->tabelCuti();
		}
	}
	
	function editCuti(){
		$id_peg = $this->auth['id_pegawai'];
		$id_cuti = $this->input->post("id");
		$uri = $this->uri->segment(3);
		if ($uri == "update" && $id_cuti != ""){
			$this->mcuti->mUpCuti($id_peg, $id_cuti);			
			redirect('/cuti/tabelCuti','refresh');
		}else{
			$this->mcuti->mEditCuti($id_peg, $id_cuti);
			$this->smarty->display("cuti/form_edCuti.html");
		}
	}
	
	function pageCuti(){
		$this->mcuti->mPageCuti();
		$this->smarty->display('index.html');	
		$this->smarty->display("cuti/form_confirm.html");
	}
	
	function tabelCuti(){
		$id = $this->auth['id_pegawai'];
		$nama = $this->auth['name'];
		$this->mcuti->mTabelCuti($id);
		$this->smarty->display('index.html');	
		$this->smarty->display("cuti/tabel_cuti.html");	
		$this->smarty->assign("nama", $nama);
	}	
	
	function tblPengajuanCuti(){
		
		$offset=$this->uri->segment(3);
		// if ($offset == ""){
			// redirect ('cuti/tblPengajuanCuti/0');
		// }
		$this->load->library('pagination');
		$id = $this->auth['id_pegawai'];
		$this->mcuti->mTblPengajuanCuti($id);
		
		$config['base_url'] = $this->host.'cuti/tblPengajuanCuti/';
		$config['total_rows'] = $this->mcuti->mTblPengajuanCuti($id);
		$config['per_page'] = '5';
		$config['first_page'] = 'Awal';
		$config['last_page'] = 'Akhir';
		$config['next_page'] = '&laquo;';
		$config['prev_page'] = '&raquo;';
		$config["uri_segment"] = '3';
		$config["first_url"] = $this->host.'cuti/tblPengajuanCuti/1';
		
		
		$this->pagination->initialize($config);
		
		$data = $this->pagination->create_links();
		$max = $config['per_page'];
		$this->mcuti->mTotalRows($id, $max, $offset);
		
		$this->smarty->assign('page', $data);
		$this->smarty->display('index.html');	
		$this->smarty->display("cuti/tbl_pengajuan_cuti.html");
	}
	
	function statusPengajuan(){
		$id = $this->auth['id_pegawai'];
		$id_cuti = $this->input->post("id");
		$this->mcuti->mStatusCuti($id, $id_cuti);
		$this->smarty->display("cuti/status_pengajuan.html");
	}
	
	function page($id, $jml){
		
		$config['base_url'] = $this->host.'cuti/tblPengajuanCuti';
		$config['total_rows'] = $jml;
		$config['per_page'] = '5';
		$config['first_page'] = 'Awal';
		$config['last_page'] = 'Akhir';
		$config['next_page'] = '&laquo;';
		$config['prev_page'] = '&raquo;';

		//inisialisasi config
		 $this->pagination->initialize($config);
		 $max = $config['per_page'];
		//buat pagination
		 $page = $this->pagination->create_links();		 
		 
		 $this->mcuti->mTblPengajuanCuti($id, $max, $jml);
		 
		 $this->smarty->assign('page', $page);
	}
}