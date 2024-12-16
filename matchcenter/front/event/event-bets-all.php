<?php
/**
 * Event All Bets Block
 *
 * @author      Alex Torbeev
 * @package     SportsPress/Templates
 * @version     2.7.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$defaults = array(
	'bets'  => [],
	'class'  => false,
);

extract( $defaults, EXTR_SKIP ); 

?>

<?php if ( count( $bets ) ) : ?>
	<div class="sp-table-wrapper">
		<div class="sp-scrollable-table-wrapper">
			<table class="sp-data-table sp_coef_table">
				<thead>
					<tr>
						<th>Букмекер</th>
						<th class="text-center">П1</th>
						<th class="text-center">Ничья</th>
						<th class="text-center">П2</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $bets as $key => $bet ) : ?>
						<tr>
							<td>
								<a href="<?php echo $bet['bk_link']; ?>">
									<img width="131" height="40" class="lazy lozad wp-post-image" data-src="<?php echo $bet['bk_image']; ?>" src="" alt="<?php echo $bet['bk_name']; ?>">
								</a>
							</td>
							<td class="text-center">
								<a class="sp_bet" href="<?php echo $bet['bet_link']; ?>" title="<?php echo 'П1 ' . $bet['bk_name']; ?>" target="blank" rel="nofollow">
									<?php echo $bet['П1']; ?>
								</a>
							</td>
							<td class="text-center">
								<a class="sp_bet" href="<?php echo $bet['bet_link']; ?>" title="<?php echo 'Х ' . $bet['bk_name']; ?>" target="blank" rel="nofollow">
									<?php echo $bet['X']; ?>
								</a>
							</td>
							<td class="text-center">
								<a class="sp_bet" href="<?php echo $bet['bet_link']; ?>" title="<?php echo 'П2 ' . $bet['bk_name']; ?>" target="blank" rel="nofollow">
									<?php echo $bet['П2']; ?>
								</a>
							</td>				
						</tr>
					<?php endforeach; ?>
				</tbody>

			</table>
		</div>
	</div>	
<?php endif; ?>
