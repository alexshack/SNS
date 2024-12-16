<?php
class ExpressFilter {
	protected $posts_query;
	protected $express_query;
	protected $order;
	protected $limit = 48;
	protected $offset;
	protected $where;
	protected $where_string;

	function __construct($where = [], $arguments = []) {
		$this->where = $where;
		$this->setupArguments(new ArrayHandler($arguments))
		     ->setupPostsQuery()
			 ->setupExpressQuery();

		return $this;
	}

	protected function setupArguments(ArrayHandler $arguments) {
		$this->limit = $arguments->get('limit', 48);
		$this->order = $arguments->get('order', 'date');
		$this->offset = $arguments->get('offset');
		$this->where_string = $arguments->get('where_string', '');

		return $this;
	}

	protected function setupPostsQuery() {
		$this->posts_query = $this->getPostsQuery();
		return $this;
	}

	protected function setupExpressQuery() {
		if ( empty($this->where_string) ) {
			$this->express_query = $this->getExpressQuery();
		} else {
			$this->express_query = $this->getExpressQuery()->where_string($this->where_string);
		}
		return $this;
	}

	function getPostsQuery() {
		return (new PostsQuery('p'))->select([
			'ID',
			'post_type',
			'post_name',
			'post_title',
		])->where([
			'post_type' => 'express'
		]);
	}

	function getExpressQuery() {
		return (new ExpressQuery('pr'))->select([
			'coef',		
		]);
	}

	function getSQL() {
		$sql = $this->getQuery()->get_sql();
		$this->reload();
		return $sql;
	}

	function getResults() {
		$results = $this->getQuery()->get_results(true);
		$this->reload();
		return ($results === null) ? [] : $results;
	}

	function getQuery() {

		if(isset($this->where['post_status'])) {
			$this->posts_query->where([
				'post_status' => $this->where['post_status']
			]);
		}

		$query = $this->joinQueries()->where($this->where)->limit($this->limit)->orderby_string($this->getOrderString());
		if($this->offset) {
			$query->offset($this->offset);
		}
		return $query;
	}

	function getCount() {
		$count = $this->joinQueries()->where($this->where)->get_count('post_id');
		$this->reload();
		return $count;
	}

	protected function reload() {
		return $this->setupPostsQuery()->setupExpressQuery();
	}

	protected function getOrderString() {

		switch ($this->order) :
			case 'date' :
				return 'pr.date DESC';
			case 'max_coef' :
				return 'pr.coef+0 DESC, pr.date DESC';
		endswitch;
	}

	protected function joinQueries() {
		return $this->express_query->join([
			'post_id',
			'ID'
		], $this->posts_query);
	}
}