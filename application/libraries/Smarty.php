<?php if (!defined('BASEPATH')) exit ('No Direct Script Acces Allowed');
require_once (APPPATH . 'libraries/smarty/libs/Smarty.class.php');
class CI_Smarty extends Smarty {
	
	public function __construct(){
		parent::__construct();
		$this->setTemplateDir(APPPATH.'views/');
		$this->setCompileDir(APPPATH.'chace/');
	}
}