<?php

require_once 'Paris2024Event.php';
require_once 'Paris2024Migrate.php';
require_once 'Paris2024Parser.php';
require_once 'Paris2024Sport.php';
require_once 'Paris2024Medals.php';
require_once 'Paris2024Table.php';
require_once 'Paris2024Quiz.php';
require_once 'Paris2024ScheduleController.php';
require_once 'Paris2024QuizController.php';
require_once 'Paris2024Cron.php';
require_once 'ajax.php';
require_once 'shortcodes.php';

if ( is_admin() ) {
	require_once 'updater/index.php';
	require_once 'admin/index.php';
	Paris2024Migrate::table_create();
}



add_filter( 'aioseop_robots_meta', function ( $robots ) {

	if ( is_page_template( 'olimpic-hockey.php' ) ) {
		$robots = 'noindex, nofollow';
	}

	return $robots;

}, 50, 2 );

//Enqueue::footer('olympics-sidebar.css');
