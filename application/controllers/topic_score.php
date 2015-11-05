<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Topic_Score extends CI_Controller {

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
       $this->load->view('topic_score');
   }

}

/* End of file Topic_Score.php */
