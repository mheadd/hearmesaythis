<?php
/*
 * When a call is received by CCXML, we need to look them up based on their ANI.
 * If a caller is registered, return their zip code and Twitter ID to the application.
 * If a caller is not registered, play the not registered dialog.
 * 
 */

try {

	/*
	 * Validate user input
	 */
	$callerAni = $_REQUEST['callerAni'];
	if(!validInput($callerAni, 10)) {
		throw new Exception("Invalid phone number submitted.");
	}	
		
	/*
	 * Include configuration values and DB connection class
	 */
	require('config.php');
	require('classes/connect.php');
	
	$db = new dbConnect($host, $user, $password, false);
	$db->selectDB($database);
	$result = $db->runQuery($getCallerZip);
	
		if(($db->getNumRowsAffected()) == 0) {
			throw new Exception("No registered user with phone number: ".$callerAni);
		}
		else {
			$row = mysql_fetch_array($result);
			$callerZip = $row[0];
			$row = mysql_fetch_array($result);
			$callerTwitterID = $row[0];
			
			echo "lookupComplete\n";
			echo "callerZip=$callerZip\n";
			echo "callerTwitterID=$callerTwitterID\n"; 	
		}	
}

catch (Exception $e) {
	echo "lookupError\n";
	echo "message=".$e->getMessage();
}

?>