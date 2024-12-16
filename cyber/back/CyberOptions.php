<?php

class CyberOptions extends AdminPage {
	private $cyber;
	private $betboom;
	private $winline;
	private $fonbet;

	function __construct( $id, $args = [] ) {
		$this->setupData();
		parent::__construct( $id, $args );
		$this->init_repeater( 'cyber_sports', new APF_Repeater( [
			APF::setup( 'text', [
				'title' => 'Название',
				'id' => 'name',
			] ),
			APF::setup( 'text', [
				'title' => 'Слаг',
				'id' => 'slug',
			] ),
			APF::setup( 'number', [
				'title' => 'BetBoom ID',
				'id' => 'betboom',
			] ),
			APF::setup( 'text', [
				'title' => 'Winline ID',
				'id' => 'winline',
			] ),
			APF::setup( 'text', [
				'title' => 'Fonbet ID',
				'id' => 'fonbet',
			] ),									
		] ) );		
	}

	private function setupData() {
		$this->cyber   = new Options('cyber');
		$this->betboom = new Options('betboom');
		$this->winline = new Options('winline');
		$this->fonbet  = new Options('fonbet');
	}


	function get_form() {

		return $this->get_subpages( [
			'subpages' => [
				'general' => [
					'name' => 'Общие настройки',
					'data' => 'value'
				],
				'betboom' => [
					'name' => 'BetBoom',
					'data' => 'value'
				],
				'winline' => [
					'name' => 'Winline',
					'data' => 'value'
				],
				'fonbet' => [
					'name' => 'Fonbet',
					'data' => 'value'
				],
			],
			'active'   => 'general'
		], false, function ( $subpage_id) {

			switch($subpage_id){
				case 'general':
					return $this->get_content_general();
				case 'betboom':
					return $this->get_content_betboom();
				case 'winline':
					return $this->get_content_winline();
				case 'fonbet':
					return $this->get_content_fonbet();									
			}

		} );

	}



	function get_content_general(){
		$content = '<div class="bk-options-wrapper width-50">';

		$content .= APF::setup( 'select', [
			'id'            => 'cyber_news_page',
			'title'         => 'Выберите страницу новостей',
			'width'         => 'full',
			'empty_first'   => 'Ничего не выбрано',
			'search'        => true,
			'values'        => Values::getPosts('page'),
			'value'         => $this->cyber->getOption('news_page'),
			'input_name'    => 'cyber[news_page]'
		] )->get_html();

		$content .= APF::setup( 'select', [
			'id'            => 'cyber_news_cat',
			'title'         => 'Выберите раздел для страницы новостей',
			'width'         => 'full',
			'empty_first'   => 'Ничего не выбрано',
			'search'        => true,
			'values'        => Values::getTerms('category'),
			'value'         => $this->cyber->getOption('news_cat'),
			'input_name'    => 'cyber[news_cat]'
		] )->get_html();

		$content .= APF::setup( 'select', [
			'id'            => 'cyber_predicts_page',
			'title'         => 'Выберите страницу прогнозов',
			'width'         => 'full',
			'empty_first'   => 'Ничего не выбрано',
			'search'        => true,
			'values'        => Values::getPosts('page'),
			'value'         => $this->cyber->getOption('predicts_page'),
			'input_name'    => 'cyber[predicts_page]'
		] )->get_html();

		$content .= APF::setup( 'select', [
			'id'            => 'cyber_predicts_cat',
			'title'         => 'Выберите раздел для страницы прогнозов',
			'width'         => 'full',
			'empty_first'   => 'Ничего не выбрано',
			'search'        => true,
			'values'        => Values::getTerms('sport-type'),
			'value'         => $this->cyber->getOption('predicts_cat'),
			'input_name'    => 'cyber[predicts_cat]'
		] )->get_html();

		$content .= APF::setup( 'select', [
			'id'            => 'cyber_posts_page',
			'title'         => 'Выберите страницу статей',
			'width'         => 'full',
			'empty_first'   => 'Ничего не выбрано',
			'search'        => true,
			'values'        => Values::getPosts('page'),
			'value'         => $this->cyber->getOption('posts_page'),
			'input_name'    => 'cyber[posts_page]'
		] )->get_html();

		$content .= APF::setup( 'select', [
			'id'            => 'cyber_posts_cat',
			'title'         => 'Выберите раздел для страницы статей',
			'width'         => 'full',
			'empty_first'   => 'Ничего не выбрано',
			'search'        => true,
			'values'        => Values::getTerms('category'),
			'value'         => $this->cyber->getOption('posts_cat'),
			'input_name'    => 'cyber[posts_cat]'
		] )->get_html();

		$content .= APF::setup( 'select', [
			'id'            => 'cyber_games_page',
			'title'         => 'Выберите страницу матчей',
			'width'         => 'full',
			'empty_first'   => 'Ничего не выбрано',
			'search'        => true,
			'values'        => Values::getPosts('page'),
			'value'         => $this->cyber->getOption('games_page'),
			'input_name'    => 'cyber[games_page]'
		] )->get_html();

		$content .= APF::setup( 'select', [
			'id'            => 'cyber_games_cat',
			'title'         => 'Выберите раздел для страницы матчей',
			'width'         => 'full',
			'empty_first'   => 'Ничего не выбрано',
			'search'        => true,
			'values'        => Values::getTerms('category'),
			'value'         => $this->cyber->getOption('games_cat'),
			'input_name'    => 'cyber[games_cat]'
		] )->get_html();		

		$content .= '</div>';

		$content .= '<h3>Виды спорта</h3>';

		$content .= $this->repeater_content( 'cyber_sports', [
			'values'  => get_option('cyber_sports'),
			'button'  => 'Добавить еще',
			'item_id' => 'cyber_sports'
		] );


		return $content;
	}

