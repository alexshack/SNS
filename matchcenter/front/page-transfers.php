<?php
/**
 * Template for Transfers page SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */
get_header();

$season_id  = get_option('sportspress_season');
$season     = get_term_by('id', $season_id, 'sp_season');

$news_term     = get_term_by('slug', get_option('sportspress_sns_news_term'), 'category' );
$news_link     = get_term_link( $news_term  );

$news_args = [
	'title' => $news_term->name,
	'limit' => 9,
   'main'  => true,
   'terms' => $news_term->term_id,
   'link'  => $news_link   
];


$transfer_args = array(
   'limit'     => 10,
   'order'     => 'DESC',
   'season'    => $season_id,
   'show_more' => true,
);


$leagues_block_args = array(
   'title'     => 'По турнирам',
   'slug'      => 'transfers/',
   'hide_transfers' => true,
   'type' => 'football'
);

$league_args = [
   'taxonomy' => 'sp_league',
   'meta_query' => [
      'relation' => 'AND',
      [
         'key' => 'sport_type',
         'value' => 'football'
      ],
      [
         'key' => 'hide_transfers',
         'value' => 'no',
      ]
   ],
   'fields' => 'ids',
];

$leagues_ids = get_terms( $league_args );

$teams_args = [
   'post_type'      => 'sp_team',
   'posts_per_page' => -1,
   'status'         => 'publish',
   'orderby'        => 'post_title',
   'order'          => 'ASC',
   'tax_query'      => [
      'relation' => 'AND',
      [
         'taxonomy' => 'sp_league',
         'field'    => 'term_id',
         'terms'    => $leagues_ids,
      ],
      [
         'taxonomy' => 'sp_season',
         'field'    => 'term_id',
         'terms'    => $season_id,
      ]
   ]          
];

$teams_query = new WP_Query;
$teams = $teams_query->query($teams_args);


$types_args = [
    'taxonomy' => 'sp_transfer_type',
];

$types = get_terms( $types_args );

$leagues = get_terms( [
  'taxonomy'   => 'sp_league',
  'meta_key'   => 'sport_type',
  'meta_value' => 'football'
] );

usort( $leagues, 'sp_sort_terms' );

$top_args = [
   'sport_type' => 'football',
   'date'       => 'day',
   'season'     => $season_id,
];

$content = apply_filters( 'the_content', get_the_content() );

?>

<div class="sp_top_wrapper">
    <div class="sp_filter">
      <input type="text" value="sport" hidden id="sp_filter_top_status">
      <input type="text" value="" hidden id="sp_filter_top_team">
      <input type="text" value="football" hidden id="sp_filter_top_type">
      <select onchange="SPSNS.scheduleTopFilter('league', this);" class="sp_filter_input" id="sp_filter_top_league">
         <option value="">Все турниры</option>
         <?php foreach ( $leagues as $league ) : ?>
            <option value="<?php echo $league->term_id; ?>"><?php echo $league->name; ?></option>
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

<?php if ( have_posts() ) {
	while ( have_posts() ) : the_post(); ?>

<div class="wrapper wrapper-bookmaker sp_wrapper">
	
    <div class="main sp_main_page">

      <div class="sp_block">
         <div class="sp_block_title">
            <h1><?php echo get_the_title(); ?></h1>
         </div>
         <?php sp_get_template( 'league-blocks.php', $leagues_block_args, SP()->template_path() . 'league/',  ); ?>
      </div>


      <div class="sp_block" id="sp_filter_transfer">
         <div class="sp_block_title">
            <h2 id="sp_filter_transfer_title">Трансферы <?php echo $season->name; ?></h2>
         </div>
         <div class="sp_filter">
            <input type="text" value="0" hidden id="sp_filter_transfer_league">
            <input type="text" value="" hidden id="sp_filter_transfer_team">
            <input type="text" value="<?php echo $season_id; ?>" hidden id="sp_filter_transfer_season">
               
            <select onchange="SPSNS.scheduleTransferFilter('team_out', this);" class="sp_filter_input" id="sp_filter_transfer_out">
               <option value="">Уходит из</option>
               <?php foreach ( $teams as $team ) : ?>
                  <option value="<?php echo $team->ID; ?>"><?php echo $team->post_title; ?></option>
               <?php endforeach; ?>
            </select>
            <select onchange="SPSNS.scheduleTransferFilter('team_in', this);" class="sp_filter_input" id="sp_filter_transfer_in">
               <option value="">Перешел в</option>
               <?php foreach ( $teams as $team ) : ?>
                  <option value="<?php echo $team->ID; ?>"><?php echo $team->post_title; ?></option>
               <?php endforeach; ?>
            </select>      
            <select onchange="SPSNS.scheduleTransferFilter('type', this);" class="sp_filter_input" id="sp_filter_transfer_type">
               <option value="">Тип трансфера</option>
               <?php foreach ( $types as $type ) : ?>
                  <option value="<?php echo $type->term_id; ?>"><?php echo $type->name; ?></option>
               <?php endforeach; ?>
            </select>                     
            <select onchange="SPSNS.scheduleTransferFilter('status', this);" class="sp_filter_input" id="sp_filter_transfer_status">
               <option value="">Все трансферы</option>
               <option value="publish">Состоявшиеся</option>
               <option value="future">Предстоящие</option>
            </select>
                       
         </div>
         <div class="sp_filter_transfer" id="sp_filter_transfer_content">
            <?php sp_get_template( 'transfer-rows.php', $transfer_args, SP()->template_path() . 'transfer/',  ); ?>
         </div>

      </div>
    	
    	<?php sp_get_template( 'news-blocks.php', $news_args, SP()->template_path() . 'news/',  ); ?>
      
      
      <?php if ( !empty( $content ) ) : ?>
         <div class="sp_block">
            <?php echo $content; ?>
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
