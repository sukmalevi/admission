<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Smb extends CUTI_Controller{
	function __construct(){
		parent::__construct();	
		//setlocale (LC_TIME, 'id_ID');
		setlocale (LC_TIME, 'INDONESIA');
		$this->auth = unserialize(base64_decode($this->session->userdata('cuti_parmad')));
		$this->host	= $this->config->item('base_url');
		
		if(! $this->auth) {header("Location: " . $this->host);}	
		
		$this->db2 = $this->load->database('second', TRUE); 
		
		$modul = "smb/";
		$this->smarty->assign('modul',$modul);
		$this->smarty->assign('host',$this->host);
		$this->load->model('madmisi');
		$this->load->model('msmb');
		
		$nama = $this->auth['name'];
		$this->smarty->assign('nama',$nama);
		
		$this->db2->query("SET lc_time_names = 'id_ID'");
	}
	
	function index() {
		
		}
		
	function smbCalon(){
		$uri = $this->uri->segment(3);
		$jalur = $this->input->post('jalur');
		$periode = $this->input->post('periode');
		$prodi = $this->input->post('prodi');
		
		$andQuery = "";
		
		if ($prodi != 'all'){
			$andQuery = "AND substr(f.bukaSmb, -4) = '$prodi'";
		}
		
		$this->madmisi->mSelectPeriode();
		
		if ($jalur != '' && $uri == "table"){
			
			$periode = implode(".",array($periode,$jalur));
			$this->db2->query("SET @num:=0;");
			$sql = "SELECT @num:=@num+1 AS 'No', f.kode as kode_smb, f.bukaSmb, f.nomor as no_smb, u.username as nama_cm , p.nama as progdi, 
			DAYNAME(u.createTime) as hari, DATE(u.createTime) as tanggal, 
			f.stsApplyPaid, f.stsApplyPaidConfirm, up.stsPribadiConfirm, f.stsEventConfirm, f.stsEventUsmPresent, up.foto,
			f.stsResultConfirm, f.stsMundurBeforeReapply,
			f.stsReapplyPaid, stsReapplyPaidConfirm, f.stsMundurAfterReapply, j.nama as n_jalur
			FROM adis_smb_form f 
			INNER JOIN adis_smb_usr u ON u.kode = f.kode
			INNER JOIN adis_buka_smb b ON b.kode = f.bukaSmb
			INNER JOIN adis_smb_usr_pribadi up ON up.kode = f.kode
			INNER JOIN adis_prodi p ON p.kode = b.prodi
			INNER JOIN adis_jalur_smb j ON j.kode = b.jalur
			WHERE SUBSTR(f.bukaSmb, 1, 13) = '$periode' $andQuery
			ORDER BY DATE(u.createTime) DESC";
			
			$sql = $this->db2->query($sql)->result();	
			$this->smarty->assign('sql',$sql);
			
			$this->smarty->assign('periode',$periode);
			$this->smarty->display('smb/tblCalonPeserta.html');	
			
		}else{
			$site = "Peserta";			
			$this->smarty->assign('site',$site);
			$this->smarty->assign('lokasi',"Daftar Peserta Ujian");
			$this->smarty->display('index.html');
		}
		
	}
	
	function smbJalur(){		
		$periode = $this->input->post('periode');
		$function = $this->input->post('fungsi');
		
		$jenjang = substr($periode, -5, 1);;
		
		$sql ="SELECT * FROM adis_jalur_smb WHERE erased = 0";
		$sql = $this->db2->query($sql)->result();
		
		$sqlProdi = "SELECT * FROM adis_prodi WHERE erased = 0 AND jenjang = $jenjang";
		$sqlProdi = $this->db2->query($sqlProdi)->result();
		
		$this->smarty->assign('fungsi',$function);
		$this->smarty->assign('periode',$periode);	
		$this->smarty->assign('jalur',$sql);		
		$this->smarty->assign('prodi',$sqlProdi);	
		$this->smarty->display('smb/selectJalur.html');	
	}
	
	function smbPay(){
		$this->load->model('mintegrasi');
		
		$uri = $this->uri->segment(3);
		$opt = $this->input->post("opt");
		$kode = $this->input->post("value");
		$jalur = $this->input->post("jalur");
		$periode = $this->input->post("periode");
		$prodi = $this->input->post('prodi');
		
		$andQuery = "";
		
		if ($prodi != 'all'){
			$andQuery = "AND substr(f.bukaSmb, -4) = '$prodi'";
		}
		
		$this->madmisi->mSelectPeriode();
		
		if ($uri == "confirm" && $opt == "confirm"){
			
			$qr= "	set @_bukasmb:=(select bukaSmb from adis_smb_form where kode='$kode');"; 
			$qr2= "	set @_tahun:=left(@_bukasmb,4); ";
			$qr3= "	set @_tahun_yy:=right(@_tahun,2); ";
			$qr4= "	set @_jenjang:=right(left(@_bukasmb,6),1); ";
			$qr5= "	set @_sesi:=right(left(@_bukasmb,8),1); ";
			$qr6= "	set @_kode:=concat(@_tahun_yy,@_jenjang,@_sesi); "; 
			$qr7= "	set @_concat := concat('%',@_kode,'%'); ";
			$qr8= "	set @_regOrder := (select max(nomor)+1 from adis_smb_form where nomor LIKE @_concat); ";
			
			$this->db2->query($qr);
			$this->db2->query($qr2);
			$this->db2->query($qr3);
			$this->db2->query($qr4);
			$this->db2->query($qr5);
			$this->db2->query($qr6);
			$this->db2->query($qr7);
			$this->db2->query($qr8);
			
			$echo = $this->db2->query("SELECT @_regOrder as data")->row();;
			
			//echo $echo->data;
			if (!$echo->data){
				$qr0= "	set @_regOrder := concat(@_kode,'0001')";
				$this->db2->query($qr0);
			}
			
			$qry_keu = $this->db2->query("SELECT nomor FROM adis_smb_form WHERE kode = '$kode';")->row_array();
			$in_nomor = $this->db2->query("UPDATE adis_smb_usr_keu SET no_tagihan_daftar = '".$qry_keu['nomor']."' WHERE smbUsr = '$kode';");
			
			$this->db2->query("UPDATE adis_smb_form SET stsApplyPaidConfirm = 1, nomor=@_regOrder WHERE kode='$kode';");
			
			
			/*** Integrasi data ke ASIK */
			$this->mintegrasi->validasi_pemb_admisi($kode);
			/*** end */
			
			//redirect ('/smb/smbPay', 'refresh');
			
		}else if($uri == "table" && $jalur != ""){
			$periode = implode(".",array($periode,$jalur));
			
			$sql ="SELECT f.kode as kode_smb, f.bukaSmb, f.nomor as no_smb, up.nama as nama_cm , p.nama as progdi, j.nama as n_jalur, t.nama as metode, f.stsApplyPaid as statusBayar,
			t2.nama as rekening, DAYNAME(f.applyBankTransferTime) as hari, f.applyBankTransferTime as tglPembayaran, 
			FORMAT (f.applyBankTransferAmount, 2) as jumlah, f.stsApplyPaidConfirm as confirm,
			f.applyBankTransferValidCode as kodeValidasi,
			k.buktiBayarPendaftaran, k.noRekPengirimPendaftaran, k.namaRekPengirimPendaftaran, k.noAtmCardPendaftaran
			FROM adis_smb_form f 
			INNER JOIN adis_smb_usr u ON u.kode = f.kode
			INNER JOIN adis_buka_smb b ON b.kode = f.bukaSmb
			INNER JOIN adis_prodi p ON p.kode = b.prodi		
			INNER JOIN adis_smb_usr_pribadi up ON up.kode = f.kode
			INNER JOIN adis_jalur_smb j ON j.kode = b.jalur
			INNER JOIN adis_smb_usr_keu k ON k.smbUsr = f.kode
			LEFT JOIN adis_type t ON t.kode = f.applyBankTransferType
			LEFT JOIN adis_type t2 ON t2.kode = f.applyBankAccountType 
			WHERE SUBSTR(f.bukaSmb, 1, 13) = '$periode' $andQuery
			ORDER BY u.createTime DESC";
			$sql = $this->db2->query($sql)->result();	
			$this->smarty->assign('sql',$sql);	
			
			$this->smarty->display('smb/tblPay.html');
		}else{	
			$site = "Pembayaran";			
			$this->smarty->assign('site',$site);
			$this->smarty->assign('lokasi',"Daftar Pembayaran Calon Peserta");
			$this->smarty->display('index.html');
		
		}
	}
	
	function smbEvent(){
		
		if ($this->auth['level'] != "99" && $this->auth['level'] != "1"){ redirect('/home','refresh');}
		
		$uri = $this->uri->segment(3);
		$jalur = $this->input->post('jalur');
		$periode = $this->input->post('periode');
		$prodi = $this->input->post('prodi');
		
		$andQuery = "";
		
		if ($prodi != 'all'){
			$andQuery = "AND substr(f.bukaSmb, -4) = '$prodi'";
		}
		
		$this->madmisi->mSelectPeriode();
		
		if ($jalur != '' && $uri == "table"){
			
			$periode = implode(".",array($periode,$jalur));
		
			$sql ="SELECT f.kode as kode_smb, f.bukaSmb, f.nomor as no_smb, up.nama as nama_cm , p.nama as progdi, j.nama as n_jalur, f.`event` as event,
			DAYNAME(e.tanggal) as hari, DAY(e.tanggal) as tanggal, MONTHNAME(e.tanggal) as bulan, YEAR(e.tanggal) as tahun,
			e.tanggal as tglEvent, r.nama as ruang, g.nama as namaGedung, g.alamat, g.kabKota, g.prop, g.kodePos,
			g.tel, g.fax, e.nama as n_periode, f.interviewPhone, 
			DAY(v.tanggal) as tanggalPhone, DAYNAME(v.tanggal) as hariPhone, MONTHNAME(v.tanggal) as bulanPhone, YEAR(v.tanggal) as tahunPhone,
			e.nama as eventName, e.jamMasuk, e.jamKeluar, e.petugas, e.pewawancara
			FROM adis_smb_form f 
			INNER JOIN adis_smb_usr u ON u.kode = f.kode
			INNER JOIN adis_buka_smb b ON b.kode = f.bukaSmb
			INNER JOIN adis_prodi p ON p.kode = b.prodi		
			INNER JOIN adis_smb_usr_pribadi up ON up.kode = f.kode
			INNER JOIN adis_jalur_smb j ON j.kode = b.jalur
			LEFT JOIN adis_event_smb e ON e.kode = f.`event`
			LEFT JOIN adis_personal o ON o.kode = e.petugas
			LEFT JOIN adis_personal w ON w.kode = e.pewawancara					
			LEFT JOIN adis_ruang r ON r.kode = e.ruang
			LEFT JOIN adis_gedung g ON g.kode = r.gedung
			LEFT JOIN adis_via_phone v ON v.kodeMahasiswa = f.kode
			WHERE f.stsApplyPaidConfirm = 1 AND SUBSTR(f.bukaSmb, 1, 13) = '$periode' $andQuery
			ORDER BY u.createTime DESC";
			$sql = $this->db2->query($sql)->result();
			$sql1 = $this->db2->query("SELECT COUNT(*) as total FROM adis_smb_form WHERE stsApplyPaidConfirm = 1 AND SUBSTR(bukaSmb, 1, 13) = '$periode' ")->row();
			
			$this->smarty->assign('sql',$sql);	
			$this->smarty->assign('total',$sql1);	
			$this->smarty->display('smb/tblEvent.html');
		}else{
			$site = "Event";			
			$this->smarty->assign('site',$site);
			$this->smarty->assign('lokasi',"Daftar Jadwal Seleksi Peserta");	
			$this->smarty->display('index.html');
		}
	}
	
	function jadwal(){		
		$this->load->model('mportal');
		
		$opt = $this->uri->segment(3);
		$nomor = $this->uri->segment(4);
		$db = $this->db2->query("SELECT kode FROM adis_smb_form WHERE nomor = '$nomor'")->row();
		$kode = $db->kode;
		$this->mportal->mSelectJadwal($kode);
		
		$site = "jadwal";
			
		$this->smarty->assign("site", $site);
		$this->smarty->assign('lokasi',"Hasil Seleksi Peserta Ujian");		
		$this->smarty->display("index.html");
	}
	
	function smbSeleksi(){
		$uri = $this->uri->segment(3);
		$jalur = $this->input->post('jalur');
		$periode = $this->input->post('periode');
		$prodi = $this->input->post('prodi');
		
		$andQuery = "";
		
		if ($prodi != 'all'){
			$andQuery = "AND substr(f.bukaSmb, -4) = '$prodi'";
		}
		
		$this->madmisi->mSelectPeriode();
		
		if ($jalur != '' && $uri == "table"){
			
			$periode = implode(".",array($periode,$jalur));
			
			$sql ="SELECT f.kode as kode_smb, f.bukaSmb, f.nomor as no_smb, up.nama as nama_cm, up.statusSaudara, p.nama as progdi, j.nama as n_jalur, f.`event` as event, up.adaSaudara, up.confirmSaudara,
			f.stsEventInterviewPresent as hadirWwc, f.stsEventUsmPresent hadirUsm, 
			f.resultUsm as hasilUsm, f.resultInterview as hasilWwc, f.resultPept, f.stsResultGrade as hasilAkhir,
			f.stsResultPass as lulusTidak, f.stsResultRecommended as recomended, f.stsResultKet as ket, f.stsResultConfirm as konfirm, 
			e.nama as eventName, e.jamMasuk, e.jamKeluar, e.petugas, e.pewawancara,
			DAYNAME(e.tanggal) as hari, e.tanggal as tglEvent, r.nama as ruang, g.nama as namaGedung, g.alamat, g.kabKota, g.prop, g.kodePos,
			g.tel, g.fax, e.nama as n_periode
			FROM adis_smb_form f 
			INNER JOIN adis_smb_usr u ON u.kode = f.kode
			INNER JOIN adis_buka_smb b ON b.kode = f.bukaSmb
			INNER JOIN adis_prodi p ON p.kode = b.prodi		
			INNER JOIN adis_smb_usr_pribadi up ON up.kode = f.kode
			INNER JOIN adis_jalur_smb j ON j.kode = b.jalur
			LEFT JOIN adis_event_smb e ON e.kode = f.`event`
			LEFT JOIN adis_personal o ON o.kode = e.petugas
			LEFT JOIN adis_personal w ON w.kode = e.pewawancara					
			LEFT JOIN adis_ruang r ON r.kode = e.ruang
			LEFT JOIN adis_gedung g ON g.kode = r.gedung
			WHERE f.stsApplyPaidConfirm = 1 AND SUBSTR(f.bukaSmb, 1, 13) = '$periode' $andQuery
			ORDER BY u.createTime DESC";
			
			$sql = $this->db2->query($sql)->result();	
			$this->smarty->assign('sql',$sql);	
			
			$this->smarty->display('smb/tblSeleksi.html');
			
		}else{
			$site = "Seleksi";			
			$this->smarty->assign('site',$site);
			$this->smarty->assign('lokasi',"Hasil Seleksi Ujian");
			$this->smarty->display('index.html');
		}
	}
	
	function smbSeleksiForm(){
		$opt = $this->input->post("opt");
		$val = $this->input->post("val");
		$uri = $this->uri->segment(3);
		
		$sql = "SELECT f.kode as kode_smb, f.bukaSmb, f.nomor as no_smb, 
				f.stsEventInterviewPresent as hadirWwc, f.stsEventUsmPresent hadirUsm, 
				f.resultUsm as hasilUsm, f.resultInterview as hasilWwc, f.resultPept, f.stsResultGrade as hasilAkhir,
				f.stsResultPass as lulusTidak, f.stsResultRecommended as recomended, f.stsResultKet as ket, f.stsResultConfirm as konfirm
				FROM adis_smb_form f 
				INNER JOIN adis_smb_usr u ON u.kode = f.kode
				WHERE f.nomor = ".$val."
				ORDER BY u.createTime DESC";
		$sql = $this->db2->query($sql)->row();
		
		
		$this->smarty->assign('hadirUjian', $sql->hadirUsm);
		$this->smarty->assign('hadirWwc', $sql->hadirWwc);
		$this->smarty->assign('hasilAkhir', $sql->hasilAkhir);
		$this->smarty->assign('recomended', $sql->recomended);	
		$this->smarty->assign('lulusTidak', $sql->lulusTidak);		
		
		$this->smarty->assign('opt', $opt);
		$this->smarty->assign('hasil', $sql);
		$this->smarty->assign('val', $val);
		$this->smarty->display("smb/formPenilaian.html");
	}
	
	function konfirmHasilSeleksi(){
		$this->load->model('mintegrasi');
		$kode = $this->input->post("kode");
		$uri = $this->uri->segment(3);
		
		if ($kode != "" && $uri == "save"){
			$this->db2->where('kode', $kode);
			$this->db2->update('adis_smb_form', array('stsEventUsmPresent'=>$this->input->post('tpaAda'),
									'stsEventInterviewPresent'=>$this->input->post('wwAda'),
									'stsResultConfirm'=>1,
									//'resultPept'=>$this->input->post('pept'),
									'resultUsm'=>$this->input->post('tpa'),
									'resultInterview'=>$this->input->post('wawancara'),
									'stsResultRecommended'=>$this->input->post('recoYa'),
									'stsResultPass'=>$this->input->post('luYa'),
									'stsResultGrade'=>$this->input->post('grade'),
									'stsResultKet'=>$this->input->post('ket')
								));
			
			$idActivity = "13.5";
			$this->activity($kode, $idActivity, $kode);
			
			/*** Integrasi data ke ASIK */
			$this->mintegrasi->insert_mahasiswa($kode);
			/*** end */
		}
	}
	
	function activity($email, $idActivity, $user){
		$kode = uniqid();
		$date = date("Y-m-d H:i:s");
		$activity = $this->db2->query("SELECT nama, kode FROM adis_type WHERE kode = '$idActivity'")->row();
		$this->db2->insert('adis_activity', array('kode'=>$kode,
								'id_activity'=>$activity->kode,
								'created_date'=>$date,
								'created_user'=>$user,
								'nama_activity'=>$activity->nama,
								'status_activity'=>0,
								'id_cmb'=>$email
								));
	}
	
	function smbDaftarUlang(){
	
		$this->load->model('mintegrasi');
		
		$uri = $this->uri->segment(3);
		$jalur = $this->input->post('jalur');
		$periode = $this->input->post('periode');
		$opt = $this->input->post('opt');
		$val = $this->input->post('val');
		$prodi = $this->input->post('prodi');
		
		$andQuery = "";
		
		if ($prodi != 'all'){
			$andQuery = "AND substr(f.bukaSmb, -4) = '$prodi'";
		}

		
		$this->madmisi->mSelectPeriode();
		if ($uri == "confirm" && $opt == "confirm"){
			
			$this->db2->where('nomor',$val);
			$this->db2->update('adis_smb_form', array('stsReapplyPaidConfirm'=>1));

			$kode = "SELECT kode, nomor FROM adis_smb_form WHERE nomor = $val";
			$kode = $this->db->query($kode)->row();	
			$kode = $kode->kode;		
			//$this->smbNim($val, $opt, $uri);
			
			/**** Integrasi data pembayaran daftar ulang ke ASIK ****/
			$this->mintegrasi->validasi_pembayaran($kode);
			/** end */
			$this->generateNIM($kode);

			/**** Integrasi data pembayaran daftar ulang ke ASIK ****/
			$this->mintegrasi->update_nim($kode);
			/** end */
			
			/**** Integrasi data Mahasiswa Lulus ke ASIK ****/
			$this->mintegrasi->upload_mahasiswa($kode);
			/** end */
			
			$html ='<div id="accordion1" class="panel-group">
                      <div class="panel panel-default">
                        <div class="panel-heading">
                          <h4 class="panel-title">CONFIRMED</h4>
                        </div>
					  </div>
					</div>';
			
			$this->smarty->display('string:'.$html);
			
		}else if ($jalur != '' && $uri == "table"){
			
			$periode = implode(".",array($periode,$jalur));
			
			$sql ="SELECT f.kode as kode_smb, f.bukaSmb, f.nomor as no_smb, up.nama as nama_cm , p.nama as progdi, j.nama as n_jalur,
			f.stsReapplyPaid, t.nama as akunBank, f.reapplyBankTransferAmount, 
			DAYNAME(f.reapplyBankTransferTime) as day,f.reapplyBankTransferTime, 
			DAY(f.reapplyBankTransferTime) as tgl,MONTHNAME(f.reapplyBankTransferTime) as bln,YEAR(f.reapplyBankTransferTime) as thn,
			t2.nama as transferTipe, f.reapplyBankTransferValidCode,
			f.reapplySenderBankAccountName, f.reapplySenderBankAccountNumber, f.stsReapplyPaidConfirm,			
			k.buktiBayarDaftarUlang, k.noRekPengirimDaftarUlang, k.namaRekPengirimDaftarUlang, k.totalBiayaDaftarUlang,
			k.noAtmCardPendaftaran, k.noAtmCardDaftarulang
			FROM adis_smb_form f 
			INNER JOIN adis_smb_usr u ON u.kode = f.kode
			INNER JOIN adis_buka_smb b ON b.kode = f.bukaSmb
			INNER JOIN adis_prodi p ON p.kode = b.prodi		
			INNER JOIN adis_smb_usr_keu k ON k.smbUsr = f.kode
			LEFT JOIN adis_smb_usr_pribadi up ON up.kode = f.kode
			INNER JOIN adis_jalur_smb j ON j.kode = b.jalur	
			LEFT JOIN adis_type t ON t.kode = f.reapplyBankAccountType 
			LEFT JOIN adis_type t2 ON t2.kode = f.reapplyBankTransferType 
			WHERE f.stsResultConfirm = 1 AND SUBSTR(f.bukaSmb, 1, 13) = '$periode' $andQuery
			ORDER BY u.createTime DESC";
			$sql = $this->db2->query($sql)->result();
			$this->smarty->assign('sql',$sql);	
			
			$this->smarty->display('smb/tblDaftarUlang.html');
		}else{
			$site = "DaftarUlang";			
			$this->smarty->assign('site',$site);
			$this->smarty->assign('lokasi',"Daftar Peserta Daftar Ulang");	
			$this->smarty->display('index.html');
		}
	}
	
	function detailDaftarUlang(){
		$this->load->model('mportal');		
		$nomor = $this->uri->segment(3);
		
		$kodew = $this->db->query("SELECT kode, SUBSTR(bukaSmb,12, 2) as jalur FROM adis_smb_form WHERE nomor = '$nomor'")->row();
		$kode = $kodew->kode;
		
		$this->mportal->mCmb($kode);
		$this->mportal->selectType();
		
		$qr = $this->db2->query("SELECT nama FROM adis_smb_usr_kel WHERE smbUsr = '$kode' AND erased = 0");
		$saudara = $qr->num_rows();
		$this->smarty->assign('saudara',$saudara);
		$this->smarty->assign('jalur',$kodew->jalur);
		
		$this->mportal->mPaidDaftarUlang($kode,$saudara);
		$site = "DaftarUlangConfirmed";			
		$this->smarty->assign('site',$site);
		$this->smarty->assign('lokasi',"Detail Transaksi Daftar Ulang");	
		$this->smarty->display('index.html');
	}

	function generateNIM($kode){
			$val = $this->db->query("SELECT nomor FROM adis_smb_form WHERE kode = '$kode';")->row();
			$val = $val->nomor;
			
			$sql_p= "SET @_periode:=(SELECT LEFT(bukaSmb,8) FROM adis_smb_form WHERE nomor = '$val');";
			$sql  = "set @_bukaSmb:=(SELECT bukaSmb FROM adis_smb_form WHERE nomor = '$val');";
			$sql2 = "set @_tahun:=left(@_bukasmb,4);";
			$sql3 = "set @_tahun_yy:=right(@_tahun,2);";
			$sql4 = "set @_jenjang:=right(left(@_bukasmb,6),1);";
			$sql5 = "set @_periode:=left(@_bukasmb,8);";
			$sql6 = "set @_sesi:=right(left(@_bukasmb,8),1);";
			$sql7 = "set @_prodi:=right(@_bukasmb,4); ";
			// $sql8 = "set @_nimOrder:=(select max(nimOrder) from adis_smb_form where bukaSmb=@_bukasmb);";			
			$sql8 = "set @_nimOrder:=(select max(nimOrder) from adis_smb_form 
					where LEFT(bukaSmb,8)=@_periode AND RIGHT(bukaSmb,4)=@_prodi);";
			$sql9 = "set @_nim:=concat(@_jenjang,@_tahun_yy,@_sesi,right(@_prodi,2),LPAD((@_nimOrder+1),3,0));";
			$sql10= "update adis_smb_form set nimOrder = (@_nimOrder+1) where nomor='$val';";
			
			$this->db2->query($sql_p);
			$this->db2->query($sql);
			$this->db2->query($sql2);
			$this->db2->query($sql3);
			$this->db2->query($sql4);
			$this->db2->query($sql5);
			$this->db2->query($sql6);
			$this->db2->query($sql7);
			$this->db2->query($sql8);
			$this->db2->query($sql9);
			$this->db2->query($sql10);
		
			$qry = "UPDATE adis_smb_form SET nim  = @_nim WHERE nomor='$val'";
			$this->db2->query($qry);
	}
	
	function smbNim($val = "", $opt = "", $uri = ""){
	if ($this->auth['level'] != "99" && $this->auth['level'] != "1"){ redirect('/home','refresh');}
		
		if ($val == "" || $opt == "" || $uri == ""){
			$uri = $this->uri->segment(3);
			$opt = $this->input->post('opt');
			$val = $this->input->post('val');
		}
		
		$jalur = $this->input->post('jalur');
		$periode = $this->input->post('periode');
		$prodi = $this->input->post('prodi');
		
		$andQuery = "";
		
		if ($prodi != 'all'){
			$andQuery = "AND substr(f.bukaSmb, -4) = '$prodi'";
		}
		
		$this->madmisi->mSelectPeriode();
		
		if (($opt == "generate" && $uri == "confirm") || ($opt == "confirm" && $uri == "confirm")){
			
			$sql  = "set @_bukaSmb:=(SELECT bukaSmb FROM adis_smb_form WHERE nomor = '$val');";
			$sql2 = "set @_tahun:=left(@_bukasmb,4);";
			$sql3 = "set @_tahun_yy:=right(@_tahun,2);";
			$sql4 = "set @_jenjang:=right(left(@_bukasmb,6),1);";
			$sql5 = "set @_periode:=left(@_bukasmb,8);";
			$sql6 = "set @_sesi:=right(left(@_bukasmb,8),1);";
			$sql7 = "set @_prodi:=right(@_bukasmb,4); ";
			$sql8 = "set @_nimOrder:=(select max(nimOrder) from adis_smb_form where bukaSmb=@_bukasmb);";
			$sql9 = "set @_nim:=concat(@_jenjang,@_tahun_yy,@_sesi,right(@_prodi,2),LPAD((@_nimOrder+1),3,0));";
			$sql10 = "update adis_smb_form set nimOrder = (@_nimOrder+1) where nomor='$val';";
			
			$this->db2->query($sql);
			$this->db2->query($sql2);
			$this->db2->query($sql3);
			$this->db2->query($sql4);
			$this->db2->query($sql5);
			$this->db2->query($sql6);
			$this->db2->query($sql7);
			$this->db2->query($sql8);
			$this->db2->query($sql9);
			$this->db2->query($sql10);
		
			$qry = "UPDATE adis_smb_form SET nim  = @_nim WHERE nomor='$val'";
			$this->db2->query($qry);
			
			if ($opt == "generate"){
			
				$row = $this->db2->query("SELECT nim FROM adis_smb_form where nomor = '$val';")->row();
				$this->smarty->assign('row',$row);
				
				$html ='<div id="accordion1" class="panel-group">
						  <div class="panel panel-default">
							<div class="panel-heading">
							  <h4 class="panel-title">{$row->nim}</h4>
							</div>
						  </div>
						</div>';
				
				$this->smarty->display('string:'.$html);	
			}
			
		}else if ($jalur != '' && $uri == "table"){
			
			$periode = implode(".",array($periode,$jalur));
			
			$sql ="SELECT f.kode as kode_smb, f.bukaSmb, f.nomor as no_smb, up.nama as nama_cm , p.nama as progdi, j.nama as n_jalur,
			f.stsResultPass, f.stsReapplyPaidConfirm, f.nimOrder, f.nim, f.stsMundurAfterReapply
			FROM adis_smb_form f 
			INNER JOIN adis_smb_usr u ON u.kode = f.kode
			INNER JOIN adis_buka_smb b ON b.kode = f.bukaSmb
			INNER JOIN adis_prodi p ON p.kode = b.prodi		
			LEFT JOIN adis_smb_usr_pribadi up ON up.kode = f.kode
			INNER JOIN adis_jalur_smb j ON j.kode = b.jalur	 
			WHERE f.stsReapplyPaidConfirm = 1 AND f.stsResultPass = 1 AND SUBSTR(f.bukaSmb, 1, 13) = '$periode' $andQuery
			ORDER BY u.createTime DESC";
			$sql = $this->db2->query($sql)->result();
			$this->smarty->assign('sql',$sql);	
			
			$sql1 = $this->db2->query("SELECT COUNT(*) as total FROM adis_smb_form WHERE stsReapplyPaidConfirm = 1 AND stsResultPass = 1 AND SUBSTR(bukaSmb, 1, 13) = '$periode' ")->row();
			$this->smarty->assign('total',$sql1);	
			
			$this->smarty->display('smb/tblNim.html');
		}else{
			$site = "Nim";			
			$this->smarty->assign('site',$site);
			$this->smarty->assign('lokasi',"Generate NIM Mahasiswa");	
			$this->smarty->display('index.html');
		}
	}
		
	function admisiBukaSeleksi(){
		$this->db2->query("SET lc_time_names = 'id_ID'");
		$sql = "SELECT b.erased as status, b.kode as id_buka, b.nama as nama_buka, b.periode as periode_buka, 
		DAYNAME(b.tanggalBuka) as hariBuka, DAYNAME(b.tanggalTutup) as hariTutup, b.tanggalBuka, b.tanggalTutup,
		p.nama as nama_periode, j.nama as nama_jalur, pr.nama as nama_prodi,
		FROM adis_buka_smb b
		INNER JOIN adis_periode p ON b.periode = p.kode
		INNER JOIN adis_jalur_smb j ON b.jalur = j.kode
		INNER JOIN adis_prodi pr ON b.prodi = pr.kode;";
		$sql = $this->db2->query($sql)->result();
		$site = "Jadwal";			
		$this->smarty->assign('site',$site);	
		$this->smarty->assign('sql',$sql);	
		$this->smarty->display('index.html');	
	}
	
	function admisiPeriode(){
		$sql ="SELECT * FROM adis_periode";
		$sql = $this->db2->query($sql)->result();		
		$site = "Event";			
		$this->smarty->assign('site',$site);	
		$this->smarty->assign('sql',$sql);	
		$this->smarty->display('index.html');	
	}
	
	function admisiJadwalSeleksi(){
		$uri = $this->uri->segment(3);
		$periode = $this->input->post('periode');
		
		if ($uri == "table" && $periode != ""){
			$this->db2->query("SET lc_time_names = 'id_ID'");
			$sql = "SELECT b.erased as status, b.kode as id_event, b.nama as nama_event, 
			DAYNAME(b.tanggal) as hariEvent, b.tanggal, b.jamMasuk, b.jamKeluar, p.jenjangType as jenjang, r.gedung as gedung_event, r.kode as kode_kelas,
			p.nama as nama_periode, p.tahun as tahun, r.nama as nama_ruang, j.nama as nama_jalur, o.nama as nama_petugas, c.nama as nama_pww
			FROM adis_event_smb b
			INNER JOIN adis_periode p ON b.periode = p.kode
			INNER JOIN adis_ruang r ON b.ruang = r.kode
			INNER JOIN adis_jalur_smb j ON b.jalur = j.kode
			INNER JOIN adis_personal o ON b.petugas =o.kode
			INNER JOIN adis_personal c ON b.pewawancara =c.kode
			WHERE b.periode = '$periode' ORDER BY id_event";
			$sql = $this->db2->query($sql)->result();
			
			$this->smarty->assign('sql',$sql);	
			$this->smarty->display('admisi/tblJadwal.html');	
		}else{
			$sql ="SELECT * FROM adis_periode";
			$sql = $this->db2->query($sql)->result();		
			$site = "Event";			
			$this->smarty->assign('site',$site);	
			$this->smarty->assign('sql',$sql);	
			$this->smarty->display('index.html');
		}
	}
	
	function mundurDiri(){
		$kode = $this->uri->segment(3);
		$opt = $this->uri->segment(4);
		$uri = $this->uri->segment(5);
		//$oper = $this->uri->segment(6);
		
		$opt2 = $this->input->post("opt");
		
				
		if ($kode != "" && $opt == "mundur"){	
			
			
			
		
			if ($uri != 'save'){
			
				if ($uri){
					$this->load->model('msmb');
					$this->msmb->mProfil($kode,'asu');
				}
				
				if (!$uri){
					$this->load->model('mportal');
					$this->mportal->mCmb($kode);				
				}
				
				$site = "Mundur";			
				$this->smarty->assign('lokasi',"Form Pengunduran Diri");	
				$this->smarty->assign('site',$site);	
				$this->smarty->display('index.html');
				
			}else{
				if ($opt2 == "save"){
					$this->msmb->mMundur($kode);
				}
				$this->smbNim();
			}
		}
		
	}
	
	function viaPhone(){
		$kode = $this->input->post('val');
		$opt = $this->input->post('opt');
		$uri = $this->uri->segment(3);
		$uri2 = $this->uri->segment(4);
		
		if ($uri == "add" && $opt == "phone"){
			$sql = "SELECT kode, nama FROM adis_smb_usr_pribadi WHERE kode = '$kode'";
			$sql = $this->db2->query($sql)->row();
			
			$this->madmisi->mSelectPetugas();
			
			$this->smarty->assign('calon',$sql);
			$this->smarty->display('smb/formViaPhone.html');
		}else if ($uri == "detail" && $opt == "phoned"){
			$sql = "SELECT  kode, nama FROM adis_smb_usr_pribadi WHERE kode = '$kode'";
			$sql = $this->db2->query($sql)->row();
			
			$sql2 = "SELECT  v.id, v.tanggal, v.jamMulai, v.jamSelesai, v.no_hp1, v.no_hp2, v.`status`, p.nama as pww1, p2.nama as pww2 
					FROM adis_via_phone v
					LEFT JOIN adis_personal p ON p.kode = v.idPewawancara
					LEFT JOIN adis_personal p2 ON p2.kode = v.idPewawancara2
					WHERE kodeMahasiswa = '$kode';";
			$sql2 = $this->db2->query($sql2)->row();
						
			$this->smarty->assign('calon',$sql);
			$this->smarty->assign('phone',$sql2);
			$this->smarty->display('smb/formViaPhoned.html');
			
		}else{
			if($uri2 == "do"){
				$unik = uniqid();
				$date = date("Y-m-d H:i:s");
				$kode = $this->input->post('kode');
				
				$this->db2->insert("adis_via_phone", array(
					"id"=>$unik,
					"createTime"=>$date,
					"createdUser"=>$this->auth['name'],
					"kodeMahasiswa"=>$kode,
					"tanggal"=>$this->input->post('tanggal'),
					"jamMulai"=>$this->input->post('jamMulai'),
					"jamSelesai"=>$this->input->post('jamSelesai'),
					"no_hp1"=>$this->input->post('noTelp'),
					"no_hp2"=>$this->input->post("noHp"),
					"idPewawancara"=>$this->input->post("pww1"),
					"idPewawancara2"=>$this->input->post("pww2"),
					"status"=>$this->input->post("status"),
				));
				
				$this->db2->where('kode',$kode);
				$this->db2->update("adis_smb_form", array('interviewPhone'=>1));
			}
			redirect('/smb/smbEvent','refresh');
		}
	}
	
	function exportToExcel(){
		$periode = $this->uri->segment(4);
		$modul = $this->uri->segment(3);
		$sql = $this->msmb->modelCalon($periode);
		$this->export->to_excel($sql, 'nameForFile'); 
	}
	
	function profil(){
		$this->load->model('mportal');
		
		$kode = $this->uri->segment(3);
		$val = $this->input->post('val');
		$id = $this->input->post('id');
		$opt = $this->input->post('opt');

		if ($kode == "confirm" && $val != ''){
			if ($opt == 'confirm'){$statusSau = 7;}else if ($opt == 'tolak'){$statusSau = 9;}
				$this->db2->where('kode',$val);
				$this->db2->update('adis_smb_usr_pribadi', array('confirmSaudara' => $statusSau));

				$this->mportal->mSelectSaudara($val);			
				$this->mportal->mCmb($val);
				$this->smarty->display('smb/tblKeluarga.html');
		}else {

			$kode = $this->db2->query("SELECT kode, stsEventConfirm FROM adis_smb_form WHERE nomor = '$kode'")->row();
			$kode = $kode->kode;
			
			$this->mportal->mCmb($kode);
			$this->mportal->mProfil($kode);
			$this->mportal->mSelectOrtu($kode);
			$this->mportal->mSelectPendidikan($kode);
			$this->mportal->mSelectSaudara($kode);
			$this->mportal->mPrestasi($kode);
			$this->mportal->mOrganisasi($kode);
			$site = "Profil";
			$this->smarty->assign('lokasi',"Profil Mahasiswa Baru");	
			$this->smarty->assign("site", $site);		
			$this->smarty->display("index.html");
		}
	}

	function formulir(){
		$this->load->model('mportal');
		
		$kode = $this->uri->segment(3);
		
		$kode = $this->db2->query("SELECT kode, stsEventConfirm FROM adis_smb_form WHERE nomor = '$kode'")->row();
		
		if ($kode->stsEventConfirm == '0') {
			// What happens when the CAPTCHA was entered incorrectly
			echo '<script>alert("Calon Mahasiswa belum melengkapi Data Diri, Silahkan Kembali!");history.go(-1);</script>';
			// die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
				 // "(reCAPTCHA said: " . $resp->error . ")");
		}else{
		
			
			$kode = $kode->kode;
			
			$this->mportal->mProfil($kode);
			$this->mportal->mSelectPendidikan($kode);		
			$this->mportal->mOrganisasi($kode);
			$this->mportal->mPrestasi($kode);
			$this->mportal->mSelectOrtu($kode);
			$this->mportal->mSelectSaudara($kode);
			
			$site = "Formulir";			
			$this->smarty->assign('lokasi',"Form Pendaftaran Mahasiswa Baru");	
			$this->smarty->assign('site',$site);	
			$this->smarty->display('index.html');	
		
		}
	}

	function tolakBayar(){
		$uri = $this->uri->segment(3);
		$val = $this->input->post('val');
		$opt = $this->input->post('opt');

		if ($uri == 'save' && $opt == 'save' ){
			if ($val != ''){
				$this->db->where('smbUsr', $val);
				$this->db->update('adis_smb_usr_keu', array(
								'tolakDU'=>1,
								'pesanTolakDU'=>$this->input->post('pesan')
								));

				$this->db->where('kode', $val);
				$this->db->update('adis_smb_form', array('stsReapplyPaid'=>0));
			}
		$status = 'daftarUlang';
		$pesan = $this->input->post('pesan');
		$this->mail($val, $status, $pesan);

		redirect('/smb/smbDaftarUlang','refresh');

		}else if ($uri == 'tolak'){

			$qry = "SELECT f.kode, f.nomor, p.nama FROM adis_smb_form f 
					INNER JOIN adis_smb_usr_pribadi p ON f.kode = p.kode 
					WHERE nomor = '$val'";
			$qry = $this->db->query($qry)->row();

			$this->smarty->assign('mah', $qry);
			$this->smarty->display('smb/formTolakBayar.html');
		}
	}
	

	function tolakBayarDaftar(){
		$uri = $this->uri->segment(3);
		$val = $this->input->post('val');
		$opt = $this->input->post('opt');

		if ($uri == 'save' && $opt == 'save' ){
			if ($val != ''){
				$this->db->where('smbUsr', $val);
				$this->db->update('adis_smb_usr_keu', array(
								'tolakPendaftaran'=>1,
								'pesanTolakP'=>$this->input->post('pesan')
								));

				$this->db->where('kode', $val);
				$this->db->update('adis_smb_form', array('stsApplyPaid'=>0));
			}
		$status = 'daftar';
		$pesan = $this->input->post('pesan');
		$this->mail($val, $status, $pesan);

		redirect('/smb/smbDaftarUlang','refresh');

		}else if ($uri == 'tolak'){

			$qry = "SELECT f.kode, f.nomor, p.nama FROM adis_smb_form f 
					INNER JOIN adis_smb_usr_pribadi p ON f.kode = p.kode 
					WHERE nomor = '$val'";
			$qry = $this->db->query($qry)->row();

			$this->smarty->assign('mah', $qry);
			$this->smarty->display('smb/formTolakBayarDaftar.html');
		}
	}

	function mail($email, $status, $pesan){
		$config = Array(
						  'protocol' => 'smtp',
						  'smtp_host' => 'students.paramadina.ac.id',
						  'smtp_port' => 25,
						  'smtp_user' => 'admin@students.paramadina.ac.id', // change it to yours
						  'smtp_pass' => 'v3mb424x1971', // change it to yours
						  'mailtype' => 'html',
						  'charset' => 'iso-8859-1',
						  'wordwrap' => TRUE
					);	
		if ($status == 'daftarUlang' ){
			$var = "Daftar Ulang";
		}else{
			$var = "Registrasi";
		}
				
					$this->load->library('email', $config);
					$this->email->from('admin@admisission.paramadina.ac.id', "Humas Paramadina");
					$this->email->to($email);
					$this->email->cc("rahmad.syalevi@paramadina.ac.id");
					$this->email->subject("Penolakan Pembayaran ".$var);
					$this->email->message("<p>".$pesan."</p><p>Silahkan buka portal http://admission.paramadina.ac.id untuk melakukan konfirmasi pembayaran kembali.</p><p>Terima Kasih</p>");
						
					$data['message'] = "Sorry Unable to send email...";	
					if($this->email->send()){					
						$data['message'] = "Mail sent...";			
					}
	}
}
