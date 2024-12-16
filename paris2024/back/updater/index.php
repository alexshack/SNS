<?php

require_once 'Paris2024EventsUpdate.php';
require_once 'Paris2024MedalsUpdate.php';
require_once 'Paris2024HockeyTablesUpdate.php';
require_once 'Paris2024KyorlingTablesUpdate.php';

class Paris2024Updater extends Updater {
	function __construct() {
		$stages = [
			'start'    => StartStage::class,
			'events'   => Paris2024EventsUpdate::class,
			'medals'   => Paris2024MedalsUpdate::class,
			'finish'   => FinishStage::class
		];
		if(Paris2024Parser::getStatus(859) === 'off') {

		}
		$this->stages = $stages;
	}
}

add_action( 'admin_menu', 'init_paris2024_updater_admin_menu', 100 );
function init_paris2024_updater_admin_menu() {
	add_submenu_page( 'paris2024-settings', __( 'Парсер' ), __( 'Парсер' ), 'manage_options', 'paris2024-parser', function () {
		the_admin_updater_page( __( 'Парсер Париж 2024' ), 'Paris2024Updater');
	} );
}
