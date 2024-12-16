<?php
/**
 * Admin functions for the stage taxonomy
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

if ( ! class_exists( 'SP_Admin_CPT_Stage' ) ) :

	/**
	 * SP_Admin_CPT_Transfer Class
	 */
	class SP_Admin_CPT_Stage extends SP_Admin_CPT {

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->type = 'sp_stage';

			// Admin columns
			add_filter( 'manage_edit-sp_stage_columns', array( $this, 'edit_columns' ) );
			add_filter( 'manage_sp_stage_custom_column', array( $this, 'custom_columns' ), 10, 3 );

			// Call SP_Admin_CPT constructor
			parent::__construct();
		}


		/**
		 * Change the columns shown in admin.
		 */
		public function edit_columns( $existing_columns ) {
			$columns = array_merge(
				array(
					'cb'          => '<input type="checkbox" />',
					'image'       => null,
					'name'       => esc_attr__( 'Название', 'sportspress' ),
					'description'   => esc_attr__( 'Описание', 'sportspress' ),
					'rus_name'   => esc_attr__( 'На русском', 'sportspress' ),
					'slug'   => esc_attr__( 'Ярлык', 'sportspress' ),
					'posts' => esc_attr__( 'Записи', 'sportspress' ),
				),
				$existing_columns,
			);
			return apply_filters( 'sportspress_player_admin_columns', $columns );
		}

		/**
		 * Define our custom columns shown in admin.
		 *
		 * @param  string $column
		 */
		public function custom_columns( $columns, $column, $id ) {
			switch ( $column ) :
				case 'rus_name':
					echo get_term_meta( $id, 'rus_name', true ) ? get_term_meta( $id, 'rus_name', true ) : '...&mdash;';
					break;
			endswitch;
		}


	}

endif;

return new SP_Admin_CPT_Stage();
