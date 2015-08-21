<?php
/**
 * Class   :  Contactmanager
 * Author  :  Jerry Sihombing
 * Created :  2015-08-11
 * Desc    :  Data handler for Contact Management
 */

class Contactmanager extends CI_Model {
        
	public function __construct() {
	
	}
	
	public function loadAll() {	
		$sql = "select contact_load_all('data'); fetch all in data;";
		$query = $this->db->query($sql);
		
		return $query->result();
	}
	
	public function load($id) {
		$params = array($id);
		$sql = "select contact_load(?, 'data'); fetch all in data;";
		$query = $this->db->query($sql, $params);
                
        return $query->row();	
	}
	
	public function remove($id) {
		$params = array($id);
		$sql = "select contact_remove(?)";

		return $this->db->query($sql, $params);
	}
	
	public function update($id, $name, $email, $position, $usr, $upd) {
		$params = array($id, $name, $email, $position, $usr, $upd);
		$sql = "select contact_update(?, ?, ?, ?, ?, ?)";		
				
		return $this->db->query($sql, $params);
	}
	
	public function addNew($name, $email, $position, $usr, $upd) {
		$params = array($name, $email, $position, $usr, $upd);
		$sql = "select contact_add(?, ?, ?, ?, ?)";	
		
		return $this->db->query($sql, $params);			
	}
	
	public function isExist($name = null) {
		$params = array($name);
		$sql = "select contact_count(?) cnt";
		$query = $this->db->query($sql, $params);
		
		if ($row = $query->row()) {
				return ($row->cnt > 0 ? TRUE : FALSE);
		}
		return FALSE;
	}

}
