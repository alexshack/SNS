<?php
/**
 * Template part for league Table page SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

$tables = $league->getTables( $season_id );


$playoffs = $league->getPlayoffs( $season_id );

$content_table = get_term_meta( $league->ID, 'content_table', true );

/*echo '<pre>';
print_r($playoffs);
echo '</pre>';*/

?>




<?php if ( $tables ) {
	if ( count( $tables ) == 1 ) {
		$table_args = array(
			'id'         => $tables[0]->ID,
			'show_title' => true,
			'tab'        => $tab,
		);

		sp_get_template( 'league-table.php', $table_args, SP()->template_path() . 'league/'  );		
	} else {
		?>
		<div class="tab-wrapper sp_block">
			<h2>Групповой этап</h2>
			<div class="sp_block_title_btns small-buttons">
				<?php
				foreach ($tables as $key => $table) {
					if( $key == 0 ) {
						$status = ' active';
					} else {
						$status = '';
					}
					echo '<div class="sp_block_title_btn tab-btn' . $status . '" data-tab="tab-' . $table->ID . '">' . $table->post_title . '</div>';
				}
				?>
			</div>
			<?php
			foreach ($tables as $key => $table) { 
				$table_args = array(
					'id'         => $table->ID,
					'show_title' => false,
					'tab'        => $tab,
					'show_events' => true
				);
				if( $key == 0 ) {
					$status = ' open';
				} else {
					$status = '';
				}
				echo '<div class="tab-content' . $status . '" id="tab-' . $table->ID . '">';
				sp_get_template( 'league-table.php', $table_args, SP()->template_path() . 'league/'  );
				echo '</div>';
			}
			?>
		</div>
		<?php
	}
}
?>

<?php if ( count( $playoffs ) ) : ?>
	<div class="sp_block">
		<h2>Плей-офф</h2>
		<?php foreach( $playoffs as $pstages ) : ?>
			<div class="tab-wrapper">
				<div class="sp_block_title_btns small-buttons">
					<?php foreach ($pstages as $key => $pstage) {
						if( $key == 0 ) {
							$status = ' active';
						} else {
							$status = '';
						}
						echo '<div class="sp_block_title_btn tab-btn ' . $status . '" data-tab="tab-' . $pstage->ID . '">' . $pstage->name . '</div>';
					} ?>			
				</div>
				<?php foreach ($pstages as $key => $pstage) { 
					$stage_args = array(
						'stage'   => $pstage->ID,
						'league'  => $league->ID,
						'season'  => $season_id,
						'sport'   => $sport->type
					);
					if( $key == 0 ) {
						$status = ' open';
					} else {
						$status = '';
					}
					echo '<div class="tab-content' . $status . '" id="tab-' . $pstage->ID . '">';
					sp_get_template( 'league-playoff.php', $stage_args, SP()->template_path() . 'league/'  );
					echo '</div>';
				} ?>
			</div>	
		<?php endforeach; ?>
	</div>
<?php endif; ?>


<?php if ( !empty( $content_table ) ) : ?>
  	<div class="sp_block">
		<?php echo apply_filters( 'the_content', $content_table ); ?>
  	</div> 
<?php endif; ?>

<?php echo do_shortcode( '[bonuses-slider bonus_type="best" title="Лучшие бонусы для ставок на ' . $league->name . '" type_link="vse-bonusy-bukmekerov" type_text="все бонусы"]' ); ?>