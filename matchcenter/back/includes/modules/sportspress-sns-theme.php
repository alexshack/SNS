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

if ( ! class_exists( 'SP_SNS_Theme' ) ) :


	class SP_SNS_Theme {
		static $sports;

		static function init() {
			//self::loadVars();
			add_filter('template_include', ['SP_SNS_Theme', 'setTemplates']);
		}


		static function loadVars() {
			self::$sports = self::getSports();
		}
		static function setTemplates($template) {

			if( self::isMainMC() ) {
				$template = get_template_directory() . '/sportspress/page-mc-main.php';
			}

			if( self::isTest() ) {
				$template = get_template_directory() . '/sportspress/test.php';
			}

			if( self::isMain() ) {
				$template = get_template_directory() . '/sportspress/page-main.php';
			}

			if( self::isSeason() ) {
				$template = get_template_directory() . '/sportspress/page-season.php';
			}

			if( self::isLeague() ) {
				$template = get_template_directory() . '/sportspress/page-league.php';
			}

			if( self::isTransfers() ) {
				$template = get_template_directory() . '/sportspress/page-transfers.php';
			}

			if( self::isTeams() ) {
				$template = get_template_directory() . '/sportspress/page-teams.php';
			}



			return $template;
		}

		static function isMC() {
			if ( self::isMainMC() || self::isTest() || self::isMain() || self::isSeason() || self::isLeague() || self::isTransfers() || self::isTeam() || self::isEvent() ) {
				return true;
			}
		}

		static function isMainMC() {
			if(self::getPageId('sportspress_sns_main_page_page') && is_page() && get_the_ID() == self::getPageId('sportspress_sns_main_page_page')) {
				return true;
			}
		}

		static function isTest() {
			if(self::getPageId('sportspress_sns_main_test_page') && is_page() && get_the_ID() == self::getPageId('sportspress_sns_main_test_page')) {
				return true;
			}
		}

		static function isMain() {
			if ( self::isFootball() || self::isHockey() || self::isBasketball() || self::isTennis() ) {
				return true;
			}
		}

		static function isFootball() {
			if(self::getPageId('sportspress_sns_main_football_page') && is_page() && get_the_ID() == self::getPageId('sportspress_sns_main_football_page')) {
				return true;
			}
		}

		static function isHockey() {
			if(self::getPageId('sportspress_sns_main_hockey_page') && is_page() && get_the_ID() == self::getPageId('sportspress_sns_main_hockey_page')) {
				return true;
			}
		}

		static function isBasketball() {
			if(self::getPageId('sportspress_sns_main_basketball_page') && is_page() && get_the_ID() == self::getPageId('sportspress_sns_main_basketball_page')) {
				return true;
			}
		}

		static function isTennis() {
			if(self::getPageId('sportspress_sns_main_tennis_page') && is_page() && get_the_ID() == self::getPageId('sportspress_sns_main_tennis_page')) {
				return true;
			}
		}

		static function getCurrentSport() {
			if ( self::isFootball() ) {
				return new SP_SNS_Sport( 'football' );
			}
			if ( self::isHockey() ) {
				return new SP_SNS_Sport( 'hockey' );
			}
			if ( self::isBasketball() ) {
				return new SP_SNS_Sport( 'basketball' );
			}
			if ( self::isTennis() ) {
				return new SP_SNS_Sport( 'tennis' );
			}
			return false;								
		}

		static function getCurrentSeasonID() {
			return get_option('sportspress_season');
		}

		static function getCurrentSeason() {
			return new SP_SNS_Season( get_option('sportspress_season') );
		}

		static function getMainSeasons() {
			$seasons = [];
			$season_terms = get_terms( [
				'taxonomy'   => 'sp_season',
				'hide_empty' => false,
				'parent'     => 0
			] );
			foreach ( $season_terms as $season_term ) {
				$seasons[] = new SP_SNS_Season( $season_term->term_id );
			}
			return $seasons;		
		}

		static function getSports() {
			$types = [
				'football',
				'hockey',
				'basketball',
				'tennis'
			];

			$sports = [];

			foreach ( $types as $type ) {
				$sport_page = get_option('sportspress_sns_main_' . $type . '_page', false);
				if ( $sport_page && $sport_page != 'default' ) {
					$sports[] = new SP_SNS_Sport( $type );
				}
			}

			return $sports;
		}

		static function getLeagues() {

	        $leagues = [];

	        $leagues_terms = get_terms( [
	            'taxonomy'   => 'sp_league',
	            'meta_key'   => 'sport_type',
	        ] );

	        usort( $leagues_terms, 'sp_sort_terms' );

	        $option_season_id = self::getCurrentSeasonID();

	        foreach ( $leagues_terms as $term ) {
	            $league = new SP_SNS_League( $term->term_id );
	            if ( $league->season_main->ID == $option_season_id ) {
	                $leagues[] = $league;
	            }
	        }

	        return $leagues;			
		}

		static function isSeason() {
			if(is_archive() && get_queried_object()->taxonomy == 'sp_season') {
				return true;
			}
		}

		static function isLeague() {
			if(is_archive() && get_queried_object()->taxonomy == 'sp_league') {
				return true;
			}
		}				

		static function isTransfers() {
			if(self::getPageId('sportspress_sns_transfers_page') && is_page() && get_the_ID() == self::getPageId('sportspress_sns_transfers_page')) {
				return true;
			}
		}

		static function isTeams() {
			if(self::getPageId('sportspress_sns_teams_page') && is_page() && get_the_ID() == self::getPageId('sportspress_sns_teams_page')) {
				return true;
			}
		}		

		static function isTeam() {
			if(is_singular('sp_team') ) {
				return true;
			}
		}

		static function isEvent() {
			if(is_singular('sp_event') ) {
				return true;
			}
		}

		static function getPageId($option) {
			$slug = get_option($option, '');

			if (!$slug) return false;

			$page = get_page_by_path( $slug );

			if (!$page) return false;

			return $page->ID;
		}

		static function getNews( $count = 6 ) {
			$sports = self::getSports();
			$term_ids = [];
			foreach ( $sports as $sport ) {
				if ( $sport->news_term ) {
					$term_ids[] = $sport->news_term->term_id;
				}
			}
			if ( count( $term_ids ) ) {
	            $news = get_posts( [
	                'post_type'   => 'post',
	                'numberposts' => $count,
	                'status'      => 'publish',
	                'category'    => $term_ids
	            ] );

	            if ( count( $news ) ) {
	                return $news;
	            }				
			}

			return false;
		}

		static function getArticles( $count = 6 ) {
			$sports = self::getSports();
			$term_ids = [];
			foreach ( $sports as $sport ) {
				if ( $sport->articles_term ) {
					$term_ids[] = $sport->articles_term->term_id;
				}
			}
			if ( count( $term_ids ) ) {
	            $articles = get_posts( [
	                'post_type'   => 'post',
	                'numberposts' => $count,
	                'status'      => 'publish',
	                'category'    => $term_ids
	            ] );

	            if ( count( $articles ) ) {
	                return $articles;
	            }				
			}

			return false;
		}

		static function getPredicts( $count = 6 ) {
			$sports = self::getSports();
			$term_ids = [];
			foreach ( $sports as $sport ) {
				if ( $sport->predicts_term ) {
					$term_ids[] = $sport->predicts_term->term_id;
				}
			}
			if ( count( $term_ids ) ) {
				$predict_type = get_option('sp_sns_predicts_post_type', false);
				$sport_field  = get_option('sp_sns_predicts_sport_meta', false);

				if ( $predict_type ) {
					if ( $sport_field ) {
			            $predicts_args = [
			                'post_type'   => $predict_type,
			                'numberposts' => $count,
			                'status'      => 'publish',
			                'meta_query'   => [
			                	'relation' => 'OR'
	                		]
			            ];

						foreach ( $term_ids as $term ) {
							$predicts_args['meta_query'][] = [
								'key'   => $sport_field,
								'value' => $term
							];
						}
					} else {
						$sport_taxonomy  = get_option('sp_sns_predicts_taxonomy', false);
			            $predicts_args = [
			                'post_type'   => $predict_type,
			                'numberposts' => $count,
			                'status'      => 'publish',
			                'tax_query'   => [
			                	'relation' => 'OR'
	                		]
			            ];
						foreach ( $term_ids as $term ) {
							$predicts_args['tax_query'][] = [
			                    'taxonomy' => $sport_taxonomy,
			                    'field'    => 'id',
			                    'terms'    => $term
							];
						}			            						
					}

					$predicts = get_posts( $predicts_args );

		            if ( count( $predicts ) ) {
		                return $predicts;
		            }
		        }				
			}

			return false;
		}


		static function downcounter($date){
		    $check_time = strtotime($date) - time();
		    if($check_time <= 0){
		        return [
		        	'days' => [
		        		'digit' => '-',
		        		'word'  => ''
		        	],
		        	'hours' => [
		        		'digit' => '-',
		        		'word'  => ''
		        	],
		        	'mins'  => [
		        		'digit' => '-',
		        		'word'  => ''
		        	],
		        ];
		    }

		    $days = floor($check_time/86400);
		    $hours = floor(($check_time%86400)/3600);
		    $minutes = floor(($check_time%3600)/60);
		    $seconds = $check_time%60; 

		    $str = [];

		    if($days >= 0) $str['days'] = ['digit' => $days, 'word' => self::declension($days,array('день','дня','дней'), true)];
		    if($hours >= 0) $str['hours'] = ['digit' => $hours, 'word' => self::declension($hours,array('час','часа','часов'), true)];
		    if($minutes >= 0) $str['mins'] = ['digit' => $minutes, 'word' => self::declension($minutes,array('минута','минуты','минут'), true)];
		    //if($seconds > 0) $str .= declension($seconds,array('секунда','секунды','секунд'));

		    if ($days < 10) $str['days']['digit'] = '0' . $days;
		    if ($hours < 10) $str['hours']['digit'] = '0' . $hours;
		    if ($minutes < 10) $str['mins']['digit'] = '0' . $minutes;

		    return $str;
		}

		static function declension($digit,$expr,$onlyword=false){
		    if(!is_array($expr)) $expr = array_filter(explode(' ', $expr));
		    if(empty($expr[2])) $expr[2]=$expr[1];
		    $i=preg_replace('/[^0-9]+/s','',$digit)%100;
		    if($onlyword) $digit='';
		    if($i>=5 && $i<=20) $res=$digit.' '.$expr[2];
		    else
		    {
		        $i%=10;
		        if($i==1) $res=$digit.' '.$expr[0];
		        elseif($i>=2 && $i<=4) $res=$digit.' '.$expr[1];
		        else $res=$digit.' '.$expr[2];
		    }
		    return trim($res);
		}

	}

endif;

SP_SNS_Theme::init();
