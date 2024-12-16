<?php
/**
 * Template part for news block SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

?>

<?php
	$image = get_the_post_thumbnail_url( $item->ID, 'thumbnail' );

?>

<div class="sp_news">
	<a href="<?php echo get_permalink( $item ) ?>" class="sp_news_image">
		<img width="355" height="355" class="lazy lozad " data-src="<?php echo $image; ?>" alt="<?php echo $item->post_title ?>">
	</a>
	<div class="sp_news_content">
		<div class="sp_news_date"><?php echo wp_date( 'j F Y', strtotime( $item->post_date ) ); ?></div>
		<a href="<?php echo get_permalink( $item ) ?>" class="sp_news_title"><?php echo $item->post_title ?></a>
	</div>
</div>