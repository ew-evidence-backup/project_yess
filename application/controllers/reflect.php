<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reflect extends CI_Controller {

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
       $this->load->view('reflect');
   }

}

/* End of file Reflect.php */
