<?php
function CreateBanList($ipArray) {
	
	$goodAddress = true;
	
	for ($i = 0; $i < sizeof($ipArray) && $goodAddress == true; $i++) {
		if (!checkIps($ipArray[$i])) {
			echo $ipArray[$i];
			$goodAddress = false;
		}
	}
	
	if ($goodAddress == true) {
	
		$insert = "order allow,deny\n" . 
			"deny from " . 
			implode(" ",$ipArray) . "\n" .
			"allow from all\n";
	
		return $insert;
	} else {
		return false;
	}
}

function getBanList() {
	$ipList = implode("\n", extract_from_markers($htaccess, 'Better WP Security Ban IPs' ));
	
	return $ipList;
}

function checkIps($address) {
	if (preg_match( "/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/", $address)) {
		return true;
	} else {
		return false;
	}
}
	