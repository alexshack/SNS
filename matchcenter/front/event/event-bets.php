<?php
/**
 * Event Bets Block
 *
 * @author      Alex Torbeev
 * @package     SportsPress/Templates
 * @version     2.7.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$defaults = array(
	'bets'                 => [],
	'class'                => false,
);

extract( $defaults, EXTR_SKIP ); 

?>

<div class="sp_bets <?php echo $class ? $class : '' ?>">
<?php foreach ( $bets as $key => $bet ) : ?>
	<a class="sp_bet" href="<?php echo $bet['url']; ?>" title="<?php echo $key . ' ' . $bet['name']; ?>" target="blank" rel="nofollow">
		<?php echo $bet['coef']; ?>
		<div class="sp_bet_image">
			<?php echo $key . ' '; ?><img width="131" height="40" class="lazy lozad wp-post-image" data-src="<?php echo $bet['image']; ?>" src="" alt="<?php echo $bet['name']; ?>">
		</div> 
	</a>
<?php endforeach; ?>
</div>

