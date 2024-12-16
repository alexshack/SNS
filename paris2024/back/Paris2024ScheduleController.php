<?php
class Paris2024ScheduleController extends PageController {

	public $templatesDir = 'inc/paris2024/templates/';
	protected $schedule = [];
	protected $sports = [];
	protected $medals = [];
	protected $tables = [];
	protected $country_flags = [];
	protected $news_limit = 5;
	protected $medals_enabled = false;

	function setup() {

		$topCountries = [
			'Россия',
			'США',
			'Китай',
			'Великобритания',
			'Германия',
			'Франция'
		];

		$medals_enabled_data = Paris2024Medals::query()->select([
            'country_id',
            'country_name',
            'gold',
            'silver',
            'bronze',
            'amount'
        ])->orderby( 'amount', 'DESC' )->limit(1)->get_results();

		if($medals_enabled_data[0]->amount == 0) {
			$this->medals_enabled = false;
		} else {
			$this->medals_enabled = true;
		}

		if($this->medalsEnabled()) {
			$q = Paris2024Medals::query()->select([
                'country_id',
                'country_name',
                'gold',
                'silver',
                'bronze',
                'amount'
            ])->limit(1000)->orderby_string( 'sns_paris2024_medals.gold DESC, sns_paris2024_medals.silver DESC, sns_paris2024_medals.bronze DESC, sns_paris2024_medals.amount DESC' );

			$this->medals = $q->get_results();
		} else {
			$this->medals = ( new DBUnion( [
				Paris2024Medals::query()->select([
                    'country_id',
                    'country_name',
                    'gold',
                    'silver',
                    'bronze',
                    'amount'
                ])->where( [
					'country_name__in' => $topCountries
				] )->limit( 6 )->orderby_case( 'country_name', $topCountries ),
				Paris2024Medals::query()->select([
                    'country_id',
                    'country_name',
                    'gold',
                    'silver',
                    'bronze',
                    'amount'
                ])->where( [
					'country_name__not_in' => $topCountries
				] )->limit( - 1 )->orderby( 'amount', 'DESC' )
			] ) )->get_results();
		}

		$country_ids = [];
		foreach ( $this->medals as $medal ) {
			$country_ids[] = $medal->country_id;
		}

		$flags_data = ( new TermMetaQuery() )->select(['term_id', 'meta_value', 'meta_key'])->limit( - 1 )->where( [
			'term_id__in' => $country_ids,
			'meta_key'    => 'country_slug'
		] )->get_results();

		if ( ! empty( $flags_data ) ) {
			foreach ( $flags_data as $flag_data ) {
				$this->country_flags[ $flag_data->term_id ] = $flag_data->meta_value;
			}
		}



	}

	function medalsEnabled() {
		return $this->medals_enabled;
	}

	function getSchedule() {
		return $this->_getProp( 'schedule', function () {
			$eventsData = Paris2024Event::query()->select([
                'event_id',
                'sport_id',
                'event_name',
                'event_type',
                'custom',
                'event_time'
            ])->limit( - 1 )->orderby( 'event_time', 'ASC' )->get_results();

			$events = [];

			foreach ( $eventsData as $event ) {
				$events[] = new Paris2024Event( $event );
			}

			return $events;
		} );
	}

	function getSports() {
		return $this->_getProp( 'sports', function () {
			$sportsData = Paris2024Sport::query()->select([
                'sport_id',
                'sport_name',
                'medals'
            ])->limit( - 1 )->get_results();
			$sports     = [];
			foreach ( $sportsData as $sport ) {
				$sports[] = new Paris2024Sport( $sport );
			}

			return $sports;
		} );
	}

	function getMedals() {
		return $this->medals;
	}

	function getSportTables() {
		return $this->_getProp( 'tables', function () {
			return Paris2024Table::query()->limit( - 1 )->order( 'ASC' )->get_results();
		} );
	}

	function getTablesBySport( $sport_id, $by_name = false ) {

		$sportTables = $this->getSportTables();

		$tables = [];
		foreach ( $sportTables as $table ) {
			if ( $sport_id == $table->sport_id ) {
				if ( $by_name && $by_name != $table->table_name ) {
					continue;
				}
				$tables[] = $table;
			}
		}

		return $tables;

	}

	function getSport( $sport_id ) {

		$sports = $this->getSports();

		foreach ( $sports as $sport ) {
			if ( $sport->sport_id == $sport_id ) {
				return $sport;
			}
		}

		return false;

	}

	function getDateTimes() {

		$schedule = $this->getSchedule();

		$data = [];

		foreach ( $schedule as $event ) {

			$dateTimeArgs = explode( ' ', $event->event_time );

			$date = $dateTimeArgs[0];
			$time = $dateTimeArgs[1];

			if ( empty( $data[ $date ] ) ) {
				$data[ $date ] = [
					$time => [
						$event
					]
				];
			} else {
				$data[ $date ][ $time ][] = $event;
			}

		}

		return $data;

	}

