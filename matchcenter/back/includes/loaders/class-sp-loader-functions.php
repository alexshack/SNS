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


class SP_Loader_Functions {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */

	static $id_field = 'sns_apisport_id';
	static $countries = array (
	  'Afghanistan' => 'afg',
	  'Anguilla' => 'aia',
	  'Albania' => 'alb',
	  'Algeria' => 'alg',
	  'Andorra' => 'and',
	  'Angola' => 'ang',
	  'Argentina' => 'arg',
	  'Armenia' => 'arm',
	  'Aruba' => 'aru',
	  'American Samoa' => 'asa',
	  'Antigua and Barbuda' => 'atg',
	  'Australia' => 'aus',
	  'Austria' => 'aut',
	  'Azerbaijan' => 'aze',
	  'Bahamas' => 'bah',
	  'Bangladesh' => 'ban',
	  'Burundi' => 'bdi',
	  'Belgium' => 'bel',
	  'Benin' => 'ben',
	  'Bermuda' => 'ber',
	  'Burkina Faso' => 'bfa',
	  'Bahrain' => 'bhr',
	  'Bhutan' => 'bhu',
	  'Bosnia and Herzegovina' => 'bih',
	  'Belarus' => 'blr',
	  'Belize' => 'blz',
	  'Bolivia' => 'bol',
	  'Botswana' => 'bot',
	  'Brazil' => 'bra',
	  'Barbados' => 'brb',
	  'Brunei' => 'bru',
	  'Bulgaria' => 'bul',
	  'Cambodia' => 'cam',
	  'Canada' => 'can',
	  'Cayman Islands' => 'cay',
	  'Congo DR' => 'cgo',
	  'Chad' => 'cha',
	  'Chile' => 'chi',
	  'China' => 'chn',
	  'Côte d\'Ivoire' => 'civ',
	  'Cameroon' => 'cmr',
	  'Congo' => 'cod',
	  'Cook Islands' => 'cok',
	  'Colombia' => 'col',
	  'Comoros' => 'com',
	  'Cape Verde' => 'cpv',
	  'Costa Rica' => 'crc',
	  'Croatia' => 'cro',
	  'Central African Republic' => 'cta',
	  'Cuba' => 'cub',
	  'Curacao' => 'cuw',
	  'Cyprus' => 'cyp',
	  'Czechia' => 'cze',
	  'Denmark' => 'den',
	  'Djibouti' => 'dji',
	  'Dominica' => 'dma',
	  'Dominican Republic' => 'dom',
	  'Ecuador' => 'ecu',
	  'Egypt' => 'egy',
	  'England' => 'eng',
	  'Equatorial Guinea' => 'eqg',
	  'Eritrea' => 'eri',
	  'Western Sahara' => 'esh',
	  'Spain' => 'esp',
	  'Estonia' => 'est',
	  'Ethiopia' => 'eth',
	  'Fiji' => 'fij',
	  'Finland' => 'fin',
	  'France' => 'fra',
	  'Faroe Islands' => 'fro',
	  'French Guiana' => 'guf',
	  'French Polynesia' => 'pyf',
	  'Micronesia' => 'fsm',
	  'Gabon' => 'gab',
	  'Gambia' => 'gam',
	  'United Kingdom' => 'gbr',
	  'Georgia' => 'geo',
	  'Germany' => 'ger',
	  'Ghana' => 'gha',
	  'Gibraltar' => 'gib',
	  'Guadeloupe' => 'glp',
	  'Guinea-Bissau' => 'gnb',
	  'Greece' => 'gre',
	  'Greenland' => 'grl',
	  'Grenada' => 'grn',
	  'Guatemala' => 'gua',
	  'Guinea' => 'gui',
	  'Guam' => 'gum',
	  'Guyana' => 'guy',
	  'Haiti' => 'hai',
	  'Hong Kong' => 'hkg',
	  'Honduras' => 'hon',
	  'Hungary' => 'hun',
	  'Indonesia' => 'idn',
	  'India' => 'ind',
	  'Ireland' => 'irl',
	  'Iran' => 'irn',
	  'Iraq' => 'irq',
	  'Iceland' => 'isl',
	  'Israel' => 'isr',
	  'Italy' => 'ita',
	  'Jamaica' => 'jam',
	  'Jordan' => 'jor',
	  'Japan' => 'jpn',
	  'Kazakhstan' => 'kaz',
	  'Kenya' => 'ken',
	  'Kosovo' => 'kos',
	  'Kyrgyzstan' => 'kgz',
	  'Kiribati' => 'kir',
	  'Martinique' => 'mtq',
	  'South Korea' => 'kor',
	  'Saudi Arabia' => 'ksa',
	  'Kuwait' => 'kuw',
	  'Laos' => 'lao',
	  'Liberia' => 'lbr',
	  'Libya' => 'lby',
	  'Saint Lucia' => 'lca',
	  'Lesotho' => 'les',
	  'Lebanon' => 'lib',
	  'Liechtenstein' => 'lie',
	  'Lithuania' => 'ltu',
	  'Luxembourg' => 'lux',
	  'Latvia' => 'lva',
	  'Macau' => 'mac',
	  'Madagascar' => 'mad',
	  'Morocco' => 'mar',
	  'Malaysia' => 'mas',
	  'Monaco' => 'mco',
	  'Moldova' => 'mda',
	  'Maldives' => 'mdv',
	  'Mexico' => 'mex',
	  'Marshall Islands' => 'mhl',
	  'North Macedonia' => 'mkd',
	  'Mali' => 'mli',
	  'Malta' => 'mlt',
	  'Montenegro' => 'mne',
	  'Mongolia' => 'mng',
	  'Mozambique' => 'moz',
	  'Mauritius' => 'mri',
	  'Montserrat' => 'msr',
	  'Mauritania' => 'mtn',
	  'Malawi' => 'mwi',
	  'Myanmar' => 'mya',
	  'Namibia' => 'nam',
	  'Nicaragua' => 'nca',
	  'New Caledonia' => 'ncl',
	  'Netherlands' => 'ned',
	  'Nepal' => 'nep',
	  'Nigeria' => 'nga',
	  'Niger' => 'nig',
	  'Northern Ireland' => 'nir',
	  'Norway' => 'nor',
	  'Nauru' => 'nru',
	  'New Zealand' => 'nzl',
	  'Oman' => 'oma',
	  'Pakistan' => 'pak',
	  'Panama' => 'pan',
	  'Paraguay' => 'par',
	  'Peru' => 'per',
	  'Philippines' => 'phi',
	  'Palestine' => 'ple',
	  'Palau' => 'plw',
	  'Papua New Guinea' => 'png',
	  'Poland' => 'pol',
	  'Portugal' => 'por',
	  'North Korea' => 'prk',
	  'Puerto Rico' => 'pur',
	  'Qatar' => 'qat',
	  'Romania' => 'rou',
	  'South Africa' => 'rsa',
	  'Reunion' => 'reu',
	  'Russia' => 'rus',
	  'Rwanda' => 'rwa',
	  'Samoa' => 'sam',
	  'Scotland' => 'sco',
	  'Sudan' => 'sdn',
	  'Senegal' => 'sen',
	  'Seychelles' => 'sey',
	  'Singapore' => 'sin',
	  'Saint Kitts and Nevis' => 'skn',
	  'Sierra Leone' => 'sle',
	  'El Salvador' => 'slv',
	  'San Marino' => 'smr',
	  'Solomon Islands' => 'sol',
	  'Somalia' => 'som',
	  'Serbia' => 'srb',
	  'Sri Lanka' => 'sri',
	  'South Sudan' => 'ssd',
	  'Sao Tome and Principe' => 'stp',
	  'Switzerland' => 'sui',
	  'Suriname' => 'sur',
	  'Slovakia' => 'svk',
	  'Slovenia' => 'svn',
	  'Sweden' => 'swe',
	  'Eswatini' => 'swz',
	  'Sint Maarten' => 'sxm',
	  'Syria' => 'syr',
	  'Tahiti' => 'tah',
	  'Tanzania' => 'tan',
	  'Turks and Caicos Islands' => 'tca',
	  'Tonga' => 'tga',
	  'Thailand' => 'tha',
	  'Tajikistan' => 'tjk',
	  'Turkmenistan' => 'tkm',
	  'East Timor' => 'tls',
	  'Togo' => 'tog',
	  'Taiwan' => 'tpe',
	  'Trinidad and Tobago' => 'tri',
	  'Tunisia' => 'tun',
	  'Turkey' => 'tur',
	  'Tuvalu' => 'tuv',
	  'United Arab Emirates' => 'uae',
	  'Uganda' => 'uga',
	  'Ukraine' => 'ukr',
	  'Uruguay' => 'uru',
	  'United States' => 'usa',
	  'Uzbekistan' => 'uzb',
	  'Vanuatu' => 'van',
	  'Vatican City' => 'vat',
	  'Venezuela' => 'ven',
	  'British Virgin Islands' => 'vgb',
	  'Vietnam' => 'vie',
	  'Saint Vincent and the Grenadines' => 'vin',
	  'US Virgin Islands' => 'vir',
	  'Wales' => 'wal',
	  'West Indies' => 'wif',
	  'Yemen' => 'yem',
	  'Zambia' => 'zam',
	  'Zimbabwe' => 'zim',
	);

