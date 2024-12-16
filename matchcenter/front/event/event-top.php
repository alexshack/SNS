<?php
/**
 * Events Top Block
 *
 * @author      Alex Torbeev
 * @package     SportsPress/Templates
 * @version     2.7.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$defaults = array(
	'id'                   => null,
	'season'               => null,
	'date'                 => 'default',
	'league'               => null,
	'team'                 => null,
	'number'               => -1,
	'orderby'              => 'default',
	'order'                => 'default',
	'show_league'          => true,
	'hide_if_empty'        => false,
	'has_predict'          => false,
	'sport_type'           => null
);

extract( $defaults, EXTR_SKIP ); 

$leagues = [];

if ( $sport_type ) {
	$leagues = get_terms( [
		'taxonomy' => 'sp_league',
		'meta_query' => [
			[
		        'key' => 'sport_type',
		        'value' => $sport_type				
			],
		],
	] );
}

if ( $league ) {
	$leagues = [];
	$leagues[] = get_term_by( 'id', $league, 'sp_league' );
}

if ( $team ) {
	$leagues = wp_get_post_terms( $team, 'sp_league' );
}

if ( empty( $leagues ) ) {
	$leagues = get_terms( [
	  'taxonomy' => 'sp_league',
	] );
}


foreach ( $leagues as $league ) :

	$calendar = new SP_Calendar( $id );

	if ( $date != 'default' ) {
		$calendar->date = $date;
	}

	$calendar->league = $league->term_id;

	if ( $season ) {
		$calendar->season = $season;
	}

	if ( $team ) {
		$calendar->team = $team;
	}

	$data       = $calendar->data();
	$usecolumns = $calendar->columns;

	if ( empty( $data ) ) {
		continue;
	}

	?>
	<div class="sp_event_top_items sp_event_top_items-<?php echo $league->slug; ?>">
		<?php 

		if ( $show_league ) {
			include('event-top-league.php');
		} 
		foreach ( $data as $event ) {
			
			$event = new SP_SNS_Event($event);

			if ( $has_predict && !$event->predict_id ) {
				continue;
			}

			include('event-top-item.php');
			
		}
		?>
	</div>
<?php endforeach; ?>