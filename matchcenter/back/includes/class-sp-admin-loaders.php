<?php
/**
 * Setup loaders for SP data from Sports API.
 *
 * @author      Alex Torbeev
 * @category    Admin
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'SP_Admin_Loaders' ) ) :

	/**
	 * SP_Admin_Loaders Class
	 */
	class SP_Admin_Loaders {

		/**
		 * Hook in tabs.
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'register_loaders' ), 7 );
		}

		/**
		 * Add menu items
		 */
		public function register_loaders() {
			global $pagenow;
			$loaders = 
				array(
					'sp_event_sports' => array(
						'name'        => esc_attr__( 'SportsPress Events (Sports API)', 'sportspress' ),
						'description' => wp_kses_post( __( 'Import <strong>events</strong> from Sports API.', 'sportspress' ) ),
						'callback'    => array( $this, 'events_loader' ),
					),
					'sp_team_sports'  => array(
						'name'        => esc_attr__( 'SportsPress Teams (Sports API)', 'sportspress' ),
						'description' => wp_kses_post( __( 'Import <strong>teams</strong>  from Sports API.', 'sportspress' ) ),
						'callback'    => array( $this, 'teams_loader' ),
					),
					'sp_player_sports'=> array(
						'name'        => esc_attr__( 'SportsPress Players (Sports API)', 'sportspress' ),
						'description' => wp_kses_post( __( 'Import <strong>players</strong> from Sports API.', 'sportspress' ) ),
						'callback'    => array( $this, 'players_loader' ),
					),
					'sp_transfer_sports'=> array(
						'name'        => esc_attr__( 'SportsPress Players (Sports API)', 'sportspress' ),
						'description' => wp_kses_post( __( 'Import <strong>transfers</strong> from Sports API.', 'sportspress' ) ),
						'callback'    => array( $this, 'transfers_loader' ),
					),														
				
					'sp_fixtures_sports' => array(
						'name'        => esc_attr__( 'SportsPress Fixtures (Sports API)', 'sportspress' ),
						'description' => wp_kses_post( __( 'Import <strong>events fixtures</strong> from Sports API.', 'sportspress' ) ),
						'callback'    => array( $this, 'fixtures_loader' ),
					),


				
/*					'sp_staff_sports' => array(
						'name'        => esc_attr__( 'SportsPress Staff (Sports API)', 'sportspress' ),
						'description' => wp_kses_post( __( 'Import <strong>staff</strong> from Sports API.', 'sportspress' ) ),
						'callback'    => array( $this, 'staff_loader' ),
					), */
			);

			foreach ( $loaders as $id => $loader ) {
				add_submenu_page('options.php', $loader['name'], $loader['name'], 'manage_options',  $id, $loader['callback']);
			}
		}

		/**
		 * Add menu item
		 */
		public function events_loader() {
			$this->includes();


			require 'loaders/class-sp-loader-event.php';


			// Dispatch
			$loader = new SP_Loader_Event();
			$loader->dispatch();
		}

		/**
		 * Add menu item
		 */
		public function fixtures_loader() {
			$this->includes();

			require 'loaders/class-sp-loader-fixture.php';

			// Dispatch
			$loader = new SP_Loader_Fixture();
			$loader->dispatch();
		}

		/**
		 * Add menu item
		 */
		public function teams_loader() {
			$this->includes();

			require 'loaders/class-sp-loader-team.php';

			// Dispatch
			$loader = new SP_Loader_Team();
			$loader->dispatch();
		}

		/**
		 * Add menu item
		 */
		public function players_loader() {
			$this->includes();

			require 'loaders/class-sp-loader-player.php';

			// Dispatch
			$loader = new SP_Loader_Player();
			$loader->dispatch();
		}

		/**
		 * Add menu item
		 */
		public function transfers_loader() {
			$this->includes();

			require 'loaders/class-sp-loader-transfer.php';

			// Dispatch
			$loader = new SP_Loader_Transfer();
			$loader->dispatch();
		}

		/**
		 * Add menu item
		 */
		public function staff_loader() {
			$this->includes();

			require 'loaders/class-sp-loader-staff.php';

			// Dispatch
			$loader = new SP_Loader_Staff();
			$loader->dispatch();
		}

		public static function includes() {

			require 'loaders/class-sp-loader.php';
				
			SP_Loader_Functions::init();

		}
	}

endif;

return new SP_Admin_Loaders();
