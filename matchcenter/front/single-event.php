<?php
/**
 * Template for single event SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */
get_header();

$tab = 'main';
if ( isset( $wp_query->query['tab'] ) ) {
	$tab = $wp_query->query['tab'];
}

$id = get_the_ID();
$event = new SP_SNS_Event($id);
$event_permalink = $event->permalink;

if($event->predict_id) {
	$predict = Predict::setup($event->predict_id);
} else {
	$predict = false;
}

$league = array_shift(wp_get_post_terms($id, 'sp_league'));
$venue  = array_shift(wp_get_post_terms($id, 'sp_venue'));
$season = array_shift(wp_get_post_terms($id, 'sp_season'));

$results = sp_get_main_results( $id );
$outcome = $event->main_results();
$team_home = $event->team_home->post;
$team_away = $event->team_away->post;

$is_football = ( $event->sport_type == 'football' );

$finished = ( get_post_meta($id, 'sp_finished', true) == 'yes' );
$fixtures = ( get_post_meta($id, 'sns_fixture_loaded', true) == 'yes' );

$timeline_args = [
	$id = $event->ID
];

if ($event->finished) {
	$event_title = 'Результаты матча ' . $team_home->post_title . ' vs ' . $team_away->post_title . ' ' . wp_date('j F Y в H:i', strtotime($event->post->post_date));
} else {
	$event_title = $team_home->post_title . ' vs ' . $team_away->post_title . ' ' . wp_date('j F Y в H:i', strtotime($event->post->post_date)) . ': прогноз, ставки на матч';
}


?>

<?php echo SP_SNS_Breadcrumbs::setBreadcrumbs(); ?>

<div class="wrapper sp_wrapper">
	<?php include 'event/single/header.php'; ?>
</div>

<div class="wrapper wrapper-bookmaker sp_wrapper">
	<?php if ( have_posts() ) {
		while ( have_posts() ) : the_post(); ?>
            <div class="main sp_event_main block-wrapper sp_main_page">
         	
        		<?php if ( $tab == 'main' ) : ?>

        			<?php 
/*        			echo '<pre>';
        			print_r($event);
        			echo '</pre>';*/
        			?>

	        		<div class="sp_block_title sp_block_title-main">
	        			<h1 class="sp_h1"><?php echo $event_title ?></h1>
						<div class="sp_block_title_btns">
							<div class="sp_block_title_btn block-btn active" data-block="all">Обзор</div>
							<?php if ( $predict ) : ?>
								<div class="sp_block_title_btn block-btn" data-block="block-predict">Прогноз</div>
							<?php endif; ?>
							<?php if ( ( $event->finished && $is_football ) || ( !$event->finished ) ) : ?>
								<div class="sp_block_title_btn block-btn" data-block="block-stat">Статистика</div>
							<?php endif; ?>
							<?php if ( ( $event->finished && $event->fixtures ) || ( !$event->finished && $is_football ) ) : ?>
								<div class="sp_block_title_btn block-btn" data-block="block-squad">Составы</div>
							<?php endif; ?>
						</div>
					</div>
					
					<div class="block-content open">
						<?php 
						if ($event->finished) {
							include 'event/single/review-after.php';	
						} else {
							include 'event/single/review-before.php';
						}
						?>
					</div>

					<?php if ( $predict ) : ?>
						<div class="block-content open" id="block-predict">
							<?php include 'event/single/predict.php'; ?>
						</div>
					<?php endif; ?>

					<div class="block-content open" id="block-stat">
						<?php 
						if ($event->finished && $is_football) {
							include 'event/single/stat-after.php';	
						} 
						if (! $event->finished ) {
							include 'event/single/stat-before.php';
						}
						?>
					</div>

					<?php if ( ( $event->finished && $event->fixtures ) || !$event->finished && $is_football ) : ?>
						<div class="block-content open" id="block-squad">
							<?php 
							if ($event->finished && $event->fixtures ) {
								include 'event/single/squad-after.php';	
							} 
							if ( !$event->finished && $is_football ) {
								include 'event/single/squad-before.php';
							}
							?>
						</div>
					<?php endif; ?>

				<?php else : ?>
					
					<?php if ( $tab == 'predict' ) {
						include 'event/single/prestat.php';	
					} ?>

				<?php endif; ?>

            </div>
            <aside class="sidebar">
            	<?php include 'sidebar/sidebar.php'; ?>
            </aside>

		<?php endwhile;
	} ?>
</div>
<?php get_footer(); ?>
