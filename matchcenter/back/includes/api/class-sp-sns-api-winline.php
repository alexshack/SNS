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


class SP_SNS_API_WINLINE extends SP_SNS_API {

	protected $current_bk    = 'winline';
	protected $url           = 'https://bn.wlbann.com/api/v2/prematch';
	public $bk_leagues       = [];

	public function __construct() {
		$this->current_bk_id = get_option('sns_bk_api_winline', false);
		parent::__construct();
		return $this;
	}

	function getFeeds() {
		$feeds = $this->getContents($this->url, true);
		return $feeds->Sport;
	}

	function filterFeeds( $feeds ) {
		$filterFeeds = [];
		$feed_sports = [ 'Футбол' => [], 'Хоккей' => [], 'Теннис' => [], 'Баскетбол' => [] ];
		foreach ( $feeds as $sport ) {
			if ( in_array($sport->{'@attributes'}->Name, ['Футбол', 'Хоккей', 'Теннис', 'Баскетбол'] ) ) {
				$feed_sport = $sport->{'@attributes'}->Name;
				foreach ( $sport->Country as $country ) {
					if ( is_array($country->Tournament) ) {
						foreach ( $country->Tournament as $tournament ) {
							$feed_sports[$feed_sport][] = [ $country->{'@attributes'}->Name, $tournament->{'@attributes'}->Name, $tournament->{'@attributes'}->Id ];
							if ( in_array($tournament->{'@attributes'}->Id, $this->league_ids ) ) {
								if ( is_array($tournament->Match) ) {
									foreach ( $tournament->Match as $match ) {
										$new_feed = $match;
										$new_feed->league = $tournament->{'@attributes'}->Name;
										$filterFeeds[] = $new_feed;
									}
								} else {
									$new_feed = $tournament->Match;
									$new_feed->league = $tournament->{'@attributes'}->Name;
									$filterFeeds[] = $new_feed;
								}
							}
						}
					} else {
						$feed_sports[$feed_sport][] = [ $country->{'@attributes'}->Name, $country->Tournament->{'@attributes'}->Name, $country->Tournament->{'@attributes'}->Id ];
						if ( in_array($country->Tournament->{'@attributes'}->Id, $this->league_ids ) ) {
							if ( is_array($country->Tournament->Match) ) {
								foreach ( $country->Tournament->Match as $match ) {
									$new_feed = $match;
									$new_feed->league = $country->Tournament->{'@attributes'}->Name;
									$filterFeeds[] = $new_feed;
								}
							} else {
								$new_feed = $country->Tournament->Match;
								$new_feed->league = $country->Tournament->{'@attributes'}->Name;
								$filterFeeds[] = $new_feed;
							}							
						}						
					}
				}
			}
		}
		$this->sports = $feed_sports;

		return $filterFeeds;
	}

	function loadBets() {
		foreach ( $this->feeds as $feed ) {
			if ( $event = $this->getEvent( [$feed->{'@attributes'}->Id1, $feed->{'@attributes'}->Id2], date('Y-m-d', strtotime( $feed->{'@attributes'}->MatchDate ) ) ) ) {
				
				/*$url = $feed->{'@attributes'}->MatchUrl;
				$url = explode( '/', $url );
				$match_id = array_pop( $url );*/
				$match_id = $feed->{'@attributes'}->Id;
				$url = 'https://stavkinasport.com/go/winline-match/?match=' . $match_id;

				if ( $feed->{'@attributes'}->TV ) {
					update_post_meta( $event->ID, 'bk_tv', $url );
				} else {
					delete_post_meta( $event->ID, 'bk_tv' );
				}			

				$bk_bets = [
					'П1'  => round( $feed->line[0]->{'@attributes'}->odd1, 2 ),
					'X'   => round( $feed->line[0]->{'@attributes'}->odd2, 2 ),
					'П2'  => round( $feed->line[0]->{'@attributes'}->odd3, 2 ),
					'url' => $url,
				];

				$this->saveEventBets( $event->ID, $bk_bets, $this->current_bk_id, $feed->{'@attributes'}->Id );

			}
		}		
	}

	public function getTeams() {
		$teams = [];
		foreach ( $this->feeds as $feed ) {
			$teams[] = [
				'league' => $feed->league,
				'name' => $feed->{'@attributes'}->Team1,
				'id'   => $feed->{'@attributes'}->Id1,
			];
			$teams[] = [
				'league' => $feed->league,
				'name' => $feed->{'@attributes'}->Team2,
				'id'   => $feed->{'@attributes'}->Id2,
			];			
		}
		return $teams;
	}


}

