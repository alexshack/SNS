<?php

/*echo '<pre>';
print_r($event);
echo '</pre>';*/

$dates = [];
if ( $show_date ) {
	$dates[] = 'd.m.y';
}
if ( $show_time ) {
	$dates[] = 'H:i';
}
$date_format = implode(' ', $dates);

if ( 'day' === $calendarOrder ) :
	$event_group = get_post_meta( $event->ID, 'sp_day', true );
	if ( ! isset( $group ) || $event_group !== $group ) :
		$group = $event_group;
		echo '<' . $matchday_tag . ' class="sp_event_rows_group">' . wp_kses_post( $group ) . '</' . $matchday_tag . '>';
	endif;
endif;
?>
<div class="sp_event_row sp_event_<?php echo $event->ID; ?>">

	<?php if ( $show_league || $show_matchday ) : ?>
		<div class="sp_event_row_places">
	<?php endif; ?>

	<?php
	if ( $show_league ) :
		$leagues = get_the_terms( $event, 'sp_league' );
		if ( $leagues ) :
			$league = array_shift( $leagues );
		?>
			<div class="sp_event_row_league"><?php echo wp_kses_post( $league->name ); ?>.</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( $show_matchday ) : ?>
		<div class="sp_event_row_matchday"><?php echo $event->getStage(); ?></div>
	<?php endif; ?>	

	<?php if ( $show_league || $show_matchday ) : ?>
		</div>
	<?php endif; ?>

	<?php if ( $show_date || $show_time ) : ?>
		<div class="sp_event_row_date">
			<?php echo wp_date( $date_format, strtotime( $event->post->post_date ) ); ?>
			<?php if ( $event->tv_link ) : ?>
				<a href="<?php echo $event->tv_link ?>" class="sp_event_row_date_tv" title="Трансляция" target="blank" rel="nofollow">
					<svg><use xlink:href="<?php echo get_template_directory_uri() ?>/sportspress/assets/img/tv.svg#tv"></use></svg>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<a href="<?php echo $event->permalink ?>" class="sp_event_row_link">
		<div class="sp_event_row_team sp_event_row_team_first">
			<img class="lozad lazy sp_event_row_team_logo" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $event->team_home->logo; ?>" width="150" height="150">
			<div class="sp_event_row_team_name"><?php echo $event->team_home->post->post_title; ?></div>
			<div class="sp_event_row_team_score"><?php echo $event->team_home->score_start . $event->team_home->score . $event->team_home->score_end; ?></div>
		</div>

		<div class="sp_event_row_score_desktop">
			<?php echo $event->get_score(); ?>
		</div>

		<div class="sp_event_row_team sp_event_row_team_second">
			<img class="lozad lazy sp_event_row_team_logo" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $event->team_away->logo; ?>" width="150" height="150">
			<div class="sp_event_row_team_name"><?php echo $event->team_away->post->post_title; ?></div>
			<div class="sp_event_row_team_score"><?php echo $event->team_away->score_start . $event->team_away->score . $event->team_away->score_end; ?></div>
		</div>
	</a>

	<div class="sp_event_row_footer">

		<?php if ( $event->predict_id ) {
			$predict_link = get_the_permalink( $event->predict_id );
			echo '<a class="sp_event_predict" href="' . $predict_link . '" title="Прогноз на матч ' . $event->team_home->post->post_title . ' - ' . $event->team_away->post->post_title . '">Прогноз</a>';
		}
		?>

		<?php 
		if ( ! $event->finished ) {
			if ( $bets = $event->get_best_bets() ) {
				sp_get_template( 'event-bets.php', ['bets' => $bets, 'class' => 'sp_event_row_bets'], SP()->template_path() . 'event/',  );
			} else {
				echo '<a href="' . $event->permalink . '" class="sp_event_row_score">';
				echo $event->get_status_text();
				echo '</a>';
			}
		} else {
			echo '<a href="' . $event->permalink . '" class="sp_event_row_score">';
			echo $event->get_status_text();
			echo '</a>';
		}
		?>
	</div>


</div>