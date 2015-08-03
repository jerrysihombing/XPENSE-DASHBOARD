<?php

date_default_timezone_set('Asia/Jakarta');

class AdminController extends MY_Controller {

        public function __construct() {
                parent::__construct();
                
                if (!$this->isLoggedIn()) {
                        header('Location: ' . base_url() . 'login');
                        exit;
                }
                
                if (!$this->isAllowable('System Administration')) {
                        header('Location: ' . base_url() . 'unauthorized');
                        exit;
                }
                
                $this->load->model('Menumanager');
                $this->load->model('Rolemanager');
                $this->load->model('Usermanager');
                $this->load->model('Site');
        }
        
        # -- user section --
        public function stores($id) {
                $stores = $this->Usermanager->loadStores($id);
                $html = "";
                foreach($stores as $store) {
                        $html .= $store->branch_code . " | ";
                }
                if ($html != "")
                        $html = substr($html, 0, strlen($html)-3);
                echo $html;
        }
        
        public function user($action = null, $id = null) {
                $data['title'] = 'Expense Report - System Administration';
                $data['userName'] = $this->getUserName();
                $data['allowUserModule'] = $this->isAllowable('Expense Report');
                
                $this->load->view('templates/header.admin.html', $data);
                
                switch ($action) {
                        case null:
                                $saved = $this->session->userdata('saved');
                                $crudMsg = $this->session->userdata('crudMsg');
                                if ($saved == "saved")
                                        $data['hide'] = '';
                                else
                                        $data['hide'] = 'hide';
                                $data['crudMsg'] = $crudMsg;
                                
                                $this->load->view('admin/user.list.html', $data);
                                $this->session->set_userdata('saved', '');
                                break;
                        
                        case "add":
                                $roles = $this->Rolemanager->loadAll();
                                $stores  = $this->Site->loadAllStoreArray();
                                
                                $storeCount = sizeof($stores);
                                $fgCount = ceil($storeCount/3);
                                $remain = $storeCount - $fgCount;
                                $sgCount = ceil($remain/2);
                                $data['roles'] = $roles;
                                $data['stores'] = $stores;
                                $data['fgCount'] = $fgCount;
                                $data['sgCount'] = $sgCount;
                                $data['storeCount'] = $storeCount;
                                
                                $this->load->view('admin/user.add.html', $data);
                                break;
                        
                        case "save":
                                $this->createUser();
                                break;
                        
                        case "saveerror":
                                $saved = $this->session->userdata('saved');
                                $errorMsg = $this->session->userdata('errorMsg');
                                $data['errorMsg'] = $errorMsg;
                                
                                $this->load->view('admin/saveerror.html', $data);
                                break;
                        
                        case "edit":
                                $roles = $this->Rolemanager->loadAll();
                                $stores  = $this->Site->loadAllStoreArray();
                                $user = $this->Usermanager->load($id);
                                $userStores = $this->Usermanager->loadStoresArray($id);
                                
                                $storeCount = sizeof($stores);
                                $fgCount = ceil($storeCount/3);
                                $remain = $storeCount - $fgCount;
                                $sgCount = ceil($remain/2);
                                $data['id'] = $id;
                                $data['user'] = $user;
                                $data['roles'] = $roles;
                                $data['stores'] = $stores;
                                $data['fgCount'] = $fgCount;
                                $data['sgCount'] = $sgCount;
                                $data['storeCount'] = $storeCount;
                                $data['userStores'] = $userStores;
                        
                                $this->load->view('admin/user.edit.html', $data);
                                break;
                        
                        case "update":
                                $this->updateUser($id);
                                break;
                        
                        case "delete":
                                $this->deleteUser($id);
                                break;
                        
                        case "detail":
                                $this->load->view('admin/user.detail.html');
                                break;
                }
                
                $this->load->view('templates/footer.html');
        }
        
        private function createUser() {
                $input = $this->input->post();
                $userName = $this->session->userdata('userName');
                $currentTime = date('Y-m-d H:i:s');
                
                if (!$this->Usermanager->isExist($input['userId'])) {
                        $ret = $this->Usermanager->addNew($input['userId'], $input['userName'], $input['passwd'], $input['email'], '', '', $input['roleName'], $input['active'], $input['chkStore'], $userName, $currentTime);
                        if ($ret) {
                                $this->session->set_userdata('saved', 'saved');
                                $this->session->set_userdata('crudMsg', 'A new user has been successfully added.');
                                header('Location: ' . base_url() . 'admin/user');
                                exit;
                        }
                        else {
                                header('Location: ' . base_url() . 'admin/user/saveerror');
                                exit;
                        }
                }
                else {
                        $this->session->set_userdata('errorMsg', 'User ' . $input['userId'] . ' already exist.');
                        header('Location: ' . base_url() . 'admin/user/saveerror');
                        exit;
                }
        }
        
        private function updateUser($id) {
                $input = $this->input->post();
                $userName = $this->session->userdata('userName');
                $currentTime = date('Y-m-d H:i:s');
                
                $ret = $this->Usermanager->update($id, $input['userId'], $input['userName'], $input['email'], '', '', $input['roleName'], $input['active'], $input['chkStore'], $userName, $currentTime);
                if ($ret) {
                        $this->session->set_userdata('saved', 'saved');
                        $this->session->set_userdata('crudMsg', 'Successfully modified user.');
                        header('Location: ' . base_url() . 'admin/user');
                        exit;
                }
                else {
                        header('Location: ' . base_url() . 'admin/user/saveerror');
                        exit;
                }
        }
        
