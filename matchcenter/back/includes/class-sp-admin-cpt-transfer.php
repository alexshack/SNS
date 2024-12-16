<?php
/**
 * Admin functions for the transfers post type
 *
 * @author      Alex Torbeev
 * @category    Admin
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'SP_Admin_CPT' ) ) {
	include( SP()->plugin_path() . '/includes/admin/post-types/class-sp-admin-cpt.php' );
}

if ( ! class_exists( 'SP_Admin_CPT_Transfer' ) ) :

	/**
	 * SP_Admin_CPT_Transfer Class
	 */
	class SP_Admin_CPT_Transfer extends SP_Admin_CPT {

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->type = 'sp_transfer';

			// Post title fields
			add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );

			// Admin columns
			add_filter( 'manage_edit-sp_transfer_columns', array( $this, 'edit_columns' ) );
			add_action( 'manage_sp_transfer_posts_custom_column', array( $this, 'custom_columns' ), 2, 2 );

			// Filtering
			add_action( 'restrict_manage_posts', array( $this, 'filters' ) );
			add_filter( 'parse_query', array( $this, 'filters_query' ) );

			// Call SP_Admin_CPT constructor
			parent::__construct();
		}

		/**
		 * Change title boxes in admin.
		 *
		 * @param  string $text
		 * @param  object $post
		 * @return string
		 */
		public function enter_title_here( $text, $post ) {
			if ( $post->post_type == 'sp_transfer' ) {
				return esc_attr__( 'Название', 'sportspress' );
			}

			return $text;
		}

		/**
		 * Change the columns shown in admin.
		 */
		public function edit_columns( $existing_columns ) {
			unset( $existing_columns['author'] );
			$columns = array_merge(
				array(
					'cb'          => '<input type="checkbox" />',
					'title'       => null,
					'sp_transfer_type'   => esc_attr__( 'Тип', 'sportspress' ),
					'sp_summ'   => esc_attr__( 'Сумма', 'sportspress' ),
					'sp_team_out' => esc_attr__( 'Уходит из', 'sportspress' ),
					'sp_team_in'     => esc_attr__( 'Приходит в', 'sportspress' ),
				),
				$existing_columns,
				array(
					'title' => esc_attr__( 'Название', 'sportspress' ),
				)
			);
			return apply_filters( 'sportspress_player_admin_columns', $columns );
		}

		/**
		 * Define our custom columns shown in admin.
		 *
		 * @param  string $column
		 */
		public function custom_columns( $column, $post_id ) {
			switch ( $column ) :
				case 'sp_team_in':
					$team_id = get_post_meta( $post_id, 'sp_team_in', true );
					$team = get_post( $team_id );
					if ( $team ) :
						echo esc_html( $team->post_title );
					endif;
					break;
				case 'sp_team_out':
					$team_id = get_post_meta( $post_id, 'sp_team_out', true );
					$team = get_post( $team_id );
					if ( $team ) :
						echo esc_html( $team->post_title );
					endif;
					break;
				case 'sp_summ':
					echo get_post_meta( $post_id, 'sp_summ', true ) ? get_post_meta( $post_id, 'sp_summ', true ) : '&mdash;';
					break;										
				case 'sp_transfer_type':
					echo get_the_terms( $post_id, 'sp_transfer_type' ) ? wp_kses_post( the_terms( $post_id, 'sp_transfer_type' ) ) : '&mdash;';
					break;
			endswitch;
		}

		/**
		 * Show a category filter box
		 */
		public function filters() {
			global $typenow, $wp_query;

			if ( $typenow != 'sp_transfer' ) {
				return;
			}

			if ( taxonomy_exists( 'sp_transfer_type' ) ) :
				$selected = isset( $_REQUEST['sp_transfer_type'] ) ? sanitize_key( $_REQUEST['sp_transfer_type'] ) : null;
				$args     = array(
					'show_option_all' => esc_attr__( 'Показать все типы', 'sportspress' ),
					'taxonomy'        => 'sp_transfer_type',
					'name'            => 'sp_transfer_type',
					'selected'        => $selected,
				);
				sp_dropdown_taxonomies( $args );
			endif;

		}

		/**
		 * Filter in admin based on options
		 *
		 * @param mixed $query
		 */
		public function filters_query( $query ) {

			return $query;
		}



	}

endif;

return new SP_Admin_CPT_Transfer();
