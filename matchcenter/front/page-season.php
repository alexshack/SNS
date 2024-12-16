<?php
/**
 * Template for Seasons page SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */
get_header();



?>

<div class="sp_event_header">
	<?php //include 'event/single/header.php'; ?>
</div>
<div class="wrapper ">
	<?php if ( have_posts() ) {
		while ( have_posts() ) : the_post(); ?>
            <div class="main sp_event_main">
	            <?php //include 'event/single/main.php'; ?>
            </div>
            <aside class="sidebar">
            	<?php //include 'sidebar/sidebar.php'; ?>
            </aside>
            <div class="duplicate-block duplicate-block_mobile">

            </div>
		<?php endwhile;
	} ?>
</div>
<?php get_footer(); ?>
