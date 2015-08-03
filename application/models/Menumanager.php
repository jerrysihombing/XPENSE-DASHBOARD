<?php
/**
 * Class   :  Menumanager
 * Author  :  Jerry Sihombing
 * Created :  2015-07-02
 * Desc    :  Data handler for Menu Management
 */

class Menumanager extends CI_Model {
        
	public function __construct() {
	
	}
	
	public function loadAllArray() {	
		$sql = "select menu_load_all('data'); fetch all in data;";
		$query = $this->db->query($sql);
		
		return $query->result_array();
	}
	
	public function loadAll() {	
		$sql = "select menu_load_all('data'); fetch all in data;";
		$query = $this->db->query($sql);
		
		return $query->result();
	}
	
}