	function get_content_betboom(){
		$content = '<div class="bk-options-wrapper width-50">';

		$content .= APF::setup( 'select', [
			'id'            => 'betboom_bk_id',
			'title'         => 'Выберите запись букмекера',
			'width'         => 'full',
			'empty_first'   => 'Ничего не выбрано',
			'search'        => true,
			'values'        => Values::getPosts('bookmakers'),
			'value'         => $this->betboom->getOption('bk_id'),
			'input_name'    => 'betboom[bk_id]'
		] )->get_html();

		$content .= APF::setup( 'url', [
			'id'            => 'betboom_link',
			'title'         => 'Партнерская ссылка',
			'width'         => 'full',
			'value'         => $this->betboom->getOption('link'),
			'input_name'    => 'betboom[link]'
		] )->get_html();

		$content .= APF::setup( 'url', [
			'id'            => 'betboom_feed',
			'title'         => 'Feed URL',
			'width'         => 'full',
			'value'         => $this->betboom->getOption('feed'),
			'input_name'    => 'betboom[feed]'
		] )->get_html();

		$content .= APF::setup( 'text', [
			'id'            => 'betboom_feeds',
			'title'         => 'ID фидов, через запятую',
			'width'         => 'full',
			'value'         => $this->betboom->getOption('feeds'),
			'input_name'    => 'betboom[feeds]'
		] )->get_html();

		$content .= '</div>';

		return $content;
	}

	function get_content_winline(){
		$content = '<div class="bk-options-wrapper width-50">';

		$content .= APF::setup( 'select', [
			'id'            => 'winline_bk_id',
			'title'         => 'Выберите запись букмекера',
			'width'         => 'full',
			'empty_first'   => 'Ничего не выбрано',
			'search'        => true,
			'values'        => Values::getPosts('bookmakers'),
			'value'         => $this->winline->getOption('bk_id'),
			'input_name'    => 'winline[bk_id]'
		] )->get_html();

		$content .= APF::setup( 'url', [
			'id'            => 'winline_link',
			'title'         => 'Партнерская ссылка',
			'width'         => 'full',
			'value'         => $this->winline->getOption('link'),
			'input_name'    => 'winline[link]'
		] )->get_html();

		$content .= APF::setup( 'url', [
			'id'            => 'winline_feed',
			'title'         => 'Feed URL',
			'width'         => 'full',
			'value'         => $this->winline->getOption('feed'),
			'input_name'    => 'winline[feed]'
		] )->get_html();

		$content .= APF::setup( 'text', [
			'id'            => 'winline_feeds',
			'title'         => 'ID фидов, через запятую',
			'width'         => 'full',
			'value'         => $this->winline->getOption('feeds'),
			'input_name'    => 'winline[feeds]'
		] )->get_html();

		$content .= '</div>';

		return $content;
	}

	function get_content_fonbet(){
		$content = '<div class="bk-options-wrapper width-50">';

		$content .= APF::setup( 'select', [
			'id'            => 'fonbet_bk_id',
			'title'         => 'Выберите запись букмекера',
			'width'         => 'full',
			'empty_first'   => 'Ничего не выбрано',
			'search'        => true,
			'values'        => Values::getPosts('bookmakers'),
			'value'         => $this->fonbet->getOption('bk_id'),
			'input_name'    => 'fonbet[bk_id]'
		] )->get_html();

		$content .= APF::setup( 'url', [
			'id'            => 'fonbet_link',
			'title'         => 'Партнерская ссылка',
			'width'         => 'full',
			'value'         => $this->fonbet->getOption('link'),
			'input_name'    => 'fonbet[link]'
		] )->get_html();

		$content .= APF::setup( 'url', [
			'id'            => 'fonbet_feed',
			'title'         => 'Feed URL',
			'width'         => 'full',
			'value'         => $this->fonbet->getOption('feed'),
			'input_name'    => 'fonbet[feed]'
		] )->get_html();

		$content .= APF::setup( 'text', [
			'id'            => 'fonbet_feeds',
			'title'         => 'ID фидов, через запятую',
			'width'         => 'full',
			'value'         => $this->fonbet->getOption('feeds'),
			'input_name'    => 'fonbet[feeds]'
		] )->get_html();

		$content .= '</div>';

		return $content;
	}

	function update() {
		
		$keys = [
			'cyber',
			'betboom',
			'winline',
			'fonbet'
		];

		foreach ($keys as $key) {
			if(isset($_POST[$key])) {
				update_option($key, $_POST[$key]);
			}
		}

		$keys = [
			'cyber_sports',
		];

		foreach ($keys as $key) {
			if(isset($_POST[$key])) {
				$value = $_POST[$key];
				if ( ! empty( self::$groups[$key] ) ) {
					$value = $this->repeater($key)->setup_value( $value );
				}
				update_option($key, $value);
			} else {
				delete_option($key);
			}
		}

		$this->setupData();		

	}

}
