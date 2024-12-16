<?php
/**
 * Event Statistics
 *
 * @author      Alex Torbeev
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
	'status'               => 'publish',
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
	'team_home'            => null,
	'team_away'            => null,
	'player'               => null,
	'number'               => 10,
	'show_team_logo'       => get_option( 'sportspress_event_blocks_show_logos', 'yes' ) == 'yes' ? true : false,
	'link_teams'           => get_option( 'sportspress_link_teams', 'no' ) == 'yes' ? true : false,
	'link_events'          => get_option( 'sportspress_link_events', 'yes' ) == 'yes' ? true : false,
	'paginated'            => get_option( 'sportspress_event_blocks_paginated', 'yes' ) == 'yes' ? true : false,
	'rows'                 => get_option( 'sportspress_event_blocks_rows', 5 ),
	'orderby'              => 'date',
	'order'                => 'DESC',
	'columns'              => null,
	'show_all_events_link' => false,
	'show_title'           => get_option( 'sportspress_event_blocks_show_title', 'no' ) == 'yes' ? true : false,
	'show_league'          => get_option( 'sportspress_event_blocks_show_league', 'no' ) == 'yes' ? true : false,
	'show_season'          => get_option( 'sportspress_event_blocks_show_season', 'no' ) == 'yes' ? true : false,
	'show_matchday'        => get_option( 'sportspress_event_blocks_show_matchday', 'no' ) == 'yes' ? true : false,
	'show_venue'           => get_option( 'sportspress_event_blocks_show_venue', 'no' ) == 'yes' ? true : false,
	'hide_if_empty'        => false,
	'single_event'	       => false,
 	'show_date'            => true,
	'show_time'            => false,
	'has_predict'          => false,
	'show_stat'            => false	
);

extract( $defaults, EXTR_SKIP );

	$events_args = [
		'post_type'   => 'sp_event',
		'post_status' => $status,
		'orderby' => $orderby,
		'order'   => $order,
		'meta_query' => [
			'relation' => 'and',
			[
				'key'   => 'sp_team',
				'value' => $team_home->ID,
			],
			[
				'key'   => 'sp_team',
				'value' => $team_away->ID,
			],			
		],
		'tax_query' => [
			'relation' => 'AND'
		]
	];

	if ( $season ) {
		$events_args['tax_query'] = [
			'taxonomy' => 'sp_season',
			'field'    => 'id',
			'terms'    => $season->term_id,			
		];
	}

	if ( $league ) {
		$events_args['tax_query'] = [
			'taxonomy' => 'sp_league',
			'field'    => 'id',
			'terms'    => $league->term_id,			
		];
	}

	$events_query = new WP_Query;
	$events = $events_query->query($events_args);

if ($show_stat) :

	$statistics = [
		$team_home->ID => [
			'games' => 0,
			'wins' => 0,
			'firsthalf' => 0,
			'secondhalf' => 0,
			'goals' => 0,
			'shotsongoal' => 0,
			'shotsoffgoal' => 0,
			'totalshots' => 0,
			'blockedshots' => 0,
			'shotsinsidebox' => 0,
			'shotsoutsidebox' => 0,
			'fouls' => 0,
			'cornerkicks' => 0,
			'offsides' => 0,
			'ballpossession' => 0,
			'yellowcards' => 0,
			'redcards' => 0,
			'goalkeepersaves' => 0,
			'totalpasses' => 0,
			'passesaccurate' => 0,
			'expected_goals' => 0,
		],
		$team_away->ID => [
			'games' => 0,
			'wins' => 0,
			'firsthalf' => 0,
			'secondhalf' => 0,
			'goals' => 0,
			'shotsongoal' => 0,
			'shotsoffgoal' => 0,
			'totalshots' => 0,
			'blockedshots' => 0,
			'shotsinsidebox' => 0,
			'shotsoutsidebox' => 0,
			'fouls' => 0,
			'cornerkicks' => 0,
			'offsides' => 0,
			'ballpossession' => 0,
			'yellowcards' => 0,
			'redcards' => 0,
			'goalkeepersaves' => 0,
			'totalpasses' => 0,
			'passesaccurate' => 0,
			'expected_goals' => 0,
		]
	];	


	foreach ($events as $game) {
		$results = get_post_meta( $game->ID, 'sp_statistics', true );
		if (is_array($results)) {
			foreach ($statistics[$team_home->ID] as $stat_name => $stat) {
				if (isset( $results[$team_home->ID][$stat_name])) {
					$result = str_replace('%', '', $results[$team_home->ID][$stat_name]);
					$statistics[$team_home->ID][$stat_name] = $stat + (double)$result;
				}
			}				
			if ($results[$team_home->ID]['outcome'][0] == 'win') {
				$statistics[$team_home->ID]['wins']++;
			}
			$statistics[$team_home->ID]['form'][] = $results[$team_home->ID]['outcome'][0];
			$statistics[$team_home->ID]['games']++;

			foreach ($statistics[$team_away->ID] as $stat_name => $stat) {
				if (isset( $results[$team_away->ID][$stat_name])) {
					$result = str_replace('%', '', $results[$team_away->ID][$stat_name]);
					$statistics[$team_away->ID][$stat_name] = $stat + (double)$result;
				}
			}				
			if ($results[$team_away->ID]['outcome'][0] == 'win') {
				$statistics[$team_away->ID]['wins']++;
			}
			$statistics[$team_away->ID]['form'][] = $results[$team_away->ID]['outcome'][0];
			$statistics[$team_away->ID]['games']++;			
		}
	}
	
	if ($statistics[$team_home->ID]['games'] > 0) {
		$statistics[$team_home->ID]['wins_percent'] = number_format($statistics[$team_home->ID]['wins'] / $statistics[$team_home->ID]['games'] * 100, 1);
		$statistics[$team_home->ID]['poss_percent'] = number_format($statistics[$team_home->ID]['ballpossession'] / $statistics[$team_home->ID]['games'], 1);
		if ( $statistics[$team_home->ID]['totalpasses'] > 0 ) {
			$statistics[$team_home->ID]['pass_percent'] = number_format($statistics[$team_home->ID]['passesaccurate'] / $statistics[$team_home->ID]['totalpasses'] * 100, 1);
		} else {
			$statistics[$team_home->ID]['pass_percent'] = 0;
		}
		if ( $statistics[$team_home->ID]['totalshots'] > 0 ) {
			$statistics[$team_home->ID]['shot_percent'] = number_format($statistics[$team_home->ID]['shotsongoal'] / $statistics[$team_home->ID]['totalshots'] * 100, 1);
		} else {
			$statistics[$team_home->ID]['shot_percent'] = 0;
		}
		$statistics[$team_home->ID]['form'] = array_slice($statistics[$team_home->ID]['form'], -5);	
	}

	if ($statistics[$team_away->ID]['games'] > 0) {
		$statistics[$team_away->ID]['wins_percent'] = number_format($statistics[$team_away->ID]['wins'] / $statistics[$team_away->ID]['games'] * 100, 1);
		$statistics[$team_away->ID]['poss_percent'] = number_format($statistics[$team_away->ID]['ballpossession'] / $statistics[$team_away->ID]['games'], 1);
		if ( $statistics[$team_away->ID]['totalpasses'] > 0 ) {
			$statistics[$team_away->ID]['pass_percent'] = number_format($statistics[$team_away->ID]['passesaccurate'] / $statistics[$team_away->ID]['totalpasses'] * 100, 1);
		} else {
			$statistics[$team_away->ID]['pass_percent'] = 0;
		}
		if ( $statistics[$team_away->ID]['totalshots'] > 0 ) {
			$statistics[$team_away->ID]['shot_percent'] = number_format($statistics[$team_away->ID]['shotsongoal'] / $statistics[$team_away->ID]['totalshots'] * 100, 1);
		} else {
			$statistics[$team_away->ID]['shot_percent'] = 0;
		}
		$statistics[$team_away->ID]['form'] = array_slice($statistics[$team_away->ID]['form'], -5);
	}		

/*echo '<pre>';
print_r($statistics);
echo '</pre>';*/

	if ($statistics[$team_home->ID]['games'] > 0) {

		$draws = $statistics[$team_home->ID]['games'] - $statistics[$team_home->ID]['wins'] - $statistics[$team_away->ID]['wins'];

		$draws_percent = number_format($draws / $statistics[$team_home->ID]['games'] * 100, 1);
		$whome_percent = number_format($statistics[$team_home->ID]['wins'] / $statistics[$team_home->ID]['games'] * 100, 1);
		$waway_percent = number_format($statistics[$team_away->ID]['wins'] / $statistics[$team_home->ID]['games'] * 100, 1);
	}

