<?php
/**
 * Class   :  Usermanager
 * Author  :  Jerry Sihombing
 * Created :  2015-07-02
 * Desc    :  Data handler for User Management
 */

define("PRE_PASSWD", "d0d0l");
define("POST_PASSWD", "p3uy3um");

class Usermanager extends CI_Model {
        
        public function __construct() {
        
        }
        
        public function login($userId, $logTime) {
                $params = array($userId, $logTime);
                $sql = "select user_login(?, ?)";
                
                return $this->db->query($sql, $params);
        }
            
        public function modifyActive($id, $act, $usr, $upd) {
                $params = array($id, $act, $usr, $upd);
                $sql = "select user_set_active(?, ?, ?, ?)";
                
                return $this->db->query($sql, $params);
        }
            
        public function assignRole($id, $role, $usr, $upd) {
                $params = array($id, $role, $usr, $upd);
                $sql = "select user_set_role(?, ?, ?, ?)";
                
                return $this->db->query($sql, $params);
        }
            
        public function modifyPasswdByUserId($userId, $pass, $usr, $upd) {
                $pass = sha1(PRE_PASSWD . $pass . POST_PASSWD);
                $params = array($userId, $pass, $usr, $upd);
                $sql = "select user_set_passwd_by_user_id(?, ?, ?, ?)";
                
                return $this->db->query($sql, $params);
        }
            
        public function modifyPasswd($id, $pass, $usr, $upd) {
                $pass = sha1(PRE_PASSWD . $pass . POST_PASSWD);
                $params = array($id, $pass, $usr, $upd);
                $sql = "select user_set_passwd(?, ?, ?, ?)";
                
                return $this->db->query($sql, $params);
        }
            
        public function isValidUser($usr, $pass) {	
                $pass = sha1(PRE_PASSWD . $pass . POST_PASSWD);
                $params = array($usr, $pass);
                $sql = "select user_is_valid(?, ?) cnt";	
                $query = $this->db->query($sql, $params);
                
                if ($row = $query->row()) {
                        return ($row->cnt > 0 ? TRUE : FALSE);
                }
                return FALSE;
        }
            
        public function loadByRoleName($role) {	
                $params = array($role);
                $sql = "select user_load_by_role_name(?, 'data'); fetch all in data;";
                $query = $this->db->query($sql, $params);
                
                return $query->result();
        }
            
        public function loadByActive($act) {
                $params = array($act);
                $sql = "select user_load_by_active(?, 'data'); fetch all in data;";
                $query = $this->db->query($sql, $params);
                
                return $query->result();			
        }
        
        public function loadAllEmail() {	
                $sql = "select user_load_all_email('data'); fetch all in data;";
                $query = $this->db->query($sql);
                
                return $query->result();
        }
        
        public function loadAll() {	
                $sql = "select user_load_all('data'); fetch all in data;";
                $query = $this->db->query($sql);
                
                return $query->result();
        }
        
        public function loadStoresByUserIdArray($usr) {	
                $params = array($usr);
                $sql = "select user_load_stores_by_user_id(?, 'data'); fetch all in data;";
                $query = $this->db->query($sql, $params);
                
                return $query->result_array();
        }
        
        public function loadStoresByUserId($usr) {	
                $params = array($usr);
                $sql = "select user_load_stores_by_user_id(?, 'data'); fetch all in data;";
                $query = $this->db->query($sql, $params);
                
                return $query->result();
        }
        
        public function loadByUserId($usr) {	
                $params = array($usr);
                $sql = "select user_load_by_user_id(?, 'data'); fetch all in data;";
                $query = $this->db->query($sql, $params);
                
                return $query->row();
        }
        
        public function loadStoresArray($id) {
                $params = array($id);
                $sql = "select user_load_stores(?, 'data'); fetch all in data;";
                $query = $this->db->query($sql, $params);
                
                return $query->result_array();	
        }
        
        public function loadStores($id) {
                $params = array($id);
                $sql = "select user_load_stores(?, 'data'); fetch all in data;";
                $query = $this->db->query($sql, $params);
                
                return $query->result();	
        }
        
        public function load($id) {
                $params = array($id);
                $sql = "select user_load(?, 'data'); fetch all in data;";
                $query = $this->db->query($sql, $params);
                
                return $query->row();	
        }
            
        public function remove($id) {
                $params = array($id);
                $sql = "select user_remove(?)";
        
                return $this->db->query($sql, $params);
        }
            
        public function updateWithModifyPasswd($id, $userId, $userName, $email, $branchCode, $departement, $roleName, $act, $pass, $detail, $usr, $upd) {
                $pass = sha1(PRE_PASSWD . $this->_passwd . POST_PASSWD);
                $detail = $this->makePostgresqlArray($detail);
                $params = array($id, $userId, $userName, $email, $branchCode, $departement, $roleName, $act, $pass, $detail, $usr, $upd);
                $sql = "select user_update_with_modify_passwd(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        
                return $this->db->query($sql, $params);
        }
    
        public function update($id, $userId, $userName, $email, $branchCode, $departement, $roleName, $act, $detail, $usr, $upd) {
                $detail = $this->makePostgresqlArray($detail);
                $params = array($id, $userId, $userName, $email, $branchCode, $departement, $roleName, $act, $detail, $usr, $upd);		
                $sql = "select user_update(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";			
                        
                return $this->db->query($sql, $params);
        }
            
        public function addNew($userId, $userName, $pass, $email, $branchCode, $departement, $roleName, $act, $detail, $usr, $upd) {
                $pass = sha1(PRE_PASSWD . $pass . POST_PASSWD);
                $detail = $this->makePostgresqlArray($detail);
                $params = array($userId, $userName, $pass, $email, $branchCode, $departement, $roleName, $act, $detail, $usr, $upd);
                $sql = "select user_add(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        
                return $this->db->query($sql, $params);	
        }
        
        public function userStoresCount($id = null) {
                $params = array($id);
                $sql = "select user_stores_count(?) cnt";
                $query = $this->db->query($sql, $params);
                
                if ($row = $query->row()) {
                        return $row->cnt;
                }
                return 0;
        }
        
        public function isExist($usr = null) {
                $params = array($usr);
                $sql = "select user_count(?) cnt";
                $query = $this->db->query($sql, $params);
                
                if ($row = $query->row()) {
                        return ($row->cnt > 0 ? TRUE : FALSE);
                }
                return FALSE;
        }
        
        private function makePostgresqlArray($a = array()) {
                $beginRet = "{";
                $endRet = "}";
                
                $bodyRet = "";
                for ($i=0; $i<sizeof($a); $i++) {
                        $bodyRet .= $a[$i] . ",";
                }
                if ($bodyRet != "")
                        $bodyRet = substr($bodyRet, 0, strlen($bodyRet)-1);
                
                $ret = $beginRet . $bodyRet . $endRet;
                
                return $ret;
        }
}