	function getEventsOnDate( $date = false, $sport_id = false ) {

		$schedule = $this->getDateTimes();

		$events = [];

		if ( $date ) {

			foreach ( $schedule[ $date ] as $time => $eventsData ) {
				foreach ( $eventsData as $event ) {
					if ( $sport_id && $event->sport_id != $sport_id ) {
						continue;
					}
					$events[ $event->sport_id ][ $time ][] = $event;
				}
			}

		} else {

			foreach ( $schedule as $schedule_date ) {

				foreach ( $schedule_date as $time => $eventsData ) {
					foreach ( $eventsData as $event ) {
						if ( $sport_id && $event->sport_id != $sport_id ) {
							continue;
						}
						$events[ $event->sport_id ][ $event->event_time ][] = $event;
					}
				}
			}
		}

		return $events;

	}

	function getEventsOnDateMain( $date = false, $sport_id = false, $type = false ) {

		$schedule = $this->getDateTimes();

		$events = [];

		if ( $date ) {

			foreach ( $schedule[ $date ] as $time => $eventsData ) {
				foreach ( $eventsData as $event ) {
					if ( $sport_id && $event->sport_id != $sport_id ) {
						continue;
					}
					if ( $type && $event->event_type != $type ) {
						continue;
					}					
					$events[] = $event;
				}
			}

		} else {

			foreach ( $schedule as $schedule_date ) {

				foreach ( $schedule_date as $time => $eventsData ) {
					foreach ( $eventsData as $event ) {
						if ( $sport_id && $event->sport_id != $sport_id ) {
							continue;
						}
						if ( $type && $event->event_type != $type ) {
							continue;
						}						
						$events[] = $event;
					}
				}
			}
		}

		return $events;

	}

	function getEventsOnDateTop( $date = false, $sport_id = false ) {

		$schedule = $this->getDateTimes();

		$events = [];

		if ( $date ) {

			foreach ( $schedule[ $date ] as $time => $eventsData ) {
				foreach ( $eventsData as $event ) {
					
					if ($sport_id) {
						if ( $event->sport_id != $sport_id ) {
							continue;
						}					
						$events[ $event->sport_id ][ $time ][] = $event;
					} else {
						$events[ $time ][] = $event;
					}
				}
			}

		} else {

			foreach ( $schedule as $schedule_date ) {

				foreach ( $schedule_date as $time => $eventsData ) {
					foreach ( $eventsData as $event ) {
						if ( $sport_id && $event->sport_id != $sport_id ) {
							continue;
						}
						$events[ $event->sport_id ][ $event->event_time ][] = $event;
					}
				}
			}
		}

		return $events;

	}

	function getEventsBySportID( $sport_id ) {

		$schedule = $this->getSchedule();

		$events = [];
		foreach ( $schedule as $event ) {
			if ( $event->sport_id == $sport_id ) {
				$dateTimeArgs                                     = explode( ' ', $event->event_time );
				$events[ $dateTimeArgs[0] ][ $dateTimeArgs[1] ][] = $event;
			}
		}

		return $events;

	}

	function getFinalsBySportID( $sport_id ) {

		$schedule = $this->getSchedule();

		$events = [];
		foreach ( $schedule as $event ) {
			if ( $event->sport_id == $sport_id ) {
				if ( $event->event_type == 'final' ) {
					$dateTimeArgs = explode( ' ', $event->event_time );
					$events[ $dateTimeArgs[0] ][] = $event;
				}
			}
		}

		return $events;

	}

	function isFinalOnTheDate( $date, $sport_id ) {

		$sportEvents = $this->getEventsBySportID( $sport_id );

		$isFinal = false;
		foreach ( $sportEvents[ $date ] as $time => $events ) {
			foreach ( $events as $event ) {
				if ( $event->event_type == 'final' ) {
					$isFinal = true;
				}
			}
		}

		return $isFinal;

	}

	function findClosestDay($dates) {
		$now = time();
		foreach($dates as $date => $times)  {
        	$interval[$date] = abs($now - strtotime($date));
    	}

    	asort($interval);
    	$closest = key($interval);

    	return $closest;
	}

	function getScheduleContent() {
		return Template::get( $this->templatesDir . 'schedule.php', [
			'controller' => $this
		] );
	}

	function getScheduleContentAll() {
		return Template::get( $this->templatesDir . 'schedule-all.php', [
			'controller' => $this
		] );
	}

	function getTopScheduleContent() {
		return Template::get( $this->templatesDir . 'schedule-top.php', [
			'controller' => $this
		] );
	}

	function getMainScheduleContent() {
		return Template::get( $this->templatesDir . 'schedule-main.php', [
			'controller' => $this
		] );
	}

	function getScheduleBySportContent( $sport_id ) {
		return Template::get( $this->templatesDir . 'schedule-by-sport.php', [
			'controller' => $this,
			'sport_id'   => $sport_id
		] );
	}

	function getScheduleByDateContent( $date = false, $sport_id = false, $type = false ) {
		return Template::get( $this->templatesDir . 'schedule-by-date.php', [
			'controller' => $this,
			'date'       => $date,
			'sport_id'   => $sport_id,
			'type'       => $type
		] );
	}

