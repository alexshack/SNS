<?php
/**
 * SNS SportsPress API
 *
 * @author      Alex Torbeev
 * @category    Admin
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class SP_SNS_API_FONBET extends SP_SNS_API {

	protected $current_bk = 'fonbet';
	protected $url        = 'https://feed.ajaxfeed.com/feeds/entmedia.json';

	public function __construct() {
		$this->current_bk_id = get_option('sns_bk_api_fonbet', false);
		parent::__construct();
		return $this;
	}

	function getFeeds() {
		$feeds = $this->getCurl($this->url, false, false);
		return $feeds->event;
	}

	function filterFeeds( $feeds ) {
		$filterFeeds = [];
		foreach ($feeds as $feed) {
			if ( in_array( $feed->topic_id, $this->league_ids ) ) {
				$filterFeeds[] = $feed;
			}
		}

		return $filterFeeds;
	}

	function loadBets() {
		$bookmaker = new SP_SNS_Bookmaker( $this->current_bk_id );
		foreach ( $this->feeds as $feed ) {
			if ( $event = $this->getEvent( [$feed->team1_id, $feed->team2_id], date('Y-m-d', strtotime( $feed->start_date2 ) ) ) ) {
				$bk_bets = [
					'П1'  => round( $feed->outcome_1->{'@attributes'}->factor_value, 2 ),
					'X'   => round( $feed->outcome_2->{'@attributes'}->factor_value, 2 ),
					'П2'  => round( $feed->outcome_3->{'@attributes'}->factor_value, 2 ),
					'url' => $bookmaker->link
				];

				$this->saveEventBets( $event->ID, $bk_bets, $this->current_bk_id, $feed->topic_id );

			}
		}
	}

	public function getTeams() {
		$teams = [];
		foreach ( $this->feeds as $feed ) {
			$teams[] = [
				'league' => $feed->topic,
				'name'   => $feed->team1,
				'id'     => $feed->team1_id,
			];
			$teams[] = [
				'league' => $feed->topic,
				'name'   => $feed->team2,
				'id'     => $feed->team2_id,
			];			
		}
		return $teams;
	}

}

