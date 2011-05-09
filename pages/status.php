<?php
	global $BWPS, $wpdb;
?>

<div class="wrap" >

	<h2>Better WP Security - <?php _e('System Status', 'better-wp-security'); ?></h2>
	
	<div id="poststuff" class="ui-sortable">
	
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3><?php _e('Better WP Security System Status', 'better-wp-security'); ?></h3>
				<div class="inside">
					<?php $BWPS->status_getStatus(); ?>
				</div>
			</div>
		</div>
		
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
		<div class="clear"></div>
		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3><?php _e('System Information', 'better-wp-security'); ?></h3>
				<div class="inside">
					<p><?php _e('Website Root Folder'); ?>: <strong><?php echo get_site_url(); ?></strong></p>
					<p><?php _e('MySQL Database Version'); ?>: <?php $sqlversion = $wpdb->get_var("SELECT VERSION() AS version"); ?><strong><?php echo $sqlversion; ?></strong></p>
					<p><?php _e('Document Root Path'); ?>: <strong><?php echo $_SERVER['DOCUMENT_ROOT']; ?></strong></p>
					<p><?php _e('MySQL Client Version'); ?>: <strong><?php echo mysql_get_client_info(); ?></strong></p>
					<p><?php _e('Database Host'); ?>: <strong><?php echo DB_HOST; ?></strong></p>
					<p><?php _e('Server / Website IP Address'); ?>: <strong><?php echo $_SERVER['SERVER_ADDR']; ?></strong></p>
					<p><?php _e('Database Name'); ?>: <strong><?php echo DB_NAME; ?></strong></p>
  					<p><?php _e('Public IP / Your Computer IP Address'); ?>: <strong><?php echo $_SERVER['REMOTE_ADDR']; ?></strong></p>
    				<p><?php _e('Database User'); ?>: <strong><?php echo DB_USER; ?></strong></p>
  					<p><?php _e('Server Type'); ?>: <strong><?php echo $_SERVER['SERVER_SOFTWARE']; ?></strong></p>
   					<?php $mysqlinfo = $wpdb->get_results("SHOW VARIABLES LIKE 'sql_mode'");
						if (is_array($mysqlinfo)) $sql_mode = $mysqlinfo[0]->Value;
    					if (empty($sql_mode)) $sql_mode = __('Not Set');
						else $sql_mode = __('Off');
					?>
					<p><?php _e('SQL Mode'); ?>: <strong><?php echo $sql_mode; ?></strong></p>
  					<p><?php _e('Operating System'); ?>: <strong><?php echo PHP_OS; ?></strong></p>
  					<?php
  						if ( is_multisite() ) { 
							_e('<p>Multisite: <strong>Multisite is enabled</strong></p>');
						} else {
							_e('<p>Multisite: <strong>Multisite is not enabled</strong></p>');
						}
  					?>
  					<p><?php _e('Browser Compression Supported'); ?>: <strong><?php echo $_SERVER['HTTP_ACCEPT_ENCODING']; ?></strong></p>
    				<p><?php _e('WP Permalink Structure'); ?>: <strong><?php echo $permalink_structure; ?></strong></p>
    				<p><?php _e('PHP Version'); ?>: <strong><?php echo PHP_VERSION; ?></strong></p>
    				<?php
    					if ( get_option('permalink_structure') != '' ) { 
							_e('<p>Permalinks: <strong>Enabled</strong></p>'); 
						} else {
							_e('<p>Permalinks: <font color="red"><strong>WARNING! Permalinks are NOT Enabled. Permalinks MUST be enabled for Better WP Security to function correctly</strong></font></p>'); 
						}
					?>
					<p><?php _e('PHP Version'); ?>: <strong><?php echo PHP_VERSION; ?></strong></p>
					<p><?php _e('PHP Memory Usage'); ?>: <strong><?php echo round(memory_get_usage() / 1024 / 1024, 2) . __(' MB'); ?></strong> </p>
					<?php 
						if (ini_get('memory_limit')) {
							$memory_limit = ini_get('memory_limit'); 
						} else {
							$memory_limit = __('N/A'); 
						}
					?>
					<p><?php _e('PHP Memory Limit'); ?>: <strong><?php echo $memory_limit; ?></strong></p>
					<?php 
						if (ini_get('upload_max_filesize')) {
							$upload_max = ini_get('upload_max_filesize');
						} else {
							$upload_max = __('N/A'); 
						}
					?>
        			<p><?php _e('PHP Max Upload Size'); ?>: <strong><?php echo $upload_max; ?></strong></p>
        			<?php 
        				if (ini_get('post_max_size')) {
        					$post_max = ini_get('post_max_size');
        				} else {
        					$post_max = __('N/A'); 
        				}
        			?>
        			<p><?php _e('PHP Max Post Size'); ?>: <strong><?php echo $post_max; ?></strong></p>
        			<?php 
        				if (ini_get('safe_mode')) {
        					$safe_mode = __('On');
        				} else {
        					$safe_mode = __('Off'); 
        				}
        			?>
        			<p><?php _e('PHP Safe Mode'); ?>: <strong><?php echo $safe_mode; ?></strong></p>
        			<?php 
        				if (ini_get('allow_url_fopen')) {
        					$allow_url_fopen = __('On');
        				} else {
        					$allow_url_fopen = __('Off'); 
        				}
        			?>
        			<p><?php _e('PHP Allow URL fopen'); ?>: <strong><?php echo $allow_url_fopen; ?></strong></p>
        			<?php 
        				if (ini_get('allow_url_include')) {
        					$allow_url_include = __('On');
						} else {
							$allow_url_include = __('Off'); 
						}
					?>
        			<p><?php _e('PHP Allow URL Include'); ?>: <strong><?php echo $allow_url_include; ?></strong></p>
        			<?php 
        				if (ini_get('display_errors')) {
        					$display_errors = __('On');
						} else {
							$display_errors = __('Off'); 
						}
					?>
        			<p><?php _e('PHP Display Errors'); ?>: <strong><?php echo $display_errors; ?></strong></p>
        			<?php 
        				if (ini_get('display_startup_errors')) {
        					$display_startup_errors = __('On');
        				} else {
        					$display_startup_errors = __('Off'); 
        				}
        			?>
        			<p><?php _e('PHP Display Startup Errors'); ?>: <strong><?php echo $display_startup_errors; ?></strong></p>
        			<?php 
        				if (ini_get('expose_php')) {
        					$expose_php = __('On');
        				} else {
        					$expose_php = __('Off'); 
        				}
        			?>
        			<p><?php _e('PHP Expose PHP'); ?>: <strong><?php echo $expose_php; ?></strong></p>
        			<?php 
        				if (ini_get('register_globals')) {
        					$register_globals = __('On');
						} else {
							$register_globals = __('Off'); 
						}
					?>
        			<p><?php _e('PHP Register Globals'); ?>: <strong><?php echo $register_globals; ?></strong></p>
        			<?php 
        				if (ini_get('max_execution_time')) {
        					$max_execute = ini_get('max_execution_time');
						} else {
							$max_execute = __('N/A'); 
						}
					?>
        			<p><?php _e('PHP Max Script Execution Time'); ?>: <strong><?php echo $max_execute; ?> <?php _e('Seconds'); ?></strong></p>
        			<?php 
        				if (ini_get('magic_quotes_gpc')) {
        					$magic_quotes_gpc = __('On');
						} else {
							$magic_quotes_gpc = __('Off'); 
						}
					?>
        			<p><?php _e('PHP Magic Quotes GPC'); ?>: <strong><?php echo $magic_quotes_gpc; ?></strong></p>
        			<?php 
        				if (ini_get('open_basedir')) {
        					$open_basedir = __('On');
						} else {
							$open_basedir = __('Off'); 
						}
					?>
        			<p><?php _e('PHP open_basedir'); ?>: <strong><?php echo $open_basedir; ?></strong></p>
        			<?php 
        				if (is_callable('xml_parser_create')) {
        					$xml = __('Yes');
						} else {
							$xml = __('No'); 
						}
					?>
        			<p><?php _e('PHP XML Support'); ?>: <strong><?php echo $xml; ?></strong></p>
        			<?php 
        				if (is_callable('iptcparse')) {
        					$iptc = __('Yes');
						} else {
							$iptc = __('No'); 
						}
					?>
        			<p><?php _e('PHP IPTC Support'); ?>: <strong><?php echo $iptc; ?></strong></p>
        			<?php 
        				if (is_callable('exif_read_data')) {
        					$exif = __('Yes'). " ( V" . substr(phpversion('exif'),0,4) . ")" ;
        				} else {
        					$exif = __('No'); 
        				}
        			?>
        			<p><?php _e('PHP Exif Support'); ?>: <strong><?php echo $exif; ?></strong></p>
				</div>
			</div>
		</div>
		
		<div class="clear"></div>
		
		<div class="postbox-container" style="width:70%">
				<div class="postbox opened">
					<h3><?php _e('Current .htaccess', 'better-wp-security'); ?></h3>	
					<div class="inside">
						<p><?php _e('Here are the current contents of your .htaccess file.', 'better-wp-security'); ?></p>
						<?php $BWPS->htaccess_showContents(); ?>
					</div>
				</div>
			</div>
		
		<div class="clear"></div>
		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3><?php _e('Support', 'better-wp-security'); ?></h3>
				<div class="inside">
					<p><?php _e('Please visit the', 'better-wp-security'); ?> <a href="http://www.chriswiegman.com/projects/better-wp-security/">Better WP Security</a> <?php _e('homepage for support and change-log', 'better-wp-security'); ?></p>
				</div>
			</div>
		</div>
		
	</div>
	
</div>