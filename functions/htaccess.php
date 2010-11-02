<?php
class BWPS_htaccess extends BWPS {

	private $opts;
	
	function __construct() {
		$opts = $this->getOptions();
	}
	
	function showContents() {
	
		$htaccess = trailingslashit(ABSPATH).'.htaccess';
		
		$fh = fopen($htaccess, 'r');
		
		$contents = fread($fh, filesize($htaccess));
		
		fclose($fh);
		
		echo "<pre>" . $contents . "</pre>";
	
	}

}