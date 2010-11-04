<?php
class BWPS_hidebe extends BWPS {

	private $opts;
	
	function __construct() {
		$opts = $this->getOptions();
	}

	function getRules() {
	
		$opts = $this->getOptions();
		
		$siteurl = explode('/',trailingslashit(get_option('siteurl')));
		
		unset($siteurl[0]); unset($siteurl[1]); unset($siteurl[2]);
		
		$dir = implode('/',$siteurl);
		
		$login_slug = $opts['hidebe_login_slug'];
		$logout_slug = $opts['hidebe_logout_slug'];
		$admin_slug = $opts['hidebe_admin_slug'];
		$register_slug = $opts['hidebe_register_slug'];
				
		$supsec_key = $this->secKey();
		
		$reDomain = $this->uDomain(get_option('siteurl'));
		
			
		$theRules = "<IfModule mod_rewrite.c>\n" . 
			"RewriteEngine On\n" . 
			"RewriteBase /\n" . 
			"RewriteRule ^" . $login_slug . " ".$dir."wp-login.php?" . $supsec_key . " [R,L]\n" .
			"RewriteCond %{HTTP_COOKIE} !^.*wordpress_logged_in_.*$\n" .
			"RewriteRule ^" . $admin_slug . " ".$dir."wp-login.php?" . $supsec_key . "&redirect_to=/wp-admin/ [R,L]\n" .
			"RewriteRule ^" . $admin_slug . " ".$dir."wp-admin/?" . $supsec_key . " [R,L]\n" .
			"RewriteRule ^" . $register_slug . " " . $dir . "wp-login.php?" . $supsec_key . "&action=register [R,L]\n" .
			"RewriteCond %{HTTP_REFERER} !^" . $reDomain . "/wp-admin \n" .
			"RewriteCond %{HTTP_REFERER} !^" . $reDomain . "/wp-login\.php \n" .
			"RewriteCond %{HTTP_REFERER} !^" . $reDomain . "/" . $login_slug . " \n" .
			"RewriteCond %{HTTP_REFERER} !^" . $reDomain . "/" . $admin_slug . " \n" .
			"RewriteCond %{HTTP_REFERER} !^" . $reDomain . "/" . $register_slug . " \n" .
			"RewriteCond %{QUERY_STRING} !^" . $supsec_key . " \n" .
			"RewriteRule ^wp-login\.php not_found [L]\n" .
			"</IfModule>\n";
		
		return $theRules;
		
	}
	
	function secKey() {	
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		srand((double)microtime()*1000000);
		$pass = '' ;		
		for ($i = 0; $i <= 20; $i++) {
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
		}
		return $pass;	
	}
		
	function confirmRules() {
		
		$htaccess = trailingslashit(ABSPATH).'.htaccess';
			
		$curRules = $this->getRules();
		$savedRules = implode("\n", extract_from_markers($htaccess, 'Better WP Security Hide Backend' ));
			
		if (strlen($curRules) != strlen($savedRules)) {
			return "#ffebeb";
		} else {
			return "#fff";
		}
			
	}
}