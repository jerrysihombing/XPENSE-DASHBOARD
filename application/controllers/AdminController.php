<?php

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
                
                $this->load->model('Budgetmanager');
                $this->load->model('Expensemanager');
                $this->load->model('Contactmanager');
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
        
        public function isUserExist() {
                $input = $this->input->post();
                if ($this->Usermanager->isExist($input['userId']))
                        echo "User with specific id already exist.";
                else
                        echo "not exist";
        }
        
        private function createUser() {
                $input = $this->input->post();
                $userName = $this->getUserName();
                $currentTime = date('Y-m-d H:i:s');
                
                #if (!$this->Usermanager->isExist($input['userId'])) {
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
                #}
                #else {
                #        $this->session->set_userdata('errorMsg', 'User ' . $input['userId'] . ' already exist.');
                #        header('Location: ' . base_url() . 'admin/user/saveerror');
                #        exit;
                #}
        }
        
        private function updateUser($id) {
                $input = $this->input->post();
                $userName = $this->getUserName();
                $currentTime = date('Y-m-d H:i:s');
                
                $ret = $this->Usermanager->update($id, $input['userId'], $input['userName'], $input['email'], '', '', $input['roleName'], $input['active'], $input['chkStore'], $userName, $currentTime);
                if ($ret) {
                        $this->session->set_userdata('saved', 'saved');
                        $this->session->set_userdata('crudMsg', 'Successfully modify user.');
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
        
        public function isRoleExist() {
                $input = $this->input->post();
                if ($this->Rolemanager->isExist($input["roleName"]))
                        echo "Role with specific name already exist.";
                else
                        echo "not exist";
        }
        
        private function createRole() {
                $input = $this->input->post();
                $userName = $this->getUserName();
                $currentTime = date('Y-m-d H:i:s');
                
                #if (!$this->Rolemanager->isExist($input['roleName'])) {
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
                #}
                #else {
                #        $this->session->set_userdata('errorMsg', 'Role ' . $input['roleName'] . ' already exist.');
                #        header('Location: ' . base_url() . 'admin/role/saveerror');
                #        exit;
                #}
        }
        
        private function updateRole($id) {
                $input = $this->input->post();
                $userName = $this->getUserName();
                $currentTime = date('Y-m-d H:i:s');
                
                $ret = $this->Rolemanager->update($id, $input['roleName'], $input['description'], $input['chkMenu'], $userName, $currentTime);
                if ($ret) {
                        $this->session->set_userdata('saved', 'saved');
                        $this->session->set_userdata('crudMsg', 'Successfully modify role.');
                        header('Location: ' . base_url() . 'admin/role');
                        exit;
                }
                else {
                        header('Location: ' . base_url() . 'admin/role/saveerror');
                        exit;
                }
        }
        
        private function deleteRole($id) {
                $ret = $this->Rolemanager->remove($id);
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
        
        # -- contact section --
        public function contact($action = null, $id = null) {
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
                                
                                $this->load->view('admin/contact.list.html', $data);
                                $this->session->set_userdata('saved', '');
                                break;
                        
                        case "add":
                                $this->load->view('admin/contact.add.html', $data);
                                break;
                        
                        case "save":
                                $this->createContact();
                                break;
                        
                        case "saveerror":
                                $saved = $this->session->userdata('saved');
                                $errorMsg = $this->session->userdata('errorMsg');
                                $data['errorMsg'] = $errorMsg;
                                
                                $this->load->view('admin/saveerror.html', $data);
                                break;
                        
                        case "edit":
                                $contact = $this->Contactmanager->load($id);
                                
                                $data['id'] = $id;
                                $data['contact'] = $contact;
                                
                                $this->load->view('admin/contact.edit.html', $data);
                                break;
                        
                        case "update":
                                $this->updateContact($id);
                                break;
                        
                        case "delete":
                                $this->deleteContact($id);
                                break;
                }
                
                $this->load->view('templates/footer.html');
        }
        
        public function isContactExist() {
                $input = $this->input->post();
                if ($this->Contactmanager->isExist($input['name']))
                        echo "Contact with specific name already exist.";
                else
                        echo "not exist";
        }
        
        private function createContact() {
                $input = $this->input->post();
                $userName = $this->getUserName();
                $currentTime = date('Y-m-d H:i:s');
                
                #if (!$this->Contactmanager->isExist($input['name'])) {
                        $ret = $this->Contactmanager->addNew($input['name'], $input['email'], $input['position'], $userName, $currentTime);
                        if ($ret) {
                                $this->session->set_userdata('saved', 'saved');
                                $this->session->set_userdata('crudMsg', 'A new contact has been successfully added.');
                                header('Location: ' . base_url() . 'admin/contact');
                                exit;
                        }
                        else {
                                header('Location: ' . base_url() . 'admin/contact/saveerror');
                                exit;
                        }
                #}
                #else {
                #        $this->session->set_userdata('errorMsg', 'Name ' . $input['name'] . ' already exist.');
                #        header('Location: ' . base_url() . 'admin/contact/saveerror');
                #        exit;
                #}
        }
        
        private function updateContact($id) {
                $input = $this->input->post();
                $userName = $this->getUserName();
                $currentTime = date('Y-m-d H:i:s');
                
                $ret = $this->Contactmanager->update($id, $input['name'], $input['email'], $input['position'], $userName, $currentTime);
                if ($ret) {
                        $this->session->set_userdata('saved', 'saved');
                        $this->session->set_userdata('crudMsg', 'Successfully modify contact.');
                        header('Location: ' . base_url() . 'admin/contact');
                        exit;
                }
                else {
                        header('Location: ' . base_url() . 'admin/contact/saveerror');
                        exit;
                }
        }
        
        private function deleteContact($id) {
                $ret = $this->Contactmanager->remove($id);
                if ($ret) {
                        $this->session->set_userdata('saved', 'saved');
                        $this->session->set_userdata('crudMsg', 'Contact has been deleted successfully.');
                        header('Location: ' . base_url() . 'admin/contact');
                        exit;
                }
                else {
                        header('Location: ' . base_url() . 'admin/contact/saveerror');
                        exit;
                }
        }
        # -- end contact section --
        
        # -- expense display section --
        public function accounts($id) {
                $accounts = $this->Expensemanager->loadDetail($id);
                $html = "";
                foreach($accounts as $account) {
                        $html .= $account->xpense_account . " (" . $account->expense_name . ")<br>";
                }
                if ($html != "")
                        $html = substr($html, 0, strlen($html)-4);
                echo $html;
        }
        
        public function expense($action = null, $id = null) {
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
                                
                                $this->load->view('admin/expense.list.html', $data);
                                $this->session->set_userdata('saved', '');
                                break;
                        
                        case "add":
                                $accounts  = $this->Expensemanager->loadAllAccountArray();
                                
                                $accountCount = sizeof($accounts);
                                $fgCount = ceil($accountCount/2);
                                $data['accounts'] = $accounts;
                                $data['fgCount'] = $fgCount;
                                $data['accountCount'] = $accountCount;
                                
                                $this->load->view('admin/expense.add.html', $data);
                                break;
                        
                        case "save":
                                $this->createExpense();
                                break;
                        
                        case "saveerror":
                                $saved = $this->session->userdata('saved');
                                $errorMsg = $this->session->userdata('errorMsg');
                                $data['errorMsg'] = $errorMsg;
                                
                                $this->load->view('admin/saveerror.html', $data);
                                break;
                        
                        case "edit":
                                $accounts  = $this->Expensemanager->loadAllAccountArray();
                                $expense = $this->Expensemanager->load($id);
                                $expenseAccounts = $this->Expensemanager->loadDetailArray($id);
                                
                                $accountCount = sizeof($accounts);
                                $fgCount = ceil($accountCount/2);
                                $data['accounts'] = $accounts;
                                $data['fgCount'] = $fgCount;
                                $data['accountCount'] = $accountCount;
                                $data['id'] = $id;
                                $data['expense'] = $expense;
                                $data['expenseAccounts'] = $expenseAccounts;
                                
                                $this->load->view('admin/expense.edit.html', $data);
                                break;
                        
                        case "update":
                                $this->updateExpense($id);
                                break;
                        
                        case "delete":
                                $this->deleteExpense($id);
                                break;
                }
                
                $this->load->view('templates/footer.html');
        }
        
        public function isExpenseExist() {
                $input = $this->input->post();
                if ($this->Expensemanager->isExist($input['expenseName']))
                        echo "Expense with specific name already exist.";
                else
                        echo "not exist";
        }
        
        private function createExpense() {
                $input = $this->input->post();
                $userName = $this->getUserName();
                $currentTime = date('Y-m-d H:i:s');
                
                #if (!$this->Expensemanager->isExist($input['expenseName'])) {
                        $ret = $this->Expensemanager->addNew($input['expenseName'], $input['chkAccount'], $userName, $currentTime);
                        if ($ret) {
                                $this->session->set_userdata('saved', 'saved');
                                $this->session->set_userdata('crudMsg', 'A new expense has been successfully added.');
                                header('Location: ' . base_url() . 'admin/expense');
                                exit;
                        }
                        else {
                                header('Location: ' . base_url() . 'admin/expense/saveerror');
                                exit;
                        }
                #}
                #else {
                #       $this->session->set_userdata('errorMsg', 'Expense ' . $input['expenseName'] . ' already exist.');
                #        header('Location: ' . base_url() . 'admin/expense/saveerror');
                #        exit;
                #}
        }
        
        private function updateExpense($id) {
                $input = $this->input->post();
                $userName = $this->getUserName();
                $currentTime = date('Y-m-d H:i:s');
                
                $ret = $this->Expensemanager->update($id, $input['expenseName'], $input['isActive'], $input['chkAccount'], $userName, $currentTime);
                if ($ret) {
                        $this->session->set_userdata('saved', 'saved');
                        $this->session->set_userdata('crudMsg', 'Successfully modify expense.');
                        header('Location: ' . base_url() . 'admin/expense');
                        exit;
                }
                else {
                        header('Location: ' . base_url() . 'admin/expense/saveerror');
                        exit;
                }
        }
        
        private function deleteExpense($id) {
                $ret = $this->Expensemanager->remove($id);
                if ($ret) {
                        $this->session->set_userdata('saved', 'saved');
                        $this->session->set_userdata('crudMsg', 'Expense has been deleted successfully.');
                        header('Location: ' . base_url() . 'admin/expense');
                        exit;
                }
                else {
                        header('Location: ' . base_url() . 'admin/expense/saveerror');
                        exit;
                }
        }
        # -- end expense display section --
        
        # -- budget section --
        public function budget($action = null, $id = null) {
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
                                
                                # add this
                                $data['userId'] = $this->getUserId();
                                
                                $this->load->view('admin/budget.list.html', $data);
                                $this->session->set_userdata('saved', '');
                                break;
                        
                        case "add":
                                $data['currYear'] = date('Y');
                                $data['currMonth'] = date('n');
                                $data['activeAccounts'] = $this->Expensemanager->loadAllActive();
                                $data['userStores'] = $this->Usermanager->loadStoresByUserId($this->getUserId());
                                
                                $this->load->view('admin/budget.add.html', $data);
                                break;
                        
                        case "save":
                                $this->createBudget();
                                break;
                        
                        case "saveerror":
                                $saved = $this->session->userdata('saved');
                                $errorMsg = $this->session->userdata('errorMsg');
                                $data['errorMsg'] = $errorMsg;
                                
                                $this->load->view('admin/saveerror.html', $data);
                                break;
                        
                        case "edit":
                                $data['activeAccounts'] = $this->Expensemanager->loadAllActive();
                                $data['userStores'] = $this->Usermanager->loadStoresByUserId($this->getUserId());
                                $budget = $this->Budgetmanager->load($id);
                                $data['monthName'] = $this->readMonth($budget->pmonth);
                                
                                $data['id'] = $id;
                                $data['budget'] = $budget;
                                
                                $this->load->view('admin/budget.edit.html', $data);
                                break;
                        
                        case "update":
                                $this->updateBudget($id);
                                break;
                        
                        case "delete":
                                $this->deleteBudget($id);
                                break;
                }
                
                $this->load->view('templates/footer.html');
        }
        
        public function isBudgetExist() {
                $input = $this->input->post();
                if ($this->Budgetmanager->isExist($input['month'], $input['year'], $input['store'], $input['account']))
                        echo "Budget with specific store, year, month and account already exist.";
                else
                        echo "not exist";
        }
        
        private function createBudget() {
                $input = $this->input->post();
                $userName = $this->getUserName();
                $currentTime = date('Y-m-d H:i:s');
                
                #if (!$this->Budgetmanager->isExist($input['month'], $input['year'], $input['store'], $input['account'])) {
                        $budget = str_replace(",", "", $input['budget']);
                        $budget = is_numeric($budget) ? $budget : 0;
                        $ret = $this->Budgetmanager->addNew($input['month'], $input['year'], $input['store'], $input['account'], $budget, $userName, $currentTime);
                        if ($ret) {
                                $this->session->set_userdata('saved', 'saved');
                                $this->session->set_userdata('crudMsg', 'A new budget has been successfully added.');
                                header('Location: ' . base_url() . 'admin/budget');
                                exit;
                        }
                        else {
                                header('Location: ' . base_url() . 'admin/budget/saveerror');
                                exit;
                        }
                #}
                #else {
                #        $this->session->set_userdata('errorMsg', 'Account ' . $input['account'] . ' already exist for this store and period.');
                #        header('Location: ' . base_url() . 'admin/budget/saveerror');
                #        exit;
                #}
        }
        
        private function updateBudget($id) {
                $input = $this->input->post();
                $userName = $this->getUserName();
                $currentTime = date('Y-m-d H:i:s');
                
                $budget = str_replace(",", "", $input['budget']);
                $budget = is_numeric($budget) ? $budget : 0;
                        
                $ret = $this->Budgetmanager->update($id, $input['month'], $input['year'], $input['store'], $input['account'], $budget, $input['active'], $userName, $currentTime);
                if ($ret) {
                        $this->session->set_userdata('saved', 'saved');
                        $this->session->set_userdata('crudMsg', 'Successfully modify budget.');
                        header('Location: ' . base_url() . 'admin/budget');
                        exit;
                }
                else {
                        header('Location: ' . base_url() . 'admin/budget/saveerror');
                        exit;
                }
        }
        
        private function deleteBudget($id) {
                $ret = $this->Budgetmanager->remove($id);
                if ($ret) {
                        $this->session->set_userdata('saved', 'saved');
                        $this->session->set_userdata('crudMsg', 'Budget has been deleted successfully.');
                        header('Location: ' . base_url() . 'admin/budget');
                        exit;
                }
                else {
                        header('Location: ' . base_url() . 'admin/budget/saveerror');
                        exit;
                }
        }
        # -- end budget section --
        
        private function readMonth($v) {
                $ret = "";
                switch ($v) {
                    case 1:
                        $ret = "January";
                        break;
                    case 2:
                        $ret = "February";
                        break;
                    case 3:
                        $ret = "March";
                        break;
                    case 4:
                        $ret = "April";
                        break;
                    case 5:
                        $ret = "May";
                        break;
                    case 6:
                        $ret = "June";
                        break;
                    case 7:
                        $ret = "July";
                        break;
                    case 8:
                        $ret = "August";
                        break;
                    case 9:
                        $ret = "September";
                        break;
                    case 10:
                        $ret = "October";
                        break;
                    case 11:
                        $ret = "November";
                        break;
                    case 12:
                        $ret = "December";
                        break;
                }
                return $ret;
        }
}