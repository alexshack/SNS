<div class="predicts-item predicts-item-table <?php echo $pr_class; ?>">
	<div class="predicts-item__header">
		<div class="predicts-item__image">
			<?php echo $predict->getThumbnailImg('attachment-post-thumbnail size-post-thumbnail wp-post-image'); ?>
			<span class="predicts-item__type"><?php echo $predict->tournament; ?></span>
			<?php if ($predict->date): ?>
				<span class="predicts-item__datetime"><?php echo date( 'd.m.Y в H:i', $predict->date ); ?></span>
			<?php endif; ?>			
			<?php if ($pr_inside ): ?>
				<span class="predicts-item__inside">инсайд</span>
			<?php endif; ?>
			<?php if ($pr_has_day ): ?>
				<span class="predicts-item__day">ставка дня</span>
			<?php endif; ?>				
		</div>
		<div class="predicts-item__name">
			<?php echo $predict->post_title; ?>
		</div>
		<a class="predicts-item__link" href="<?php echo $predict->getPermalink(); ?>" aria-label="<?php echo $predict->post_title; ?>" title="<?php echo $predict->post_title; ?>"></a>
	</div>
	<div class="predicts-item__content">
		<div class="predicts-item__bet_type">
			<?php echo $predict->bet_type . ' ' . $predict->metadata->get('pr_bet_additional'); ?>
			
		</div>
		<div class="predicts-item__votes">
            <span class="predicts-item__votes-line">
                <span class="predicts-item__votes-line_fill" style="width: <?php echo $predict->getPercentVotes('yes'); ?>%"></span>
            </span>
            <div class="predicts-item__votes-footer">
            	<div>
					<span class="predicts-item__votes-percent"><?php echo $predict->getPercentVotes('yes'); ?>%</span> голосов «За»
				</div>
				<div class="predicts-item__votes-bet"><?php echo $predict->max_bet; ?></div>
			</div>
		</div>
		<?php if ( $predict->bookmaker ): ?>
			<?php if ( $predict->hasPromoCode() ) : ?>
				<div class="predicts-item__promo-code-outer promo-code-action bk-promo-code" <?php echo $predict->bookmaker->promo_attr; ?>>
					<div class="predicts-item__promo-code bk-promo-code" <?php echo $predict->bookmaker->promo_attr; ?>>
						<?php echo $predict->bookmaker->promo_data->getOption( 'promocode' ) ?>
						<img class="predicts-item__cursor bk-promo-code" <?php echo $predict->bookmaker->promo_attr; ?>
						     src="<?php echo SNS_URL; ?>/img/cursor.png" alt="Скопировать промокод">
						<svg class="predicts-item__promo-code-svg bk-promo-code" <?php echo $predict->bookmaker->promo_attr; ?>>
							<use xlink:href="<?php echo SNS_URL; ?>/img/promo-code-white.svg#promo-code-white"></use>
						</svg>
					</div>
				</div>
			<?php else : ?>
				<a <?php echo getBookmakerLinkAttr($predict->bookmaker->ID, 'predicts-item__promo-code predicts-item__not-promo', '') ?>
					href="<?php echo $predict->bookmaker->getPartnerLink(); ?>" target="_blank" rel="nofollow">
					Бонус на матч
					<img class="predicts-item__cursor" src="<?php echo SNS_URL; ?>/img/cursor.png" alt="Бонус по ссылке">
				</a>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
