<?php
if (!class_exists('BWPS_banips')) {
	class BWPS_banips {
	
		public $theRules;
		public $ipCheck;
		
		function __construct() {
			global $opts, $theRules;
			
			if (strlen($opts['banips_iplist']) > 1) { 
				$this->createRules(explode("\n",  $opts['banips_iplist']));
			}
		}

		function createRules($ipArray) {
			global $theRules, $ipCheck;
	
			$goodAddress = true;
			$myIp = getenv("REMOTE_ADDR");
			
			
	
			for ($i = 0; $i < sizeof($ipArray) && $goodAddress == true; $i++) { //check all ips until we find a bad one 
				$ipArray[$i] = trim($ipArray[$i]);
				if (strlen($ipArray[$i]) > 0 && (!$this->checkIps($ipArray[$i]) || $ipArray[$i] == $myIp)) { //make sure input is valid IPV4 address and it is NOT your own IP
					$goodAddress = false;
				}
			}
	
			if ($goodAddress == true) { //generate and return code if all addresses are good
			
				$ipList = implode(" ",$ipArray);
	
				$theRules = "order allow,deny\n" . 
					"deny from " . $ipList . "\n" . 
					"allow from all\n";
				
				return $theRules;
				
			} else {
			
				return false;
				
			}
		}

		function checkIps($address) {
			if (preg_match( "/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/", $address)) {
				return true;
			} else {
				return false;
			}
		}
		
		function getList() {
			global $theRules;
			
			return $theRules;
		}
		
		function confirmRules() {
		
			$htaccess = trailingslashit(ABSPATH).'.htaccess';
			
			$curRules = $this->getList();
			$savedRules = implode("\n", extract_from_markers($htaccess, 'Better WP Security Ban IPs' ));
			
			if (strlen($curRules) != strlen($savedRules)) {
				return "#ffebeb";
			} else {
				return "#fff";
			}
			
		}
	}
}