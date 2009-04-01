<?php
/*
 * This class is used to invoke the Twitter API.
 */
if (!class_exists(apiBaseClass)) { require('base.php'); }

class twitterApiInvoker extends apiBaseClass {
	
	// Parameters that can be passed with API invokation
	public $userid;
	public $password;
	public $message;
	
	function __construct($endPoint) {
		parent::__construct($endPoint);
	}
	
	function invoke($overrideExpect = false) {
		
		$data = array('status' => $this->message);
		
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_URL, $this->baseURL);
		curl_setopt($this->ch, CURLOPT_USERPWD, "$this->userid:$this->password");
		curl_setopt($this->ch, CURLOPT_POST, true);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
		
		if($overrideExpect) {
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Expect:'));
		}
		
		$this->output = curl_exec($this->ch);
		$this->info = curl_getinfo($this->ch);
		
	}
	
	function logResults($logFile, $message) {
		parent::logResults($logFile, $message);
	}
	
	function __destruct() {
		parent::__destruct();		
	}
	
}

?>
