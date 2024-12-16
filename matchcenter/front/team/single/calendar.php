<?php
/**
 * Template part for team Calendar page SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

$filter_args = array(
	'team'   => $team_id,
	'season_id' => $season_id,
);

$predicts_args = [
	'title' => 'Прогнозы на матчи '  . $team->post->post_title,
	'link'  => false,
	'posts' => $team->getPredicts(6)	
];

$content_calendar = apply_filters( 'the_content', get_post_meta( $team_id, 'content_calendar', true ) );

$leagues = wp_get_post_terms( $team_id, 'sp_league' );

?>

<div class="sp_block" id="sp_filter_team">
	<div class="sp_block_title">
		<h2 id="sp_filter_team_title">Результаты и календарь <?php echo $team->post->post_title; ?></h2>
	</div>
	<div class="sp_filter">
		<input type="text" value="<?php echo $team_id; ?>" hidden id="sp_filter_team_team">
		<input type="text" value="<?php echo $season_id; ?>" hidden id="sp_filter_team_season">
 			
      <select onchange="SPSNS.scheduleTeamFilter('league', this);" class="sp_filter_input">
         <option value="">Лига</option>
			<?php foreach ( $leagues as $league ) : ?>
           	<option value="<?php echo $league->term_id; ?>"><?php echo $league->name; ?></option>
			<?php endforeach; ?>
      </select>
      <select onchange="SPSNS.scheduleTeamFilter('date', this);" class="sp_filter_input" id="sp_filter_team_date">
         <option value="" selected>Все матчи</option>
        	<option value="-w">На прошлой неделе</option>
        	<option value="-day">Вчера</option>
        	<option value="day">Сегодня</option>
        	<option value="+day">Завтра</option>
        	<option value="w">На этой неделе</option>
        	<option value="+w">На следующей неделе</option>
      </select>            				
      <select onchange="SPSNS.scheduleTeamFilter('status', this);" class="sp_filter_input">
         <option value="">Все матчи</option>
         <option value="publish">Завершенные</option>
         <option value="future">Предстоящие</option>
      </select>
      <select onchange="SPSNS.scheduleTeamFilter('predict', this);" class="sp_filter_input">
         <option value="">Прогнозы</option>
         <option value="has_predict">Есть прогноз</option>
      </select>           				
	</div>
	<div class="sp_inner_block sp_filter_main" id="sp_filter_team_content">
		<?php sp_get_template( 'event-filter-team.php', $filter_args, SP()->template_path() . 'event/',  ); ?>
	</div>

</div>

<?php if ( !empty( $content_calendar ) ) : ?>
	<div class="sp_block">
	   <?php echo $content_calendar; ?>
	</div>
<?php endif; ?>

<?php sp_get_template( 'predict-blocks.php', $predicts_args, SP()->template_path() . 'predict/' ); ?>