	static function init() {

	}


	public static function getPostByApiID($post_type = false, $api_id = false) {
		
		if (! $api_id || ! $post_type ) {
			return false;
		}

		$sp_post_args = [
			'post_type'   => $post_type,
			'post_status' => ['publish', 'future'],
			'suppress_filters' => true,
			'posts_per_page' => 1,
			'cache_results' => false,
			'update_post_meta_cache' => false,
			'meta_query' => [
				[
					'key'   => self::$id_field,
					'value' => $api_id,
				],
			]
		];

		$sp_posts_query = new WP_Query;

		$sp_posts = $sp_posts_query->query( $sp_post_args );
		
		if ( $sp_posts ) {
			return $sp_posts[0];
		} 

		return false;
	}

	public static function getTermByApiID($taxonomy = false, $api_id = false) {
		
		if (! $api_id || ! $taxonomy ) {
			return false;
		}

		$term_args = [
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'number'     => 1,
			'meta_query' => [
				[
					'key'   => self::$id_field,
					'value' => $api_id,
				],
			]			
		];
		$terms = get_terms($term_args);
		
		if ($terms) {
			return $terms[0];
		} 

		return false;
	}

	public static function addVenue($venue) {
		
		if (! count($venue) ) {
			return false;
		}

		$args = [
			'slug' => sanitize_title( $venue['name'] ),
		];
		$term = wp_insert_term($venue['name'], 'sp_venue', $args );

		if ( is_wp_error( $term )) {
			return false;
		}
		$term_id = $term['term_id'];

		update_term_meta( $term_id, self::$id_field, $venue['id'] );
		update_term_meta( $term_id, 'sp_address', $venue['address'] );
		update_term_meta( $term_id, 'sp_city', $venue['city'] );
		update_term_meta( $term_id, 'sp_capacity', $venue['capacity'] );
		update_term_meta( $term_id, 'sp_surface', $venue['surface'] );	

		if ( $venue['image'] ) {
			$logo_id = media_sideload_image( $venue['image'], 0, $venue['name'], 'id');
			update_term_meta( $term_id, '_thumbnail_id', $logo_id );
		}

		return get_term($term_id);

	}

