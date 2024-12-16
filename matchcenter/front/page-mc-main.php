<?php
/**
 * Template for main page match-center SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */
get_header();

$season_id = get_option('sportspress_season');
$season    = get_term_by('id', $season_id, 'sp_season');
$sports    = SP_SNS_Theme::getSports();


$top_args = [
	'date'    => 'day',
	'season'  => $season_id,
];

$date_from = wp_date('Y-m-d', strtotime('-1 days'));
$date_to   = wp_date('Y-m-d', strtotime('+1 days'));

$filter_args = [
	'show_terms' => true,
	'date_from'  => $date_from,
	'date_to'    => $date_to,
];

$news_args = [
	'title' => 'Новости',
	'link'  => '/novosti/',
	'posts' => SP_SNS_Theme::getNews( 6 )
];

$articles_args = [
	'title' => 'Статьи',
	'link'  => '/novosti/',
	'posts' => SP_SNS_Theme::getArticles( 3 ),
	'button' => 'Все статьи'
];

$predicts = SP_SNS_Theme::getPredicts( 6 );

$predicts_args = [
	'title' => 'Прогнозы на матчи',
	'link'  => '/prognozy/',
	'posts' => $predicts
];

$leagues = SP_SNS_Theme::getLeagues();

?>

<?php if ( have_posts() ) {
	while ( have_posts() ) : the_post(); ?>

<div class="sp_top_wrapper">
    <div class="sp_filter">
    	<input type="text" value="main" hidden id="sp_filter_top_status">
    	<input type="text" value="" hidden id="sp_filter_top_team">
    	<input type="text" value="" hidden id="sp_filter_top_league">
		<select onchange="SPSNS.scheduleTopFilter('type', this);" class="sp_filter_input" id="sp_filter_top_type">
		   	<option value="">Весь спорт</option>
		   	<?php foreach ( $sports as $sport ) : ?>
		   		<option value="<?php echo $sport->type; ?>"><?php echo $sport->name; ?></option>
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

<div class="wrapper wrapper-bookmaker sp_wrapper">

    <div class="main sp_main_page">
    	<?php
/* 		echo '<pre>';
		print_r($predicts);
		echo '</pre>';*/
		?>
    	
    	<div class="sp_block" id="sp_filter_main">
    		<div class="sp_block_title">
    			<h1 id="sp_filter_main_title">Матчи с <?php echo wp_date('j F', strtotime($date_from)) . ' по ' . wp_date('j F', strtotime($date_to)); ?></h1>
    		</div>
    		<div class="sp_filter">
    			<input type="text" value="1" hidden id="sp_filter_main_offset">
	            <select onchange="SPSNS.scheduleMainFilter('type', this);" class="sp_filter_input" id="sp_filter_main_type">
	               <option value="all" selected>Весь спорт</option>
				   	<?php foreach ( $sports as $sport ) : ?>
				   		<option value="<?php echo $sport->type; ?>"><?php echo $sport->name; ?></option>
					<?php endforeach; ?>
	            </select>

		        <select onchange="SPSNS.scheduleMainFilter('league', this);" class="sp_filter_input" id="sp_filter_main_league">
	               <option value="">Все турниры</option>
						<?php foreach ( $leagues as $league ) : ?>
	                  		<option value="<?php echo $league->ID; ?>"><?php echo $league->name; ?></option>
						<?php endforeach; ?>
	            </select>  
	            <div class="sp_filter_input sp_filter_input_date"> 				
					<input 
					onchange="SPSNS.scheduleMainFilter('date_from', this);" 
					type="date" 
					class="sp_filter_input" 
					value="<?php echo $date_from; ?>" 
					max="<?php echo $date_to; ?>" 
					id="sp_filter_main_date_from">
					<input 
					onchange="SPSNS.scheduleMainFilter('date_to', this);" 
					type="date" 
					class="sp_filter_input" 
					value="<?php echo $date_to; ?>" 
					min="<?php echo $date_from; ?>" 
					id="sp_filter_main_date_to">
				</div>    				
	            <select onchange="SPSNS.scheduleMainFilter('status', this);" class="sp_filter_input" id="sp_filter_main_status">
	               <option value="">Все матчи</option>
	               <option value="publish" id="sp_filter_main_status_publish">Завершенные</option>
	               <option value="future" id="sp_filter_main_status_future">Предстоящие</option>
	            </select>    				
    		</div>
    		<div class="sp_inner_block sp_filter_main" id="sp_filter_main_content">
    			<?php sp_get_template( 'event-filter-main.php', $filter_args, SP()->template_path() . 'event/',  ); ?>
    		</div>

     	</div>

     	<?php sp_get_template( 'predict-blocks.php', $predicts_args, SP()->template_path() . 'predict/',  ); ?>
    	
    	<?php sp_get_template( 'news-blocks.php', $news_args, SP()->template_path() . 'news/',  ); ?>
    	<?php sp_get_template( 'news-blocks.php', $articles_args, SP()->template_path() . 'news/',  ); ?>
 
     	<?php 
    	$content = apply_filters( 'the_content', get_the_content() );
    	if ( !empty( $content ) ) :
    	?>
	    	<div class="sp_block">
	  			<?php the_content() ?>
	    	</div> 
	   <?php endif; ?>   
    </div>

    <aside class="sidebar">
    	<?php include 'sidebar/sidebar.php'; ?>
    </aside>
</div>
		<?php endwhile;
	} ?>
<?php get_footer(); ?>
