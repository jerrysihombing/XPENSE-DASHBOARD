<?php
	
	/** Error reporting */
	error_reporting(E_ALL);
	ini_set("display_errors", TRUE);
	ini_set("display_startup_errors", TRUE);
    
	// set the default timezone to use. Available since PHP 5.1
	date_default_timezone_set("Asia/Jakarta");
	
	define("EOL", (PHP_SAPI == "cli") ? PHP_EOL : "<br>");
	
	include_once("db.conf.php");
	
    echo "start job at ", date("Y-m-d H:i:s"), EOL; 
    
    $msgToSend = "";
	
	# yyyymm
	$FORM  = date("Ym", strtotime("-1 months"));
	$FORM2 = date("Ym", strtotime("-2 months"));
	$fileName  = "EXPENSE_" . $FORM   . ".txt";
	$fileName2 = "EXPENSE_" . $FORM2  . ".txt";
	$fileToRead  = $filePath . "/" . $fileName;
	$fileToRead2 = $filePath . "/" . $fileName2;
	
	# -- ETL JOB
	echo "preparing etl job..", EOL;
	
	# make connection
	echo "make connection to database.. ";
	// make connection
	$conn = pg_connect("host=$host port=5432 dbname=$dbname user=$user password=$pass");
	if (!$conn) {
		$msgToSend .= "Failed to connect to database.<br>";
		echo "could not establish a connection!", EOL;
	}
	else {
		echo "connected.", EOL;	
		
		# second file
		echo "looking for file ", $fileName2, ".. ";
		if (!file_exists($fileToRead2)) {
			echo "not found!", EOL;
			$msgToSend .= "file " . $fileName2 . " not found!";
		}
		else {
			echo "found.", EOL;
			
			echo "start transaction.. ";
			# start transaction
			$res = pg_query($conn, "BEGIN");
			if (!$res) {
				$msgToSend .= "Failed to start transaction for second file.<br>";
				echo "could not start transaction!", EOL;
			}
			else {
				echo "started.", EOL;
				
				# get id of existing data
				$pid = -1;
				$aParams = array($FORM2);
				$sql = "select id from xpense_info where file_form = $1";
				$res = pg_query_params($conn, $sql, $aParams);
				if ($row = pg_fetch_row($res)) {
					$pid = $row[0];
				}	
				
				# delete if only exists
				if ($pid != -1) {
					$aParams = array($pid);
					
					# delete existing data
					echo "delete existing xpense_info data.. ";
					$sql = "delete from xpense_info where id = $1";
					$res = pg_query_params($conn, $sql, $aParams);
					if (!$res) {
						$msgToSend .= "Failed to delete existing xpense_info data for second file.<br>";
						echo "failed.", EOL;
					}
					else {
						echo "deleted.", EOL;	
					}
					
					echo "delete existing xpense data.. ";
					$sql = "delete from xpense where pid = $1";
					$res = pg_query_params($conn, $sql, $aParams);
					if (!$res) {
						$msgToSend .= "Failed to delete existing xpense data for second file.<br>";
						echo "failed.", EOL;
					}
					else {
						echo "deleted.", EOL;
					}
				}
				
				echo "insert xpense_info.. ";
				# insert xpense_info
				$aParams = array($FORM2, $fileName2);
				# insert info
				$sql = "insert into xpense_info (file_form, file_name, inserted_date) values ($1, $2, current_timestamp)";
				$res = pg_query_params($conn, $sql, $aParams);
				if (!$res) {
					echo "failed, data: file_form, file_name => ";
					echo $FORM2, ", ", $fileName2, EOL;
					$msgToSend .= "Failed to insert xpense_info for second file.<br>";
				}
				else {
					echo "inserted.", EOL;	
				}
				
				# get sequence
				$curSeq = -1;
				$sql = "select currval('xpense_seq')";
				$res = pg_query($conn, $sql);
				if ($row = pg_fetch_row($res)) {
					$curSeq = $row[0];
				}
				
				$lineNumber = 0;
				$totalInsertedRow = 0;
				
				echo "insert xpense.. ";
				
				# read file and insert to table
				$lines = file($fileToRead2);
				foreach ($lines as $line) {
					
					# prevent PHP Fatal error:  Can't use function return value in write context
					$line = trim($line);
					if (!empty($line)) {
						
						$lineNumber++;
						
						# check for "|" character
						$found = strpos($line, "|");
						
						if ($found) {
							
							$DATA = explode("|", $line);
					
							$pmonth = trim((isset($DATA[0]) ? $DATA[0] : -1));
							$pyear = trim((isset($DATA[1]) ? $DATA[1] : -1));
							$storeCode = trim((isset($DATA[2]) ? $DATA[2] : ""));
							$xpenseName = trim((isset($DATA[3]) ? $DATA[3] : ""));
							$xpensePc = trim((isset($DATA[4]) ? $DATA[4] : 0));
							$amount = trim((isset($DATA[5]) ? $DATA[5] : 0));
							$xpenseAccount = trim((isset($DATA[6]) ? $DATA[6] : 0));
							
							$aParams = array ($curSeq, $pmonth, $pyear, $storeCode, $xpenseName, $xpensePc, $amount, $xpenseAccount);
							$sql = "insert into xpense (pid, pmonth, pyear, store_code, xpense_name, xpense_pc, amount, xpense_account) values ($1, $2, $3, $4, $5, $6, $7, $8)";
							$res = pg_query_params($conn, $sql, $aParams);
							if (!$res) {
								echo "failed at line ", $lineNumber, ", data: pid, pmonth, pyear, store_code, xpense_name, xpense_pc, amount, xpense_account => ";
								echo $curSeq, ", ", $pmonth, ", ", $pyear, ", ", $storeCode, ", ", $xpenseName, ", ", $xpensePc, ", ", $amount, ", ", $xpenseAccount, EOL;
								$msgToSend .= "Failed to insert data from file " . $fileToRead . " at line " . $lineNumber . ".<br>";
								continue;
							}
							else {
								$totalInsertedRow++;
							}
						}
					}
				
				}
				
				echo $totalInsertedRow . " rows inserted.", EOL;
				
				echo "commit transaction.. ", EOL;
				# commit transaction
				$res = pg_query($conn, "COMMIT");
				if (!$res) {
					$msgToSend .= "Failed to commit transaction for second file.<br>";
					echo "could not commit transaction!", EOL;
				}
				else {
					echo "commited.", EOL;
				}
				
			}
			
		}
		# end
		
		# first file
		echo "looking for file ", $fileName, ".. ";
		if (!file_exists($fileToRead)) {
			echo "not found!", EOL;
			$msgToSend .= "file " . $fileName . " not found!";
		}
		else {
			echo "found.", EOL;
			
			echo "start transaction.. ";
			# start transaction
			$res = pg_query($conn, "BEGIN");
			if (!$res) {
				$msgToSend .= "Failed to start transaction for first file.<br>";
				echo "could not start transaction!", EOL;
			}
			else {
				echo "started.", EOL;
				
				# get id of existing data
				$pid = -1;
				$aParams = array($FORM);
				$sql = "select id from xpense_info where file_form = $1";
				$res = pg_query_params($conn, $sql, $aParams);
				if ($row = pg_fetch_row($res)) {
					$pid = $row[0];
				}	
				
				# delete if only exists
				if ($pid != -1) {
					$aParams = array($pid);
					
					# delete existing data
					echo "delete existing xpense_info data.. ";
					$sql = "delete from xpense_info where id = $1";
					$res = pg_query_params($conn, $sql, $aParams);
					if (!$res) {
						$msgToSend .= "Failed to delete existing xpense_info data for first file.<br>";
						echo "failed.", EOL;
					}
					else {
						echo "deleted.", EOL;	
					}
					
					echo "delete existing xpense data.. ";
					$sql = "delete from xpense where pid = $1";
					$res = pg_query_params($conn, $sql, $aParams);
					if (!$res) {
						$msgToSend .= "Failed to delete existing xpense data for first file.<br>";
						echo "failed.", EOL;
					}
					else {
						echo "deleted.", EOL;
					}
				}
				
				echo "insert xpense_info.. ";
				# insert xpense_info
				$aParams = array($FORM, $fileName);
				# insert info
				$sql = "insert into xpense_info (file_form, file_name, inserted_date) values ($1, $2, current_timestamp)";
				$res = pg_query_params($conn, $sql, $aParams);
				if (!$res) {
					echo "failed, data: file_form, file_name => ";
					echo $FORM, ", ", $fileName, EOL;
					$msgToSend .= "Failed to insert xpense_info for first file.<br>";
				}
				else {
					echo "inserted.", EOL;	
				}
				
				# get sequence
				$curSeq = -1;
				$sql = "select currval('xpense_seq')";
				$res = pg_query($conn, $sql);
				if ($row = pg_fetch_row($res)) {
					$curSeq = $row[0];
				}
				
				$lineNumber = 0;
				$totalInsertedRow = 0;
				
				echo "insert xpense.. ";
				
				# read file and insert to table
				$lines = file($fileToRead);
				foreach ($lines as $line) {
					
					# prevent PHP Fatal error:  Can't use function return value in write context
					$line = trim($line);
					if (!empty($line)) {
						
						$lineNumber++;
						
						# check for "|" character
						$found = strpos($line, "|");
						
						if ($found) {
							
							$DATA = explode("|", $line);
					
							$pmonth = trim((isset($DATA[0]) ? $DATA[0] : -1));
							$pyear = trim((isset($DATA[1]) ? $DATA[1] : -1));
							$storeCode = trim((isset($DATA[2]) ? $DATA[2] : ""));
							$xpenseName = trim((isset($DATA[3]) ? $DATA[3] : ""));
							$xpensePc = trim((isset($DATA[4]) ? $DATA[4] : 0));
							
							$aParams = array ($curSeq, $pmonth, $pyear, $storeCode, $xpenseName, $xpensePc);
							$sql = "insert into xpense (pid, pmonth, pyear, store_code, xpense_name, xpense_pc) values ($1, $2, $3, $4, $5, $6)";
							$res = pg_query_params($conn, $sql, $aParams);
							if (!$res) {
								echo "failed at line ", $lineNumber, ", data: pid, pmonth, pyear, store_code, xpense_name, xpense_pc => ";
								echo $curSeq, ", ", $pmonth, ", ", $pyear, ", ", $storeCode, ", ", $xpenseName, ", ", $xpensePc, EOL;
								$msgToSend .= "Failed to insert data from file " . $fileToRead . " at line " . $lineNumber . ".<br>";
								continue;
							}
							else {
								$totalInsertedRow++;
							}
						}
					}
				
				}
				
				echo $totalInsertedRow . " rows inserted.", EOL;
				
				echo "commit transaction.. ", EOL;
				# commit transaction
				$res = pg_query($conn, "COMMIT");
				if (!$res) {
					$msgToSend .= "Failed to commit transaction for first file.<br>";
					echo "could not commit transaction!", EOL;
				}
				else {
					echo "commited.", EOL;
				}
				
			}
			
		}
		# end
		
		echo "close connection.", EOL;
		# php document said no need this call 
		pg_close($conn);
	
	}
	# make connection end
	
	echo "job done.. leave job.", EOL;    

	# -- END ETL
    
    # -- sending mail
    if ($msgToSend != "") {
        
        echo "some error found, sending error message.", EOL;
        
		require_once(dirname(__FILE__) . "/../vendors/PHPMailer/PHPMailerAutoload.php");
    
        $mail = new PHPMailer;
        
        //$mail->SMTPDebug = 3;                               // Enable verbose debug output
        
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = "mail.toserbayogya.com";                // Specify main and backup SMTP servers
        $mail->SMTPAuth = false;                              // Enable SMTP authentication
        $mail->Username = "";      // SMTP username
        $mail->Password = "";                                 // SMTP password
        #$mail->SMTPSecure = "tls";                            // Enable TLS encryption, `ssl` also accepted
        #$mail->Port = 587;                                    // TCP port to connect to
        
        $mail->From = "xpense@mailer.com";
        $mail->FromName = "Xpense";
        $mail->addAddress("jerry.hasudungan@dominomail.yogya.com", "Jerry");     // Add a recipient
        $mail->isHTML(true);                                                    // Set email format to HTML
        
        $mail->Subject = "Xpense Dashboard Job Error";
        $mail->Body    = $msgToSend;
        $mail->AltBody = $msgToSend;
        
        if (!$mail->send()) {
            echo "message could not be sent.", EOL;
            echo "mailer error: ", $mail->ErrorInfo, EOL;
        } else {
            echo "message has been sent.", EOL;
        }    
    }
    # -- mail end
    
    echo "finished all jobs at ", date("Y-m-d H:i:s"), EOL, EOL;
    
?>