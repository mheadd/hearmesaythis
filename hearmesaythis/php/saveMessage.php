<?php
/*
 * This file saves the recorded audio as a WAV file.
 * It also saves the message details to the twitterqueue table.
 * Note - because this file is accessed from the VoiceXML dialog using the <data/> element, a successful save should return a valid XML document.
 */

$message = $_FILES['recordMessage']['tmp_name'];
$twitterList = json_decode(stripslashes($_REQUEST['twitterList']));

try {

	/*
	 * Include configuration values and DB connection class
	 */
	require('classes/connect.php');
	require('config.php');
		
	/*
	 * Create a unique identifier for this message and use it to name the audio file
	 */
	$messageid = md5(uniqid(rand(), true));
	$fileName = $baseRecordingDirectory.$messageid.'.wav';
	
	if(move_uploaded_file($message, $fileName)) {
		
		require('config.php');
		$db = new dbConnect($host, $user, $password, false);
		$db->selectDB($database);
		
		/*
	 	* Validate user input
	 	*/
		$sender = $db->escapeInput($twitterList->sender);
		$state = $db->escapeInput($twitterList->state);
		$sendToList = $db->escapeInput($twitterList->sendToList);
		$sendToBioId = $db->escapeInput($twitterList->sendToBioId);
		$sendToName = $db->escapeInput($twitterList->sendToName);
		
		$result = $db->runQuery($saveMessage);	
		
		echo '<?xml version="1.0" encoding="utf-8"?>';
	}
	else {	
		throw new Exception("Could not save message");
	}
		
}

catch (Exception $e) {
	echo 'ERROR: '.$e->getMessage();	
}

?>
<status>
<value>ok</value>
</status>