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


class SP_Loader_Fixture_Footbal {

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

		$date_parameters = '';

		if ( $date_start && $date_end ) {
			$date_parameters = '&from=' . $date_start . '&to=' . $date_end;
		}

		if ( $date_start && ! $date_end ) {
			$date_parameters = '&date=' . $date_start;
		}

		$imported = 0;
		$updated = 0;
		$skipped = 0;
		$events_array = [];

		$games = SP_Loader_Functions::getGames( $season->ID, $league->ID, $date_start, $date_end );

		foreach ($games as $game) {
			
			$game_id = get_post_meta( $game->ID, SP_Loader_Functions::$id_field, 1 );

			if ( ! $game_id ) {
				continue;
			}

			$request = 'fixtures?id=' . $game_id;

			$feeds = SP_Loader_Functions::getFeeds($request);					

			if ($feeds) {

				$feed = $feeds[0];

				$positions = [
					'G' => get_term_by('slug', 'goalkeeper', 'sp_position')->term_id,
					'D' => get_term_by('slug', 'defender', 'sp_position')->term_id,
					'M' => get_term_by('slug', 'midfielder', 'sp_position')->term_id,
					'F' => get_term_by('slug', 'attacker', 'sp_position')->term_id,
				];

				$sp_players    = [];
				$sp_timeline   = [];
				$substitutions = [];

				$lineups    = $feed['lineups'];
				foreach ($lineups as $lineup) {
					$team_post = SP_Loader_Functions::getPostByApiID('sp_team', $lineup['team']['id']);

					$sp_players[$team_post->ID] = [];
					$sp_players[$team_post->ID][0] = [
						'goals'       => '',
						'assists'     => '',
						'yellowcards' => '',
						'redcards'    => ''
					];							

					$players = $lineup['startXI'];
					foreach ($players as $player) {
						$player_post_id = SP_Loader_Functions::setPlayer($player['player']['id'], $season->slug, $season->term_id, $team_post->ID, $league->ID);

						$sp_players[$team_post->ID][$player_post_id] = [
							'number' => $player['player']['number'],
							'position' => [
								$positions[$player['player']['pos']]
							],
							'goals'       => '',
							'assists'     => '',
							'yellowcards' => '',
							'redcards'    => '',
							'status'      => 'lineup',
							'sub'         => ''
						];
					}

					$substitutions[$team_post->ID] = [];
					$players = $lineup['substitutes'];
					foreach ($players as $player) {
						$player_post_id = SP_Loader_Functions::setPlayer($player['player']['id'], $season->slug, $season->term_id, $team_post->ID, $league->ID);

						$substitutions[$team_post->ID][$player_post_id] = [
							'number' => $player['player']['number'],
							'position' => [
								$positions[$player['player']['pos']]
							],
							'goals'       => '',
							'assists'     => '',
							'yellowcards' => '',
							'redcards'    => '',
							'status'      => 'sub',
							'sub'         => ''
						];
					}
				} //конец составы
				$events = $feed['events'];
				foreach ($events as $event) {
					$team_post   = SP_Loader_Functions::getPostByApiID('sp_team', $event['team']['id']);
					$player_post = SP_Loader_Functions::getPostByApiID('sp_player', $event['player']['id']);

					switch ($event['type']) {
						case 'Card':
							if ($event['detail'] == 'Yellow Card') $event_type = 'yellowcards';
							if ($event['detail'] == 'Red Card') $event_type = 'redcards';
							$sp_players[$team_post->ID][0][$event_type]++;
							$sp_players[$team_post->ID][$player_post->ID][$event_type]++;
							$sp_timeline[$team_post->ID][$player_post->ID][$event_type][] = $event['time']['elapsed'];
							break;

						case 'Goal':
							$event_type = 'goals';
							$sp_players[$team_post->ID][0][$event_type]++;
							$sp_players[$team_post->ID][$player_post->ID][$event_type]++;
							$sp_timeline[$team_post->ID][$player_post->ID][$event_type][] = $event['time']['elapsed'];
							if (!empty($event['assist']['id'])) {
								$assist_post = SP_Loader_Functions::getPostByApiID('sp_player', $event['assist']['id']);
								$event_type = 'assists';
								$sp_players[$team_post->ID][0][$event_type]++;
								$sp_players[$team_post->ID][$assist_post->ID][$event_type]++;										
							}									
							break;

						case 'subst':
							$sub_player_post = SP_Loader_Functions::getPostByApiID('sp_player', $event['assist']['id']);
							$sp_players[$team_post->ID][$sub_player_post->ID] = $substitutions[$team_post->ID][$sub_player_post->ID];

							$sp_players[$team_post->ID][$sub_player_post->ID]['sub'] = $player_post->ID;
							$sp_players[$team_post->ID][$player_post->ID]['sub'] = 0;

							$sp_timeline[$team_post->ID][$sub_player_post->ID]['sub'][] = $event['time']['elapsed'];
							$sp_timeline[$team_post->ID][$player_post->ID]['sub'][] = '';
						
							break;

						default:
							
							break;
					}
				} //конец события

				foreach($sp_players as $sp_player) {
					foreach ($sp_player as $k => $player) {
						add_post_meta($game->ID, 'sp_player', $k);
					}
				}
			
				update_post_meta( $game->ID, 'sp_players', $sp_players );
				update_post_meta( $game->ID, 'sp_timeline', $sp_timeline );
				update_post_meta( $game->ID, 'sns_fixture_loaded', 'yes' );

				$imported++;
			}

			$events_array[] = $game->post_title . '<br/>';

		}//end games

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