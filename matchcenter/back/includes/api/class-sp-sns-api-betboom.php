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


class SP_SNS_API_BETBOOM extends SP_SNS_API {

	protected $current_bk = 'betboom';
	protected $url        = 'https://feeds.betboom.ru/api/digitainfeeds/v1/get';

	public function __construct() {
		$this->current_bk_id = get_option('sns_bk_api_betboom', false);
		parent::__construct();
		return $this;
	}

	function getFeeds() {
		$feeds = [];

		$request_array = ['id' => 36];
		$request = json_encode($request_array, JSON_UNESCAPED_UNICODE);
		$token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjQwODM2NTA3OTh9.fbvPGi15Oo_jE_KD-MVdcga9et-I1Y6XOJ5hFQP8T_k';

		$feed1 = $this->getCurl( $this->url, $request, false, $token );

		if ( $feed1 ) {
			array_merge( $feeds, $feed1->data->SportList );
		}

		$request_array = ['id' => 4];
		$request = json_encode($request_array, JSON_UNESCAPED_UNICODE);
		$feed2 = $this->getCurl($this->url, $request);

		if ( $feed2 ) {
			array_merge( $feeds, $feed2->data->SportList );
		}

		return $feeds;

	}

	function filterFeeds( $feeds ) {
		$filterFeeds = [];
		foreach ($feeds as $feed) {
			if ( in_array( $feed->RegionList[0]->CompetitionModelsList[0]->Id, $this->league_ids ) && $feed->RegionList[0]->CompetitionModelsList[0]->MatchModelsList[0]->ParentEventId === 0 ) {
				$new_feed = $feed->RegionList[0]->CompetitionModelsList[0]->MatchModelsList[0];
				$new_feed->league = $feed->RegionList[0]->CompetitionModelsList[0]->Name;
				$filterFeeds[] = $new_feed;
			}
		}

		return $filterFeeds; 
		return $feeds;
	}

	function loadBets() {
		$bookmaker = new SP_SNS_Bookmaker( $this->current_bk_id );
		foreach ( $this->feeds as $feed ) {
			$date = substr($feed->Date, 6, -10);
			if ( $event = $this->getEvent( [$feed->Home->Id, $feed->Away->Id], date('Y-m-d', $date ) ) ) {

				$bk_bets = [
					'П1' => 0,
					'X'  => 0,
					'П2' => 0,
					'url' => $bookmaker->link
				];

				foreach ( $feed->StakeModelsList as $stake ) {
					if ( $stake->TypeId === 1 ) {
						foreach ( $stake->OddModelsList as $odd ) {
							if ( $odd->Type == 'Исход П1' ) {
								$bk_bets['П1'] = round( $odd->Odd, 2 );
							}
							if ( $odd->Type == 'Исход П2' ) {
								$bk_bets['П2'] = round( $odd->Odd, 2 );
							}
							if ( $odd->Type == 'Исход X' ) {
								$bk_bets['X'] = round( $odd->Odd, 2 );
							}														
						}
					}
				}
				
				$this->saveEventBets( $event->ID, $bk_bets, $this->current_bk_id, $feed->Id );
			}
		}
	}

	public function getTeams() {
		$teams = [];
		foreach ( $this->feeds as $feed ) {
			$teams[] = [
				'league' => $feed->league,
				'name' => $feed->Home->Name,
				'id'   => $feed->Home->Id,
			];
			$teams[] = [
				'league' => $feed->league,
				'name' => $feed->Away->Name,
				'id'   => $feed->Away->Id,
			];			
		}
		return $teams;
	}	

}

