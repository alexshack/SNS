<?php
/**
 * Template part for predicts block SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

?>
<div class="predicts-item predicts-item-table sp_predicts_block" >
	<div class="predicts-item__header">
		<div class="predicts-item__image">
			<img width="298" height="228" class="lazy lozad attachment-post-thumbnail size-post-thumbnail wp-post-image" data-src="<?php echo $predict->image ?>" alt="<?php echo $predict->title; ?>">
			<?php if ( $predict->tournament ): ?>
				<span class="predicts-item__type"><?php echo $predict->tournament; ?></span>]
			<?php endif; ?>
			<?php if ( $predict->date ): ?>
				<span class="predicts-item__datetime"><?php echo $predict->date; ?></span>
			<?php endif; ?>			

		</div>
		<div class="predicts-item__name">
			<?php echo $predict->title; ?>
		</div>
		<a class="predicts-item__link" href="<?php echo $predict->url; ?>" aria-label="<?php echo $predict->title; ?>" title="<?php echo $predict->title; ?>"></a>
	</div>
	<div class="predicts-item__content">
		<div class="predicts-item__bet_type">
			<?php echo $predict->stake; ?>
		</div>
		<?php if ( $predict->bookmaker ): ?>
            <a class="predicts-item__promo-code predicts-item__not-promo" href="<?php echo $predict->bookmaker->link; ?>" target="_blank" rel="nofollow">
                Бонус на матч
            </a>
		<?php endif; ?>
		<a class="sp_predicts_block_link" href="<?php echo $predict->url; ?>" title="<?php echo $predict->title; ?>">Читать прогноз</a>		
	</div>
</div>
