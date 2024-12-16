<?php
/**
 * Template part for team table page SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

$tables = $team->getTables( $season_id );

$content_table = apply_filters( 'the_content', get_post_meta( $team_id, 'content_table', true ) );

?>


<?php if ($tables) { 
	foreach ($tables as $table) { 
		$table_args = array(
			'id'         => $table->ID,
			'show_title' => true,
			'tab'        => $tab,
			'highlight'  => $team_id
		);	
		sp_get_template( 'league-table.php', $table_args, SP()->template_path() . 'league/'  ); 
	}
}
?>

<?php if ( !empty( $content_table ) ) : ?>
	<div class="sp_block">
	   <?php echo $content_table ?>
	</div>
<?php endif; ?>

<?php echo do_shortcode( '[bonuses-slider bonus_type="best" title="Лучшие бонусы для ставок на ' . $team->post->post_title . '" type_link="vse-bonusy-bukmekerov" type_text="все бонусы"]' ); ?>