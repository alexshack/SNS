<?php

$dates        = $controller->getDateTimes();

$current_date = $controller->findClosestDay( $dates );

?>
<div class="olympics_filter-wrapper" id="pekin-schedule">
    <div class="olympics_filter-fields pekin-schedule__filter">
        <div class="olympics_filter-field">
            <select onchange="Paris2024.scheduleFilter('sport', this);"
                    class="olympics_filter-input olympics_filter-input--sport pekin-schedule__filter_sport">
                <option value="">Весь спорт</option>
                <?php foreach ( $controller->getSports() as $sport ) { ?>
                    <option value="<?php echo $sport->sport_id; ?>"><?php echo $sport->sport_name; ?></option>
                <?php } ?>
            </select>
        </div>        
        <div class="olympics_filter-field">
            <select onchange="Paris2024.scheduleFilter('date', this);"
                    class="olympics_filter-input olympics_filter-input--date pekin-schedule__filter_date">
                <option value="">Дата</option>
				<?php foreach ( $dates as $date => $times ) { ?>
                    <option value="<?php echo $date; ?>" <?php print ( $date == $current_date ) ? 'selected="selected"' : ''; ?>>
						<?php echo mysql2date( 'j F', $date . ' 00:00:00' ); ?>
                    </option>
				<?php } ?>
            </select>
        </div>
    </div>
    <div class="olympics_filter-events olympics_overflow pekin-schedule__content">
		<?php echo $controller->getScheduleByDateContentTop($current_date); ?>
    </div>
</div>
