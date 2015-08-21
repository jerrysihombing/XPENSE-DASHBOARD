<?php

class DboardController extends MY_Controller {

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
                $this->load->model('Expense');
        }
        
        public function index($actYear = null, $actMonth = null, $storeCode = null, $clusterCode = null) {
                
                $isHome = false;
                if ($actYear == null && $actMonth == null && $storeCode == null && $clusterCode == null) {
                        $isHome = true;
                }
                
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
                if ($isHome) {
                        $data['greeting'] = $this->greeting();
                        $data['defaultPeriod'] = $this->defaultPeriod();
                        $this->load->view('dboard/welcome.html', $data);
                }
                else {
                        $this->load->view('dboard/index.html');
                }
                $this->load->view('templates/footer.html');
        }
        
        public function ytd($actYear = null, $actMonth = null, $storeCode = null) {
                $isHome = false;
                
                $currTime = strtotime('-2 months');
                $currYear = date('Y', $currTime);
                $currMonth = date('n', $currTime);
                
                if ($actYear == null) $actYear = $currYear;
                if ($actMonth == null) $actMonth = $currMonth;
                if ($storeCode == null) $storeCode = "";
                $clusterCode = "";
                $regionalCode = "";
                
                $data['allowGroup'] = $this->getRoleName() == "Administrator" ? true : false;
                $data['allowAdminModule'] = $this->isAllowable('System Administration');
                $data['userName'] = $this->getUserName();
                if ($this->getUserId() == 'admin')
                        $stores = $this->Site->loadAllStore();
                else 
                        $stores = $this->getStores();
                
                $data['isHome'] = $isHome;
                $data['ytdLbl'] = 'Monthly Mode';
                $data['ytd'] = 'yes';
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
                $this->load->view('dboard/ytd.html');
                $this->load->view('templates/footer.html');
        }
        
        public function getData($pYear, $pMonth, $pStore) {
                $store = $this->Site->loadStore($pStore);
                $storeInit = "";
                $storeName = "";
                if ($store) {
                        $storeInit = $store->store_init;
                        $storeName = $store->store_name; 
                }
                
                #$result = $this->Expense->loadArray($pYear, $pMonth, $pStore);
                $result = $this->Expense->loadArrayV2($pYear, $pMonth, $pStore);
                $result['expense_pc']['name'] = (!empty($storeName) ? $storeName : ($pStore == 0 ? 'no store selected' : $pStore));
                
                echo json_encode($result);
        }
        
        public function getDataYtdV2($pStore, $pYear) {
                $store = $this->Site->loadStore($pStore);
                $storeInit = "";
                $storeName = "";
                if ($store) {
                        $storeInit = $store->store_init;
                        $storeName = $store->store_name; 
                }
                
                $result = $this->Expense->loadYtdArrayV2($pStore, $pYear);
                $result['group_name'] = (!empty($storeName) ? $storeName : ($pStore == 0 ? 'no store selected' : $pStore));
                
                echo json_encode($result);
        }
        
        public function getDataYtd($pStore) {
                $store = $this->Site->loadStore($pStore);
                $storeInit = "";
                $storeName = "";
                if ($store) {
                        $storeInit = $store->store_init;
                        $storeName = $store->store_name; 
                }
                
                $result = $this->Expense->loadYtdArray($pStore);
                $result['group_name'] = (!empty($storeName) ? $storeName : ($pStore == 0 ? 'no store selected' : $pStore));
                
                echo json_encode($result);
        }
        
        public function getDataRegional($pYear, $pMonth, $pRegional) {
                $pRegional = urldecode($pRegional);
                $result = $this->Expense->loadRegionalArray($pYear, $pMonth, $pRegional);
                $result['group_name'] = $pRegional;
                
                echo json_encode($result);
        }
        
        public function getDataCluster($pYear, $pMonth, $pCluster) {
                $pCluster = urldecode($pCluster);
                $result = $this->Expense->loadClusterArray($pYear, $pMonth, $pCluster);
                $result['group_name'] = $pCluster;
                
                echo json_encode($result);
        }
        
        public function saveCharts() {
                $fileName = date('ymdhis') . '.jpg';
                $filePath = CHART_FORGE . '/' . $fileName;
                
                $binData = str_replace(' ', '+', $this->input->post('binData'));
                $binData = base64_decode($binData);
                $im = imagecreatefromstring($binData);
                 
                if ($im !== false) {
                    // Save image in the specified location
                    header('Content-Type: image/jpeg');
                    imagejpeg($im, $filePath);
                    imagedestroy($im);
                    echo $fileName;
                }
                else {
                    echo 'Error occured while trying to create image.';
                }
        }
        
        private function greeting() {
                $userName = strtoupper($this->getUserName());
                $chkTime = date("G");
        
                if ($chkTime > 3 && $chkTime <= 11) {
                    return 'Good morning ' . $userName;
                    #return 'Selamat pagi ' . $userName;
                }
                else if ($chkTime > 11 && $chkTime <= 18) {
                    return 'Good afternoon ' . $userName;
                    #return 'Selamat siang ' . $userName;
                }
                else if ($chkTime > 18 && $chkTime <= 24) {
                    return 'Good evening ' . $userName;
                    #return 'Selamat petang ' . $userName;
                }
                else if ($chkTime > 0 && $chkTime <= 3) {
                    return 'Good evening ' . $userName;
                    #return 'Selamat malam ' . $userName;
                }
                else {
                    return 'Good morning ' . $userName;
                    #return 'Selamat pagi ' . $userName;
                }
        }
        
        private function defaultPeriod() {
                $currTime = strtotime('-2 months');
                $currYear = date('Y', $currTime);
                $currMonth = date('F', $currTime);
                
                return $currYear . ' and ' . $currMonth;
        }
        
}