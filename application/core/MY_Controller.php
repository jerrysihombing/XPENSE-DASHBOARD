<?php

class MY_Controller extends CI_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function isLoggedIn() {
        $userInfo = $this->session->userdata('userInfo');
        
        return isset($userInfo);
    }
    
    public function getUserId() {
        $userInfo = $this->session->userdata('userInfo');
        return $userInfo["userId"];
    }
    
    public function getUserName() {
        $userInfo = $this->session->userdata('userInfo');
        return (!empty($userInfo["userName"]) ? $userInfo["userName"] : $userInfo["userId"]);
    }
    
    public function getRoleName() {
        $userInfo = $this->session->userdata('userInfo');
        return $userInfo["roleName"];
    }
    
    public function getMenus() {
        $userInfo = $this->session->userdata('userInfo');
        return $userInfo["menus"];
    }
    
    public function getStores() {
        $userInfo = $this->session->userdata('userInfo');
        return $userInfo["stores"];
    }
    
    public function isAllowable($title) {
        $userInfo = $this->session->userdata('userInfo');
        $menus = $userInfo["menus"];
        
        $found = false;
        foreach($menus as $menu) {
            if ($menu["title"] == $title) {
                $found = true;
                break;
            }
        }
        
        return $found;
    }
    
    public function logout() {
        $this->session->sess_destroy();
    }
    
}