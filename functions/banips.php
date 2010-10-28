<?php
if (!class_exists('BWPS_banips')) {
	class BWPS_banips {
	
		public $ipList;
		public $ipCheck;

		function __construct($ipArray) {
			global $ipList,$ipCheck;
	
			$goodAddress = true;
			$myIp = getenv("REMOTE_ADDR");
	
			for ($i = 0; $i < sizeof($ipArray) && $goodAddress == true; $i++) { //check all ips until we find a bad one
				if (!$this->checkIps($ipArray[$i]) || $ipArray[$i] == $myIp) { //make sure input is valid IPV4 address and it is NOT your own IP
					$goodAddress = false;
				}
			}
	
			if ($goodAddress == true) { //generate and return code if all addresses are good
	
				$ipList = "order allow,deny\n" . 
					"deny from " . 
					implode(" ",$ipArray) . "\n" .
					"allow from all\n";
				
				$ipCheck = true;
				
			} else { //return false as there was a bad address entered
				
				$ipCheck = false;
				
			}
		}

		function checkIps($address) {
			if (preg_match( "/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/", $address)) {
				return true;
			} else {
				return false;
			}
		}
		
		function getOk() {
			global $ipCheck;
			
			return $ipCheck;
		}
		
		function getList() {
			global $ipList;
			
			return $ipList;
		}
	}
}