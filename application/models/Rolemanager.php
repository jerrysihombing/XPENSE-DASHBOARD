<?php
/**
 * Class   :  Rolemanager
 * Author  :  Jerry Sihombing
 * Created :  2015-07-02
 * Desc    :  Data handler for Role Management
 */

class Rolemanager extends CI_Model {
        
	public function __construct() {
	
	}
	
	# test
	private function limit ($request) {
		$limit = "";

		if (isset($request["start"]) && $request["length"] != -1) {
			$limit = "OFFSET " . intval($request["start"]) . " LIMIT " . intval($request["length"]);
		}

		return $limit;
	}
	
	# test
	private function order ( $request, $columns ) {
		$order = '';

		if ( isset($request['order']) && count($request['order']) ) {
			$orderBy = array();
			
			$dtColumns = self::pluck( $columns, 'dt' );

			for ($i = 0; $i < count($request['order']); $i++) {
				
				// Convert the column index into the column data property
				$columnIdx = intval($request['order'][$i]['column']);
				$requestColumn = $request['columns'][$columnIdx];

				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
				$column = $columns[ $columnIdx ];

				if ($requestColumn['orderable'] == 'true') {
					$dir = $request['order'][$i]['dir'] === 'asc' ? 'ASC' : 'DESC';
					$orderBy[] = '' . $column['db'] . ' ' . $dir;
				}
			}

			$order = 'ORDER BY ' . implode(', ', $orderBy);
		}

		return $order;
	}
	
	# test
	public function makeDataTable($detail = true, $edit = true, $delete = true) {
		$sql = "select id, role_name, description from adm_role_hdr";
		
		$limit = self::limit( $request, $columns );
		
		// Main query to actually get the data
		$tableWithFilter = $table . " " . $where;
		$sql = "select * from ({$tableWithFilter}) tbl " . $order . " " . $limit;
		$data = self::sql_exec( $db, $bindings, $sql );
		
		// Data set length after filtering
		$sql = "select count({$primaryKey}) from ({$tableWithFilter}) tbl";
		$resFilterLength = self::sql_exec( $db, $bindings, $sql );
		$recordsFiltered = $resFilterLength[0][0];
		
		// Total data set length
		$sql = "select count({$primaryKey}) from ({$table}) tbl";
		$resTotalLength = self::sql_exec( $db, $bindings, $sql );
		$recordsTotal = $resTotalLength[0][0];
		
	}
	
	public function loadAll() {	
		$sql = "select role_load_all('data'); fetch all in data;";
		$query = $this->db->query($sql);
		
		return $query->result();
	}
	
	public function loadDetailByNameArray($name) {
		$params = array($name);
		$sql = "select role_load_dtl_by_name(?, 'data'); fetch all in data;";
		$query = $this->db->query($sql, $params);
                
        return $query->result_array();
	}
	
	public function loadDetailByName($name) {
		$params = array($name);
		$sql = "select role_load_dtl_by_name(?, 'data'); fetch all in data;";
		$query = $this->db->query($sql, $params);
                
        return $query->result();
	}
	
	public function loadDetailArray($id) {
		$params = array($id);
		$sql = "select role_load_dtl(?, 'data'); fetch all in data;";
		$query = $this->db->query($sql, $params);
                
        return $query->result_array();	
	}
	
	public function loadDetail($id) {
		$params = array($id);
		$sql = "select role_load_dtl(?, 'data'); fetch all in data;";
		$query = $this->db->query($sql, $params);
                
        return $query->result();	
	}
			
	public function load($id) {
		$params = array($id);
		$sql = "select role_load(?, 'data'); fetch all in data;";
		$query = $this->db->query($sql, $params);
                
        return $query->row();	
	}
	
	public function remove($id) {
		$params = array($id);
		$sql = "select role_remove(?)";

		return $this->db->query($sql, $params);
	}
	
	public function update($id, $roleName, $description, $detail, $usr, $upd) {
		$detail = $this->makePostgresqlArray($detail);
		$params = array($id, $roleName, $description, $detail, $usr, $upd);
		$sql = "select role_update(?, ?, ?, ?, ?, ?)";		
				
		return $this->db->query($sql, $params);
	}
	
	public function addNew($roleName, $description, $detail, $usr, $upd) {
		$detail = $this->makePostgresqlArray($detail);
		$params = array($roleName, $description, $detail, $usr, $upd);
		$sql = "SELECT role_add(?, ?, ?, ?, ?)";	
		
		return $this->db->query($sql, $params);			
	}
	
	public function isExist($role = null) {
		$params = array($role);
		$sql = "select role_count(?) cnt";
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
