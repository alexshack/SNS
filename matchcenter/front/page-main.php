<?php
/**
 * Template for main page SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */
get_header();

$sport = SP_SNS_Theme::getCurrentSport();

$season_id     = get_option('sportspress_season');
$season        = get_term_by('id', $season_id, 'sp_season');


$leagues_args = array(
	'type'      => $sport->type,
   'title'     => null,
);

$date_from = wp_date('Y-m-d', strtotime('-2 days'));
$date_to   = wp_date('Y-m-d', strtotime('+2 days'));

$filter_args = array(
	'sports'     => [ $sport ],
	'date_from'  => $date_from,
	'date_to'    => $date_to,	
);

$news_args = [
	'title' => $sport->news_term ? $sport->news_term->name : '',
	'link'  => $sport->news_link,
	'posts' => $sport->getNews(6)
];

$articles_args = [
	'title' => $sport->articles_term ? $sport->articles_term->name : '',
	'link'  => $sport->articles_link,
	'posts' => $sport->getArticles(3),
	'button' => 'Все статьи'
];

$predicts_args = [
	'title' => 'Прогнозы на матчи',
	'link'  => $sport->predicts_link,
	'posts' => $sport->getPredicts(6)
];

$transfer_args = array(
   'limit'     => 5,
   'title'     => 'Трансферы',
   'status'    => 'any',
   'order'     => 'DESC',
   'title_tag' => 'h3',
   'season'    => $season_id
);

$transfer_next_args = array(
   'limit'     => 5,
   'title'     => 'Предстоящие трансферы',
   'status'    => 'future',
   'order'     => 'ASC',
   'title_tag' => 'h3',
   'season'    => $season_id
);


$leagues = $sport->getLeagues();

$top_args = [
	'sport_type' => $sport->type,
	'date'       => 'day',
	'season'     => $season_id,
];


$content = apply_filters( 'the_content', get_the_content() );

$type   = $sport->type . '_sport';
$layouts = get_option( 'sportspress_' . $type . '_template_order' );



?>

<?php if ( have_posts() ) {
	while ( have_posts() ) : the_post(); ?>


<div class="sp_top_wrapper">
    <div class="sp_filter">
    	<input type="text" value="sport" hidden id="sp_filter_top_status">
    	<input type="text" value="" hidden id="sp_filter_top_team">
    	<input type="text" value="<?php echo $sport->type; ?>" hidden id="sp_filter_top_type">
		<select onchange="SPSNS.scheduleTopFilter('league', this);" class="sp_filter_input" id="sp_filter_top_league">
         <option value="">Все турниры</option>
			<?php foreach ( $leagues as $league ) : ?>
            <option value="<?php echo $league->ID; ?>"><?php echo $league->name; ?></option>
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

<div class="wrapper wrapper-bookmaker sp_wrapper">
	
    <div class="main sp_main_page">
 
		<div class="sp_block">
			<div class="sp_block_title sp_block_title-main">
				<h1 class="sp_h1"><?php echo get_the_title() . ': ' . $season->name; ?></h1>
				<div class="sp_block_title_btns">
					<?php if ( $sport->transfer_link ) : ?>
						<a href="<?php echo $sport->transfer_link; ?>">Трансферы</a>
					<?php endif; ?>
					<?php if ( $sport->predicts_link ) : ?>
						<a href="<?php echo $sport->predicts_link; ?>">Прогнозы</a>
					<?php endif; ?>
					<?php if ( $sport->news_link ) : ?>
						<a href="<?php echo $sport->news_link; ?>">Новости</a>
					<?php endif; ?>
					<?php if ( $sport->articles_link ) : ?>
						<a href="<?php echo $sport->articles_link; ?>">Статьи</a>
					<?php endif; ?>					
				</div>
			</div>
			
		</div>

    	<?php foreach ( $layouts as $layout ) : 
    		
    		$visible = get_option( 'sportspress_' . $type . '_show_' . $layout );

    		if ( $visible && $visible == 'yes' ) :
    		?>
    			<?php if ( $layout == 'leagues' ) : ?>
    				<div class="sp_block">
    					<?php sp_get_template( 'league-blocks.php', $leagues_args, SP()->template_path() . 'league/',  ); ?>
    				</div>
    			<?php endif; ?>

    			<?php if ( $layout == 'events' ) : ?>
			    	<div class="sp_block" id="sp_filter_main">
			    		<div class="sp_block_title">
			    			<h2 id="sp_filter_main_title">Матчи с <?php echo wp_date('j F', strtotime( $date_from )) . ' по ' . wp_date('j F', strtotime( $date_to )); ?></h2>
			    		</div>
			    		<div class="sp_filter">
			  				<input type="text" value="" hidden id="sp_filter_main_offset">
			  				<input type="text" value="<?php echo $sport->type; ?>" hidden id="sp_filter_main_type">
			  				<div class="sp_filter_input"><?php echo $sport->name; ?></div>
				         <select onchange="SPSNS.scheduleMainFilter('league', this);" class="sp_filter_input" id="sp_filter_main_league">
			               <option value="">Все турниры</option>
								<?php foreach ( $leagues as $league ) : ?>
			                  <option value="<?php echo $league->ID; ?>"><?php echo $league->term->name; ?></option>
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
    			<?php endif; ?>

    			<?php if ( $layout == 'predicts' ) : ?>
					<?php sp_get_template( 'predict-blocks.php', $predicts_args, SP()->template_path() . 'predict/',  ); ?>
    			<?php endif; ?>

    			<?php if ( $layout == 'news' ) : ?>
					<?php sp_get_template( 'news-blocks.php', $news_args, SP()->template_path() . 'news/',  ); ?>
    			<?php endif; ?>

    			<?php if ( $layout == 'articles' ) : ?>
					<?php sp_get_template( 'news-blocks.php', $articles_args, SP()->template_path() . 'news/',  ); ?>
    			<?php endif; ?>

    			<?php if ( $layout == 'transfers' && isset( $transfer_link ) ) : ?>
		    		<div class="sp_block">
		    			<div class="sp_block_title">
		    				<h2>Трансферы <?php echo $season->name; ?></h2>
		    				<a href="<?php echo $transfer_link; ?>">Все трансферы сезона</a>
		    			</div>
		   			<?php sp_get_template( 'transfer-rows.php', $transfer_args, SP()->template_path() . 'transfer/',  ); ?>
		   		</div>
    			<?php endif; ?>

    			<?php if ( $layout == 'bonuses' ) : ?>
					<?php echo do_shortcode( '[bonuses-slider bonus_type="best" title="Лучшие бонусы для ставок на ' . $sport->name . '" type_link="vse-bonusy-bukmekerov" type_text="все бонусы"]' ); ?>
    			<?php endif; ?>

    			<?php if ( $layout == 'content' && !empty( $content ) ) : ?>
			    	<div class="sp_block">
			  			<?php echo $content; ?>
			    	</div> 
    			<?php endif; ?>
    	
    		<?php endif; ?>
    	<?php endforeach; ?>

    </div>

    <aside class="sidebar">
    	<?php include 'sidebar/sidebar.php'; ?>
    </aside>
</div>
		<?php endwhile;
	} ?>
<?php get_footer(); ?>
