<?php
/**
 * SNS Event Basketball importer - import events from Sports API into SportsPress.
 *
 * @author      Alex Torbeev
 * @category    Admin
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class SP_Loader_Event_Basketball {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	static function init( ) {

	}

	static function import( $season_id, $league_id, $date_start = false, $date_end = false ) {

		if (! $season_id || ! $league_id ) {
			return false;
		}

		$season = new SP_SNS_Season( $season_id );
		$league = new SP_SNS_League( $league_id );

		if ( ! $season->api_id || ! $league->api_id ) {
			return false;
		}

		$feed_league_id = $league->api_id;
		$feed_season_id = $season->api_id;

		$timezone = '&timezone=Europe/Moscow';
		
		if ( $date_start && $date_end ) {

			if ( $date_start != $date_end ) {
				$feeds = [];

				$date_end = wp_date('Y-m-d', strtotime($date_end . '+1 day') );
				$interval = DateInterval::createFromDateString('1 day');
				$period = new DatePeriod( new DateTime($date_start), $interval, new DateTime($date_end) );

				foreach ($period as $day) {
				    $date = $day->format( 'Y-m-d' );
				    $request = 'games?league=' . $feed_league_id . '&season=' . $feed_season_id . '&date=' . $date . $timezone;
				    $feed = SP_Loader_Functions::getFeeds($request, 'basketball');
				    if ( $feed ) {
				    	$feeds = array_merge( $feeds, $feed );
				    }
				}
			} else {
				$request = 'games?league=' . $feed_league_id . '&season=' . $feed_season_id . '&date=' . $date_start . $timezone;
				$feeds = SP_Loader_Functions::getFeeds($request, 'basketball');				
			}

		}

		if ( $date_start && ! $date_end ) {
			$request = 'games?league=' . $feed_league_id . '&season=' . $feed_season_id . '&date=' . $date_start . $timezone;
			$feeds = SP_Loader_Functions::getFeeds($request, 'basketball');
		}

		if ( ! $date_start && ! $date_end ) {
			$request = 'games?league=' . $feed_league_id . '&season=' . $feed_season_id . $timezone;
			$feeds = SP_Loader_Functions::getFeeds($request, 'basketball');			
		}

		$imported = 0;
		$updated = 0;
		$skipped = 0;
		$events_array = [];

		$live_statuses = [
			'Q1',
			'Q2',
			'Q3',
			'Q4',
			'OT',
			'BT',
			'HT'
		];

		$finish_statuses = [
			'FT',
			'AOT'
		];

		$games = [];

		if ( $feeds && count( $feeds ) ) {

			$league_days = [];
			$league_stages = [];

			foreach ($feeds as $game) {

				$results = false;
				$is_finished = in_array( $game['status']['short'], $finish_statuses );
				$is_live     = in_array( $game['status']['short'], $live_statuses );

				$game['id']                  = 'b' . $game['id'];
				$game['teams']['home']['id'] = 'b' . $game['teams']['home']['id'];
				$game['teams']['away']['id'] = 'b' . $game['teams']['away']['id'];
				
				
				$game['round'] = SP_Loader_Functions::translateRound( $game['week'] );
				$stage_id = SP_Loader_Functions::getStage( $game['week'] );

				$home_team = SP_Loader_Functions::getPostByApiID( 'sp_team', $game['teams']['home']['id'] );
				$away_team = SP_Loader_Functions::getPostByApiID( 'sp_team', $game['teams']['away']['id'] );

				if ( ! $home_team ) {
					$home_team = SP_Loader_Functions::addTeam( $game['teams']['home'], $league->ID, $season->ID );
				} else {
					wp_set_object_terms( $home_team->ID, (int)$league->ID, 'sp_league', true );
					wp_set_object_terms( $home_team->ID, (int)$season->ID, 'sp_season', true );						
				}
				if ( ! $away_team ) {
					$away_team = SP_Loader_Functions::addTeam( $game['teams']['away'], $league->ID, $season->ID );
				} else {
					wp_set_object_terms( $away_team->ID, (int)$league->ID, 'sp_league', true );
					wp_set_object_terms( $away_team->ID, (int)$season->ID, 'sp_season', true );						
				}					

				$date_str = date('j F Y', $game['timestamp']);
				$date_time = date('Y-m-d H:i:s', $game['timestamp']);

				$event_title = $home_team->post_title . ' - ' . $away_team->post_title . ', ' . $date_str;

				if ( $is_finished || $is_live ) {

					$outcome = [
						'home' => 'loss',
						'away' => 'loss'
					];

					if ( (int)$game['scores']['home']['total'] > (int)$game['scores']['away']['total'] ) {
						$outcome['home'] = 'win';
					}

					if ( (int)$game['scores']['home']['total'] < (int)$game['scores']['away']['total'] ) {
						$outcome['away'] = 'win';
					}							


					$firsthalf  = [null, null];
					$secondhalf = [null, null];
					$thirdhalf  = [null, null];
					$fourthhalf = [null, null];
					$overtime   = [null, null];
					
					$results = [
						$home_team->ID => [
							'firsthalf'   => $game['scores']['home']['quarter_1'],
							'secondhalf'  => $game['scores']['home']['quarter_2'],
							'thirdhalf'   => $game['scores']['home']['quarter_3'],
							'fourthhalf'  => $game['scores']['home']['quarter_4'],
							'overtime'    => $game['scores']['home']['over_time'],
							'goals'       => $game['scores']['home']['total'],
							'outcome'     => [$outcome['home']],
						],
						$away_team->ID => [
							'firsthalf'   => $game['scores']['away']['quarter_1'],
							'secondhalf'  => $game['scores']['away']['quarter_2'],
							'thirdhalf'   => $game['scores']['away']['quarter_3'],
							'fourthhalf'  => $game['scores']['away']['quarter_4'],
							'overtime'    => $game['scores']['away']['over_time'],
							'goals'       => $game['scores']['away']['total'],
							'outcome'     => [$outcome['away']],
						]
					];


				}

				if ( ( $game_post = SP_Loader_Functions::getPostByApiID( 'sp_event', $game['id'] ) ) ) {

					$post_id = $game_post->ID;

					if ( $results ) {
						wp_update_post( array(
							'ID' => $post_id,
							'post_status' => 'publish',
							'post_date'   => $date_time,
							'post_title'  => $event_title,
						) );
					} else {
						wp_update_post( array(
							'ID' => $post_id,
							'post_status' => 'future',
							'post_date'   => $date_time,
							'post_title'  => $event_title,
						) );				
					}

					$updated++;

				} else {

					if ($results) {
						$post_status = 'publish';
					} else {
						$post_status = 'future';
					}

					$args = [
						'post_type'   => 'sp_event',
						'post_status' => $post_status,
						'post_title'  => $event_title,
						'post_date'   => $date_time,
					];
					$post_id = wp_insert_post( $args );
					
					update_post_meta( $post_id, SP_Loader_Functions::$id_field, $game['id'] );

					add_post_meta( $post_id, 'sp_team', $home_team->ID );
					add_post_meta( $post_id, 'sp_team', $away_team->ID );

					$imported++;
				}

				wp_set_object_terms( $post_id, (int)$league->ID, 'sp_league', false );
				wp_set_object_terms( $post_id, (int)$season->ID, 'sp_season', false );
				wp_set_object_terms( $post_id, (int)$stage_id, 'sp_stage', false );

				update_post_meta( $post_id, 'sp_format', 'league' );
				update_post_meta( $post_id, 'sp_mode', 'team' );
				update_post_meta( $post_id, 'sp_result_columns', array() );
				update_post_meta( $post_id, 'sp_status', 'ok');	

				update_post_meta( $post_id, 'sp_day', $game['round'] );

				if ( $is_finished || $is_live ) {

					update_post_meta( $post_id, 'sp_results', $results );

					if ( $is_finished ) {
						update_post_meta( $post_id, 'sp_finished', 'yes' );
						update_post_meta( $post_id, 'sp_event_status', 'finished' );						
					}
					if ( $is_live ) {
						update_post_meta( $post_id, 'sp_finished', 'no' );
						update_post_meta( $post_id, 'sp_event_status', 'live' );
						update_post_meta( $post_id, 'sp_minutes', $game['status']['timer'] );							
					}

				} else {

					update_post_meta( $post_id, 'sp_event_status', $game['status']['short'] );

				}

				$league_days[$season->ID][] = $game['round'];
				$events_array[] = $home_team->post_title . ' - ' . $away_team->post_title . ', ' . $date_str . '<br>';

				if ( ! array_search( $stage_id, $league_stages ) ) {
					$league_stages[] = $stage_id;
				}

			}

			$current_days = get_term_meta( $league->ID, 'sp_days', 1 );
			if ( empty( $current_days ) || !is_array( $current_days ) ) {
				$current_days = [];
			}

			foreach ( $league_days as $season_day => $season_days ) {
				$days = array_unique( $season_days );
				$current_days[ $season_day ] = $days;
			}

			update_term_meta( $league->ID, 'sp_days', $current_days );

			$all_stages = get_term_meta( $league->ID, 'sp_stages', 1 );
			if ( empty( $all_stages ) || !is_array( $all_stages ) ) {
				$all_stages = [
					$season_id => []
				];
			}

			$stages = $all_stages[ $season_id ];
			$stages = array_merge( $stages, $league_stages );
			$stages = array_unique( $stages );
			$all_stages[ $season_id ] = $stages;

			update_term_meta( $league->ID, 'sp_stages', $all_stages );

		}

		$data = [
			'request'  => $request,
			'imported' => $imported,
			'updated'  => $updated,
			'skipped'  => $skipped,
			'events'   => $events_array
		];

		return $data;
	}


}