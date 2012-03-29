<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


if ( ! class_exists( 'log_content_6' ) ) {

	class log_content_6 extends WP_List_Table {
	
		function __construct() {
		
			parent::__construct(
				array(
					'singular'	=> 'log_content_6_item',
					'plural'	=> 'log_content_6_items',
					'ajax'		=> true
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
				'col_item_time'		=> __( 'Time', $bwps->hook ),
				'col_item_added'	=> __( 'Added', $bwps->hook ),
				'col_item_deleted'	=> __( 'Deleted', $bwps->hook ),
				'col_item_modified'	=> __( 'Modified', $bwps->hook ),
				'col_item_details'	=> __( 'Details', $bwps->hook ),
			);
		
		}
		
		function get_sortable_columns() {
		
			return array(
				'col_item_time'	=> 'timestamp'
			);
			
		}
		
		function prepare_items() {
		
			global $wpdb, $_wp_column_headers;
			
			$screen = get_current_screen();
			
			$query = "SELECT id, timestamp, data FROM `" . $wpdb->base_prefix . "bwps_log` WHERE type=3";
			
			$orderby = ! empty( $_GET['orderby'] ) ? mysql_real_escape_string( $_GET['orderby'] ) : 'timestamp';

	        $order = ! empty( $_GET['order'] ) ? mysql_real_escape_string( $_GET['order'] ) : 'DESC';

        	$query .= ' ORDER BY ' . $orderby . ' ' . $order;
        	
        	$totalitems = $wpdb->query( $query );
        	
        	$perpage = 50;
        	
        	$paged = ! empty( $_GET['paged'] ) ? mysql_real_escape_string( $_GET['paged'] ) : '';
        	
        	if ( empty( $paged ) || ! is_numeric( $paged ) || $paged <= 0 ) {
        		$paged = 1;
        	}
        	
        	$totalpages = ceil( $totalitems / $perpage );
        	
			if( ! empty( $paged ) && ! empty( $perpage ) ) {

				$offset = ( $paged - 1 ) * $perpage;
				$query .= ' LIMIT ' . ( int ) $offset . ',' . ( int ) $perpage;
			
			}
			
			$this->set_pagination_args(
				array(
					"total_items" 	=> $totalitems,
					"total_pages" 	=> $totalpages,
					"per_page" 		=> $perpage
				)
			);
			
			$columns = $this->get_columns();
	        $_wp_column_headers[$screen->id] = $columns;
	        
	        $items = $wpdb->get_results( $query, ARRAY_A );
	        $rows = array();
	               
	        foreach ( $items as $item ) {
	                
				$data = maybe_unserialize( $item['data'] );
	        	$row = array(
		        	'id' 		=> $item['id'],
		        	'timestamp' => $item['timestamp'],
	        		'added' 	=> sizeof( $data['added'] ),
	        		'deleted' 	=> sizeof( $data['removed'] ),
	        		'changed' 	=> sizeof( $data['changed'] )
	        	);
	        	
	        	$rows[] = (object) $row;
	        	       	
	        }
	        
	        $this->items = $rows;
			
		}
		
		function display_rows() {
		
			$records = $this->items;
					
			list( $columns, $hidden ) = $this->get_column_info();
			
			print_r( $columns );
		
			if( ! empty ( $records ) ) {

				foreach( $records as $rec ) {
		
			        echo '<tr id="record_'.$rec->id.'">';

					foreach ( $columns as $column_name => $column_display_name ) {
		
						$class = 'class="' . $column_name . ' column-' . $column_name . '"';
						$style = '';
						if ( in_array( $column_name, $hidden ) ) {
							$style = ' style="display:none;"';
						}
						
						$attributes = $class . $style;
		
						$editlink  = '/wp-admin/link.php?action=edit&link_id='.(int)$rec->id;
		
						//Display the cell
						switch ( $column_name ) {
							case "col_link_id":	
								echo '< td '.$attributes.'>'.stripslashes( $rec->timestamp ).'< /td>';	
								break;
							case "col_link_name": 
								echo '< td '.$attributes.'><strong><a href="'.$editlink.'" title="Edit">'.stripslashes( $rec->added ).'</a></strong>< /td>'; 
								break;
							case "col_link_url": 
								echo '< td '.$attributes.'>'.stripslashes( $rec->deleted ).'< /td>'; 
								break;
							case "col_link_description": 
								echo '< td '.$attributes.'>'.$rec->changed.'< /td>'; 
								break;
							case "col_link_visible": 
								echo '< td '.$attributes.'>'.$rec->link_visible.'< /td>'; 
								break;
						}
						
					}
		
					echo'</tr>';
					
				}
				
			}
			
		}
	
	}

}