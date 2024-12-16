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


class SP_SNS_API {

	protected $url;
	protected $current_bk;
	protected $current_bk_id;
	protected $league_ids = [];
	protected $feeds = [];
	protected $sports = [];
	protected $raw_feeds = [];

	public function __construct() {
		if ( $this->current_bk_id ) {
			$this->league_ids = $this->getLeagues();
			$this->raw_feeds  = $this->getFeeds();
			$this->feeds      = $this->filterFeeds( $this->getFeeds() );
			$this->loadBets();
		} else {
			return false;
		}
	}

	public function getSports() {
		return $this->sports;
	}


	public function getBks() {
		return $this->bks;
	}

	function getEvent( $teams, $date ) {
		
		$team_ids = [];

		foreach ( $teams as $team ) {
			$team_args = [
				'post_type'  => 'sp_team',
				'meta_query' => [
					[
						'key'   => $this->current_bk . '_id',
						'value' => $team,
					],
				],
			];

			$team_posts = get_posts( $team_args );

			if ( count( $team_posts ) ) {
				$team_ids[] = $team_posts[0]->ID;
			} else {
				return false;
			}
		}

		$event_args = [
			'post_type'   => 'sp_event',
			'post_status' => ['future', 'publish'],
			'meta_query'  => [
				'relation' => 'and',
				[
					'key'   => 'sp_team',
					'value' => $team_ids[0],
				],
				[
					'key'   => 'sp_team',
					'value' => $team_ids[1],
				],			
			],
			'date_query' => [
				[
					'year'  => date('Y', strtotime($date) ),
					'month' => date('n', strtotime($date) ),
					'day'   => date('j', strtotime($date) ),
				],
			]
		];

		$event_posts = get_posts( $event_args );

		if ( count( $event_posts ) ) {
			return $event_posts[0];
		} else {
			return false;
		}

	}

	function getLeagues() {
		$leagues = get_terms( [
        	'taxonomy' => ['sp_league'],
        	'fields'   => 'ids'
      	] );
      	$league_ids = [];
 
      	foreach ($leagues as $league) {
      		$bk_field = 'api_' . $this->current_bk;
      		$bk_id = get_term_meta( $league, $bk_field, true );
      		if ( !empty( $bk_id ) ) {
      			$league_ids[$league] = $bk_id;
      		}
      	}

	    return $league_ids;
	}

	function getCurl( $url, $request, $is_post = true, $token = false ) {
		$headers = array(
	       "accept: text/plain",
	       "Content-Type: application/json",
	    );    

		if ( $token ) {
			$headers[] = 'Authorization: Bearer ' . $token;
		}

	    $curl = curl_init($url);

	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);	    
	    
	    if ($is_post) {	 
	    	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);   	
	    	curl_setopt($curl, CURLOPT_URL, $url);
	    	curl_setopt($curl, CURLOPT_POST, $is_post);
	    	curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
	    } 
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

	    $responce = curl_exec($curl);
	    curl_close($curl);
	    return json_decode($responce);
	}

	function getContents( $url, $is_xml = true ) {
 

		if ( $file_content = file_get_contents( $url, false ) ) {
			$xmlstring = mb_convert_encoding( $file_content, 'HTML-ENTITIES', "UTF-8" );
			$content = simplexml_load_string( $xmlstring, "SimpleXMLElement", LIBXML_NOCDATA | LIBXML_COMPACT | LIBXML_PARSEHUGE );
			$json    = json_encode( $content );
			return json_decode($json, false);
		}

		return false;	

	}

	function saveEventBets( $event_id, $bk_bets, $bk, $api_id ) {
		$bets = get_post_meta( $event_id, 'bets', 1 );
		if ( empty($bets) ) {
			$bets = [];
		}

		$bets[ $bk ] = $bk_bets;

		update_post_meta( $event_id, $bk . '_id', $api_id );
		update_post_meta( $event_id, 'bets', $bets );
	}

}