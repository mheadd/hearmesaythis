<?php
/*
 * This file uses the sunlightApiInvoker class to obtain a list of representatives for a given zip code.
 * The result is formated as JSON and rendered so that it may be accessed in a VoiceXML dialog.
 * Additional JavaScript logic that repackages the Sunlight API JSON data for use by other application components is also included.
 * 
 */

try {
		
	/*
	 * Include configuration values and sunlight API class
	 */
	require('config.php');
	require('classes/sunlight_invoker.php');
	
	/*
	 * Validate user input
	 */
	$zip = $_REQUEST['callerZip'];
	if(!validInput($zip, 5)) {
		throw new Exception("Invalid zip code submitted.");
	}
	
	/*
	 * Intantiate new instance of sunlightApiInvoker and make method call
	 */
	$repList = new sunlightApiInvoker($sulightEndpoint);
	$methodUrl = $method.$format."?apikey=".$key."&zip=".$zip;
	$repList->invoke($methodUrl);
	
	if(!$repList) {
		throw new Exception("Could not access Sunlight API.");
	}
	
	/*
	 * Write out reponse as JavaScript object that can be accessed in VoiceXML dialog
	 */
	header('Content-type: application/x-javascript');
	echo 'var sunlightResponse='.$repList->output.";";	
	
}

catch (Exception $e) {
	
	/*
	 * Return a 404 error. This will cause an error.badfetch event to be raised in the VoiceXML dialog
	 * This will cause the technical difficulties dialog to be played to the caller
	 */ 	
	header("HTTP/1.0 404 Not Found");
	die($e->getMessage());
	
}

?>
// Use this to iterate over legislator names in dialog and to build sunlightDataObject
var legislators = sunlightResponse.response.legislators;

// Create a simple data structure to hold information about who the message will be sent to
function createTwitterList(choice) {

var sunlightDataObject = new Object();
var sendToList = Array();
var sendToName = Array();
var sendToBioId = Array();

	sunlightDataObject.sender = callerTwitterID;
	sunlightDataObject.state = legislators[0].legislator.state;

	if(choice == 'all') {
		
		for(var i=0; i< legislators.length; i++) {
		
			if(legislators[i].legislator.twitter_id == '') {
				sendToList.push(makeHasTag(legislators[i].legislator.title, legislators[i].legislator.state, legislators[i].legislator.district));
			}
			else {
				sendToList.push(legislators[i].legislator.twitter_id);
			}
			sendToName.push(legislators[i].legislator.title + ' ' + legislators[i].legislator.firstname + ' ' + legislators[i].legislator.middlename + ' ' +  legislators[i].legislator.lastname);
			sendToBioId.push(legislators[i].legislator.bioguide_id);		
		}
	
	} else {
		
		if(legislators[choice].legislator.twitter_id == '') {
				sendToList.push(makeHasTag(legislators[choice].legislator.title, legislators[choice].legislator.state, legislators[choice].legislator.district));
			}
			else {
				sendToList.push(legislators[choice].legislator.twitter_id);					
			}
			sendToName.push(legislators[choice].legislator.title + ' ' + legislators[choice].legislator.firstname + ' ' + legislators[choice].legislator.middlename + ' ' +  legislators[choice].legislator.lastname);
			sendToBioId.push(legislators[choice].legislator.bioguide_id);
	}
		sunlightDataObject.sendToList = sendToList.join('|');
		sunlightDataObject.sendToName = sendToName.join('|');
		sunlightDataObject.sendToBioId = sendToBioId.join('|');
		return sunlightDataObject;
}

// Helper function to create a hash tag for legislators with no twitter_id
function makeHasTag(title, state, district) {

	var tag = 'hmst-' + state.toLowerCase() + '-';
	
	if(title.toLowerCase() == 'rep') {
		tag = tag + 'r' + district;
	} else {
		tag = tag + 's';
	}

	if(title.toLowerCase() == 'sen') {
		tag = tag + district.charAt(0).toLowerCase();
	}
	
	return tag;

}