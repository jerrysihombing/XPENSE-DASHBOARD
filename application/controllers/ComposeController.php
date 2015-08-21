<?php

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
                $this->load->model('Contactmanager');
        }
        
        public function index($actYear = null, $actMonth = null, $storeCode = null, $clusterCode = null, $fileName = null) {
                $isHome = false;
                if ($storeCode != "cluster" && $storeCode != "regional") $fileName = $clusterCode;
                
                $currTime = strtotime('-2 months');
                $currYear = date('Y', $currTime);
                $currMonth = date('n', $currTime);
                
                if ($actYear == null) $actYear = $currYear;
                if ($actMonth == null) $actMonth = $currMonth;
                if ($storeCode == null) $storeCode = "";
                if ($clusterCode == null) $clusterCode = ""; else $clusterCode = str_replace('-', ' ', $clusterCode);
                # regional use same value as cluster!
                $regionalCode = $clusterCode;
                
                $data['allowGroup'] = $this->getRoleName() == "Administrator" ? true : false;
                $data['allowAdminModule'] = $this->isAllowable('System Administration');
                $data['userName'] = $this->getUserName();
                if ($this->getUserId() == 'admin')
                        //$data['stores'] = $this->Site->loadAllStore();
                        $stores = $this->Site->loadAllStore();
                else 
                        //$data['stores'] = $this->getStores();
                        $stores = $this->getStores();
                
                $data['emails'] = $this->Contactmanager->loadAll();
                $data['fileName'] = $fileName;
                $data['isHome'] = $isHome;
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
                $recipient = trim($this->input->post("recipient"));
                # valid pattern => "jerry [jerry.hasudungan@dominomail.yogya.com]" or "jerry.hasudungan@dominomail.yogya.com"
                $recipients = explode(" ", $recipient);
                $cnt = count($recipients);
                if ($cnt == 1) {
                        $recipient = $recipients[0];
                }
                else if ($cnt > 1) {
                        $vowels = array("[", "]");
                        $recipient = str_replace($vowels, "", $recipients[1]);
                }
                
                $notes = $this->input->post("notes");
                #$notes = preg_replace('/<(\s*)img[^<>]*>/i', '', $notes);
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