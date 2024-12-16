<?php
/**
 * Event Blocks
 *
 * @author      ThemeBoy
 * @package     SportsPress/Templates
 * @version   2.7.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


$defaults = array(
	'id'                   => null,
	'event'                => null,
	'title'                => false,
	'status'               => 'default',
	'format'               => 'default',
	'date'                 => 'default',
	'date_from'            => 'default',
	'date_to'              => 'default',
	'date_past'            => 'default',
	'date_future'          => 'default',
	'date_relative'        => 'default',
	'day'                  => 'default',
	'league'               => null,
	'season'               => null,
	'venue'                => null,
	'team'                 => null,
	'teams_past'           => null,
	'date_before'          => null,
	'player'               => null,
	'number'               => -1,
	'show_team_logo'       => get_option( 'sportspress_event_blocks_show_logos', 'yes' ) == 'yes' ? true : false,
	'link_teams'           => get_option( 'sportspress_link_teams', 'no' ) == 'yes' ? true : false,
	'link_events'          => get_option( 'sportspress_link_events', 'yes' ) == 'yes' ? true : false,
	'paginated'            => get_option( 'sportspress_event_blocks_paginated', 'yes' ) == 'yes' ? true : false,
	'rows'                 => get_option( 'sportspress_event_blocks_rows', 5 ),
	'orderby'              => 'default',
	'order'                => 'default',
	'columns'              => null,
	'show_all_events_link' => false,
	'show_title'           => get_option( 'sportspress_event_blocks_show_title', 'no' ) == 'yes' ? true : false,
	'show_league'          => get_option( 'sportspress_event_blocks_show_league', 'no' ) == 'yes' ? true : false,
	'show_season'          => get_option( 'sportspress_event_blocks_show_season', 'no' ) == 'yes' ? true : false,
	'show_matchday'        => get_option( 'sportspress_event_blocks_show_matchday', 'no' ) == 'yes' ? true : false,
	'show_venue'           => get_option( 'sportspress_event_blocks_show_venue', 'no' ) == 'yes' ? true : false,
	'hide_if_empty'        => false,
	'bonus'                => false
);

extract( $defaults, EXTR_SKIP );

$calendar = new SP_Calendar( $id );

if ( $status != 'default' ) {
	$calendar->status = $status;
}
if ( $format != 'default' ) {
	$calendar->event_format = $format;
}
if ( $date != 'default' ) {
	$calendar->date = $date;
}
if ( $date_from != 'default' ) {
	$calendar->from = $date_from;
}
if ( $date_to != 'default' ) {
	$calendar->to = $date_to;
}
if ( $date_past != 'default' ) {
	$calendar->past = $date_past;
}
if ( $date_future != 'default' ) {
	$calendar->future = $date_future;
}
if ( $date_relative != 'default' ) {
	$calendar->relative = $date_relative;
}
if ( $event ) {
	$calendar->event = $event;
}
if ( $league ) {
	$calendar->league = $league;
}
if ( $season ) {
	$calendar->season = $season;
}
if ( $venue ) {
	$calendar->venue = $venue;
}
if ( $team ) {
	$calendar->team = $team;
}
if ( $teams_past ) {
	$calendar->teams_past = $teams_past;
}
if ( $date_before ) {
	$calendar->date_before = $date_before;
}
if ( $player ) {
	$calendar->player = $player;
}
if ( $order != 'default' ) {
	$calendar->order = $order;
}
if ( $orderby != 'default' ) {
	$calendar->orderby = $orderby;
}
if ( $day != 'default' ) {
	$calendar->day = $day;
}
$data       = $calendar->data();
$usecolumns = $calendar->columns;

if ( isset( $columns ) ) :
	if ( is_array( $columns ) ) {
		$usecolumns = $columns;
	} else {
		$usecolumns = explode( ',', $columns );
	}
endif;

if ( $hide_if_empty && empty( $data ) ) {
	return false;
}

if ( $show_title && false === $title && $id ) :
	$caption = $calendar->caption;
	if ( $caption ) {
		$title = $caption;
	} else {
		$title = get_the_title( $id );
	}
endif;

if ($data) {

	if ($bonus) {
		$bookmaker = false;
        if ( $bk_id = get_option( 'sns_bk_api_winline', false ) ) {
            $bookmaker = new SP_SNS_Bookmaker( $bk_id );
        }
	}

	echo '<div class="sp_block">';

	if ( $title ) {
		echo '<div class="sp_block_title">';
		echo '<h2 class="sp-table-caption">' . wp_kses_post( $title ) . '</h2>';
		if ($league != null) {
			echo '<a href="' . get_term_link($league) . 'calendar/">Все результаты</a>';
		}
		echo '</div>';
	}
	?>
	<div class="sp_event_blocks">
			<?php
			$i = 0;

			if ( intval( $number ) > 0 ) {
				$limit = $number;
			}

			foreach ( $data as $event ) :
				if ( isset( $limit ) && $i >= $limit ) {
					continue;
				}

				$event_object = new SP_SNS_Event($event);
				$event_status = get_post_meta( $event->ID, 'sp_status', true );


				if ( 'day' === $calendar->orderby ) :
					$event_group = get_post_meta( $event->ID, 'sp_day', true );
					if ( ! isset( $group ) || $event_group !== $group ) :
						$group = $event_group;
						echo '<div class="sp_event_group_name">', esc_attr__( 'Match Day', 'sportspress' ), ' ', wp_kses_post( $group ), '</div>';
					endif;
				endif;
				?>
				<a class="sp_event_block" href="<?php echo $event_object->permalink ?>">
					<div class="sp_event_block_header">
						<?php
							if ( $show_league && $league == null ) :
								$leagues = get_the_terms( $event, 'sp_league' );
								if ( $leagues ) :
									$game_league   = array_shift( $leagues );
									$league_img_id = get_term_meta($game_league->term_id, '_thumbnail_id', 1 );
									$league_img    = wp_get_attachment_image_url( $league_img_id, 'sportspress-fit-mini' );
									$league_link   = get_term_link($game_league);
								?>
								<div class="sp_event_block_league">
									<img class="sp_event_block_league_img lozad lazy" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $league_img; ?>" width="32" height="32" title="<?php echo wp_kses_post( $game_league->name ); ?>">
									<div class="sp_event_block_league_name"><?php echo wp_kses_post( $game_league->name ); ?></div>
								</div>
							<?php endif;
							endif;
						?>
						<?php if ( $show_matchday && $league == null ) : ?>
							<div class="sp_event_block_matchday"><?php echo $event_object->getStage(); ?></div>
						<?php endif; ?>						

					</div>
					<div class="sp_event_block_content">
						<div class="sp_event_block_teams">
							<div class="sp_event_block_team">
								<div class="sp_event_block_team_name">
									<img class="lozad team_logo" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $event_object->team_home->logo; ?>" width="150" height="150">
									<div class="team_name"><?php echo $event_object->team_home->post->post_title; ?></div>
								</div>
								<div class="sp_event_block_team_score">
									<?php echo $event_object->team_home->score; ?>
								</div>
							</div>
							<div class="sp_event_block_team">
								<div class="sp_event_block_team_name">
									<img class="lozad team_logo" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $event_object->team_away->logo; ?>" width="150" height="150">
									<div class="team_name"><?php echo $event_object->team_away->post->post_title; ?></div>
								</div>
								<div class="sp_event_block_team_score">
									<?php echo $event_object->team_away->score; ?>
								</div>									
							</div>							
						</div>
						<div class="sp_event_block_results">
							<?php if ( $show_matchday && $league != null ) : ?>
								<div class="sp_event_block_matchday"><?php echo $event_object->getStage(); ?></div>
							<?php endif; ?>								

							<time class="sp_event_block_date" datetime="<?php echo esc_attr( $event->post_date ); ?>" >
								<?php echo wp_date('d.m.Y', strtotime($event->post_date)); ?>
							</time>
							<div class="sp_event_block_time">
								<?php echo wp_date('H:i', strtotime($event->post_date)); ?>
							</div>
						</div>
					</div>
					<?php do_action( 'sportspress_event_blocks_after', $event, $usecolumns ); ?>

				</a>
				<?php if ( $bonus && $bookmaker ) { ?>
					<div class="sp_event_block_bonus">
						<img width="105" height="50" class="lazy lozad sp_event_block_bonus_bk" data-src="<?php echo $bookmaker->image; ?>" alt="Бонус на матч">
						<span class="sp_event_block_bonus_name">
							Забери бонус на матч
						</span>
						<a class="sp_event_block_bonus_btn" target="_blank" rel="nofollow" href="<?php echo $bookmaker->link; ?>" aria-label="Забрать бонус"></a>					
					</div>
				<?php } ?>				
				<?php
				$i++;
			endforeach;
			?>
	</div>
	<?php
	if ( $id && $show_all_events_link ) {
		echo '<div class="sp-calendar-link sp-view-all-link"><a href="' . esc_url( get_permalink( $id ) ) . '">' . esc_attr__( 'View all events', 'sportspress' ) . '</a></div>';
	}
	?>
</div>
<?php } ?>