	public static function addTeam($team, $league_id = false, $season_id = false) {
		
		if (! count( $team ) ) {
			return false;
		}

		$args = [
			'post_type'   => 'sp_team',
			'post_status' => 'publish',
			'post_title'  => $team['name'],
		];
		$post_id = wp_insert_post( $args );

		update_post_meta( $post_id, self::$id_field, $team['id'] );		

		if ( isset( $team['logo'] ) && !empty( $team['logo'] ) ) {
			$logo_id = media_sideload_image( $team['logo'], $post_id, $team['name'], 'id');
			set_post_thumbnail( $post_id, $logo_id );
		}

		if ( isset( $team['venue'] ) && !empty( $team['venue'] ) ) {
			$venue = self::getTermByApiID('sp_venue', $team['venue']['id']);
						
			if (! $venue ) {
				$venue = self::addVenue($team['venue']);
			}

			wp_set_object_terms( $post_id, $venue->term_id, 'sp_venue', false );			
		}		

		if ( $league_id ) {
			wp_set_object_terms( $post_id, (int)$league_id, 'sp_league', true );
		}

		if ( $season_id ) {
			wp_set_object_terms( $post_id, (int)$season_id, 'sp_season', true );
		}		

		update_post_meta( $post_id, '_sp_import', 1 );
		
		return get_post($post_id);

	}


