<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Mportal extends CUTI_Model{
	
	function __construct(){
		parent::__construct();
	}
	
	function Mportal(){
		parent::__construct();
		
		$this->db2->query("SET lc_time_names = 'id_ID'");
	}
	
	
	function mCmb($kode){
		if (is_numeric($kode)){
			$wer = "WHERE f.nomor = '$kode'";
		}else{
			$wer ="WHERE f.kode = '$kode'";
		}
		
		$sql = "SELECT f.kode as kode_smb, f.bukaSmb, f.nomor as no_smb, u.username as nama_cm , p.nama as progdi, p.kode as kodeProdi,  
				DAYNAME(u.createTime) as hari, DATE(u.createTime) as tanggal, 
				DAY(u.createTime) as cDay, MONTHNAME(u.createTime) as cMonth, YEAR(u.createTime) as cYear,
				u.validation_status, m.jenjangType, up.rumahCell, up.genderType,
				up.tempatLahir, up.tanggalLahir, DAYNAME(up.tanggalLahir) as hLahir, 
				DAY(up.tanggalLahir) as tLahir, MONTHNAME(up.tanggalLahir) as bLahir, YEAR(up.tanggalLahir) as yLahir,
				up.rumahAlamat, up.statusAlamat, up.nama as gender, up.statusKeluarga, e.status as statusPend,
				up.statusPrestasi, up.stsPribadiConfirm, up.statusSaudara, up.foto, up.confirmSaudara,
				f.stsApplyPaid, f.stsApplyPaidConfirm, up.stsPribadiConfirm, f.stsEventConfirm, f.stsResultConfirm, f.stsMundurBeforeReapply,
				f.stsReapplyPaid, stsReapplyPaidConfirm, f.stsMundurAfterReapply, j.nama as n_jalur, f.stsResultPass, f.stsReapplyPaidConfirm, f.stsReapplyPaid,
				m.tahun, m.semester, f.nim, d.nama as namaPeriode,
				k.tolakPendaftaran, k.tolakDU, k.pesanTolakP, k.pesanTolakDU, b.jalur
				FROM adis_smb_form f 
				INNER JOIN adis_smb_usr u ON u.kode = f.kode
				INNER JOIN adis_buka_smb b ON b.kode = f.bukaSmb
				INNER JOIN adis_periode d ON d.kode = b.periode
				INNER JOIN adis_periode_master m ON m.kode = d.idPeriodeMaster		
				INNER JOIN adis_smb_usr_pribadi up ON up.kode = f.kode
				INNER JOIN adis_smb_usr_edu e ON e.smbUsr = f.kode
				INNER JOIN adis_prodi p ON p.kode = b.prodi
				INNER JOIN adis_jalur_smb j ON j.kode = b.jalur
				INNER JOIN adis_type t ON t.kode = up.genderType
				LEFT JOIN adis_smb_usr_keu k ON k.smbUsr = f.kode 
				$wer";
		$sql = $this->db2->query($sql)->row();
		$this->smarty->assign("cmb", $sql);
	
	}
	
	function mProfil($kode){
		if (is_numeric($kode)){
			$wer = "WHERE f.nomor = '$kode'";
		}else{
			$wer ="WHERE f.kode = '$kode'";
		}
		
		$sql = "SELECT f.kode as kode_smb, f.bukaSmb, f.nomor as no_smb, u.username as nama_cm , p.nama as progdi, p.kode as proKode, 
				m.jenjangType, up.rumahCell, up.genderType, up.tempatLahir, up.tanggalLahir, DAYNAME(up.tanggalLahir) as hLahir, 
				DAY(up.tanggalLahir) as tLahir, MONTHNAME(up.tanggalLahir) as bLahir, YEAR(up.tanggalLahir) as yLahir,
				up.nomorKtp, up.rumahEmail, up.suratAlamat, tA.nama as suratProp, tD.kodepos as suratKodPos,
				up.suratTel, up.suratFax,
				up.rumahAlamat, t2.nama as propNama, t3.nama as kabKotaNama, t4.kodepos as kodePos, up.rumahTel, up.rumahFax, 
				up.ayahNama, up.ayahAlamat, up.ayahCell, up.ayahEmail, 
				up.ibuNama, up.ibuAlamat, up.ibuCell, up.ibuEmail, 
				up.waliNama, up.waliAlamat, up.waliCell, up.waliEmail, 
				t.nama as gender, t.kode as kodeGender, t1.nama as agamaName,
				up.statusAlamat, up.statusKeluarga, 
				e.status as statusPend, e.nama as namaEdu, e.jurusan,
				up.statusPrestasi, up.stsPribadiConfirm, up.statusSaudara, up.foto,
				f.stsApplyPaid, f.stsApplyPaidConfirm, up.stsPribadiConfirm, f.stsEventConfirm, f.stsResultConfirm, f.stsMundurBeforeReapply,
				f.stsReapplyPaid, stsReapplyPaidConfirm, f.stsMundurAfterReapply, j.nama as n_jalur, f.stsResultPass, f.stsReapplyPaidConfirm,
				m.tahun, m.semester
				FROM adis_smb_form f 
				INNER JOIN adis_smb_usr u ON u.kode = f.kode
				INNER JOIN adis_buka_smb b ON b.kode = f.bukaSmb
				INNER JOIN adis_periode d ON d.kode = b.periode
				INNER JOIN adis_periode_master m ON m.kode = d.idPeriodeMaster
				INNER JOIN adis_smb_usr_pribadi up ON up.kode = f.kode
				INNER JOIN adis_smb_usr_edu e ON e.smbUsr = f.kode
				INNER JOIN adis_prodi p ON p.kode = b.prodi
				INNER JOIN adis_jalur_smb j ON j.kode = b.jalur
				INNER JOIN adis_type t ON t.kode = up.genderType
				LEFT JOIN adis_type t1 ON t1.kode = up.agamaType
				LEFT JOIN adis_wil t2 ON t2.kode = up.rumahProp
				LEFT JOIN adis_wil t3 ON t3.kode = up.rumahKabKota		
				LEFT JOIN adis_kodepos t4 ON t4.kode = up.rumahKodePos
				LEFT JOIN adis_wil tA ON tA.kode = up.suratProp	
				LEFT JOIN adis_kodepos tD ON tD.kode = up.suratKodePos
				$wer";
		
		$sql = $this->db2->query($sql)->row();
		$this->smarty->assign("prof", $sql);
	}
	
	function mStatus($kode){
		$sql = "SELECT f.kode 
				FROM adis_smb_form f 
				INNER JOIN adis_smb_usr_pribadi u ON u.kode = f.kode
				INNER JOIN adis_smb_usr_edu e ON e.smbUsr = f.kode";
	}
	
	function mDetailAlamat($kode){
		$sql ="SELECT up.kode, up.nama, up.rumahCell, t.nama as gender, up.tempatLahir, up.tanggalLahir, up.nomorKtp, 
		agamaType, t2.nama as agama, 
		rumahProp, w.nama as propRumah, rumahKabKota, w2.nama as kabkotaRumah, rumahKodePos, k.kodepos as kodeposRumah, 
		suratProp, ws.nama as propSurat, suratKabKota, ws2.nama as kabkotaSurat, suratKodePos, ks.kodepos as kodeposSurat, 		
		up.rumahAlamat, up.rumahKabKota, up.rumahProp, up.rumahKodePos, rumahTel, rumahFax,
		up.suratAlamat, up.suratKabKota, up.suratProp, up.suratKodePos, suratTel, suratFax,
		up.statusAlamat 
		FROM adis_smb_usr_pribadi up
		LEFT JOIN adis_type t ON t.kode = up.genderType
		LEFT JOIN adis_type t2 ON t2.kode = up.agamaType
		LEFT JOIN adis_wil w ON w.kode = up.rumahProp
		LEFT JOIN adis_wil w2 ON w2.kode = up.rumahKabKota
		LEFT JOIN adis_kodepos k ON k.kode = up.rumahKodePos
		LEFT JOIN adis_wil ws ON ws.kode = up.suratProp
		LEFT JOIN adis_wil ws2 ON ws2.kode = up.suratKabKota
		LEFT JOIN adis_kodepos ks ON k.kode = up.suratKodePos
		WHERE up.kode = '$kode'";
		$sql = $this->db2->query($sql)->row();
		$this->smarty->assign("alamat", $sql);
	}
	
	function mPaidPendaftaran($kode){
		$sql = "SELECT f.kode as kode_smb, f.bukaSmb, f.nomor as no_smb, u.username as nama_cm , p.nama as progdi, 
		DAYNAME(u.createTime) as hari, DATE(u.createTime) as tanggal, u.validation_status, m.jenjangType, up.rumahCell, 
		FORMAT(f.applyBankTransferAmount, 2) as biaya, f.stsApplyPaid, f.stsApplyPaidConfirm, j.nama as n_jalur, t.kode as kode_typeTrans,
		m.tahun, m.semester, t.nama as typeTrans, t2.nama as typeAccount, DAYNAME(f.applyBankTransferTime) as hari_trans, 
		DATE(f.applyBankTransferTime) as tanggal_trans, k.buktiBayarPendaftaran as buktiBayarPendaftaran, 
		k.noRekPengirimPendaftaran, k.namaRekPengirimPendaftaran, k.noAtmCardPendaftaran
		FROM adis_smb_form f 
		INNER JOIN adis_smb_usr u ON u.kode = f.kode
		INNER JOIN adis_buka_smb b ON b.kode = f.bukaSmb
		INNER JOIN adis_periode d ON d.kode = b.periode
		INNER JOIN adis_periode_master m ON m.kode = d.idPeriodeMaster
		INNER JOIN adis_smb_usr_pribadi up ON up.kode = f.kode
		INNER JOIN adis_prodi p ON p.kode = b.prodi
		INNER JOIN adis_jalur_smb j ON j.kode = b.jalur
		INNER JOIN adis_type t ON t.kode = f.applyBankTransferType
		INNER JOIN adis_type t2 ON t2.kode = f.applyBankAccountType
		INNER JOIN adis_smb_usr_keu k ON k.smbUsr = f.kode 
		WHERE f.kode = '$kode'";
		$sql = $this->db2->query($sql)->row();
		$this->smarty->assign("transPaid", $sql);
	}
	
	function mSimpanBayar($file_name, $kode){
		$unik = uniqid();
		$date = date("Y-m-d H:i:s");
		
		$this->db2->where("kode", $kode);
		$this->db2->update("adis_smb_form", array(
				"applyBankTransferType"=>$this->input->post("typeTrans"),
				"applyBankAccountType"=>$this->input->post("bankAkun"),
				"applyBankTransferAmount"=>$this->input->post("totalBiaya"),
				"applyBankTransferTime"=>$this->input->post("tanggalBayar"),
				"stsApplyPaid"=>1
			));
		
		
		$dbExist = $this->db->query("SELECT kode, smbUsr FROM adis_smb_usr_keu WHERE smbUsr = '$kode'")->num_rows();
		if ( $dbExist == 0){
			$this->db2->insert("adis_smb_usr_keu", array(
					"kode"=>$unik,
					"smbUsr"=>$kode,
					"createTime"=>$date,
					"createUser"=>$kode,
					"buktiBayarPendaftaran"=>$file_name,
					"noRekPengirimPendaftaran"=>$this->input->post("no_rek_cmb"),
					"namaRekPengirimPendaftaran"=>$this->input->post("nama_rek_cmb"),
					"noAtmCardPendaftaran"=>$this->input->post("noAtmCard")
				));
		}else{
			$this->db2->where("smbUsr", $kode);
			$this->db2->update("adis_smb_usr_keu", array(
					"smbUsr"=>$kode,
					"createTime"=>$date,
					"createUser"=>$kode,
					"buktiBayarPendaftaran"=>$file_name,
					"noRekPengirimPendaftaran"=>$this->input->post("no_rek_cmb"),
					"namaRekPengirimPendaftaran"=>$this->input->post("nama_rek_cmb"),
					"noAtmCardPendaftaran"=>$this->input->post("noAtmCard"),
					"tolakPendaftaran"=>0
				));
		}
	}
	
	function mEmailKonfirm($kode, $bayar){
			$cmb = $this->db->query("SELECT nama FROM adis_smb_usr_pribadi WHERE kode = '$kode';")->row();
			$noReg = $this->db->query("SELECT nomor FROM adis_smb_form WHERE kode = '$kode';")->row();
			$nama = $cmb->nama;
			$regNo = $noReg->nomor;
			// Validasi email terlebih dahulu.
				// Email configuration
					$config = Array(
						  'protocol' => 'smtp',
						  'smtp_host' => 'students.paramadina.ac.id',
						  'smtp_port' => 25,
						  'smtp_user' => 'orangbaik@students.paramadina.ac.id', // change it to yours
						  'smtp_pass' => 'S@l4mb3l@k4ng', // change it to yours
						  'mailtype' => 'html',
						  'charset' => 'iso-8859-1',
						  'wordwrap' => TRUE
					);	
				
					$this->load->library('email', $config);
					$this->email->from('admission@paramadina.ac.id', "Admission Paramadina");
					$this->email->to("div.keu@paramadina.ac.id");
					$this->email->cc("admission@paramadina.ac.id");
					if ($bayar == 'D'){
						$this->email->subject("Pembayaran Pendaftaran Registrasi Online (".$nama.")");
						$this->email->message("Calon Mahasiswa (".$nama.") dengan No Registrasi ".$regNo." sudah melakukan pembayaran pendaftaran masuk Universitas Paramadina, Silahkan dikonfirmasi pada tautan berikut : http://admission.paramadina.ac.id/smb/smb/smbPay");
					}else{
						$this->email->subject("Pembayaran Pendaftaran Ulang (".$nama.")");
						$this->email->message("Calon Mahasiswa (".$nama.") dengan No Registrasi ".$regNo." sudah melakukan pembayaran pendaftaran ulang masuk Universitas Paramadina, Silahkan dikonfirmasi pada tautan berikut : http://admission.paramadina.ac.id/smb/smb/smbPay");

					}
					$data['message'] = "Sorry Unable to send email...";	
					if($this->email->send()){					
						$data['message'] = "Mail sent...";			
					}
	}
	
	function mPaidDaftarUlang($kode, $saudara){
		$sql = "SELECT f.kode as kode_smb, f.bukaSmb, f.nomor as no_smb, u.username as nama_cm , p.nama as progdi, p.kode as kodeProdi, SUBSTR(bukaSmb,12, 2) as jalur,
		DAYNAME(u.createTime) as hari, DATE(u.createTime) as tanggal, u.validation_status, m.jenjangType, up.rumahCell, 
		f.stsReapplyPaid, f.stsReapplyPaidConfirm, j.nama as n_jalur, t.kode as kode_typeTrans,
		m.tahun, m.semester, t.nama as typeTrans, t2.nama as typeAccount, DAYNAME(f.reapplyBankTransferTime) as hari_trans, 
		DATE(f.reapplyBankTransferTime) as tanggal_trans, k.buktiBayarDaftarUlang, f.reapplyBankTransferAmount,
		k.noRekPengirimDaftarUlang, k.namaRekPengirimDaftarUlang, 
		k.metodBayarDaftarUlang, k.totalBiayaDaftarUlang, k.noAtmCardDaftarulang
		FROM adis_smb_form f 
		INNER JOIN adis_smb_usr u ON u.kode = f.kode
		INNER JOIN adis_buka_smb b ON b.kode = f.bukaSmb
		INNER JOIN adis_periode d ON d.kode = b.periode
		INNER JOIN adis_periode_master m ON m.kode = d.idPeriodeMaster
		INNER JOIN adis_smb_usr_pribadi up ON up.kode = f.kode
		INNER JOIN adis_prodi p ON p.kode = b.prodi
		INNER JOIN adis_jalur_smb j ON j.kode = b.jalur
		INNER JOIN adis_type t ON t.kode = f.reapplyBankTransferType
		INNER JOIN adis_type t2 ON t2.kode = f.reapplyBankAccountType
		INNER JOIN adis_smb_usr_keu k ON k.smbUsr = f.kode 
		WHERE f.kode = '$kode'";
		$sql = $this->db2->query($sql)->row();
		$this->smarty->assign("transPaid", $sql);
		
		$kodePaidType = $sql->metodBayarDaftarUlang;
		$metodBayar = "SELECT * FROM adis_pembayaran WHERE kode = '$kodePaidType'";
		$biaya = $this->db2->query($metodBayar)->row();
		
		
		$pembayaran = $biaya->pembayaran;
		$total = $biaya->total;
		//$total = $total - 1000000;
		
		$sisaAng = 0;
		$totalAll = 0;
		$diskonSau = "";

		//Potongan memiliki saudara
		if ($sql->kodeProdi != '1204'){
			if ($saudara != 0){
				if ($pembayaran == 1){
					$diskonSau = 20/100*$biaya->uangMasuk;
				}else{
					$diskonSau = 20/100*$biaya->totalAngsuran;
				}
				$total = $total - $diskonSau;
			}
		}		

		//Potongan pembayaran lunas
		if ($pembayaran == 1){
			if ($sql->kodeProdi != '1204'){
				if($sql->jalur != 'KP'){
					$total = $total - 1000000;
				}
			}
		}else{
			$sisaAng = $biaya->totalAngsuran - $biaya->angsuran1;	
					
			if ($sql->jalur == 'KP'){
				if ($sql->kodeProdi == '1206'){
					$totalAll = $total + $biaya->angsuran2 + $biaya->angsuran3 + $biaya->angsuran4;
				}else if($sql->kodeProdi == '1208'){
					$totalAll = $total + $biaya->angsuran2 + $biaya->angsuran3 + $biaya->angsuran4 + $biaya->angsuran6 + $biaya->angsuran6 + $biaya->angsuran7 + $biaya->angsuran8;
				}
			}else{		
				$totalAll = $total + $biaya->angsuran2 + $biaya->angsuran3;
			}
		}
		
		$this->smarty->assign('sisaAng',$sisaAng);
		$this->smarty->assign('diskonSau', $diskonSau);
		$this->smarty->assign('totalAll', $totalAll);
		$this->smarty->assign('total', $total);
		$this->smarty->assign('method',$pembayaran);		
		$this->smarty->assign('biaya', $biaya);
		
		return true;
	}
	
	
	function mSimpanDaftarUlang($file_name, $kode){
		$date = date("Y-m-d H:i:s");
		
		$jumlahBayar = $this->input->post("jumlahBayar");		
		$jumlahBayar = str_replace('.', '', $jumlahBayar); 
		$jumlahBayar = str_replace(' ', '', $jumlahBayar);
		
		$this->db2->where("kode", $kode);
		$this->db2->update("adis_smb_form", array(
				"reapplyBankTransferType"=>$this->input->post("typeTrans"),
				"reapplyBankAccountType"=>$this->input->post("bankAkun"),
				"reapplyBankTransferAmount"=>$jumlahBayar,
				"reapplyBankTransferTime"=>$this->input->post("tanggalBayar"),
				"stsReapplyPaid"=>1
			));
		
		$this->db2->where("smbUsr", $kode);
		$this->db2->update("adis_smb_usr_keu", array(				
				"updateTime"=>$date,
				"updateUser"=>$kode,
				"buktiBayarDaftarUlang"=>$file_name,
				"noRekPengirimDaftarUlang"=>$this->input->post("no_rek_cmb"),
				"namaRekPengirimDaftarUlang"=>$this->input->post("nama_rek_cmb"),
				"metodBayarDaftarUlang"=>$this->input->post("kodeMetod"),
				"totalBiayaDaftarUlang"=>$this->input->post("totalDaftarUlang"),
				"noAtmCardDaftarulang"=>$this->input->post("noAtmCard"),
				"tolakDU"=>0
			));
	}
	
	function mSaveAlamat($file_name, $kode, $date){
		$this->db2->where('kode', $kode);
			$this->db2->update('adis_smb_usr_pribadi', 
							array(  "updateUser"=>$kode,
									"updateTime"=>$date,
									"nama"=> $this->input->post("nama"),
									"tempatLahir" => $this->input->post("tempatLahir"),
									"tanggalLahir" => $this->input->post("tanggalLahir"),
									"nomorKtp" => $this->input->post("no_id"),
									"agamaType" => $this->input->post("agama"),
									"foto"=>$file_name,
									"rumahAlamat" => $this->input->post("occupation"),
									"rumahProp" => $this->input->post("propinsi"),
									"rumahKabKota" => $this->input->post("kabkota"),
									"rumahKodePos" => $this->input->post("kodepos"),
									"rumahTel" => $this->input->post("telRumah"),
									"rumahFax" => $this->input->post("fax"),
									"suratAlamat" => $this->input->post("occupationsur"),
									"suratProp" => $this->input->post("propinsi2"),
									"suratKabKota" => $this->input->post("kabkota2"),
									"suratKodePos" => $this->input->post("kodepos2"),
									"suratTel" => $this->input->post("telRumah2"),
									"suratFax" => $this->input->post("fax2"),
									"statusAlamat" =>1,
			));
	}
	
	function mAddSaudara($kode){
		$unik = uniqid();
		$time = date("Y-m-d H:i:s");
		$this->db2->insert('adis_smb_usr_kel', array(
							"createUser"=>$kode,
							"createTime"=>$time,
							"kode"=>$unik,
							"smbUsr"=>$kode,
							"nomor"=>$this->input->post("nomorKe"),
							"nama"=>$this->input->post("namaSaudara"),
							"genderType"=>$this->input->post("sexSaudara"),
							"prodi"=>$this->input->post("pendSaudara"),
							"lulus"=>$this->input->post("sekSau"),
							"nim"=>$this->input->post("nim"),
							"kerja"=>$this->input->post("perSau"),
							"angkatan"=>$this->input->post("angkatan"),
							"tanggalLahir"=>$this->input->post("tglLahirSau"),
							"status"=>1));

		$this->db2->where('kode',$kode);
		$this->db2->update('adis_smb_usr_pribadi', array('adaSaudara' => +1));
	}
	
	function mSelectSaudara($kode){
		$query = "	SELECT s.kode as kodeSaudara, s.smbUsr, s.nomor, s.nama as namaSaudara, t.nama as gender, p.nama as prodi, s.confirmed,
					p.singkatan as singProdi, s.angkatan, s.lulus, s.nim, 
					s.tanggalLahir, s.kerja, s.status
					FROM adis_smb_usr_kel s
					INNER JOIN adis_type t ON t.kode = s.genderType
					LEFT JOIN adis_prodi p ON p.kode = s.prodi
					WHERE s.smbUsr = '$kode' AND s.erased = 0 ORDER BY s.nomor;
				 ";
		$result = $this->db2->query($query)->result();
		$num_rows =  $this->db2->query($query)->num_rows();
		
		$this->smarty->assign('saudara',$result);
		$this->smarty->assign('saudara_rows',$num_rows);
	}
	
	
	
	function mAddOrtu($kode){		
		$time = date("Y-m-d H:i:s");
		$this->db2->where('kode',$kode);
		$this->db2->update('adis_smb_usr_pribadi', array(
							'updateuser'=>$kode,
							'updateTime'=>$time,
							'ayahNama'=>$this->input->post('namaAyah'),
							'ayahAlamat'=>$this->input->post('alamatAyah'),
							'ayahKabKota'=>$this->input->post('kabAyah'),
							'ayahProp'=>$this->input->post('propAyah'),
							'ayahKodePos'=>$this->input->post('kposAyah'),
							'ayahTel'=>$this->input->post('telAyah'),
							'ayahCell'=>$this->input->post('hpAyah'),
							'ayahEmail'=>$this->input->post('emailAyah'),
							'ibuNama'=>$this->input->post('namaIbu'),
							'ibuAlamat'=>$this->input->post('alamatIbu'),
							'ibuKabKota'=>$this->input->post('kabIbu'),
							'ibuProp'=>$this->input->post('propIbu'),
							'ibuKodePos'=>$this->input->post('kposIbu'),
							'ibuTel'=>$this->input->post('telIbu'),
							'ibuCell'=>$this->input->post('hpIbu'),
							'ibuEmail'=>$this->input->post('emailIbu'),
							'waliNama'=>$this->input->post('namaWali'),
							'waliAlamat'=>$this->input->post('alamatWali'),
							'waliKabKota'=>$this->input->post('kabWali'),
							'waliProp'=>$this->input->post('propWali'),
							'waliKodePos'=>$this->input->post('kposWali'),
							'waliTel'=>$this->input->post('telWali'),
							'waliCell'=>$this->input->post('hpWali'),
							'waliEmail'=>$this->input->post('emailWali'),
							'stsPribadi'=>1,
							'statusKeluarga'=>1
							));
	}
	
	function mSelectOrtu($kode){
		$qry =	"SELECT
				p.kode, p.ayahNama, p.ayahAlamat, p.ayahKabKota, p.ayahProp, p.ayahKodePos,	p.ayahCell, p.ayahTel,p.ayahEmail,
				p.ibuNama, p.ibuAlamat,	p.ibuKabKota, p.ibuProp, p.ibuKodePos, p.ibuCell, p.ibuTel,	p.ibuEmail,
				p.waliNama, p.waliAlamat, p.waliKabKota, p.waliProp, p.waliKodePos, p.waliCell,	p.waliTel, p.waliEmail,
				t.nama as ayahPropNama, t2.nama as ayahKabNama, t3.nama as ibuKabNama, t4.nama as ibuPropNama, 
				t6.nama as waliPropNama, t7.nama as waliKabNama, 
				k1.kodepos as ayahKodePosNama, k2.kodepos as ibuKodePosNama, k3.kodepos as waliKodePosNama
				FROM adis_smb_usr_pribadi p 
				LEFT JOIN adis_wil t ON p.ayahProp = t.kode 
				LEFT JOIN adis_wil t2 ON p.ayahKabKota = t2.kode 
				LEFT JOIN adis_wil t3 ON p.ibuKabKota = t3.kode 
				LEFT JOIN adis_wil t4 ON p.ibuProp = t4.kode  
				LEFT JOIN adis_wil t6 ON p.waliProp = t6.kode 
				LEFT JOIN adis_wil t7 ON p.waliKabKota = t7.kode 	
				LEFT JOIN adis_kodepos k1 ON p.ayahKodePos = k1.kode  
				LEFT JOIN adis_kodepos k2 ON p.ibuKodePos = k2.kode 
				LEFT JOIN adis_kodepos k3 ON p.waliKodePos = k3.kode 				
				WHERE p.kode ='$kode';";
		$qry = $this->db2->query($qry)->row();
		$this->smarty->assign('ortu',$qry);
	}
	
	function mSelectPendidikan($kode){
		$qry = "SELECT e.kode as kodeEdu, e.smbUsr, e.sekolahType, e.sekolahOwnerType, e.nama as sekolah, e.prop, e.kabKota, e.kodePos,
				e.jurusan, e.tahunLulus, e.nilai,
				t.nama as sekolahNama, t2.nama as sekolahTipe, w.nama as propNama, w2.nama as kabNama, k.kodepos
				FROM adis_smb_usr_edu e
				LEFT JOIN adis_type t ON t.kode = e.sekolahType
				LEFT JOIN adis_type t2 ON t2.kode = e.sekolahOwnerType
				LEFT JOIN adis_wil w ON w.kode = e.prop
				LEFT JOIN adis_wil w2 ON w2.kode = e.kabKota
				LEFT JOIN adis_kodepos k ON k.kode = e.kodePos
				WHERE e.smbUsr = '$kode' AND e.erased = 0;
				";
		$qry = $this->db2->query($qry)->result();
		$this->smarty->assign('eduList', $qry);
		
		$qry1 = "SELECT e.kode as kodeEdu, e.smbUsr, e.sekolahType, e.sekolahOwnerType, e.nama as sekolah, e.prop, e.kabKota, e.kodePos,
				e.jurusan, e.tahunLulus, e.nilai,
				t.nama as sekolahNama, t2.nama as sekolahTipe, w.nama as propNama, w2.nama as kabNama, k.kodepos as kodeposN
				FROM adis_smb_usr_edu e
				LEFT JOIN adis_type t ON t.kode = e.sekolahType
				LEFT JOIN adis_type t2 ON t2.kode = e.sekolahOwnerType
				LEFT JOIN adis_wil w ON w.kode = e.prop
				LEFT JOIN adis_wil w2 ON w2.kode = e.kabKota
				LEFT JOIN adis_kodepos k ON k.kode = e.kodePos
				WHERE e.smbUsr = '$kode' AND e.erased = 0;
				";
		$qry1 = $this->db2->query($qry1)->row();
		$this->smarty->assign('eduData', $qry1);
	}
	
	function mAddPendidikan($kode){		
		$date = date("Y-m-d H:i:s");
		$this->db2->where('smbUsr', $kode);
		$this->db2->update('adis_smb_usr_edu',array(
							"updateUser"=>$kode,
							"updateTime"=>$date,
							'sekolahType'=> $this->input->post("pendType"),
							'sekolahOwnerType'=> $this->input->post("sekType"),
							"nama"=> $this->input->post("namaSekolah"),
							'prop'=> $this->input->post("prop"),
							'kabKota'=> $this->input->post("kab"),
							'kodePos'=> $this->input->post("kpos"),
							'tahunLulus'=> $this->input->post("tahunLulus"),
							'jurusan'=> $this->input->post("jurusan"),
							'nilai'=> $this->input->post("nilai"),
							'status'=>1
							));
	}
	
	function mAddPrestasi($kode){
		$date = date("Y-m-d H:i:s");
		$unik = uniqid();
		$this->db2->insert('adis_smb_usr_prestasi',array(
							"createUser"=>$kode,
							"createTime"=>$date,
							"kode"=>$unik,
							'smbUsr'=> $kode,
							'namaInstitusi'=> $this->input->post("instPres"),
							'namaPrestasi'=> $this->input->post("namaPres"),
							'tahun'=> $this->input->post("tahun")
							));
	}
	
	function mAddOrganisasi($kode){
		$date = date("Y-m-d H:i:s");
		$unik = uniqid();
		$this->db2->insert('adis_smb_usr_org',array(
							"createUser"=>$kode,
							"createTime"=>$date,
							"kode"=>$unik,
							'smbUsr'=> $kode,
							'namaKegiatan'=> $this->input->post("namaOrg"),
							'jabatan'=> $this->input->post("jabatanOrg"),
							'tempat'=> $this->input->post("tempatOrg"),
							'dariTahun'=> $this->input->post("mulaiOrg"),
							'sampaiTahun'=> $this->input->post("selesaiOrg")
							));
	}
	
	function mRuangSmb($kode){
		$date = date("Y-m-d H:i:s");
		$this->db2->query("SET @_periode = (SELECT substr(bukaSmb, 1, 10) FROM adis_smb_form WHERE kode ='$kode');");
		$this->db2->query("SET @_jalur = (SELECT substr(bukaSmb, 12, 2) FROM adis_smb_form WHERE kode ='$kode');");
		
		$this->db2->query("SET @_prioritas = (SELECT MIN(e.prioritas) FROM adis_event_smb e 
									INNER JOIN adis_ruang r ON r.kode = e.ruang 
									WHERE periode = @_periode AND jalur = @_jalur 
									AND e.totalPeserta != r.kursiTes 
									AND e.erased = 0 AND statusJadwal = 1);");
		
		$ruang = "SELECT e.kode, e.nama, e.periode, e.tanggal, e.jalur,e.jamMasuk, e.ruang, e.petugas, e.pewawancara, e.jamKeluar, e.statusJadwal,
							e.totalPeserta, e.pewawancara2, e.prioritas, r.kursiTes
							FROM adis_event_smb e
							INNER JOIN adis_ruang r ON r.kode = e.ruang
							WHERE periode = @_periode 
							AND jalur = @_jalur 
							AND e.erased = 0 AND e.statusJadwal = 1 
							AND e.totalPeserta != r.kursiTes 
							AND e.prioritas = @_prioritas LIMIT 1;";
							
		$ruang = $this->db2->query($ruang)->row();
		
		if (!$ruang){
			echo "<script>alert('Belum ada Jadwal Seleksi, mohon menunggu dan konfirmasi kembali nanti.');history.go(-1);</script>";
			
			
		}else{
			$kodeEvent = $ruang->kode;
			$totalPeserta = $ruang->totalPeserta;
			$ruangEvent = $ruang->ruang;
			
			$this->db2->where("kode", $kode);
			$this->db2->update("adis_smb_usr_pribadi", array('stsPribadiConfirm'=>1));
			
			$this->db2->where("kode", $kodeEvent);
			$this->db2->update("adis_event_smb", array('totalPeserta'=>$totalPeserta+1));
			
			$this->db2->where("kode", $kode);
			$this->db2->update("adis_smb_form", array(
								'updateUser'=>$kode,
								'updateTime'=>$date,
								'event'=>$kodeEvent,
								'ruangEvent'=>$ruangEvent,
								'stsEventConfirm'=>1
								));
								
			redirect ('/portal/formulir', 'refresh');
		}
	}
	
	function mSelectJadwal($kode){
		$sql = "SELECT u.nama as namaCmb, f.kode, f.nomor, f.bukaSmb, f.`event`, s.nama as progdi, j.nama as jalurCmb,
				e.kode, e.nama as namaEvent, e.periode, DAY(e.tanggal) as days, u.foto,
				e.tanggal, DAYNAME(e.tanggal) as hari, MONTHNAME(e.tanggal) as bulan, YEAR(e.tanggal) as tahun,  
				e.jalur,e.jamMasuk, e.ruang, 
				e.petugas, e.pewawancara, e.jamKeluar, e.statusJadwal,
				e.totalPeserta, e.pewawancara2, e.prioritas, r.kursiTes, r.nama as namaRuang, 
				g.nama as namaGedung, g.alamat, g.fax, w.nama as kabKota, g.kodePos, wa.nama as prop, g.tel
				FROM adis_smb_form f
				INNER JOIN adis_event_smb e ON e.kode = f.`event`
				INNER JOIN adis_smb_usr_pribadi u ON u.kode = f.kode
				INNER JOIN adis_buka_smb b ON b.kode = f.bukaSmb
				INNER JOIN adis_jalur_smb j ON j.kode = e.jalur
				INNER JOIN adis_prodi s ON s.kode = b.prodi
				INNER JOIN adis_ruang r ON r.kode = e.ruang
				INNER JOIN adis_gedung g ON g.kode = r.gedung
				LEFT JOIN adis_wil w ON w.kode = g.kabKota
				LEFT JOIN adis_wil wa ON wa.kode = g.prop
				WHERE f.kode = '$kode'";
		$sql = $this->db2->query($sql)->row();
		$this->smarty->assign('jadwal',$sql);
	}
	
	function mJadwalInterview($kode){
		$sql = "SELECT u.nama as namaCmb, f.kode, f.nomor, f.bukaSmb, f.`event`, s.nama as progdi, j.nama as jalurCmb,
				e.kode, e.nama as namaEvent, e.periode, DAY(e.tanggal) as days, u.foto,
				e.tanggal, DAYNAME(e.tanggal) as hari, MONTHNAME(e.tanggal) as bulan, YEAR(e.tanggal) as tahun,  
				e.jalur,e.jamMasuk, e.ruang, 
				e.petugas, e.pewawancara, e.jamKeluar, e.statusJadwal,
				e.totalPeserta, e.pewawancara2, e.prioritas
				FROM adis_smb_form f
				INNER JOIN adis_event_smb e ON e.kode = f.`event`
				INNER JOIN adis_smb_usr_pribadi u ON u.kode = f.kode
				INNER JOIN adis_buka_smb b ON b.kode = f.bukaSmb
				INNER JOIN adis_jalur_smb j ON j.kode = e.jalur
				INNER JOIN adis_prodi s ON s.kode = b.prodi
				WHERE f.kode = '$kode';";
		$sql = $this->db2->query($sql)->row();
		$this->smarty->assign('jadwal',$sql);
	}
	
	function mHasilSeleksi($kode){
		$sql = "SELECT f.kode as kode_smb, f.bukaSmb, f.nomor as no_smb, 
				f.stsEventInterviewPresent as hadirWwc, f.stsEventUsmPresent hadirUsm, 
				f.resultUsm as hasilUsm, f.resultInterview as hasilWwc, f.resultPept, f.stsResultGrade as hasilAkhir,
				f.stsResultPass as lulusTidak, f.stsResultRecommended as recomended, f.stsResultKet as ket, f.stsResultConfirm as konfirm,
				b.prodi, p.nama as progdi, u.nama as student
				FROM adis_smb_form f 
				INNER JOIN adis_smb_usr_pribadi u ON u.kode = f.kode
				INNER JOIN adis_buka_smb b ON b.kode = f.bukaSmb
				INNER JOIN adis_prodi p ON p.kode = b.prodi
				WHERE f.kode = '$kode'";
		$sql = $this->db2->query($sql)->row();
		$this->smarty->assign('hasil', $sql);
	}
	
	function mPrestasi($kode){
		$prestasi = $this->db2->get_where('adis_smb_usr_prestasi', array('erased'=>0,'smbUsr'=>$kode))->result();
		$this->smarty->assign("prestasi", $prestasi);
	}
	
	function mOrganisasi($kode){
		$organisasi = $this->db2->get_where('adis_smb_usr_org', array('erased'=>0,'smbUsr'=>$kode))->result();
		$this->smarty->assign("organisasi", $organisasi);
	}
	
	function selectType(){
		$typeBayar = $this->db2->query("SELECT * FROM adis_type WHERE kode LIKE '04.%'")->result();
		$this->smarty->assign("typeBayar", $typeBayar);
		
		$bankAkun = $this->db2->query("SELECT * FROM adis_type WHERE kode LIKE '05.1'")->row();
		$this->smarty->assign("bankAkun", $bankAkun);
		
		$sex = $this->db2->query("SELECT * FROM adis_type WHERE kode LIKE '03.%' AND erased = 0")->result();
		$this->smarty->assign("sex", $sex);
		
		$edu = $this->db2->query("SELECT * FROM adis_type WHERE kode LIKE '08.%' AND erased = 0")->result();
		$this->smarty->assign("edu", $edu);
		
		$eduSMA = $this->db2->query("SELECT * FROM adis_type WHERE kode LIKE '08.1%' AND erased = 0")->result();
		$this->smarty->assign("eduSMA", $eduSMA);
		
		$prodi = $this->db2->query("SELECT * FROM adis_prodi WHERE erased = 0")->result();
		$this->smarty->assign("prodi", $prodi);
		
	}
	
	function selectAgama(){		
		$agama = $this->db2->query("SELECT * FROM adis_type WHERE kode LIKE '02.%' ORDER BY kode = '02.I' DESC")->result();
		$this->smarty->assign("religion", $agama);
	}
	
	function selectPropinsi(){
		$wil = $this->db2->get_where('adis_wil', array('parent'=>NULL))->result();
		$this->smarty->assign("wil", $wil);
	}
	
	
}
