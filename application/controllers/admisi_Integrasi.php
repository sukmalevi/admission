<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admisi_Integrasi extends CI_Controller{
	function __construct(){
		parent::__construct();	
		date_default_timezone_set("Asia/Jakarta");
		$this->db2 = $this->load->database('second', TRUE);
		// $this->db4 = $this->load->database('quart', TRUE);
		$this->auth = unserialize(base64_decode($this->session->userdata('cuti_parmad')));
		$this->host	= $this->config->item('base_url');
		$this->smarty->assign('host',$this->host);
		$host = $this->host;
		
	}
	
	function index() {
	
			
	}
	
	function json_tag_daftar(){ 
		
		$host_asik = "https://asik.paramadina.ac.id/gt/uat/gtpembayaran/";
		$service_url = $host_asik.'index.php?mod=service&sub=pembayaranAdmisi&act=rest&typ=rest';
		
		$username = 'registrasi';
		$password = 'regGT2015';
		
		$kode = "rahmadsyalevi@outlook.com"; //$this->input->post("kode");
		$sql = "SELECT nomor as mahasiswaNoPendaftaran, d.kode_asik as mahasiswaProdiKode,
				j.kode_asik as mahasiswaJalurKode, REPLACE(LEFT(bukaSmb, 6),'.','') as mahasiswaPeriodeKode, p.nama as mahasiswaNama,
				applyBankTransferAmount as nominal, dueDateTagihanDaftar as dueDateTagihan
				FROM `adis_smb_form` f
				LEFT JOIN adis_smb_usr_pribadi p ON p.kode = f.kode
				LEFT JOIN adis_smb_usr_keu k ON k.smbUsr = f.kode
				LEFT JOIN adis_jalur_smb j ON SUBSTR(f.bukaSmb,12,2) = j.kode
				LEFT JOIN adis_prodi d ON d.kode = RIGHT(f.bukaSmb,4)
				WHERE f.kode = '$kode'";
		$json_daftar = $this->db2->query($sql);		
		$data = '';
		foreach($json_daftar->result_array() as $row){
			$data[] = $row;
			
		}
		echo '<pre>';
		print_r($data);
		
		
		$curl_post_data = json_encode($data);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $service_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);
		curl_setopt($curl, CURLOPT_TIMEOUT, 400); //timeout in seconds
		curl_setopt($curl, CURLOPT_VERBOSE, true); #debugging purpose only

		$curl_response = curl_exec($curl);
		curl_close($curl);

		$response = json_decode($curl_response);
		//echo $curl_post_data;
		print_r($curl_response);
		
	}
	
	function json_bayar_daftar(){ 
		
		$host_asik = "https://asik.paramadina.ac.id/gt/uat/gtpembayaran/";
		$service_url = $host_asik.'index.php?mod=service&sub=validasiPembayaranAdmisi&act=rest&typ=rest';
		
		$username = 'registrasi';
		$password = 'regGT2015';
		
		$kode = "rahmadsyalevi@outlook.com";
		$sql = "SELECT nomor as mahasiswaNoPendaftaran, 
			k.no_tagihan_daftar as nomorTagihan,
			applyBankTransferTime as tanggalBayar
			FROM adis_smb_form f
			LEFT JOIN adis_smb_usr_keu k ON k.smbUsr = f.kode
			WHERE stsApplyPaidConfirm = 1 AND f.kode = '$kode'";		
		$json_bayar_daftar = $this->db2->query($sql);
		
		foreach($json_bayar_daftar->result_array() as $row){
			$data[] = $row;
			
		}
		echo '<pre>';
		print_r($data);
		
		$curl_post_data = json_encode($data);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $service_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);
		curl_setopt($curl, CURLOPT_TIMEOUT, 400); //timeout in seconds
		curl_setopt($curl, CURLOPT_VERBOSE, true); #debugging purpose only

		$curl_response = curl_exec($curl);
		curl_close($curl);

		$response = json_decode($curl_response);
		//echo $curl_post_data;
		print_r($curl_response);
		
	}
	
	function update_noreg(){
		
		$host_asik = "https://asik.paramadina.ac.id/gt/uat/gtpembayaran/";
		$service_url = $host_asik.'index.php?mod=service&sub=updateNoregDaftar&act=rest&typ=rest';
		
		$username = 'registrasi';
		$password = 'regGT2015';
		
		$kode='bt4ivel@gmail.com';
		
		$sql = "SELECT k.no_tagihan_daftar as noregLama, nomor as noregBaru
			FROM adis_smb_form f
			LEFT JOIN adis_smb_usr_keu k ON k.smbUsr = f.kode
			WHERE stsApplyPaidConfirm = 1 AND f.kode = '$kode'";		
		$json_noreg = $this->db2->query($sql)->row_array();
		$data = $json_noreg;
		
		echo "<pre>";
		print_r($data);
		
		$curl_post_data = json_encode($data);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $service_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);
		curl_setopt($curl, CURLOPT_TIMEOUT, 400); //timeout in seconds
		curl_setopt($curl, CURLOPT_VERBOSE, true); #debugging purpose only

		$curl_response = curl_exec($curl);
		curl_close($curl);

		$response = json_decode($curl_response);
		//echo $curl_post_data;
		print_r($curl_response);
		
	}
	
	function insert_bulk(){
		$kode = $this->uri->segment(3);
		$method = $this->uri->segment(4);
		$sql = "";
		switch($method){
			case "1" :
				$val = 'Insert Mahasiswa : {"gtfwResult":0} Parameter : Array';
				$sql = "SELECT kode_mahasiswa as kode FROM `tbl_log_respon` 
					WHERE kode_mahasiswa = '$kode'
					GROUP BY kode_mahasiswa;";
			break;
			case "2":
				$val2 = 'Generate Tagihan : {"gtfwResult":"Data setting pembayaran untuk no pendaftaran%';
				$sql = "SELECT kode_mahasiswa as kode FROM `tbl_log_respon`
					WHERE kode_mahasiswa = '$kode'
					GROUP BY kode_mahasiswa;";
			break;
		}			
		
		$sql = $this->db2->query($sql)->result_array();
		
		$jumlah = count ($sql);
		
		foreach($sql as $v){
			switch($method){
				case "1" :
					$this->json_data_calon_mah($v['kode']);
				break;
				case "2":
					$this->json_tagihan($v['kode']);
				break;
			}
		}
		
		//echo $jumlah;
	}
	
	function json_data_calon_mah($kode = ''){ 
		
		$host_asik = "https://asik.paramadina.ac.id/gt/uat/gtpembayaran/";
		$service_url = $host_asik.'index.php?mod=service&sub=addMahasiswa&act=rest&typ=rest';
		
		$username = 'registrasi';
		$password = 'registrasi';
		
		//Err 1 $kode = "55cea84670ce3";
		//Err 2 $kode = "55cebaf70c9ba";
		$kode = "55ed2cc4caade";
		
		$sql = "SELECT 
					f.nomor as mahasiswaNoPendaftaran, 
					f.nim as mahasiswaNIM, 
					f.nomor as mahasiswaNoTest, 
					p.nama as mahasiswaNama, 
					p.rumahAlamat as mahasiswaAlamat, 
					d.kode_asik as mahasiswaProdiKode,
					LEFT(bukaSmb, 4) as mahasiswaAngkatan, 
					CONCAT('','0',f.stsResultGrade) as mahasiswaPeringkatKode,
					j.kode_asik as mahasiswaJalurKode, 
					REPLACE(LEFT(bukaSmb, 6),'.','') as mahasiswaPeriodeKode ,
					IF((SELECT COUNT(k.kode) FROM adis_smb_usr_kel k WHERE k.smbUsr = f.kode) > 0 , 'AD', 'UM') as mahasiswaStatKelKode,
					if(f.stsReapplyPaidConfirm = 1, 'L', 'CM') as mahasiswaStatusMhsKode
				FROM adis_smb_form f
				LEFT JOIN adis_smb_usr_pribadi p ON p.kode = f.kode
				LEFT JOIN adis_jalur_smb j ON SUBSTR(f.bukaSmb,12,2) = j.kode
				LEFT JOIN adis_prodi d ON d.kode = RIGHT(f.bukaSmb,4)
				WHERE f.stsResultConfirm = 1 AND 
				f.kode = '$kode'";		
		$json_data_calon_mah = $this->db2->query($sql);
		
        foreach($json_data_calon_mah->result_array() as $row){
			$data['mahasiswa'] = array($row);;
			
		}
		print_r($data);
		
		$curl_post_data = json_encode($data);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $service_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);
		curl_setopt($curl, CURLOPT_TIMEOUT, 400); //timeout in seconds
		curl_setopt($curl, CURLOPT_VERBOSE, true); #debugging purpose only

		$curl_response = curl_exec($curl);
		curl_close($curl);
		
		print_r($curl_response);
		
		
	}
	
	function json_tagihan($kode = ''){ 
		
		$host_asik = "https://asik.paramadina.ac.id/gt/gtpembayaran/";
		$service_url = $host_asik.'index.php?mod=service&sub=generateTagihanRegistrasi&act=rest&typ=rest';
		
		$username = 'registrasi';
		$password = 'regGT2015';
		
		$kode_jalur = '01';
	
		//Err 2 $kode = "55cea84670ce3";
		//Err 2 $kode = "55cebaf70c9ba";
		//$kode = '55cef2b61a68b';
		
		//$kode = "55ceb3c924754";
		$kode = "55ed2cc4caade";
		
		$sql = "SELECT a.nomor as mahasiswaNoPendaftaran, 
				REPLACE(LEFT(a.bukaSmb, 6), '.','') as mahasiswaPeriodeKode, 
				IF (p.pembayaran = '69', 1, 0) as tagihanIsCicilan
				FROM adis_smb_form a 
				LEFT JOIN adis_smb_usr_keu k ON k.smbUsr = a.kode
				LEFT JOIN adis_pembayaran p ON p.kode = k.metodBayarDaftarUlang
				WHERE a.stsReapplyPaid = 1 AND a.kode = '$kode'";		
		$json_tagihan = $this->db2->query($sql);
			
		$nominal = "";
		$tglTempo = "";
		
		IF ($kode_jalur == 'KP'){
			$cicilan = "SELECT p.pembayaran,
				CONCAT_WS(',',p.angsuran1,p.angsuran2,p.angsuran3,p.angsuran4, p.angsuran5) as nominal,
				CONCAT_WS(',', p.due_date1, p.due_date2, p.due_date3, p.due_date4, p.due_date5) as tglJatuhTempo
				FROM adis_smb_form a 
				LEFT JOIN adis_smb_usr_keu k ON k.smbUsr = a.kode
				LEFT JOIN adis_pembayaran_paralel p ON p.kode = k.metodBayarDaftarUlang
				WHERE a.stsReapplyPaid = 1 AND a.kode = '5577a26a59d45'";
			$cicilan = $this->db2->query($cicilan);
			$rows_a = $cicilan->row_array();	
			IF ($rows_a['pembayaran'] == '69'){				
				$nominal = explode(",",$rows_a['nominal']);
				$tglTempo = explode(",",$rows_a['tglJatuhTempo']);
			}
			
		}else{
		/*
			$cicilan = "SELECT 
					CASE
						WHEN a.stsResultGrade = 1 THEN CONCAT_WS(',',c.angsuran1_1,c.angsuran2_1,c.angsuran3_1) 
						WHEN a.stsResultGrade = 2 THEN CONCAT_WS(',',c.angsuran1_2,c.angsuran2_2,c.angsuran3_2) 
						WHEN a.stsResultGrade = 3 THEN CONCAT_WS(',',c.angsuran1_3,c.angsuran2_3,c.angsuran3_3) 
					END as nominal,
					CONCAT_WS(',', c.due_date1, c.due_date2, c.due_date3) as tglJatuhTempo
					FROM adis_smb_form a 
					LEFT JOIN adis_smb_usr_keu k ON k.smbUsr = a.kode
					LEFT JOIN adis_pembayaran p ON p.kode = k.metodBayarDaftarUlang
					LEFT JOIN adis_cicilan c On c.id_pembayaran = p.kode
					WHERE a.stsReapplyPaid = 1 AND a.kode = '$kode'";*/
			$cicilan = "SELECT 
						CONCAT_WS(',',k.cicilan1,k.cicilan2,k.cicilan3) as nominal,
						CONCAT_WS(',', c.due_date1, c.due_date2, c.due_date3) as tglJatuhTempo,
						p.bpp, p.sks, p.spp
						FROM adis_smb_form a 
						LEFT JOIN adis_smb_usr_keu k ON k.smbUsr = a.kode
						LEFT JOIN adis_pembayaran p ON p.kode = k.metodBayarDaftarUlang
						LEFT JOIN adis_cicilan c On c.id_pembayaran = p.kode
						WHERE a.stsReapplyPaid = 1 AND a.kode = '$kode'";
			$cicilan = $this->db2->query($cicilan);
			
			$num_rows = $cicilan->num_rows();
			$rows_a = $cicilan->row_array();
			
			if ($num_rows > 0){
				$nominal = explode(",",$rows_a['nominal']);
				$tglTempo = explode(",",$rows_a['tglJatuhTempo']);
			}
		}
		
        foreach ($json_tagihan->result_array() as $row){
			$data['mahasiswaNoPendaftaran'] = $row['mahasiswaNoPendaftaran'];
			$data['mahasiswaPeriodeKode'] = $row['mahasiswaPeriodeKode'];
			$data['tagihanIsCicilan'] = $row['tagihanIsCicilan'];
			$data['totalSppSksBpp'] = $rows_a['spp']+$rows_a['sks']+$rows_a['bpp'];
			$data['tglJatuhTempo'] = $tglTempo;
			$data['nominalCicilan'] = $nominal;
			
		}
		$data = array($data);
		
		echo "<pre>";
		print_r($data);
		
		
		$curl_post_data = json_encode($data);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $service_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);
		curl_setopt($curl, CURLOPT_TIMEOUT, 400); //timeout in seconds
		curl_setopt($curl, CURLOPT_VERBOSE, true); #debugging purpose only

		$curl_response = curl_exec($curl);
		curl_close($curl);

		$response = json_decode($curl_response, true);
		print_r($curl_response);
		
		$notagihan=$response['gtfwResult'];
		$notagihantemp=explode(",",$notagihan);		
		
		if (strpos($notagihan,'Setting pembayaran untuk pendaftar dengan nomor pendaftaran ') !== false ||
			strpos($notagihan,'Tagihan untuk nomor pendaftaran') !== false){
            echo "<script>alert('Integrasi Data Error');</script>";
        }else{
			if ($row['tagihanIsCicilan'] == 1){
							$notagihanSppSksBpp = $notagihantemp[0];
							$notagihan1         = $notagihantemp[1];
				$notagihan2         = $notagihantemp[2];
				$notagihan3         = $notagihantemp[3];
				$this->db2->query("UPDATE adis_smb_usr_keu "
									. " SET no_tagihanSppSksBpp = '$notagihanSppSksBpp', "
									. " no_tagihan1 = '$notagihan1', no_tagihan2 = '$notagihan2', "
									. " no_tagihan3 = '$notagihan3' WHERE smbUsr='$kode';");
			}else{
				$notagihan1=$notagihantemp[0];
				$this->db2->query("UPDATE adis_smb_usr_keu SET no_tagihanSppSksBpp = '$notagihan1' "
									. " WHERE smbUsr='$kode';");		
			}
		}
		echo $curl_post_data;
		print_r($curl_response);
		
		
		
	}
	
	function test(){
		// $response = array("gtfwResult"=>"14102141100021");
		// //print_r($response);
		// $notagihan=$response['gtfwResult'];
		// $notagihantemp=explode(",",$notagihan);
		// $notagihanuse=$notagihantemp[0];
		// //echo $notagihanuse;
		// echo '<pre>';
		$curl_response = '{"gtfwResult":"14102141100021,14102141100022,14102141100023"}';
		$response = json_decode($curl_response,true);
		print_r ($response);
	}
	
	function json_validasi_pembayaran(){ 
		
		$host_asik = "https://asik.paramadina.ac.id/gt/gtpembayaran/";
		$service_url = $host_asik.'index.php?mod=service&sub=validasiTagihanRegistrasi&act=rest&typ=rest';
		
		$username = 'registrasi';
		$password = 'regGT2015';
		
		//$kode = "55cd97bb7b6e0";
		$kode = "55ed2cc4caade";
		
		$sql = "SELECT f.nomor as mahasiswaNoPendaftaran, 
			k.no_tagihanSppSksBpp as nomorTagihan,
			REPLACE(LEFT(f.bukaSmb, 6), '.','') as pembayaranPeriodeKode,
			TRUNCATE(f.reapplyBankTransferAmount, 0) as totalBayar
			FROM adis_smb_form f
			LEFT JOIN adis_smb_usr_keu k ON k.smbUsr = f.kode
			WHERE f.stsReapplyPaidConfirm = 1 AND
			f.kode = '$kode';";		
		$json_validasi_pembayaran = $this->db2->query($sql)->row_array();
		
		$data[] = $json_validasi_pembayaran;
		
		echo "<pre>";
        print_r($data);
		
		$curl_post_data = json_encode($data);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $service_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);
		curl_setopt($curl, CURLOPT_TIMEOUT, 400); //timeout in seconds
		curl_setopt($curl, CURLOPT_VERBOSE, true); #debugging purpose only

		$curl_response = curl_exec($curl);
		curl_close($curl);

		$response = json_decode($curl_response);
		echo $curl_post_data;
		print_r($curl_response);
		
	}
	
	function json_up_nim(){ 
		
		$host_asik = "https://asik.paramadina.ac.id/gt/gtpembayaran/";
		$service_url = $host_asik.'index.php?mod=service&sub=generateNIM&act=rest&typ=rest';
		
		$username = 'registrasi';
		$password = 'regGT2015';
		
		$kode = "55cee2cec5aa5";
		$sql = "SELECT f.nomor as mahasiswaNoPendaftaran, f.nim as mahasiswaNIM
			FROM adis_smb_form f
			WHERE f.stsReapplyPaidConfirm = 1 AND
			f.kode = '$kode'";		
		$json_up_nim = $this->db2->query($sql);
		
		foreach($json_up_nim->result_array() as $row){
			$data[] = $row;
		}
		
		echo "<pre>";
		print_r($data);
		
		
		$curl_post_data = json_encode($data);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $service_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);
		curl_setopt($curl, CURLOPT_TIMEOUT, 400); //timeout in seconds
		curl_setopt($curl, CURLOPT_VERBOSE, true); #debugging purpose only

		$curl_response = curl_exec($curl);
		curl_close($curl);

		$response = json_decode($curl_response);
		//echo $curl_post_data;
		print_r($curl_response);
	}
	
	function json_mahasiswa(){ 
		
		$service_url = 'http://asik.paramadina.ac.id/gt/uat/gtakademik_service/rest-v2.php/service/updateStudent';
		//$service_url = 'http://asik.paramadina.ac.id/gt/uat/gtakademik_service/rest-v2.php/service/addStudent';
		$username = 'admingt';
		$password = 'admingt';
		
		$kode = "5578f850a47fb";
		
		$sql_mah = "SELECT f.nim as nim, p.nama as nama, LEFT(bukaSmb, 4) as angkatan,r.kode_asik as program_studi, 
			'1' as semester_masuk, '' as konsentrasi, 
			f.nomor as nomor_test_masuk,  p.tanggalLahir as tanggal_lahir, IF (p.genderType = '03.P', 'L', 'P') as jenis_kelamin, 
			p.suratAlamat as alamat_asli, p.rumahAlamat as alamat_sekarang, p.rumahCell as telepon_hp, 
			CASE 
				WHEN p.agamaType = '02.I' THEN '1'
				WHEN p.agamaType = '02.K' THEN '2'
				WHEN p.agamaType = '02.P' THEN '3'
				WHEN p.agamaType = '02.H' THEN '4'
				WHEN p.agamaType = '02.B' THEN '5'
				WHEN p.agamaType = '02L' THEN '6'
				WHEN p.agamaType = '02.C' THEN '8'
			END agama, p.kode as email, 
			e.nama as nama_smta, w.nama as alamat_smta,
			CASE 
				WHEN SUBSTR(f.bukaSmb,12,2) = '01' THEN 'BIASA'
				WHEN SUBSTR(f.bukaSmb,12,2) = '02' THEN 'TRANSFER'
				WHEN SUBSTR(f.bukaSmb,12,2) = '03' THEN 'FELLOWSHIP'
				WHEN SUBSTR(f.bukaSmb,12,2) = '04' THEN 'PSR'
				WHEN SUBSTR(f.bukaSmb,12,2) = 'KP' THEN 'PARALEL'
			END status
			FROM adis_smb_form f
			LEFT JOIN adis_smb_usr_pribadi p ON p.kode = f.kode
			LEFT JOIN adis_smb_usr_edu e ON e.smbUsr = f.kode
			LEFT JOIN adis_wil w ON e.kabKota = w.kode
			LEFT JOIN adis_prodi r ON r.kode = RIGHT(f.bukaSmb,4)
			WHERE f.kode = '$kode'";		
		$json_mah = $this->db2->query($sql_mah)->row_array();
		
		$sql_keg = "SELECT namaKegiatan as nama_kegiatan, 
			dariTahun as tanggal_kegiatan_mulai,
			sampaiTahun as tanggal_kegiatan_selesai,
			jabatan as deskripsi_kegiatan 
			FROM adis_smb_usr_org 
			WHERE smbUsr = '$kode';";
		$json_keg = $this->db2->query($sql_keg)->result_array();
		
		$sql_ayh = "SELECT ayahNama as nama, ayahAlamat as alamat, ayahCell as telepon_hp, ayahEmail as email, '' as pekerjaan,
			'' as tanggal_meninggal
			FROM adis_smb_usr_pribadi 
			WHERE kode = '$kode';";
		$json_ayh = $this->db2->query($sql_ayh)->row_array();
		
		$sql_ibu = "SELECT ibuNama as nama, ibuAlamat as alamat, ibuCell as telepon_hp, ibuEmail as email, '' as pekerjaan,
			'' as tanggal_meninggal
			FROM adis_smb_usr_pribadi 
			WHERE kode = '$kode';";
		$json_ibu = $this->db2->query($sql_ibu)->row_array();
		
		$sql_wli = "SELECT waliNama as nama, waliAlamat as alamat, waliCell as telepon_hp, waliEmail as email,'' as pekerjaan,
			'' as tanggal_meninggal
			FROM adis_smb_usr_pribadi 
			WHERE kode = '$kode';";
		$json_wli = $this->db2->query($sql_wli)->row_array();
		
		$sql_sau = "SELECT nim, kerja as nama_tempat_bekerja, '' as alamat_tempat_bekerja
		FROM adis_smb_usr_kel
		WHERE smbUsr = '$kode';";
		$json_sau = $this->db2->query($sql_sau)->result_array();		
		
		$json_ortu = array("ayah" => $json_ayh, "ibu" => $json_ibu, "wali" => $json_wli);
		
		$data[] = array("mahasiswa" => $json_mah, "mahasiswa_kegiatan" => $json_keg, "mahasiswa_orang_tua" => $json_ortu,
			"mahasiswa_saudara" => $json_sau);
		
		echo "<pre>";
		print_r($data);
		/*
		$curl_post_data = json_encode($data);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $service_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);
		curl_setopt($curl, CURLOPT_TIMEOUT, 400); //timeout in seconds
		curl_setopt($curl, CURLOPT_VERBOSE, true); #debugging purpose only

		$curl_response = curl_exec($curl);
		curl_close($curl);

		$response = json_decode($curl_response);
		//echo $curl_post_data;
		print_r($curl_response);
		*/
	}
	
	function update_mahasiswa(){ 
		
		$service_url = 'http://asik.paramadina.ac.id/gt/gtakademik_service/rest-v2.php/service/updateStudent';
		
		$username = 'admingt';
		$password = 'admingt';
				
		$sql_mah = "SELECT f.nim as nim, p.nama as nama, LEFT(bukaSmb, 4) as angkatan,r.kode_asik as program_studi, 
			'1' as semester_masuk, '' as konsentrasi, 
			f.nomor as nomor_test_masuk,  p.tanggalLahir as tanggal_lahir, IF (p.genderType = '03.P', 'L', 'P') as jenis_kelamin, 
			p.suratAlamat as alamat_asli, p.rumahAlamat as alamat_sekarang, p.rumahCell as telepon_hp, p.tempatLahir as kota_kelahiran,
			CASE 
				WHEN p.agamaType = '02.I' THEN '1'
				WHEN p.agamaType = '02.K' THEN '2'
				WHEN p.agamaType = '02.P' THEN '3'
				WHEN p.agamaType = '02.H' THEN '4'
				WHEN p.agamaType = '02.B' THEN '5'
				WHEN p.agamaType = '02L' THEN '6'
				WHEN p.agamaType = '02.C' THEN '8'
			END agama, u.email as email, 
			e.nama as nama_smta, w.nama as alamat_smta,
			j.kode_asik as status
			FROM adis_smb_form f
			LEFT JOIN adis_smb_usr u ON u.kode = f.kode
			LEFT JOIN adis_smb_usr_pribadi p ON p.kode = f.kode
			LEFT JOIN adis_smb_usr_edu e ON e.smbUsr = f.kode
			LEFT JOIN adis_wil w ON e.kabKota = w.kode
			LEFT JOIN adis_prodi r ON r.kode = RIGHT(f.bukaSmb,4)
			LEFT JOIN adis_jalur_smb j ON j.kode = SUBSTR(f.bukaSmb,12,2)
			WHERE f.nim != '' AND f.nomor LIKE '151%' AND nim NOT IN('115105044','115107025','115107027','115108025')";		// AND nim NOT IN('115105044','115107025','115107027','115108025')
		//$json_mah = $this->db2->query($sql_mah)->row_array();
		$json_mah = $this->db2->query($sql_mah)->result_array();
		
		foreach($json_mah as $v){
			
			$nim = $v['nim'];
			$mah = $this->db2->query("SELECT * FROM adis_smb_form WHERE nim = '".$nim."'")->row_array();
			$kode = $mah['kode'];
			
			$sql_keg = "SELECT namaKegiatan as nama_kegiatan, 
				dariTahun as tanggal_kegiatan_mulai,
				sampaiTahun as tanggal_kegiatan_selesai,
				jabatan as deskripsi_kegiatan 
				FROM adis_smb_usr_org 
				WHERE smbUsr = '$kode';";
			$json_keg = $this->db2->query($sql_keg)->result_array();
			
			$sql_ayh = "SELECT ayahNama as nama, ayahAlamat as alamat, ayahCell as telepon_hp, ayahEmail as email, '' as pekerjaan,
				'' as tanggal_meninggal
				FROM adis_smb_usr_pribadi 
				WHERE kode = '$kode';";
			$json_ayh = $this->db2->query($sql_ayh)->row_array();
			
			$sql_ibu = "SELECT ibuNama as nama, ibuAlamat as alamat, ibuCell as telepon_hp, ibuEmail as email, '' as pekerjaan,
				'' as tanggal_meninggal
				FROM adis_smb_usr_pribadi 
				WHERE kode = '$kode';";
			$json_ibu = $this->db2->query($sql_ibu)->row_array();
			
			$sql_wli = "SELECT waliNama as nama, waliAlamat as alamat, waliCell as telepon_hp, waliEmail as email,'' as pekerjaan,
				'' as tanggal_meninggal
				FROM adis_smb_usr_pribadi 
				WHERE kode = '$kode';";
			$json_wli = $this->db2->query($sql_wli)->row_array();
			
			$sql_sau = "SELECT nim, kerja as nama_tempat_bekerja, '' as alamat_tempat_bekerja
			FROM adis_smb_usr_kel
			WHERE smbUsr = '$kode';";
			$json_sau = $this->db2->query($sql_sau)->result_array();

			$json_ortu = array("ayah" => $json_ayh, "ibu" => $json_ibu, "wali" => $json_wli);
			
			$mah_array[] = $v;
			
			$data[] = array("mahasiswa"=>$v, "mahasiswa_kegiatan" => $json_keg, "mahasiswa_orang_tua" => $json_ortu,
			"mahasiswa_saudara" => $json_sau);
		
		}
		/*
		$json_ortu = array("ayah" => $json_ayh, "ibu" => $json_ibu, "wali" => $json_wli);
		
		$data[] = array("mahasiswa"=>$json_mah, "mahasiswa_kegiatan" => $json_keg, "mahasiswa_orang_tua" => $json_ortu,
			"mahasiswa_saudara" => $json_sau);
		*/
		
		// echo "<pre>";
		// print_r($data);
		
		
		$curl_post_data = json_encode($data);
		//echo $curl_post_data;
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $service_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);
		curl_setopt($curl, CURLOPT_TIMEOUT, 400); //timeout in seconds
		curl_setopt($curl, CURLOPT_VERBOSE, true); #debugging purpose only

		$curl_response = curl_exec($curl);
		curl_close($curl);

		$response = json_decode($curl_response);
		//echo $curl_post_data;
		print_r($curl_response);
		
	}
	
	function his_to_asik(){
		
	
	}
	
	
	

}
?>