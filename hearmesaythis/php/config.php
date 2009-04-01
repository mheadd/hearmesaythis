<?php
/*
 * This file holds configuration items for the Hear Me Say This voice application and scripts.
 *  
 */

// Sunlight API Config items
$sulightEndpoint = "http://services.sunlightlabs.com/api/";
$method = "legislators.allForZip";
$format = ".json";
$key = "";

// Database Config items
$host = "";
$user = "";
$password = "";
$database = "";

// Twitter API credentials & Message Info.
$twitter_userid = "";
$twitter_password = "";
$twitterEndpoint = "http://twitter.com/statuses/update.xml";

// Log file location
$apiLogFile = "logfile.txt";

// Cligs API URL
$cligsEndpoint = "http://cli.gs/api/v1/cligs/create";

// Playback config items
$ListenURL = "http://www.hearmesaythis.org/listen.php?id=";

// Recording config items
$baseRecordingDirectory = "../recordings/wavs/";

// SQL Queries
$saveMessage = "INSERT INTO `twitter_queue` (messageid, datetime, sender, state, sendtolist, sendtobioid, sendtoname) VALUES ('$messageid', NOW(), '$sender', '$state', '$sendToList', '$sendToBioId', '$sendToName')";
$getMessagesToSend = "SELECT messageid, sender, sendtolist FROM twitter_queue WHERE status = 1";
$getMessageDetails = "SELECT sender, state, sendtobioid, sendtoname FROM twitter_queue WHERE messageid = '$messageid' AND status = 2";
$updateTwitterStatus = "UPDATE twitter_queue SET status= 2 WHERE messageid =";

// This query should be tailored to your specific user table structure.
// E.g., SELECT user_zip FROM user_table WHERE user_phone = $callerAni;
$getCallerZip = "";

// Helper method for validating user input before using it in a SQL query
function validInput($value, $length) {
	
	if(is_numeric($value) && strlen($value) == $length) {
		return true;		
	}
	return false;	
}

?>
