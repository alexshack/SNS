<?php

rest_action('paris2024_load_video', true);
function paris2024_load_video(){

	$url  = strval( $_POST['url'] );

	wp_send_json( [
		'content' => wp_oembed_get($url, array('width' => 700) )
	] );

}

rest_action( 'paris2024_schedule_filter', true );
function paris2024_schedule_filter() {

	$sport = intval( $_POST['sport'] );
	$date  = strval( $_POST['date'] );

	//if ( $sport || $date ) {
	$content = Paris2024ScheduleController::get()->getScheduleByDateContentTop( $date, $sport );
	/*} else {
		$content = Paris2024ScheduleController::get()->getMainScheduleContent();
	}*/

	wp_send_json( [
		'content' => $content
	] );

}

rest_action( 'paris2024_schedule_main_filter', true );
function paris2024_schedule_main_filter() {

	$sport = intval( $_POST['sport'] );
	$date  = strval( $_POST['date'] );
	$type  = strval( $_POST['type'] );

	//if ( $sport || $date ) {
	$content = Paris2024ScheduleController::get()->getScheduleByDateContent( $date, $sport, $type );
	/*} else {
		$content = Paris2024ScheduleController::get()->getMainScheduleContent();
	}*/

	wp_send_json( [
		'content' => $content
	] );

}

rest_action( 'paris2024_load_medals', true );
function paris2024_load_medals() {

	$showAll = intval($_POST['all']);

	$controller = Paris2024ScheduleController::get();

	if($showAll){
		$offset = 4;
		$medals = array_slice( $controller->getMedals(), $offset );
	}else{
		$offset = 0;
		$medals = array_slice( $controller->getMedals(), $offset, 4 );
	}

	$content = '';

	foreach ( $medals as $k => $medal ) {
		$content .= $controller->getSingleMedalContent( $medal, $k + $offset + 1 );
	}

	wp_send_json( [
		'content' => $content
	] );
}

rest_action( 'paris2024_load_news', true );
function paris2024_load_news() {

	$controller = Paris2024ScheduleController::get();

	$page = intval( $_POST['page'] );

	$number = $controller->getNewsLimit();
	$offset = ( $page - 1 ) * $number;
	$pages  = ceil( $controller->getNewsTotal() / $number );

	$news = $controller->getNews( $offset );

	$content = '';
	foreach ( $news as $news_item ) {
		$content .= $controller::get()->getSingleNewsContent( $news_item );
	}

	wp_send_json( [
		'content' => $content,
		'pages'   => $pages
	] );
}

rest_action( 'paris2024_quiz_show', true );
function paris2024_quiz_show() {

	$controller = Paris2024QuizController::get();

	$content = $controller::get()->getPage( 0, false );

	wp_send_json( [
		'content' => $content,
	] );
}

rest_action( 'paris2024_quiz_page', true );
function paris2024_quiz_page() {

	$controller = Paris2024QuizController::get();

	$page    = intval( $_POST['page'] );
	$answers = strval( $_POST['answers'] );

	$content = $controller::get()->getPage( $page, $answers );

	wp_send_json( [
		'content' => $content,
	] );
}