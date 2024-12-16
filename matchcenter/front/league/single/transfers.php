<?php
/**
 * Template part for league transfers block SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

if ( $league->news_term ) {
   $news_args = [
      'title' => $league->news_term->name,
      'link'  => $league->news_link,
      'posts' => $league->getNews( 6 ) 
   ];
} else {
   $news_args = [
      'title' => 'Новости',
      'link'  => false,
      'posts' => $league->getNews( 6 )
   ]; 
}


$transfer_args = array(
   'limit'   => 10,
   'league'  => $league->ID,
   'season'  => $season_id,
   'show_more' => true,
);


$types_args = [
    'taxonomy' => 'sp_transfer_type',
];

$types = get_terms( $types_args );

$content_transfers = get_term_meta( $league->ID, 'content_transfers', true );

?>

<div class="sp_block" id="sp_filter_transfer">
   <div class="sp_block_title">
      <h2 id="sp_filter_transfer_title">Трансферы <?php echo $league->name; ?></h2>
   </div>
   <div class="sp_filter">
      <input type="text" value="<?php echo $league->ID; ?>" hidden id="sp_filter_transfer_league">
      <input type="text" value="<?php echo $season_id; ?>" hidden id="sp_filter_transfer_season">
      <input type="text" value="" hidden id="sp_filter_transfer_team">
         
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


<?php if ( !empty( $content_transfers ) ) : ?>
   <div class="sp_block">
         <?php echo apply_filters( 'the_content', $content_transfers ); ?>
   </div> 
<?php endif; ?>


<?php sp_get_template( 'news-blocks.php', $news_args, SP()->template_path() . 'news/',  ); ?>

