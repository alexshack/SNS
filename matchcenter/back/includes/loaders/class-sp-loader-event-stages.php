<?php
/**
 * SNS Event Stages importer - import stages from Sports API into SportsPress.
 *
 * @author      Alex Torbeev
 * @category    Admin
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class SP_Loader_Event_Stages {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	static function init( ) {

	}

	static function import( $season_id, $league_id ) {

		if (! $season_id || ! $league_id ) {
			return false;
		}

		$season = new SP_SNS_Season( $season_id );
		$league = new SP_SNS_League( $league_id );

		if ( ! $season->api_id || ! $league->api_id || ! $league->sport_type ) {
			return false;
		}

		$feed_league_id = $league->api_id;
		$feed_season_id = $season->api_id;		

		if ( $league->sport_type == 'football' ) {		
			$request = 'fixtures?league=' . $feed_league_id . '&season=' . $feed_season_id;
		}

		if ( $league->sport_type == 'hockey' || $league->sport_type == 'basketball' ) {		
			$request = 'games?league=' . $feed_league_id . '&season=' . $feed_season_id;
		}		

		if ( $league->sport_type == 'tennis' ) {		
			$request = 'games?tournament=' . $feed_league_id;
		}	

		$feeds = SP_Loader_Functions::getFeeds( $request, $league->sport_type );

		$imported = 0;
		$updated = 0;
		$skipped = 0;
		$events_array = [];

		if ( $feeds && count( $feeds ) ) {
			
			$league_stages = [];

			foreach ($feeds as $key => $game) {
		

				if ( $league->sport_type == 'football' ) {
					$round = $game['league']['round'];
					$api_id = $game['fixture']['id'];
				}

				if ( $league->sport_type == 'hockey' ) {
					$round = $game['week'];
					$api_id = 'h' . $game['id'];
				}

				if ( $league->sport_type == 'basketball' ) {
					$round = $game['week'];
					$api_id = 'b' . $game['id'];
				}

				if ( $league->sport_type == 'tennis' ) {
					$round = $game['game']['stage'];
					$api_id = 't' . $game['game']['id'];
				}

				$stage_id = SP_Loader_Functions::getStage( $round );

				if ( ($game_post = SP_Loader_Functions::getPostByApiID( 'sp_event', $api_id )) ) {

					$post_id = $game_post->ID;					
					
					$round_ru = SP_Loader_Functions::translateRound( $round );

					wp_set_object_terms( $post_id, (int)$stage_id, 'sp_stage', false );
					update_post_meta( $post_id, 'sp_day', $round_ru );

					$updated++;

				}

				$league_stages[] = $stage_id;

			}

			$league_stages = array_unique( $league_stages );

			foreach ( $league_stages as $stage_id ) {
				$stage = new SP_SNS_Stage( $stage_id );
				$events_array[] = $stage->ID . '. ' . $stage->name . '<br>';						
			}			

			$all_stages = get_term_meta( $league->ID, 'sp_stages', 1 );
			if ( empty( $all_stages ) || !is_array( $all_stages ) ) {
				$all_stages = [
					$season_id => []
				];
			}

			$all_stages[ $season_id ] = $league_stages;

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