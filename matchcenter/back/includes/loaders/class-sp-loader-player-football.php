<?php
/**
 * SNS Players Football importer - import events from Sports API into SportsPress.
 *
 * @author      Alex Torbeev
 * @category    Admin
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class SP_Loader_Player_Footbal {

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

		if ( ! $season->api_id || ! $league->api_id ) {
			return false;
		}

		$request = 'players?league=' . $league->api_id . '&season=' . $season->api_id;

		$feeds = SP_Loader_Functions::getFeeds($request, 'football');

		$imported = 0;
		$updated = 0;
		$skipped = 0;
		$events_array = [];


		if ($feeds) {
			
			foreach ($feeds as $feed) {
				$info  = $feed['player'];
				$stats = $feed['statistics'];

				$info['position'] = $stats[0]['games']['position'];

				if ( ! $player = SP_Loader_Functions::getPostByApiID( 'sp_player', $info['id'] ) ) {
					$player = SP_Loader_Functions::addPlayerSingle( $info );
					$imported++;
				} else {
					$updated++;
				}

				$position = 5;
				switch ( $info['position'] ) {
					case 'Goalkeeper':
						$position = 1;
						break;
					case 'Defender':
						$position = 2;
						break;
					case 'Midfielder':
						$position = 3;
						break;
					case 'Attacker':
						$position = 4;
						break;																	
				}

				update_post_meta( $player->ID, 'sp_position', $position );

				$player_teams = get_post_meta( $player->ID, 'sp_team', false );

				if ( empty( $player_teams ) || ! is_array( $player_teams ) ) {
					$player_teams = [];
				}

				$statistics = [];

				foreach ( $stats as $stat ) {
					$team = SP_Loader_Functions::getPostByApiID( 'sp_team', $stat['team']['id'] );
					if ( ! $team || $stat['games']['appearences'] == 0 || is_null( $stat['games']['appearences'] ) ) {
						continue;
					}

					$key = 'sp_stat_' . $season_id . '_' . $league_id . '_' . $team->ID;

					$statistics[ $key ]['captain']  = $stat['games']['captain'];
					$statistics[ $key ]['games']    = $stat['games']['appearences'];
					$statistics[ $key ]['minutes']  = $stat['games']['minutes'];
					$statistics[ $key ]['rating']   = round( $stat['games']['rating'], 1);
					$statistics[ $key ]['goals']    = $stat['goals']['total'];
					$statistics[ $key ]['conceded'] = $stat['goals']['conceded'];
					if ( is_null( $stat['goals']['assists'] ) ) {
						$statistics[ $key ]['assists'] = 0;
					} else {
						$statistics[ $key ]['assists'] = $stat['goals']['assists'];
					}
					$statistics[ $key ]['yellow']   = $stat['cards']['yellow'];
					$statistics[ $key ]['red']      = $stat['cards']['red'];

					if ( ! in_array ( $team->ID, $player_teams ) ) {
						$player_teams[] = $team->ID;
						add_post_meta( $player->ID, 'sp_team', $team->ID );
					}

				}

				wp_set_object_terms( $player->ID, (int)$season_id, 'sp_season', true );
				wp_set_object_terms( $player->ID, (int)$league_id, 'sp_league', true );

				foreach ( $statistics as $key => $value ) {
					update_post_meta( $player->ID, $key, $value );
				}

				$events_array[] = $player->post_title . '<br>';
				
			}

			

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