	public static function addPlayer($player, $league_id = false, $season_id = false, $teams = false, $current_team = false, $past_team = false) {
		
		if (! count($player) ) {
			return false;
		}

		$metrics = [
			'height' => (int)$player['height'],
			'weight' => (int)$player['weight']
		];

		$args = [
			'post_type'   => 'sp_player',
			'post_status' => 'publish',
			'post_title'  => $player['lastname'] . ' ' . $player['firstname'],
			'post_date'   => $player['birth']['date'],
		];
		$post_id = wp_insert_post( $args );

		update_post_meta( $post_id, self::$id_field, $player['id'] );
		update_post_meta( $post_id, 'sp_nationality', self::$countries[$player['nationality']] );
		update_post_meta( $post_id, 'sp_metrics', $metrics );

		if ( $player['photo'] ) {
			$logo_id = media_sideload_image( $player['photo'], $post_id, $player['name'], 'id');
			set_post_thumbnail( $post_id, $logo_id );
		}

		if ( $player['position'] ) {
			wp_set_object_terms( $post_id, $player['position'], 'sp_position', false );
		}		

		if ( $league_id ) {
			wp_set_object_terms( $post_id, (int)$league_id, 'sp_league', true );
		}

		if ( $season_id ) {
			wp_set_object_terms( $post_id, (int)$season_id, 'sp_season', true );
		}

		if ( $teams && is_array($teams) ) {
			foreach ($teams as $team ) {
				add_post_meta( $post_id, 'sp_team', $team );
			}
		}

		if ( $current_team ) {
			update_post_meta( $post_id, 'sp_current_team', $current_team );
		}

		if ( $past_team ) {
			update_post_meta( $post_id, 'sp_past_team', $past_team );
		}			

		return get_post($post_id);

	}

	public static function addPlayerSingle ( $info ) {
		
		$args = [
			'post_type'   => 'sp_player',
			'post_status' => 'publish',
			'post_title'  => $info['name'],
			'post_date'   => $info['birth']['date'],
		];

		$post_id = wp_insert_post( $args );

		$metrics = [
			'height' => (int)$info['height'],
			'weight' => (int)$info['weight']
		];

		update_post_meta( $post_id, self::$id_field, $info['id'] );
		update_post_meta( $post_id, 'sp_nationality', self::$countries[$info['nationality']] );
		update_post_meta( $post_id, 'sp_injured', $info['injured'] );
		update_post_meta( $post_id, 'sp_metrics', $metrics );

		if ( $info['position'] ) {
			wp_set_object_terms( $post_id, $info['position'], 'sp_position', false );
		}
		
		if ( $info['photo'] ) {
			$logo_id = media_sideload_image( $info['photo'], $post_id, $info['name'], 'id');
			set_post_thumbnail( $post_id, $logo_id );
		}


	}

