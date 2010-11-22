<?php
class BWPS_d404 extends BWPS {

	function __construct() {
		global $wpdb;
		$opts = $this->getOptions();
		if ($opts['d404_enable'] == 1) {
			$computer_id = $wpdb->escape($_SERVER['REMOTE_ADDR']);
			if ($this->checkLock($computer_id)) {
				die("Why don't you take some downers and come back later?");
			}
			add_action('wp_head', array(&$this,'checkd404'));
		}
		unset($opts);
	}
	
	function checkd404() {
		global $wpdb;
		
		if (is_404()) {
			$computer_id = $wpdb->escape($_SERVER['REMOTE_ADDR']);
			$this->logd404($computer_id);
			if ($this->countAttempts($computer_id) >= 20 && !$this->checkLock($computer_id) && !is_user_logged_in()) {
				$this->lockout($computer_id);
			}
		}
	}
	
	function logd404($computer_id) {
		global $wpdb;
		
		$opts = $this->getOptions();
		
		$qstring = $wpdb->escape($_SERVER['REQUEST_URI']);
					
		$hackQuery = "INSERT INTO " . $opts['d404_table_attempts'] . " (computer_id, qstring, attempt_date)
			VALUES ('" . $computer_id . "', '" . $qstring . "', " . time() . ");";
		
		unset($opts);		
		return $wpdb->query($hackQuery);
	}
	
	function countAttempts($computer_id) {
		global $wpdb;
		
		$opts = $this->getOptions();
			
		$reTime = 300;
				
		$count = $wpdb->get_var("SELECT COUNT(attempt_ID) FROM " . $opts['d404_table_attempts'] . "
			WHERE attempt_date +
			" . $reTime . " >'" . time() . "' AND
			computer_id = '" . $computer_id . "';"
		);
		
		unset($opts);
		
		return $count;
			
	}
	
	function lockOut($computer_id) {
		global $wpdb;
		
		$opts = $this->getOptions();
				
		$lHost = "INSERT INTO " . $opts['d404_table_lockouts'] . " (computer_id, lockout_date)
			VALUES ('" . $computer_id . "', " . time() . ")";
					
		$wpdb->query($lHost);			
		
		unset($opts);
			
	}
	
	function checkLock($computer_id) {
		global $wpdb;
		
		$opts = $this->getOptions();
		
		$hostCheck = $wpdb->get_var("SELECT computer_id FROM " . $opts['d404_table_lockouts']  . 
			" WHERE lockout_date < " . (time() + 1800) . " AND computer_id = '" . $computer_id . "';");
		
		unset($opts);
		
		if ($hostCheck) {
			return true;
		} else {
			return false;
		}
	}
	
	function listLocked() {
		global $wpdb;
		
		$opts = $this->getOptions();
			

		$lockList = $wpdb->get_results("SELECT lockout_ID, lockout_date, computer_id FROM " . $opts['d404_table_lockouts']  . " WHERE lockout_date < " . (time() + 1800) . ";", ARRAY_A);
					
		unset($opts);
		return $lockList;
	}
}
