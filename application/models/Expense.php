<?php

date_default_timezone_set("Asia/Jakarta");

/**
 * Class   :  Expense
 * Author  :  Jerry Sihombing
 * Created :  2015-07-10
 * Desc    :  Data handler for Expense
 */
class Expense extends CI_Model {
        
        public function __construct() {
        
        }
        
        public function loadClusterArray($pYear, $pMonth, $pCluster) {
                $currTime = strtotime('-2 months');
                $currYear = date('Y', $currTime);
                $currMonth = date('n', $currTime);
                
                $actYear = (!empty($pYear) ? $pYear : $currYear);
                $actMonth = (!empty($pMonth) ? $pMonth : $currMonth);
                $clusterCode = (!empty($pCluster) ? $pCluster : "no cluster selected");
                
                $dataLabel = "Period: " . $this->readMonth($actMonth) . " " . $actYear;
                
                $expenseHolder = array();
                $tmpExpenseNameHolder = array();
                $expenseNameHolder = array();
                $expenseNamePcHolder = array();
                
                # -- load cluster members
                $params = array($pCluster);
                $sql = "select load_cluster_members(?, 'v_members'); fetch all in v_members;";
                $queryCluster = $this->db->query($sql, $params);
                
                foreach($queryCluster->result() as $rowCluster) {
                        $storeCode = $rowCluster->store_code;
                        $storeInit = $rowCluster->store_init;
                        #$storeName = trim(str_ireplace('yomart', '', $rowCluster->store_name));
                        $storeName = trim($rowCluster->store_name);
                        #$storeName = trim($rowCluster->store_name_2);
                        
                        $params = array($actYear, $actMonth, $storeCode);
                        $sql = "select load_expense(?, ?, ?, 'v_expense'); fetch all in v_expense;";        
                        $query = $this->db->query($sql, $params);
                        
                        $expenseNamePc = array();
                        foreach($query->result() as $row) {
                                # put all names here
                                $tmpExpenseNameHolder[] = $row->xpense_name;
                                # normal code
                                #$expenseNamePc[$row->xpense_name] = round($row->xpense_pc, 2);
                                
                                # use additional info
                                $expenseNamePc[$row->xpense_name]["pc"] = round($row->xpense_pc, 2);
                                $expenseNamePc[$row->xpense_name]["amount"] = number_format($row->amount, 2, ".", ",");
                                $expenseNamePc[$row->xpense_name]["store_code"] = $storeCode;
                        }
                        $expenseNamePcHolder[$storeName] = $expenseNamePc;
                }
                
                $expenseNameHolder = array_values(array_unique($tmpExpenseNameHolder));
                
                # iterate through the names pc holder
                $i = 0;
                foreach($expenseNamePcHolder as $key => $value) {
                
                        $expenseName = array();
                        $expensePc = array();
                
                        # iterate through the names
                        $j = 0;
                        foreach($expenseNameHolder as $name) {
                                $expenseName[] = $name;
                                if (isset($value[$name])) {
                                        $expensePc["data"][$j]["y"] = $value[$name]["pc"];
                                        $expensePc["data"][$j]["amount"] = $value[$name]["amount"];
                                        $expensePc["data"][$j]["url"] = "dboard/" . $actYear . "/" . $actMonth . "/" . $value[$name]["store_code"];
                                }
                                else {
                                        $expensePc["data"][$j]["y"] = 0;
                                        $expensePc["data"][$j]["amount"] = "";
                                        $expensePc["data"][$j]["url"] = "";
                                }
                                $j++;
                        }
                        
                        $expensePc["name"][] = $key;
                        $expenseHolder[$i]["name"] = $expenseName;
                        $expenseHolder[$i]["pc"] = $expensePc;
                        
                        $i++;
                }
                
                $result["series_count"] = $i;
                $result["data_label"] = $dataLabel;
                $result["expense_holder"] = $expenseHolder;
                
                return $result;
        }
        