        private function deleteUser($id) {
                $ret = $this->Usermanager->remove($id);
                if ($ret) {
                        $this->session->set_userdata('saved', 'saved');
                        $this->session->set_userdata('crudMsg', 'User has been deleted successfully.');
                        header('Location: ' . base_url() . 'admin/user');
                        exit;
                }
                else {
                        header('Location: ' . base_url() . 'admin/user/saveerror');
                        exit;
                }
        }
        # -- end user section --
        
        # -- role section --
        public function menus($id) {
                $menus = $this->Rolemanager->loadDetail($id);
                $html = "";
                foreach($menus as $menu) {
                        $html .= $menu->title . "<br>";
                }
                if ($html != "")
                        $html = substr($html, 0, strlen($html)-4);
                echo $html;
        }
        
        public function role($action = null, $id = null) {
                $data['title'] = 'Expense Report - System Administration';
                $data['userName'] = $this->getUserName();
                $data['allowUserModule'] = $this->isAllowable('Expense Report');
                
                $this->load->view('templates/header.admin.html', $data);
                
                switch ($action) {
                        case null:
                                $saved = $this->session->userdata('saved');
                                $crudMsg = $this->session->userdata('crudMsg');
                                if ($saved == "saved")
                                        $data['hide'] = '';
                                else
                                        $data['hide'] = 'hide';
                                $data['crudMsg'] = $crudMsg;
                                
                                $this->load->view('admin/role.list.html', $data);
                                $this->session->set_userdata('saved', '');
                                break;
                        
                        case "add":
                                $menus  = $this->Menumanager->loadAllArray();
                                
                                $menuCount = sizeof($menus);
                                $fgCount = ceil($menuCount/2);
                                $data['menus'] = $menus;
                                $data['fgCount'] = $fgCount;
                                $data['menuCount'] = $menuCount;
                                
                                $this->load->view('admin/role.add.html', $data);
                                break;
                        
                        case "save":
                                $this->createRole();
                                break;
                        
                        case "saveerror":
                                $saved = $this->session->userdata('saved');
                                $errorMsg = $this->session->userdata('errorMsg');
                                $data['errorMsg'] = $errorMsg;
                                
                                $this->load->view('admin/saveerror.html', $data);
                                break;
                        
                        case "edit":
                                $menus  = $this->Menumanager->loadAllArray();
                                $role = $this->Rolemanager->load($id);
                                $roleMenus = $this->Rolemanager->loadDetailArray($id);
                                
                                $menuCount = sizeof($menus);
                                $fgCount = ceil($menuCount/2);
                                $data['menus'] = $menus;
                                $data['fgCount'] = $fgCount;
                                $data['menuCount'] = $menuCount;
                                $data['id'] = $id;
                                $data['role'] = $role;
                                $data['roleMenus'] = $roleMenus;
                                
                                $this->load->view('admin/role.edit.html', $data);
                                break;
                        
                        case "update":
                                $this->updateRole($id);
                                break;
                        
                        case "delete":
                                $this->deleteRole($id);
                                break;
                        
                        case "detail":
                                $this->load->view('admin/role.detail.html');
                                break;
                }
                
                $this->load->view('templates/footer.html');
        }
        
        private function createRole() {
                $input = $this->input->post();
                $userName = $this->session->userdata('userName');
                $currentTime = date('Y-m-d H:i:s');
                
                if (!$this->Rolemanager->isExist($input['roleName'])) {
                        $ret = $this->Rolemanager->addNew($input['roleName'], $input['description'], $input['chkMenu'], $userName, $currentTime);
                        if ($ret) {
                                $this->session->set_userdata('saved', 'saved');
                                $this->session->set_userdata('crudMsg', 'A new role has been successfully added.');
                                header('Location: ' . base_url() . 'admin/role');
                                exit;
                        }
                        else {
                                header('Location: ' . base_url() . 'admin/role/saveerror');
                                exit;
                        }
                }
                else {
                        $this->session->set_userdata('errorMsg', 'Role ' . $input['roleName'] . ' already exist.');
                        header('Location: ' . base_url() . 'admin/role/saveerror');
                        exit;
                }
        }
        
        private function updateRole($id) {
                $input = $this->input->post();
                $userName = $this->session->userdata('userName');
                $currentTime = date('Y-m-d H:i:s');
                
                $ret = $this->Rolemanager->update($id, $input['roleName'], $input['description'], $input['chkMenu'], $userName, $currentTime);
                if ($ret) {
                        $this->session->set_userdata('saved', 'saved');
                        $this->session->set_userdata('crudMsg', 'Successfully modified role.');
                        header('Location: ' . base_url() . 'admin/role');
                        exit;
                }
                else {
                        header('Location: ' . base_url() . 'admin/role/saveerror');
                        exit;
                }
        }
        
        private function deleteRole($id) {
                $ret = $this->RoleManager->remove($id);
                if ($ret) {
                        $this->session->set_userdata('saved', 'saved');
                        $this->session->set_userdata('crudMsg', 'Role has been deleted successfully.');
                        header('Location: ' . base_url() . 'admin/role');
                        exit;
                }
                else {
                        header('Location: ' . base_url() . 'admin/role/saveerror');
                        exit;
                }
        }
        # -- end role section --
}