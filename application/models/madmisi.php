<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Madmisi extends CUTI_Model{
	
	function __construct(){
		parent::__construct();
	}
	
	function Madmisi(){
		parent::__construct();
		
		$this->db2->query("SET lc_time_names = 'id_ID'");
		
	}
	
	function mBukaSeleksi($periode, $jalur){
		
		$sql = "SELECT b.erased as hapuskah, b.kode as id_buka, b.nama as nama_buka, b.periode as periode_buka, 
		DAYNAME(b.tanggalBuka) as hariBuka, DAYNAME(b.tanggalTutup) as hariTutup, b.tanggalBuka, b.tanggalTutup,
		p.nama as nama_periode, j.nama as nama_jalur, pr.nama as nama_prodi, m.jenjangType as jenjang, m.tahun as tahun,
		pr.singkatan as ini_prodi, b.stsBuka as status, pr.kode as kode_prodi
		FROM adis_buka_smb b
		INNER JOIN adis_periode p ON b.periode = p.kode
		INNER JOIN adis_periode_master m ON m.kode = p.idPeriodeMaster
		INNER JOIN adis_jalur_smb j ON b.jalur = j.kode
		INNER JOIN adis_prodi pr ON b.prodi = pr.kode
		WHERE b.periode ='$periode'
		AND b.jalur = '$jalur'
		AND b.erased = 0;";
		$sql = $this->db2->query($sql)->result();
		$this->smarty->assign('sql',$sql);	
	}
	
	function mDataBuka($kode){
		
		$sql = "SELECT b.erased as status, b.kode as id_buka, b.nama as nama_buka, b.periode as periode_buka, 
		DAYNAME(b.tanggalBuka) as hariBuka, DAYNAME(b.tanggalTutup) as hariTutup, b.tanggalBuka, b.tanggalTutup,
		p.nama as nama_periode, j.kode as kode_jalur, j.nama as nama_jalur, pr.kode as kode_prodi,
		pr.nama as nama_prodi, m.jenjangType as jenjang, m.tahun as tahun,
		pr.singkatan as ini_prodi, b.stsBuka as status
		FROM adis_buka_smb b
		INNER JOIN adis_periode p ON b.periode = p.kode
		INNER JOIN adis_periode_master m ON m.kode = p.idPeriodeMaster
		INNER JOIN adis_jalur_smb j ON b.jalur = j.kode
		INNER JOIN adis_prodi pr ON b.prodi = pr.kode
		WHERE b.kode ='$kode';";
		$sql = $this->db2->query($sql)->row();
		$this->smarty->assign('buka',$sql);	
	}
	
	function mDataEvent($jalur, $periode){
		
		$sql = "SELECT b.erased, b.kode as id_event, b.nama as nama_event, 
			DAYNAME(b.tanggal) as hariEvent, b.tanggal, b.jamMasuk, b.jamKeluar, m.jenjangType as jenjang, r.gedung as gedung_event, r.kode as kode_kelas,
			p.nama as nama_periode, m.tahun as tahun, r.nama as nama_ruang, j.nama as nama_jalur, o.nama as nama_petugas, c.nama as nama_pww, f.nama as nama_pww2,
			b.statusJadwal as status, r.kursiTes, b.totalPeserta, b.prioritas
			FROM adis_event_smb b
			INNER JOIN adis_periode p ON b.periode = p.kode
			INNER JOIN adis_periode_master m ON m.kode = p.idPeriodeMaster
			INNER JOIN adis_ruang r ON b.ruang = r.kode
			INNER JOIN adis_jalur_smb j ON b.jalur = j.kode
			INNER JOIN adis_personal o ON b.petugas =o.kode
			INNER JOIN adis_personal c ON b.pewawancara =c.kode
			LEFT JOIN adis_personal f ON b.pewawancara2 =f.kode
			WHERE b.periode = '$periode' AND b.erased = 0
			AND jalur = '$jalur'
			ORDER BY id_event, status DESC";
			$sql = $this->db2->query($sql)->result();
			
			$this->smarty->assign('sql',$sql);	
	
	}
	
	function mSelectEvent($kode){
		
		$sql = "SELECT b.erased, b.kode as id_event, b.nama as nama_event, b.periode, b.jalur, b.ruang, b.petugas, b.pewawancara, b.pewawancara2,
			DAYNAME(b.tanggal) as hariEvent, b.tanggal, b.jamMasuk, b.jamKeluar, m.jenjangType as jenjang, r.gedung as gedung_event, r.kode as kode_kelas,
			p.nama as nama_periode, m.tahun as tahun, r.nama as nama_ruang, j.nama as nama_jalur, o.nama as nama_petugas, c.nama as nama_pww, f.nama as nama_pww2,
			b.statusJadwal as status, r.kursiTes, b.totalPeserta, b.prioritas
			FROM adis_event_smb b
			INNER JOIN adis_periode p ON b.periode = p.kode
			INNER JOIN adis_periode_master m ON m.kode = p.idPeriodeMaster
			INNER JOIN adis_ruang r ON b.ruang = r.kode
			INNER JOIN adis_jalur_smb j ON b.jalur = j.kode
			INNER JOIN adis_personal o ON b.petugas =o.kode
			INNER JOIN adis_personal c ON b.pewawancara =c.kode
			LEFT JOIN adis_personal f ON b.pewawancara2 =f.kode
			WHERE b.kode = '$kode'";
			$sql = $this->db2->query($sql)->row();
			
			$this->smarty->assign('event',$sql);	
	
	}
	
	function mAddEvent(){
		
		$periode = $this->input->post("periode");
		$jalur = $this->input->post("jalur");
		$tanggal = $this->input->post("tanggal");
		$ruang = $this->input->post("ruang");
		
		$this->db2->insert("adis_event_smb", array(
				"kode"=>implode (".",array($periode,$tanggal, $jalur, $ruang)),
				"nama"=>$this->input->post("nama"),
				"periode"=>$periode,
				"jalur"=>$jalur,
				"ruang"=>$this->input->post("ruang"),
				"tanggal"=>$this->input->post("tanggal"),
				"jamMasuk"=>$this->input->post("pukulMasuk"),
				"jamKeluar"=>$this->input->post("pukulKeluar"),
				"petugas"=>$this->input->post("petugas"),
				"pewawancara"=>$this->input->post("pww1"),
				"pewawancara2"=>$this->input->post("pww2"),
				"statusJadwal"=>$this->input->post("status"),
				"prioritas"=>$this->input->post("prioritas")
			));
	}
	
	function mEditEvent(){
		
		$kode = $this->input->post("kode");
		
		$jalur = $this->input->post("jalur");
		$tanggal = $this->input->post("tanggal");
		
		$this->db2->where('kode', $kode);
		$this->db2->update("adis_event_smb", array(
				"nama"=>$this->input->post("nama"),
				"periode"=>$this->input->post("periode"),
				"jalur"=>$jalur,
				"ruang"=>$this->input->post("ruang"),
				"tanggal"=>$this->input->post("tanggal"),
				"jamMasuk"=>$this->input->post("pukulMasuk"),
				"jamKeluar"=>$this->input->post("pukulKeluar"),
				"petugas"=>$this->input->post("petugas"),
				"pewawancara"=>$this->input->post("pww1"),
				"pewawancara2"=>$this->input->post("pww2"),
				"statusJadwal"=>$this->input->post("status"),
				"prioritas"=>$this->input->post("prioritas")
			));
	}
	
	function mPaidDaftarUlang($kode){
		$sql = "SELECT f.kode as kode_smb, f.bukaSmb, f.nomor as no_smb, u.username as nama_cm , p.nama as progdi, 
		DAYNAME(u.createTime) as hari, DATE(u.createTime) as tanggal, u.validation_status, d.jenjangType, up.rumahCell, 
		f.stsReapplyPaid, f.stsReapplyPaidConfirm, j.nama as n_jalur, t.kode as kode_typeTrans,
		d.tahun, d.semester, t.nama as typeTrans, t2.nama as typeAccount, DAYNAME(f.reapplyBankTransferTime) as hari_trans, 
		DATE(f.reapplyBankTransferTime) as tanggal_trans, k.buktiBayarDaftarUlang, f.reapplyBankTransferAmount,
		k.noRekPengirimDaftarUlang, k.namaRekPengirimDaftarUlang, k.metodBayarDaftarUlang, k.totalBiayaDaftarUlang
		FROM adis_smb_form f 
		INNER JOIN adis_smb_usr u ON u.kode = f.kode
		INNER JOIN adis_buka_smb b ON b.kode = f.bukaSmb
		INNER JOIN adis_periode d ON d.kode = b.periode
		INNER JOIN adis_smb_usr_pribadi up ON up.kode = f.kode
		INNER JOIN adis_prodi p ON p.kode = b.prodi
		INNER JOIN adis_jalur_smb j ON j.kode = b.jalur
		INNER JOIN adis_type t ON t.kode = f.reapplyBankTransferType
		INNER JOIN adis_type t2 ON t2.kode = f.reapplyBankAccountType
		INNER JOIN adis_smb_usr_keu k ON k.smbUsr = f.kode 
		WHERE f.nomor = '$kode'";
		$sql = $this->db2->query($sql)->row();
		$this->smarty->assign("transPaid", $sql);
		
		$kodePaidType = $sql->metodBayarDaftarUlang;
		$metodBayar = "SELECT * FROM adis_pembayaran WHERE kode = '$kodePaidType'";
		$metodBayar = $this->db2->query($metodBayar)->row();
		
		
		$total = $metodBayar->total;
		$total = $total - 1000000;
		
		$pembayaran = $metodBayar->pembayaran;
		
		$this->smarty->assign('total', $total);
		$this->smarty->assign('method',$pembayaran);		
		$this->smarty->assign('biaya', $metodBayar);
	}
	
	
	function mSelectPeriode(){
		$periode ="SELECT * FROM adis_periode WHERE status = 1 AND erased = 0";
		$periode = $this->db2->query($periode)->result();			
			
		$this->smarty->assign('periode',$periode);	
	
	}
	
	function mSelectPeriodeAka(){
		$periodeAka = "SELECT * FROM adis_periode_master WHERE erased = 0 ORDER BY kode DESC";
		$periodeAka = $this->db2->query($periodeAka)->result();
		$this->smarty->assign('periodeAka', $periodeAka);
	}
	
	function mSelectPeriodeCMB($jenjang = ''){
		$where = '';
		IF ($jenjang == 's1'){
			$where = 'AND jenjangType = 1';
		}
		IF ($jenjang == 's2'){
			$where = 'AND jenjangType = 2';
		}
		$periodeAka = "SELECT * FROM adis_periode_master WHERE erased = 0 $where ORDER BY kode DESC";
		$periodeAka = $this->db2->query($periodeAka)->result();
		$this->smarty->assign('periodeAka', $periodeAka);
	}
	
	function mSelectProdi(){
		$prodi ="SELECT * FROM adis_prodi WHERE erased = 0";
		$prodi = $this->db2->query($prodi)->result();			
			
		$this->smarty->assign('prodi',$prodi);	
	
	}
	
	function mSelectJalur(){
		$jalur ="SELECT * FROM adis_jalur_smb WHERE erased = 0";
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
	
	function mAbsen($kode){
		$query = "	SELECT f.kode, f.`event`, f.nomor, p.nama as namaPeserta, e.nama as namaEvent, 
					e.ruang, e.petugas, e.pewawancara, e.pewawancara2, 
					o.nama as namaPetugas, o1.nama as namaPww1, o2.nama as namaPww2,
					e.tanggal, e.jamMasuk, e.jamKeluar, pr.nama as namaProdi, d.sesi, pr.singkatan
					FROM adis_smb_form f
					INNER JOIN adis_smb_usr_pribadi p ON p.kode = f.kode
					INNER JOIN adis_event_smb e ON e.kode = f.`event`
					LEFT JOIN adis_personal o ON o.kode = e.petugas
					LEFT JOIN adis_personal o1 ON o1.kode = e.pewawancara
					LEFT JOIN adis_personal o2 ON o2.kode = e.pewawancara2
					INNER JOIN adis_prodi pr ON pr.kode = RIGHT(f.bukaSmb,4)
					INNER JOIN adis_periode d ON d.kode = e.periode
					WHERE f.`event` = '$kode' ORDER BY RIGHT(f.bukaSmb,4); ";
		$query = $this->db2->query($query)->result();
		$this->smarty->assign('absen', $query);
		
		$query2 = "	SELECT e.nama as namaEvent, e.ruang, e.petugas, e.pewawancara, e.pewawancara2, 
					o.nama as namaPetugas, o1.nama as namaPww1, o2.nama as namaPww2, 
					DAYNAME(e.tanggal) as hari, MONTHNAME(e.tanggal) as bulan, YEAR(e.tanggal) as tahun,  DAY(e.tanggal) as days,
					e.tanggal, e.jamMasuk, e.jamKeluar, d.sesi
					FROM adis_event_smb e 
					LEFT JOIN adis_personal o ON o.kode = e.petugas
					LEFT JOIN adis_personal o1 ON o1.kode = e.pewawancara
					LEFT JOIN adis_personal o2 ON o2.kode = e.pewawancara2
					INNER JOIN adis_periode d ON d.kode = e.periode
					WHERE e.kode = '$kode';";
		$query2 = $this->db2->query($query2)->row();
		$this->smarty->assign('event', $query2);
	
	}
	
	function mMahasiswa($kode, $prodi = ""){
		$this->db2->query("SET @num:=0;");
		$query = "	SELECT  @num:=@num+1 AS 'No', f.kode as 'Email', f.bukaSmb, f.nomor as 'No Registrasi', u.username as 'Nama Lengkap' 
					, p.nama as progdi, DATE(u.createTime) as 'Tanggal Pendaftaran', j.nama as Jalur
					FROM adis_smb_form f 
					INNER JOIN adis_smb_usr u ON u.kode = f.kode
					INNER JOIN adis_buka_smb b ON b.kode = f.bukaSmb
					INNER JOIN adis_smb_usr_pribadi up ON up.kode = f.kode
					INNER JOIN adis_prodi p ON p.kode = b.prodi
					INNER JOIN adis_jalur_smb j ON j.kode = b.jalur
					WHERE f.bukaSmb = '$kode'
					ORDER BY f.nomor";
		if ($prodi == ""){
			$query = $this->db2->query($query)->result();
			$this->smarty->assign('mahasiswa', $query);
		}else{
			$query = $this->db2->query($query);
			return $query;
		}
	}
	
	
	
}
