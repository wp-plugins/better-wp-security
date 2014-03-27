<?php
global $wpdb, $itsec_globals;
$config_file = ITSEC_Lib::get_config();
$htaccess = ITSEC_Lib::get_htaccess();
?>

<ul class="itsec-support">
<li>
	<h4><?php _e( 'User Information', 'LION' ); ?></h4>
	<ul>
		<li><?php _e( 'Public IP Address', 'LION' ); ?>: <strong><a target="_blank"
		                                                                        title="<?php _e( 'Get more information on this address', 'LION' ); ?>"
		                                                                        href="http://whois.domaintools.com/<?php echo ITSEC_Lib::get_ip(); ?>"><?php echo ITSEC_Lib::get_ip(); ?></a></strong>
		</li>
		<li><?php _e( 'User Agent', 'LION' ); ?>:
			<strong><?php echo filter_var( $_SERVER['HTTP_USER_AGENT'], FILTER_SANITIZE_STRING ); ?></strong></li>
	</ul>
</li>

<li>
	<h4><?php _e( 'File System Information', 'LION' ); ?></h4>
	<ul>
		<li><?php _e( 'Website Root Folder', 'LION' ); ?>: <strong><?php echo get_site_url(); ?></strong>
		</li>
		<li><?php _e( 'Document Root Path', 'LION' ); ?>:
			<strong><?php echo filter_var( $_SERVER['DOCUMENT_ROOT'], FILTER_SANITIZE_STRING ); ?></strong></li>
		<?php
		if ( @is_writable( $htaccess ) ) {

			$copen  = '<font color="red">';
			$cclose = '</font>';
			$htaw   = __( 'Yes', 'LION' );

		} else {

			$copen  = '';
			$cclose = '';
			$htaw   = __( 'No.', 'LION' );

		}
		?>
		<li><?php _e( '.htaccess File is Writable', 'LION' ); ?>:
			<strong><?php echo $copen . $htaw . $cclose; ?></strong></li>
		<?php
		if ( @is_writable( $config_file ) ) {

			$copen  = '<font color="red">';
			$cclose = '</font>';
			$wconf  = __( 'Yes', 'LION' );

		} else {

			$copen  = '';
			$cclose = '';
			$wconf  = __( 'No.', 'LION' );

		}
		?>
		<li><?php _e( 'wp-config.php File is Writable', 'LION' ); ?>:
			<strong><?php echo $copen . $wconf . $cclose; ?></strong></li>
	</ul>
</li>

<li>
	<h4><?php _e( 'Database Information', 'LION' ); ?></h4>
	<ul>
		<li><?php _e( 'MySQL Database Version', 'LION' ); ?>
			: <?php $sqlversion = $wpdb->get_var( "SELECT VERSION() AS version" ); ?>
			<strong><?php echo $sqlversion; ?></strong></li>
		<li><?php _e( 'MySQL Client Version', 'LION' ); ?>:
			<strong><?php echo mysql_get_client_info(); ?></strong></li>
		<li><?php _e( 'Database Host', 'LION' ); ?>: <strong><?php echo DB_HOST; ?></strong></li>
		<li><?php _e( 'Database Name', 'LION' ); ?>: <strong><?php echo DB_NAME; ?></strong></li>
		<li><?php _e( 'Database User', 'LION' ); ?>: <strong><?php echo DB_USER; ?></strong></li>
		<?php $mysqlinfo = $wpdb->get_results( "SHOW VARIABLES LIKE 'sql_mode'" );
		if ( is_array( $mysqlinfo ) )
			$sql_mode = $mysqlinfo[0]->Value;
		if ( empty( $sql_mode ) )
			$sql_mode = __( 'Not Set', 'LION' ); else $sql_mode = __( 'Off', 'LION' );
		?>
		<li><?php _e( 'SQL Mode', 'LION' ); ?>: <strong><?php echo $sql_mode; ?></strong></li>
	</ul>
</li>

