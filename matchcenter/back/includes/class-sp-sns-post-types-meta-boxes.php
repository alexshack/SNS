<?php
/**
 * Post Meta Boxes
 *
 * @author 		Alex Torbeev
 * @category 	Admin
 * @package 	SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SP_SNS_Post_Types_Meta_Boxes
 */
class SP_SNS_Post_Types_Meta_Boxes {

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
		
		if ( taxonomy_exists( 'sp_league' ) ) :
			$leagues    = get_the_terms( $post->ID, 'sp_league' );
			$league_ids = array();
			if ( $leagues ) :
				foreach ( $leagues as $league ) :
					$league_ids[] = $league->term_id;
				endforeach;
			endif;
		endif;

		$teams = get_posts(
			array(
				'post_type'      => 'sp_team',
				'posts_per_page' => -1,
			)
		);

		$post_teams = array_filter( get_post_meta( $post->ID, 'sp_post_team', false ) );

		?>

		<?php if ( taxonomy_exists( 'sp_league' ) ) { ?>
		<p><strong><?php esc_attr_e( 'Leagues', 'sportspress' ); ?></strong></p>
		<p>
			<?php
			$args = array(
				'taxonomy'    => 'sp_league',
				'name'        => 'tax_input[sp_league][]',
				'selected'    => $league_ids,
				'values'      => 'term_id',
				'placeholder' => sprintf( esc_attr__( 'Select %s', 'sportspress' ), esc_attr__( 'Leagues', 'sportspress' ) ),
				'class'       => 'widefat',
				'property'    => 'multiple',
				'chosen'      => true,
			);
			sp_dropdown_taxonomies( $args );
			?>
		</p>
		<?php } ?>


		<div class="sp-instance">
			<p><strong><?php esc_attr_e( 'Команды', 'sportspress' ); ?></strong></p>
			<p class="sp-tab-select sp-title-generator">
			<?php
			$args = array(
				'post_type'   => 'sp_team',
				'name'        => 'sp_post_team[]',
				'selected'    => $post_teams,
				'values'      => 'ID',
				'placeholder' => sprintf( esc_attr__( 'Select %s', 'sportspress' ), esc_attr__( 'Teams', 'sportspress' ) ),
				'class'       => 'sp-post-teams widefat',
				'property'    => 'multiple',
				'chosen'      => true,
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

		sp_update_post_meta_recursive( $post_id, 'sp_post_team', sp_array_value( $_POST, 'sp_post_team', array(), 'int' ) );
		
	}


}

