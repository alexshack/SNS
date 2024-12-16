<?php

class Paris2024EventsUpdate extends UpdaterStage {

	public $parser;
	public $data;
	public $labels = [
		'title'     => 'Обновление расписания',
		'not-found' => 'Ничего не найдено',
		'total'     => 'Всего мероприятий',
		'start'     => 'Считаем кол-во мероприятий',
		'success'   => 'Все мероприятий обновлены'
	];

	function __construct( $args = [] ) {
		$args['number'] = 30;
		$this->parser   = new Paris2024Parser();
		$this->data     = $this->parser->getSchedule();
		parent::__construct( $args );
	}

	function get_total() {

		if(wp_doing_cron()){
			return 0;
		}

		if(!empty($_POST['stages']) && !in_array('events', $_POST['stages'])){
			return 0;
		}

		if ( is_array( $items = $this->data ) ) {
			return count( $items );
		}

		return 0;
	}

	function get_data() {
		if ($this->data) {
			return array_slice( $this->data, $this->offset, $this->number );
		}
	}

	function updateItem( $item ) {

		$event_time = date( 'Y-m-d H:i:s', strtotime( $item[0] ) );

		$sport_id = Helpers::getTermId( $item[1], 'sport-type', 'name' );

		$event_name = $item[2];
		if ( ! empty( $sport_id ) && ! Paris2024Sport::query()->select(['sport_id'])->where( [ 'sport_id' => $sport_id ] )->get_var() ) {
			Paris2024Sport::insertData( [
				'sport_id'   => $sport_id,
				'sport_name' => $item[1],
				'medals'     => 0
			] );
		}

		$nameArgs  = explode( ': ', $event_name );
		$countries = [];

		if ( ! empty( $nameArgs[1] ) ) {

			$countryPairs = explode( ', ', $nameArgs[1] );

			foreach ( $countryPairs as $countryPair ) {

				$countriesData = explode( ' - ', $countryPair );

				if ( ! empty( $countriesData ) ) {

					$countries[] = [
						[
							'name' => $countriesData[0],
							'id'   => Helpers::getTermId( $countriesData[0], 'prohibited_countries', 'name' )
						],
						[
							'name' => $countriesData[1],
							'id'   => Helpers::getTermId( $countriesData[1], 'prohibited_countries', 'name' )
						]
					];

				}
			}

			$event_name = $nameArgs[0];

		}

		$event_type = 'compete';
		if ( mb_stristr( $event_name, '3-е место' ) !== false ) {
			$event_type = 'third';
		} else if ( ! empty( $item[3] ) ) {
			$event_type = 'final';
		} else if ( mb_stristr( $event_name, 'открытия' ) !== false ) {
			$event_type = 'open';
		} else if ( mb_stristr( $event_name, 'закрытия' ) !== false ) {
			$event_type = 'close';
		}


		$hasEvent = Paris2024Event::query()->select(['event_id'])->where( [
			'sport_id' => $sport_id,
			'event_time' => $event_time,
			'event_name' => $event_name
		] )->get_var();

		if ( $hasEvent ) {


		} else {

			Paris2024Event::insertData( [
				'sport_id'   => $sport_id,
				'event_name' => $event_name,
				'event_type' => $event_type,
				'custom'     => ! empty( $countries )? maybe_serialize( $countries ): '',
				'event_time' => $event_time
			] );

		}


	}

	function last_iteration() {

		$sports = Paris2024Sport::query()->limit(-1)->get_results();

		foreach ( $sports as $sport ) {
			Paris2024Sport::query()->where( [
				'sport_id' => $sport->sport_id
			] )->update( [
				'medals' => Paris2024Event::query()->where( [
					'sport_id'   => $sport->sport_id,
					'event_type' => 'final'
				] )->get_count()
			] );
		}

	}

}