<?php

function createhtaccess() {

	$opts = $this->getOptions();
		
	$siteurl = explode('/',trailingslashit(get_option('siteurl')));
		
	unset($siteurl[0]); unset($siteurl[1]); unset($siteurl[2]);
		
	$dir = implode('/',$siteurl);
		
	//get the slugs
	$login_slug = $opts['hidebe_login_slug'];
	$logout_slug = $opts['hidebe_logout_slug'];
	$admin_slug = $opts['hidebe_admin_slug'];
	$register_slug = $opts['hidebe_register_slug'];
				
	//generate the key
	$supsec_key = $this->hidebe_secKey();
		
	//get the domain without subdomain
	$reDomain = $this->uDomain(get_option('siteurl'));
		
	//see if user registration is allowed
	if (get_option('users_can_register') == 1) {
		$regEn = "RewriteCond %{QUERY_STRING} !^action=register\n";
	} else {
		$regEn = "";
	}
	
	$theRules = '';
	
	//Disable directory browsing
	if ($_POST['BWPS_dirbrowse'] == 1 || $opts['htaccess_protectht'] == 1) { 
		$theRules .= "Options All -Indexes\n\n";	
	} 
	
	//protect .htaccess
	if ($_POST['BWPS_protectht'] == 1 || $opts['htaccess_protectht'] == 1) { 
		$theRules .= "<files .htaccess>\n" .
			"Order allow,deny\n" . 
			"Deny from all\n" .
			"</files>\n\n";
	}
	
	//protect readme.html	
	if ($_POST['BWPS_protectreadme'] == 1 || $opts['htaccess_protectreadme'] == 1) { 
		$theRules .= "<files readme.html>\n" .
			"Order allow,deny\n" . 
			"Deny from all\n" .
			"</files>\n\n";
	}
	
	//protect install.php	
	if ($_POST['BWPS_protectinstall'] == 1 || $opts['htaccess_protectinstall'] == 1) { 
		$theRules .= "<files install.php>\n" .
			"Order allow,deny\n" . 
			"Deny from all\n" .
			"</files>\n\n";
	}
	
	//protect wp-config.php			
	if ($_POST['BWPS_protectwpc'] == 1 || $opts['htaccess_protectwpc'] == 1) { 
		$theRules .= "<files wp-config.php>\n" .
			"Order allow,deny\n" . 
			"Deny from all\n" .
			"</files>\n\n";
	}
	
	//open rewrite rules	
	if ($opts['htaccess_request'] == 1 || $opts['htaccess_qstring'] == 1 || $opts['hidebe_enable'] == 1) { 
		$theRules .= "<IfModule mod_rewrite.c>\n" . 
			"RewriteEngine On\n" . 
			"RewriteBase /\n\n";
	}
	
	//ignore invalid http requests	
	if ($_POST['BWPS_request'] == 1 || $opts['htaccess_request'] == 1) { 
		$theRules .= "RewriteCond %{REQUEST_METHOD} ^(HEAD|TRACE|DELETE|TRACK) [NC]\n" . 
			"RewriteRule ^(.*)$ - [F,L]\n";
	}
	
	//protect against invalid query strings		
	if ($_POST['BWPS_qstring'] == 1 || $opts['htaccess_qstring'] == 1) { 
		$theRules .= "RewriteCond %{QUERY_STRING} \.\.\/ [NC,OR]\n" . 
			"RewriteCond %{QUERY_STRING} boot\.ini [NC,OR]\n" . 
			"RewriteCond %{QUERY_STRING} tag\= [NC,OR]\n" . 
			"RewriteCond %{QUERY_STRING} ftp\:  [NC,OR]\n" . 
			"RewriteCond %{QUERY_STRING} http\:  [NC,OR]\n" . 
			"RewriteCond %{QUERY_STRING} https\:  [NC,OR]\n" . 
			"RewriteCond %{QUERY_STRING} (\<|%3C).*script.*(\>|%3E) [NC,OR]\n" . 
			"RewriteCond %{QUERY_STRING} mosConfig_[a-zA-Z_]{1,21}(=|%3D) [NC,OR]\n" . 
			"RewriteCond %{QUERY_STRING} base64_encode.*\(.*\) [NC,OR]\n" . 
			"RewriteCond %{QUERY_STRING} ^.*(\[|\]|\(|\)|<|>|ê|\"|;|\?|\*|=$).* [NC,OR]\n" . 
			"RewriteCond %{QUERY_STRING} ^.*(&#x22;|&#x27;|&#x3C;|&#x3E;|&#x5C;|&#x7B;|&#x7C;).* [NC,OR]\n" . 
			"RewriteCond %{QUERY_STRING} ^.*(%24&x).* [NC,OR]\n" .  
			"RewriteCond %{QUERY_STRING} ^.*(%0|%A|%B|%C|%D|%E|%F|127\.0).* [NC,OR]\n" . 
			"RewriteCond %{QUERY_STRING} ^.*(globals|encode|localhost|loopback).* [NC,OR]\n" . 
			"RewriteCond %{QUERY_STRING} ^.*(request|select|insert|union|declare).* [NC]\n" . 
			"RewriteCond %{HTTP_COOKIE} !^.*wordpress_logged_in_.*$\n" .
			"RewriteRule ^(.*)$ - [F,L]\n\n";
	}
	
	//hide wordpress backend
	$theRules .= "RewriteRule ^" . $login_slug . " ".$dir."wp-login.php?" . $supsec_key . " [R,L]\n" .
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
		"RewriteCond %{QUERY_STRING} !^action=logout\n" . 
		$regEn . 
		"RewriteCond %{HTTP_COOKIE} !^.*wordpress_logged_in_.*$\n" .
		"RewriteRule ^wp-login\.php not_found [L]\n";
	
	//end rewrite rules
	if ($opts['htaccess_request'] == 1 || $opts['htaccess_qstring'] == 1 || $opts['hidebe_enable'] == 1) { 
		$theRules .= "</IfModule>\n";
	}
	
	unset($opts);

	$wprules = implode("\n", extract_from_markers($htaccess, 'WordPress' ));
				
	$this->remove_section($htaccess, 'WordPress');
	$this->remove_section($htaccess, 'Better WP Security');
				
	insert_with_markers($htaccess,'Better WP Security', explode( "\n", $theRules));
	insert_with_markers($htaccess,'WordPress', explode( "\n", $wprules));		

}