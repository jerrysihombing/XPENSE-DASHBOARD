<?php
include_once ("db.conf.php");

date_default_timezone_set("Asia/Jakarta");

function readMonth($v) {
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

$currTime = strtotime('-2 months');
$currYear = date('Y', $currTime);
$currMonth = date('n', $currTime);

$actYear = (isset($_GET["actYear"]) && !empty($_GET["actYear"]) ? $_GET["actYear"] : $currYear);
$actMonth = (isset($_GET["actMonth"]) && !empty($_GET["actMonth"]) ? $_GET["actMonth"] : $currMonth);
$storeCode = (isset($_GET["storeCode"]) && !empty($_GET["storeCode"]) ? $_GET["storeCode"] : "no store selected");

// make connection
$conn = pg_connect("host=$host port=5432 dbname=$dbname user=$user password=$pass");
if (!$conn) {
	die ("Error: Could not establish a connection!");
}

$storeInit = "";
$aParams = array($storeCode);
$sql = "select store_init from mst_site where store_code = $1 limit 1";
$res = pg_query_params($conn, $sql, $aParams);
if ($row = pg_fetch_array($res)) {
    $storeInit = $row["store_init"];
}

$dataLabel = "Period: " . readMonth($actMonth) . " " . $actYear;
$expense_name = array();
$expense_pc["name"] = !empty($storeInit) ? $storeInit : $storeCode;

$aParams = array($actYear, $actMonth, $storeCode);
$sql = "select pid, pmonth, pyear, store_code, xpense_name, xpense_pc from xpense where pyear = $1 and pmonth = $2 and store_code = $3 order by xpense_name";
$res = pg_query_params($conn, $sql, $aParams);
while ($row = pg_fetch_array($res)) {
    $expense_name[] = $row["xpense_name"];
    $expense_pc["data"][] = round($row["xpense_pc"], 2);
}

$result["data_label"] = $dataLabel;
$result["expense_name"] = $expense_name;
$result["expense_pc"] = $expense_pc;

# php document said no need this call 
pg_close($conn);
		
#echo json_encode($result, JSON_NUMERIC_CHECK); #  Available since PHP 5.3.3. 
echo json_encode($result);
?>