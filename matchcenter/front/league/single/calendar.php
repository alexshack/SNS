<?php
/**
 * Template part for league Calendar page SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

$filter_args = array(
	'league_id' => $league->ID,
	'season_id' => $season_id,
);
if ( $league->predicts_term ) {
	$predicts_args = [
		'title' => 'Прогнозы',
		'link'  => $league->predicts_link,
		'posts' => $league->getPredicts( 6 )	
	];
} else {
	$predicts_args = [
		'title' => 'Прогнозы',
		'link'  => false,
		'posts' => $league->getPredicts( 6 )
	];	
}

$content_calendar = get_term_meta( $league->ID, 'content_calendar', true );


$days = get_term_meta( $league->ID, 'sp_days', 1 )[ $season_id ];


?>

<div class="sp_block" id="sp_filter_league">
	<div class="sp_block_title">
		<h2 id="sp_filter_league_title">Расписание и календарь <?php echo $league->name; ?></h2>
	</div>
	<div class="sp_filter">
		<input type="text" value="<?php echo $league->ID; ?>" hidden id="sp_filter_league_league">
		<input type="text" value="<?php echo $season_id; ?>" hidden id="sp_filter_league_season">
 			
        <select onchange="SPSNS.scheduleLeagueFilter('team', this);" class="sp_filter_input">
           	<option value="">Команда</option>
			<?php foreach ( $teams as $team ) : ?>
              	<option value="<?php echo $team->ID; ?>"><?php echo $team->post_title; ?></option>
			<?php endforeach; ?>
        </select>
         <select onchange="SPSNS.scheduleLeagueFilter('day', this);" class="sp_filter_input">
           	<option value="">Все стадии</option>
			<?php foreach ( $stages as $stage ) : ?>
              	<option value="<?php echo $stage->ID; ?>"><?php echo $stage->name; ?></option>
			<?php endforeach; ?>
        </select>            				
        <select onchange="SPSNS.scheduleLeagueFilter('status', this);" class="sp_filter_input">
           <option value="">Все матчи</option>
           <option value="publish">Завершенные</option>
           <option value="future">Предстоящие</option>
        </select>
        <select onchange="SPSNS.scheduleLeagueFilter('predict', this);" class="sp_filter_input">
           <option value="">Прогнозы</option>
           <option value="has_predict">Есть прогноз</option>
        </select>           				
	</div>
	<div class="sp_inner_block sp_filter_main" id="sp_filter_league_content">
		<?php sp_get_template( 'event-filter-league.php', $filter_args, SP()->template_path() . 'event/',  ); ?>
	</div>

</div>

<?php if ( !empty( $content_calendar ) ) : ?>
  	<div class="sp_block">
			<?php echo apply_filters( 'the_content', $content_calendar ); ?>
  	</div> 
<?php endif; ?>

<?php sp_get_template( 'predict-blocks.php', $predicts_args, SP()->template_path() . 'predict/',  ); ?>