<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Mintegrasi extends CUTI_Model{
	
	function __construct(){
		parent::__construct();
	}
	
	function Mintegrasi(){
		parent::__construct();
		
		$this->db2->query("SET lc_time_names = 'id_ID'");
		
	}
	/*** Di panggil ketika mahasiswa melakukan pembayaran di portal Admisi ****/
	function pembayaran_admisi($kode){ 
		
		$host_asik = "https://asik.paramadina.ac.id/gt/gtpembayaran/";
		$service_url = $host_asik.'index.php?mod=service&sub=pembayaranAdmisi&act=rest&typ=rest';
		
		$username = 'registrasi';
		$password = 'regGT2015';
		
		$sql = "SELECT nomor as mahasiswaNoPendaftaran, 
				d.kode_asik as mahasiswaProdiKode,
				j.kode_asik as mahasiswaJalurKode, 
				REPLACE(LEFT(bukaSmb, 6),'.','') as mahasiswaPeriodeKode, 
				p.nama as mahasiswaNama,
				applyBankTransferAmount as nominal, 
				dueDateTagihanDaftar as dueDateTagihan
				FROM `adis_smb_form` f
				LEFT JOIN adis_smb_usr_pribadi p ON p.kode = f.kode
				LEFT JOIN adis_smb_usr_keu k ON k.smbUsr = f.kode
				LEFT JOIN adis_jalur_smb j ON SUBSTR(f.bukaSmb,12,2) = j.kode
				LEFT JOIN adis_prodi d ON d.kode = RIGHT(f.bukaSmb,4)
				WHERE f.stsApplyPaid = 1
				AND f.kode = '$kode'";
		$json_daftar = $this->db2->query($sql);		
		$data = '';
		foreach($json_daftar->result_array() as $row){
			$data[] = $row;
			
		}
		//print_r($data);
		
		
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
	
	/*** Di gunakan ketika KEU melakukan Approval transaksi keuangan mahasiswa di Admin Admisi ***/
	function validasi_pemb_admisi($kode){ 
		
		$host_asik = "https://asik.paramadina.ac.id/gt/gtpembayaran/";
		$service_url = $host_asik.'index.php?mod=service&sub=validasiPembayaranAdmisi&act=rest&typ=rest';
		
		$username = 'registrasi';
		$password = 'regGT2015';
		
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
		//print_r($data);
		
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
	
	function update_noreg($kode='bt4ivel@gmail.com'){
		
		$host_asik = "https://asik.paramadina.ac.id/gt/gtpembayaran/";
		$service_url = $host_asik.'index.php?mod=service&sub=updateNoregDaftar&act=rest&typ=rest';
		
		$username = 'registrasi';
		$password = 'regGT2015';
		
		$sql = "SELECT nomor as noregBaru, 
			k.no_tagihan_daftar as noregLama
			FROM adis_smb_form f
			LEFT JOIN adis_smb_usr_keu k ON k.smbUsr = f.kode
			WHERE stsApplyPaidConfirm = 1 AND f.kode = '$kode'";		
		$json_noreg = $this->db2->query($sql)->row();
		$data = $json_noreg;
		
		//print_r($data);
		
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
		// echo $curl_post_data;
		print_r($curl_response);
		
	}
	
	/*** Digunakan ketika Admin Admisi mengubah status mahasiswa menjadi lulus di Modul Admin Admisi ***/
	function insert_mahasiswa($kode){ 
		
		$host_asik = "https://asik.paramadina.ac.id/gt/gtpembayaran/";
		$service_url = $host_asik.'index.php?mod=service&sub=addMahasiswa&act=rest&typ=rest';
		
		$username = 'registrasi';
		$password = 'regGT2015';
		
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
		//print_r($data);
		
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
		//echo $curl_post_data;
		//print_r($curl_response);
        $this->log_asik_resp($kode, "Insert Mahasiswa : ".$curl_response." Parameter : ".$data);               
		$resp = $response['gtfwResult'];
                
        return $resp;
	}
	
	/**** Digunakan ketika mahasiswa melakukan pembayaran daftar ulang di modul portal Admisi ***/
	function generate_tagihan($kode, $kode_jalur){ 
		
		$host_asik = "https://asik.paramadina.ac.id/gt/gtpembayaran/";
		$service_url = $host_asik.'index.php?mod=service&sub=generateTagihanRegistrasi&act=rest&typ=rest';
		
		$username = 'registrasi';
		$password = 'regGT2015';
	
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
		
		// echo "<pre>";
		// print_r($data);
		
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
		
		if ($row['tagihanIsCicilan'] == 1){
			$notagihanSppSksBpp = $notagihantemp[0];
            $notagihan1=$notagihantemp[1];
			$notagihan2=$notagihantemp[2];
			$notagihan3=$notagihantemp[3];
			$this->db2->query("UPDATE adis_smb_usr_keu "
                                . " SET no_tagihanSppSksBpp = '$notagihanSppSksBpp', "
                                . " no_tagihan1 = '$notagihan1', no_tagihan2 = '$notagihan2', "
                                . " no_tagihan3 = '$notagihan3' WHERE smbUsr='$kode';");
		}else{
			$notagihan1=$notagihantemp[0];
			$this->db2->query("UPDATE adis_smb_usr_keu SET no_tagihanSppSksBpp = '$notagihan1' WHERE smbUsr='$kode';");		
		}
		//echo $curl_post_data;
		//print_r($curl_response);
                $this->log_asik_resp($kode, "Generate Tagihan : ".$curl_response." Parameter : ".$data);
	}
	
	/***** Digunakan ketika bagian keuangan melakukan approval transaksi 
       *** keuangan pendaftaran ulang mahasiswa di admin Admisi ***/
	function validasi_pembayaran($kode){ 
		
		$host_asik = "https://asik.paramadina.ac.id/gt/gtpembayaran/";
		$service_url = $host_asik.'index.php?mod=service&sub=validasiTagihanRegistrasi&act=rest&typ=rest';
		
		$username = 'registrasi';
		$password = 'regGT2015';
		
		//$kode = "luky.arifin@outlook.com";
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
		
		//print_r($data);
		
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
//		print_r($curl_response);
                
                $this->log_asik_resp($kode, "Validasi Pembayaran : ".$curl_response." Parameter : ".$data);
		
		
	}
	
	/*** Digunakan bersamaan ketika mahasiswa di approve keuangan pendaftaran ulang dan setelah proses generate NIM selesai **/
	function update_nim($kode){ 
		
		$host_asik = "https://asik.paramadina.ac.id/gt/gtpembayaran/";
		$service_url = $host_asik.'index.php?mod=service&sub=generateNIM&act=rest&typ=rest';
		
		$username = 'registrasi';
		$password = 'regGT2015';
		
		$sql = "SELECT f.nomor as mahasiswaNoPendaftaran, f.nim as mahasiswaNIM
			FROM adis_smb_form f
			WHERE f.stsReapplyPaidConfirm = 1 AND
			f.kode = '$kode'";		
		$json_up_nim = $this->db2->query($sql);
		
		foreach($json_up_nim->result_array() as $row){
			$data[] = $row;
		}
		
		//print_r($data);
		
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
		//print_r($curl_response);
                $this->log_asik_resp($kode, "Update NIM : ".$curl_response." Parameter : ".$data);
	}
	
	function upload_mahasiswa($kode){ 
		
		$service_url = 'http://asik.paramadina.ac.id/gt/gtakademik_service/rest-v2.php/service/addStudent';
		$username = 'admingt';
		$password = 'admingt';
		
		//$kode = "luky.arifin@outlook.com";
		
		$sql_mah = "SELECT f.nim as nim, p.nama as nama, LEFT(bukaSmb, 4) as angkatan,
                        r.kode_asik as program_studi, 
			'1' as semester_masuk, r.konsentrasi as konsentrasi, 
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
			CASE 
				WHEN SUBSTR(f.bukaSmb,12,2) = '01' THEN 'BIASA'
				WHEN SUBSTR(f.bukaSmb,12,2) = '02' THEN 'TRANSFER'
				WHEN SUBSTR(f.bukaSmb,12,2) = '03' THEN 'FELLOWSHIP'
				WHEN SUBSTR(f.bukaSmb,12,2) = '04' THEN 'PSR'
				WHEN SUBSTR(f.bukaSmb,12,2) = 'KP' THEN 'PARALEL'
				WHEN SUBSTR(f.bukaSmb,12,2) = '10' THEN 'BIASA'
			END status
			FROM adis_smb_form f
			LEFT JOIN adis_smb_usr_pribadi p ON p.kode = f.kode
			LEFT JOIN adis_smb_usr_edu e ON e.smbUsr = f.kode
			LEFT JOIN adis_smb_usr u ON u.kode = f.kode
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
		
		$sql_ortu = "SELECT ayahNama as nama_ayah,ibuNama as nama_ibu, waliNama as nama_wali, 
			ayahAlamat as alamat_ayah, ibuAlamat as alamat_ibu, waliAlamat as alamat_wali, 
			ayahCell as telepon_hp_ayah, ibuCell as telepon_hp_ibu, waliCell as telepon_hp_wali,
			ayahEmail as email_ayah,ibuEmail as email_ibu,waliEmail as email_wali, 
			'' as pekerjaan_ayah,'' as pekerjaan_ibu,'' as pekerjaan_wali,
			'' as pendidikan_ayah,'' as pendidikan_ibu,'' as pendidikan_wali, 
			'' as tanggal_ayah_meninggal,'' as tanggal_ibu_meninggal
			FROM adis_smb_usr_pribadi 
			WHERE kode = '$kode';";
		$json_ortu = $this->db2->query($sql_ortu)->row_array();
		
		/*
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
		*/
		
		$sql_sau = "SELECT nim, kerja as nama_tempat_bekerja, '' as alamat_tempat_bekerja
		FROM adis_smb_usr_kel
		WHERE smbUsr = '$kode';";
		$json_sau = $this->db2->query($sql_sau)->result_array();		
		
		// $json_ortu = array("ayah" => $json_ayh, "ibu" => $json_ibu, "wali" => $json_wli);
		
		
		$data[] = array("mahasiswa" => $json_mah, "mahasiswa_kegiatan" => $json_keg, "mahasiswa_orang_tua" => $json_ortu,
			"mahasiswa_saudara" => $json_sau);
		
		// echo "<pre>";
		// print_r($data);
		
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
		//print_r($curl_response);
                $this->log_asik_resp($kode, "Upload Mahasiswa Ke ASIK: ".$curl_response." Parameter : ".$data);
	}
	
	function delete_mahasiswa($nim){ 
		
		$service_url = 'http://asik.paramadina.ac.id/gt/gtakademik_service/rest-v2.php/service/deleteStudent';
		
        $username = 'admingt';
		$password = 'admingt';
		
		$data[] = array("nim"=>$nim); 
		
		// echo "<pre>";
		// print_r($data);
		
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
		//print_r($curl_response);
                $this->log_asik_resp($nim, "Hapus Mahasiswa ASIK: ".$curl_response);
	}
        
        function log_asik_resp($kode, $respon){
            $this->db2->insert('tbl_log_respon',array('kode_mahasiswa'=>$kode, 
                                'respon_integrasi'=>$respon));
        }
	
}