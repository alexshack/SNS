<?php

	if (!isset($show_title)) {
		$show_title = false;
	}

	$home_bonuses = get_option('home_bonuses');

	$bonuses = Bonuses::setup((new BonusesFilter())->where([
		'status' => 'active',
	    'ID__in' => $home_bonuses
	])->limit(12)->order('popular')->getResults());
?>

<div class="bookmaker_block bookmaker-main-bonuses bonuses_main">
	<?php if ($show_title) { ?>
		<div class="bookmaker_block-header">
			<h2><?php echo $slider_title ?></h2>
		</div>
	<?php } ?>
	<div class="bonuses-slider" id="bonus-slider-<?php echo $bonus_type; ?>">
		<?php 
	    if($bonuses) {
	       	$bonus_position = 1;
			foreach ($bonuses as $bonus) {
				if(!isset($button_text)) {
            		$button_text = 'Забрать бонус';
        		}
				Bonuses::template('/items/bonus-loop.php', ['bonus' => $bonus, 'button_text' => $button_text, 'bonus_position' => $bonus_position]);
				$bonus_position++;
		  
			}; 
        ?>

        <?php
	    } else {
	        Bookmakers::template('no-bonuses.php', ['text' => 'У этого букмекера нет актуальных бонусов. Рекомендуем лучшие бонусы топовых БК.']);
	    }         
		?>
	</div>
</div>