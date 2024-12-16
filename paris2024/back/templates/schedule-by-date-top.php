<?php $sportEvents = $controller->getEventsOnDateTop( $date, $sport_id ); ?>

<div class="olympics_filter-events_wrapper">
    <?php if(empty($sportEvents)): ?>
        <p class="olympics__events-empty">К сожалению, ничего не было найдено. Попробуйте изменить парамеры поиска.</p>
    <?php else: ?>
        <?php if ($date) : ?>
            <?php if ($sport_id) : ?>
        	    <?php foreach ( $sportEvents as $sport_id => $times ) {
                    echo $controller->getEventTopSportContent($times, $sport_id);
                } ?>
            <?php else : ?>
                <?php
                    //echo '<pre>';
                    //print_r($sportEvents);
                    //echo '</pre>';
                ?>
                <?php  echo $controller->getEventTopDateContent($sportEvents, $sport_id); ?>
            <?php endif; ?>
        <?php else: ?>
            <?php foreach ( $sportEvents as $sport_id => $times ) {
                echo $controller->getEventTopSportContent($times, $sport_id);
            } ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

