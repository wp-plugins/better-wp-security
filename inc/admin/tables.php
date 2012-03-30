<?php

//make syre we have the WordPress class
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

//table of all 404's
if ( ! class_exists( 'log_content_4_table' ) ) {

	class log_content_4_table extends WP_List_Table {
	
		/**
		 * Construct table object
		 *
		 **/
		function __construct() {
		
			parent::__construct(
				array(
					'singular'	=> 'log_content_4_item',
					'plural'	=> 'log_content_4_items',
					'ajax'		=> false
				)
			);
		
		}
		
		/**
		 * Create Table headers
		 * 
		 * @param string $which top for above table, bottom for below
		 *
		 **/
		function extra_tablenav( $which ) {
		
			global $bwps;
		
			if ( $which == 'top' ) {
				_e( 'The following is a list of 404 errors found on your site with the relative url listed first, the number of times the error was encountered in parenthases, and the last time the error was encounterd given last.', $bwps->hook );
			}
			
		}
		
		/**
		 * Define Columns
		 *
		 * @return array array of column titles
		 *
		 **/
		function get_columns() {
		
			global $bwps;
		
			return array(
				'time'		=> __( 'Last Found', $bwps->hook ),
				'uri'		=> __( 'URI', $bwps->hook ),
				'referrer' 	=> __( 'Referrer', $bwps->hook ),
				'count'		=> __( 'Count', $bwps->hook )
			);
		
		}
		
		/**
		 * Define time column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_time( $item ) {
		
			return date( 'Y-m-d H:i:s', $item['time'] );
		
		}
		
		/**
		 * Define added column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_uri( $item ) {
		
			return $item['uri'];
		
		}
		
		/**
		 * Define deleted column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_referrer( $item ) {
		
			return $item['referrer'];
		
		}
		
		/**
		 * Define modified column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_count( $item ) {
		
			return $item['count'];
		
		}
		
		/**
		 * Prepare data for table
		 *
		 **/
		function prepare_items() {
		
			global $wpdb;
			
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );
        	
        	$errors = $wpdb->get_results( "SELECT * FROM `" . $wpdb->base_prefix . "bwps_log` WHERE `type` = 2;", ARRAY_A );
        	$data = array();
        	foreach ( $errors as $error ) { //loop through and group 404s

        		if ( isset( $data[$error['url']] ) ) {
        			$data[$error['url']]['count'] = $data[$error['url']]['count'] + 1;
        			$data[$error['url']]['last'] = $data[$error['url']]['last'] > $error['timestamp'] ? $data[$error['url']]['last'] : $error['timestamp'];
        			$data[$error['url']]['referrer'] = $error['referrer'];
        			$data[$error['url']]['id'] = $error['id'];
        		} else {
        			$data[$error['url']]['count'] = 1;
        			$data[$error['url']]['last'] = $error['timestamp'];
        			$data[$error['url']]['referrer'] = $error['referrer'];
        			$data[$error['url']]['id'] = $error['id'];
        		} 
        		
        	}
        	
        	$per_page = 50; //50 items per page
        	
        	$current_page = $this->get_pagenum();
        	
        	$total_items = count( $data );
        	
        	$data = array_slice( $data,( ( $current_page - 1 ) * $per_page ), $per_page );
        	
        	$rows = array();
        	
        	$count = 0;
        	
	       	//Loop through results and take data we need
        	foreach ( $data as $item => $attr ) {
        	
	       		$rows[$count]['time'] = $attr['last'];
        		$rows[$count]['id'] = $attr['id'];
        		$rows[$count]['uri'] = $item;
        		$rows[$count]['referrer'] = $attr['referrer'];
        		$rows[$count]['count'] = $attr['count'];
        		
        		$count++;
        	
        	}    
        	
        	usort ( $rows, array( &$this, 'sortrows' ) );    	
        	
        	$this->items = $rows;
        	
        	$this->set_pagination_args( 
        		array(
        	    	'total_items' => $total_items,
	        	    'per_page'    => $per_page,
    	    	    'total_pages' => ceil( $total_items/$per_page )
        		)
        	);
			
		}
		
		/**
		 * Sort rows
		 *
		 * Sorts rows by count in descending order
		 *
		 * @param array $a first array to compare
		 * @param array $b second array to compare
		 * @return int comparison result
		 *
		 **/
		function sortrows( $a, $b ) {
		
			if ( $a['count'] > $b['count'] ) {
				return -1;
			} elseif ( $a['count'] < $b['count'] ) {
				return 1;
			} else {
				return 0;
			}
			
		}
	
	}

}