	public static function setPlayer($player_api, $season_slug, $season_id, $team_id, $league_id) {
		if ( $player_post = self::getPostByApiID('sp_player', $player_api) ) {
			
			$post_id = $player_post->ID;
			$current_team = get_post_meta( $post_id, 'sp_current_team', true );
			$player_teams = get_post_meta( $post_id, 'sp_team', false );

			if (! in_array($team_id, $player_teams)) {
				add_post_meta( $post_id, 'sp_team', $team_id );
			}

			$is_national = get_term_meta( $league_id, 'is_national', true );

			if ($is_national != 'yes') {
				if ($current_team != $team_id) {
					if ($current_team) {
						delete_post_meta( $post_id, 'sp_current_team' );
						add_post_meta( $post_id, 'sp_past_team', $current_team );
					} 
					update_post_meta( $post_id, 'sp_current_team', $team_id );
				}	
			}		
			wp_set_object_terms( $post_id, (int)$league_id, 'sp_league', true );
			wp_set_object_terms( $post_id, (int)$season_id, 'sp_season', true );
			

		} else {

			$request = 'players?id=' . $player_api . '&season=' . $season_slug;
			$feed    = self::getFeeds($request)[0];
			$player  = $feed['player'];

			$metrics = [
				'height' => (int)$player['height'],
				'weight' => (int)$player['weight']
			];			

			$args = [
				'post_type'   => 'sp_player',
				'post_status' => 'publish',
				'post_title'  => $player['lastname'] . ' ' . $player['firstname'],
				'post_date'   => $player['birth']['date'],
			];
			$post_id = wp_insert_post( $args );
			
			update_post_meta( $post_id, self::$id_field, $player_api );

			add_post_meta( $post_id, 'sp_team', $team_id );

			$is_national = get_term_meta( $league_id, 'is_national', true );
			if ($is_national != 'yes') {
				update_post_meta( $post_id, 'sp_current_team', $team_id );
			}
			
			update_post_meta( $post_id, 'sp_nationality', $player['nationality'] );
			update_post_meta( $post_id, 'sp_metrics', $metrics );

			wp_set_object_terms( $post_id, (int)$league_id, 'sp_league', true );
			wp_set_object_terms( $post_id, (int)$season_id, 'sp_season', true );
			wp_set_object_terms( $post_id,  mb_strtolower($feed['statistics'][0]['games']['position']), 'sp_position', false );

			if ( $player['photo'] ) {
				$logo_id = media_sideload_image( $player['photo'], $post_id, $player['name'], 'id');
				set_post_thumbnail( $post_id, $logo_id );
			}	

			update_post_meta( $post_id, '_sp_import', 1 );

		}

		return $post_id;
	}


	public static function getStatistics($event_id, $results) {
		$fixture_request = 'fixtures?id=' . $event_id;
		$fixture_feeds = self::getFeeds($fixture_request);

		if ($fixture_feeds) {

			$fixture_feed = $fixture_feeds[0];
			$fixture_statistics = $fixture_feed['statistics'];

			foreach ($fixture_statistics as $statistic) {
				$team_post = self::getPostByApiID('sp_team', $statistic['team']['id']);
				$stats = $statistic['statistics'];
				foreach($stats as $stat) {
					$stat_type = mb_strtolower($stat['type']);
					$stat_type = str_replace(' ', '', $stat_type);
					$results[$team_post->ID][$stat_type] = $stat['value'];
				}
			}								
		}
		return $results;	
	}

