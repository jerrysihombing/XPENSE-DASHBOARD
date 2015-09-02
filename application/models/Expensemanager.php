<?php
/**
 * Class   :  Expensemanager
 * Author  :  Jerry Sihombing
 * Created :  2015-08-19
 * Desc    :  Data handler for Expense Management
 */

class Expensemanager extends CI_Model {
        
	public function __construct() {
	
	}
	
	public function loadAccount($id) {
		$params = array($id);
		$sql = "select expense_load(?, 'data'); fetch all in data;";
		$query = $this->db->query($sql, $params);
                
        return $query->row();	
	}
	
	public function loadAllAccountArray() {	
		$sql = "select expense_load_all('data'); fetch all in data;";
		$query = $this->db->query($sql);
		
		return $query->result_array();
	}
	
	public function loadAllActive($arrayMode = false) {	
		$sql = "select adm_expense_load_all_active('data'); fetch all in data;";
		$query = $this->db->query($sql);
		
		if ($arrayMode)
			return $query->result_array();
		else
			return $query->result();
	}
	
	public function loadAll() {	
		$sql = "select adm_expense_load_all('data'); fetch all in data;";
		$query = $this->db->query($sql);
		
		return $query->result();
	}
	
	public function loadDetailArray($id) {
		$params = array($id);
		$sql = "select adm_expense_load_dtl(?, 'data'); fetch all in data;";
		$query = $this->db->query($sql, $params);
                
        return $query->result_array();	
	}
	
	public function loadDetail($id) {
		$params = array($id);
		$sql = "select adm_expense_load_dtl(?, 'data'); fetch all in data;";
		$query = $this->db->query($sql, $params);
                
        return $query->result();	
	}
	
	public function load($id) {
		$params = array($id);
		$sql = "select adm_expense_load(?, 'data'); fetch all in data;";
		$query = $this->db->query($sql, $params);
                
        return $query->row();	
	}
	
	public function remove($id) {
		$params = array($id);
		$sql = "select adm_expense_remove(?)";

		return $this->db->query($sql, $params);
	}
	
	public function update($id, $name, $act, $detail, $usr, $upd) {
		$detail = $this->makePostgresqlArray($detail);
		$params = array($id, $name, $act, $detail, $usr, $upd);
		$sql = "select adm_expense_update(?, ?, ?, ?, ?, ?)";		
				
		return $this->db->query($sql, $params);
	}
	
	public function addNew($name, $detail, $usr, $upd) {
		$detail = $this->makePostgresqlArray($detail);
		$params = array($name, $detail, $usr, $upd);
		$sql = "select adm_expense_add(?, ?, ?, ?)";	
		
		return $this->db->query($sql, $params);			
	}
	
	public function isExist($name = null) {
		$params = array($name);
		$sql = "select adm_expense_count(?) cnt";
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
