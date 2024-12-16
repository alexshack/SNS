<?php
/**
 * Template for single league SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */
get_header();

$tab = 'main';
if ( isset( $wp_query->query['tab'] ) ) {
	$tab = $wp_query->query['tab'];
}

$league = new SP_SNS_League( get_queried_object()->term_id );
$sport  = $league->getSport();

$main_season = SP_SNS_Theme::getCurrentSeason();

if ( isset( $wp_query->query['season'] ) ) {
	$season_slug       = $wp_query->query['season'];
	$season_term       = get_term_by('slug', $season_slug, 'sp_season');
	$season            = new SP_SNS_Season( $season_term->term_id );
	$season_id         = $season->ID;
	$is_current_season = false;
} else {
	$season_slug       = $league->season->term->slug;
	$season_id         = $league->season->ID;
	$season            = $league->season;
	$is_current_season = true;
}

$teams = $league->getTeams( $season_id );

$top_args = [
	'league' => $league->ID,
	'date'   => 'day',
	'season' => $main_season->ID,
	'show_league' => false
];

$stages = $league->getStages( $season_id );

?>

<div class="sp_top_wrapper">
    <div class="sp_filter">
    	<input type="text" value="league" hidden id="sp_filter_top_status">
    	<input type="text" value="<?php echo $league->ID; ?>" hidden id="sp_filter_top_league">
    	<input type="text" value="" hidden id="sp_filter_top_type">
		<select onchange="SPSNS.scheduleTopFilter('team', this);" class="sp_filter_input" id="sp_filter_top_team">
           	<option value="">Все команды</option>
			<?php foreach ( $teams as $team ) : ?>
              	<option value="<?php echo $team->ID; ?>"><?php echo $team->post_title; ?></option>
			<?php endforeach; ?>
		</select>    			
      	<select onchange="SPSNS.scheduleTopFilter('date', this);" class="sp_filter_input" id="sp_filter_top_date">
         	<option value="-w">На прошлой неделе</option>
	        	<option value="-day">Вчера</option>
	        	<option value="day" selected>Сегодня</option>
	        	<option value="+day">Завтра</option>
	        	<option value="w">На этой неделе</option>
	        	<option value="+w">На следующей неделе</option>
      </select> 
    </div>
    <div class="sp_top_content_wrapper" id="sp_filter_top_scroll">
    	<div class="sp_top_content" id="sp_filter_top_content">
    		<?php sp_get_template( 'event-top.php', $top_args, SP()->template_path() . 'event/',  ); ?>
		</div>
    </div>
</div>

<?php echo SP_SNS_Breadcrumbs::setBreadcrumbs(); ?>

<div class="wrapper sp_wrapper">
	<?php include 'league/single/header.php'; ?>
</div>

<div class="wrapper wrapper-bookmaker sp_wrapper">
	
    <div class="main sp_main_page">
    	<?php include 'league/single/' . $tab . '.php'; ?>
    </div>

    <aside class="sidebar">
    	<?php include 'sidebar/sidebar.php'; ?>
    </aside>
</div>

<?php get_footer(); ?>
