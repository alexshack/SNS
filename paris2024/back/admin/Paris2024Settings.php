<?php

class Paris2024Settings extends AdminPage {

	function __construct( $id, $args = [] ) {
		parent::__construct( $id, $args );

		$this->init_repeater( 'videos', new APF_Repeater( [
			APF::setup( 'image', [
				'id'    => 'image',
				'title' => 'Изображение превью'
			] ),
			APF::setup( 'text', [
				'id'        => 'url',
				'title'     => 'Ссылка на видео',
				'width'     => 'full',
				'width_min' => 400
			] )
		] ) );
	}

	function get_form() {

		$settings = get_option( 'paris2024_settings' );

		$content = APF::setup( 'radio', [
			'id'     => 'medals_widget',
			'title'  => __( 'Виджет медального зачета' ),
			'values' => [
				'Отключено',
				'Включено'
			],
			'value'  => ! empty( $settings['medals_widget'] ) ? $settings['medals_widget'] : [],
		] )->get_html();

		$content .= APF::setup( 'select', [
			'id'            => 'news_term',
			'title'         => __( 'Категория новостей' ),
			'value'         => ! empty( $settings['news_term'] ) ? $settings['news_term'] : '',
			'search'        => true,
			'values'        => Values::getTerms('category', ['hide_empty' => false])
		] )->get_html();

		$content .= APF::setup( 'select', [
			'id'            => 'articles_term',
			'title'         => __( 'Категория статей' ),
			'value'         => ! empty( $settings['articles_term'] ) ? $settings['articles_term'] : '',
			'search'        => true,
			'values'        => Values::getTerms('category', ['hide_empty' => false])
		] )->get_html();

		$content .= APF::setup( 'select', [
			'id'            => 'predicts_term',
			'title'         => __( 'Категория прогнозов' ),
			'value'         => ! empty( $settings['predicts_term'] ) ? $settings['predicts_term'] : '',
			'search'        => true,
			'values'        => Values::getTerms('tournament', ['hide_empty' => false])
		] )->get_html();

		$content .= APF::setup( 'select', [
			'id'            => 'page_main',
			'title'         => __( 'Главная страница' ),
			'value'         => ! empty( $settings['page_main'] ) ? $settings['page_main'] : '',
			'search'        => true,
			'values'        => Values::getPosts('page'),
		] )->get_html();

		$content .= APF::setup( 'text', [
			'id'    => 'page_schedule',
			'title' => __( 'Расписание' ),
			'width' => 'half',
			'value' => ! empty( $settings['page_schedule'] ) ? $settings['page_schedule'] : '',
		] )->get_html();

		$content .= APF::setup( 'text', [
			'id'    => 'page_predicts',
			'title' => __( 'Страница прогнозов' ),
			'width' => 'half',
			'value' => ! empty( $settings['page_predicts'] ) ? $settings['page_predicts'] : '',
		] )->get_html();

		$content .= APF::setup( 'text', [
			'id'    => 'page_football',
			'title' => __( 'Страница Футбол' ),
			'width' => 'half',
			'value' => ! empty( $settings['page_football'] ) ? $settings['page_football'] : '',
		] )->get_html();

		$content .= APF::setup( 'text', [
			'title' => __( 'Страница Баскетбол' ),
			'id'    => 'page_basketball',
			'width' => 'half',
			'value' => ! empty( $settings['page_basketball'] ) ? $settings['page_basketball'] : '',
		] )->get_html();


		$content .= APF::setup( 'text', [
			'id'    => 'page_tennis',
			'title' => __( 'Страница Теннис' ),
			'width' => 'half',
			'value' => ! empty( $settings['page_tennis'] ) ? $settings['page_tennis'] : '',
		] )->get_html();

		$content .= $this->repeater_content( 'videos', [
			'title'  => 'Видеожурнал',
			'values' => ! empty( $settings['videos'] ) ? $settings['videos'] : [],
			'button' => 'Добавить еще'
		] );



		return $content;
	}

	function update() {

		$keys = [
			'medals_widget',
			'news_term',
			'articles_term',
			'predicts_term',
			'page_main',
			'page_schedule',
			'page_football',
			'page_basketball',
			'page_tennis',
			'page_medals',
			'videos'
		];

		$settings = [];
		foreach ( $keys as $key ) {

			$value = false;
			if ( ! empty( $_POST[ $key ] ) ) {
				$value = $_POST[ $key ];
			}

			//данные репитера
			if ( ! empty( self::$groups[ $key ] ) ) {
				$value = $this->repeater( $key )->setup_value( $value );
			}

			$settings[ $key ] = $value;

		}

		update_option( 'paris2024_settings', $settings );
	}

}
