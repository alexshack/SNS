<?php
/**
 * Template for single team SNS.
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

$team_id = get_the_ID();
$team = new SP_SNS_Team( $team_id );
$team_permalink = $team->url;

$sport = $team->getSport();

$main_season = SP_SNS_Theme::getCurrentSeason();

if ( isset( $wp_query->query['season'] ) ) {
	$season_slug       = $wp_query->query['season'];
	$season_term       = get_term_by('slug', $season_slug, 'sp_season');
	$season            = new SP_SNS_Season( $season_term->term_id );
	$season_id         = $season->ID;
	$is_current_season = false;
} else {
	$season_slug       = $main_season->slug;
	$season_id         = $main_season->ID;
	$season            = $main_season;
	$is_current_season = true;
}

$main_leagues    = $team->getSeasonLeagues( $main_season->ID );
$current_leagues = $team->getSeasonLeagues( $season_id );

$current_seasons = $team->getSeasonSeasons( $season_id );

$season_ids = [];
foreach ( $current_seasons as $current_season ) {
	$season_ids[] = $current_season->ID;
}

$squads = [];
if ( $sport->type == 'football' ) {
	$squads = $team->getPlayers( $season_id );
}

if ( $team->hasTransfers() ) {
	$transfer_link = $team->url . 'transfers/';
} else {
	$transfer_link = false;
}

$top_args = [
	'team'       => $team_id,
	'date'       => 'w',
	'season'     => $main_season->ID,
];

?>


<div class="sp_top_wrapper">
    <div class="sp_filter">
    	<input type="text" value="sport" hidden id="sp_filter_top_status">
    	<input type="text" value="<?php echo $team_id; ?>" hidden id="sp_filter_top_team">
    	<input type="text" value="" hidden id="sp_filter_top_type">
		<select onchange="SPSNS.scheduleTopFilter('league', this);" class="sp_filter_input" id="sp_filter_top_league">
         	<option value="">Все турниры</option>
			<?php foreach ( $main_leagues as $league ) : ?>
            	<option value="<?php echo $league->ID; ?>"><?php echo $league->name; ?></option>
			<?php endforeach; ?>
		</select>    			
      	<select onchange="SPSNS.scheduleTopFilter('date', this);" class="sp_filter_input" id="sp_filter_top_date">
         	<option value="-w">На прошлой неделе</option>
	        	<option value="-day">Вчера</option>
	        	<option value="day">Сегодня</option>
	        	<option value="+day">Завтра</option>
	        	<option value="w" selected>На этой неделе</option>
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
	<?php include 'team/single/header.php'; ?>
</div>

<div class="wrapper wrapper-bookmaker sp_wrapper">

    <div class="main sp_main_page">
    	<?php include 'team/single/' . $tab . '.php'; ?>
    </div>

    <aside class="sidebar">
    	<?php include 'sidebar/sidebar.php'; ?>
    </aside>
</div>

<?php get_footer(); ?>
