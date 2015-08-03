<?php

date_default_timezone_set('Asia/Jakarta');

class ChpassController extends MY_Controller {

        public function __construct() {
                parent::__construct();
                
                if (!$this->isLoggedIn()) {
                        header('Location: ' . base_url() . 'login');
                        exit;
                }
                
                $this->load->model('Site');
                $this->load->model('Usermanager');
        }
        
        public function index() {
                $currTime = strtotime('-2 months');
                $currYear = date('Y', $currTime);
                $currMonth = date('n', $currTime);
                $actYear = $currYear;
                $actMonth = $currMonth;
                
                $storeCode = "";
                $clusterCode = "";
                # regional use same value as cluster!
                $regionalCode = $clusterCode;
                
                $data['allowAdminModule'] = $this->isAllowable('System Administration');
                $data['userName'] = $this->getUserName();
                if ($this->getUserId() == 'admin')
                        //$data['stores'] = $this->Site->loadAllStore();
                        $stores = $this->Site->loadAllStore();
                else 
                        //$data['stores'] = $this->getStores();
                        $stores = $this->getStores();
                
                $data['fileName'] = "dummy";
                $data['ytdLbl'] = 'YTD Mode';
                $data['ytd'] = 'no';
                $data['stores'] = $stores;        
                $data['clusters'] = $this->Site->loadAllCluster();
                $data['regionals'] = $this->Site->loadAllRegional();
                $data['currYear'] = $currYear;
                $data['currMonth'] = $currMonth;
                $data['actYear'] = $actYear;
                $data['actMonth'] = $actMonth;
                $data['storeCode'] = $storeCode;
                $data['clusterCode'] = $clusterCode;
                $data['regionalCode'] = $regionalCode;
                $data['title'] = 'Expense Report';
                
                $this->load->view('templates/header.html', $data);
                $this->load->view('chpass/index.html');
                $this->load->view('templates/footer.html');
        }
        
        public function commit() {
                $userId = $this->getUserId();
                $input = $this->input->post();
                
                if ($this->Usermanager->isValidUser($userId, $input['passwd'])) {
                        $currentTime = date('Y-m-d H:i:s');
                        $this->Usermanager->modifyPasswdByUserId($userId, $input['newPasswd'], $userId, $currentTime);
                }
                else {
                        echo "Error: invalid current password";
                }
                
        }
        
}