//table of all lockouts
if ( ! class_exists( 'log_content_5_table' ) ) {

	class log_content_5_table extends WP_List_Table {
	
		/**
		 * Construct table object
		 *
		 **/
		function __construct() {
		
			parent::__construct(
				array(
					'singular'	=> 'log_content_5_item',
					'plural'	=> 'log_content_5_items',
					'ajax'		=> false
				)
			);
		
		}
		
		/**
		 * Create Table headers
		 * 
		 * @param string $which top for above table, bottom for below
		 *
		 **/
		function extra_tablenav( $which ) {
		
			global $bwps;
		
			if ( $which == 'top' ) {
				_e( 'The following is a log of all lockouts in the system.', $bwps->hook );
			}
			
		}
		
		/**
		 * Define Columns
		 *
		 * @return array array of column titles
		 *
		 **/
		function get_columns() {
		
			global $bwps;
		
			return array(
				'time'		=> __( 'Time', $bwps->hook ),
				'reason'	=> __( 'Reason', $bwps->hook ),
				'host'	=> __( 'Host', $bwps->hook ),
				'user'	=> __( 'User', $bwps->hook )
			);
		
		}
		
		/**
		 * Define time column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_time( $item ) {
		
			return date( 'Y-m-d H:i:s', $item['time'] );
		
		}
		
		/**
		 * Define added column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_reason( $item ) {
		
			return $item['reason'] == 2 ? 'Too many 404s' : 'Bad Logins';
		
		}
		
		/**
		 * Define deleted column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_host( $item ) {
		
			return $item['host'];
		
		}
		
		/**
		 * Define modified column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_user( $item ) {
		
			$lockuser = get_user_by( 'id', $item['user'] );
		
			if ( $lockuser === false ) {
				return '';
			} else {
				return $lockuser->user_login;
			}
		
		}
		
		/**
		 * Prepare data for table
		 *
		 **/
		function prepare_items() {
		
			global $wpdb;
			
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );
        	
        	$data = $wpdb->get_results( "SELECT * FROM `" . $wpdb->base_prefix . "bwps_lockouts` ORDER BY starttime DESC;", ARRAY_A );
        	
        	$per_page = 50; //50 items per page
        	
        	$current_page = $this->get_pagenum();
        	
        	$total_items = count( $data );
        	
        	$data = array_slice( $data,( ( $current_page - 1 ) * $per_page ), $per_page );
        	
        	$rows = array();
        	
        	$count = 0;
        	
        	//Loop through results and take data we need
        	foreach ( $data as $item ) {
        	
	       		$rows[$count]['time'] = $item['starttime'];
        		$rows[$count]['id'] = $item['id'];
        		$rows[$count]['host'] = $item['host'];
        		$rows[$count]['reason'] = $item['type'];
        		$rows[$count]['user'] = $item['user'];
        		
        		$count++;
        	
        	}        	
        	
        	$this->items = $rows;
        	
        	$this->set_pagination_args( 
        		array(
        	    	'total_items' => $total_items,
	        	    'per_page'    => $per_page,
    	    	    'total_pages' => ceil( $total_items/$per_page )
        		)
        	);
			
		}
	
	}

}

