<?php
//add login slug to new user registration email 

if (get_option('BWPS_Login_Slug')) {
	if (!function_exists('wp_new_user_notification')) {
		function wp_new_user_notification($user_id, $plaintext_pass = '') {

			$user = new WP_User($user_id);
			
			$user_login = stripslashes($user->user_login);
			$user_email = stripslashes($user->user_email);
	
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	
			$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
			$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
			$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";
			
			@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

			if (empty($plaintext_pass)) {
				return;
			}
			
			$message  = sprintf(__('Username: %s'), $user_login) . "\r\n";
			$message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
			$message .= get_site_url() . '/' . get_option('BWPS_Login_Slug') . "\r\n";

			wp_mail($user_email, sprintf(__('[%s] Your username and password'), $blogname), $message);

		}
	} 
}
