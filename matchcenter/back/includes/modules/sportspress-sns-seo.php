<?php
/**
 * Sportspress SNS seo theme support functions
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

if ( ! class_exists( 'SP_SNS_SEO' ) ) :


	class SP_SNS_SEO {

		static function init() {
			add_filter( 'aioseop_title',          array( 'SP_SNS_SEO', 'default_aioseop_title' ));
			add_filter( 'aioseop_description',    array( 'SP_SNS_SEO', 'default_aioseop_description' ));
			add_filter( 'aioseop_canonical_url',  array( 'SP_SNS_SEO', 'default_aioseop_canonical'));
			add_filter( 'aioseop_prev_link',      array( 'SP_SNS_SEO', 'remove_aioseop_rel') );
			add_filter( 'aioseop_next_link',      array( 'SP_SNS_SEO', 'remove_aioseop_rel') );
			add_filter( 'aioseop_robots_meta',    array( 'SP_SNS_SEO', 'change_robots_meta_value') );
		}

		static function default_aioseop_title( $title ) {
			global $wp_query;
			
			$tab = 'main';
			if ( isset( $wp_query->query['tab'] ) ) {
				$tab = $wp_query->query['tab'];
			}

			$current_season_id = get_option('sportspress_season');
			$current_season    = get_term_by('id', $current_season_id, 'sp_season');

			if ( isset( $wp_query->query['season'] ) ) {
				$season_slug       = $wp_query->query['season'];
				$season            = get_term_by('slug', $season_slug, 'sp_season');
			} else {
				$season            = $current_season;
			}

			//$season_id = get_option('sportspress_season');
			//$season    = get_term_by('id', $season_id, 'sp_season');


			if ( SP_SNS_Theme::isMain() ) {

			}

			if ( SP_SNS_Theme::isTransfers() ) {

			}

			if ( SP_SNS_Theme::isLeague() ) {

				$league = get_queried_object();
				$title = get_term_meta( $league->term_id, 'seo_title_' . $tab, true );

				if ( ! isset( $wp_query->query['season'] ) ) {
					$league = new SP_SNS_league( $league->term_id );
					$season = $league->season;
				}

				$title = str_replace('[season]', $season->name, $title);

				if (empty($title)) {
					if ($tab == 'main') {
						$title = 'Обзор турнира ' . $league->name . ' ' . $season->name;
					}
					if ($tab == 'table') {
						$title = 'Турнирная таблица турнира ' . $league->name . ' ' . $season->name;
					}
					if ($tab == 'calendar') {
						$title = 'Результаты и расписание матчей турнира ' . $league->name . ' ' . $season->name;
					}
					if ($tab == 'transfers') {
						$title = 'Трансферы турнира ' . $league->name . ' ' . $season->name;
					}
				}
			}	

			if ( is_singular( 'sp_team' ) ) {

				$team = get_post(get_the_ID());
				$title = get_option('sns_team_title_' . $tab, '');
				$title = str_replace('[team]', $team->post_title, $title);
				$title = str_replace('[season]', $season->name, $title);			

				if (empty($title)) {
					if ($tab == 'main') {
						$title = 'Обзор клуба ' . $team->post_title . ' ' . $season->name;
					}
					if ($tab == 'table') {
						$title = 'Положение клуба ' . $team->post_title . ' в турнирных таблицах - ' . $season->name;
					}
					if ($tab == 'calendar') {
						$title = 'Результаты и расписание матчей клуба ' . $team->post_title . ' ' . $season->name;
					}
					if ($tab == 'transfers') {
						$title = 'Трансферы клуба ' . $team->post_title . ' ' . $season->name;
					}
				}			
			}

			if ( is_singular( 'sp_event' ) ) {
				$event = new SP_SNS_Event(get_the_ID());
				$league = array_shift(wp_get_post_terms(get_the_ID(), 'sp_league'));
				$team_home = $event->team_home->post;
				$team_away = $event->team_away->post;
				$event_date = wp_date('j M Y, h:i', strtotime($event->post->post_date));

				$title = get_option('sns_event_title_' . $tab, '');
				$title = str_replace('[team1]', $team_home->post_title, $title);
				$title = str_replace('[team2]', $team_away->post_title, $title);
				$title = str_replace('[date]', $event_date, $title);
				$title = str_replace('[season]', $season->name, $title);
				$title = str_replace('[league]', $league->name, $title);

				if (empty($title)) {
					if ($tab == 'main') {
						$title = 'Обзор матча ' . $team_home->post_title . ' - ' . $team_away->post_title . ' ' . $event_date . '. ' . $league->name;
					}
					if ($tab == 'predict') {
						$title = 'Прогноз на матч ' . $team_home->post_title . ' - ' . $team_away->post_title . ' ' . $event_date . '. ' . $league->name;
					}
				}			
			}


			return $title;
			
		}

		static function default_aioseop_description( $description ) {
			global $wp_query;
			
			$tab = 'main';
			if ( isset( $wp_query->query['tab'] ) ) {
				$tab = $wp_query->query['tab'];
			}

			$current_season_id = get_option('sportspress_season');
			$current_season    = get_term_by('id', $current_season_id, 'sp_season');

			if ( isset( $wp_query->query['season'] ) ) {
				$season_slug       = $wp_query->query['season'];
				$season            = get_term_by('slug', $season_slug, 'sp_season');
			} else {
				$season            = $current_season;
			}

			//$season_id = get_option('sportspress_season');
			//$season    = get_term_by('id', $season_id, 'sp_season');

			if (!$description) {

				if ( SP_SNS_Theme::isMain() ) {

				}

				if ( SP_SNS_Theme::isTransfers() ) {

				}

				if ( SP_SNS_Theme::isLeague() ) {

					$league = get_queried_object();
					$description = get_term_meta( $league->term_id, 'seo_description_' . $tab, true );
					if ( ! isset( $wp_query->query['season'] ) ) {
						$league = new SP_SNS_league( $league->term_id );
						$season = $league->season;
					}					
					$description = str_replace('[season]', $season->name, $description);

					if (empty($description)) {
						if ($tab == 'main') {
							$description = 'Календарь ' . $league->name . ' ' . $season->name . '. &#9888; Узнайте расписание матчей на текущий сезон. Турнирная таблица, статистика команд, календарь игр и онлайн трансляции.';
						}
						if ($tab == 'table') {
							$description = 'Турнирная таблица ' . $league->name . ' ' . $season->name . '. &#9888; Все о футбольном турнире - актуальная информация на сегодня. Узнайте результаты и расписание матчей, календарь игр, статистику команд.';
						}
						if ($tab == 'calendar') {
							$description = 'Результаты матчей ' . $league->name . ' ' . $season->name . '. &#9888; Узнайте результаты и расписание матчей на текущий сезон футбольного турнира ' . $league->name . '.';
						}
						if ($tab == 'transfers') {
							$description = 'Все трансферы турнира ' . $league->name . ' ' . $season->name . '. &#9888; Узнайте результаты и расписание трансферов на текущий сезон футбольного турнира ' . $league->name . '.';
						}
					}
				}	

				if ( is_singular( 'sp_team' ) ) {

					$team = get_post(get_the_ID());
					$description = get_option('sns_team_description_' . $tab, '');
					$description = str_replace('[team]', $team->post_title, $description);
					$description = str_replace('[season]', $season->name, $description);

					if (empty($description)) {
						if ($tab == 'main') {
							$description = 'Информация о команде ' . $team->post_title . ', результаты прошедших игр, расписание матчей, статистика. ' . $season->name;
						}
						if ($tab == 'table') {
							$description = 'Положение клуба ' . $team->post_title . ' в турнирных таблицах. &#9888; ' . $season->name;
						}
						if ($tab == 'calendar') {
							$description = 'Результаты и расписание матчей клуба ' . $team->post_title . '. Все о футбольном клубе - актуальная информация на сегодня. Узнайте результаты и расписание матчей, календарь игр. ' . $season->name;
						}
						if ($tab == 'transfers') {
							$description = 'Трансферы клуба ' . $team->post_title . '. Все трансферы футбольного клуба - актуальная информация на сегодня. Узнайте результаты трансферов, аренды. ' . $season->name;
						}
					}				
				}

				if ( is_singular( 'sp_event' ) ) {

					$event = new SP_SNS_Event(get_the_ID());
					$league = array_shift(wp_get_post_terms(get_the_ID(), 'sp_league'));
					$team_home = $event->team_home->post;
					$team_away = $event->team_away->post;
					$event_date = wp_date('j M Y, h:i', strtotime($event->post->post_date));

					$description = get_option('sns_event_description_' . $tab, '');
					$description = str_replace('[team1]', $team_home->post_title, $description);
					$description = str_replace('[team2]', $team_away->post_title, $description);
					$description = str_replace('[date]', $event_date, $description);
					$description = str_replace('[season]', $season->name, $description);
					$description = str_replace('[league]', $league->name, $description);

					if (empty($description)) {
						if ($tab == 'main') {
							$description = 'Обзор матча и статистика ' . $team_home->post_title . ' - ' . $team_away->post_title . ' ' . $event_date . '. ' . $league->name;
						}
						if ($tab == 'predict') {
							$description = 'Прогнозы экспертов на матч ' . $team_home->post_title . ' - ' . $team_away->post_title . ' ' . $event_date . '. ' . $league->name;
						}
					}

				}

			}

			return $description;
			
		}

		static function default_aioseop_canonical( $url ) {
			global $wp_query;

			$tab = false;
			if ( isset( $wp_query->query['tab'] ) ) {
				$tab = $wp_query->query['tab'];
			}

			$season = false;
			if ( isset( $wp_query->query['season'] ) ) {
				$season = $wp_query->query['season'];
			} 

			if ( SP_SNS_Theme::isLeague() ) {
				$slug = basename( get_term_link( get_queried_object() ) );
				if ($tab) {
					$url = home_url() . '/' . $slug . '/' . $tab . '/';
				}
			}

			if ( is_singular( 'sp_team' ) ) {
				$slug = basename( get_permalink( get_the_ID() ) );
				if ($tab) {
					$url = home_url() . '/' . $slug . '/' . $tab . '/';
				}
			}

			if ( is_singular( 'sp_event' ) ) {
				$event_object = new SP_SNS_Event(get_the_ID());
				$slug = $event_object->permalink;
				if ($tab) {
					$url = $slug . $tab . '/';
				} else {
					$url = $slug;
				}
			}

			return $url;
		}

		static function remove_aioseop_rel( $data ) {
			if ( SP_SNS_Theme::isLeague() || is_singular( 'sp_team' ) || is_singular( 'sp_event' ) ) {
				return false;
			}
			return $data;
		}

	    static function change_robots_meta_value($robots_meta_value)
	    {
	        
	        if ( is_singular( 'sp_event' ) ) {
	            $robots_meta_value = 'index, follow';
	        }
	        
	        return $robots_meta_value;
	    }


	}

endif;

SP_SNS_SEO::init();
