<?php
/**
 * Class   :  Site
 * Author  :  Jerry Sihombing
 * Created :  2015-07-02
 * Desc    :  Data handler for Site
 */

class Site extends CI_Model {
        
        public function __construct() {
        
        }
        
        public function loadRegionalMembers($regional) {
                $params = array($regional);
                $sql = "select load_regional_members(?, 'v_members'); fetch all in v_members;";
                $query = $this->db->query($sql, $params);
                
                return  $query->result();
        }
        
        public function loadAllRegionalArray() {
                $sql = "select load_all_regional('v_regional'); fetch all in v_regional;";        
                $query = $this->db->query($sql);
                
                return $query->result_array();
        }
        
        public function loadAllRegional() {
                $sql = "select load_all_regional('v_regional'); fetch all in v_regional;";        
                $query = $this->db->query($sql);
                
                return $query->result();
        }
        
        public function loadClusterMembers($cluster) {
                $params = array($cluster);
                $sql = "select load_cluster_members(?, 'v_members'); fetch all in v_members;";
                $query = $this->db->query($sql, $params);
                
                return  $query->result();
        }
        
        public function loadAllClusterArray() {
                $sql = "select load_all_cluster('v_cluster'); fetch all in v_cluster;";        
                $query = $this->db->query($sql);
                
                return $query->result_array();
        }
        
        public function loadAllCluster() {
                $sql = "select load_all_cluster('v_cluster'); fetch all in v_cluster;";        
                $query = $this->db->query($sql);
                
                return $query->result();
        }
        
        public function loadStore($store) {
                $params = array($store);
                $sql = "select load_store(?, 'v_store'); fetch all in v_store;";        
                $query = $this->db->query($sql, $params);
                
                return $query->row();
        }
        
        public function loadAllStoreArray() {
                $sql = "select load_all_store('v_store'); fetch all in v_store;";        
                $query = $this->db->query($sql);
                
                return $query->result_array();
        }
        
        public function loadAllStore() {
                $sql = "select load_all_store('v_store'); fetch all in v_store;";        
                $query = $this->db->query($sql);
                
                return $query->result();
        }
        
        public function load($id) {
                $params = array($id);
                $sql = "select load_site(?, 'v_site'); fetch all in v_site;";
                $query = $this->db->query($sql, $params);
                
                return  $query->row();
        }
        
        public function isExist($id = null) {
                $params = array($id);
                $sql = 'select site_count(?) cnt';
                $query = $this->db->query($sql, $params);
                
                if ($row = $query->row()) {
                        return ($row->cnt > 0 ? TRUE : FALSE);
                }
                return FALSE;
        }
}