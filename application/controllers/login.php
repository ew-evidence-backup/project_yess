<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

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
       $this->load->view('login');
   }

    public function submit(){

        $this->load->model('auth', 'Auth');
        $this->Auth->login();

    }

}

/* End of file Login.php */