?>
<?php if ($statistics[$team_home->ID]['games'] > 0) : ?>
<div class="sp_event_games">
	<div class="sp_event_games_title">
		<div class="sp_event_games_title_team home">
            <a href="<?php echo get_permalink($team_home) ?>">
                <img class="lazy lozad" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo get_the_post_thumbnail_url($team_home, 'w70h70'); ?>" alt="<?php echo $team_home->post_title; ?>" width="150" height="150">
            </a>            
            <a href="<?php echo get_permalink($team_home) ?>" ><?php echo $team_home->post_title; ?></a>
		</div>
		<div class="sp_event_games_forms">
			<div class="sp_event_games_form home"><?php echo $statistics[$team_home->ID]['wins'] ?></div>
			<div class="sp_event_games_form "><?php echo $draws ?></div>
			<div class="sp_event_games_form away"><?php echo $statistics[$team_away->ID]['wins'] ?></div>
		</div>
		<div class="sp_event_games_title_team away">
            <a href="<?php echo get_permalink($team_away) ?>"><?php echo $team_away->post_title; ?></a>
            <a href="<?php echo get_permalink($team_away) ?>">
                <img class="lazy lozad" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo get_the_post_thumbnail_url($team_away, 'w70h70'); ?>" alt="<?php echo $team_away->post_title; ?>" width="150" height="150">
            </a>		            
		</div>				
	</div>
	<div class="sp_event_stats">
		<div class="sp_event_stat home">			
			<div class="sp_event_stat_diagram">
				<div class="diagram progress <?php echo ($statistics[$team_home->ID]['wins_percent'] > 50) ? 'over_50' : ''; ?>" >
				    <div class="piece left"></div>
				    <div class="piece right" style="transform: rotate(calc((360deg * <?php echo $statistics[$team_home->ID]['wins_percent']; ?> / 100) + 180deg));"></div>
				    <div class="text"><?php echo $statistics[$team_home->ID]['wins_percent']; ?>%</div>
				</div>
				<div class="sp_event_stat_diagram_title">Процент <br>побед</div>
			</div>
			<div class="sp_event_stat_diagram">
				<div class="diagram progress <?php echo ($statistics[$team_home->ID]['poss_percent'] > 50) ? 'over_50' : ''; ?>" >
				    <div class="piece left"></div>
				    <div class="piece right" style="transform: rotate(calc((360deg * <?php echo $statistics[$team_home->ID]['poss_percent']; ?> / 100) + 180deg));"></div>
				    <div class="text"><?php echo $statistics[$team_home->ID]['poss_percent']; ?>%</div>
				</div>
				<div class="sp_event_stat_diagram_title">Владение <br>мячом</div>
			</div>
			<div class="sp_event_stat_diagram">
				<div class="diagram progress <?php echo ($statistics[$team_home->ID]['shot_percent'] > 50) ? 'over_50' : ''; ?>" >
				    <div class="piece left"></div>
				    <div class="piece right" style="transform: rotate(calc((360deg * <?php echo $statistics[$team_home->ID]['shot_percent']; ?> / 100) + 180deg));"></div>
				    <div class="text"><?php echo $statistics[$team_home->ID]['shot_percent']; ?>%</div>
				</div>
				<div class="sp_event_stat_diagram_title">Точные <br>удары</div>
			</div>
			<div class="sp_event_stat_diagram">
				<div class="diagram progress <?php echo ($statistics[$team_home->ID]['pass_percent'] > 50) ? 'over_50' : ''; ?>" >
				    <div class="piece left"></div>
				    <div class="piece right" style="transform: rotate(calc((360deg * <?php echo $statistics[$team_home->ID]['pass_percent']; ?> / 100) + 180deg));"></div>
				    <div class="text"><?php echo $statistics[$team_home->ID]['pass_percent']; ?>%</div>
				</div>
				<div class="sp_event_stat_diagram_title">Точные <br>передачи</div>
			</div>											
		</div>

		<div class="sp_event_stat away">
			<div class="sp_event_stat_diagram">
				<div class="diagram progress <?php echo ($statistics[$team_away->ID]['wins_percent'] > 50) ? 'over_50' : ''; ?>" >
				    <div class="piece left"></div>
				    <div class="piece right" style="transform: rotate(calc((360deg * <?php echo $statistics[$team_away->ID]['wins_percent']; ?> / 100) + 180deg));"></div>
				    <div class="text"><?php echo $statistics[$team_away->ID]['wins_percent']; ?>%</div>
				</div>
				<div class="sp_event_stat_diagram_title">Процент <br>побед</div>
			</div>
			<div class="sp_event_stat_diagram">
				<div class="diagram progress <?php echo ($statistics[$team_away->ID]['poss_percent'] > 50) ? 'over_50' : ''; ?>" >
				    <div class="piece left"></div>
				    <div class="piece right" style="transform: rotate(calc((360deg * <?php echo $statistics[$team_away->ID]['poss_percent']; ?> / 100) + 180deg));"></div>
				    <div class="text"><?php echo $statistics[$team_away->ID]['poss_percent']; ?>%</div>
				</div>
				<div class="sp_event_stat_diagram_title">Владение <br>мячом</div>
			</div>
			<div class="sp_event_stat_diagram">
				<div class="diagram progress <?php echo ($statistics[$team_away->ID]['shot_percent'] > 50) ? 'over_50' : ''; ?>" >
				    <div class="piece left"></div>
				    <div class="piece right" style="transform: rotate(calc((360deg * <?php echo $statistics[$team_away->ID]['shot_percent']; ?> / 100) + 180deg));"></div>
				    <div class="text"><?php echo $statistics[$team_away->ID]['shot_percent']; ?>%</div>
				</div>
				<div class="sp_event_stat_diagram_title">Точные <br>удары</div>
			</div>
			<div class="sp_event_stat_diagram">
				<div class="diagram progress <?php echo ($statistics[$team_away->ID]['pass_percent'] > 50) ? 'over_50' : ''; ?>" >
				    <div class="piece left"></div>
				    <div class="piece right" style="transform: rotate(calc((360deg * <?php echo $statistics[$team_away->ID]['pass_percent']; ?> / 100) + 180deg));"></div>
				    <div class="text"><?php echo $statistics[$team_away->ID]['pass_percent']; ?>%</div>
				</div>
				<div class="sp_event_stat_diagram_title">Точные <br>передачи</div>
			</div>											
		</div>
		<div class="sp_event_stat_tables">
			<table class="sp_event_stat_table">
				<tr>
					<td class="home"><?php echo $statistics[$team_home->ID]['goals']; ?></td>
					<td>Забитые голы</td>
					<td class="away"><?php echo $statistics[$team_away->ID]['goals']; ?></td>
				</tr>
				<tr>
					<td class="home"><?php echo $statistics[$team_home->ID]['expected_goals']; ?></td>
					<td>xG</td>
					<td class="away"><?php echo $statistics[$team_away->ID]['expected_goals']; ?></td>
				</tr>
				<tr>
					<td class="home"><?php echo $statistics[$team_home->ID]['totalshots']; ?></td>
					<td>Удары</td>
					<td class="away"><?php echo $statistics[$team_away->ID]['totalshots']; ?></td>
				</tr>
				<tr>
					<td class="home"><?php echo $statistics[$team_home->ID]['shotsongoal']; ?></td>
					<td>Удары в створ</td>
					<td class="away"><?php echo $statistics[$team_away->ID]['shotsongoal']; ?></td>
				</tr>
				<tr>
					<td class="home"><?php echo $statistics[$team_home->ID]['goalkeepersaves']; ?></td>
					<td>Сейвы</td>
					<td class="away"><?php echo $statistics[$team_away->ID]['goalkeepersaves']; ?></td>
				</tr>				
				<tr>
					<td class="home"><?php echo $statistics[$team_home->ID]['fouls']; ?></td>
					<td>Нарушения</td>
					<td class="away"><?php echo $statistics[$team_away->ID]['fouls']; ?></td>
				</tr>
				<tr>
					<td class="home"><?php echo $statistics[$team_home->ID]['yellowcards']; ?></td>
					<td>Желтые карточки</td>
					<td class="away"><?php echo $statistics[$team_away->ID]['yellowcards']; ?></td>
				</tr>
				<tr>
					<td class="home"><?php echo $statistics[$team_home->ID]['redcards']; ?></td>
					<td>Красные карточки</td>
					<td class="away"><?php echo $statistics[$team_away->ID]['redcards']; ?></td>
				</tr>
				<tr>
					<td class="home"><?php echo $statistics[$team_home->ID]['cornerkicks']; ?></td>
					<td>Угловые</td>
					<td class="away"><?php echo $statistics[$team_away->ID]['cornerkicks']; ?></td>
				</tr>																														
			</table>
		</div>		
	</div>
</div>
<?php endif; ?>


<div class="sp_block_title">
	<h3>Результаты личных встреч:</h3>
</div>	
<?php endif; ?>
<?php if ($events) : ?>
	<div class="sp_inner_block">
		<?php
		$calendarOrder = $orderby;
		foreach ( $events as $event ) :
			$event = new SP_SNS_Event($event);
			include('event-row.php');
		endforeach;
		?>
	</div>
<?php endif; ?>
