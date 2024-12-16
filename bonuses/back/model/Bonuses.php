<?php
class Bonuses extends PostsAbstract {

	protected $bookmakers_ids = [];
	protected $bookmakers = [];
	protected $currencies;
	protected $types = [];

	protected function setupData() {
		$this->types = (new BonusesMetaQuery('bsm'))->select([
			'post_id',
			'meta_key',
			'meta_value'
		])->where([
			'meta_key__in' => ['bonus_type'],
			'post_id__in' => $this->posts_ids
		])->limit(1000)->get_results(1);


		$this->setupTerms()->setupBookmakerObj()->setupBonusObj()->setupBonusesMeta();


		return $this;

	}

	protected function setupBookmakerObj() {
		if(!$this->arguments->get('no_bookmaker')) {
			$this->bookmakers_ids = (new ObjectsHandler($this->posts))->getPropertiesArray('bookmaker_id');
			$this->bookmakers = Bookmakers::setup($this->bookmakers_ids, [
				'data' => [
					'ID',
					'post_name',
					'post_title',
					'post_type'
				],
				'setup_thumbnails' => 1
			]);

			foreach ($this->posts as $bonus) {
				$bonus->bookmaker = $this->getBookmaker($bonus->bookmaker_id);
			}

		}
		return $this;
	}

	protected function setupBonusesMeta() {
		$types = $this->types;

		$sort = [];
		foreach ($types as $obj) {

			$sort[$obj->post_id][$obj->meta_key][] = $obj->meta_value;
		}


		$posts = [];
		foreach ($this->posts as $post) {
			$post->bonus_types = isset($sort[$post->ID]) && isset($sort[$post->ID]['bonus_type']) ? $sort[$post->ID]['bonus_type'] : false;
			$posts[] = $post;
		}
		$this->posts = $posts;

		return $this;
	}

	protected function getBookmaker($bookmaker_id) {
		foreach ($this->bookmakers as $bookmaker) {
			if(intval($bookmaker_id) === intval($bookmaker->ID)) {
				return $bookmaker;
			}
		}
		return false;
	}

	protected function setupBonusObj() {
		$posts = [];
		foreach ($this->posts as $post) {

			$post = new Bonus($post);
			$post->terms = $this->terms_objects;
			$posts[] = $post;
		}

		$this->posts = $posts;

		return $this;
	}

	protected function setupTerms() {

		$objects = (new TermsQuery())->select([
			'name',
			'term_id',
			'slug'
		])->join([
			'term_id',
			'term_id'
		], (new TermTaxonomyQuery())->select([
			'taxonomy',
			'parent'
		]))->where([
			'term_id__in' => array_merge(
				(new ObjectsHandler($this->posts))->getPropertiesArray('min_bonus_currency'),
				(new ObjectsHandler($this->posts))->getPropertiesArray('max_bonus_currency')
			)
		])->limit(1000)->get_results(1);

		foreach ($objects as $object) {
			$this->terms_objects[$object->term_id] = $object;
		}

		$objects = (new TermsQuery())->select([
			'name',
			'term_id',
			'slug'
		])->join([
			'term_id',
			'term_id'
		], (new TermTaxonomyQuery())->select([
			'taxonomy',
			'parent'
		])->where([
			'taxonomy' => 'bonus_type'
		]))->limit(1000)->get_results(1);

		foreach ($objects as $object) {
			$this->terms_objects[$object->term_id] = $object;
		}

		return $this;

	}

	static function getOrder($is_bk_bonus = false) {

		$order = [
			'bs.achievement = "Эксклюзив" DESC'
		];

		$order[] = 'bm.meta_value=313 DESC';
		$order[] = 'bm.meta_value=314 DESC';
		//$order[] = 'bs.max_bonus DESC';

		if(!$is_bk_bonus) {
			$order[] = 'p.post_date DESC';
			$order[] = 'bk.rate DESC';
		}

		return implode(', ', $order);
	}

	static function getBest($limit = 4) {
		$country_code = getUserCountry() ? getUserCountry() : 'world';
		return (new BonusesQuery('bs'))->select([
			'bookmaker_id',
			'date_end',
			'date_unlimited',
			'achievement',
			'amount',
			'min_bonus',
			'max_bonus',
			'min_bonus_currency',
			'max_bonus_currency',
			'promocode'
		])->join([
			'bookmaker_id',
			'post_id'
		], (new BookmakersQuery('bk'))->select([
			'rate',
			'cupis',
			'order_' . $country_code
		]))->join([
			'post_id',
			'ID'
		], (new PostsQuery('p'))->select([
			'post_type',
			'post_title',
			'post_name',
			'ID',
			'post_content'
		])->where([
			'post_status' => 'publish'
		]))->join([
			'post_id',
			'post_id'
		], (new BonusesMetaQuery('bm'))->select([
			'meta_value'
		])->where([
			'meta_key' => 'bonus_type'
		]))->where_string('(bs.date_end > "' . date('Y-m-d') . '" OR bs.date_unlimited = 1)')
           ->orderby_string('bk.order_' . $country_code . ' ASC, bk.rate DESC, bs.achievement = "Эксклюзив" DESC, bm.meta_value=313 DESC, bm.meta_value=314 DESC, bs.max_bonus DESC, p.post_date DESC')
           ->groupby('bs.bookmaker_id')
           ->limit($limit)->get_results();
	}

	static function template($path, $data = []) {
		extract($data);
		include get_template_directory() . '/templates/bonuses/' . $path;
	}

	static function getTemplate($path, $data = []) {
		ob_start();
		self::template($path, $data);
		return ob_get_clean();
	}

	static function getBookmakersBonuses($bookmakers) {
		$bonuses_ids = [];
		foreach ($bookmakers as $bookmaker) {
			$bonuses_ids[] = $bookmaker->bonus->ID;
		}

		return Bonuses::setup((new BonusesFilter())->where([
			'ID__in' => $bonuses_ids
		])->order('rate')->getResults());
	}
}