//Table for file changes log
if ( ! class_exists( 'log_content_6_table' ) ) {

	class log_content_6_table extends WP_List_Table {
	
		/**
		 * Construct table object
		 *
		 **/
		function __construct() {
		
			parent::__construct(
				array(
					'singular'	=> 'log_content_6_item',
					'plural'	=> 'log_content_6_items',
					'ajax'		=> false
				)
			);
		
		}
		
		/**
		 * Create Table headers
		 * 
		 * @param string $which top for above table, bottom for below
		 *
		 **/
		function extra_tablenav( $which ) {
		
			global $bwps;
		
			if ( $which == 'top' ) {
				_e( 'The following is a log of all file changes seen by the system.', $bwps->hook );
			}
			
		}
		
		/**
		 * Define Columns
		 *
		 * @return array array of column titles
		 *
		 **/
		function get_columns() {
		
			global $bwps;
		
			return array(
				'time'		=> __( 'Time', $bwps->hook ),
				'added'	=> __( 'Added', $bwps->hook ),
				'deleted'	=> __( 'Deleted', $bwps->hook ),
				'modified'	=> __( 'Modified', $bwps->hook ),
				'details'	=> __( 'Details', $bwps->hook ),
			);
		
		}
		
		/**
		 * Define time column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_time( $item ) {
		
			return date( 'Y-m-d H:i:s', $item['timestamp'] );
		
		}
		
		/**
		 * Define added column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_added( $item ) {
		
			return $item['added'];
		
		}
		
		/**
		 * Define deleted column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_deleted( $item ) {
		
			return $item['deleted'];
		
		}
		
		/**
		 * Define modified column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_modified( $item ) {
		
			return $item['modified'];
		
		}
		
		/**
		 * Define details column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_details( $item ) {
		
			global $bwps;
		
			return '<a href="' . $_SERVER['REQUEST_URI'] . '&bwps_change_details_id=' . $item['id'] . '#file-change">' . __( 'View Details', $bwps->hook ) . '</a>';
		
		}
		
		/**
		 * Prepare data for table
		 *
		 **/
		function prepare_items() {
		
			global $wpdb;
			
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );
        	
        	$data = $wpdb->get_results( "SELECT id, timestamp, data FROM `" . $wpdb->base_prefix . "bwps_log` WHERE type=3 ORDER BY timestamp DESC;", ARRAY_A );
        	
        	$per_page = 50; //50 items per page
        	
        	$current_page = $this->get_pagenum();
        	
        	$total_items = count( $data );
        	
        	$data = array_slice( $data,( ( $current_page - 1 ) * $per_page ), $per_page );
        	
        	$rows = array();
        	
        	$count = 0;
        	
        	//Loop through results and take data we need
        	foreach ( $data as $item ) {
        	
        		$files = maybe_unserialize( $item['data'] );
        	
        		$rows[$count]['timestamp'] = $item['timestamp'];
        		$rows[$count]['id'] = $item['id'];
        		$rows[$count]['added'] = sizeof( $files['added'] );
        		$rows[$count]['deleted'] = sizeof( $files['removed'] );
        		$rows[$count]['modified'] = sizeof( $files['changed'] );
        		
        		$count++;
        	
        	}        	
        	
        	$this->items = $rows;
        	
        	$this->set_pagination_args( 
        		array(
        	    	'total_items' => $total_items,
	        	    'per_page'    => $per_page,
    	    	    'total_pages' => ceil( $total_items/$per_page )
        		)
        	);
			
		}
	
	}

}

