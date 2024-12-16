<?php

class ExpressOptions extends AdminPage {
	private $express;

	function __construct( $id, $args = [] ) {
		$this->express = new Options('express');
		parent::__construct( $id, $args );
	}

	function get_form() {

		return $this->get_subpages( [
			'subpages' => [
				'general' => [
					'name' => 'Общие настройки',
					'data' => 'value'
				],
			],
			'active'   => 'general'
		], false, function ( $subpage_id) {

			switch($subpage_id){
				case 'general':
					return $this->get_content_general();
			}

		} );

	}

	function get_content_general(){
		$content = '<div class="bk-options-wrapper width-50">';

		$content .= APF::setup( 'select', [
			'id'            => 'express_page',
			'title'         => 'Выберите страницу списка экспрессов',
			'width'         => 'full',
			'empty_first'   => 'Ничего не выбрано',
			'search'        => true,
			'values'        => Values::getPosts('page'),
			'value'         => $this->express->getOption('express_page'),
			'input_name'    => 'express[express_page]'
		] )->get_html();

		$content .= APF::setup( 'select', [
			'id'            => 'express_page_main',
			'title'         => 'Показывать на главной странице',
			'values'        => [0 => 'Выключено', 1 => 'Включено'],
			'value'         => $this->express->getOption('express_page_main'),
			'width'         => 'full',
			'input_name'    => 'express[express_page_main]'
		] )->get_html();

		return $content . '</div>';
	}

	function update() {
		if(isset($_POST['express'])) {
			$express = $_POST['express'];
			update_option('express', $express);
			$this->express = new Options('express');
		}

	}

}
