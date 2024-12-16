<?php
/**
 * Term Selector
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

if ( ! class_exists( 'SportsPress_Term_Selector' ) ) :

	class SportsPress_Term_Selector {

		/**
		 * Constructor
		 */
		protected $sport_taxonomy;

		public function __construct() {
			$this->sport_taxonomy = get_option('sp_sns_predicts_taxonomy');

			// Hooks
			add_filter( 'sportspress_sns_terms_football', array( $this, 'sns_predicts_football' ) );
			add_filter( 'sportspress_sns_terms_football', array( $this, 'sns_articles_football' ) );
			add_filter( 'sportspress_sns_terms_football', array( $this, 'sns_news_football' ) );

			add_filter( 'sportspress_sns_terms_hockey', array( $this, 'sns_predicts_hockey' ) );
			add_filter( 'sportspress_sns_terms_hockey', array( $this, 'sns_articles_hockey' ) );
			add_filter( 'sportspress_sns_terms_hockey', array( $this, 'sns_news_hockey' ) );

			add_filter( 'sportspress_sns_terms_basketball', array( $this, 'sns_predicts_basketball' ) );
			add_filter( 'sportspress_sns_terms_basketball', array( $this, 'sns_articles_basketball' ) );
			add_filter( 'sportspress_sns_terms_basketball', array( $this, 'sns_news_basketball' ) );

			add_filter( 'sportspress_sns_terms_tennis', array( $this, 'sns_predicts_tennis' ) );
			add_filter( 'sportspress_sns_terms_tennis', array( $this, 'sns_articles_tennis' ) );
			add_filter( 'sportspress_sns_terms_tennis', array( $this, 'sns_news_tennis' ) );


			
		}

		public function sns_news_football( $options ) {
			return $this->options( $options, 'sns_news_football', 'Категория новостей', 'category' );
		}

		public function sns_articles_football( $options ) {
			return $this->options( $options, 'sns_articles_football', 'Категория статей', 'category' );
		}

		public function sns_predicts_football( $options ) {
			return $this->options( $options, 'sns_predicts_football', 'Категория прогнозов', $this->sport_taxonomy );
		}		

		public function sns_news_hockey( $options ) {
			return $this->options( $options, 'sns_news_hockey', 'Категория новостей', 'category' );
		}

		public function sns_articles_hockey( $options ) {
			return $this->options( $options, 'sns_articles_hockey', 'Категория статей', 'category' );
		}

		public function sns_predicts_hockey( $options ) {
			return $this->options( $options, 'sns_predicts_hockey', 'Категория прогнозов', $this->sport_taxonomy );
		}

		public function sns_news_basketball( $options ) {
			return $this->options( $options, 'sns_news_basketball', 'Категория новостей', 'category' );
		}

		public function sns_articles_basketball( $options ) {
			return $this->options( $options, 'sns_articles_basketball', 'Категория статей', 'category' );
		}

		public function sns_predicts_basketball( $options ) {
			return $this->options( $options, 'sns_predicts_basketball', 'Категория прогнозов', $this->sport_taxonomy );
		}

		public function sns_news_tennis( $options ) {
			return $this->options( $options, 'sns_news_tennis', 'Категория новостей', 'category' );
		}

		public function sns_articles_tennis( $options ) {
			return $this->options( $options, 'sns_articles_tennis', 'Категория статей', 'category' );
		}

		public function sns_predicts_tennis( $options ) {
			return $this->options( $options, 'sns_predicts_tennis', 'Категория прогнозов', $this->sport_taxonomy );
		}

		public function options( $options, $post_type, $title, $taxonomy ) {

			$terms = get_terms( [
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			] );

			$items = [];
			
			foreach ($terms as $term) {
				$items[$term->slug] = $term->name;
			}

			$items = array_merge( array( 'default' => 'Выберите категорию' ), $items );

			$options = array_merge(
				array(
					array(
						'title'   => $title,
						'id'      => 'sportspress_' . $post_type . '_term',
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

new SportsPress_Term_Selector();