<li>
	<h4><?php _e( 'Server Information', 'LION' ); ?></h4>
	<?php $server_addr = array_key_exists( 'SERVER_ADDR', $_SERVER ) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR']; ?>
	<ul>
		<li><?php _e( 'Server / Website IP Address', 'LION' ); ?>: <strong><a target="_blank"
		                                                                                  title="<?php _e( 'Get more information on this address', 'LION' ); ?>"
		                                                                                  href="http://whois.domaintools.com/<?php echo $server_addr; ?>"><?php echo $server_addr; ?></a></strong>
		</li>
		<li><?php _e( 'Server Type', 'LION' ); ?>:
			<strong><?php echo filter_var( filter_var( $_SERVER['SERVER_SOFTWARE'], FILTER_SANITIZE_STRING ), FILTER_SANITIZE_STRING ); ?></strong>
		</li>
		<li><?php _e( 'Operating System', 'LION' ); ?>: <strong><?php echo PHP_OS; ?></strong></li>
		<li><?php _e( 'Browser Compression Supported', 'LION' ); ?>:
			<strong><?php echo filter_var( $_SERVER['HTTP_ACCEPT_ENCODING'], FILTER_SANITIZE_STRING ); ?></strong></li>
	</ul>
</li>

<li>
	<h4><?php _e( 'PHP Information', 'LION' ); ?></h4>
	<ul>
		<li><?php _e( 'PHP Version', 'LION' ); ?>: <strong><?php echo PHP_VERSION; ?></strong></li>
		<li><?php _e( 'PHP Memory Usage', 'LION' ); ?>:
			<strong><?php echo round( memory_get_usage() / 1024 / 1024, 2 ) . __( ' MB', 'LION' ); ?></strong>
		</li>
		<?php
		if ( ini_get( 'memory_limit' ) ) {
			$memory_limit = filter_var( ini_get( 'memory_limit' ), FILTER_SANITIZE_STRING );
		} else {
			$memory_limit = __( 'N/A', 'LION' );
		}
		?>
		<li><?php _e( 'PHP Memory Limit', 'LION' ); ?>: <strong><?php echo $memory_limit; ?></strong></li>
		<?php
		if ( ini_get( 'upload_max_filesize' ) ) {
			$upload_max = filter_var( ini_get( 'upload_max_filesize' ), FILTER_SANITIZE_STRING );
		} else {
			$upload_max = __( 'N/A', 'LION' );
		}
		?>
		<li><?php _e( 'PHP Max Upload Size', 'LION' ); ?>: <strong><?php echo $upload_max; ?></strong></li>
		<?php
		if ( ini_get( 'post_max_size' ) ) {
			$post_max = filter_var( ini_get( 'post_max_size' ), FILTER_SANITIZE_STRING );
		} else {
			$post_max = __( 'N/A', 'LION' );
		}
		?>
		<li><?php _e( 'PHP Max Post Size', 'LION' ); ?>: <strong><?php echo $post_max; ?></strong></li>
		<?php
		if ( ini_get( 'safe_mode' ) ) {
			$safe_mode = __( 'On', 'LION' );
		} else {
			$safe_mode = __( 'Off', 'LION' );
		}
		?>
		<li><?php _e( 'PHP Safe Mode', 'LION' ); ?>: <strong><?php echo $safe_mode; ?></strong></li>
		<?php
		if ( ini_get( 'allow_url_fopen' ) ) {
			$allow_url_fopen = __( 'On', 'LION' );
		} else {
			$allow_url_fopen = __( 'Off', 'LION' );
		}
		?>
		<li><?php _e( 'PHP Allow URL fopen', 'LION' ); ?>: <strong><?php echo $allow_url_fopen; ?></strong>
		</li>
		<?php
		if ( ini_get( 'allow_url_include' ) ) {
			$allow_url_include = __( 'On', 'LION' );
		} else {
			$allow_url_include = __( 'Off', 'LION' );
		}
		?>
		<li><?php _e( 'PHP Allow URL Include' ); ?>: <strong><?php echo $allow_url_include; ?></strong></li>
		<?php
		if ( ini_get( 'display_errors' ) ) {
			$display_errors = __( 'On', 'LION' );
		} else {
			$display_errors = __( 'Off', 'LION' );
		}
		?>
		<li><?php _e( 'PHP Display Errors', 'LION' ); ?>: <strong><?php echo $display_errors; ?></strong>
		</li>
		<?php
		if ( ini_get( 'display_startup_errors' ) ) {
			$display_startup_errors = __( 'On', 'LION' );
		} else {
			$display_startup_errors = __( 'Off', 'LION' );
		}
		?>
		<li><?php _e( 'PHP Display Startup Errors', 'LION' ); ?>:
			<strong><?php echo $display_startup_errors; ?></strong></li>
		<?php
		if ( ini_get( 'expose_php' ) ) {
			$expose_php = __( 'On', 'LION' );
		} else {
			$expose_php = __( 'Off', 'LION' );
		}
		?>
		<li><?php _e( 'PHP Expose PHP', 'LION' ); ?>: <strong><?php echo $expose_php; ?></strong></li>
		<?php
		if ( ini_get( 'register_globals' ) ) {
			$register_globals = __( 'On', 'LION' );
		} else {
			$register_globals = __( 'Off', 'LION' );
		}
		?>
		<li><?php _e( 'PHP Register Globals', 'LION' ); ?>:
			<strong><?php echo $register_globals; ?></strong></li>
		<?php
		if ( ini_get( 'max_execution_time' ) ) {
			$max_execute = filter_var( ini_get( 'max_execution_time' ) );
		} else {
			$max_execute = __( 'N/A', 'LION' );
		}
		?>
		<li><?php _e( 'PHP Max Script Execution Time' ); ?>:
			<strong><?php echo $max_execute; ?> <?php _e( 'Seconds' ); ?></strong></li>
		<?php
		if ( ini_get( 'magic_quotes_gpc' ) ) {
			$magic_quotes_gpc = __( 'On', 'LION' );
		} else {
			$magic_quotes_gpc = __( 'Off', 'LION' );
		}
		?>
		<li><?php _e( 'PHP Magic Quotes GPC', 'LION' ); ?>:
			<strong><?php echo $magic_quotes_gpc; ?></strong></li>
		<?php
		if ( ini_get( 'open_basedir' ) ) {
			$open_basedir = __( 'On', 'LION' );
		} else {
			$open_basedir = __( 'Off', 'LION' );
		}
		?>
		<li><?php _e( 'PHP open_basedir', 'LION' ); ?>: <strong><?php echo $open_basedir; ?></strong></li>
		<?php
		if ( is_callable( 'xml_parser_create' ) ) {
			$xml = __( 'Yes', 'LION' );
		} else {
			$xml = __( 'No', 'LION' );
		}
		?>
		<li><?php _e( 'PHP XML Support', 'LION' ); ?>: <strong><?php echo $xml; ?></strong></li>
		<?php
		if ( is_callable( 'iptcparse' ) ) {
			$iptc = __( 'Yes', 'LION' );
		} else {
			$iptc = __( 'No', 'LION' );
		}
		?>
		<li><?php _e( 'PHP IPTC Support', 'LION' ); ?>: <strong><?php echo $iptc; ?></strong></li>
		<?php
		if ( is_callable( 'exif_read_data' ) ) {
			$exif = __( 'Yes', 'LION' ) . " ( V" . substr( phpversion( 'exif' ), 0, 4 ) . ")";
		} else {
			$exif = __( 'No', 'LION' );
		}
		?>
		<li><?php _e( 'PHP Exif Support', 'LION' ); ?>: <strong><?php echo $exif; ?></strong></li>
	</ul>