        public function loadRegionalArray($pYear, $pMonth, $pRegional) {
                $currTime = strtotime('-2 months');
                $currYear = date('Y', $currTime);
                $currMonth = date('n', $currTime);
                
                $actYear = (!empty($pYear) ? $pYear : $currYear);
                $actMonth = (!empty($pMonth) ? $pMonth : $currMonth);
                $regionalCode = (!empty($pRegional) ? $pRegional : "no regional selected");
                
                $dataLabel = "Period: " . $this->readMonth($actMonth) . " " . $actYear;
                
                $expenseHolder = array();
                $tmpExpenseNameHolder = array();
                $expenseNameHolder = array();
                $expenseNamePcHolder = array();
                
                # -- load regional members
                $params = array($pRegional);
                $sql = "select load_regional_members(?, 'v_members'); fetch all in v_members;";
                $queryRegional = $this->db->query($sql, $params);
                
                foreach($queryRegional->result() as $rowRegional) {
                        $storeCode = $rowRegional->store_code;
                        $storeInit = $rowRegional->store_init;
                        $storeName = trim($rowRegional->store_name);
                        
                        $params = array($actYear, $actMonth, $storeCode);
                        $sql = "select load_expense(?, ?, ?, 'v_expense'); fetch all in v_expense;";        
                        $query = $this->db->query($sql, $params);
                        
                        $expenseNamePc = array();
                        foreach($query->result() as $row) {
                                # put all names here
                                $tmpExpenseNameHolder[] = $row->xpense_name;
                                # normal code
                                #$expenseNamePc[$row->xpense_name] = round($row->xpense_pc, 2);
                                
                                # use additional info
                                $expenseNamePc[$row->xpense_name]["pc"] = round($row->xpense_pc, 2);
                                $expenseNamePc[$row->xpense_name]["amount"] = number_format($row->amount, 2, ".", ",");
                                $expenseNamePc[$row->xpense_name]["store_code"] = $storeCode;
                        }
                        $expenseNamePcHolder[$storeName] = $expenseNamePc;
                }
                
                $expenseNameHolder = array_values(array_unique($tmpExpenseNameHolder));
                
                # iterate through the names pc holder
                $i = 0;
                foreach($expenseNamePcHolder as $key => $value) {
                
                        $expenseName = array();
                        $expensePc = array();
                
                        # iterate through the names
                        $j = 0;
                        foreach($expenseNameHolder as $name) {
                                $expenseName[] = $name;
                                if (isset($value[$name])) {
                                        $expensePc["data"][$j]["y"] = $value[$name]["pc"];
                                        $expensePc["data"][$j]["amount"] = $value[$name]["amount"];
                                        $expensePc["data"][$j]["url"] = "dboard/" . $actYear . "/" . $actMonth . "/" . $value[$name]["store_code"];
                                }
                                else {
                                        $expensePc["data"][$j]["y"] = 0;
                                        $expensePc["data"][$j]["amount"] = "";
                                        $expensePc["data"][$j]["url"] = "";
                                }
                                $j++;
                        }
                        
                        $expensePc["name"][] = $key;
                        $expenseHolder[$i]["name"] = $expenseName;
                        $expenseHolder[$i]["pc"] = $expensePc;
                        
                        $i++;
                }
                
                $result["series_count"] = $i;
                $result["data_label"] = $dataLabel;
                $result["expense_holder"] = $expenseHolder;
                
                return $result;
        }
        
        public function loadYtdArray($pStore) {
                $currTime = strtotime('-2 months');
                $currYear = date('Y', $currTime);
                $currMonth = date('n', $currTime);
                
                $actYear = $currYear;
                $storeCode = (!empty($pStore) ? $pStore : "no store selected");
                
                $dataLabel = "Period: Jan - " . $this->readShortMonth($currMonth) . " " . $actYear;
                
                $expenseHolder = array();
                $tmpExpenseNameHolder = array();
                $expenseNameHolder = array();
                $expenseNamePcHolder = array();
                
                # -- load by months
                for ($actMonth = 1; $actMonth <= $currMonth; $actMonth++) {
                        $params = array($actYear, $actMonth, $storeCode);
                        $sql = "select load_expense(?, ?, ?, 'v_expense'); fetch all in v_expense;";        
                        $query = $this->db->query($sql, $params);
                        
                        $expenseNamePc = array();
                        foreach($query->result() as $row) {
                                # put all names here
                                $tmpExpenseNameHolder[] = $row->xpense_name;
                                # normal code
                                #$expenseNamePc[$row->xpense_name] = round($row->xpense_pc, 2);
                                
                                # use additional info
                                $expenseNamePc[$row->xpense_name]["pc"] = round($row->xpense_pc, 2);
                                $expenseNamePc[$row->xpense_name]["amount"] = number_format($row->amount, 2, ".", ",");
                        }
                        $expenseNamePcHolder[$this->readShortMonth($actMonth)] = $expenseNamePc;
                }
                
                $expenseNameHolder = array_values(array_unique($tmpExpenseNameHolder));
                
                # iterate through the names pc holder
                $i = 0;
                foreach($expenseNamePcHolder as $key => $value) {
                
                        $expenseName = array();
                        $expensePc = array();
                
                        # iterate through the names
                        $j = 0;
                        foreach($expenseNameHolder as $name) {
                                $expenseName[] = $name;
                                if (isset($value[$name])) {
                                        $expensePc["data"][$j]["y"] = $value[$name]["pc"];
                                        $expensePc["data"][$j]["amount"] = $value[$name]["amount"];
                                }
                                else {
                                        $expensePc["data"][$j]["y"] = 0;
                                        $expensePc["data"][$j]["amount"] = "";
                                }
                                $j++;
                        }
                        
                        $expensePc["name"][] = $key;
                        $expenseHolder[$i]["name"] = $expenseName;
                        $expenseHolder[$i]["pc"] = $expensePc;
                        
                        $i++;
                }
                
                $result["series_count"] = $i;
                $result["data_label"] = $dataLabel;
                $result["expense_holder"] = $expenseHolder;
                
                return $result;
        }
        
