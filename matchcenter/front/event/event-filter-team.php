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
	'league_id'   => null,
	'season_id'   => null,
	'day'         => 'default',
	'date'        => 'default',
	'status'      => 'default',
	'has_predict' => false,
	'team'        => null,
);

extract( $defaults, EXTR_SKIP );

?>

<?php 
    $events_args = array(
		'league'        => $league_id,
		'season'        => $season_id,
		'date'          => $date,
		'status'        => $status,
		'day'           => $day,
		'team'          => $team,
		'show_date'     => true,
		'show_time'     => true,
		'show_league'   => true,
		'show_matchday' => true,
		'orderby'       => 'date',
		'hide_if_empty' => true,
		'has_predict'   => $has_predict 			
	);

    sp_get_template( 'event-rows.php', $events_args, SP()->template_path() . 'event/'  );

?>
