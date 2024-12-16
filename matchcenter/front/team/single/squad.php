<?php
/**
 * Template part for league main block SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */


$keys = array_keys($squads); 

?>
<div class="sp_block_title"><h2>Состав</h2></div>
<div class="block-wrapper">
	<?php if ( count( $keys ) > 1 ) : ?>
		<div class="sp_block_title_btns">
			<div class="sp_block_title_btn block-btn active" data-block="all">Все турниры</div>
			<?php foreach ( $keys as $key => $index ) : ?>
				<div class="sp_block_title_btn block-btn" data-block="block-<?php echo $key ?>"><?php echo $index; ?></div>
			<?php endforeach; ?>

		</div>
	<?php endif; ?>

	<?php foreach ( $keys as $key => $index ) : ?>
		<div class="sp_block block-content open" id="block-<?php echo $key ?>">
			<div class="sp_block_title"><h3><?php echo $index; ?></h3></div>
			<div class="sp-table-wrapper">
				<table class="sp-league-table sp-data-table sp_table_rows sp-sortable-table">
					<thead>
						<tr>
							<th class="data-name" role="columnheader">Игрок</th>
							<th class="data-position" role="columnheader">Позиция</th>
							<th class="data-p" role="columnheader">И</th>
							<th class="data-f" role="columnheader">Мин</th>
							<th class="data-p" role="columnheader">ГЗ</th>
							<th class="data-p" role="columnheader">ГП</th>
							<th class="data-p" role="columnheader">Асс</th>
							<th class="data-p" role="columnheader">ЖК</th>
							<th class="data-p" role="columnheader">КК</th>
							<th class="data-f" role="columnheader">Оц</th>							
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $squads[ $index ] as $player_id => $player ) : ?>
							<?php
							$player_post = get_post( $player_id );
							$photo = get_the_post_thumbnail_url( $player_id, 'sportspress-fit-mini' );
							$positions = wp_get_post_terms( $player_id, 'sp_position' );
							if ( count( $positions ) ) {
								$position = $positions[0];
							}
							?>
							<tr>
								<td class="data-name" >
									<span class="team-logo">
										<img class="sp_player_table_img lazy lozad" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $photo; ?>" width="38" height="38" alt="<?php echo wp_kses_post( $player_post->post_title ); ?>" title="<?php echo wp_kses_post( $player_post->post_title ); ?>">
									</span>
									<?php echo $player_post->post_title; ?>
									<?php echo $player['captain'] ? ' (К)' : ''; ?>
								</td>
								<td class="data-position" ><?php echo $position->name; ?></td>
								<td class="data-p"><?php echo $player['games']; ?></td>
								<td class="data-f" ><?php echo $player['minutes']; ?></td>
								<td class="data-p" ><?php echo $player['goals']; ?></td>
								<td class="data-p"><?php echo $player['conceded']; ?></td>
								<td class="data-p"><?php echo $player['assists']; ?></td>
								<td class="data-p"><?php echo $player['yellow']; ?></td>
								<td class="data-p"><?php echo $player['red']; ?></td>
								<td class="data-f"><?php echo $player['rating']; ?></td>																																							
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	<?php endforeach; ?>	
</div>