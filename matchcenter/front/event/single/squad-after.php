<?php
/**
 * Template part for event main block after match SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */


?>


<div class="sp_block">
	<div class="sp_block_title">
		<h2>Составы команд</h2>
	</div>
	
		<?php echo do_shortcode('[event_performance id="' . $event->ID . '" align="none"]'); ?>
	
</div>

