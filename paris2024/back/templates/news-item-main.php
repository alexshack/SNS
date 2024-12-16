<a href="<?php print get_permalink( $news_item->ID ); ?>" class="olympics_news-main-item">
	<?php print get_the_post_thumbnail( $news_item->ID, [], [
		'class' => 'olympics_news-main-item-image lazy lozad',
		'alt'   => $news_item->post_title
	] ); ?>
	<div class="olympics_news-main-item-info">
		<div class="olympics_news-main-item-header">
			<?php if ( $tags = get_the_tags( $news_item->ID ) ): ?>
				<?php $tag = array_shift( $tags ); ?>
				<div class="olympics_news-main-item-tag olympics__news-item-tag--<?php print sanitize_title( $tag->name ); ?>">
					<?php print $tag->name; ?>
				</div>
			<?php endif; ?>
			<div class="olympics_news-main-item-date">
				<?php print mysql2date( 'd.m.Y в H:i', $news_item->post_date ); ?>
			</div>			
		</div>
		<div class="olympics_news-main-item-text">
			<?php print $news_item->post_title; ?>
		</div>		
	</div>
</a>