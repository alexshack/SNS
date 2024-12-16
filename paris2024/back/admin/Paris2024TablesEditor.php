<?php

class Paris2024TablesEditor extends AdminPage {

	function __construct( $id, $args = [] ) {
		parent::__construct( $id, $args );
	}

	function page_content() {

		$this->init_scripts();

		$content = '<div class="wrap pkr-page">';
		$content .= '<h1>' . $this->title . '</h1>';

		if ( ! isset( $_GET['sport-id'] ) ) {

			$content .= '<form method="get">';
			$content .= '<div class="postbox">';

			$values = [
				'Выберите вид спорта',
				859  => 'Хоккей',
				3078 => 'Кёрлинг'
			];

			$content .= APF::setup( 'select', [
				'id'     => 'sport-id',
				'title'  => __( 'Вид спорта' ),
				'values' => $values,
			] )->get_html();

			$content .= APF::setup( 'hidden', [
				'id'    => 'page',
				'value' => 'paris2024-tables-editor',
			] )->get_html();

			$content .= '</div>';

			$content .= '<div class="submit-wrap">' . get_submit_button() . '</div>';
			$content .= '</form>';

		} else {
			$content .= '<style>input[type="text"],input[type="number"]{width: 100px}</style>';

			$content .= '<form method="post">';
			$content .= APF::setup('select', [
				'title' => 'Отключить парсер для данного вида спорта',
				'id'    => 'paris2024_parser_status',
				'input_name'  => 'paris2024_parser_status',
				'values' => [
					'on' => 'Включен',
					'off' => 'Выключен'
				],
				'value' => Paris2024Parser::getStatus($_GET['sport-id'])
			])->get_html();
			$content .= APF::setup( 'hidden', [
				'id'    => 'sport-id',
				'value' => $_GET['sport-id'],
			] )->get_html();
			$content .= '<div class="postbox" style="padding:20px">';

			$content .= $this->get_content();

			$content .= '</div>';

			$content .= $this->get_nonce_field();
			$content .= '<div class="submit-wrap">' . get_submit_button() . '</div>';
			$content .= '</form>';
		}

		$content .= '</div>';
		echo $content;
	}

	function get_content() {

		$sport_id    = $_GET['sport-id'];
		$templateUri = '/inc/paris2024/templates/admin/table.php';

		$tables = Paris2024Table::query()->where( [
			'sport_id' => $sport_id
		] )->limit( - 1 )->order( 'ASC' )->get_results();

		$content = '';
		foreach ( $tables as $table ) {
			$content .= Template::get( $templateUri, [
				'table' => $table,
				'sport' => get_term( $sport_id )
			] );
		}

		return $content;

	}

	function update() {

		if ( empty( $_POST['tables'] ) || empty( $_POST['sport-id'] ) ) {
			return false;
		}

		foreach ( $_POST['tables'] as $table_id => $table ) {

			$content = [];
			foreach ( $table['body'] as $r => $cols ) {
				foreach ( $cols as $k => $col ) {
					if ( ! empty( $col ) && $table['head'][ $k ] == 'Команда' ) {
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

			Paris2024Table::updateData( [
				'table_content' => $content
			], [
				'tid' => $table_id
			] );


		}
	
		if(isset($_POST['paris2024_parser_status']) && isset($_GET['sport-id'])) {
			update_option('paris2024_parser_status_' . $_GET['sport-id'], $_POST['paris2024_parser_status']);
		}

	}

}
