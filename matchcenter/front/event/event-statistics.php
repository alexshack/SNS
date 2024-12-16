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
	'teams_past'           => null,
	'date_before'          => null,
	'player'               => null,
	'number'               => 10,
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
	'single_event'	       => false
);

extract( $defaults, EXTR_SKIP );



if ($event) :
	$team_home = $event->team_home->post;
	$team_away = $event->team_away->post;
	$statistics = [
		$event->team_home->ID => [
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
			'wins_percent' => 0,
			'poss_percent' => 0,
			'pass_percent' => 0,
			'shot_percent' => 0,			
		],
		$event->team_away->ID => [
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
			'wins_percent' => 0,
			'poss_percent' => 0,
			'pass_percent' => 0,
			'shot_percent' => 0,
		]
	];

	$teams = [
		$event->team_home,
		$event->team_away
	];	
	if (! $single_event) {
		$teams = [
			$event->team_home,
			$event->team_away
		];
		foreach ($teams as $key=>$team) {

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

			$calendar->team = $team->ID;

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
			$i = 0;
			foreach ( $data as $game ) {
				if ($i >= $number) {
					continue;
				}
				$results = get_post_meta( $game->ID, 'sp_statistics', true );
				if (is_array($results)) {
					foreach ($statistics[$team->ID] as $stat_name => $stat) {
						if (isset( $results[$team->ID][$stat_name])) {
							$result = str_replace('%', '', $results[$team->ID][$stat_name]);
							$statistics[$team->ID][$stat_name] = $stat + (double)$result;
						}
					}				
					if ($results[$team->ID]['outcome'][0] == 'win') {
						$statistics[$team->ID]['wins']++;
					}
					$statistics[$team->ID]['form'][] = $results[$team->ID]['outcome'][0];
					$statistics[$team->ID]['games']++;
				}

				$i++;
			}
			if ($statistics[$team->ID]['games'] > 0) {
				$statistics[$team->ID]['wins_percent'] = number_format($statistics[$team->ID]['wins'] / $statistics[$team->ID]['games'] * 100, 1);
				$statistics[$team->ID]['poss_percent'] = number_format($statistics[$team->ID]['ballpossession'] / $statistics[$team->ID]['games'], 1);
				if ( $statistics[$team->ID]['totalpasses'] > 0 ) {
					$statistics[$team->ID]['pass_percent'] = number_format($statistics[$team->ID]['passesaccurate'] / $statistics[$team->ID]['totalpasses'] * 100, 1);
				} else {
					$statistics[$team->ID]['pass_percent'] = 0;
				}
				if ( $statistics[$team->ID]['totalshots'] > 0 ) {
					$statistics[$team->ID]['shot_percent'] = number_format($statistics[$team->ID]['shotsongoal'] / $statistics[$team->ID]['totalshots'] * 100, 1);
				} else {
					$statistics[$team->ID]['shot_percent'] = 0;
				}
				$statistics[$team->ID]['form'] = array_slice($statistics[$team->ID]['form'], -5);
			}

		}

	} else {
		$results = get_post_meta( $event->ID, 'sp_statistics', true );
		if (is_array($results)) {
			foreach ($teams as $key=>$team) {
				foreach ($statistics[$team->ID] as $stat_name => $stat) {
					if (isset( $results[$team->ID][$stat_name])) {
						$result = str_replace('%', '', $results[$team->ID][$stat_name]);
						$statistics[$team->ID][$stat_name] = $stat + (double)$result;
					}
				}				
				if ($results[$team->ID]['outcome'][0] == 'win') {
					$statistics[$team->ID]['wins']++;
				}
				$statistics[$team->ID]['games']++;
			
				$statistics[$team->ID]['poss_percent'] = number_format($statistics[$team->ID]['ballpossession'], 1);
				if($statistics[$team->ID]['totalpasses'] > 0) {
					$statistics[$team->ID]['pass_percent'] = number_format($statistics[$team->ID]['passesaccurate'] / $statistics[$team->ID]['totalpasses'] * 100, 1);
				}
				if($statistics[$team->ID]['totalshots'] > 0) {
					$statistics[$team->ID]['shot_percent'] = number_format($statistics[$team->ID]['shotsongoal'] / $statistics[$team->ID]['totalshots'] * 100, 1);
				}
			}
		}				
	}
?>
<div class="sp_event_games">
	<div class="sp_event_games_title">
		<div class="sp_event_games_title_team home">
            <a href="<?php echo get_permalink($team_home) ?>">
                <img class="lazy lozad" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo get_the_post_thumbnail_url($team_home, 'w70h70'); ?>" alt="<?php echo $team_home->post_title; ?>" width="150" height="150">
            </a>            
            <a href="<?php echo get_permalink($team_home) ?>" ><?php echo $team_home->post_title; ?></a>
		</div>
		<div class="sp_event_games_title_team away">
            <a href="<?php echo get_permalink($team_away) ?>"><?php echo $team_away->post_title; ?></a>
            <a href="<?php echo get_permalink($team_away) ?>">
                <img class="lazy lozad" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo get_the_post_thumbnail_url($team_away, 'w70h70'); ?>" alt="<?php echo $team_away->post_title; ?>" width="150" height="150">
            </a>		            
		</div>				
	</div>
	<?php if (! $single_event) : ?>
	<div class="sp_event_forms">
		<div class="sp_event_form home">
			<?php foreach ($statistics[$team_home->ID]['form'] as $form) {
				echo '<div class="' . $form . '"></div>';
			} ?>
		</div>
		<div class="sp_event_form away">
			<?php foreach ($statistics[$team_away->ID]['form'] as $form) {
				echo '<div class="' . $form . '"></div>';
			} ?>
		</div>		
	</div>
	<?php endif; ?>
	<div class="sp_event_stats">
		<div class="sp_event_stat home">
			<?php if (! $single_event) : ?>		
			<div class="sp_event_stat_diagram">
				<div class="diagram progress <?php echo ($statistics[$team_home->ID]['wins_percent'] > 50) ? 'over_50' : ''; ?>" >
				    <div class="piece left"></div>
				    <div class="piece right" style="transform: rotate(calc((360deg * <?php echo $statistics[$team_home->ID]['wins_percent']; ?> / 100) + 180deg));"></div>
				    <div class="text"><?php echo $statistics[$team_home->ID]['wins_percent']; ?>%</div>
				</div>
				<div class="sp_event_stat_diagram_title">Процент <br>побед</div>
			</div>
			<?php endif; ?>
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
			<?php if (! $single_event) : ?>		
			<div class="sp_event_stat_diagram">
				<div class="diagram progress <?php echo ($statistics[$team_away->ID]['wins_percent'] > 50) ? 'over_50' : ''; ?>" >
				    <div class="piece left"></div>
				    <div class="piece right" style="transform: rotate(calc((360deg * <?php echo $statistics[$team_away->ID]['wins_percent']; ?> / 100) + 180deg));"></div>
				    <div class="text"><?php echo $statistics[$team_away->ID]['wins_percent']; ?>%</div>
				</div>
				<div class="sp_event_stat_diagram_title">Процент <br>побед</div>
			</div>
			<?php endif; ?>
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

<?php 
/*
	echo '<pre>';
	print_r($statistics);
	echo '</pre>';*/
endif; ?>

