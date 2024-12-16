<?php
/**
 * SNS Event importer - import events from Sports API into SportsPress.
 *
 * @author      Alex Torbeev
 * @category    Admin
 * @package     SportsPress_SNS
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class SP_Loader_Transfer extends SP_Loader {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->import_label = esc_attr__( 'Загрузка трансферов из Sports API', 'sportspress' );
	}


	function import() {
		set_time_limit(0);

		$leagues_get = empty( $_GET['leagues'] ) ? false : urldecode($_GET['leagues']);
		$seasons_get = empty( $_GET['seasons'] ) ? false : urldecode($_GET['seasons']);
		$date_from = empty( $_GET['date_from'] ) ? false : urldecode($_GET['date_from']);
		$date_to = empty( $_GET['date_to'] ) ? false : urldecode($_GET['date_to']);

		if (! $leagues_get || ! $seasons_get ) {
			return false;
		}
		$leagues_id = explode(',', $leagues_get);
		$seasons_id = explode(',', $seasons_get);

		foreach ($leagues_id as $league_id) {

			$league = new SP_SNS_League( $league_id );

			if (! $league->api_id || ! $league->has_transfers ) {
				continue;
			}

			foreach ( $seasons_id as $season_id ) {

				$seasons = $league->getSeasonSeasons( $season_id );

				foreach ( $seasons as $season ) {

					if (! $season->api_id ) {
						continue;						
					}

					$date_start = strtotime( $season->api_id . '-07-01' );
					$date_end   = strtotime( $season->api_id + 1 . '-06-30' );

					if ( $date_from ) {
						$date_start = $date_from;
					}

					if ( $date_to ) {
						$date_end = $date_to;
					}

					if ( $league->sport_type == 'football' ) {
						$data = SP_Loader_Transfer_Footbal::import( $season->ID, $league->ID, $date_start, $date_end );
					}

				} //end league seasons

			} //end seasons


		} //end leagues

		if ($data) {
			echo '<div class="updated settings-error below-h2"><p>
				' . wp_kses_post( sprintf( __( 'Импорт завершен - создано <strong>%1$s</strong> трансферов, обновлено <strong>%2$s</strong> и пропущено <strong>%3$s</strong>.', 'sportspress' ), esc_html( $data['imported'] ), esc_html( $data['updated'] ) , esc_html( $data['skipped'] ) ) ) . '
			</p></div>';
			$this->import_end();
		}

		
	}

	/**
	 * Performs post-import cleanup of files and the cache
	 */
	function import_end() {
		echo '<p>' . esc_html__( 'Все готово!', 'sportspress' ) . ' <a href="' . esc_url( admin_url( 'edit.php?post_type=sp_transfer' ) ) . '">' . esc_html__( 'Перейти к трансферам', 'sportspress' ) . '</a>' . '</p>';

		do_action( 'import_end' );
	}


	function greet() {

		if ( taxonomy_exists( 'sp_league' ) ) :
			$leagues = get_terms( [
				'taxonomy' => 'sp_league',
				'hide_empty' => false,
			] );
		endif;

		if ( taxonomy_exists( 'sp_season' ) ) :
			$seasons = get_terms( [
				'taxonomy'   => 'sp_season',
				'hide_empty' => false,
				'parent'     => 0
			] );
		endif;

		?>		
		<div class="loaders_row">

			<?php if ( $seasons ) { 
				echo '<div class="loaders_col">';
				echo '<h2>Год трансфера</h2>';
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
			<div class="loaders_col">
				<ul>
					<li>
						<label class="selectit">С</label>
						<input type="date" class="date_from loaders-check" id="date_from">
					</li>
					<li>
						<label class="selectit">По</label>
						<input type="date" class="date_to loaders-check" id="date_to">
					</li>
				</ul>				
			</div>

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

