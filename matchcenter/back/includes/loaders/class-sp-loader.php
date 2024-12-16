<?php
/**
 * SNS SportsPress Loader
 *
 * @author      Alex Torbeev
 * @category    Admin
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

class SP_Loader {

	var $import_page;
	var $import_label;
	static $id_field = 'sns_apisport_id';

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ));
	}

	public function admin_styles($hook) {
			
	}

	public function admin_scripts($hook) {


	}


	function dispatch() {
		$this->header();

		$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];
		switch ( $step ) :
			case 0:
				$this->greet();
				break;
			case 1:
				$this->import();
				break;
		endswitch;

		$this->footer();
	}


	function header() {
		echo '<div class="wrap"><h2>' . esc_html( $this->import_label ) . '</h2>';
		$current_page = admin_url( "admin.php?page=" . $_GET["page"] );
		echo '<script>let page_loader = "' . $current_page . '"</script>';
	}


	function footer() {
		echo '</div>';
	}

}