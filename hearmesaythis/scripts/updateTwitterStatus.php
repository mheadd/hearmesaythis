<?php

/*
 * This script updates the status of the Hear Me Say This Twitter account.
 * Put this script somewhere outside the web root directory.
 */

/*
 * This script uses some of the class files and config settings found in the project directory.
 * Create symlinks in the directory where this script is located to the files in the project directory.
 * e.g., in the directory where this script resides, run something like: 
 * 
 * 		~$ ln -s path/to/confg.php config.php
 * 
 * Create a symbolic link for each of the files listed below so that they can be accessed by the script.
 */
require('config.php');
require('connect.php');
require('cligs_invoker.php');
require('twitter_invoker.php');

// Helper function that adds the appropriate prefix to twitter names or hash tags
function makeHashTags($name) {	
	if(!strstr($name, "hmst-")) {
		return "@".$name;
	} else {
		return "#".$name;
	}	
}

try {

/*
 * Set up the Twitter and Cligs objects that will be used to send messages
 */ 
$twitter = new twitterApiInvoker($twitterEndpoint);
$twitter->userid = $twitter_userid;
$twitter->password = $twitter_password;
$overrideExpect = true; //Override HTTP expect header as workaroud to Twitter API bug
$cligs = new cligsApiInvoker($cligsEndpoint);

/*
 * Set up database connection object and run query to retrieve messages
 */
$db = new dbConnect($host, $user, $password, false);
$db->selectDB($database);
$result = $db->runQuery($getMessagesToSend);

$numberOfMessage = $db->getNumRowsAffected();

	if($numberOfMessage == 0) {
		
		die("No queued messages to send.");
		
	} else {
		
		for($i=0; $i<$numberOfMessage; $i++) {
			
			// Set up message components
			$row = mysql_fetch_array($result);
			$messageid = $row[0];
			$sender = $row[1];
			$sendtolist = explode("|", $row[2]);
			
			// Shorten URL to message
			$cligs->url = $ListenURL.$messageid;
			$cligs->invoke();
			$shortUrl = $cligs->output;
			
			// Create base message
			$twitterMessage = "Listen to ".makeHashTags($sender)." send a message to Congress: ".$shortUrl.".";
			
			// Add Twitter names or hash tags
			for($j=0; $j<count($sendtolist); $j++) {
				
				$twitterMessage .= " ".makeHashTags($sendtolist[$j]);
				
			}
			
			$twitter->message = $twitterMessage;
			$twitter->invoke($overrideExpect);
			
			if($twitter->info["http_code"] == '200') {
				$db->runQuery($updateTwitterStatus."'$messageid'");
			} else {
				$twitter->logResults($apiLogFile, $twitter->info["http_code"]);
			}
			
		}
			
	}
	
}

catch (Exception $e) {	
	echo "An error occured. $e->getMessage();";	
}

?>
