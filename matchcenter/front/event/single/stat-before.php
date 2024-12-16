<?php
/**
 * Template part for league main block SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

$events_home_args = array(
	'title'         => false,
	'team'          => $team_home->ID,
	'season'        => $season->term_id,
	'orderby'       => 'date',
	'order'         => 'DESC',
	'number'        => 5,
	'show_league'   => true,
	'show_matchday' => false,
	'status'        => 'publish',
	'show_time'     => false
);

$events_away_args = array(
	'title'         => false,
	'team'          => $team_away->ID,
	'season'        => $season->term_id,
	'orderby'       => 'date',
	'order'         => 'DESC',
	'number'        => 5,
	'show_league'   => true,
	'show_matchday' => false,
	'status'        => 'publish',
	'show_time'     => false
);

$stat_args = array(
	'title'        => false,
	'event'        => $event,
	'season'       => $season->term_id,
	'orderby'      => 'date',
	'order'        => 'DESC',
	'number'       => 10,
	'status'       => 'publish',
	'single_event' => false
);

$h2h_args = array(
	'title'        => false,
	'team_home'    => $team_home,
	'team_away'    => $team_away,
	'season'       => $season,
	'orderby'      => 'date',
	'order'        => 'DESC',
	'status'       => 'publish',
	'show_matchday' => false,
	'show_stat'    => $is_football
);


$table_args = [
	'post_type'      => 'sp_table',
	'posts_per_page' => -1,
	'status'         => 'publish',
	'tax_query'      => [
		'relation' => 'AND',
		[
			'taxonomy' => 'sp_season',
  			'field'    => 'term_id',
  			'terms'    => $season->term_id,
		],
		[
			'taxonomy' => 'sp_league',
  			'field'    => 'term_id',
  			'terms'    => $league->term_id,
		],				
	],
	'meta_query'   => [
		'relation' => 'AND',
		[
			'key'      => 'sp_teams',
			'value'    => $team_home->ID,
			'compare'  => 'LIKE'
		],
		[
			'key'      => 'sp_teams',
			'value'    => $team_away->ID,
			'compare'  => 'LIKE'
		],		
	]  	  
];

$tables_query = new WP_Query;

$tables = $tables_query->query($table_args);

?>

<div class="sp_block">
	<div class="sp_block_title">
		<h2>Результаты и статистика последних матчей</h2>
	</div>
	<?php if ( $is_football ) sp_get_template( 'event-statistics.php', $stat_args, SP()->template_path() . 'event/'  ); ?>
	<div class="sp_block_title">
		<h3>Результаты последних игр <?php echo $team_home->post_title; ?>:</h3>
		<a href="<?php echo get_permalink($team_home) . 'calendar/'; ?>">Календарь игр <?php echo $team_home->post_title; ?></a>
	</div>
	<div class="sp_inner_block mb-15">
		<?php sp_get_template( 'event-rows.php', $events_home_args, SP()->template_path() . 'event/'  ); ?>
	</div>

	<div class="sp_block_title">
		<h3>Результаты последних игр <?php echo $team_away->post_title; ?>:</h3>
		<a href="<?php echo get_permalink($team_away) . 'calendar/'; ?>">Календарь игр <?php echo $team_away->post_title; ?></a>
	</div>
	<div class="sp_inner_block">
		<?php sp_get_template( 'event-rows.php', $events_away_args, SP()->template_path() . 'event/'  ); ?>
	</div>

</div>


<div class="sp_block">
	<div class="sp_block_title">
		<h2>Статистика личных встреч</h2>
	</div>
	<?php sp_get_template( 'event-headtohead.php', $h2h_args, SP()->template_path() . 'event/'  ); ?>
</div>

<?php if ($tables) : ?> 

<?php
	foreach ($tables as $table) { 
		$table_args = array(
			'id'         => $table->ID,
			'show_title' => true,
			'tab'        => $tab,
			'highlight'  => $team_home->ID,
			'highlight2' => $team_away->ID
		);	
		sp_get_template( 'league-table.php', $table_args, SP()->template_path() . 'league/'  ); 
		//sp_get_template( 'league-matrix.php', $matrix_args, SP()->template_path() . 'league/' );
	} ?>

<?php endif; ?>
