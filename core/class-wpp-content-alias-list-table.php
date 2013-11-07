<?php
/**
 * Copyright (c) 2013, WP Poets and/or its affiliates <plugins@wppoets.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( ! class_exists( 'WP_List_Table' ) )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

/**
 * @author Michael Stutz <michaeljstutz@gmail.com>
 */
class WPP_Content_Alias_List_Table extends WP_List_Table {
	private $columns;
	private $data;
	
	/**
	 * 
	 */
	public function __construct() {
		$this->columns = array();
		$this->data = array();
		parent::__construct( array(
			'singular'	=> 'wpp_content_alias_list',	//Singular label
			'plural'		=> 'wpp_content_alias_lists', //plural label, also this well be one of the table css class
			'ajax'			=> false,											//We won't support Ajax for this table
		) );
	}
	
	/**
	 * 
	 */
	public function set_data( $new_data ) {
		$this->data = $new_data;
	}
	
	/**
	 * 
	 */
	public function set_columns( $new_columns ) {
		$this->columns = $new_columns;
	}
	
	/**
	 * 
	 */
	public function column_default( $item, $column_name ){
		switch( $column_name ){
			case 'url':
				return $item[ $column_name ];
			default:
				return '';
				//return print_r($item,true); //Show the whole array for troubleshooting purposes
		}
	}
	
	/**
	 * 
	 */
	public function get_columns(){
		return $this->columns;
	}
	
	/**
	 * 
	 */
	public function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items = $this->data;
	}
	
	/**
	 * 
	 */
	public function display_tablenav( $which ) {
		switch( $which ) {
			case "top":
				break;
			case "bottom":
				break;
		}
	}
	
	/**
	 * 
	 */
	public function extra_tablenav( $which ) {
		switch( $which ) {
			case "top":
				break;
			case "bottom":
				break;
		}
	}
}