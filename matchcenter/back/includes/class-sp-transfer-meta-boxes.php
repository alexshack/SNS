<?php
/**
 * Transfer Meta Boxes
 *
 * @author 		Alex Torbeev
 * @category 	Admin
 * @package 	SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SP_Transfer_Meta_Boxes
 */
class SP_Transfer_Meta_Boxes {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'sportspress_process_sp_transfer_meta', array( $this, 'save' ) );
	}

	/**
	 * Add Meta boxes
	 */
	public function add_meta_boxes() {
		add_meta_box( 'sp_detailsdiv', __( 'Details', 'sportspress' ), array( $this, 'details' ), 'sp_transfer', 'side', 'default' );
	}

	/**
	 * Remove default meta boxes
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'sp_leaguediv', 'sp_transfer', 'side' );
		remove_meta_box( 'sp_seasondiv', 'sp_transfer', 'side' );		
		remove_meta_box( 'postimagediv', 'sp_transfer', 'side' );
		remove_meta_box( 'tagsdiv-sp_transfer_type', 'sp_transfer', 'side' );
	}


	/**
	 * Output the details metabox
	 */
	public static function details( $post ) {
		wp_nonce_field( 'sportspress_save_data', 'sportspress_meta_nonce' );

		?>
		<?php 
			if ( taxonomy_exists( 'sp_transfer_type' ) ) :
				$types = get_the_terms( $post->ID, 'sp_transfer_type' );
				if($types) {
					$type  = array_shift( $types );
				}
			endif;	

			$team_in  = get_post_meta( $post->ID, 'sp_team_in', true );
			$team_out = get_post_meta( $post->ID, 'sp_team_out', true );
			$player   = get_post_meta( $post->ID, 'sp_player', true );
			$summ     = get_post_meta( $post->ID, 'sp_summ', true );

			$post_type = 'sp_team';
			//print_r($type_ids);

		?>

		<?php if ( taxonomy_exists( 'sp_transfer_type' ) ) { ?>
			<p><strong><?php esc_attr_e( 'Тип трансфера', 'sportspress' ); ?></strong></p>
			<p>
			<?php
			$args = array(
				'taxonomy'    => 'sp_transfer_type',
				'name'        => 'sp_transfer_type[]',
				'selected'    => $type->term_id,
				'values'      => 'term_id',
				'placeholder' => sprintf( esc_attr__( 'Select %s', 'sportspress' ), esc_attr__( 'Positions', 'sportspress' ) ),
				'class'       => 'widefat',
				'chosen'      => true,
			);
			sp_dropdown_taxonomies( $args );
			?>
			</p>
		<?php } ?>

		<p><strong><?php esc_attr_e( 'Сумма трансфера', 'sportspress' ); ?></strong></p>
		<p><input type="text" size="10" id="sp_summ" name="sp_summ" value="<?php echo esc_attr( $summ ); ?>"></p>

		<div class="sp-instance">
			<p><strong><?php esc_attr_e( 'Игрок', 'sportspress' ); ?></strong></p>
			<p class="sp-tab-select sp-title-generator">
			<?php
			$args = array(
				'post_type'        => 'sp_player',
				'name'             => 'sp_player[]',
				'class'            => 'sportspress-pages',
				'show_option_none' => esc_attr__( '&mdash; None &mdash;', 'sportspress' ),
				'values'           => 'ID',
				'selected'         => $player,
				'chosen'           => true,
			);
			sp_dropdown_pages( $args );
			?>
			</p>
		</div>

		<div class="sp-instance">
			<p><strong><?php esc_attr_e( 'Куда пришел', 'sportspress' ); ?></strong></p>
			<p class="sp-tab-select sp-title-generator">
			<?php
			$args = array(
				'post_type'        => $post_type,
				'name'             => 'sp_team_in[]',
				'class'            => 'sportspress-pages',
				'show_option_none' => esc_attr__( '&mdash; None &mdash;', 'sportspress' ),
				'values'           => 'ID',
				'selected'         => $team_in,
				'chosen'           => true,
			);
			sp_dropdown_pages( $args );
			?>
			</p>
		</div>

		<div class="sp-instance">
			<p><strong><?php esc_attr_e( 'Откуда ушел', 'sportspress' ); ?></strong></p>
			<p class="sp-tab-select sp-title-generator">
			<?php
			$args = array(
				'post_type'        => $post_type,
				'name'             => 'sp_team_out[]',
				'class'            => 'sportspress-pages',
				'show_option_none' => esc_attr__( '&mdash; None &mdash;', 'sportspress' ),
				'values'           => 'ID',
				'selected'         => $team_out,
				'chosen'           => true,
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
	public static function save( $post_id ) {
		wp_set_object_terms( $post_id, sp_array_value( $_POST, 'sp_transfer_type', array(), 'int' ), 'sp_transfer_type', false );

		update_post_meta( $post_id, 'sp_summ', sp_array_value( $_POST, 'sp_summ', '', 'text' ) );

		sp_update_post_meta_recursive( $post_id, 'sp_team_in', sp_array_value( $_POST, 'sp_team_in', array(), 'int' ) );
		sp_update_post_meta_recursive( $post_id, 'sp_team_out', sp_array_value( $_POST, 'sp_team_out', array(), 'int' ) );
		sp_update_post_meta_recursive( $post_id, 'sp_player', sp_array_value( $_POST, 'sp_player', array(), 'int' ) );
	}


}

new SP_Transfer_Meta_Boxes();