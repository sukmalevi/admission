<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CUTI_Controller extends CI_Controller{
	
	function CUTI_Controller () {
		parent::__construct();
		
		date_default_timezone_set("Asia/Jakarta");
		
		$this->load->database();
		
		$this->cuti = $this->config->item('base_url');		
		$this->host	= $this->config->item('base_url');
		$this->USER = unserialize(base64_decode($this->session->userdata('cuti_parmad')));	
		$this->cmb = unserialize(base64_decode($this->session->userdata('calon_mah')));	
		
		$uri = $this->uri->segment(1);
		$this->smarty->assign('pages',"");
		
		/*
		if((! $this->USER && $uri == "") || (! $this->USER  && $uri == "home" || $uri == "dashboard")) {
		
			header("Location: " . $this->host."home");
			
		} 
		else if(! $this->cmb && $uri == "site"){
			
			header("Location: " . $this->host."site");
			
		}	

		*/	
		// else if( (! $this->cmb || $this->cmb) && $uri == "registrasi"){
		
			// //header("Location: " . $this->host."registrasi");
			// redirect ('registrasi', 'refresh');
		// }
		
		
		$this->smarty->assign('USER', $this->USER);
		$this->smarty->assign('host', $this->cuti);
		
		
		$level = $this->USER['level'];
		$this->smarty->assign('levelID',$level);
		
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("If-Modified-Since: Mon, 22 Jan 2008 00:00:00 GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Cache-Control: private");
		header("Pragma: no-cache");
	}
}
?>