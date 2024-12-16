<?php

class ExpressGamesMetaBox extends MetaBox {

	function __construct( $id, $args = [] ) {
		parent::__construct( $id, $args );
		$this->init_repeater( 'express_games', new APF_Repeater( [
			APF::setup( 'select', [
				'title'     => 'Вид спорта',
				'id'        => 'sport_type',
				'search'    => true,
				'values'	=> Values::getTerms('sport-type', ['hide_empty' => false])
			] ),
			APF::setup( 'select', [
				'title'     => 'Турнир',
				'id'        => 'tournament',
				'search'    => true,
				'values'	=> Values::getTerms('tournament', ['hide_empty' => false])
			] ),			
			APF::setup( 'text', [
				'title'     => 'Название матча',
				'id'        => 'match'
			] ),
			APF::setup( 'text', [
				'title'     => 'Время',
				'id'        => 'time'
			] ),
			APF::setup( 'text', [
				'title'     => 'Ставка',
				'id'        => 'bet'
			] ),
			APF::setup( 'text', [
				'title'     => 'Кэф',
				'id'        => 'coef'
			] ),
		] ) );		
	}


	function get_content( $post ) {
		$content = '';
		$content .= $this->repeater_content( 'express_games', [
			'values'  => get_post_meta($post->ID, 'express_games', 1),
			'button'  => 'Добавить',
			'item_id' => 'express_games'
		] );

		$this->get_chosen_scripts();

		return $content;
	}

	function update( $post_id ) {
		Expresses::load('Express');
		$express = new Express($post_id);		

		if(isset($_POST['express_games'])) {
			$value = $_POST['express_games'];
			
			if ( ! empty( self::$groups['express_games'] ) ) {
				$matches = $this->repeater('express_games')->setup_value( $value );
				$coef = 1;
				$text = '';
				foreach ($matches as $match) {
					$match['coef'] = str_replace(',', '.', $match['coef']);
					$coef = $coef * $match['coef'];
				}				
				$value = $matches;
				$coef = number_format($coef, 2, '.', ' ');
				$express->updateTable(['coef' => $coef]);
				update_post_meta($post_id, 'express_coef', $coef);
			}
			update_post_meta($post_id, 'express_games', $value);

		}

	}

}
