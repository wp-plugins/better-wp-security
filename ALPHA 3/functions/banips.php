<?php

/*
 * Generate the .htaccess rules or return false if IP is invalid
 */
function createBanList($ipArray) {
	
	$goodAddress = true;
	$myIp = getenv("REMOTE_ADDR");
	
	for ($i = 0; $i < sizeof($ipArray) && $goodAddress == true; $i++) { //check all ips until we find a bad one
		if (!checkIps($ipArray[$i]) || $ipArray[$i] == $myIp) { //make sure input is valid IPV4 address and it is NOT your own IP
			$goodAddress = false;
		}
	}
	
	if ($goodAddress == true) { //generate and return code if all addresses are good
	
		$insert = "order allow,deny\n" . 
			"deny from " . 
			implode(" ",$ipArray) . "\n" .
			"allow from all\n";
	
		return $insert;
	} else { //return false as there was a bad address entered
		return false;
	}
}

/*
 * Verify valid IPV4 address with regex
 */
function checkIps($address) {
	if (preg_match( "/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/", $address)) {
		return true;
	} else {
		return false;
	}
}
	