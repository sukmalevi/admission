<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SITE_Controller extends CI_Controller{
	
	function SITE_Controller () {
		parent::__construct();
		
		$this->load->database();
			
		$this->host	= $this->config->item('base_url');	
		$this->cmb = unserialize(base64_decode($this->session->userdata('calon_mah')));	
		
		$uri = $this->uri->segment(2);
		
		
		if(! $this->cmb && $uri == "site"){
			
			header("Location: " . $this->host."site");
			
		}		
		else if(! $this->cmb && $uri == "registrasi"){
		
			header("Location: " . $this->host."registrasi/registrasiForm");
		}
		
		$this->smarty->assign('host', $this->cuti);
		
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