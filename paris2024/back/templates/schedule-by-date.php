<?php $sportEvents = $controller->getEventsOnDateMain( $date, $sport_id, $type ); ?>

<?php if(empty($sportEvents)): ?>
    <p class="olympics__events-empty">К сожалению, ничего не было найдено. Попробуйте изменить парамеры поиска.</p>
<?php else: ?>
	<?php foreach ( $sportEvents as $event ): ?>
        <div class="olympics_calendar_event olympics_calendar_event-<?php echo sanitize_title( $event->event_type ); ?>" id="event-<?php echo sanitize_title( $event->event_id ); ?>">
            <div class="olympics_calendar_event-wrapper">
                <div class="olympics_calendar_sport olympics_calendar_col-2">
                    <?php echo $event->sport_id ? $controller->getSport( $event->sport_id )->sport_name : ''; ?>
                </div>
                <div class="olympics_calendar_date olympics_calendar_col-1">
                    <?php echo mysql2date( 'd.m.Y H:i', $event->event_time ); ?>
                </div>
                <div class="olympics_calendar_content olympics_calendar_col-3">
                    <h3 class="olympics_calendar_content-title">
                        <?php if ( $event->event_type == 'third' || $event->event_type == 'final' ) : ?>
                            <svg class="olympics_calendar_medal">
                                <use xlink:href="<?php echo SNS_URL;  ?>/img/olympics/<?php echo $event->event_type; ?>.svg#<?php echo $event->event_type; ?>"></use>
                            </svg>                        
                        <?php endif; ?>
                        <span>
                            <?php echo $event->event_name; ?>
                        </span>                
                    </h3>

                    <?php if ( $event->custom ): ?>
                        <div class="olympics_calendar_pairs">
                        <?php foreach ( maybe_unserialize($event->custom) as $countryPair ): ?>
                            <?php if ( $countryPair[0]['name'] && $countryPair[1]['name'] ): ?>
                                <div class="olympics_calendar_pair">
                                    <div class="olympics_calendar_country">
                                        <?php
                                        if ( $controller->hasCountryFlag( $countryPair[0]['id'] ) ): ?>
                                            <img class="olympics_calendar_country_image"
                                                 src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                 data-src="<?php echo SNS_URL;  ?>/img/country/<?php print $controller->getCountryFlag( $countryPair[0]['id'] ); ?>.svg"
                                                 width="30"
                                                 height="20"
                                                 alt="<?php $countryPair[0]['name']; ?>">
                                        <?php endif; ?>
                                        <span class="olympics_calendar_country_name"><?php print $countryPair[0]['name']; ?></span>
                                    </div>
                                    <div class="olympics_calendar_country">
                                        <?php if ( $controller->hasCountryFlag( $countryPair[1]['id'] ) ): ?>
                                            <img class="olympics_calendar_country_image"
                                                 src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                 data-src="<?php echo SNS_URL;  ?>/img/country/<?php print $controller->getCountryFlag( $countryPair[1]['id'] ); ?>.svg"
                                                 width="30"
                                                 height="20"
                                                 alt="<?php $countryPair[1]['name']; ?>">
                                        <?php endif; ?>
                                        <span class="olympics_calendar_country_name"><?php print $countryPair[1]['name']; ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
	<?php endforeach; ?>
<?php endif; ?>

