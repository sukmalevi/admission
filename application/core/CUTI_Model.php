<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//It extends the native model and all other models in the application/model will extend this one
class CUTI_Model extends CI_Model
{
        //The constructor
    function CUTI_Model()
    {
        parent::__construct();
        $this->USER = unserialize(base64_decode($this->session->userdata('cuti_parmad')));
        /*$this->USER['ID'] 			= $auth['ID'];
        $this->USER['USERNAME'] 	= $auth['USERNAME'];
        $this->USER['NAME'] 		= $auth['NAME'];
        $this->USER['LEVEL_NAME'] 	= $auth['LEVEL_NAME'];
        $this->USER['LEVEL_ID'] 	= $auth['LEVEL_ID'];
        */ 
    }
}