<div class="bonus_item bonus_item_menu">
	<?php echo ($bonus->bookmaker->thumbnail) ? $bonus->bookmaker->imageLinkMarkup('bonus_item_', '', '105x70') : ''; ?>

	<div class="bonus_item_buttons">
		<div class="bonus_item_head">
			<svg class="bonus_item_bonus_img"><use xlink:href="<?php echo SNS_URL; ?>/img/icons/gift-menu.svg#gift-menu"></use></svg>
			<?php 
			if($bonus->getBonusValue()) {
				echo '<div class="bonus_item_value">';
				echo $bonus->getBonusValue();
				echo '</div>';
			}
			?>
		</div>		
		<?php
		
		echo $bonus->bookmaker->partnerLinkMarkup('На сайт', ['class' => 'bonus_item_get']); 
		?>
	</div>
</div>