	public static function getTransfer( $transfer, $player_id = false ) {
		
		if ( ! count($transfer) || ! $player_id ) {
			return false;
		}

		$date     = getdate(strtotime($transfer['date']));
		$team_in  = self::getPostByApiID('sp_team', $transfer['teams']['in']['id']);
		$team_out = self::getPostByApiID('sp_team', $transfer['teams']['out']['id']);

		if (! $team_in || ! $team_out) {
			return false;
		}


		$post_args = [
			'post_type'  => 'sp_transfer',
			'post_status' => ['future', 'publish'],
			'numberposts' => 1,
			'date_query' => [
				'year'     => $date['year'],
				'monthnum' => $date['mon'],
				'day'      => $date['mday'],
			], 
			'meta_query' => [
				'relation' => 'AND',
				[
					'key'   => 'sp_team_in',
					'value' => $team_in->ID,
				],
				[
					'key'   => 'sp_team_out',
					'value' => $team_out->ID,
				],
				[
					'key'   => 'sp_player',
					'value' => $player_id,
				],								
			],
		];

		$posts_query = new WP_Query;

		$posts = $posts_query->query($post_args);
		
		if ($posts) {
			return $posts[0];
		} 

		return false;

	}


	public static function getGames( $season_id, $league_id, $date_from = false, $date_to = false ) {
		
		if (! $season_id || ! $league_id ) {
			return false;
		}

		$post_args = [
			'post_type'  => 'sp_event',
			'post_status' => 'publish',
			'tax_query' => [
				'relation' => 'AND',
				[
					'taxonomy' => 'sp_season',
					'field'    => 'id',
					'terms'    => $season_id,
				],
				[
					'taxonomy' => 'sp_league',
					'field'    => 'id',
					'terms'    => $league_id,
				],
			],
			'meta_query' => [
				'relation' => 'AND',
				[
					'key'   => 'sp_finished',
					'value' => 'yes',
				],
				[
					'key'   => 'sns_fixture_loaded',
					'compare_key' => 'NOT EXISTS',
				],								
			]
		];

		if ( $date_from && $date_to ) {
			$post_args['date_query'][] = [
				'after' => $date_from,
				'before' => $date_to,
				'inclusive' => true,
			];
		}

		if ( $date_from && ! $date_to ) {
			$post_args['date_query'][] = [
				'after' => $date_from,
				'before' => $date_from,
				'inclusive' => true,
			];
		}

		$posts_query = new WP_Query;

		$posts = $posts_query->query($post_args);
		
		if ($posts) {
			return $posts;
		} 

		return false;

	}

	public static function getGamesForTitles( $season_id, $league_id, $date_from = false, $date_to = false ) {
		
		if (! $season_id || ! $league_id ) {
			return false;
		}

		$post_args = [
			'post_type'  => 'sp_event',
			'posts_per_page' => -1,
			'post_status' => ['publish', 'future'],
			'tax_query' => [
				'relation' => 'AND',
				[
					'taxonomy' => 'sp_season',
					'field'    => 'id',
					'terms'    => $season_id,
				],
				[
					'taxonomy' => 'sp_league',
					'field'    => 'id',
					'terms'    => $league_id,
				],
			],
		];

		if ( $date_from && $date_to ) {
			$post_args['date_query'][] = [
				'after' => $date_from,
				'before' => $date_to,
				'inclusive' => true,
			];
		}

		if ( $date_from && ! $date_to ) {
			$post_args['date_query'][] = [
				'after' => $date_from,
				'before' => $date_from,
				'inclusive' => true,
			];
		}

		$posts_query = new WP_Query;

		$posts = $posts_query->query($post_args);
		
		if ($posts) {
			return $posts;
		} 

		return false;

	}

