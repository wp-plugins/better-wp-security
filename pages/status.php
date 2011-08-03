<?php
	global $BWPS, $wpdb;
	$opts = $BWPS->getOptions();
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
					<ul>
						<li>
							<h4><?php _e('User Information', 'better-wp-security'); ?></h4>
							<ul>
								<li><?php _e('Public IP Address', 'better-wp-security'); ?>: <strong><a target="_blank" title="<?php _e('Get more information on this address', 'better-wp-security'); ?>" href="http://whois.domaintools.com/<?php echo $_SERVER['REMOTE_ADDR']; ?>"><?php echo $_SERVER['REMOTE_ADDR']; ?></a></strong></li>
								<li><?php _e('User Agent', 'better-wp-security'); ?>: <strong><?php echo $_SERVER['HTTP_USER_AGENT']; ?></strong></li>
							</ul>
						</li>
						
						<li>
							<h4><?php _e('File System Information', 'better-wp-security'); ?></h4>
							<ul>
								<li><?php _e('Website Root Folder', 'better-wp-security'); ?>: <strong><?php echo get_site_url(); ?></strong></li>
								<li><?php _e('Document Root Path', 'better-wp-security'); ?>: <strong><?php echo $_SERVER['DOCUMENT_ROOT']; ?></strong></li>
								<?php 
									if ($BWPS->can_write(ABSPATH . WPINC . '/.htaccess')) { 
										$copen = '';
										$cclose = '';
										$htaw = __('Yes', 'better-wp-security'); 
									} else {
										$copen = '<font color="red">';
										$cclose = '</font>';
										$htaw = __('No. Better WP Security will be severely limited in it\'s ability to secure your site', 'better-wp-security'); 
									}
								?>
								<li><?php _e('.htaccess File is Writable', 'better-wp-security'); ?>: <strong><?php echo $copen . $htaw . $cclose; ?></strong></li>
								<?php 
									if ($BWPS->can_write($BWPS->getConfig())) { 
										$copen = '';
										$cclose = '';
										$wconf = __('Yes', 'better-wp-security'); 
									} else {
										$copen = '<font color="red">';
										$cclose = '</font>';
										$wconf = __('No. Better WP Security will be severely limited in it\'s ability to secure your site', 'better-wp-security'); 
									}
								?>
								<li><?php _e('wp-config.php File is Writable', 'better-wp-security'); ?>: <strong><?php echo $copen . $wconf . $cclose; ?></strong></li>
								<?php 
									if ($BWPS->can_write(ABSPATH . $BWPS->getDir() . '/')) { 
										$copen = '';
										$cclose = '';
										$wconf = __('Yes', 'better-wp-security'); 
									} else {
										$copen = '<font color="red">';
										$cclose = '</font>';
										$wconf = __('No. Better WP Security will be limited in it\'s ability to secure your site', 'better-wp-security'); 
									}
								?>
								<li><?php _e('Content directory can be renamed', 'better-wp-security'); ?>: <strong><?php echo $copen . $wconf . $cclose; ?></strong></li>
							</ul>
						</li>
					
						<li>
							<h4><?php _e('Database Information', 'better-wp-security'); ?></h4>
							<ul>
								<li><?php _e('MySQL Database Version', 'better-wp-security'); ?>: <?php $sqlversion = $wpdb->get_var("SELECT VERSION() AS version"); ?><strong><?php echo $sqlversion; ?></strong></li>
								<li><?php _e('MySQL Client Version', 'better-wp-security'); ?>: <strong><?php echo mysql_get_client_info(); ?></strong></li>
								<li><?php _e('Database Host', 'better-wp-security'); ?>: <strong><?php echo DB_HOST; ?></strong></li>
								<li><?php _e('Database Name', 'better-wp-security'); ?>: <strong><?php echo DB_NAME; ?></strong></li>
								<li><?php _e('Database User', 'better-wp-security'); ?>: <strong><?php echo DB_USER; ?></strong></li>
								<?php $mysqlinfo = $wpdb->get_results("SHOW VARIABLES LIKE 'sql_mode'");
									if (is_array($mysqlinfo)) $sql_mode = $mysqlinfo[0]->Value;
		    						if (empty($sql_mode)) $sql_mode = __('Not Set', 'better-wp-security');
									else $sql_mode = __('Off', 'better-wp-security');
								?>
								<li><?php _e('SQL Mode', 'better-wp-security'); ?>: <strong><?php echo $sql_mode; ?></strong></li>
							</ul>
						</li>
						
						<li>
							<h4><?php _e('Server Information', 'better-wp-security'); ?></h4>
							<ul>
								<li><?php _e('Server / Website IP Address', 'better-wp-security'); ?>: <strong><a target="_blank" title="<?php _e('Get more information on this address', 'better-wp-security'); ?>" href="http://whois.domaintools.com/<?php echo $_SERVER['SERVER_ADDR']; ?>"><?php echo $_SERVER['SERVER_ADDR']; ?></a></strong></li>
  								<li><?php _e('Server Type', 'better-wp-security'); ?>: <strong><?php echo $_SERVER['SERVER_SOFTWARE']; ?></strong></li>
  								<li><?php _e('Operating System', 'better-wp-security'); ?>: <strong><?php echo PHP_OS; ?></strong></li>
  								<li><?php _e('Browser Compression Supported', 'better-wp-security'); ?>: <strong><?php echo $_SERVER['HTTP_ACCEPT_ENCODING']; ?></strong></li>
							</ul>
						</li>
						
						<li>
							<h4><?php _e('PHP Information', 'better-wp-security'); ?></h4>
							<ul>
								<li><?php _e('PHP Version', 'better-wp-security'); ?>: <strong><?php echo PHP_VERSION; ?></strong></li>
								<li><?php _e('PHP Memory Usage', 'better-wp-security'); ?>: <strong><?php echo round(memory_get_usage() / 1024 / 1024, 2) . __(' MB', 'better-wp-security'); ?></strong> </li>
								<?php 
									if (ini_get('memory_limit')) {
										$memory_limit = ini_get('memory_limit'); 
									} else {
										$memory_limit = __('N/A', 'better-wp-security'); 
									}
								?>
								<li><?php _e('PHP Memory Limit', 'better-wp-security'); ?>: <strong><?php echo $memory_limit; ?></strong></li>
								<?php 
									if (ini_get('upload_max_filesize')) {
										$upload_max = ini_get('upload_max_filesize');
									} else 	{
										$upload_max = __('N/A', 'better-wp-security'); 
									}
								?>
    	    					<li><?php _e('PHP Max Upload Size', 'better-wp-security'); ?>: <strong><?php echo $upload_max; ?></strong></li>
    		    				<?php 
	        						if (ini_get('post_max_size')) {
        								$post_max = ini_get('post_max_size');
        							} else {
        								$post_max = __('N/A', 'better-wp-security'); 
	        						}
	    	    				?>
    		    				<li><?php _e('PHP Max Post Size', 'better-wp-security'); ?>: <strong><?php echo $post_max; ?></strong></li>
	    		    			<?php 
        							if (ini_get('safe_mode')) {
        								$safe_mode = __('On', 'better-wp-security');
        							} else {
        								$safe_mode = __('Off', 'better-wp-security'); 
    	    						}
			        			?>
    	    					<li><?php _e('PHP Safe Mode', 'better-wp-security'); ?>: <strong><?php echo $safe_mode; ?></strong></li>
    		    				<?php 
        							if (ini_get('allow_url_fopen')) {
        								$allow_url_fopen = __('On', 'better-wp-security');
    	    						} else {
										$allow_url_fopen = __('Off', 'better-wp-security'); 
									}
		        				?>
    	    					<li><?php _e('PHP Allow URL fopen', 'better-wp-security'); ?>: <strong><?php echo $allow_url_fopen; ?></strong></li>
    		    				<?php 
        							if (ini_get('allow_url_include')) {
    	    							$allow_url_include = __('On', 'better-wp-security');
									} else {
										$allow_url_include = __('Off', 'better-wp-security'); 
									}
								?>
    	    					<li><?php _e('PHP Allow URL Include'); ?>: <strong><?php echo $allow_url_include; ?></strong></li>
   			     				<?php 
        							if (ini_get('display_errors')) {
    	    							$display_errors = __('On', 'better-wp-security');
									} else {
										$display_errors = __('Off', 'better-wp-security'); 
									}
								?>
    	    					<li><?php _e('PHP Display Errors', 'better-wp-security'); ?>: <strong><?php echo $display_errors; ?></strong></li>
    		    				<?php 
    			    				if (ini_get('display_startup_errors')) {
	        							$display_startup_errors = __('On', 'better-wp-security');
        							} else {
        								$display_startup_errors = __('Off', 'better-wp-security'); 
        							}
		        				?>
    	    					<li><?php _e('PHP Display Startup Errors', 'better-wp-security'); ?>: <strong><?php echo $display_startup_errors; ?></strong></li>
    			    			<?php 
	    		    				if (ini_get('expose_php')) {
        								$expose_php = __('On', 'better-wp-security');
        							} else {
        								$expose_php = __('Off', 'better-wp-security'); 
        							}
		        				?>
    		    				<li><?php _e('PHP Expose PHP', 'better-wp-security'); ?>: <strong><?php echo $expose_php; ?></strong></li>
			        			<?php 
        							if (ini_get('register_globals')) {
        								$register_globals = __('On', 'better-wp-security');
									} else {
										$register_globals = __('Off', 'better-wp-security'); 
									}
								?>
    	    					<li><?php _e('PHP Register Globals', 'better-wp-security'); ?>: <strong><?php echo $register_globals; ?></strong></li>
    		    				<?php 
        							if (ini_get('max_execution_time')) {
        								$max_execute = ini_get('max_execution_time');
									} else {
										$max_execute = __('N/A', 'better-wp-security'); 
									}
								?>
    	    					<li><?php _e('PHP Max Script Execution Time'); ?>: <strong><?php echo $max_execute; ?> <?php _e('Seconds'); ?></strong></li>
    		    				<?php 
        							if (ini_get('magic_quotes_gpc')) {
    	    							$magic_quotes_gpc = __('On', 'better-wp-security');
									} else {
										$magic_quotes_gpc = __('Off', 'better-wp-security'); 
									}
								?>
    	    					<li><?php _e('PHP Magic Quotes GPC', 'better-wp-security'); ?>: <strong><?php echo $magic_quotes_gpc; ?></strong></li>
    		    				<?php 
    			    				if (ini_get('open_basedir')) {
	        							$open_basedir = __('On', 'better-wp-security');
									} else {
										$open_basedir = __('Off', 'better-wp-security'); 
									}
								?>
    	    					<li><?php _e('PHP open_basedir', 'better-wp-security'); ?>: <strong><?php echo $open_basedir; ?></strong></li>
    			    			<?php 
	    		    				if (is_callable('xml_parser_create')) {
        								$xml = __('Yes', 'better-wp-security');
									} else {
										$xml = __('No', 'better-wp-security'); 
									}
								?>
    		    				<li><?php _e('PHP XML Support', 'better-wp-security'); ?>: <strong><?php echo $xml; ?></strong></li>
			        			<?php 
        							if (is_callable('iptcparse')) {
        								$iptc = __('Yes', 'better-wp-security');
									} else {
										$iptc = __('No', 'better-wp-security'); 
									}
								?>
    	    					<li><?php _e('PHP IPTC Support', 'better-wp-security'); ?>: <strong><?php echo $iptc; ?></strong></li>
    		    				<?php 
        							if (is_callable('exif_read_data')) {
        								$exif = __('Yes', 'better-wp-security'). " ( V" . substr(phpversion('exif'),0,4) . ")" ;
        							} else {
    	    							$exif = __('No', 'better-wp-security'); 
	        						}
		        				?>
    	    					<li><?php _e('PHP Exif Support', 'better-wp-security'); ?>: <strong><?php echo $exif; ?></strong></li>
        					</ul>
        				</li>
        				
        				<li>
    	    				<h4><?php _e('Wordpress Configuration', 'better-wp-security'); ?></h4>
							<ul>
        						<?php
  									if ( is_multisite() ) { 
	  									$multSite = __('Multisite is enabled', 'better-wp-security');
									} else {
										$multSite = __('Multisite is NOT enabled', 'better-wp-security');
									}
  								?>
  								<li><?php _e('	Multisite', 'better-wp-security');?>:  <strong><?php echo $multSite; ?></strong></li>
    							<?php
    								if ( get_option('permalink_structure') != '' ) { 
    									$copen = '';
										$cclose = '';
										$permalink_structure = __('Enabled', 'better-wp-security'); 
									} else {
										$copen = '<font color="red">';
										$cclose = '</font>';
										$permalink_structure = __('WARNING! Permalinks are NOT Enabled. Permalinks MUST be enabled for Better WP Security to function correctly', 'better-wp-security'); 
									}
								?>
								<li><?php _e('WP Permalink Structure', 'better-wp-security'); ?>: <strong> <?php echo $copen . $permalink_structure . $cclose; ?></strong></li>
								<li><?php _e('Wp-config Location', 'better-wp-security');?>:  <strong><?php echo $BWPS->getConfig(); ?></strong></li>
							</ul>
						</li>
						<li>
    	    				<h4><?php _e('Better WP Security variables', 'better-wp-security'); ?></h4>
							<ul>
  								<li><?php _e('Hide Backend Key', 'better-wp-security');?>:  <strong><?php echo $opts['hidebe_key']; ?></strong></li>
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</div>
		
		<div class="clear"></div>
		
		<div class="postbox-container" style="width:70%">
				<div class="postbox opened">
					<h3><?php _e('Current .htaccess Contents', 'better-wp-security'); ?></h3>	
					<div class="inside">
						<style type="text/css">
							code {
								 overflow-x: auto; /* Use horizontal scroller if needed; for Firefox 2, not needed in Firefox 3 */
								 overflow-y: hidden;
								 background-color: transparent;
								 white-space: pre-wrap; /* css-3 */
								 white-space: -moz-pre-wrap !important; /* Mozilla, since 1999 */
								 white-space: -pre-wrap; /* Opera 4-6 */
								 white-space: -o-pre-wrap; /* Opera 7 */
								 /* width: 99%; */
								 word-wrap: break-word; /* Internet Explorer 5.5+ */
								 
							}
						</style>
						<?php 
							$htaccess = trailingslashit(ABSPATH).'.htaccess';
		
							$fh = fopen($htaccess, 'r');
		
							$contents = fread($fh, filesize($htaccess));
		
							fclose($fh);
							
							echo highlight_string($contents,true);
						?>
					</div>
				</div>
			</div>
		<div class="clear"></div>
		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3><?php _e('Support', 'better-wp-security'); ?></h3>
				<div class="inside">
					<p><?php _e('Please visit the', 'better-wp-security'); ?> <a href="http://bit51.com/software/better-wp-security/">Better WP Security</a> <?php _e('homepage for support and change-log', 'better-wp-security'); ?></p>
				</div>
			</div>
		</div>
		
	</div>
	
</div>