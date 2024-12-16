<?php

class ExpressBookmakerMetaBox extends MetaBox {

	function __construct( $id, $args = [] ) {
		parent::__construct( $id, $args );
	}

	function get_content( $post ) {

		$content = '';
		$content .= APF::setup( 'date', [
			'id'    => 'express_date',
			'title' => 'Дата экспресса',
			'width' => 'full',
			'value' =>  wp_date('Y-m-d', get_post_meta($post->ID, 'express_date', 1))
		] )->get_html();	
		$content .= APF::setup( 'select', [
			'id'    => 'bk_id',
			'title' => 'Букмекер',
			'width' => 'full',
			'empty_first'   => 'Ничего не выбрано',
			'search'    => 1,
			'values' => Values::getPosts('bookmakers'),
			'value' => get_post_meta($post->ID, 'bk_id', 1)
		] )->get_html();
		$content .= APF::setup( 'url', [
			'id'    => 'express_url',
			'title' => 'Ссылка на экспресс',
			'width' => 'full',
			'value' => get_post_meta($post->ID, 'express_url', 1)
		] )->get_html();
		if(get_post_meta($post->ID, 'express_coef', 1)) {
			$content .= '<div class="apf-field"><label class="apf-field__title">Итоговый кэф</label><div class="apf-field__content apf-field__content_full">' . get_post_meta($post->ID, 'express_coef', 1) . '</div></div>';
		}
				
		return $content;

	}

	function update( $post_id ) {
		Expresses::load('Express');
		$express = new Express($post_id);	

		$keys = [
			'bk_id',
			'express_url'			
		];
		foreach ($keys as $key) {
			if(isset($_POST[$key])) {
				update_post_meta($post_id, $key, $_POST[$key]);
			}
		}
		if(isset($_POST['express_date'])) {
			$express->updateTable(['date' => strtotime($_POST['express_date'])]);
			update_post_meta($post_id, 'express_date', strtotime($_POST['express_date']));
		}


	}

}