        public function loadArray($pYear, $pMonth, $pStore) {
                $currTime = strtotime('-2 months');
                $currYear = date('Y', $currTime);
                $currMonth = date('n', $currTime);
                
                $actYear = (!empty($pYear) ? $pYear : $currYear);
                $actMonth = (!empty($pMonth) ? $pMonth : $currMonth);
                $storeCode = (!empty($pStore) ? $pStore : "no store selected");
                
                $params = array($actYear, $actMonth, $storeCode);
                $sql = "select load_expense(?, ?, ?, 'v_expense'); fetch all in v_expense;";        
                $query = $this->db->query($sql, $params);
                
                $dataLabel = "Period: " . $this->readMonth($actMonth) . " " . $actYear;
                $expenseName = array();
                $expensePc = array();
                #$expensePc["name"] = (!empty($storeInit) ? $storeInit : $storeCode);
                
                $j = 0;
                foreach($query->result() as $row) {
                        $expenseName[] = $row->xpense_name;
                        # normal code
                        #$expensePc["data"][] = round($row->xpense_pc, 2);
                        
                        # use additional info
                        $expensePc["data"][$j]["y"] = round($row->xpense_pc, 2);
                        $expensePc["data"][$j]["amount"] = number_format($row->amount, 2, ".", ",");
                        
                        $j++;
                }
                
                $result["data_label"] = $dataLabel;
                $result["expense_name"] = $expenseName;
                $result["expense_pc"] = $expensePc;
                
                return $result;
        }
        
        private function readMonth($v) {
                $vReturn = "Unknown";
                switch($v) {
                    case 1:
                        $vReturn = "January";
                        break;
                    case 2:
                        $vReturn = "February";
                        break;
                    case 3:
                        $vReturn = "March";
                        break;
                    case 4:
                        $vReturn = "April";
                        break;
                    case 5:
                        $vReturn = "May";
                        break;
                    case 6:
                        $vReturn = "June";
                        break;
                    case 7:
                        $vReturn = "July";
                        break;
                    case 8:
                        $vReturn = "August";
                        break;
                    case 9:
                        $vReturn = "September";
                        break;
                    case 10:
                        $vReturn = "October";
                        break;
                    case 11:
                        $vReturn = "November";
                        break;
                    case 12:
                        $vReturn = "December";
                        break;
                }
                return $vReturn;
        }
        
        private function readShortMonth($v) {
                $vReturn = "Unknown";
                switch($v) {
                    case 1:
                        $vReturn = "Jan";
                        break;
                    case 2:
                        $vReturn = "Feb";
                        break;
                    case 3:
                        $vReturn = "Mar";
                        break;
                    case 4:
                        $vReturn = "Apr";
                        break;
                    case 5:
                        $vReturn = "May";
                        break;
                    case 6:
                        $vReturn = "Jun";
                        break;
                    case 7:
                        $vReturn = "Jul";
                        break;
                    case 8:
                        $vReturn = "Aug";
                        break;
                    case 9:
                        $vReturn = "Sep";
                        break;
                    case 10:
                        $vReturn = "Oct";
                        break;
                    case 11:
                        $vReturn = "Nov";
                        break;
                    case 12:
                        $vReturn = "Dec";
                        break;
                }
                return $vReturn;
        }
        
}