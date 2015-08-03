<?php

class LoginController extends MY_Controller {

        public function __construct() {
                parent::__construct();
                $this->load->model('Usermanager');
                $this->load->model('Rolemanager');
        }
        
        public function index() {
                $loginMsg = $this->session->userdata('loginMsg');
                $hide = ($loginMsg ? '' : 'hide');
                $data['hide'] = $hide;
                $data['loginMsg'] = $loginMsg;
                $data['title'] = 'Expense Report - System Administration';
                
                $this->load->view('login/login.html', $data);
                $this->session->set_userdata('loginMsg', '');
        }
        
        public function auth() {
                $input = $this->input->post();
                
                if ($this->Usermanager->isValidUser($input['userId'], $input['passwd'])) {
                        $user = $this->Usermanager->loadByUserId($input['userId']);
                        $userInfo['userId'] = $user->user_id;
                        $userInfo['userName'] = $user->user_name;
                        $userInfo['roleName'] = $user->role_name;
                        $userInfo['menus'] =  $this->Rolemanager->loadDetailByNameArray($user->role_name);
                        $userInfo['stores'] = $this->Usermanager->loadStoresByUserId($input['userId']);
                        
                        $this->session->set_userdata('userInfo', $userInfo);
                        $this->session->set_userdata('loginMsg', '');
                        header('Location: ' . base_url() . 'dboard');
                        exit;
                }
                else {
                        $this->session->set_userdata('loginMsg', 'Invalid user id and/or password.');
                        header('Location: ' . base_url() . 'login');
                        exit;
                }        
        }
        
}