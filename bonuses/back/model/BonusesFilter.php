<?php
class BonusesFilter {

	protected $query;
	protected $meta_query;
	protected $was_join = false;
	protected $bookmakers_query;
	protected $bookmakers_meta_query;

	function __construct( $all_statuses = false ) {
		$where = [];
		if ( ! $all_statuses ) {
			$where['post_status'] = 'publish';
		}
		$this->query = static::query( $where );
		$this->meta_query = static::metaQuery();
		$this->bookmakers_query = static::bookmakersQuery();
		$this->bookmakers_meta_query = static::metaQuery();

		return $this;
	}

	function where($where) {
		foreach ($where as $key => $value) {

			switch ($key) :
				case 'bookmaker_id' :
					if(intval($value) !== 0) {
						$this->query->where([
							'bookmaker_id__in' => $value
						]);
					}

					break;
				case 'bookmaker_id__not_in' :
					if(intval($value) !== 0) {
						$this->query->where([
							'bookmaker_id__not_in' => $value
						]);
					}

					break;					

				case 'bonus_type' :

					$this->meta_query->where([
						'meta_key' => 'bonus_type',
						'meta_value__in' => $value
					]);

					break;

				case 'sport_type' :

					$this->meta_query->where([
						'meta_key' => 'sport_type',
						'meta_value__in' => $value
					]);

					break;

				case 'ID__in' :

					$this->query->where([
						'post_id__in' => $value
					]);
					break;

				case 'ID__not_in' :

					$this->query->where([
						'post_id__not_in' => $value
					]);
					break;

				case 'status' :

					switch ($value) {

						case 'active':

							$this->query->where_string('(bs.date_end > "' . date('Y-m-d') . '" OR bs.date_unlimited = 1)');

							break;

					}

			endswitch;

		}

		return $this;
	}

	static function metaQuery() {
		return (new BonusesMetaQuery('bm'));
	}

	static function query( $where = [] ) {
		return (new BonusesQuery('bs'))->select([
			'post_id',
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
			'order_' . BookmakersFilter::getCountrySlug(),
			'min_deposit',
			'min_deposit_currency'
		]))->join([
			'post_id',
			'ID'
		], (new PostsQuery('p'))->select([
			'post_type',
			'post_title',
			'post_name',
			'ID',
			'post_content'
		])->where($where));

	}

	function getSQL() {
		return $this->query->get_sql();
	}

	function getCount() {
		$this->query->groupby('bs.bookmaker_id, bs.post_id');
		if(!$this->was_join) {
			$this->query->join([
				'post_id',
				'post_id'
			], $this->meta_query);
			$this->was_join = true;
		}
		return $this->query->get_count();
	}

	function limit($limit) {
		$this->query->limit($limit);
		return $this;
	}

	function order($order) {
		$this->query->orderby_string(self::getOrderString($order));
		return $this;
	}

	function orderByString($order) {
		$this->query->orderby_string($order);
	}

	function offset($num) {
		$this->query->offset($num);
		return $this;
	}

	function getResults() {
		$this->query->groupby('bs.bookmaker_id, bs.post_id');
		if(!$this->was_join) {

			$this->query->join([
				'post_id',
				'post_id'
			], $this->meta_query);

			$this->was_join = true;
		}

		return $this->query->get_results(true);
	}

	function getBookmakers() {
		$this->bookmakers_query->join([
			'post_id',
			'post_id'
		], $this->bookmakers_meta_query);

		return $this->bookmakers_query->get_results();
	}

	static function getOrderString($order) {
		switch ($order) :
			case 'popular':
				return '(bs.date_end > "' . date('Y-m-d') . '" OR bs.date_unlimited = 1) AND (bs.achievement = "Эксклюзив") DESC, bs.date_end > "' . date('Y-m-d') . '" OR bs.date_unlimited = 1 DESC, bk.order_' . BookmakersFilter::getCountrySlug() . ' ASC, bk.rate DESC, bm.meta_value=313 DESC, p.post_date DESC';
			break;
			case 'new':
				return 'bs.date_end > "' . date('Y-m-d') . '" OR bs.date_unlimited = 1 DESC, p.post_date DESC';
			break;
			case 'value':
				return 'bs.date_end > "' . date('Y-m-d') . '" OR bs.date_unlimited = 1 DESC, bs.max_bonus DESC';
			break;
			case 'type':
				return 'bs.date_end > "' . date('Y-m-d') . '" OR bs.date_unlimited = 1 DESC, bm.meta_value=313 DESC, bm.meta_value=314 DESC';
			break;
			case 'cupis':
				return 'bk.cupis DESC, bk.rate DESC';
			break;			
			case 'rate':
				return 'bk.rate DESC';
				break;
		endswitch;
		return '';
	}

	static function getFilterSettings($post_id) {

		$data = [
			'bookmaker_id' => 0
		];

		$bonus_type = get_post_meta($post_id, 'bonus_type', 1);

		if($bonus_type) {
			$data['bonus_type'] = $bonus_type;
		}

		return $data;

	}

	static function bookmakersQuery() {
		return
			(new BonusesQuery('bs'))->select([
				'bookmaker_id',
				'post_id',
			])->join([
				'bookmaker_id',
				'post_id'
			], (new BookmakersQuery('bk'))->select([
				'name',
				'rate',
				'cupis',
				'order_' . BookmakersFilter::getCountrySlug(),
                'license_ua',
                'license_by',
                'partner_link'
			]))->where([
				'post_status' => 'publish'
			])->limit(100)->orderby_string('bk.name ASC')->groupby('bookmaker_id');
	}

}
