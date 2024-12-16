<?php
/**
 * Template part for league Main page SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */



$news_args = [
	'title' => $league->news_term ? $league->news_term->name : '',
	'link'  => $league->news_link,
	'posts' => $league->getNews( 6 )	
];

$articles_args = [
	'title' => $league->articles_term ? $league->articles_term->name : '',
	'link'  => $league->articles_link,
	'posts' => $league->getArticles( 6 ),
	'button' => 'Все статьи'	
];


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

$date_from = wp_date('Y-m-d', strtotime('-3 days'));
$date_to   = wp_date('Y-m-d', strtotime('+3 days'));

$filter_args = array(
	'sports'     => [ $sport ],
	'leagues'    => [ $league->ID ],
	'date_from'  => $date_from,
	'date_to'    => $date_to,	
);

$transfer_args = array(
   'limit'     => 10,
   'title'     => false,
   'status'    => 'any',
   'order'     => 'DESC',
   'title_tag' => 'h3',
   'season'    => $season_id,
   'league'    => $league->ID,
);


$transfer_link = get_term_link( $league->ID, 'sp_league' ) . 'transfers/';


$teams_args = [
  'league' => $league->ID,
  'season' => $season_id,
];

$tables = $league->getTables( $season );

$content_main = get_term_meta( $league->ID, 'content_main', true );

$type    = $sport->type . '_league';
$layouts = get_option( 'sportspress_' . $type . '_template_order' );

?>

<?php foreach ( $layouts as $layout ) : 
	
	$visible = get_option( 'sportspress_' . $type . '_show_' . $layout );

	if ( $visible && $visible == 'yes' ) :
	?>
		<?php if ( $layout == 'teams' ) : ?>
			<div class="sp_block">
				<?php sp_get_template( 'team-blocks.php', $teams_args, SP()->template_path() . 'team/',  ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $layout == 'events' ) : ?>
			<div class="sp_block" id="sp_filter_main">
				<div class="sp_block_title">
					<h2 id="sp_filter_main_title">Матчи с <?php echo wp_date('j F', strtotime($date_from)) . ' по ' . wp_date('j F', strtotime($date_to)); ?></h2>
				</div>
				<div class="sp_filter">
					<input type="text" value="" hidden id="sp_filter_main_offset">
					<input type="text" value="<?php echo $sport->type; ?>" hidden id="sp_filter_main_type">
					<input type="text" value="<?php echo $league->ID; ?>" hidden id="sp_filter_main_league">
					<div class="sp_filter_input"><?php echo $sport->name; ?></div>
					<div class="sp_filter_input"><?php echo $league->name; ?></div>

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

		<?php if ( $layout == 'transfers' && ! $league->hide_transfers ) : ?>
			<div class="sp_block">
				<div class="sp_block_title">
					<h2>Трансферы <?php echo $league->name; ?></h2>
					<a href="<?php echo $transfer_link; ?>">Все трансферы</a>
				</div>
				<?php sp_get_template( 'transfer-rows.php', $transfer_args, SP()->template_path() . 'transfer/',  ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $layout == 'bonuses' ) : ?>
			<?php echo do_shortcode( '[bonuses-slider bonus_type="best" title="Лучшие бонусы для ставок на ' . $league->name . '" type_link="vse-bonusy-bukmekerov" type_text="все бонусы"]' ); ?>
		<?php endif; ?>

		<?php if ( $layout == 'tables' && $tables ) : ?>
			<?php 
			$number = 6;
			if (count($tables) > 1) $number = -1;
			foreach ($tables as $table) { 

				$table_args = array(
					'id'         => $table->ID,
					'show_title' => true,
					'tab'        => $tab,
					'number'     => $number
				);	
				sp_get_template( 'league-table.php', $table_args, SP()->template_path() . 'league/'  ); 
			}
			?>
		<?php endif; ?>

		<?php if ( $layout == 'content' && ! empty( $content_main ) ) : ?>
	    	<div class="sp_block">
	  			<?php echo apply_filters( 'the_content', $content_main ); ?>
	    	</div> 
		<?php endif; ?>

	<?php endif; ?>
<?php endforeach; ?>