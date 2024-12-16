<?php

class SP_SNS_Cron_Functions {

	static $logs = false;

	public static function cronEvents() {

		//set_time_limit(600);

		$leagues = SP_SNS_Theme::getLeagues();

		$date_from = wp_date( 'Y-m-d', strtotime( '-6 hours' ) );
		$date_to   = wp_date( 'Y-m-d', strtotime( 'now' ) );

		if ( self::$logs ) {
			$file = dirname( ABSPATH ) .'/__cron_check.txt';
			$content = 'cronEvents ' . current_time('mysql') ."\n";
			file_put_contents( $file, $content, FILE_APPEND );
		}

		foreach ( $leagues as $league ) {

			if ( self::$logs ) {
				$content = $league->season->ID . ' - ' . $league->ID . ' - ' . $date_from . ' - ' . $date_to . "\n";
				file_put_contents( $file, $content, FILE_APPEND );
			}

			$data = false;

			if (! $league->api_id || ! $league->isCron() ) {
				continue;
			}

			if ( $league->sport_type == 'football' ) {
				$data = SP_Loader_Event_Footbal::import( $league->season->ID, $league->ID, $date_from, $date_to );
			} 

			if ( $league->sport_type == 'hockey' ) {	
				$data = SP_Loader_Event_Hockey::import( $league->season->ID, $league->ID, $date_from, $date_to );
			}

			if ( $league->sport_type == 'basketball' ) {	
				$data = SP_Loader_Event_Basketball::import( $league->season->ID, $league->ID, $date_from, $date_to );
			}

			if ( $league->sport_type == 'tennis' ) {	
				$data = SP_Loader_Event_Tennis::import( $league->season->ID, $league->ID, $date_from, $date_to );
			}						

			if ( self::$logs ) {
				
				if ( $data ) {
					$content = $data['request'] . "\n";
					foreach ( $data['events'] as $event ) {
						$content .= $event . "\n";
					}
					file_put_contents( $file, $content, FILE_APPEND );
				}
				
			}

		}		

	}

	public static function cronFixtures() {

		$leagues = SP_SNS_Theme::getLeagues();

		$date_from = wp_date( 'Y-m-d', strtotime( '-2 days' ) );
		$date_to   = wp_date( 'Y-m-d', strtotime( 'now' ) );

		foreach ( $leagues as $league ) {

			if (! $league->api_id || ! $league->isCron() ) {
				continue;				
			}

			if ( $league->sport_type == 'football' ) {
				$data = SP_Loader_Fixture_Footbal::import( $league->season->ID, $league->ID, $date_from, $date_to );
			} 

			if ( self::$logs ) {
				$file = dirname( ABSPATH ) .'/__cron_check.txt';
				$content = 'cronFixtures ' . current_time('mysql') ."\n";
				$content .= $league->season->ID . ' - ' . $league->ID . ' - ' . $date_from . ' - ' . $date_to . "\n";
				if ( $data ) {
					$content .= $data['request'] . "\n";
					foreach ( $data['events'] as $event ) {
						$content .= $event . "\n";
					}
				}
				file_put_contents( $file, $content, FILE_APPEND );
			}

		}

	}

	public static function cronCoef() {	
		new SP_SNS_API_WINLINE();
		new SP_SNS_API_FONBET();
		new SP_SNS_API_BETBOOM();

		if ( self::$logs ) {
			$file = dirname( ABSPATH ) .'/__cron_check.txt';
			$content = 'cronCoef ' . current_time('mysql') ."\n";
			file_put_contents( $file, $content, FILE_APPEND );		
		}
	}

	public static function cronLeague() {

		$leagues = SP_SNS_Theme::getLeagues();

		$date    = wp_date( 'Y-m-d', strtotime( '+1 days' ) );
		$date_to = wp_date( 'Y-m-d', strtotime( '+14 days' ) );

		foreach ( $leagues as $league ) {

			if ( ! $league->api_id || ! $league->isCron() ) {
				continue;				
			}

			if ( $league->sport_type == 'football' ) {
				$data = SP_Loader_Event_Footbal::import( $league->season->ID, $league->ID, $date, $date_to );
			} 

			if ( $league->sport_type == 'hockey' ) {	
				$data = SP_Loader_Event_Hockey::import( $league->season->ID, $league->ID, $date, $date_to );
			}

			if ( $league->sport_type == 'basketball' ) {	
				$data = SP_Loader_Event_Basketball::import( $league->season->ID, $league->ID, $date_from, $date_to );
			}

			if ( $league->sport_type == 'tennis' ) {	
				$data = SP_Loader_Event_Tennis::import( $league->season->ID, $league->ID, $date_from, $date_to );
			}			

		}

		if ( self::$logs ) {
			$file = dirname( ABSPATH ) .'/__cron_check.txt';
			$content = 'cronLeague ' . current_time('mysql') ."\n";
			file_put_contents( $file, $content, FILE_APPEND );		
		}

	}

	public static function cronTransfers() {

		$leagues = SP_SNS_Theme::getLeagues();

		$date    = wp_date( 'Y-m-d', strtotime( '-10 days' ) );
		$date_to = wp_date( 'Y-m-d', strtotime( '+10 days' ) );

		foreach ( $leagues as $league ) {

			if ( ! $league->api_id || ! $league->has_transfers ) {
				continue;
			}

			if ( $sport_type == 'football' ) {
				$data = SP_Loader_Transfer_Footbal::import( $league->season->ID, $league->ID, $date, $date_to );
			} 

		}

		if ( self::$logs ) {
			$file = dirname( ABSPATH ) .'/__cron_check.txt';
			$content = 'cronTransfers ' . current_time('mysql') ."\n";
			file_put_contents( $file, $content, FILE_APPEND );	
		}	

	}

	public static function cronSquad() {

		$leagues = SP_SNS_Theme::getLeagues();

		foreach ( $leagues as $league ) {

			if ( ! $league->api_id || ! $league->isCron() ) {
				continue;				
			}

			if ( $league->sport_type == 'football' ) {
				if ( get_term_meta( $league->ID, 'sp_update_squad', 1 ) == 'yes' ) {
					$data = SP_Loader_Player_Footbal::import( $league->season->ID, $league->ID );
				}
			} 
	

		}

		if ( self::$logs ) {
			$file = dirname( ABSPATH ) .'/__cron_check.txt';
			$content = 'cronLeague ' . current_time('mysql') ."\n";
			file_put_contents( $file, $content, FILE_APPEND );		
		}

	}	

} 