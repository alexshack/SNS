<?php
class Paris2024Cron {

	public $step = [];
	public $updaterStages = [
		'medals',
		//'hockey',
		//'kyorling',
	];

	function __construct() {
		require_once THEME_PATH . '/inc/core/updater/index.php';
		require_once THEME_PATH . '/inc/paris2024/updater/index.php';
	}

	function update() {

		$this->step = get_option( 'paris2024_updater_step' );

		$update = true;
		if ( isset( $this->step['success'] ) ) {
			if ( (strtotime($this->step['success']) + 1800) > current_time('timestamp') ) { //каждые полчаса
				$update = false;
			}
		}

		if ( $update ) {

			if ( empty( $this->step['stage'] ) ) {
				$this->step['stage'] = 'medals';
			}

			$updaterStep = $this->getStepRequest( new Paris2024Updater() );

			$updaterStep['time']  = current_time( 'mysql' );
			$updaterStep['stage'] = $this->step['stage'];

			if ( empty( $updaterStep['total'] ) ) { //закончили

				if ( $updaterStep['stage'] == 'kyorling' ) {
					$updaterStep = [ 'success' => current_time( 'mysql' ) ];
				} else {
					$updaterStep['stage'] = $this->getNextStageId( $updaterStep['stage'] );
				}
			}

			update_option( 'paris2024_updater_step', $updaterStep );

		}

	}

	function getNextStageId( $current ) {

		foreach ( $this->updaterStages as $k => $stage ) {
			if ( $current == $stage ) {
				return $this->updaterStages[ $k + 1 ];
			}
		}

		return 0;

	}

	function getStepRequest( $Updater ) {

		$stage = $Updater->stage( $this->step['stage'], [
			'page'     => ! empty( $this->step['page'] ) ? $this->step['page'] : 0,
			'total'    => ! empty( $this->step['total'] ) ? $this->step['total'] : 0,
			'progress' => ! empty( $this->step['progress'] ) ? $this->step['progress'] : 0,
			'counter'  => ! empty( $this->step['counter'] ) ? $this->step['counter'] : 0
		] );

		return $stage->request();

	}

}
