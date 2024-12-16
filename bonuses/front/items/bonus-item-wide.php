<div class="bonus_item bonus_item_wide">
	<div class="bonus_item_head">
		<?php 
		echo ($bonus->bookmaker->thumbnail) ? $bonus->bookmaker->imageLinkMarkup('bonus_item_', '', '105x70') : ''; 		
		?>
	</div>
	<div class="bonus_item_data">
		<div class="bonus_item_content">
			<?php echo $bonus->getBonusText(); ?>
			<?php if($bonus->getBonusValue()) {
				echo '<div class="bonus_item_value_block">';
				echo '<span>Бонус</span>';
				echo '<div class="bonus_item_value">';
	    		echo $bonus->getBonusValue();
	    		echo '</div>';
	    		echo '</div>';
			} ?>	
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
</div>
