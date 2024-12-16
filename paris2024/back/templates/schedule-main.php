<?php 
	Enqueue::footer('/olympics/olympics-schedule.css');
	$dateTimes = $controller->getDateTimes(); 
	$sports = $controller->getSports();
	$pekin_options = get_option( 'paris2024_settings' );	
	
	if ( isset( $pekin_options['page_schedule'] ) ) {
		$schedule_page = $pekin_options['page_schedule'];
	} else {
		$schedule_page = false;
	}
?>
<div class="olympics_table olympics_block_inside more-wrapper olympics-desktop">
	<table class="olympics_schedule">
		<thead>
		    <tr>
		        <th class="olympics_schedule-month">Август</th>
				<?php foreach ( $dateTimes as $date => $times ) { ?>
		            <th>
		            	<span><?php echo mysql2date( 'j', $date . ' 00:00:00' ); ?></span> 
		            	<?php echo mysql2date( 'D', $date . ' 00:00:00' ); ?>
		            </th>
				<?php } ?>
				<th></th>
		    </tr>
		</thead>
		<tbody>
			<?php
			$rows = 0;
			foreach ( $sports as $sport ) { 
				$rows++;
			?>
		        <tr class="olympics_schedule-row <?php echo $rows > 10 ? 'more-hidden' : ''; ?>">
		        	<?php

		        		$sportEvents = $controller->getEventsBySportID( $sport->sport_id ); 
		        		$sportFinals = $controller->getFinalsBySportID( $sport->sport_id );
		        		$countFinals = 0;
		        	?>
		            <td class="olympics_schedule-sport"><h3><?php echo $sport->sport_name; ?></h3></td>
					<?php foreach ( $dateTimes as $date => $times ) { ?>
						<td>
						<?php if ( ! empty( $sportEvents[ $date ] ) ) { ?>
							<?php if ( $controller->isFinalOnTheDate( $date, $sport->sport_id ) ): ?>
		                        <div class="olympics_schedule-event olympics_schedule-final">
		                        	<?php echo count($sportFinals[ $date ]); ?>
		                        	<?php $countFinals = $countFinals + count($sportFinals[ $date ]); ?>
							<?php else: ?>
		                        <div class="olympics_schedule-event">
							<?php endif; ?>		                        	
	                        	<div class="olympics_schedule-details">
	                        		<?php foreach ($sportEvents[ $date ] as $time => $events) : ?>
	                        			<?php foreach ($events as $event) : ?>
		                        			<div class="olympics_schedule-detail">
		                        				<div class="olympics_schedule-detail_time"><?php echo mysql2date( 'H:i', $event->event_time ); ?></div>
		                        				<?php if ( $schedule_page ) : ?>
		                        					<a href="<?php echo $schedule_page . '#event-' . $event->event_id; ?>" class="olympics_schedule-detail_title"><?php echo $event->event_name; ?></a>
		                        				<?php else : ?>
		                        					<div class="olympics_schedule-detail_title"><?php echo $event->event_name; ?></div>
		                        				<?php endif; ?>
		                        				<div class="olympics_schedule-detail_medal">
		                        					<?php if ( $event->event_type != 'compete' ) : ?>
		                        						<svg class="olympics_schedule-detail_medal-icon">
		                        							<use xlink:href="<?php echo SNS_URL;  ?>/img/olympics/<?php echo $event->event_type; ?>.svg#<?php echo $event->event_type; ?>"></use>
		                        						</svg>
		                        					<?php else: ?>
		                        						&nbsp;
		                        					<?php endif; ?>
		                        				</div>
		                        			</div>
		                        		<?php endforeach; ?>
	                        		<?php endforeach; ?>
	                        	</div>
	                      	</div>

						<?php } ?>
						</td>
					<?php } ?>
					<td><?php echo $countFinals; ?></td>
		        </tr>
			<?php } ?>
		</tbody>
	</table>

	<?php if($rows > 10) : ?>
	<div class="olympics_schedule-more">
		<button class="olympics_schedule-more_button more-btn">Показать еще</button>
	</div>
	<?php endif; ?>

	<div class="olympics_schedule-legend">
		<div class="olympics_schedule-legend_item">
			<div class="olympics_schedule-event">&nbsp;</div>
			<span>Предварительные</span>
		</div>
		<div class="olympics_schedule-legend_item">
			<div class="olympics_schedule-event olympics_schedule-final">&nbsp;</div>
			<span>Финалы</span>
		</div>		
	</div>

</div>

<?php $closest = $controller->findClosestDay( $dateTimes ); ?>
<div class="tab-wrapper olympics_block_inside olympics-mobile">
	<div class="olympics_table olympics_table-mobile">
		<table class="olympics_schedule">
			<thead>
			    <tr>
					<?php foreach ( $dateTimes as $date => $times ) : ?>
			            <th>
			            	<div class="olympics_schedule-date tab-btn <?php echo ($date == $closest) ? 'active' : ''; ?>" 
			            		data-tab="tab-<?php echo mysql2date( 'dmY', $date . ' 00:00:00' ); ?>">
			            		<span><?php echo mysql2date( 'j', $date . ' 00:00:00' ); ?></span> 
			            		<?php echo mysql2date( 'D', $date . ' 00:00:00' ); ?>
			            	</div>
			            </th>
					<?php endforeach; ?>
					<th></th>
			    </tr>
			</thead>
		</table>
	</div>
	<?php foreach ( $dateTimes as $date => $times ) : ?>
		<div class="more-wrapper olympics_schedule-sports tab-content <?php echo ($date == $closest) ? 'open' : ''; ?>" id="tab-<?php echo mysql2date( 'dmY', $date . ' 00:00:00' ); ?>">
			<?php $rows = 0; ?>
			<?php foreach ( $sports as $sport ) : ?>
				<?php 
					$sportEvents = $controller->getEventsBySportID( $sport->sport_id );
					if ( ! empty( $sportEvents[ $date ] ) ) : ?>
						<div class="olympics_schedule-sports-sport <?php echo $rows > 5 ? 'more-hidden' : ''; ?>">
							<h3><?php echo $sport->sport_name; ?></h3>
							<?php foreach ($sportEvents[ $date ] as $time => $events) : ?>
	                        	<?php foreach ($events as $event) : ?>
                        			<div class="olympics_schedule-sports-detail">
                        				<div class="olympics_schedule-sports-detail_time"><?php echo mysql2date( 'H:i', $event->event_time ); ?></div>
                        				<div class="olympics_schedule-sports-detail_title"><?php echo $event->event_name; ?></div>
                        				<div class="olympics_schedule-sports-detail_medal">
                        					<?php if ( $event->event_type == 'final' ) : ?>
                        						<svg class="olympics_schedule-sports-detail_medal-icon">
                        							<use xlink:href="<?php echo SNS_URL;  ?>/img/olympics/final.svg#final"></use>
                        						</svg>
                        					<?php else: ?>
                        						&nbsp;
                        					<?php endif; ?>
                        				</div>
                        			</div>
                        			<?php $rows++; ?>
	                        	<?php endforeach; ?>
	                        <?php endforeach; ?>
						</div>
					<?php endif; ?>
			<?php endforeach; ?>
			<?php if($rows > 5) : ?>
			<div class="olympics_schedule-more">
				<button class="olympics_schedule-more_button more-btn">Показать еще</button>
			</div>
			<?php endif; ?>				
		</div>
	<?php endforeach; ?>

</div>	

<?php 

 echo '<pre>';
// echo $closest;
// print_r($dateTimes);
//print_r($sports);
 echo '</pre>';


?>

