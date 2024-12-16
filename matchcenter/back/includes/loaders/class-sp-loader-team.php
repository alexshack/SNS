<?php
/**
 * SNS Event importer - import events from Sports API into SportsPress.
 *
 * @author      Alex Torbeev
 * @category    Admin
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class SP_Loader_Team extends SP_Loader {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->import_label = esc_attr__( 'Загрузка клубов из Sports API', 'sportspress' );
	}


	function import() {
		$this->imported = $this->skipped = $this->updated = 0;

		$errors = [];

		$leagues_get = empty( $_GET['leagues'] ) ? false : urldecode($_GET['leagues']);
		$seasons_get = empty( $_GET['seasons'] ) ? false : urldecode($_GET['seasons']);


		if (! $leagues_get || ! $seasons_get ) {
			//return false;
			$errors ++;
		}

		$leagues_id = explode(',', $leagues_get);
		$seasons_id = explode(',', $seasons_get);

		foreach ($leagues_id as $league_id) {

			$feed_league_id = get_term_meta( $league_id, 'sp_order', true );

			if (! $feed_league_id ) {
				continue;
				
			}

			foreach ($seasons_id as $season_id) {

				$season = get_term_by('id', $season_id, 'sp_season');

				$feed_season_id = $season->slug;

				if (! $feed_season_id ) {
					
					continue;
					
				}				

				$request = 'teams?league=' . $feed_league_id . '&season=' . $feed_season_id;

				$feeds = $this->getFeeds($request);

				$games = [];

				if ($feeds) {
	
					foreach ($feeds as $feed) {
						$d_team = $feed['team'];
						$teams[$d_team['id']] = $d_team;
						$teams[$d_team['id']]['venue'] = $feed['venue'];
					}

					foreach ($teams as $key => $team) {

						$venue = self::getTermByApiID('sp_venue', $team['venue']['id']);
						
						if (! $venue ) {
							$errors['addVenue'] = 1;
							$venue = self::addVenue($team['venue']);
						}

						if ( ($team_post = self::getPostByApiID('sp_team', $key)) ) {
							
							$post_id = $team_post->ID;

							if ( ! has_post_thumbnail($post_id) ) {
								$logo_id = media_sideload_image( $team['logo'], $post_id, $team['name'], 'id');
								set_post_thumbnail( $post_id, $logo_id );
							}
							
							wp_set_object_terms( $post_id, (int)$league_id, 'sp_league', true );
							wp_set_object_terms( $post_id, (int)$season_id, 'sp_season', true );
							wp_set_object_terms( $post_id, $venue->term_id, 'sp_venue', false );

							$this->updated++;

						} else {

							self::addTeam($team, $league_id, $season_id);

							$this->imported++;			
						}
					}

				} 

			} //end seasons

		} //end leagues


		// Show Result
		echo '<div class="updated settings-error below-h2"><p>
			' . wp_kses_post( sprintf( __( 'Import complete - imported <strong>%1$s</strong> events, updated <strong>%2$s</strong> and skipped <strong>%3$s</strong>.', 'sportspress' ), esc_html( $this->imported ), esc_html( $this->updated ) , esc_html( $this->skipped ) ) ) . '
		</p></div>';
		print_r($errors);

		$this->import_end();
	}

	/**
	 * Performs post-import cleanup of files and the cache
	 */
	function import_end() {
		echo '<p>' . esc_html__( 'All done!', 'sportspress' ) . ' <a href="' . esc_url( admin_url( 'edit.php?post_type=sp_event' ) ) . '">' . esc_html__( 'View Events', 'sportspress' ) . '</a>' . '</p>';

		do_action( 'import_end' );
	}


	function greet() {

		if ( taxonomy_exists( 'sp_league' ) ) :
			$leagues    = get_terms( [
				'taxonomy' => 'sp_league',
				'hide_empty' => false,
			] );
		endif;

		if ( taxonomy_exists( 'sp_season' ) ) :
			$seasons    = get_terms( [
				'taxonomy' => 'sp_season',
				'hide_empty' => false,
			] );
		endif;

		?>		
		<div class="loaders_row">
			<?php if ( $seasons ) { 
				echo '<div class="loaders_col">';
				echo '<h2>Сезоны</h2>';
				echo '<ul class=" ">';
				foreach ($seasons as $season) {
					echo '<li><label class="selectit"><input type="checkbox" value="' . $season->term_id . '" class="loaders-season loaders-check" checked>' . $season->name . '</label></li>';
				}
				echo '</ul>';
				echo '</div>';
			} ?>

			<?php if ( $leagues ) { 
				echo '<div class="loaders_col">';
				echo '<h2>Лиги</h2>';
				echo '<ul class="categorychecklist form-no-clear">';
				foreach ($leagues as $league) {
					echo '<li><label class="selectit"><input type="checkbox" value="' . $league->term_id . '" class="loaders-league loaders-check" checked>' . $league->name . '</label></li>';
				}
				echo '</ul>';
				echo '</div>';
			} ?>


		</div>	
		<a class="button button-primary button-large" id="loader-start" href="">Начать получение данных</a>
		
		<?php
	}

	/**
	 * options function.
	 *
	 * @access public
	 * @return void
	 */

}

