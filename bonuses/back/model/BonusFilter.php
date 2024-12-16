<?php
class BonusFilter {
	public $query;
	public $type_query;
	public $bk_query;
	public $sub_where = 'bonus.post_type = "bonuses"';
	public $order = [];
	public $group_by = ['bk.meta_value', 'bonus.ID', 'bonus.post_title', 'bonus.post_date', 'expiration_date.meta_value', 'indefinitely_date.meta_value', 'bk.meta_key'];

	function __construct() {

		$this->query = (new PostsQuery('bonus'))->select_string('
			DISTINCT
		    bonus.ID as bonus_id,
	        bonus.post_title as bonus_title,
	        bonus.post_date as bonus_date,
	        expiration_date.meta_value as expiration_date,
	        bk.meta_value as bk_id,
	        indefinitely_date.meta_value as indefinitely_date
	    ')->join([
	    	'ID', 'post_id'
		], (new PostMetaQuery('expiration_date'))->where([
			'meta_key' => 'bs_date_end'
		]))->join([
			'ID', 'post_id'
		], (new PostMetaQuery('indefinitely_date'))->where([
			'meta_key' => 'bs_date_no_limit'
		]))->where([
			'post_type'     => 'bonuses',
			'post_status'   => 'publish'
		]);

		$this->bk_query = (new PostMetaQuery('bk'))->where([
			'meta_key'  => 'bs_bm_id'
		]);

		return $this;
	}

	function bookmaker($post_id) {
		$this->setSubWhere('bk.meta_value IN (' . $this->getStringValue($post_id) . ')', 'AND');
		return $this;
	}

	function type($bonus_type) {
		$this->type_query = (new TermRelationshipsQuery('type'));
		$this->query->join([
			'ID', 'object_id'
		], $this->type_query);
		$this->setSubWhere('type.term_taxonomy_id IN (' . $this->getStringValue($bonus_type) . ')', 'AND');
		return $this;
	}

	function bonus($bonus_id = []) {
		$this->setSubWhere('bonus.ID IN (' . $this->getStringValue($bonus_id) . ')', 'OR');
		return $this;
	}

	function orderByWeight() {
		$weight_query = (new PostMetaQuery('weight'))->where([
			'meta_key' => 'weight'
		]);
		$this->query->select_string('
		    weight.meta_value as weight
        ')->join([
			'ID', 'post_id'
		], $weight_query);
		$this->order[] = "weight.meta_value+0 DESC";
		$this->group_by[] = 'weight.meta_value';
		return $this;
	}

	function orderByID($post_ids) {
		$order = [];
		foreach ($post_ids as $post_id) {
			$order[] = 'bonus.ID = ' . $post_id . ' DESC';
		}
		$order = array_reverse($order);
		foreach ($order as $o) {
			array_unshift($this->order, $o);
		}
		return $this;
	}

	function orderByBookmakerRate() {
		$this->query->select_string('rate.meta_value as rate, cupis.meta_value');
		$this->bk_query->join([
			'meta_value',
			'post_id'
		], (new PostMetaQuery('rate'))->where([
			'meta_key' => 'bm_editorial_rating'
		]))->join([
			'meta_value',
			'post_id'
		], (new PostMetaQuery('cupis'))->where([
			'meta_key' => '_cupys_meta_key'
		]));
		$this->order[] = "cupis.meta_value DESC";
		$this->order[] = "rate.meta_value+0 DESC";
		$this->group_by[] = 'rate.meta_value, cupis.meta_value';
		return $this;
	}

	function getStringValue($value) {
		$values = [];
		if(is_array($value)) {
			foreach ($value as $v) {
				$values[] = '"' . $v . '"';
			}
		} else {
			$values[] = '"' . $value . '"';
		}
		return implode(',', $values);
	}

	protected function updateWhere() {
		if(!empty($this->sub_where)) {
			$this->query->where_string('(' . $this->sub_where . ')');
		}
	}

	protected function setSubWhere($string, $operator) {
		if(!empty($this->sub_where)) {
			$this->sub_where .= ' ' . $operator . ' ' . $string;
		} else {
			$this->sub_where .= ' ' . $string;
		}
	}

	function orderByDefault() {
		$this->order[] = "STR_TO_DATE(expiration_date.meta_value, '%d.%m.%Y') >= '" . date('Y-m-d') . "' OR indefinitely_date.meta_value = 'yes' DESC";
		return $this;
	}

	function orderByPostDate() {
		$this->order[] = "bonus.post_date DESC";
		return $this;
	}

	function orderByBonusValue() {
		$this->query->join([
			'ID', 'post_id'
		], (new PostMetaQuery('bonus_value'))->where([
			'meta_key' => 'bs_value'
		]));
		$this->order[] = "bonus_value.meta_value+0 DESC";
		return $this;
	}

	function setQueryData($limit = 30, $offset = 0) {
		$this->query->join(
			['ID', 'post_id'],
			$this->bk_query
		);
		$this->order[] = "bk.meta_value DESC";
		$this->order[] = "STR_TO_DATE(expiration_date.meta_value, '%d.%m.%Y') DESC";
		$this->query->limit($limit)->offset($offset)->orderby_string(implode(', ', $this->order))->groupby(implode(', ', $this->group_by));
		return $this;
	}

	function getResults($limit = 30, $offset = 0, $count = false) {
		$this->updateWhere();
		if($count) {
			$this->query->join(
				['ID', 'post_id'],
				$this->bk_query
			);
			return $this->query->get_count();
		} else {
			$this->setQueryData($limit, $offset);
			return $this->query->get_results();
		}
	}

	function getCount() {
		return $this->getResults(30, 0, true);
	}

	function getSQL($limit = 30, $offset = 0) {
		$this->updateWhere();
		$this->setQueryData($limit, $offset);
		return $this->query->get_sql();
	}
}
