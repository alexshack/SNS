
<div class="olympics_filter-events_items olympics__event">

	<?php foreach ( $times as $time => $events ): ?>
		<?php foreach ( $events as $event ): ?>
            <?php if ( $event->custom ): ?>
                <?php foreach ( maybe_unserialize($event->custom) as $countryPair ): ?>
                    <div class="olympics__event-item <?php print sanitize_title( $event->event_type ); ?>">
                        <div class="olympics__event-item-time">
                            <span><?php echo $controller->getSport( $event->sport_id )->sport_name; ?> в </span>
                            <span><?php echo mysql2date( 'H:i', $event->event_time ); ?></span>                    
                            <?php if ($event->event_type != 'compete') : ?>
                                <svg class="olympics__event-item-icon">
                                    <use xlink:href="<?php echo SNS_URL;  ?>/img/olympics/<?php echo $event->event_type; ?>.svg#<?php echo $event->event_type; ?>"></use>
                                </svg>                        
                            <?php endif; ?>
                        </div>
                         <div class="olympics__event-item-title">
        					<?php echo $event->event_name; ?>
                        </div>

						<?php if ( $countryPair[0]['name'] && $countryPair[1]['name'] ): ?>
                            <div class="olympics__event-item-countries">
                                <div class="olympics__event-item-country olympics__event-item-country--first">
									<?php
                                    if ( $controller->hasCountryFlag( $countryPair[0]['id'] ) ): ?>
                                        <img class="olympics__event-item-country-image"
                                             src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                             data-src="<?php print get_template_directory_uri(); ?>/img/country/<?php print $controller->getCountryFlag( $countryPair[0]['id'] ); ?>.svg"
                                             width="30"
                                             height="20"
                                             alt="<?php $countryPair[0]['name']; ?>">
									<?php endif; ?>
                                    <span class="olympics__event-item-country-name"><?php print $countryPair[0]['name']; ?></span>
                                </div>
                                <div class="olympics__event-item-country olympics__event-item-country--last">
									<?php if ( $controller->hasCountryFlag( $countryPair[1]['id'] ) ): ?>
                                        <img class="olympics__event-item-country-image"
                                             src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                             data-src="<?php print get_template_directory_uri(); ?>/img/country/<?php print $controller->getCountryFlag( $countryPair[1]['id'] ); ?>.svg"
                                             width="30"
                                             height="20"
                                             alt="<?php $countryPair[1]['name']; ?>">
									<?php endif; ?>
                                    <span class="olympics__event-item-country-name"><?php print $countryPair[1]['name']; ?></span>
                                </div>
                            </div>
						<?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="olympics__event-item <?php print sanitize_title( $event->event_type ); ?>">
                    <div class="olympics__event-item-time">
                        <span><?php echo $event->sport_id ? $controller->getSport( $event->sport_id )->sport_name : ''; ?> в </span>
                        <span><?php echo mysql2date( 'H:i', $event->event_time ); ?></span>                    
                        <?php if ($event->event_type != 'compete') : ?>
                            <svg class="olympics__event-item-icon">
                                <use xlink:href="<?php echo SNS_URL;  ?>/img/olympics/<?php echo $event->event_type; ?>.svg#<?php echo $event->event_type; ?>"></use>
                            </svg>                        
                        <?php endif; ?>
                    </div>
                     <div class="olympics__event-item-title">
                        <?php echo $event->event_name; ?>
                    </div>
                </div>                
            <?php endif; ?>


		<?php endforeach; ?>
	<?php endforeach; ?>
</div>