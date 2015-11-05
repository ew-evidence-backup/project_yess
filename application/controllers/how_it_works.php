<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class How_It_Works extends CI_Controller {

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
       $this->load->view('how_it_works');
   }

}

/* End of file How_It_Works.php */
