<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cmb extends CUTI_Controller{
	function __construct(){
		parent::__construct();	
		//setlocale (LC_TIME, 'id_ID');
		setlocale (LC_TIME, 'INDONESIA');
		$this->auth = unserialize(base64_decode($this->session->userdata('cuti_parmad')));
		$this->host	= $this->config->item('base_url');
		
		if(! $this->auth) {header("Location: " . $this->host);}	
		
		$this->db2 = $this->load->database('second', TRUE); 
		
		$modul = "cmb/";
		$this->smarty->assign('modul',$modul);
		$this->smarty->assign('host',$this->host);
		$this->load->model('madmisi');
		$this->load->model('msmb');
		$this->load->model('mregistrasi');
		$this->load->model('mportal');
		
		$nama = $this->auth['name'];
		$this->smarty->assign('nama',$nama);
		
		$this->db2->query("SET lc_time_names = 'id_ID'");
	}
	
	function index() {
		$this->cmb_data();
		}
		
	function cmb_data(){
		$uri = $this->uri->segment(3);
		$jalur = $this->input->post('jalur');
		$periode = $this->input->post('periode');
		$prodi = $this->input->post('prodi');
		
		$andQuery = "";
		$andQueryJalur = "";
		
		if ($prodi != 'all'){
			$andQuery = "AND substr(f.bukaSmb, -4) = '$prodi'";
		}
		
		if ($jalur != 'all'){
			$andQueryJalur = "AND  SUBSTR(f.bukaSmb, 12, 2) = '$jalur'";
		}
		
		$this->madmisi->mSelectPeriodeAka();
		
		if ($jalur != '' && $uri == "table"){
			
			//$periode = implode(".",array($periode,$jalur));
			$this->db2->query("SET @num:=0;");
			$sql = "SELECT @num:=@num+1 AS 'No', f.kode as kode_smb, f.bukaSmb, f.nomor as no_smb, up.nama as nama_cm , 
			p.nama as progdi, p.singkatan as prodi_sing,
			DAYNAME(u.createTime) as hari, DATE(u.createTime) as tanggal, 
			f.stsApplyPaid, f.stsApplyPaidConfirm, up.stsPribadiConfirm, f.stsEventConfirm, f.stsEventUsmPresent, up.foto,
			f.stsResultConfirm, f.stsMundurBeforeReapply,
			f.stsReapplyPaid, stsReapplyPaidConfirm as comfirm_keu, f.stsMundurAfterReapply, j.nama as n_jalur
			FROM adis_smb_form f 
			LEFT JOIN adis_smb_usr u ON u.kode = f.kode
			LEFT JOIN adis_smb_usr_pribadi up ON up.kode = f.kode
			LEFT JOIN adis_prodi p ON p.kode = RIGHT(f.bukaSmb,4)
			LEFT JOIN adis_jalur_smb j ON j.kode = SUBSTR(f.bukaSmb, 12, 2)
			WHERE SUBSTR(f.bukaSmb, 1, 8) = '$periode' $andQueryJalur  $andQuery AND f.kode NOT LIKE '%@%'
			ORDER BY DATE(u.createTime) DESC";
			
			$sql = $this->db2->query($sql)->result();	
			$this->smarty->assign('sql',$sql);
			
			$this->smarty->assign('periode',$periode);
			$this->smarty->display('cmb/tblCalonPeserta.html');	
			
		}else{
			$site = "Peserta";			
			$this->smarty->assign('site',$site);
			$this->smarty->assign('lokasi',"Daftar Calon Mahasiswa");
			$this->smarty->display('index.html');
		}
		
	}
	
	function mahasiswa(){
		$uri = $this->uri->segment(3);
		$pages = $this->uri->segment(4);
		$jalur = $this->input->post('jalur');
		$periode = $this->input->post('periode');
		$prodi = $this->input->post('prodi');
		
		$andQuery = "";
		$andQueryJalur = "";
		
		if ($prodi != 'all'){
			$andQuery = "AND substr(f.bukaSmb, -4) = '$prodi'";
		}
		
		if ($jalur != 'all'){
			$andQueryJalur = "AND  SUBSTR(f.bukaSmb, 12, 2) = '$jalur'";
		}
		
		if ($uri == 's1'){
			$this->madmisi->mSelectPeriodeCMB('s1');
		}
		if ($uri == 's2'){
			$this->madmisi->mSelectPeriodeCMB('s2');
		}
		if ($jalur != '' && $uri == "table"){
			
			//$periode = implode(".",array($periode,$jalur));
			$this->db2->query("SET @num:=0;");
			$sql = "SELECT @num:=@num+1 AS 'No', f.kode as kode_smb, f.bukaSmb, f.nomor as no_smb, up.nama as nama_cm , 
			p.nama as progdi, p.singkatan as prodi_sing, f.nim,
			DAYNAME(u.createTime) as hari, DATE(u.createTime) as tanggal, 
			f.stsApplyPaid, f.stsApplyPaidConfirm, up.stsPribadiConfirm, f.stsEventConfirm, f.stsEventUsmPresent, up.foto,
			f.stsResultConfirm, f.stsMundurBeforeReapply,
			f.stsReapplyPaid, stsReapplyPaidConfirm as comfirm_keu, f.stsMundurAfterReapply, j.nama as n_jalur
			FROM adis_smb_form f 
			LEFT JOIN adis_smb_usr u ON u.kode = f.kode
			LEFT JOIN adis_smb_usr_pribadi up ON up.kode = f.kode
			LEFT JOIN adis_prodi p ON p.kode = RIGHT(f.bukaSmb,4)
			LEFT JOIN adis_jalur_smb j ON j.kode = SUBSTR(f.bukaSmb, 12, 2)
			WHERE SUBSTR(f.bukaSmb, 1, 8) = '$periode' $andQueryJalur  $andQuery AND f.nim != ''
			ORDER BY DATE(u.createTime) DESC";
			
			$sql = $this->db2->query($sql)->result();	
			$this->smarty->assign('sql',$sql);
			
			$this->smarty->assign('periode',$periode);
			$this->smarty->display('cmb/tblMahasiswa.html');	
			
		}else{
			$site = "Mahasiswa";			
			$this->smarty->assign('site',$site);
			$this->smarty->assign('lokasi',"Daftar Mahasiswa");
			$this->smarty->display('index.html');
		}
		
	}
	
	function smbJalur(){		
		$periode = $this->input->post('periode');
		$function = $this->input->post('fungsi');
		
		$jenjang = substr($periode, -3, 1);
		// echo $jenjang;
		
		$sql ="SELECT * FROM adis_jalur_smb WHERE erased = 0 AND jenjang = $jenjang";
		$sql = $this->db2->query($sql)->result();
		
		$sqlProdi = "SELECT * FROM adis_prodi WHERE erased = 0 AND jenjang = $jenjang";
		$sqlProdi = $this->db2->query($sqlProdi)->result();
		
		$this->smarty->assign('fungsi',$function);
		$this->smarty->assign('periode',$periode);	
		$this->smarty->assign('jalur',$sql);		
		$this->smarty->assign('prodi',$sqlProdi);	
		$this->smarty->display('cmb/selectJalur.html');	
	}
	
	

	function registrasiForm(){
		$this->mregistrasi->mSelectGender();
		$this->madmisi->mSelectPeriodeAka();
		
		$this->mportal->selectAgama();
		$this->mportal->selectType();
		$this->mportal->selectPropinsi();
		// $this->mportal->selectKabKota();
		// $this->mportal->selectKodePos();
		
		$title = "FORM REGISTRASI";
		$page = "formPendaftaran";
		$this->smarty->assign('title', $title);
		$this->smarty->assign('page', $page);
		$this->smarty->display("pendaftaran/index.html");
	}
	
	function saveCmbBaru(){
		$periode = $this->input->post('periode');
		$jenjang = $this->input->post('jenjang');
		$prodi = $this->input->post('prodi');
		$jalur = $this->input->post('jalur');
		
		
		$email = $this->input->post('email');
		
		$cek_email = $this->db2->query("SELECT * FROM adis_smb_usr WHERE email = '$email'")->num_rows();
		
		if ($cek_email > 0){
			echo "<script>alert('Email yang dimasukkan sudah terdaftar di Sistem. "
			. "Pastikan data mahasiswa Tidak Duplikat!');history.go(-1);</script>";
		}else{
		
			$bukaSmb = implode (".", array($periode,'0', $jalur, $prodi));
			
			$_kode = implode ("",array(substr($periode,2,2), $jenjang,'0'));
			
				$qr2= "	set @_tahun:=left('$periode',4); ";
				$qr3= "	set @_tahun_yy:=right(@_tahun,2); ";
				$qr4= "	set @_jenjang:='$jenjang'; ";
				$qr5= "	set @_sesi:=0; ";
				$qr6= "	set @_kode:=concat(@_tahun_yy,@_jenjang,@_sesi); "; 
				$qr7= "	set @_concat := concat('%',@_kode,'%'); ";
				$qr8= "	set @_regOrder := (select max(nomor)+1 from adis_smb_form where nomor LIKE @_concat); ";
				
				$this->db2->query($qr2);
				$this->db2->query($qr3);
				$this->db2->query($qr4);
				$this->db2->query($qr5);
				$this->db2->query($qr6);
				$this->db2->query($qr7);
				$this->db2->query($qr8);
				
				$echo = $this->db2->query("SELECT @_regOrder as data")->row();;
				
				$noReg = $echo->data;
				if (!$echo->data){
					$this->db2->query("	set @_regOrder := concat(@_kode,'0001')");
					$noReg = $_kode.'0001';
				}
			$this->msmb->saveCmbNotReg($bukaSmb, $noReg);
			redirect ('/cmb', 'refresh');
			
		}
			
			// $qry = "UPDATE adis_smb_form SET stsApplyPaidConfirm = 1, nomor=@_regOrder WHERE kode='$kode'";
			// $this->db2->query($qry);
	}
	
	function selectProdi(){
		$jenjang = $this->input->post("jenjang");
		
		$prodi ="SELECT * FROM adis_prodi WHERE erased = 0 AND jenjang = '$jenjang' ORDER BY nama";
		$prodi = $this->db2->query($prodi)->result();			
		$view = '<option value="">-: Pilih Program Studi :-</option>
				{foreach name=lope from=$prodi item=row}
					<option value="{$row->kode}">{$row->nama}</option>
				{/foreach}';
					
		//INISIASI PERIODE YANG SEDANG AKTIF Berdasarkan Jenjang
		/*
                $periode = $this->db2->query("SELECT p.kode as id, m.jenjangType 
                    from adis_periode p
                    LEFT JOIN adis_periode_master m ON m.kode = p.idPeriodeMaster
                    WHERE m.jenjangType = '$jenjang' AND m.status = 1 AND m.erased = 0")->row();
                */
                $periode = $this->db2->query("SELECT m.kode as id, m.jenjangType 
                    from  adis_periode_master m 
                    WHERE m.jenjangType = '$jenjang' AND m.status = 1 AND m.erased = 0")->row();
		$periode = $periode->id;
		$this->session->set_userdata('periode', $periode);
		
		$this->smarty->assign('prodi',$prodi);
		$this->smarty->display('string:'.$view);
	}
	
	function selectJalur(){
		
		$prodi = $this->input->post("prodi");
		$jenjang = $this->input->post("jenjang");
		$periode = $this->session->userdata('periode');
		
		$unit_kerja = '';
		if ($this->auth['level'] != '99'){
			$unit_kerja = "AND unitKerja = '".$this->auth['level']."'";
		}
		
		$jalur ="SELECT * FROM adis_jalur_smb WHERE erased = 0 $unit_kerja AND jenjang = '$jenjang' ORDER BY kode";
		$jalur = $this->db2->query($jalur)->result();	
		
		$view = '<option value="">-: Pilih Jalur Masuk :-</option>
				{foreach name=lope from=$jalur item=row}
						<option value="{$row->kode}">{$row->nama}</option>
				 {/foreach}';
		$this->smarty->assign('jalur',$jalur);
		$this->smarty->display('string:'.$view);
	}
	
	function select_jalur_bayar(){
		$jalur = $this->input->post("jalur");
		$prodi = $this->input->post("prodi");
		$jenjang = $this->input->post("jenjang");
		switch($jenjang){
			case 1:
				if ($jalur == '02' || $jalur == '01' || $jalur == 'KP'){
					$this->smarty->assign('jalur',$jalur);
					$this->smarty->assign('prodi',$prodi);
					$this->smarty->display('cmb/detail_bayar.html');
				}
                                if ($jalur == '03'){
                                    $this->smarty->assign('jalur',$jalur);
                                    $this->smarty->assign('prodi',$prodi);
                                    $this->smarty->display('cmb/detail_bayar.html');
                                    
                                }
			break;
			case 2:
				$this->smarty->assign('jalur',$jalur);
				$this->smarty->assign('prodi',$prodi);
				$this->smarty->display('cmb/detail_s2.html');
			break;
		}
	
	}
	
	function formulir(){		
		$noreg = $this->uri->segment(3);		
		
		$kode = $this->db2->query("SELECT kode FROM adis_smb_form WHERE nomor = '$noreg'")->row();
		$kode = $kode->kode;
			
		$this->msmb->mProfil($noreg);
		$this->mportal->mSelectPendidikan($kode);		
		$this->mportal->mOrganisasi($kode);
		$this->mportal->mPrestasi($kode);
		$this->mportal->mSelectOrtu($kode);
		$this->mportal->mSelectSaudara($kode);
		
		$this->smarty->assign('modul','smb/');
		$site = "Formulir";			
		$this->smarty->assign('lokasi',"Form Pendaftaran Mahasiswa Baru");	
		$this->smarty->assign('site',$site);	
		$this->smarty->display('index.html');	
		
	}
	
	function payment(){
		$this->load->model('mportal');		
		$nomor = $this->uri->segment(3);
		
		$kodew = $this->db->query("SELECT kode, stsReapplyPaid, SUBSTR(bukaSmb,12, 2) as jalur, SUBSTR(bukaSmb,6,1 ) as jenjang
				FROM adis_smb_form WHERE nomor = '$nomor'")->row();
                
                if ($kodew->stsReapplyPaid != 1){
                    $kode = $kodew->kode;
                    $jenjang = $kodew->jenjang;

                    $this->msmb->mProfil($nomor);
                    $this->mportal->selectType();

                    $qr = $this->db2->query("SELECT nama FROM adis_smb_usr_kel WHERE smbUsr = '$kode' AND erased = 0");
                    $saudara = $qr->num_rows();
                    $this->smarty->assign('saudara',$saudara);
                    $this->smarty->assign('jalur',$kodew->jalur);

                    $this->mportal->mPaidDaftarUlang($kode,$saudara,$jenjang);

                    SWITCH($jenjang){
                            case '1':
                                    if ($kodew->jalur == 'KP'){
                                            $site = "DaftarUlangKP";
                                    }else{
                                            $site = "DaftarUlang";		
                                    }
                            break;
                            case '2':
                                    $site = 'DaftarUlangPasca';
                            break;
                    }

                    $this->smarty->assign('site',$site);
                    $this->smarty->assign('lokasi',"Biaya Daftar Ulang");
                    $this->smarty->display('index.html');
                }else{
                   redirect ('cmb', 'refresh');
                }
	}
	
	function confirm_du(){
		if ($this->auth['level'] == '33' 
                        || $this->auth['level'] == '55' 
                        || $this->auth['level'] == '66' 
                        || $this->auth['level'] == '99'
                        || $this->auth['level'] == '101'
                    ){
			
			$this->load->model('mintegrasi');
			$date = date("Y-m-d H:i:s");
			$kode = $this->input->post('kode');
			$kode_jalur = $this->input->post('kode_jalur');
			$uri = $this->uri->segment(3);
			$jumlahBayar = str_replace(' ', '', str_replace('.', '', $this->input->post("jumlahBayar")));
			$update_string = "";		
			
			$file_name = '';
			$config['upload_path'] = './assets/upload/bukti_bayar';
			$config['allowed_types'] = 'gif|jpg|png|pdf';
			$config['max_size']    = '10000';
			$config['max_width']  = '1024';
			$config['max_height']  = '768';
			
			$this->load->library('upload', $config);
			
			$this->upload->initialize($config);
			
			$this->upload->set_allowed_types('*');
			
			$data['upload_data'] = '';
                        
                        $kodew = $this->db->query("SELECT kode, stsReapplyPaid, SUBSTR(bukaSmb,12, 2) as jalur, 
                            SUBSTR(bukaSmb,6,1 ) as jenjang FROM adis_smb_form 
                            WHERE kode = '$kode'")->row();
			$jenjang = $kodew->jenjang;
                        switch ($jenjang){
                            CASE "1":
//			Proses penyimpanan bukti bayar tanpa tanda berkas untu PSR dan Fellowship
                                if ($kode_jalur == '03' || $kode_jalur == 'PS' || $kode_jalur == 'BM'){
				
                                        $update_string = array('updateTime'=>$date,
                                                'updateUser'=>'Admin Admisi',
                                                'totalBiayaDaftarUlang'=>$jumlahBayar);
                                        $this->db2->where("smbUsr", $kode);
                                        $this->db2->update("adis_smb_usr_keu", $update_string);

                                        $this->db2->where('kode',$kode);
                                        $this->db2->update('adis_smb_form', array(
                                                'stsResultConfirm'=>1,
                                                'stsReapplyPaid'=>1,
                                                'reapplyBankTransferAmount'=>$jumlahBayar,
                                                'reapplyBankTransferTime'=>date("Y-m-d", strtotime($this->input->post('tanggalBayar'))),
                                                'reapplyBankTransferType'=>$this->input->post('typeTrans')
                                                // ,'stsReapplyPaidConfirm'=>1
                                        ));

                                        /*****Untuk Sementara Integrasi ke sistem ASIK Pemayaran Hanya Jalur PSR dan Fellowship*/
                                        if($kode_jalur == '03' || $kode_jalur == 'PS' || $kode_jalur == 'BM'){
                                            /*** Integrasi data Mahasiswa ke GTKEU */
                                                $this->mintegrasi->insert_mahasiswa($kode);
                                            /*** end */

                                            /**** Integrasi data pembayaran daftar ulang ke GTKEU ****/
                                                $this->mintegrasi->generate_tagihan($kode, $kode_jalur);
                                            /** end */
                                        }
                                        redirect ('cmb', 'refresh');
        //			Penyimpanan bukti bayar mahasiswa yang menggunakan upload dokumen REG,S2,PAR	
                                }else{
                                        $uploadFile = $this->upload->do_upload('bukti');
                                        if (!$uploadFile){ // CEK KONDISI FILE UPLOAD

                                                echo "<script>alert('Penyimpanan Gagal!! :".$this->upload->display_errors()."');history.go(-1);</script>";

                                        }else{

                                                $data = $this->upload->data();
                                                $file_name = $data['file_name'];

                                                $update_string = array('updateTime'=>$date,
                                                        'updateUser'=>'Admin Admisi',
                                                        'totalBiayaDaftarUlang'=>$jumlahBayar,
                                                        'buktiBayarDaftarUlang'=>$file_name,
                                                        'noRekPengirimDaftarUlang'=>$this->input->post('no_rek_cmb'),
                                                        'namaRekPengirimDaftarUlang'=>$this->input->post('nama_rek_cmb'),
                                                        'noAtmCardDaftarulang'=>$this->input->post('noAtmCard'),
                                                        'cicilan1'=>$this->input->post('angsuran1'),
                                                        'cicilan2'=>$this->input->post('angsuran2'),
                                                        'cicilan3'=>$this->input->post('angsuran3'),
                                                        'tolakDU'=>0);

                                                if ($kode && $uri ){
                                                    $this->db2->trans_begin();
													
                                                        $this->db2->where('kode',$kode);
                                                        $this->db2->update('adis_smb_form', array(
                                                                'stsResultConfirm'=>1,
                                                                'stsReapplyPaid'=>1,
                                                                'reapplyBankTransferAmount'=>$jumlahBayar,
                                                                'reapplyBankTransferTime'=>date("Y-m-d", strtotime($this->input->post('tanggalBayar'))),
                                                                'reapplyBankTransferType'=>$this->input->post('typeTrans')
                                                                // ,'stsReapplyPaidConfirm'=>1
                                                        ));


                                                        $this->db2->where("smbUsr", $kode);
                                                        $this->db2->update("adis_smb_usr_keu", $update_string);

                                                        /*****Untuk Sementara Integrasi ke sistem ASIK Pemayaran Hanya Jalur Reguler*/
                                                        if($kode_jalur == '01' || $kode_jalur == 'KP' || $kode_jalur == '02'){
                                                                /*** Integrasi data Mahasiswa ke GTKEU */
                                                                        $insert_mah = $this->mintegrasi->insert_mahasiswa($kode);
                                                                /*** end */
															if ($insert_mah == 1){
                                                                /**** Integrasi data pembayaran daftar ulang ke GTKEU ****/
                                                                        $this->mintegrasi->generate_tagihan($kode, $kode_jalur);
                                                                /** end */
															}
                                                        }
														
														if ($this->db2->trans_status() === FALSE && $insert_mah == 1)
                                                        {
                                                            $this->db2->trans_rollback();
                                                        }
                                                        else
                                                        {
                                                            $this->db2->trans_commit();
                                                        }
                                                        redirect ('cmb', 'refresh');
                                                }

                                        }


                                }
                            break;
                            CASE "2":
                                $update_string = array('updateTime'=>$date,
                                        'updateUser'=>'Admin Admisi',
                                        'totalBiayaDaftarUlang'=>$jumlahBayar);
                                $this->db2->where("smbUsr", $kode);
                                $this->db2->update("adis_smb_usr_keu", $update_string);

                                $this->db2->where('kode',$kode);
                                $this->db2->update('adis_smb_form', array(
                                        'stsResultConfirm'=>1,
                                        'stsReapplyPaid'=>1,
                                        'reapplyBankTransferAmount'=>$jumlahBayar,
                                        'reapplyBankTransferTime'=>date("Y-m-d", strtotime($this->input->post('tanggalBayar'))),
                                        'reapplyBankTransferType'=>$this->input->post('typeTrans')
                                        // ,'stsReapplyPaidConfirm'=>1
                                ));

                                /*****Untuk Sementara Integrasi ke sistem ASIK Pemayaran Hanya Jalur PSR dan Fellowship*/
                                /*** Integrasi data Mahasiswa ke GTKEU */
                                    $this->mintegrasi->insert_mahasiswa($kode);
                                /*** end */
                                        
                                redirect ('cmb', 'refresh');
                            break;
                        }
			
                        
                        $this->mail("div.keu@paramadina.ac.id");
		}else{ redirect('/home','refresh');}
		
		
	}
	
	function confirm_keu(){
		if ($this->auth['level'] == '77' 
                        || $this->auth['level'] == '99' 
                        || $this->auth['level'] == '101' 
                    ){
                    
		
                    $this->load->model('mintegrasi');
                    $action = $this->uri->segment(3);
                    $kode = $this->input->post('kode');
                    $kode_jalur = $this->input->post('kode_jalur');
                    $nama_op = $this->input->post('nama_operator');		
                    $date = date("Y-m-d H:i:s");
                    
                    
                    $stsMahasiswa = $this->db->query("SELECT kode, stsReapplyPaidConfirm
			FROM adis_smb_form WHERE kode = '$kode'")->row_array();
                    
                    if ($stsMahasiswa['stsReapplyPaidConfirm'] == '1'){
                        echo "<script>alert('Data Mahasiswa sudah dikonfirmasi');</script>";
                        redirect('cmb','refresh');
                    }else{

                        $level = $this->auth['level'];

                        if ($level == '99' || $level == '77' ){
                                if ($kode && $action == 'do'){
                                        $this->db2->where('kode',$kode);
                                        $this->db2->update('adis_smb_form', array(
                                                'updateUser'=>$nama_op,
                                                'updateTime'=>$date,
                                                'stsReapplyPaidConfirm'=>1
                                        ));			
                                        /**** Memanggil Fungsi Generate NIM **/
                                        $this->generateNIM($kode);


                                        #####Untuk Sementara Integrasi ke sistem ASIK Pemayaran Hanya Jalur Reguler*/
                                        if($kode_jalur == '01' || $kode_jalur == '03' || $kode_jalur == 'PS' || $kode_jalur == 'BM'){
                                                ####Integrasi data pembayaran daftar ulang ke GTKEU ****/
                                                        $this->mintegrasi->validasi_pembayaran($kode);
                                                #### end */

                                                #### Integrasi data pembayaran daftar ulang ke GTKEU ****/
                                                        $this->mintegrasi->update_nim($kode);
                                                #### end */
                                        }else{
                                            ######UNtuk memasukkan data mahasisa ke database GTKEU
                                            /*** Integrasi data Mahasiswa ke GTKEU */
                                                $this->mintegrasi->insert_mahasiswa($kode);
                                            /*** end */
                                        }

                                        #######Integrasi Ke ASIK Akademik*/
                                        #### Integrasi data Mahasiswa Lulus ke ASIK ****/
                                                $this->mintegrasi->upload_mahasiswa($kode);
                                        #### end */
                                        
                                        //redirect ('cmb', 'refresh');
                                }else{
                                        echo "<script>alert('Use Validation Form');</script>";
                                }
                        }else if ($this->auth['level'] == '101'){
                            if ($kode && $action == 'do'){
                                    $this->db2->where('kode',$kode);
                                    $this->db2->update('adis_smb_form', array(
                                            'updateUser'=>$nama_op,
                                            'updateTime'=>$date,
                                            'stsReapplyPaidConfirm'=>1
                                    ));			
                                    /**** Memanggil Fungsi Generate NIM **/
                                    $this->generateNIM($kode);
                                    
                                ######UNtuk memasukkan data mahasisa ke database GTKEU
                                    /*** Integrasi data Mahasiswa ke GTKEU */
                                        $this->mintegrasi->insert_mahasiswa($kode);
                                    /*** end */                                        
                                        
                                #######Integrasi Ke ASIK Akademik*/
                                #### Integrasi data Mahasiswa Lulus ke ASIK ****/
                                        $this->mintegrasi->upload_mahasiswa($kode);
                                #### end */
                            }
                            
                        }else{
                                echo "<script>alert('Youe Are not a Man!');</script>";
                        }
                    }
                }else{ redirect('/home','refresh');}
		redirect ('cmb', 'refresh');
		
	}
	
	function detailPayment(){
		$this->load->model('mportal');		
		$nomor = $this->uri->segment(3);
		
		$kodew = $this->db->query("SELECT kode, stsReapplyPaidConfirm, SUBSTR(bukaSmb,12, 2) as jalur, SUBSTR(bukaSmb,6,1 ) as jenjang
					FROM adis_smb_form WHERE nomor = '$nomor'")->row();
                
                if ($kodew->stsReapplyPaidConfirm == '1'){
                    redirect('cmb', 'refresh');
                }else{
                    $kode = $kodew->kode;
                    $jenjang = $kodew->jenjang;
                    $jalur = $kodew->jalur;

                    $this->msmb->mProfil($nomor);
                    $this->mportal->selectType();

                    $qr = $this->db2->query("SELECT nama FROM adis_smb_usr_kel WHERE smbUsr = '$kode' AND erased = 0");
                    $saudara = $qr->num_rows();
                    $this->smarty->assign('saudara',$saudara);
                    $this->smarty->assign('jalur',$kodew->jalur);

                    $this->mportal->mPaidDaftarUlang($kode,$saudara,$jenjang);
                    switch($jenjang){
                            case '1':
                                    if ($jalur == 'KP'){
                                            $site = "DaftarUlangKPConfirmed";
                                    }else{					
                                            $site = "DaftarUlangConfirmed";	
                                    }
                            break;
                            case '2':
                                    $site = "DaftarUlangPascaConfirmed";
                            break;
                    }		
                    $this->smarty->assign('site',$site);
                    $this->smarty->assign('lokasi',"Detail Daftar Ulang - Verifikasi Untuk Mendapatkan NIM");	
                    $this->smarty->display('index.html');
                }
	}
	
	function generateNIM($kode){
		$val = $this->db->query("SELECT nomor FROM adis_smb_form WHERE kode = '$kode';")->row_array();
		$val = $val['nomor'];
		
		$sql_p= "SET @_periode:=(SELECT LEFT(bukaSmb,8) FROM adis_smb_form WHERE nomor = '$val');";
		$sql  = "set @_bukaSmb:=(SELECT bukaSmb FROM adis_smb_form WHERE nomor = '$val');";
		$sql2 = "set @_tahun:=left(@_bukasmb,4);";
		$sql3 = "set @_tahun_yy:=right(@_tahun,2);";
		$sql4 = "set @_jenjang:=right(left(@_bukasmb,6),1);";
		$sql5 = "set @_periode:=left(@_bukasmb,8);";
		$sql6 = "set @_sesi:=right(left(@_bukasmb,8),1);";
		$sql7 = "set @_prodi:=right(@_bukasmb,4); ";
		//$sql8 = "set @_nimOrder:=(select max(nimOrder) from adis_smb_form where bukaSmb=@_bukasmb);";
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
        
        function mail($email = ""){
                //$email = "rahmadsyalevi@gmail.com";
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
                $pesan ="Dear Bagian Keuangan, </br> Humas sudah memasukan data Mahasiswa yang sudah melakukan "
                                . "Pendaftaran Ulang. Silahkan mengunjungi aplikasi "
                                . "<a href='".$this->host."home' terget='_blank'>Admisi</a> Untuk melakukan Approval. </br></br> "
                                . "Terima Kasih.</br></br></br> "
                                . "Note : Inilah adalah pesan yang dikirim oleh system.";
				
                $this->load->library('email', $config);
                $this->email->from('admin@admisission.paramadina.ac.id', "Humas Paramadina");
                $this->email->to($email);
                $this->email->bcc("rahmad.syalevi@paramadina.ac.id");
                $this->email->subject("Pembayaran Daftar Ulang Calon Mahasiswa Baru");
                $this->email->message($pesan);

                $data['message'] = "Sorry Unable to send email...";	
                if($this->email->send()){					
                        $data['message'] = "Mail sent...";			
                }
	}
	
	function edit_pembayaran(){
		
		$this->db->trans_begin(); 
		
		$sql = "SELECT * FROM `tbl_bayar_mahasiswa`;";
		$data_keu = $this->db2->query($sql)->result_array();
		
		$data = array();
		
		foreach ($data_keu as $v){
			//$data['reapplyBankTransferTime'] = $v['tgl_bayar'];
			$data['tgl_bayar_keu'] = $v['tgl_bayar'];
			$data['total_dibayarkan_keu'] = $v['total_dibayarkan'];
			
			$edit_data = $this->db2->where('noreg', $v['noreg']);
			$edit_data = $this->db2->update('tbl_bayar_mahasiswa_humas', $data);
		}
		
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			echo "Data not saved";
		} else{
			echo $this->db->trans_commit();
		}
	}
	
	function deleteMhs(){
            
            $this->load->model('mintegrasi');
            $noReg = $this->input->post('id');
            $method = $this->input->post('method');
            $nim = "";
            $this->db->trans_begin(); 
            
            if ($noReg && $method){
                $qry = "SELECT kode,nim FROM adis_smb_form WHERE nomor = '$noReg'";
                $qry = $this->db2->query($qry)->row_array();
                $kode = $qry['kode'];
                $nim = $qry['nim'];
                                
                $delete = $this->db2->query("SET @kode := '$kode';");
                $delete = $this->db2->query("DELETE FROM adis_smb_form WHERE kode = @kode;");
                $delete = $this->db2->query("DELETE FROM adis_smb_usr_edu WHERE smbUsr = @kode;");
                $delete = $this->db2->query("DELETE FROM adis_smb_usr_kel WHERE smbUsr = @kode;");
                $delete = $this->db2->query("DELETE FROM adis_smb_usr_keu WHERE smbUsr = @kode;");
                $delete = $this->db2->query("DELETE FROM adis_smb_usr_kursus WHERE smbUsr = @kode;");
                $delete = $this->db2->query("DELETE FROM adis_smb_usr_org WHERE smbUsr = @kode;");
                $delete = $this->db2->query("DELETE FROM adis_smb_usr_pribadi WHERE kode = @kode;");
                $delete = $this->db2->query("DELETE FROM adis_smb_usr WHERE kode = @kode;");
            }
            
            if($this->db->trans_status() == false){
                    $this->db->trans_rollback();
                    echo "Data not saved";
            } else{
                    if ($nim){$this->mintegrasi->delete_mahasiswa($nim);}
                    echo $this->db->trans_commit();
            }
        }
	
	function mailZimbra(){
            
            $sql = "SELECT A.nim, A.username, A.email as email_zimbra, A.password, C.email, D.nama  "
                    . " FROM tbl_email_zimbra A"
                    . " LEFT JOIN adis_smb_form B ON B.nim = A.nim"
                    . " LEFT JOIN adis_smb_usr C ON C.kode = B.kode"
                    . " LEFT JOIN adis_smb_usr_pribadi D ON D.kode = B.kode" 
					. " WHERE respon = 0 OR respon IS NULL";
            $detail_mah = $this->db2->query($sql)->result_array();
            
            $config = Array(
                      'protocol' => 'smtp',
                      'smtp_host' => 'students.paramadina.ac.id',
                      'smtp_port' => 25,
                      'smtp_user' => 'orangbaik@students.paramadina.ac.id', // change it to yours
                      'smtp_pass' => 'S@l4mb3l@k4ng', // change it to yours
                      'mailtype' => 'html',
                      'charset' => 'utf-8',
                      'wordwrap' => false,
                      'crlf' => '\r\n',
                      'newline' => '\r\n'
            );	
			$this->load->library('email', $config);
			$this->load->library('parser');
			
			echo "<pre>";
			print_r($detail_mah);
            
            foreach($detail_mah as $v){
                
                $this->email->from('div.it@paramadina.ac.id', "Div IT Paramadina");
                $this->email->to($v['email']);
                $this->email->subject("Informasi Akun Email, E-learning & Hotspot Universitas Paramadina");	

                $data = array( 'nama' => $v['nama'],
                               'username' => $v['username'],
                               'password' => $v['password']);

                $htmlMessage = $this->parser->parse('cmb/email_template.html', $data, true);
                $this->email->message($htmlMessage);

                $data['message'] = "Sorry Unable to send email...";	
				$send = $this->email->send();
                if($send){					
                        $data['message'] = "Mail sent...";	
						$this->db2->where('nim', $v['nim']);
						$this->db2->update('tbl_email_zimbra', array('respon'=>'1'));
                        echo 1;
                }else{
						$this->db2->where('nim', $v['nim']);
						$this->db2->update('tbl_email_zimbra', array('respon'=>'0'));
                        echo 0;
                }
                
            }
            
            
	}
}
