<?php

class Paris2024HockeyTablesUpdate extends UpdaterStage {

	public $parser;
	public $sport_id;
	public $labels = [
		'title'     => 'Обновление таблиц хоккея',
		'not-found' => 'Ничего не найдено',
		'total'     => 'Всего таблиц',
		'start'     => 'Считаем кол-во таблиц',
		'success'   => 'Таблицы хоккея обновлены'
	];

	function __construct( $args = [] ) {
		$args['number'] = wp_doing_cron() ? 999 : 1;
		$this->parser   = new Paris2024Parser();
		$this->sport_id = get_term_by( 'name', 'Хоккей', 'sport-type' )->term_id;
		parent::__construct( $args );
	}

	function get_total() {

		if(!empty($_POST['stages']) && !in_array('hockey', $_POST['stages'])){
			return 0;
		}

		if ( ! wp_doing_cron() ) {
			Paris2024Table::query()->where( [
				'sport_id' => $this->sport_id
			] )->delete();
		}

		return count( $this->parser->getHockeyTables() );
	}

	function get_data() {
		return array_slice( $this->parser->getHockeyTables(), $this->offset, $this->number );
	}

	function updateItem( $item ) {

		$table = Paris2024Table::query()->where( [
			'sport_id'   => $this->sport_id,
			'table_name' => $item['title'],
			'type'       => $item['type'],
		] )->get_row();

		$content = [];
		foreach ( $item['body'] as $r => $cols ) {
			foreach ( $cols as $k => $col ) {
				if ( ! empty( $col ) && $item['head'][ $k ] == 'Команда' ) {
					$country_id          = Helpers::getTermId( $col, 'prohibited_countries', 'name' );
					$content[ $r ][ $k ] = [
						'name' => $col,
						'id'   => $country_id
					];
				} else {
					$content[ $r ][ $k ] = $col;
				}
			}
		}

		if ( $table ) {
			Paris2024Table::updateData( [
				'table_content' => $content
			], [
				'tid' => $table->tid
			] );
		} else {
			Paris2024Table::insertData( [
				'sport_id'      => $this->sport_id,
				'type'          => $item['type'],
				'table_name'    => $item['title'],
				'table_header'  => $item['head'],
				'table_content' => $content
			] );
		}

	}

}