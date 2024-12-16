<?php $sportEvents = $controller->getEventsBySportID( $sport_id ); ?>
<table>
    <tr>
        <th>Дата</th>
        <th>Время по Пекину</th>
        <th>Соревнование</th>
        <th></th>
    </tr>
	<?php foreach ( $sportEvents as $date => $times ) {
		foreach ( $times as $time => $events ) {
			foreach ( $events as $event ) { ?>
                <tr>
                    <td><?php echo mysql2date( 'M l j', $event->event_time ); ?></td>
                    <td><?php echo mysql2date( 'H:i', $event->event_time ); ?></td>
                    <td><?php echo $event->event_name; ?></td>
                    <td>
						<?php if ( $event->event_type == 'final' ): ?>
                            Ф
						<?php endif; ?>
                    </td>
                </tr>
			<?php } ?>
		<?php } ?>
	<?php } ?>
</table>
