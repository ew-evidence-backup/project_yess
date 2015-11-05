<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upgrade extends CI_Controller {

/**
   class comments 
 
 */

   public function __construct()
   {
     parent::__construct(); 
       // Your own constructor code
   }

   public function index()
   {
       $this->load->view('upgrade');
   }

    function paypal(){



    }

}

/* End of file Upgrade.php */
