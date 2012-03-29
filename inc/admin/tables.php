<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


if ( ! class_exists( 'log_content_6_table' ) ) {

	class log_content_6_table extends WP_List_Table {
	
		function __construct() {
		
			parent::__construct(
				array(
					'singular'	=> 'log_content_6_item',
					'plural'	=> 'log_content_6_items',
					'ajax'		=> false
				)
			);
		
		}
		
		function extra_tablenav( $which ) {
		
			global $bwps;
		
			if ( $which == 'top' ) {
				_e( 'The following is a log of all file changes seen by the system.', $bwps->hook );
			}
			
		}
		
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
		
		function column_time( $item ) {
		
			return date( 'Y-m-d H:i:s', $item['timestamp'] );
		
		}
		
		function column_added( $item ) {
		
			return $item['added'];
		
		}
		
		function column_deleted( $item ) {
		
			return $item['deleted'];
		
		}
		
		function column_modified( $item ) {
		
			return $item['modified'];
		
		}
		
		function column_details( $item ) {
		
			global $bwps;
		
			return '<a href="' . $_SERVER['REQUEST_URI'] . '&bwps_change_details_id=' . $item['id'] . '#file-change">' . __( 'View Details', $bwps->hook ) . '</a>';
		
		}
		
		function prepare_items() {
		
			global $wpdb;
			
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );
        	
        	$data = $wpdb->get_results( "SELECT id, timestamp, data FROM `" . $wpdb->base_prefix . "bwps_log` WHERE type=3 ORDER BY timestamp DESC;", ARRAY_A );
        	
        	$per_page = 50;
        	
        	$current_page = $this->get_pagenum();
        	
        	$total_items = count( $data );
        	
        	$data = array_slice( $data,( ( $current_page - 1 ) * $per_page ), $per_page );
        	
        	$rows = array();
        	
        	$count = 0;
        	
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

if ( ! class_exists( 'log_details_added_table' ) ) {

	class log_details_added_table extends WP_List_Table {
	
		public $recordid;
	
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
		
		function extra_tablenav( $which ) {
		
			global $bwps;
		
			if ( $which == 'top' ) {
				echo '<h4>' . __( 'Files Added', $bwps->hook ) . '</h4>';
			}
			
		}
		
		function get_columns() {
		
			global $bwps;
		
			return array(
				'file'		=> __( 'File', $bwps->hook ),
				'modified'	=> __( 'modified', $bwps->hook ),
				'hash'	=> __( 'hash', $bwps->hook ),
			);
		
		}
		
		function column_file( $item ) {
		
			return $item['file'];
		
		}
		
		function column_modified( $item ) {
		
			return get_date_from_gmt( date( 'Y-m-d H:i:s', $item['modified'] ) );
		
		}
		
		function column_hash( $item ) {
		
			return $item['hash'];
		
		}
		
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

if ( ! class_exists( 'log_details_removed_table' ) ) {

	class log_details_removed_table extends WP_List_Table {
	
		public $recordid;
	
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
		
		function extra_tablenav( $which ) {
		
			global $bwps;
		
			if ( $which == 'top' ) {
				echo '<h4>' . __( 'Files Removed', $bwps->hook ) . '</h4>';
			}
			
		}
		
		function get_columns() {
		
			global $bwps;
		
			return array(
				'file'		=> __( 'File', $bwps->hook ),
				'modified'	=> __( 'modified', $bwps->hook ),
				'hash'	=> __( 'hash', $bwps->hook ),
			);
		
		}
		
		function column_file( $item ) {
		
			return $item['file'];
		
		}
		
		function column_modified( $item ) {
		
			return get_date_from_gmt( date( 'Y-m-d H:i:s', $item['modified'] ) );
		
		}
		
		function column_hash( $item ) {
		
			return $item['hash'];
		
		}
		
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

if ( ! class_exists( 'log_details_modified_table' ) ) {

	class log_details_modified_table extends WP_List_Table {
	
		public $recordid;
	
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
		
		function extra_tablenav( $which ) {
		
			global $bwps;
		
			if ( $which == 'top' ) {
				echo '<h4>' . __( 'Files Modified', $bwps->hook ) . '</h4>';
			}
			
		}
		
		function get_columns() {
		
			global $bwps;
		
			return array(
				'file'		=> __( 'File', $bwps->hook ),
				'modified'	=> __( 'modified', $bwps->hook ),
				'hash'	=> __( 'hash', $bwps->hook ),
			);
		
		}
		
		function column_file( $item ) {
		
			return $item['file'];
		
		}
		
		function column_modified( $item ) {
		
			return get_date_from_gmt( date( 'Y-m-d H:i:s', $item['modified'] ) );
		
		}
		
		function column_hash( $item ) {
		
			return $item['hash'];
		
		}
		
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