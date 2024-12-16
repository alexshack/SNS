<?php
/**
 * Transfers Blocks
 *
 * @author      Alex Torbeev
 * @package     SportsPress SNS/Templates
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


$defaults = array(
   'limit'     => 10,
   'title'     => null,
   'paginated' => false,
   'status'    => 'any',
   'orderby'   => 'date',
   'order'     => 'DESC',
   'season'    => null,
   'league'    => null,
   'team'      => null,
   'team_in'   => null,
   'team_out'  => null,
   'link'      => false,
   'type'      => null,
   'title_tag' => 'h3',
   'offset'    => 0,
   'show_more' => false,
);

extract( $defaults, EXTR_SKIP );

$more_args = 'data-offset="' . $limit + $offset . '"';

$post_args = [
	'post_type'      => 'sp_transfer',
	'posts_per_page' => $limit,
	'orderby'        => $orderby,
	'order'          => $order,
	'post_status'    => $status,
	'tax_query'      => [
		'relation'    => 'AND'
	]
];

if ( $offset ) {
	$post_args['offset'] = $offset;
}

if ( $season && $league ) {
	$post_args['tax_query']['relation'] = 'AND';
}

if ($season) {

	$post_args['tax_query'][] = [
		'taxonomy' => 'sp_season',
		'field'    => 'term_id',
		'terms'    => $season,
	];
	if ( is_array( $season ) ) {
		$season_ids = implode(',', $season);
	} else {
		$season_ids = $season;
	}
	$more_args .= ' data-season="' . $season_ids . '"'; 
}

if ($league) {
	$post_args['tax_query'][] = [
		'taxonomy' => 'sp_league',
		'field'    => 'term_id',
		'terms'    => $league,
	];

	$more_args .= ' data-league="' . $league . '"'; 
}

if ($type) {
	$post_args['tax_query'][] = [
		'taxonomy' => 'sp_transfer_type',
  		'field'    => 'term_id',
  		'terms'    => $type,
	];
	$more_args .= ' data-type="' . $type . '"'; 
}

if ($team) {
	$post_args['meta_query'] = [
		'relation' => 'OR',
		[
			'key'   => 'sp_team_in',
  			'value' => $team,
		],
		[
			'key'   => 'sp_team_out',
  			'value' => $team,
		],		
	];

	$more_args .= ' data-team="' . $team . '"'; 
}

if ( $team_in && $team_out ) {
	$post_args['meta_query']['relation'] = 'AND';
}

if ( $team_in ) {
	$post_args['meta_query'][] = [
		'key'   => 'sp_team_in',
  		'value' => $team_in,
	];

	$more_args .= ' data-teamin="' . $team_in . '"'; 
}

if ( $team_out ) {
	$post_args['meta_query'][] = [
		'key'   => 'sp_team_out',
  		'value' => $team_out,
	];

	$more_args .= ' data-teamout="' . $team_out . '"'; 
}

$posts_query = new WP_Query;

$transfers = $posts_query->query($post_args);

if ( $transfers ) :

if ( !$offset ) :

if ( $title ) {
	echo '<div class="sp_block_title">';
	echo '<' . $title_tag . '>' . wp_kses_post( $title ) . '</' . $title_tag . '>';
	if ( $link ) {
		echo '<a href="' . $link . '">Все трансферы</a>';
	}
	echo '</div>';
}

?>

<div class="sp_transfer_header">
	<div class="sp_transfer_header_player">Игрок</div>
	<div class="sp_transfer_header_teams">
		<div class="sp_transfer_header_team">Уходит из</div>
		<div class="sp_transfer_header_team">Переходит в</div>
	</div>
	<div class="sp_transfer_header_info">
		<div class="sp_transfer_header_date">Дата</div>
		<div class="sp_transfer_header_summ">Сумма</div>
	</div>	
</div>


	<div class="sp_transfer_rows" id="sp_transfer_rows">
<?php endif; ?>

	<?php foreach ( $transfers as $transfer_post ) : ?>
		<?php $transfer = new SP_SNS_Transfer($transfer_post); ?>
		<div class="sp_transfer_row">

			<div class="sp_transfer_row_player">
				<img class="sp_transfer_row_img lazy lozad" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $transfer->player->logo; ?>" width="38" height="38" alt="<?php echo wp_kses_post( $transfer->player->post->post_title ); ?>" title="<?php echo wp_kses_post( $transfer->player->post->post_title ); ?>">
				<div class="sp_transfer_row_player_info">
					<div class="sp_transfer_row_name"><?php echo wp_kses_post( $transfer->player->post->post_title ); ?></div>
					<div class="sp_transfer_row_position"><?php echo wp_kses_post( $transfer->player->position ); ?></div>
				</div>				
			</div>

			<div class="sp_transfer_row_teams">
				<div class="sp_transfer_row_team">
					<div class="sp_transfer_row_team_type">Уходит из</div>
					<div class="sp_transfer_row_team_team">
						<img class="sp_transfer_row_team_img lazy lozad" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $transfer->team_out->logo; ?>" width="20" height="20" title="<?php echo wp_kses_post( $transfer->team_out->post->post_title ); ?>">
						<div class="sp_transfer_row_team_name"><?php echo wp_kses_post( $transfer->team_out->post->post_title ); ?></div>
					</div>
				</div>
				<div class="sp_transfer_row_team">
					<div class="sp_transfer_row_team_type">Переходит в</div>
					<div class="sp_transfer_row_team_team">
						<img class="sp_transfer_row_team_img lazy lozad" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $transfer->team_in->logo; ?>" width="20" height="20" title="<?php echo wp_kses_post( $transfer->team_in->post->post_title ); ?>">
						<div class="sp_transfer_row_team_name"><?php echo wp_kses_post( $transfer->team_in->post->post_title ); ?></div>
					</div>
				</div>				
			</div>

			<div class="sp_transfer_row_infos">
				<div class="sp_transfer_row_info">
					<div class="sp_transfer_row_info_key">Дата</div>
					<div class="sp_transfer_row_info_name"><?php echo wp_date('d.m.y', strtotime($transfer->post->post_date)); ?></div>
				</div>
				<div class="sp_transfer_row_info">
					<div class="sp_transfer_row_info_key">Сумма</div>
					<div class="sp_transfer_row_info_name"><?php echo $transfer->type; ?></div>
				</div>				
			</div>
			
		</div>
	<?php endforeach; ?>
	


<?php if ( !$offset ) : ?>	
	</div>
	<?php if ( $show_more && count( $transfers ) == $limit ) : ?>
		<div class="sp_more_btn_wrapper">
			<button class="sp_more_btn" <?php echo $more_args; ?> onclick="SPSNS.scheduleTransferMore(this);" id="sp_transfers_more">Показать все</button>
		</div>
	<?php endif; ?>
<?php endif; ?>

<?php else: ?>
	<div class="sp_transfer_rows">
		<div class="sp_transfer_row">В выбранный период события не найдены</div>
	</div>
<?php endif; ?>