<div class="bookmaker-bonus-item bookmaker-bonus-item--<?php print strtolower( sanitize_title( $bonus->getBonusType() ) ); ?>">
	<div class="bookmaker-bonus-item__inside">
		<div class="bonus-achievements">
			<?php if($bonus->isFinished()) : ?>
                <div class="bookmaker-bonus-item__type achievement">
                    <span class="bookmaker-bonus-item__type-text">Завершен</span>
                </div>
			<?php endif; ?>
			<?php if($bonus->achievement) : ?>
                <div class="bookmaker-bonus-item__type achievement">
                    <span class="bookmaker-bonus-item__type-text"><?php echo $bonus->achievement; ?></span>
                </div>
			<?php endif; ?>
			<div class="bookmaker-bonus-item__type">
				<span class="bookmaker-bonus-item__type-text"><?php echo str_replace(' бонус', '', $bonus->getBonusType()); ?></span>
			</div>
		</div>
		<div class="bookmaker-bonus-item__title"><?php echo $bonus->post_title; ?></div>
		<div class="bookmaker-bonus-item__buttons">
			<a class="bookmaker-bonus-item__button bookmaker-bonus-item__button--go" href="<?php echo $bonus->bookmaker->partner_link ?>" target="_blank" rel="nofollow">Получить бонус</a>
			<button class="bookmaker-bonus-item__button bookmaker-bonus-item__button--details js_open-bonus-details" data-bonus-url="<?php echo $bonus->bookmaker->partner_link ?>"></button>
		</div>
	</div>
	<div class="bookmaker-bonus-item__modal-content">
		<div class="bookmaker-bonus-item__details">
			<?php if($bonus->max_bonus) : ?>
			<div class="bookmaker-bonus-item__details-item">
				<div>Макс. бонус:</div>
				<span><?php echo $bonus->max_bonus; ?> <span title="<?php echo $bonus->getMaxBonusCurrency()->slug; ?>"><?php echo $bonus->getMaxBonusCurrency()->name; ?></span></span>
			</div>
			<?php endif; ?>
			<?php if($bonus->max_bonus) : ?>
			<div class="bookmaker-bonus-item__details-item">
				<div>Мин. депозит:</div>
				<span><?php echo $bonus->min_bonus; ?> <span title="<?php echo $bonus->getMinBonusCurrency()->slug; ?>"><?php echo $bonus->getMaxBonusCurrency()->name; ?></span></span>
			</div>
			<?php endif; ?>
		</div>
		<div class="bookmaker-bonuses-item__text">
			<?php echo apply_filters( 'the_content', do_shortcode( $bonus->post_content ) ); ?>
		</div>
	</div>
</div>
