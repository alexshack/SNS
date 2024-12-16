<?php
/**
 * Predicts Meta Boxes
 *
 * @author 		Alex Torbeev
 * @category 	Admin
 * @package 	SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SP_SNS_Predicts_Meta_Boxes
 */
class SP_SNS_Predicts_Meta_Boxes {

	static $post_types;
	/**
	 * Constructor
	 */
	public function __construct($post_types) {
		self::$post_types = $post_types;
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'save_post', array( $this, 'save' ), 1, 2 );
	}

	/**
	 * Add Meta boxes
	 */
	public function add_meta_boxes() {
		add_meta_box( 'sp_detailsdiv', __( 'Матч-центр', 'sportspress' ), array( $this, 'details' ), self::$post_types, 'side', 'default' );
	}

	/**
	 * Remove default meta boxes
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'sp_leaguediv', self::$post_types, 'side' );
		remove_meta_box( 'sp_seasondiv', self::$post_types, 'side' );
		remove_meta_box( 'tagsdiv-tournament', self::$post_types, 'side' );
		remove_meta_box( 'tagsdiv-stage', self::$post_types, 'side' );
		remove_meta_box( 'placediv', self::$post_types, 'side' );
		remove_meta_box( 'tagsdiv-sport-type', self::$post_types, 'side' );
		remove_meta_box( 'tagsdiv-type-bet', self::$post_types, 'side' );		
		remove_meta_box( 'tags-meta-box', self::$post_types, 'side' );
	}

	/**
	 * Output the details metabox
	 */
	public static function details( $post ) {
		wp_nonce_field( 'sportspress_save_data', 'sportspress_meta_nonce' );
		
		$event     = get_post_meta( $post->ID, 'sp_event', true );


		?>


		<div class="sp-instance">
			<p><strong><?php esc_attr_e( 'Матч', 'sportspress' ); ?></strong></p>
			<p class="sp-tab-select sp-title-generator">
			<?php
			$args = array(
				'post_type'        => 'sp_event',
				'name'             => 'sp_event[]',
				'class'            => 'widefat',
				'show_option_none' => esc_attr__( '&mdash; None &mdash;', 'sportspress' ),
				'values'           => 'ID',
				'selected'         => $event,
				'chosen'           => true,
				'orderby' 		   => 'date',
				'order'            => 'DESC',
				'post_status'       => ['publish', 'future'],	
			);
			sp_dropdown_pages( $args );
			?>
			</p>
		</div>		

		<?php
	}


	/**
	 * Save meta boxes data
	 */
	public static function save( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( is_int( wp_is_post_revision( $post ) ) ) {
			return;
		}
		if ( is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}
		if ( empty( $_POST['sportspress_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['sportspress_meta_nonce'] ), 'sportspress_save_data' ) ) {
			return;
		}

        if( isset($_POST['action_performed']) ){
            //Prevent running the action twice
            return;
        }

		if (isset($_POST['sp_event'])) {

            $old_sp_event = get_post_meta( $post_id, 'sp_event', 1 );

            delete_post_meta($post_id, 'sp_team');

            sp_update_post_meta_recursive($post_id, 'sp_event', sp_array_value($_POST, 'sp_event', array(), 'int'));

            $event_id = $_POST['sp_event'][0];
            $leagues = get_the_terms($event_id, 'sp_league');
            if ($leagues) {
                $league = array_shift($leagues);
                wp_set_object_terms($post_id, $league->term_id, 'sp_league', false);
            }
            $teams = get_post_meta($event_id, 'sp_team');
            if ($teams) {
                $teams = array_unique($teams);
                foreach ($teams as $team) {
                    add_post_meta($post_id, 'sp_team', $team);
                }
            }

            if ( ( ! has_post_thumbnail($post_id) || $old_sp_event != $_POST['sp_event'][0] ) && count($_POST['sp_event']) && extension_loaded('imagick') ) {
                self::generateThumbnail($post_id, $_POST['sp_event'][0]);
            }

		} else {
			delete_post_meta( $post_id, 'sp_event' );
		}

        $_POST['action_performed'] = true;
		
	}

	public static function generateThumbnail($post_id, $event_id) {

		$league_bg_file = false;

		$leagues = get_the_terms( $event_id, 'sp_league' );

		if ( $leagues ) {
			$game_league   = array_shift( $leagues );

			$league_bg_id  = get_term_meta($game_league->term_id, 'league_bg_id', 1 );
			$league_bg_img = wp_get_attachment_image_url( $league_bg_id, 'full' );
			if ($league_bg_img) {
				$league_bg_file = file_get_contents($league_bg_img);
			
			} 

			$league_img_id = get_term_meta($game_league->term_id, '_thumbnail_id', 1 );
			$league_img    = wp_get_attachment_image_url( $league_img_id, 'full' );

			if( $league_img ) {
				$league_file = file_get_contents($league_img);
				$image3 = new Imagick();
				$image3->readImageBlob($league_file);
				$image3->resizeImage(44, 44, imagick::FILTER_LANCZOS, 1);
			}		
		}

		$teams = get_post_meta( $event_id, 'sp_team' );

		if($teams) {
			$teams  = array_unique($teams );
			$teams  = array_filter( $teams, 'sp_filter_positive' );	
			if ( get_option( 'sportspress_event_reverse_teams', 'no' ) === 'yes' ) {
				$teams   = array_reverse( $teams, true );
			}
			$logos = [];
			foreach($teams as $team) {
				if ( has_post_thumbnail( $team ) ) {
					$team_img  = get_the_post_thumbnail_url( $team, 'full' );
					$logos[] = file_get_contents($team_img);
				}
			}
		
			$image_team_1 = new Imagick();
			$image_team_1->readImageBlob($logos[0]);
			$image_team_1->resizeImage(110, 110, imagick::FILTER_LANCZOS, 1);

			$image_team_2 = new Imagick();
			$image_team_2->readImageBlob($logos[1]);
			$image_team_2->resizeImage(110, 110, imagick::FILTER_LANCZOS, 1);
					
		}


		$image = new Imagick();
		if($league_bg_file) {
			$image->readImageBlob($league_bg_file);				
		} else {
			$image->readImage(SP_SNS_DIR . '/img/predicts/543x300-football-field.png');
		}

		$image->resizeImage(320, 200, imagick::FILTER_LANCZOS, 1);

		$bg_width = $image->getImageWidth();
		$bg_height = $image->getImageHeight();

		
		if( ! $league_bg_file ) {
			$image_top = new Imagick();
			$image_top->readImage(SP_SNS_DIR . '/img/predicts/543x300-football-league.png');
			$image_top_left = $bg_width / 2 - 27;

			$image->compositeImage($image_top, Imagick::COMPOSITE_OVER, $image_top_left, 0);
		

			if(isset($image3)) {
				$image3_left = $bg_width / 2 - 22;
				$image->compositeImage($image3, Imagick::COMPOSITE_OVER, $image3_left, 5);
			}

		}

		if(isset($image_team_1)) {
			$image_team_1_left = $bg_width / 2 / 2 - 55;
			$image->compositeImage($image_team_1, Imagick::COMPOSITE_OVER, $image_team_1_left, 40);
		}

		if(isset($image_team_2)) {
			$image_team_2_left = $bg_width - ($bg_width / 2 / 2 ) - 55;
			$image->compositeImage($image_team_2, Imagick::COMPOSITE_OVER, $image_team_2_left, 40);		
		}

		
		if( ! $league_bg_file ) {
			$image_ball = new Imagick();
			$image_ball->readImage(SP_SNS_DIR . '/img/predicts/543x300-ball.png');

			$image->compositeImage($image_ball, Imagick::COMPOSITE_OVER, 0, 0);
		}
		
		$image->setImageFormat ("jpeg");
		$image->writeImage(SP_SNS_DIR . '/img/predicts/full.jpg');

		$image_name = get_post_field('post_name', $teams[0]) . '-' . get_post_field('post_name', $teams[1]) . '.jpg';

		$file_array = ["name" => $image_name, "tmp_name" => SP_SNS_DIR . '/img/predicts/full.jpg'];  
   		$attachment_id = media_handle_sideload($file_array , 0, '');
   		set_post_thumbnail( $post_id, $attachment_id );

   		$image->resizeImage(80, 50, imagick::FILTER_LANCZOS, 1);
   		$image->writeImage(SP_SNS_DIR . '/img/predicts/thumb.jpg');
   		$image_name = get_post_field('post_name', $teams[0]) . '-' . get_post_field('post_name', $teams[1]) . '-small.jpg';
   		$file_array = ["name" => $image_name, "tmp_name" => SP_SNS_DIR . '/img/predicts/thumb.jpg']; 
   		$attachment_id = media_handle_sideload($file_array , 0, '');
   		update_post_meta($post_id, 'kdmfi_small-image-predicts', $attachment_id);

   		

	}

}