//added files table
if ( ! class_exists( 'log_details_added_table' ) ) {

	class log_details_added_table extends WP_List_Table {
	
		public $recordid;
	
		/**
		 * Construct table object
		 *
		 **/
		function __construct( $id ) {
		
			global $recordid;
			
			$recordid = $id;
		
			parent::__construct(
				array(
					'singular'	=> 'log_details_added_item',
					'plural'	=> 'log_details_added_items',
					'ajax'		=> false
				)
			);
		
		}
		
		/**
		 * Create Table headers
		 * 
		 * @param string $which top for above table, bottom for below
		 *
		 **/
		function extra_tablenav( $which ) {
		
			global $bwps;
		
			if ( $which == 'top' ) {
				echo '<h4>' . __( 'Files Added', $bwps->hook ) . '</h4>';
			}
			
		}
		
		/**
		 * Define Columns
		 *
		 * @return array array of column titles
		 *
		 **/
		function get_columns() {
		
			global $bwps;
		
			return array(
				'file'		=> __( 'File', $bwps->hook ),
				'modified'	=> __( 'modified', $bwps->hook ),
				'hash'	=> __( 'hash', $bwps->hook ),
			);
		
		}
		
		/**
		 * Define file column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_file( $item ) {
		
			return $item['file'];
		
		}
		
		/**
		 * Define modified column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_modified( $item ) {
		
			return get_date_from_gmt( date( 'Y-m-d H:i:s', $item['modified'] ) );
		
		}
		
		/**
		 * Define hash column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_hash( $item ) {
		
			return $item['hash'];
		
		}
		
		/**
		 * Prepare data for table
		 *
		 **/
		function prepare_items() {
		
			global $wpdb, $recordid;
			
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );
        	
        	$data = $wpdb->get_results( "SELECT * FROM `" . $wpdb->base_prefix . "bwps_log` WHERE id=" . absint( $recordid ) . " ORDER BY timestamp DESC;", ARRAY_A );
        	
        	$data = maybe_unserialize( $data[0]['data'] );
        		
        	//seperate array by category
        	$data = $data['added'];
        	
        	$per_page = 50;
        	
        	$current_page = $this->get_pagenum();
        	
        	$total_items = count( $data );
        	
        	$data = array_slice( $data,( ( $current_page - 1 ) * $per_page ), $per_page );
        	
        	$rows = array();
        	
        	$count = 0;
        	
        	foreach ( $data as $item => $attr ) {
        	
        		$rows[$count]['file'] = $item;
        		$rows[$count]['hash'] = $attr['hash'];
        		$rows[$count]['modified'] = $attr['mod_date'];
        		
        		$count++;
        	
        	}        	
        	
        	$this->items = $rows;
        	
        	$this->set_pagination_args( 
        		array(
        	    	'total_items' => $total_items,
	        	    'per_page'    => $per_page,
    	    	    'total_pages' => ceil( $total_items/$per_page )
        		)
        	);
			
		}
	
	}

}

//removed files table
if ( ! class_exists( 'log_details_removed_table' ) ) {

	class log_details_removed_table extends WP_List_Table {
	
		public $recordid;
	
		/**
		 * Construct table object
		 *
		 **/
		function __construct( $id ) {
		
			global $recordid;
			
			$recordid = $id;
		
			parent::__construct(
				array(
					'singular'	=> 'log_details_removed_item',
					'plural'	=> 'log_details_removed_items',
					'ajax'		=> false
				)
			);
		
		}
		
		/**
		 * Create Table headers
		 * 
		 * @param string $which top for above table, bottom for below
		 *
		 **/
		function extra_tablenav( $which ) {
		
			global $bwps;
		
			if ( $which == 'top' ) {
				echo '<h4>' . __( 'Files Removed', $bwps->hook ) . '</h4>';
			}
			
		}
		
		/**
		 * Define Columns
		 *
		 * @return array array of column titles
		 *
		 **/
		function get_columns() {
		
			global $bwps;
		
			return array(
				'file'		=> __( 'File', $bwps->hook ),
				'modified'	=> __( 'modified', $bwps->hook ),
				'hash'	=> __( 'hash', $bwps->hook ),
			);
		
		}
		
		/**
		 * Define file column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_file( $item ) {
		
			return $item['file'];
		
		}
		
		/**
		 * Define modified column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_modified( $item ) {
		
			return get_date_from_gmt( date( 'Y-m-d H:i:s', $item['modified'] ) );
		
		}
		
		/**
		 * Define hash column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_hash( $item ) {
		
			return $item['hash'];
		
		}
		
		/**
		 * Prepare data for table
		 *
		 **/
		function prepare_items() {
		
			global $wpdb, $recordid;
			
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );
        	
        	$data = $wpdb->get_results( "SELECT * FROM `" . $wpdb->base_prefix . "bwps_log` WHERE id=" . absint( $recordid ) . " ORDER BY timestamp DESC;", ARRAY_A );
        	
        	$data = maybe_unserialize( $data[0]['data'] );
        		
        	//seperate array by category
        	$data = $data['removed'];
        	
        	$per_page = 50;
        	
        	$current_page = $this->get_pagenum();
        	
        	$total_items = count( $data );
        	
        	$data = array_slice( $data,( ( $current_page - 1 ) * $per_page ), $per_page );
        	
        	$rows = array();
        	
        	$count = 0;
        	
        	foreach ( $data as $item => $attr ) {
        	
        		$rows[$count]['file'] = $item;
        		$rows[$count]['hash'] = $attr['hash'];
        		$rows[$count]['modified'] = $attr['mod_date'];
        		
        		$count++;
        	
        	}        	
        	
        	$this->items = $rows;
        	
        	$this->set_pagination_args( 
        		array(
        	    	'total_items' => $total_items,
	        	    'per_page'    => $per_page,
    	    	    'total_pages' => ceil( $total_items/$per_page )
        		)
        	);
			
		}
	
	}

}