	public static function translateRound( $round ) {

		if ( empty( $round ) ) {
			$round = 'Регулярный чемпионат';
		}

		$round = str_replace('Regular Season - ', 'Тур ', $round);
		$round = str_replace('Premier League Path - ', 'Путь РПЛ. ', $round);
		$round = str_replace('Group Stage - ', 'Групповой этап. Тур ', $round);
		$round = str_replace('Regions Path - ', 'Путь регионов. ', $round);
		$round = str_replace('1st', '1', $round);
		$round = str_replace('2nd', '2', $round);
		$round = str_replace('3rd', '3', $round);
		$round = str_replace('Round of 16', '1/8 финала', $round);
		$round = str_replace('Round of 8', '1/4 финала', $round);
		$round = str_replace('Round', 'этап', $round);
		$round = str_replace('Preliminary', 'Предв.', $round);
		$round = str_replace('Qualifying', 'Квалиф.', $round);
		$round = str_replace('Play-offs', 'Плей-офф', $round);
		$round = str_replace('Group', 'Группа', $round);
		$round = str_replace('Quarter-finals', '1/4 финала', $round);
		$round = str_replace('Semi-finals', 'Полуфинал', $round);
		$round = str_replace('Final', 'Финал', $round);


		return $round;
	}

	public static function getStage( $round ) {

		if ( empty( $round ) ) {
			$round = 'Регулярный чемпионат';
		}

		if ( $stage = get_term_by('name', $round, 'sp_stage' ) ) {
			return $stage->term_id;
		} else {
			$stage = wp_insert_term( $round, 'sp_stage', array() );
			if ( is_wp_error( $stage ) ) {
				return false;
			}
			update_term_meta( $stage['term_id'], 'rus_name', self::translateRound( $round ) );
			return $stage['term_id'];
		}

	}

	public static function getFeeds( $request, $sport_type = false ) {

		if (! $request) {
			return false;
		}


		$feed = self::getCurl( $request, $sport_type );


/*		if ( count($feed['errors']) || $feed['results'] == 0 ) {
			
			$file = WP_CONTENT_DIR . '/api_feeds.log';
			$content = json_encode($feed) . "\n";
			file_put_contents( $file, $content, FILE_APPEND );	
			return false;
		}*/

		$feeds = $feed['response'];

		if (isset($feed['paging']['total']) && $feed['paging']['total'] > 1) {

			$count = $feed['paging']['total'];

			for ($i = 2; $i <= $count; $i++) {
				$page_request = $request . '&page=' . $i;
				$page_feed = self::getCurl($page_request, $sport_type);
				$feeds = array_merge($feeds, $page_feed['response']);
			}
		}

		return $feeds;


	}
	

	public static function getCurl( $request, $sport_type = false ) {

	    $key  = '';

		$url  = 'https://v3.football.api-sports.io/' . $request;
		$host = 'v3.football.api-sports.io';
		$x_key = 'x-rapidapi-key: ' . $key;
		$x_host = 'x-rapidapi-host: ' . $host;


	    if ( $sport_type == 'football' ) {
	    	$url  = 'https://v3.football.api-sports.io/' . $request;
	    	$host = 'v3.football.api-sports.io';
			$x_key = 'x-rapidapi-key: ' . $key;
			$x_host = 'x-rapidapi-host: ' . $host;	    	
	    }

	    if ( $sport_type == 'hockey' ) {
	    	$url  = 'https://v1.hockey.api-sports.io/' . $request;
	    	$host = 'v1.hockey.api-sports.io';
			$x_key = 'x-rapidapi-key: ' . $key;
			$x_host = 'x-rapidapi-host: ' . $host;	    	
	    }

	    if ( $sport_type == 'basketball' ) {
	    	$url  = 'https://v1.basketball.api-sports.io/' . $request;
	    	$host = 'v1.basketball.api-sports.io';
			$x_key = 'x-rapidapi-key: ' . $key;
			$x_host = 'x-rapidapi-host: ' . $host;	    	
	    }

	    if ( $sport_type == 'tennis' ) {
	    	$url  = 'https://beta.api-sports.io/tennis/' . $request;
	    	$host = 'beta.api-sports.io';
			$x_key = 'x-apisports-key: ' . $key;
			//$x_host = 'x-apisports-host: ' . $host;
			$x_host = '';   	
	    }	    

	    $curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_HTTPHEADER => array(
		    $x_key,
		    $x_host
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
	    return json_decode($response, true);
	}



}

