<div class="bonus_item bonus_item_half bonus_item_predict">
	<div class="bonus_item_content">
		<div class="bonus_item_content_title"><?php echo $predict->getBetType(); ?></div>
		<div class="bonus_item_inputs">
			<div class="bonus_item_form">
				<input type="number" class="bonus_item_form_input" placeholder="Ставка" id="pr_bet_price" oninput="PredictsFilter.calcPredict(this);" min="1000" max="100000" step="100">
				<span>X</span>
				<div id="bonus_item_form_bet" data-bet="<?php echo $predict->getMaxBet(); ?>"><strong><?php echo $predict->getMaxBet(); ?></strong></div>
				<span>=</span>
				<div id="bonus_item_form_summ">Выигрыш</div>
			</div>
			<a target="_blank" rel="nofollow" href="<?php echo $predict->getBetLink($predict->bookmaker->ID); ?>" class="bonus_item_inputs_link">
				Сделать ставку
			</a>
		</div>
	</div>

	<div class="bonus_item_head">
		<?php 
		echo ($bonus->bookmaker->thumbnail) ? $bonus->bookmaker->imageLinkMarkup('bonus_item_', '', '105x70') : ''; 		
		if($bonus->getBonusValue()) {
			echo '<div class="bonus_item_value">';
    		echo $bonus->getBonusValue();
    		echo '</div>';
		}
		?>
	</div>

	<div class="bonus_item_buttons">
		<?php
		if ( $bonus->getPromoCode() ) {
			echo '<div class="bonus_item_promo promo-code-action bk-promo-code"' . $bonus->getPromoAttr() . '>';
			echo $bonus->getPromoCode();
			echo '<svg class="bonus_item_promo_img"><use xlink:href="' . SNS_URL . '/img/icons/copy2.svg#copy2"></use></svg>';
			echo '</div>';
		}
		echo $bonus->bookmaker->partnerLinkMarkup('Получить бонус', ['class' => 'bonus_item_get']); 
		?>
	</div>
</div>
