<?php
/**
 * Event Filter
 *
 * @author      Alex Torbeev
 * @package     SportsPress/Templates
 * @version   2.7.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}



$defaults = array(
	'sports'               => SP_SNS_Theme::getSports(),
	'leagues'              => get_terms( ['taxonomy' => 'sp_league', 'fields' => 'ids'] ),
	'date_from'            => date('Y-m-d'),
	'date_to'              => date('Y-m-d'),
	'status'               => 'default',
	'show_terms'           => false,
	'league_terms'         => false,
	'team'                 => null
);

extract( $defaults, EXTR_SKIP );

?>

<?php if ( $show_terms ) :

$news_terms     = [];
$articles_terms = [];
$predicts_terms = [];
$news_link      = get_term_link( 38, 'category' );
$articles_link  = get_term_link( 1840, 'category' );
$predicts_link  = get_permalink( 9881 );
$news_count     = 0;
$articles_count = 0;
$predicts_count = 0;

/*if ( $league_terms ) {

	if ( !empty( $term = get_term_meta( $leagues[0], 'news_term', true ) ) ) {
		$news_terms[] = $term;
		$news_link    = get_term_link( (int)$term, 'category' );
	}
	if ( !empty( $term = get_term_meta( $leagues[0], 'articles_term', true ) ) ) {
		$articles_terms[] = $term;
		$articles_link    = get_term_link( (int)$term, 'category' );
	}
	if ( !empty( $term = get_term_meta( $leagues[0], 'predicts_term', true ) ) ) {
		$predicts_terms[] = $term;
		$predicts_page    = get_term_meta( $term, 'term_slice', true);
		$predicts_link    = get_permalink( $predicts_page );		
	}

} else {

	foreach ( $sports as $sport ) {
		if ( $sport->news_term ) {
			$news_terms[]     = $sport->news_term->term_id;
		}
		if ( $sport->predicts_term ) {
			$predicts_terms[] = $sport->predicts_term->term_id;
		}
		if ( $sport->articles_term ) {
			$articles_terms[] = $sport->articles_term->term_id;
		}		
	}

	if ( count( $sports ) == 1 ) {
		if ( count( $news_terms ) ) {
			$news_link     = get_term_link( $news_terms[0], 'category' );
		}
		if ( count( $articles_terms ) ) {
			$articles_link = get_term_link( $articles_terms[0], 'category' );
		}
		if ( count( $predicts_terms ) ) {
			$predicts_page = get_term_meta( $predicts_terms[0], 'term_slice', true);
			$predicts_link = get_permalink( $predicts_page );			
		}
	}
}

foreach ( $news_terms as $term_id ) {
	$model = new TermsModel($term_id);
	$news_count = $news_count + $model->getPosts( ['get_from_cache' => false, 'get' => 'count' ] );
}

foreach ( $articles_terms as $term_id ) {
	$model = new TermsModel($term_id);
	$articles_count = $articles_count + $model->getPosts( ['get_from_cache' => false, 'get' => 'count' ] );
}

$where = [
    'post_status' => 'publish', 
    'sport__in'   => $predicts_terms
];	

$predicts_filter = new PredictsFilter($where, ['limit' => -1]);
$predicts_count = $predicts_filter->getCount();
*/
?>
<!-- 	<div class="sp_block_title_btns">
		<div class="sp_block_title_btn active">Матч-центр</div>
		<?php if (  $news_count  ) : ?>
			<a href="<?php echo $news_link; ?>" class="sp_block_title_btn">Новости<span><?php echo $news_count ?></span></a>
		<?php endif; ?>
		<?php if ( $articles_count ) : ?>
			<a href="<?php echo $articles_link; ?>" class="sp_block_title_btn">Статьи<span><?php echo $articles_count ?></span></a>
		<?php endif; ?>
		<?php if ( $predicts_count ) : ?>
			<a href="<?php echo $predicts_link; ?>" class="sp_block_title_btn">Прогнозы<span><?php echo $predicts_count ?></span></a>
		<?php endif; ?>
	</div> -->

<?php endif; ?>
<?php foreach ( $sports as $sport ) : ?>
	<?php if ( is_array( $sports ) && count( $sports ) > 1 ) : ?>
		<a href="<?php echo $sport->url; ?>"><h2 class="sp_inner_title sp_event_filter_title"><?php echo $sport->name; ?></h2></a>
	<?php endif; ?>
	<?php
	foreach ( $leagues as $league_id ) {
		$league = new SP_SNS_league( $league_id );
		if ( $league->sport_type != $sport->type ) continue; 
        if ( $league->image_url ) {
            $image = '<img class="lazy sp_event_rows_title_img" data-src="' . $league->image_url . '" alt="' . $league->term->name . '">';
        } else {
            $image = '';
        }
        $league_title = '<a href="' . $league->url . '">' . $image . $league->term->name . '</a>';

        $events_args = array(
			'league'        => $league_id,
			'date'          => 'range',
			'date_from'     => $date_from,
			'date_to'       => $date_to,
			'status'        => $status,
			'accord'        => true,
			'accord_open'   => true,
			'title'         => $league_title,
			'show_title'    => true,
			'title_tag'     => 'h3',
 			'show_date'     => true,
			'show_time'     => true,
			'show_league'   => false,
			'show_matchday' => false,
			'hide_if_empty' => true, 
			'orderby'       => 'date',
			'team'          => $team			
		);
        sp_get_template( 'event-rows.php', $events_args, SP()->template_path() . 'event/'  );
 	}
 	?>
<?php endforeach; ?>