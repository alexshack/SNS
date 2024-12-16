<?php

$predict_link = get_the_permalink( $event->predict_id );
$bets         = $event->get_best_bets();
$results      = sp_get_main_results_or_time( $event );
/*echo '<pre>';
print_r($event);
echo '</pre>';*/
?>

<div class="sp_event_top_item">
	<div class="sp_event_top_item_header">
		<?php echo wp_date( 'd.m.y в H:i', strtotime( $event->post->post_date ) ); ?>
	</div>
	<a href="<?php echo $event->permalink ?>" class="sp_event_top_item_teams">
		<div class="sp_event_top_item_team">
			<img class="lozad lazy" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $event->team_home->logo; ?>" alt="<?php $event->team_home->post->post_title; ?>" width="16" height="16">
			<div class="sp_event_top_item_team_name">
				<?php echo $event->team_home->post->post_title; ?>
			</div>
			<div class="sp_event_top_item_team_score">
				<?php echo $event->team_home->score_start . $event->team_home->score . $event->team_home->score_end; ?>
			</div>
		</div>
		<div class="sp_event_top_item_team">
			<img class="lozad lazy" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $event->team_away->logo; ?>" alt="<?php $event->team_away->post->post_title; ?>" width="16" height="16">
			<div class="sp_event_top_item_team_name">
				<?php echo $event->team_away->post->post_title; ?>
			</div>
			<div class="sp_event_top_item_team_score">
				<?php echo $event->team_away->score_start . $event->team_away->score . $event->team_away->score_end; ?>
			</div>
		</div>		
	</a>
	<div class="sp_event_top_item_footer">
<!-- 		<a class="sp_top_item_predict" href="<?php echo $predict_link; ?>" title="Прогноз на матч <? echo $event->team_home->post->post_title; ?> - <?php echo $event->team_away->post->post_title; ?>">Прогноз</a> -->
		<?php if ( $event->post->post_status == 'future' && $bets ) {
			sp_get_template( 'event-bets.php', ['bets' => $bets, 'class' => 'sp_event_top_item_bets'], SP()->template_path() . 'event/',  );
		} ?>
	</div>
</div>