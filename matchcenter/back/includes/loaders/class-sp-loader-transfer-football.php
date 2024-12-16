<?php
/**
 * SNS Event Football importer - import events from Sports API into SportsPress.
 *
 * @author      Alex Torbeev
 * @category    Admin
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class SP_Loader_Transfer_Footbal {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	static function init( ) {

	}

	static function import( $season_id, $league_id, $date_start, $date_end ) {

		if ( ! $season_id || ! $league_id || ! $date_start || ! $date_end ) {
			return false;
		}

		$season = new SP_SNS_Season( $season_id );
		$league = new SP_SNS_League( $league_id );

		if ( ! $season->api_id || ! $league->api_id ) {
			return false;
		}

		$feed_league_id = $league->api_id;
		$feed_season_id = $season->api_id;

		$teams = $league->getTeams( $season->ID );

		if(! $teams ) {
			return false;
		}

		$imported = 0;
		$updated = 0;
		$skipped = 0;
		$events_array = [];

		foreach ($teams as $team) {

			$feed_team_id = get_post_meta( $team->ID, SP_Loader_Functions::$id_field, true );

			if (! $feed_team_id ) {
				continue;					
			}				

			$request = 'transfers?team=' . $feed_team_id;

			$players = SP_Loader_Functions::getFeeds($request);

			if ($players) {
				
				foreach ($players as $player) {

					$transfers = $player['transfers'];

					foreach ($transfers as $transfer) {

						if (strtotime($transfer['date']) < $date_start || strtotime($transfer['date']) > $date_end ) {
							$skipped++;
							continue;
						}

						$team_in  = SP_Loader_Functions::getPostByApiID('sp_team', $transfer['teams']['in']['id']);
						$team_out = SP_Loader_Functions::getPostByApiID('sp_team', $transfer['teams']['out']['id']);

						if (! $team_in ) {
							$team_in = SP_Loader_Functions::addTeam( $transfer['teams']['in'] );
						}
						if (! $team_out ) {
							$team_out = SP_Loader_Functions::addTeam( $transfer['teams']['out'] );
						}

						$player_post = SP_Loader_Functions::getPostByApiID('sp_player', $player['player']['id']);

						if (! $player_post )  {

							$request = 'players?id=' . $player['player']['id'] . '&season=' . $season->api_id;
							$player_response = SP_Loader_Functions::getFeeds($request);

							$player_feeds = $player_response[0];

							if ($player_feeds) {
								$player_feed = $player_feeds['player'];
								$player_feed['position'] = mb_strtolower($player_feeds['statistics'][0]['games']['position']);
								$player_feed['nationality'] = SP_Loader_Functions::$countries[$player_feeds['player']['nationality']];

								if (! is_array($player_feed)) {
									continue;
								}

								if ( strtotime('now') > strtotime($transfer['date']) ) {
									$current_team = $team_in->ID;
									$past_team    = $team_out->ID;
								} else {
									$current_team = $team_out->ID;
									$past_team    = false;
								}

								$player_post = SP_Loader_Functions::addPlayer($player_feed, $league->ID, $season->ID, [$team_in->ID, $team_out->ID], $current_team, $past_team);
							}

						}

						if ( SP_Loader_Functions::getTransfer($transfer, $player_post->ID) ) {
							$skipped++;
							continue;
						}

						$summ = '';

						switch ($transfer['type']) {
						    case 'N/A':
						        $type = 'back';
						        break;
						    case 'Loan':
						        $type = 'loan';
						        break;
						    case 'Free':
						        $type = 'free';
						        break;
						    case 'Swap':
						        $type = 'swap';
						        break;								        
						    default:
						    	$type = 'sale';
						    	$summ = $transfer['type'];
						};

						$transfer_term = get_term_by('slug', $type, 'sp_transfer_type');

						$args = [
							'post_type'   => 'sp_transfer',
							'post_status' => 'publish',
							'post_title'  => $transfer_term->name . ' ' . $player_post->post_title,
							'post_date'   => $transfer['date'],
						];
						$post_id = wp_insert_post( $args );

						update_post_meta( $post_id, 'sp_player', $player_post->ID );
						update_post_meta( $post_id, 'sp_team_in', $team_in->ID );
						update_post_meta( $post_id, 'sp_team_out', $team_out->ID );
						update_post_meta( $post_id, 'sp_summ', $summ );

						wp_set_object_terms( $post_id, $transfer_term->term_id, 'sp_transfer_type', false );
						wp_set_object_terms( $post_id, (int)$league->ID, 'sp_league', false );
						wp_set_object_terms( $post_id, (int)$season->ID, 'sp_season', false );

						$imported++;
						
					} //player->transfers


				} //end players

			} 

		} //end teams

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