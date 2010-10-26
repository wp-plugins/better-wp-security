<?php

/*
 * Generate the .htaccess rewrite rules
 */
function CreateRewriteRules() {
	$logout_uri = str_replace(trailingslashit(get_option('siteurl')), '', wp_logout_url());
		
	$siteurl = explode('/',trailingslashit(get_option('siteurl')));
		
	unset($siteurl[0]); unset($siteurl[1]); unset($siteurl[2]);
		
	$dir = implode('/',$siteurl);
			
	if (get_option('BWPS_hideadmin_login_slug')) { //make sure a login slug is set or none of this is worth it
			
		if (get_option('BWPS_hideadmin_login_redirect') != "Custom") { //set custom login destination if necessary
			$login_url = get_option('BWPS_hideadmin_login_redirect');
		} else {
			$login_url = get_option('BWPS_hideadmin_login_custom');
		}
			
		$login_slug = get_option('BWPS_hideadmin_login_slug');
		$logout_slug = get_option('BWPS_hideadmin_logout_slug');
		$admin_slug = get_option('BWPS_hideadmin_admin_slug');
		$register_slug = get_option('BWPS_hideadmin_register_slug');
				
		$supsec_key = secKey();
			
		$insert = "<IfModule mod_rewrite.c>\n" . 
			"RewriteEngine On\n" . 
			"RewriteBase /\n" . 
			"RewriteRule ^" . $logout_slug . " ".$dir.$logout_uri."&" . $supsec_key . " [L]\n" . //Redirect Logout slug to logout with hideadmin_key
			"RewriteRule ^" . $login_slug . " ".$dir."wp-login.php?" . $supsec_key . "&redirect_to=" . $login_url . " [R,L]\n" . 	//Redirect Login slug to show wp-login.php with hideadmin_key
			"RewriteCond %{HTTP_COOKIE} !^.*wordpress_logged_in_.*$\n" . //Check if user is logged in
			"RewriteRule ^" . $admin_slug . " ".$dir."wp-login.php?" . $supsec_key . "&redirect_to=http://dev.chriswiegman.com/wp-admin/ [R,L]\n" . 	//Send to login form if not logged in
			"RewriteRule ^" . $admin_slug . " ".$dir."wp-admin/?" . $supsec_key . " [R,L]\n" . 	//Send to admin area if logged in
			"RewriteRule ^" . $register_slug . " " . $dir . "wp-login.php?hideadmin_reg_key=" . $register_key . "&action=register [R,L]\n" .
			"RewriteCond %{HTTP_REFERER} !^" . get_option('siteurl') . "/wp-admin \n" . //if did not come from WP Admin
			"RewriteCond %{HTTP_REFERER} !^" . get_option('siteurl') . "/wp-login\.php \n" . //if did not come from wp-login.php
			"RewriteCond %{HTTP_REFERER} !^" . get_option('siteurl') . "/" . $login_slug . " \n" . //if did not come from Login slug
			"RewriteCond %{HTTP_REFERER} !^" . get_option('siteurl') . "/" . $admin_slug . " \n" . //if did not come from Admin slug
			"RewriteCond %{HTTP_REFERER} !^" . get_option('siteurl') . "/" . $register_slug . " \n" . //if did not come from Register slug
			"RewriteCond %{QUERY_STRING} !^" . $supsec_key . " \n" . //if no hideadmin_key query
			"RewriteRule ^wp-login\.php not_found [L]\n" . //Send to home page
			"RewriteCond %{QUERY_STRING} ^loggedout=true \n" . // if logout confirm query is true
			"RewriteRule ^wp-login\.php " . get_option('siteurl') . " [L]\n" .  //Send to home page
			"</IfModule>\n";			
	}	
		
	return $insert;
		
}
	
	
	/*
 * Generate secret key to protect direct access to admin functions
 */
function secKey() {	
	$chars = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	srand((double)microtime()*1000000);
	$pass = '' ;		
	for ($i = 0; $i <= 25; $i++) {
		$num = rand() % 33;
		$tmp = substr($chars, $num, 1);
		$pass = $pass . $tmp;
	}
	return $pass;	
}