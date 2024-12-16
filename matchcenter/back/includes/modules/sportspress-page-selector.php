<?php
/**
 * Page Selector
 *
 * @author    Alex Torbeev
 * @category  Modules
 * @package   SportsPress SNS/Modules
 * @version   1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SportsPress_Page_Selector' ) ) :

	/**
	 * Main SportsPress Template Selector Class
	 *
	 * @class SportsPress_Template_Selector
	 * @version 2.3
	 */
	class SportsPress_Page_Selector {

		/**
		 * Constructor
		 */
		public function __construct() {

			// Hooks
			add_filter( 'sportspress_sns_pages_main', array( $this, 'sns_main_test' ) );
			add_filter( 'sportspress_sns_pages_main', array( $this, 'sns_main_page' ) );

			//add_filter( 'sportspress_sns_pages', array( $this, 'sns_teams' ) );
			add_filter( 'sportspress_sns_pages_football', array( $this, 'sns_transfer_football' ) );
			add_filter( 'sportspress_sns_pages_football', array( $this, 'sns_main_football' ) );
			add_filter( 'sportspress_sns_pages_hockey', array( $this, 'sns_main_hockey' ) );
			add_filter( 'sportspress_sns_pages_basketball', array( $this, 'sns_main_basketball' ) );
			add_filter( 'sportspress_sns_pages_tennis', array( $this, 'sns_main_tennis' ) );
		}

		public function sns_main_page( $options ) {
			return $this->options( $options, 'sns_main_page', 'Страница матч-центра' );
		}

		public function sns_main_test( $options ) {
			return $this->options( $options, 'sns_main_test', 'Страница тестирования' );
		}

		public function sns_main_football( $options ) {
			return $this->options( $options, 'sns_main_football', 'Главная страница' );
		}

		public function sns_transfer_football( $options ) {
			return $this->options( $options, 'sns_transfers_football', 'Страница трансферов' );
		}

		public function sns_teams( $options ) {
			return $this->options( $options, 'sns_teams', 'Страница списка команд' );
		}		

		public function sns_main_hockey( $options ) {
			return $this->options( $options, 'sns_main_hockey', 'Главная страница' );
		}

		public function sns_main_basketball( $options ) {
			return $this->options( $options, 'sns_main_basketball', 'Главная страница' );
		}

		public function sns_main_tennis( $options ) {
			return $this->options( $options, 'sns_main_tennis', 'Главная страница' );
		}

		public function options( $options, $post_type, $title ) {

			$pages = get_posts([
				'post_type' => 'page',
				'post_status' => 'publish',
				'numberposts' => -1
			]);

			$items = [];
			
			foreach ($pages as $page) {
				$items[$page->post_name] = $page->post_title;
			}

			$items = array_merge( array( 'default' => 'Выберите страницу' ), $items );

			$options = array_merge(
				array(
					array(
						'title'   => $title,
						'id'      => 'sportspress_' . $post_type . '_page',
						'default' => 'default',
						'type'    => 'select',
						'options' => $items,
					),
				),
				$options
			);

			return $options;
		}


	}

endif;

new SportsPress_Page_Selector();