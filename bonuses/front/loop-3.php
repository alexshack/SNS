<div class="side-bonus">
	<div class="side-bonus__head">
		<?php echo ($bonus->bookmaker->thumbnail) ? $bonus->bookmaker->imageLinkMarkup('side-bonus__', '', '105x70') : ''; ?>
		<?php echo $bonus->getBonusType(); ?>
	</div>
	<div class="side-bonus__content">
		<?php echo $bonus->getBonusText(); ?>
		<?php echo $bonus->bookmaker->partnerLinkMarkup('Получить бонус', [
			'class' => 'side-bonus__btn'
		]); ?>
	</div>
</div>
