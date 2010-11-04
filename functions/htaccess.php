<?php
class BWPS_htaccess extends BWPS {
	
	function showContents() {
	
		$htaccess = trailingslashit(ABSPATH).'.htaccess';
		
		$fh = fopen($htaccess, 'r');
		
		$contents = fread($fh, filesize($htaccess));
		
		fclose($fh);
		
		echo "<pre>" . $contents . "</pre>";
	
	}
	
	function genRules() {
	
		$rules = "";
		
		if ($_POST['BWPS_dirbrowse'] == 1) { 
			$rules .= "Options All -Indexes\n\n";	
		} 
	
		if ($_POST['BWPS_protectht'] == 1) { 
			$rules .= "<files .htaccess>\n" .
				"Order allow,deny\n" . 
				"Deny from all\n" .
				"</files>\n\n";
		}
		
		if ($_POST['BWPS_protectreadme'] == 1) { 
			$rules .= "<files readme.html>\n" .
				"Order allow,deny\n" . 
				"Deny from all\n" .
				"</files>\n\n";
		}
		
		if ($_POST['BWPS_protectinstall'] == 1) { 
			$rules .= "<files install.php>\n" .
				"Order allow,deny\n" . 
				"Deny from all\n" .
				"</files>\n\n";
		}
				
		if ($_POST['BWPS_protectwpc'] == 1) { 
			$rules .= "<files wp-config.php>\n" .
				"Order allow,deny\n" . 
				"Deny from all\n" .
				"</files>\n\n";
		}
		
		if ($_POST['BWPS_request'] == 1 || $_POST['BWPS_qstring'] == 1 || $_POST['BWPS_hotlink'] == 1) { 
			$rules .= "<IfModule mod_rewrite.c>\n" . 
				"RewriteEngine On\n" . 
				"RewriteBase /\n\n";
		}
			
		if ($_POST['BWPS_request'] == 1) { 
			$rules .= "RewriteCond %{REQUEST_METHOD} ^(HEAD|TRACE|DELETE|TRACK) [NC]\n" . 
				"RewriteRule ^(.*)$ - [F,L]\n";
		}
			
		if ($_POST['BWPS_qstring'] == 1) { 
			$rules .= "RewriteCond %{QUERY_STRING} \.\.\/ [NC,OR]\n" . 
				"RewriteCond %{QUERY_STRING} boot\.ini [NC,OR]\n" . 
				"RewriteCond %{QUERY_STRING} tag\= [NC,OR]\n" . 
				"RewriteCond %{QUERY_STRING} ftp\:  [NC,OR]\n" . 
				"RewriteCond %{QUERY_STRING} http\:  [NC,OR]\n" . 
				"RewriteCond %{QUERY_STRING} https\:  [NC,OR]\n" . 
				"RewriteCond %{QUERY_STRING} (\<|%3C).*script.*(\>|%3E) [NC,OR]\n" . 
				"RewriteCond %{QUERY_STRING} mosConfig_[a-zA-Z_]{1,21}(=|%3D) [NC,OR]\n" . 
				"RewriteCond %{QUERY_STRING} base64_encode.*\(.*\) [NC,OR]\n" . 
				"RewriteCond %{QUERY_STRING} ^.*(\[|\]|\(|\)|<|>|Í|\"|;|\?|\*|=$).* [NC,OR]\n" . 
				"RewriteCond %{QUERY_STRING} ^.*(&#x22;|&#x27;|&#x3C;|&#x3E;|&#x5C;|&#x7B;|&#x7C;).* [NC,OR]\n" . 
				"RewriteCond %{QUERY_STRING} ^.*(%24&x).* [NC,OR]\n" .  
				"RewriteCond %{QUERY_STRING} ^.*(%0|%A|%B|%C|%D|%E|%F|127\.0).* [NC,OR]\n" . 
				"RewriteCond %{QUERY_STRING} ^.*(globals|encode|localhost|loopback).* [NC,OR]\n" . 
				"RewriteCond %{QUERY_STRING} ^.*(request|select|insert|union|declare|drop).* [NC]\n" . 
				"RewriteRule ^(.*)$ - [F,L]\n\n";
		}
		
		if ($_POST['BWPS_hotlink'] == 1) { 
				
			$reDomain = $this->uDomain(get_option('siteurl'));
				
			$rules .= "RewriteCond %{HTTP_REFERER} !^$\n" .
				"RewriteCond %{HTTP_REFERER} !^" . $reDomain . "/.*$ [NC]\n" .
				"RewriteRule .(jpg|jpeg|png|gif|pdf|doc)$ - [F]\n\n";
				
		}
		
		if ($_POST['BWPS_request'] == 1 || $_POST['BWPS_qstring'] == 1 || $_POST['BWPS_hotlink'] == 1) { 
			$rules .= "</IfModule>\n";
		}
		
		return $rules;
	}
}