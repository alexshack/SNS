<?php $canceledKeys = []; ?>
<h3><?php echo str_replace('Плэй-офф', 'Плeй-офф', $table->table_name); ?></h3>
<div class="kws__table">
    <table class="olimpics-table" id="table_<?php echo $table->tid; ?>">
        <tr>
			<?php foreach(maybe_unserialize($table->table_header) as $k => $head):
				if($sport->name == 'Кёрлинг' && $head == 'Местн'){
					$canceledKeys[] = $k;
					continue;
				}
				?>
                <th><?php echo $head; ?></th>
			<?php endforeach; ?>
        </tr>
		<?php foreach(maybe_unserialize($table->table_content) as $row): ?>
            <tr>
				<?php foreach($row as $c => $col):

					if($canceledKeys && in_array($c, $canceledKeys)){
						continue;
					}

					?>
                    <td<?php echo is_array($col)? '': ' class="align__center"' ?>>
						<?php if(is_array($col)): ?>
							<?php if ( $controller->hasCountryFlag( $col['id'] ) ): ?>
                                <img class="olympics__event-item-country-image"
                                     src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                     data-src="<?php print get_template_directory_uri(); ?>/img/country/<?php print $controller->getCountryFlag( $col['id'] ); ?>.svg"
                                     width="30"
                                     height="20"
                                     alt="<?php $col['name']; ?>">
							<?php endif; ?>
							<?php echo $col['name']; ?>
						<?php else: ?>
							<?php echo $col; ?>
						<?php endif; ?>
                    </td>
				<?php endforeach; ?>
            </tr>
		<?php endforeach; ?>
    </table>
</div>

