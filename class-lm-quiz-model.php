<?php

/**
 * Created by PhpStorm.
 * User: dips
 * Date: 24/9/15
 * Time: 2:47 PM
 */

if ( ! class_exists( 'LM_Quiz_Model' ) ) {


	class LM_Quiz_Model extends RT_DB_Model {

		public function __construct() {
			parent::__construct( 'results' );
		}

		/**
		 * add quiz_result
		 *
		 * @param $data
		 *
		 * @return int
		 */
		function add_quiz_result( $data ) {
			return parent::insert( $data );
		}

		/**
		 * update quiz_result in DB
		 *
		 * @param $data
		 * @param $where
		 *
		 * @return mixed
		 */
		function update_quiz_result( $data, $where ) {
			return parent::update( $data, $where );
		}

		/**
		 * Delete quiz_result in DB
		 *
		 * @param $where
		 *
		 * @return int
		 */
		function delete_quiz_result( $where ) {
			return parent::delete( $where );
		}

		/**
		 * get quiz_result
		 * @param $where
		 * @param bool|false $offset
		 * @param bool|false $per_page
		 * @param string $order_by
		 *
		 * @return mixed
		 */
		function get_quiz_result( $where, $offset = false, $per_page = false, $order_by = 'id desc' ) {
			return parent::get( $where, $offset, $per_page, $order_by );
		}

		function get_columns( $select_cols, $columns, $offset = false, $per_page = false, $order_by = 'id desc' )
		{
			$select_cols = implode( ',', $select_cols );
			$select = 'SELECT ' . $select_cols . ' FROM ' . $this->table_name ;
			$where  = ' where 2=2 ';
			foreach ( $columns as $colname => $colvalue ) {
				if ( is_array( $colvalue ) ) {
					if ( ! isset( $colvalue['compare'] ) ) {
						$compare = 'IN';
					} else {
						$compare = $colvalue['compare'];
					}
					if ( ! isset( $colvalue['value'] ) ) {
						$colvalue['value'] = $colvalue;
					}
					$col_val_comapare = ( $colvalue['value'] ) ? '(\'' . implode( "','", $colvalue['value'] ) . '\')' : '';
					$where .= " AND {$this->table_name}.{$colname} {$compare} {$col_val_comapare}";
				} else {
					$where .= " AND {$this->table_name}.{$colname} = '{$colvalue}'";
				}
			}
			$sql = $select . $where;

			$sql .= " ORDER BY {$this->table_name}.$order_by";
			if ( false !== $offset ) {
				if ( ! is_integer( $offset ) ) {
					$offset = 0;
				}
				if ( intval( $offset ) < 0 ) {
					$offset = 0;
				}

				if ( ! is_integer( $per_page ) ) {
					$per_page = 1;
				}
				if ( intval( $per_page ) < 0 ) {
					$per_page = 1;
				}
				$sql .= ' LIMIT ' . $offset . ',' . $per_page;

			}
			global $wpdb;
			return $wpdb->get_results( $sql );
		}
	}
}