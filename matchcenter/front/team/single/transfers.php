<?php
/**
 * Template part for team transfers page SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

$news_args = [
   'title' => 'Новости ' . $team->post->post_title,
   'link'  => false,
   'posts' => $team->getNews( 6 )   
];


$transfer_args = array(
   'limit'     => 10,
   'title'     => 'Трансферы ' . $team->post->post_title,
   'title_tag' => 'h2',
   'team'      => $team_id,
   'season'    => $season_ids,
   'show_more' => true
);

$content_transfers = apply_filters( 'the_content', get_post_meta( $team_id, 'content_transfers', true ) );

?>

<div class="sp_block">
   <?php sp_get_template( 'transfer-rows.php', $transfer_args, SP()->template_path() . 'transfer/',  ); ?>
</div>

<?php if ( !empty( $content_transfers ) ) : ?>
<div class="sp_block">
   <?php echo $content_transfers; ?>
</div>
<?php endif; ?>

<?php sp_get_template( 'news-blocks.php', $news_args, SP()->template_path() . 'news/',  ); ?>

