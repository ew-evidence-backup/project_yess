<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Topic extends CI_Controller {

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
       $this->load->view('topic');
   }

}

/* End of file Topic.php */
