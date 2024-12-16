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


class SP_Loader_Event_Titles {

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

		$season = get_term_by('id', $season_id, 'sp_season');
		$feed_season_id = $season->slug;

		$feed_league_id = get_term_meta( $league_id, 'sp_order', true );

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

		$games = SP_Loader_Functions::getGamesForTitles( $season_id, $league_id, $date_start, $date_end );

		foreach ($games as $game) {
			
			$event = new SP_SNS_Event( $game->ID );

			$date_str = wp_date( 'j F Y', strtotime( $game->post_date ) );
			$old_title = $game->post_title;
			$event_title = $event->team_home->post->post_title . ' - ' . $event->team_away->post->post_title . ', ' . $date_str;

			if ( $old_title != $event_title ) {
				wp_update_post( array(
					'ID' => $game->ID,
					'post_title'  => $event_title,
				) );
				$updated++;
				$events_array[] = $event_title . '<br/>';
			} else {
				$skipped++;
			}

		}//end games

		$data = [
			'request'  => '',
			'imported' => $imported,
			'updated'  => $updated,
			'skipped'  => $skipped,
			'events'   => $events_array,
		];

		return $data;
	}


}