<?php

final class SP_SNS_AJAX_REST {

	public $name;
	public $nopriv = false;
	public $rest = false;
	public $rest_space = 'sp_sns';
	public $rest_route = '';
	public $rest_callback = '';
	public $ajax_callbacks = array();

	public static function getInstance() {
		static $instance;

		if ( null === $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	private function __construct() {
		static $hasInstance = false;

		if ( $hasInstance ) {
			return;
		}

		$hasInstance = true;
	}

	public function is_rest_request() {
		return isset( $_REQUEST['rest_route'] ) && $_REQUEST['rest_route'] == '/' . $this->rest_space . '/' . $this->rest_route . '/' ? true : false;
	}

	public function init_ajax_callback( $callback, $guest_access = false ) {

		if ( ! $this->is_rest_request() ) {
			return false;
		}

		$this->ajax_callbacks[ $callback ] = [ 'guest' => $guest_access ];
	}

	public function get_ajax_callback( $callback ) {
		return isset( $this->ajax_callbacks[ $callback ] ) ? $this->ajax_callbacks[ $callback ] : false;
	}

	function init_rest( $rest_callback ) {

		$this->rest_callback = $rest_callback;
		$this->rest_route    = $rest_callback;

		add_action( 'rest_api_init', array( $this, 'register_route' ) );
	}

	function register_route() {

		register_rest_route( $this->rest_space, '/' . $this->rest_route . '/', array(
			'methods'  => 'POST',
			'callback' => $this->rest_callback,
			'permission_callback' => '__return_true'
		) );
	}

	function verify() {

		if ( isset( $_POST['ajax_nonce'] ) ) {
			if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
				return false;
			}
			if ( ! wp_verify_nonce( $_POST['ajax_nonce'], 'wp_rest' ) ) {
				wp_send_json( array( 'error' => __( 'Signature verification failed' ) . '!' ) );
			}
		} else {
			check_ajax_referer( 'wp_rest' );
		}
	}

}
