<?php
/**
 * Sportspress SNS theme support functions
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

if ( ! class_exists( 'SP_SNS_Breadcrumbs' ) ) :

	

	class SP_SNS_Breadcrumbs {

		static $main_page, $breadcrumbs;

		static function init() {
			self::$main_page = get_option('sportspress_sns_main_page', '');
			//self::$breadcrumbs = self::setBreadcrumbs();
		}

		static function setBreadcrumbs() {

			global $wp_query;

			$main_page = get_page_by_path(get_option('sportspress_sns_main_page_page', ''));

			$main_page_url     = get_permalink($main_page);
			$main_page_name    = 'Матч-центр';

			$breadcrumbs_start = '<nav class="breadcrumb"><div class="wrapper">';
			
			$stories = do_shortcode('[wp_stories]');
			if ($stories == '[wp_stories]') {
				$stories = '';
			} else {
				$stories = '<div>' . $stories . '</div>';
			}

			$breadcrumbs_end   =  $stories . '</div></nav>';
			$breadcrumbs_home  = '<a href="' . get_home_url() . '/" class="home"><span>Главная</span></a>';
			$breadcrumbs_sns   = '<a href="' . $main_page_url . '">' . $main_page_name . '</a>';
			$breadcrumbs_sep   = '<i class="fa-chevron-right"></i>';

			$breadcrumbs = '';

			$tab = 'main';
			if ( isset( $wp_query->query['tab'] ) ) {
				$tab = $wp_query->query['tab'];
			}	
		

			if(SP_SNS_Theme::isMain()) {
				$breadcrumbs = $breadcrumbs_start . $breadcrumbs_home . $breadcrumbs_sep . $breadcrumbs_sns . $breadcrumbs_end;
			}

			if(SP_SNS_Theme::isSeason()) {

			}

			if(SP_SNS_Theme::isLeague()) {
				
				$league = get_queried_object();

				$sport_link = self::setLeagueBc($league);
				$league_link  = '<a href="' . get_term_link($league) .'">' . $league->name . '</a>';

				if ($tab == 'main') {
					$breadcrumbs = $breadcrumbs_start . $breadcrumbs_home . $breadcrumbs_sep . $breadcrumbs_sns . $breadcrumbs_sep . $sport_link . $breadcrumbs_end;
				} else {
					$breadcrumbs = $breadcrumbs_start . $breadcrumbs_home . $breadcrumbs_sep . $breadcrumbs_sns . $breadcrumbs_sep . $sport_link . $breadcrumbs_sep . $league_link . $breadcrumbs_end;				}
			}

			if(SP_SNS_Theme::isTransfers()) {
				$url = get_permalink( get_page_by_path( get_option('sportspress_sns_main_page', '') ) );
				$name = 'Футбол';
				$breadcrumb_type = '<a href="' . $url .'">' . $name . '</a>';
				$breadcrumbs = $breadcrumbs_start . $breadcrumbs_home . $breadcrumbs_sep . $breadcrumbs_sns . $breadcrumbs_sep . $breadcrumb_type . $breadcrumbs_end;
			}

			if(SP_SNS_Theme::isTeam()) {

				$team = get_post(get_the_ID());
				$league = array_shift( wp_get_post_terms( $team->ID, 'sp_league' ) );

				$sport_link = self::setLeagueBc($league);
				$team_link  = '<a href="' . get_permalink($team) .'">' . $team->post_title . '</a>';

				if ($tab == 'main') {
					$breadcrumbs = $breadcrumbs_start . $breadcrumbs_home . $breadcrumbs_sep . $breadcrumbs_sns . $breadcrumbs_sep . $sport_link . $breadcrumbs_end ;
				} else {

					$breadcrumbs = $breadcrumbs_start . $breadcrumbs_home . $breadcrumbs_sep . $breadcrumbs_sns . $breadcrumbs_sep . $sport_link . $breadcrumbs_sep . $team_link . $breadcrumbs_end;
				}
			}

			if(SP_SNS_Theme::isEvent()) {
				$event = get_post(get_the_ID());
				$event_object = new SP_SNS_Event(get_the_ID());
				$event_link  = '<a href="' . $event_object->permalink .'">' . $event->post_title . '</a>';
				$league = array_shift(wp_get_post_terms($event->ID, 'sp_league'));

				$sport_link = self::setLeagueBc($league);
				$league_link = '<a href="' . get_term_link($league) .'">' . $league->name . '</a>';

				if ($tab == 'main') {
					$breadcrumbs = $breadcrumbs_start . $breadcrumbs_home . $breadcrumbs_sep . $breadcrumbs_sns . $breadcrumbs_sep . $sport_link . $breadcrumbs_sep . $league_link . $breadcrumbs_end ;
				} else {
					$breadcrumbs = $breadcrumbs_start . $breadcrumbs_home . $breadcrumbs_sep . $breadcrumbs_sns . $breadcrumbs_sep . $sport_link . $breadcrumbs_sep . $league_link . $breadcrumbs_sep . $event_link . $breadcrumbs_end;
				}				
			}

			return $breadcrumbs;

		}

		static function setLeagueBc( $league ) {
			$sport_type = get_term_meta( $league->term_id, 'sport_type', 1 );
			$sport = new SP_SNS_Sport( $sport_type );

			$breadcrumb = '<a href="' . $sport->url .'">' . $sport->name . '</a>';

			return $breadcrumb;
		}



	}

endif;

SP_SNS_Breadcrumbs::init();
