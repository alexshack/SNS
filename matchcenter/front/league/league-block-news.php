<?php
/**
 * Template part for league block for news SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

$main_page_slug = get_option('sportspress_sns_main_page', '');
$league_slug = get_term_link($league);

$news_term_id = get_term_meta( $league->term_id, 'news_term', 1 );
if ( $news_term_id && $news_term_id > 0 ) {
    $news_term_link = get_term_link( (int)$news_term_id, 'category' );
}

$image_id = get_term_meta( $league->term_id, '_thumbnail_id', 1 );
$image_url = wp_get_attachment_url( $image_id );
$league_tabs = [
    'table/'  => 'Таблица',
];

?>

<div class="sp_league_block_news">
    <a class="sp_league_block_btn" href="<?php echo $league_slug; ?>">
        <img class="lazy lozad" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $image_url; ?>" alt="<?php echo $league->name; ?>" width="20" height="20">
        <?php echo $league->name; ?>
    </a>
    <?php if ( isset( $news_term_link ) ) : ?>
        <a class="sp_league_block_btn" href="<?php echo $news_term_link ?>">
            <img class="lazy lozad" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $image_url; ?>" alt="<?php echo $league->name; ?>" width="20" height="20">
            <?php echo $league->name; ?> Новости
        </a>
    <?php endif; ?>
    <?php foreach($league_tabs as $slug => $league_tab) : ?>
        <a class="sp_league_block_btn" href="<?php echo $league_slug . $slug; ?>">
            <img class="lazy lozad" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $image_url; ?>" alt="<?php echo $league->name; ?>" width="20" height="20">
            <?php echo $league->name . ' ' . $league_tab; ?>
        </a>
    <?php endforeach; ?>
</div>