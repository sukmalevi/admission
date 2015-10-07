<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Mcuti extends CUTI_Model{
	
	function __construct(){
		parent::__construct();
	}
	
	function Mcuti(){
		parent::__construct();
	}
	
	function mpegawai($id){
		
		//$sql = "SELECT * FROM tbl_master_personal ORDER BY id";
		$sql ="SELECT p.id, nama, inisial, personal_NIP, alamat_skr, email, phone, mobile_phone1,
			d.acadept_uid as d_id, d.description as depart, j.acajbt_uid as j_id, j.description as jabatan,
			d.id_direktorat as dir_id, dir.id_rektorat as rek_id
			FROM tbl_master_personal p
			INNER JOIN tbl_master_department d ON p.id_depart = d.acadept_uid
			INNER JOIN tbl_master_jabatan j ON p.id_jabatan = j.acajbt_uid
			INNER JOIN tbl_direktorat dir ON d.id_direktorat = dir.id
			INNER JOIN tbl_rektorat r ON dir.id_rektorat = r.id
			WHERE p.id = ".$id." GROUP BY p.id";
		$sql = $this->db->query($sql)->row();
		$this->smarty->assign('pegawai',$sql);
		
		//$sql->result() as $hasil;
		$departemenID = $sql->d_id;
		$jabatanID = $sql->j_id;
		$direktoratID = $sql->dir_id;
		$rektoratID = $sql->rek_id;
		//$idAtasan = $pegawai=>"id_depart";
		$this->mAtasan($departemenID, $jabatanID, $direktoratID, $rektoratID);
		$this->mTahunKerja();
		
		return $sql;
	}
	
	function mAtasan ($departemenID, $jabatanID, $direktoratID, $rektoratID){
		
		$sql2 = "SELECT p.id as id_karyawan, nama, inisial, personal_NIP, alamat_skr, email, phone, mobile_phone1,
			d.acadept_uid as d_id, d.description as depart, j.acajbt_uid as j_id, j.description as jabatan 
			FROM tbl_master_personal p
			INNER JOIN tbl_master_department d ON p.id_depart = d.acadept_uid
			INNER JOIN tbl_master_jabatan j ON p.id_jabatan = j.acajbt_uid
			INNER JOIN tbl_direktorat dir ON d.id_direktorat = dir.id
			INNER JOIN tbl_rektorat r ON dir.id_rektorat = r.id
			WHERE dir.id_rektorat = ".$rektoratID."
			AND 
			IF( 
			(".$jabatanID." <= 8 OR  ".$jabatanID." <= 7 OR ".$jabatanID." <= 6), 
			 p.id_jabatan < ".$jabatanID." AND d.id_direktorat = ".$direktoratID.", 
			 p.id_depart = ".$departemenID." AND p.id_jabatan < ".$jabatanID."
			) ORDER BY id_jabatan ASC";
		
		$sql2 = $this->db->query($sql2)->result();
		$this->smarty->assign("atasan", $sql2);
	}
	
	function mTahunKerja(){
		$sql = "SELECT * FROM tbl_periode_cuti ORDER BY periode_cuti DESC";
		$tahun = $this->db->query($sql)->row();
		$this->smarty->assign("tahun", $tahun);
	}
	
	function mjCuti(){
		$sql = "SELECT * FROM tbl_jenis_cuti ORDER BY id_jenis_cuti";
		$query = $this->db->query($sql)->result();
		$this->smarty->assign("jcuti", $query);
	}
	
	function mAddCuti($id_peg){
		$date1 = $this->input->post("tgl_mulai");
		$date2 = $this->input->post("tgl_selesai");
		
		$diff = abs(strtotime($date2) - strtotime($date1));

		$years = floor($diff / (365*60*60*24));
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
		$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
		
		$jd=gregoriantojd($months,$days,$years);
		echo jddayofweek($jd,1);

		//printf("%d years, %d months, %d days\n", $years, $months, $days);
		
		
		
		$this->db->insert("tbl_trans_cuti", array(
				"id_pegawai"=>$id_peg,
				"id_jenis_cuti"=>$this->input->post("jenis_cuti"),
				"tgl_cuti"=>$this->input->post("tgl_mulai"),
				"tgl_selesai_cuti"=>$this->input->post("tgl_selesai"),
				"tgl_kembali"=>$this->input->post("tgl_kembali"),
				"alasan"=>$this->input->post("alasan"),
				"alamat_cuti"=>$this->input->post("al_cuti"),
				"jumlah_cuti"=>$this->input->post("total_cuti"),
				"isConfirm"=>0,"isApproved"=>0,
				"atasan_id"=>$this->input->post("atasan"),
				"periode_id"=>$this->input->post("periode"),
				"date_created"=>date("Y-m-d")
			));
		$this->db->query("SET @id_cuti_last = LAST_INSERT_ID()");
	}
	
	function mPageCuti(){
		$id_last = $this->db->query("SELECT LAST_INSERT_ID()");
		$sql = "SELECT c.id_cuti, c.id_pegawai, c.id_jenis_cuti, c.tgl_cuti,
				c.tgl_selesai_cuti, c.tgl_kembali, c.alasan, c.alamat_cuti, c.isConfirm, c.isApproved, c.approved_by, 
				p.nama as nama, p.id_depart, p.id_jabatan, j.jenis_cuti as jenis_cuti,
				d.description as depart, b.description as jabatan
				FROM tbl_trans_cuti c 				
				INNER JOIN tbl_jenis_cuti j ON c.id_jenis_cuti = j.id_jenis_cuti
				INNER JOIN tbl_master_personal p ON p.id = c.id_pegawai
				INNER JOIN tbl_master_jabatan b ON p.id_jabatan = b.acajbt_uid
				INNER JOIN tbl_master_department d ON p.id_depart = d.acadept_uid
				WHERE id_cuti=@id_cuti_last LIMIT 1";
		$db = $this->db->query($sql)->row();
		$this->smarty->assign("cuti", $db);
	}
	
	function mTabelCuti($id){
		$sql ="SELECT id_cuti, id_pegawai, t.id_jenis_cuti, tgl_cuti, tgl_selesai_cuti, jumlah_cuti,
		j.jenis_cuti, alasan, isApproved, isConfirm, approved_by, p.nama as nama
		FROM tbl_trans_cuti t		
		INNER JOIN tbl_jenis_cuti j ON t.id_jenis_cuti = j.id_jenis_cuti
		INNER JOIN tbl_master_personal p ON t.id_pegawai = p.id
		WHERE id_pegawai = ".$id." ORDER BY id_cuti DESC";
		$sql1 = $this->db->query($sql)->result();
		$sql2 = $this->db->query($sql)->row();
		$this->smarty->assign("dCuti", $sql1);
		$this->smarty->assign("cuti", $sql2);
	}
	
	function mTblPengajuanCuti($id){
		$jml = "";
		$sql ="SELECT id_cuti, id_pegawai, t.id_jenis_cuti, tgl_cuti, tgl_selesai_cuti, jumlah_cuti,
		j.jenis_cuti, alasan, isApproved, isConfirm, approved_by, p.nama as nama, t.atasan_id as atasan_id
		FROM tbl_trans_cuti t		
		INNER JOIN tbl_jenis_cuti j ON t.id_jenis_cuti = j.id_jenis_cuti
		INNER JOIN tbl_master_personal p ON t.id_pegawai = p.id
		WHERE atasan_id=".$id."";
		//$sql1 = $this->db->query($sql)->result();
		$sql2 = $this->db->query($sql)->row();		
				
		$jml = $this->db->query($sql)->num_rows();
		
		//$this->smarty->assign("dCuti", $sql1);
		$this->smarty->assign("cuti", $sql2);	
		
		return $jml;
	}
	
	function mTotalRows($id, $max, $offset){
		$offset = $offset-1;
		$sql = "SELECT id_cuti, id_pegawai, t.id_jenis_cuti, tgl_cuti, tgl_selesai_cuti, jumlah_cuti,
		j.jenis_cuti, alasan, isApproved, isConfirm, approved_by, p.nama as nama, t.atasan_id as atasan_id
		FROM tbl_trans_cuti t		
		INNER JOIN tbl_jenis_cuti j ON t.id_jenis_cuti = j.id_jenis_cuti
		INNER JOIN tbl_master_personal p ON t.id_pegawai = p.id
		WHERE atasan_id=".$id." ORDER BY id_cuti DESC LIMIT ".$offset.", ".$max."";
		$sql1 = $this->db->query($sql)->result();
		$this->smarty->assign("dCuti", $sql1);
	}
	
	function mStatusCuti($id, $id_cuti){
		$sql ="SELECT id_cuti, id_pegawai, t.id_jenis_cuti, tgl_cuti, tgl_selesai_cuti, tgl_kembali, alamat_cuti,
		j.jenis_cuti, alasan, isApproved, isConfirm, approved_by, p.nama as nama, jumlah_cuti,
		jb.description as jabatan, d.description as depart, atasan_id 
		FROM tbl_trans_cuti t		
		INNER JOIN tbl_jenis_cuti j ON t.id_jenis_cuti = j.id_jenis_cuti
		INNER JOIN tbl_master_personal p ON t.id_pegawai = p.id
		INNER JOIN tbl_master_jabatan jb ON p.id_jabatan = jb.acajbt_uid
		INNER JOIN tbl_master_department d ON p.id_depart = d.acadept_uid
		WHERE id_cuti =".$id_cuti." LIMIT 1";
		$sql = $this->db->query($sql)->row();
		
		$idAtasan = $sql->atasan_id;
		
		$this->getAtasanById($idAtasan);
		
		$this->smarty->assign("cuti", $sql);
	}
	
	function getAtasanById($idAtasan){
		
		$sql2 ="SELECT id , nama, p.id_jabatan as id_jab, j.description as jabatan FROM tbl_master_personal p
		INNER JOIN tbl_master_jabatan j ON j.acajbt_uid = p.id_jabatan WHERE id = ".$idAtasan." LIMIT 1";
		$sql2 = $this->db->query($sql2)->row();
		$this->smarty->assign("atasan", $sql2);
	}
	
	function mEditCuti($id_peg, $id_cuti){
		$this->mjCuti();
		$sql = "SELECT c.id_cuti, c.id_pegawai, c.id_jenis_cuti, c.tgl_cuti,
				c.tgl_selesai_cuti, c.tgl_kembali, c.alasan, c.alamat_cuti, c.isConfirm, c.isApproved, c.approved_by, 
				p.nama as nama, p.id_depart, p.id_jabatan, j.jenis_cuti as jenis_cuti,
				d.description as depart, b.description as jabatan
				FROM tbl_trans_cuti c 				
				INNER JOIN tbl_jenis_cuti j ON c.id_jenis_cuti = j.id_jenis_cuti
				INNER JOIN tbl_master_personal p ON p.id = c.id_pegawai
				INNER JOIN tbl_master_jabatan b ON p.id_jabatan = b.acajbt_uid
				INNER JOIN tbl_master_department d ON p.id_depart = d.acadept_uid
				WHERE id_cuti=".$id_cuti." AND id_pegawai =".$id_peg." LIMIT 1";
		$sql = $this->db->query($sql)->row();
		$this->smarty->assign("eCuti",$sql);
	}
	
	function mUpCuti($id_peg, $id_cuti){
		$date1 = $this->input->post("tgl_mulai");
		$date2 = $this->input->post("tgl_selesai");
		
		$diff = abs(strtotime($date2) - strtotime($date1));

		$years = floor($diff / (365*60*60*24));
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
		$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
		
		$jd=gregoriantojd($months,$days,$years);
		echo jddayofweek($jd,1);	
		
		$this->db->where("id_cuti",$id_cuti);
		$this->db->update("tbl_trans_cuti", array(
				"id_pegawai"=>$id_peg,
				"id_jenis_cuti"=>$this->input->post("jenis_cuti"),
				"tgl_cuti"=>$this->input->post("tgl_mulai"),
				"tgl_selesai_cuti"=>$this->input->post("tgl_selesai"),
				"tgl_kembali"=>$this->input->post("tgl_kembali"),
				"alasan"=>$this->input->post("alasan"),
				"alamat_cuti"=>$this->input->post("al_cuti"),
				"jumlah_cuti"=>$days+1,
				"isConfirm"=>0,"isApproved"=>0
			));
	}
	
	function mDelCuti($id){
		$sql = "DELETE FROM tbl_trans_cuti WHERE id_cuti = ".$id."";
		$query = $this->db->query($sql);
		return $query;
	}
	
}