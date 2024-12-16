<?php

add_shortcode( 'olimp-hockey-tables', function ($atts = []) {

	$atts = wp_parse_args( $atts, [
		'by_name'    => false
	] );

	$controller = Paris2024ScheduleController::get();

	return $controller->getSportTablesContent( get_term_by( 'name', 'Хоккей', 'sport-type' )->term_id, $atts['by_name'] );
} );

add_shortcode( 'olimp-kyorling-tables', function ($atts = []) {

	$atts = wp_parse_args( $atts, [
		'id'    => false
	] );

	$controller = Paris2024ScheduleController::get();

	return $controller->getSportTablesContent( get_term_by( 'name', 'Кёрлинг', 'sport-type' )->term_id, $atts['by_name'] );
} );

add_shortcode( 'olimp-calendar', function ( $atts = [] ) {

	$atts = wp_parse_args( $atts, [
		'title'    => 'Календарь',
		'date'     => false,
		'sport_id' => false
	] );

    if(empty($atts['date'])){

	    $date = new DateTime("now", new DateTimeZone('Europe/Moscow') );
	    $atts['date'] = $date->format('Y-m-d');

        if(strtotime($atts['date']) < strtotime('2022-02-02')){
	        $atts['date'] = '2022-02-02';
        }

    }

	if($atts['date'] === 'all') {
		$atts['date'] = false;
	}

	ob_start();

	?>

    <div class="olympics__block olympics__block--no-padding">
        <div class="olympics__block-title"><?php print $atts['title']; ?></div>
        <div class="olympics__block-content">
			<?php print Paris2024ScheduleController::get()->getScheduleByDateContent( $atts['date'], $atts['sport_id'] ); ?>
        </div>
    </div>

	<?php

	return ob_get_clean();
} );

add_shortcode( 'olimp-medals', function ( $atts = [] ) {

	$atts = wp_parse_args( $atts, [
		'title' => 'Медальный зачет',
		'all'   => false
	] );

	$controller = Paris2024ScheduleController::get();

	$is_top = ! isset( $atts['all'] ) || ! $atts['all'];

	ob_start();

	?>

    <div class="olympics__block olympics__block--no-padding olympics__medals">
        <div class="olympics__block-title"><?php print $atts['title']; ?></div>
        <div class="olympics__block-content">
			<?php print $controller->getMedalsContent( $is_top ); ?>
        </div>
		<?php if ( $is_top ): ?>
            <div class="olympics__block-footer olympics__medals-footer">
                <button class="olympics__block-button" onclick="Paris2024.Medals.loadMore();">Показать все</button>
            </div>
		<?php endif; ?>
    </div>

	<?php

	return ob_get_clean();
} );
