<?php
class BWPS_banips extends BWPS {

	function createRules($ipArray) {
		global $theRules; 
		
		$goodAddress = true;
		$myIp = getenv("REMOTE_ADDR");
				
		for ($i = 0; $i < sizeof($ipArray) && $goodAddress == true; $i++) {
			$ipArray[$i] = trim($ipArray[$i]);
			if (strlen($ipArray[$i]) > 0 && (!$this->checkIps($ipArray[$i]) || $ipArray[$i] == $myIp)) {
				$goodAddress = false;
			}
		}
	
		if ($goodAddress == true) {
			
			$ipList = implode(" ",$ipArray);
	
			$theRules = "order allow,deny\n" . 
				"deny from " . $ipList . "\n" . 
				"allow from all\n";
			return true;
				
		} else {
			
			return false;
				
		}
	}
	
	function getList() {
		global $theRules; 
		
		if (strlen($theRules) < 1) {
			return implode("\n", extract_from_markers($htaccess, 'Better WP Security Ban IPs' ));
		} else {
			return $theRules;
		}
	}

	function checkIps($address) {
		if (preg_match( "/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/", $address)) {
			return true;
		} else {
			return false;
		}
	}
		
	function confirmRules() {
		global $theRules; 
		
		$htaccess = trailingslashit(ABSPATH).'.htaccess';
			
		$savedRules = implode("\n", extract_from_markers($htaccess, 'Better WP Security Ban IPs' ));
			
		if (strlen($theRules) != strlen($savedRules)) {
			return "#ffebeb";
		} else {
			return "#fff";
		}
			
	}
}