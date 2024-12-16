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
	'stage'                => null,
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
	'title_tag'            => 'div',
	'show_league'          => get_option( 'sportspress_event_blocks_show_league', 'no' ) == 'yes' ? true : false,
	'show_season'          => get_option( 'sportspress_event_blocks_show_season', 'no' ) == 'yes' ? true : false,
	'show_matchday'        => get_option( 'sportspress_event_blocks_show_matchday', 'no' ) == 'yes' ? true : false,
	'matchday_tag'         => 'div',
	'show_venue'           => get_option( 'sportspress_event_blocks_show_venue', 'no' ) == 'yes' ? true : false,
	'hide_if_empty'        => false,
	'accord'               => false,
	'accord_open'          => false,
 	'show_date'            => true,
	'show_time'            => true,
	'has_predict'          => false,
	'data'                 => false
);

extract( $defaults, EXTR_SKIP );

if ( ! $data ) {
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
	if ( $stage ) {
		$calendar->stage = $stage;
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

	$calendarOrder = $calendar->orderby;
} else {
	$calendarOrder = '';
}

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



?>
<div class="sp_event_rows <?php echo $accord ? ' sp_block_accord' : ''; ?><?php echo $accord_open ? ' open' : ''; ?>">
	<?php if ( $title ) : ?>
		<<?php echo $title_tag ?> class="sp_event_rows_title <?php echo $accord ? ' sp_block_accord_title' : ''; ?>">
			<?php echo $title; ?>
		</<?php echo $title_tag ?>>
	<?php endif; ?>
	<div class="sp_event_rows_wrapper <?php echo $accord ? ' sp_block_accord_content' : ''; ?>">
		<?php
		if ( !empty($data) ) {
		
			$i = 0;
			if ( intval( $number ) > 0 ) {
				$limit = $number;
			}

			foreach ( $data as $event ) :
				if ( isset( $limit ) && $i >= $limit ) {
					continue;
				}
				
				$event = new SP_SNS_Event($event);

				if ( $has_predict && !$event->predict_id ) {
					continue;
				}
				include('event-row.php');
				$i++;
			endforeach;
		} else {
			echo '<div class="sp_event_row">В выбранный период события не найдены</div>';
		}
		?>
	</div>
	<?php if ( $id && $show_all_events_link ) {
		echo '<div class="sp-calendar-link sp-view-all-link"><a href="' . esc_url( get_permalink( $id ) ) . '">' . esc_attr__( 'View all events', 'sportspress' ) . '</a></div>';
	} ?>

</div>