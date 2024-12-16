<?php
$dates        = $controller->getDateTimes();
$current_date = array_key_first( $dates );

foreach ( $dates as $date => $times ) {
	if ( strtotime( date( 'Y-m-d' ) ) == strtotime( $date ) ) {
		$current_date = $date;
		break;
	}
}
?>

     <div class="olympics_calendar_filter">
        <div class="olympics_calendar_filter-field olympics_calendar_col-1">
            <select onchange="Paris2024.scheduleMainFilter('date', this);"
                    class="olympics_calendar_filter-input olympics_calendar_filter-input--date pekin-schedule__filter_date">
                <option value="">Дата</option>
				<?php foreach ( $dates as $date => $times ) { ?>
                    <option value="<?php echo $date; ?>" <?php selected( $current_date, $date ); ?>>
						<?php echo mysql2date( 'j F', $date . ' 00:00:00' ); ?>
                    </option>
				<?php } ?>
            </select>
        </div>
        <div class="olympics_calendar_filter-field olympics_calendar_col-2">
            <select onchange="Paris2024.scheduleMainFilter('sport', this);"
                    class="olympics_calendar_filter-input olympics_calendar_filter-input--sport pekin-schedule__filter_sport">
                <option value="">Вид спорта</option>
				<?php foreach ( $controller->getSports() as $sport ) { ?>
                    <option value="<?php echo $sport->sport_id; ?>"><?php echo $sport->sport_name; ?></option>
				<?php } ?>
            </select>
        </div>
        <div class="olympics_calendar_filter-field olympics_calendar_col-3">
            <select onchange="Paris2024.scheduleMainFilter('type', this);"
                    class="olympics_calendar_filter-input olympics_calendar_filter-input--type pekin-schedule__filter_sport">
                <option value="">Категория соревнования</option>
                <option value="compete">Квалификация</option>
                <option value="third">За 3 место</option>
                <option value="final">Финал</option>
            </select>
        </div>
    </div>
    <div class="olympics_calendar pekin_calendar-content" id="pekin-calendar">
		<?php echo $controller->getScheduleByDateContent($current_date); ?>
    </div>

