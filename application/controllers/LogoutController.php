<?php

class LogoutController extends MY_Controller {

        public function __construct() {
                parent::__construct();
        }
        
        public function index() {
                $this->logout();
                header('Location: ' . base_url() . 'login');
                exit;
        }
        
}