</li>

<li>
	<h4><?php _e( 'WordPress Configuration', 'LION' ); ?></h4>
	<ul>
		<?php
		if ( is_multisite() ) {
			$multSite = __( 'Multisite is enabled', 'LION' );
		} else {
			$multSite = __( 'Multisite is NOT enabled', 'LION' );
		}
		?>
		<li><?php _e( '	Multisite', 'LION' ); ?>: <strong><?php echo $multSite; ?></strong></li>
		<?php
		if ( get_option( 'permalink_structure' ) != '' ) {
			$copen               = '';
			$cclose              = '';
			$permalink_structure = __( 'Enabled', 'LION' );
		} else {
			$copen               = '<font color="red">';
			$cclose              = '</font>';
			$permalink_structure = __( 'WARNING! Permalinks are NOT Enabled. Permalinks MUST be enabled for this plugin to function correctly', 'LION' );
		}
		?>
		<li><?php _e( 'WP Permalink Structure', 'LION' ); ?>:
			<strong> <?php echo $copen . $permalink_structure . $cclose; ?></strong></li>
		<li><?php _e( 'Wp-config Location', 'LION' ); ?>: <strong><?php echo $config_file ?></strong></li>
	</ul>
</li>
<li>
	<h4><?php echo $itsec_globals['plugin_name'] . __( ' variables', 'LION' ); ?></h4>
	<ul>
		<li><?php _e( 'Build Version', 'LION' ); ?>:
			<strong><?php echo $itsec_globals['plugin_build']; ?></strong><br/>
			<em><?php _e( 'Note: this is NOT the same as the version number on the plugin page or WordPress.org page and is instead used for support.', 'LION' ); ?></em>
		</li>
	</ul>
</li>
</ul>