	function getScheduleByDateContentTop( $date = false, $sport_id = false ) {
		return Template::get( $this->templatesDir . 'schedule-by-date-top.php', [
			'controller' => $this,
			'date'       => $date,
			'sport_id'   => $sport_id
		] );
	}

	function getEventTopSportContent( $times, $sport_id = false ) {
		return Template::get( $this->templatesDir . 'event-top-sport.php', [
			'controller' => $this,
			'times'      => $times,
			'sport_id'   => $sport_id
		] );
	}	

	function getEventTopDateContent( $times, $sport_id = false ) {
		return Template::get( $this->templatesDir . 'event-top-date.php', [
			'controller' => $this,
			'times'      => $times,
			'sport_id'   => $sport_id
		] );
	}

	function getMedalsContent( $top = true ) {
		return Template::get( $this->templatesDir . 'medals.php', [
			'controller' => $this,
			'top'        => $top
		] );
	}

	function getSingleMedalContent( $medal, $position ) {
		return Template::get( $this->templatesDir . 'medal-single.php', [
			'controller' => $this,
			'medal'      => $medal,
			'position'   => $position
		] );
	}

	function hasCountryFlag( $country_id = false ) {

		if ( ! $country_id ) {
			return false;
		}

		if ( ! isset( $this->country_flags[ $country_id ] ) ) {
			return false;
		}

		return true;
	}

	function getCountryFlag( $country_id = false ) {

		if ( ! $this->hasCountryFlag( $country_id ) ) {
			return false;
		}

		return $this->country_flags[ $country_id ];
	}

	function getSportTablesContent( $sport_id, $by_name = false ) {

		$tables = $this->getTablesBySport( $sport_id, $by_name );

		$content = '';
		foreach ( $tables as $table ) {
			$content .= Template::get( $this->templatesDir . 'table.php', [
				'controller' => $this,
				'table'      => $table,
				'sport'      => get_term( $sport_id )
			] );
		}

		return $content;

	}

	function getOption( $option ) {
		$options = get_option( 'paris2024_settings' );
		return $options[$option];
	}

	function getNewsQuery( $args = [] ) {


		$args = wp_parse_args( $args, [
			'term_id' => $this->getOption('news_term')
		] );

		return ( new PostsQuery() )->select( [
			'ID',
			'post_title',
			'post_date',
			'post_name'
		] )->where( [ 'post_type' => 'post', 'post_status' => 'publish' ] )->join( [
			'ID',
			'object_id',
			'LEFT'
		], DBQuery::tbl( new TermRelationshipsQuery( 'tags_relationships' ) )->join( [
			'term_taxonomy_id',
			'term_taxonomy_id',
			'LEFT'
		], DBQuery::tbl( new TermTaxonomyQuery( 'tags_terms' ) ) ) )->where_string( "(tags_terms.term_id = '" . $args['term_id'] . "' OR tags_terms.parent = '" . $args['term_id'] . "')" )->orderby_string('post_date DESC');
	}

	function getNewsLimit() {
		return $this->news_limit;
	}

	function getNews( $args = [] ) {
		return $this->_getProp( 'news', function () use ( $args ) {

			$args = wp_parse_args( $args, [
				'offset'  => 0,
				'number'  => $this->news_limit,
				'term_id' => $this->getOption('news_term'),
			] );

			$news_query = $this->getNewsQuery( $args );

			return $news_query->limit( $args['number'], $args['offset'] )->get_results();
		} );
	}

	function getNewsTotal( $args = [] ) {

		return $this->_getProp( 'news_total', function () use ( $args ) {

			$news_query = $this->getNewsQuery( $args );

			return $news_query->limit( - 1 )->get_count();
		} );
	}

	function getNewsContent( $args = [] ) {

		$args = wp_parse_args( $args, [
			'term_id' => $this->getOption('news_term')
		] );

		return Template::get( $this->templatesDir . 'news.php', [
			'controller' => $this,
			'news'       => $this->getNews( $args )
		] );
	}

	function getSingleNewsContent( $news_item ) {
		return Template::get( $this->templatesDir . 'news-item.php', [
			'news_item' => $news_item
		] );
	}
	function getSingleNewsContentMain( $news_item ) {
		return Template::get( $this->templatesDir . 'news-item-main.php', [
			'news_item' => $news_item
		] );
	}

	function getSportsTables($league_id, $season_id) {
		$post_args = [
			'post_type'      => 'sp_table',
			'posts_per_page' => -1,
			'status'         => 'publish',
			'tax_query'      => [
				'relation' => 'AND',
				[
					'taxonomy' => 'sp_league',
		  			'field'    => 'term_id',
		  			'terms'    => $league_id,
				],
				[
					'taxonomy' => 'sp_season',
		  			'field'    => 'term_id',
		  			'terms'    => $season_id,
				]		

			]  
		];

		$posts_query = new WP_Query;

		$tables = $posts_query->query($post_args);

		return $tables;	
	}
}
