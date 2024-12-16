<?php
class BonusesUnion extends DataUnion {

	public $number = 20; //кол-во к выводу

	public $dataType = 'bookmakers'; //идентификатор типа контента

	protected $model = BookmakersModel::class; //указываем основную модель

	public $postData = ['post_title', 'post_type', 'post_name', 'comment_count'];

	public $top = [];

	public $get_top = false; // включить очередь true

	function __construct( $args = [] ) {

		parent::__construct( $args );

		if ( isset( $args['number'] ) ) {

			$this->number = $args['number'];

		}

		if ( isset( $args['post_data'] ) ) {

			$this->postData = array_merge( $this->postData, $args['post_data'] );

		}

		$this->get_top = ( isset( $args['get_top'] ) ) ? $args['get_top'] : false;
	}

	//метод с основными правилами выборки,
	//справедливые для всех условий
	function getMainQuery( $args = [] ) {

		$query = $this->model::query( 'bookmakers' );

		if ( $args ) {
			$query->parse( $args );
		}

		return $query->join(['post_id', 'ID' ], ( new PostsQuery( 'posts' ) )
			->select($this->postData)
			->where( [
				'post_status' => 'publish'
			] ) )
		             ->join([
			             'post_id',
			             'bookmaker_id'
		             ], BonusesModel::query('bonuses')->select(

		             )->where_string('(bonuses.date_end >= "' . date('Y-m-d') . '" OR bonuses.date_unlimited=1)'))->orderby_string();
	}

	function getFilterRules() {

		return [
			// запрос для среза по метаданным
			'currencies'            => function ( $query, $value ) {
				return $query->join(
					'post_id', BookmakersMetaModel::query( 'currencies' )->where( [ 'meta_key' => 'currencies', 'meta_value__in' => $value ] )
				);
			}
		];
	}

	//очередь формирования списка
	function getQueueQuery() {
		// надо получить объединенный топ - ручное указание и остальные
		if ( $this->get_top ) {
			return [
				'getTopQuery',
				'getGeneralQuery',
			];
		}
	}

	//условие для выборки на позицию в очереди
	function getTopQuery( $defaultFilters = [] ) {

		$query = $this->filterQuery( $this->getMainQuery(), $defaultFilters );

		if ( ! $query ) {
			return false;
		}

		// БК выводимые первыми (в зависимости от гео)
		$leaders = get_option( pbk_get_manual_rating_field_name() );
		if ( empty( $leaders ) ) {
			return false;
		}

		$this->top = $leaders;

		$query->where( [ 'post_id__in' => $this->top ] )->orderby_case( 'post_id', $this->top );

		return $query;
	}

	//условие для выборки на позицию в очереди
	function getGeneralQuery( $defaultFilters = [] ) {

		$query = $this->filterQuery( $this->getMainQuery(), $defaultFilters );

		if ( ! $query ) {
			return false;
		}


		if ( $this->top ) {
			$query->where( [ 'post_id__not_in' => $this->top ] );
		}

		return $query;
	}


	//указание методов для заполнения дополнительных данных объекта модели
	//ключ - наименование доп. свойства модели
	//значение - имя метода
	//уже зарезервиновано: metadata, thumbnails
	function getExtraData() {

		return [];

	}


}
