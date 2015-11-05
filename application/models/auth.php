<?php

/**
 * Author: Evin Weissenberg
 * Date: 2014
 */
class Auth extends CI_Model {

    private $username;
    private $password;

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }


    function register($data = array()) {
        //Check to see if username already available

        if ($data['fb'] == TRUE) {


        } else {


        }

    }

    function login() {


        try {

            if (empty($_REQUEST['Email']) || empty($_REQUEST['Password'])) {

                throw new Exception('Email or Password values are missing.');

            } else {

                $Email = $this->db->escape($_REQUEST['Email']);
                $Password = $this->db->escape($_REQUEST['Password']);
                $query = $this->db->query("SELECT Email, Password, Type FROM Users WHERE Email=$Email AND Password=$Password");
                $row = $query->row();


                if (isset($row->Email) == trim($_REQUEST['Email']) AND isset($row->Password) == trim($_REQUEST['Password'])) {


                    $userSessionObject = $this->db->query("SELECT * FROM Users WHERE Email=$Email");

                    if ($userSessionObject->row()=='') {

                        throw new Exception('Session Object is empty');

                    }

                    $_SESSION['data'] = $userSessionObject->row();
                    //print_r($_SESSION);


                    if ($row->Type == '1') {

                        header('location: /topic');

                    } elseif ($row->Type == '2') {

                        header('location: /topic');

                    }



                } else {

                    header('location: /login?s=f');

                }
            }
        } catch (Exception $e) {

            echo $e->getMessage();
            //echo $e->getFile();
            //echo $e->getLine();

        }
    }

    function isLoggedIn() {


        if (!empty($_SESSION['data'][0]['Username'])) {


            if ($_SESSION['data'][0]['user_type_id'] == 1) {


                header('location: /admin/dashboard');

            } elseif ($_SESSION['user_type_id'] == 2) {


                header('location: /user/dashboard');

            }

        } else {

            //header('location: /');

        }
    }

    function logOut() {

        session_destroy();
        header('location: / ');


    }

    function deactivateUser($username) {

        $Username = $this->db->escape($username);
        $query = $this->db->query("UPDATE Users set IsActive=0 WHERE Username=$Username");
        return TRUE;

    }


}