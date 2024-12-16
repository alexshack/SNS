<?php

class Paris2024MedalsUpdate extends UpdaterStage {

	public $parser;
	public $labels = [
		'title'     => 'Подсчет стран',
		'not-found' => 'Ничего не найдено',
		'total'     => 'Всего стран',
		'start'     => 'Считаем кол-во стран',
		'success'   => 'Медальные зачеты стран обновлены'
	];

	function __construct( $args = [] ) {
		$args['number'] = wp_doing_cron() ? 999 : 30;
		$this->parser   = new Paris2024Parser();
		parent::__construct( $args );
	}

	function get_total() {

		if(!empty($_POST['stages']) && !in_array('medals', $_POST['stages'])){
			return 0;
		}

		return count( $this->parser->getMedals() );
	}

	function get_data() {
		return array_slice( $this->parser->getMedals(), $this->offset, $this->number );
	}

	function updateItem( $item ) {

		$country_id = Helpers::getTermId( $item['country'], 'prohibited_countries' );

		if(empty($country_id)){
			print_r($item);exit;
		}

		$countryMedals = Paris2024Medals::query()->select(['country_id'])->where( [
			'country_id' => $country_id
		] )->get_var();

		if ( $countryMedals ) {

			Paris2024Medals::updateData( [
				'gold'   => $item['gold'],
				'silver' => $item['silver'],
				'bronze' => $item['bronze'],
				'amount' => $item['all']
			], [
				'country_id' => $country_id,
			] );

		} else {

			Paris2024Medals::insertData( [
				'country_id'   => $country_id,
				'country_name' => $item['country'],
				'gold'         => $item['gold'],
				'silver'       => $item['silver'],
				'bronze'       => $item['bronze'],
				'amount'       => $item['all']
			] );

		}

	}

}