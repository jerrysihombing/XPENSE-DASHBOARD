<?php

date_default_timezone_set('Asia/Jakarta');

class ComposeController extends MY_Controller {

        public function __construct() {
                parent::__construct();
                
                if (!$this->isLoggedIn()) {
                        header('Location: ' . base_url() . 'login');
                        exit;
                }
                
                if (!$this->isAllowable('Expense Report')) {
                        header('Location: ' . base_url() . 'unauthorized');
                        exit;
                }
                
                $this->load->model('Site');
        }
        
        public function index($fileName = null) {
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
                
                $data['fileName'] = $fileName;
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
                $this->load->view('compose/index.html');
                $this->load->view('templates/footer.html');
        }
        
        public function send() {
                $recipient = $this->input->post("recipient");
                $notes = $this->input->post("notes");
                $notes = preg_replace('/<(\s*)img[^<>]*>/i', '', $notes);
                $fileName = $this->input->post("fileName");
                      
                $config = Array(
                        'mailtype' => 'html'
                );
                $this->load->library('email');
                $this->email->initialize($config);
                $this->email->from('admin@expensedashboard.yogya.com', 'Admin Expense Dashboard');
                $this->email->to($recipient);
                #$this->email->cc('another@another-example.com');
                #$this->email->bcc('them@their-example.com');
                
                $this->email->subject('Expense Report');
                $this->email->message($notes);
                $this->email->attach(CHART_FORGE . '/' . $fileName);
                
                $this->email->send();
                
                #echo $this->email->print_debugger();
        }
        
}