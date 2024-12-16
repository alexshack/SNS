<?php
	Enqueue::footer('/bonuses/bonuses-slider.css');
	Enqueue::footer('/bonuses/bonus-item.css');
	Enqueue::footer('/bonuses/bonus-item-loop.css');


	if (!isset($show_title)) {
		$show_title = false;
	}
	$slug = basename( get_permalink( $bk_id ) );
	$bm_main_name   = get_post_meta( $bk_id, 'bm_main_name', true );

	$bonuses_freebet = Bonuses::setup((new BonusesFilter())->where([
	    'bookmaker_id' => $bk_id,
	    'status' => 'active',
	    'bonus_type' => '313'
	])->limit(4)->order('value')->getResults());
	$bonus_ids = [];
	if (is_array($bonuses_freebet)) {
		foreach ($bonuses_freebet as $bonus_freebet) {
			$bonus_ids[] = $bonus_freebet->post_id;
		}
	}
	$args = [
	    'bookmaker_id' => $bk_id,
	    'status' => 'active',
	    'bonus_type' => '314',
	];
	if (count($bonus_ids)) {
		$args['ID__not_in'] = $bonus_ids;
	}
	$bonuses_promocode = Bonuses::setup((new BonusesFilter())->where($args)->limit(4)->order('value')->getResults());
	if (is_array($bonuses_promocode)) {
		foreach ($bonuses_promocode as $bonus_promocode) {
			$bonus_ids[] = $bonus_promocode->post_id;
		}
	}
	$args = [
	    'bookmaker_id' => $bk_id,
	    'status' => 'active',
	];
	if (count($bonus_ids)) {
		$args['ID__not_in'] = $bonus_ids;
	}	
	$bonuses_bonus = Bonuses::setup((new BonusesFilter())->where($args)->limit(9)->order('value')->getResults());

	$bonuses = [];
	if ($bonuses_freebet) {
	    $bonuses[] = $bonuses_freebet[0];
	}
	if ($bonuses_promocode) {
		$bonuses[] = $bonuses_promocode[0]; 
	} 
	if ($bonuses_bonus) {
	    $bonuses[] = $bonuses_bonus[0]; 
	}
	if ($bonuses_freebet && count($bonuses_freebet) > 1) {
	    $bonuses = array_merge($bonuses, array_slice($bonuses_freebet, 1));
	}
	if ($bonuses_promocode && count($bonuses_promocode) > 1) {
	    $bonuses = array_merge($bonuses, array_slice($bonuses_promocode, 1));
	}
	if ($bonuses_bonus && count($bonuses_bonus) > 1) {
		$bonuses = array_merge($bonuses, array_slice($bonuses_bonus, 1, 9 - count($bonuses)));
	} 
?>

<div class="bookmaker_block bookmaker-main-bonuses" id="bookmaker-main-bonuses">
	<?php if ($show_title) { ?>
	<div class="bookmaker_block-header">
		<h2> Бонусы и акции <?php echo $bm_main_name ?></h2>
	</div>
	<?php } ?>
	<div class="bonuses-slider_wrapper">
		<div class="bonuses-slider">
			<?php 
		    if($bonuses) {
		       	$bonus_position = 1;
				foreach ($bonuses as $bonus) {
					if(!isset($button_text)) {
	            		$button_text = 'Забрать бонус';
	        		}
					Bonuses::template('/items/bonus-loop.php', ['bonus' => $bonus, 'button_text' => $button_text, 'bk_link' => false, 'bonus_position' => $bonus_position]);
					$bonus_position++;		  
				};
	        ?>

	        <?php
		    } else {
		    	echo '<div class="no-bonuses__text h4">У этого букмекера нет актуальных бонусов.</div>';
		    }         
			?>
		</div>
		<?php if ( $bonuses ) :
			$b_count = count($bonuses);
			if (count($bonuses) > 3 ) {
				$b_count = 4;
			}
			if ($b_count > 1) : ?>
		    	<div class="slider_arrow slider_arrow_prev slider_arrow_<?php echo $b_count; ?>"></div>
		    	<div class="slider_arrow slider_arrow_next slider_arrow_<?php echo $b_count; ?>"></div>
			<?php endif;
		endif;
		?>
	</div>
	<?php if ( count($bonuses) > 3 && $show_title)  : ?>
	<a class="section_button" href="<?php echo home_url() . '/bonusy-' . $slug . '/'; ?>">Все бонусы от <?php echo $bm_main_name ?>
	    <svg>
	        <use xlink:href="<?php echo SNS_URL ?>/img/icons/chevron.svg#chevron"></use>
	    </svg>
	</a>
	<?php endif; ?>
</div>