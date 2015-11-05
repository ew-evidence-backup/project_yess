<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class My_Essays extends CI_Controller {

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
       $this->load->view('my_essays');
   }

}

/* End of file My_Essays.php */