//modified files table
if ( ! class_exists( 'log_details_modified_table' ) ) {

	class log_details_modified_table extends WP_List_Table {
	
		public $recordid;
	
		/**
		 * Construct table object
		 *
		 **/
		function __construct( $id ) {
		
			global $recordid;
			
			$recordid = $id;
		
			parent::__construct(
				array(
					'singular'	=> 'log_details_modified_item',
					'plural'	=> 'log_details_modified_items',
					'ajax'		=> false
				)
			);
		
		}
		
		/**
		 * Create Table headers
		 * 
		 * @param string $which top for above table, bottom for below
		 *
		 **/
		function extra_tablenav( $which ) {
		
			global $bwps;
		
			if ( $which == 'top' ) {
				echo '<h4>' . __( 'Files Modified', $bwps->hook ) . '</h4>';
			}
			
		}
		
		/**
		 * Define Columns
		 *
		 * @return array array of column titles
		 *
		 **/
		function get_columns() {
		
			global $bwps;
		
			return array(
				'file'		=> __( 'File', $bwps->hook ),
				'modified'	=> __( 'modified', $bwps->hook ),
				'hash'	=> __( 'hash', $bwps->hook ),
			);
		
		}
		
		/**
		 * Define file column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_file( $item ) {
		
			return $item['file'];
		
		}
		
		/**
		 * Define modified column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_modified( $item ) {
		
			return get_date_from_gmt( date( 'Y-m-d H:i:s', $item['modified'] ) );
		
		}
		
		/**
		 * Define hash column
		 *
		 * @param array $item array of row data
		 * @return string formatted output
		 *
		 **/
		function column_hash( $item ) {
		
			return $item['hash'];
		
		}
		
		/**
		 * Prepare data for table
		 *
		 **/
		function prepare_items() {
		
			global $wpdb, $recordid;
			
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );
        	
        	$data = $wpdb->get_results( "SELECT * FROM `" . $wpdb->base_prefix . "bwps_log` WHERE id=" . absint( $recordid ) . " ORDER BY timestamp DESC;", ARRAY_A );
        	
        	$data = maybe_unserialize( $data[0]['data'] );
        		
        	//seperate array by category
        	$data = $data['changed'];
        	
        	$per_page = 50;
        	
        	$current_page = $this->get_pagenum();
        	
        	$total_items = count( $data );
        	
        	$data = array_slice( $data,( ( $current_page - 1 ) * $per_page ), $per_page );
        	
        	$rows = array();
        	
        	$count = 0;
        	
        	foreach ( $data as $item => $attr ) {
        	
        		$rows[$count]['file'] = $item;
        		$rows[$count]['hash'] = $attr['hash'];
        		$rows[$count]['modified'] = $attr['mod_date'];
        		
        		$count++;
        	
        	}        	
        	
        	$this->items = $rows;
        	
        	$this->set_pagination_args( 
        		array(
        	    	'total_items' => $total_items,
	        	    'per_page'    => $per_page,
    	    	    'total_pages' => ceil( $total_items/$per_page )
        		)
        	);
			
		}
	
	}

}
