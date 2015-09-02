<?php
/**
 * Class   :  Budgetmanager
 * Author  :  Jerry Sihombing
 * Created :  2015-08-31
 * Desc    :  Data handler for Budget Management
 */

class Budgetmanager extends CI_Model {
        
	public function __construct() {
	
	}
	
	public function loadAmount($mt, $yr, $sto, $acc) {
		$params = array($mt, $yr, $sto, $acc);
		$sql = "select adm_budget_load_amount(?, ?, ?, ?, 'data'); fetch all in data;";
		$query = $this->db->query($sql, $params);
                
        return $query->row();	
	}
	
	public function loadByStore($mt, $yr, $sto) {
		$params = array($mt, $yr, $sto);
		$sql = "select adm_budget_load_by_store(?, ?, ?, 'data'); fetch all in data;";
		$query = $this->db->query($sql);
		
		return $query->result();
	}
	
	public function loadAll() {	
		$sql = "select adm_budget_load_all('data'); fetch all in data;";
		$query = $this->db->query($sql);
		
		return $query->result();
	}
	
	public function load($id) {
		$params = array($id);
		$sql = "select adm_budget_load(?, 'data'); fetch all in data;";
		$query = $this->db->query($sql, $params);
                
        return $query->row();	
	}
	
	public function remove($id) {
		$params = array($id);
		$sql = "select adm_budget_remove(?)";

		return $this->db->query($sql, $params);
	}
	
	public function update($id, $mt, $yr, $sto, $acc, $amt, $act, $usr, $upd) {
		$params = array($id, $mt, $yr, $sto, $acc, $amt, $act, $usr, $upd);
		$sql = "select adm_budget_update(?, ?, ?, ?, ?, ?, ?, ?, ?)";		
				
		return $this->db->query($sql, $params);
	}
	
	public function addNew($mt, $yr, $sto, $acc, $amt, $usr, $upd) {
		$params = array($mt, $yr, $sto, $acc, $amt, $usr, $upd);
		$sql = "select adm_budget_add(?, ?, ?, ?, ?, ?, ?)";	
		
		return $this->db->query($sql, $params);			
	}
	
	public function isExist($mt, $yr, $sto, $acc) {
		$params = array($mt, $yr, $sto, $acc);
		$sql = "select adm_budget_count(?, ?, ?, ?) cnt";
		$query = $this->db->query($sql, $params);
		
		if ($row = $query->row()) {
				return ($row->cnt > 0 ? TRUE : FALSE);
		}
		return FALSE;
	}
	
}
