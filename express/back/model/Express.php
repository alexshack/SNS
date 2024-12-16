<?php
class Express extends PostAbstract {
	public $date;
	public $post_title;
	public $bookmaker;
	public $games;
	public $coef;
	public $url;

	function __construct($post) {
		parent::__construct($post);
		$this->metadata = isset($this->post->metadata) ? $this->post->metadata : new ArrayHandler();
		$this->coef = $this->metadata->get('express_coef') ? $this->metadata->get('express_coef') : 0;
		$this->url = $this->metadata->get('express_url') ? $this->metadata->get('express_url') : '';
		$this->date = $this->metadata->get('express_date') ? $this->getDate($this->metadata->get('express_date')) : 0;
		$this->games = $this->setupGames();
		$this->post_title = isset($this->post->post_title) ? do_shortcode($this->post->post_title) : '';
		$this->bookmaker = $this->bookmakerQuery();

		return $this;
	}

	public function getDate($date) {
		return wp_date('d.m.Y', $date );
	}

	protected function bookmakerQuery() {

		$country_code = getUserCountry() ? getUserCountry() : 'world';
		$id = $this->metadata->get('bk_id');
		$bookmakers = (new BookmakersQuery('b'))->select([
			'name',
			'rate',
			'partner_link',
			'license_ua',
			'license_by',
			'cupis',
			'order_' . $country_code
		])->join([
			'post_id',
			'ID'
		], (new PostsQuery('p'))->select([
			'ID',
			'post_name',
			'post_title',
			'post_type',
			'comment_count'
		])->where([
			'post_status' => 'publish',
			'ID' => $id
		]))->get_results(1);

		return Bookmaker::setup($bookmakers);

	}

	public function setupMetaData($meta_keys) {
		$meta_keys_list = [];

		if(!is_array($meta_keys)) {
			$meta_keys_list[] = $meta_keys;
		} else {
			$meta_keys_list = $meta_keys;
		}

		$query_result = (new PostMetaQuery())->select([
			'meta_key',
			'meta_value'
		])->where([
			'post_id' => $this->post->ID,
			'meta_key__in' => $meta_keys_list
		])->limit(100)->get_results();

		foreach ($query_result as $result) {
			if(!empty($result->meta_value)) {
				$this->metadata->set($result->meta_key, $result->meta_value);
			}
		}
	}

	public function updateTable($data) {
		Expresses::loadTableUpdater();
		$table = new ExpressTableUpdater();
		$table->updateByPrimaryKey($this->post->ID, $data);
	}

	protected function setupGames() {
		$games = $this->metadata->get('express_games');
		$items = [];
		foreach ($games as $k => $game) {
			$items[$k]['name'] = $game['match'];
			$items[$k]['time'] = $game['time'];
			$items[$k]['bet'] = $game['bet'];
			$items[$k]['coef'] = $game['coef'];
			$term_id = $game['sport_type'];
			$term = new TermsModel($term_id);
			$items[$k]['type'] = get_term_by('id', $term_id, 'sport-type')->name;
			$items[$k]['type_icon'] = $term->getCategoryImage('express_game_icon');
			$items[$k]['tournament'] = get_term_by('id', $game['tournament'], 'tournament')->name;

		}
		return $items;
	}

	public function getThumbnailImg($class = '', $widthSize = '350x300', $attrSize = '298x228') {
		if($this->post->thumbnail) {
			return $this->post->thumbnail->getlazyLoadImg($class, ['alt' => do_shortcode($this->post_title)], $widthSize, $attrSize);
		} else {
			return false;
		}
	}

	public function getThumbnailUrl() {
		if($this->post->thumbnail) {
			return $this->post->thumbnail->getUrl();
		} else {
			return false;
		}
	}

	public function getPartnerLink() {
		return $this->bookmaker && isset($this->bookmaker->partner_link) ? $this->bookmaker->getPartnerLink() : false;
	}

	public function getBookmakerThumbnailImg() {
		return $this->bookmaker && isset($this->bookmaker->thumbnail) ? $this->bookmaker->thumbnail->getLazyLoadImg('', [], '105x70') : false;
	}

	static function setup($post_id) {
		$express_filter = new ExpressFilter([
			'post_id' => $post_id
		], [
			'limit' => 1
		]);
		return (Expresses::setup($express_filter->getResults()))[0];
	}

}