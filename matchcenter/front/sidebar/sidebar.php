<?php
/**
 * Template part for sidebar SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */
?>
<?php include('main.php'); ?>

<?php Template::render('templates/sidebar/lenta/lenta', ['posts_count' => 40]); ?>