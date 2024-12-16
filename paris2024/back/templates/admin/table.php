<?php $canceledKeys = []; ?>
<h3><?php echo str_replace('Плэй-офф', 'Плeй-офф', $table->table_name); ?></h3>
<table class="olimpics-table" id="table_<?php echo $table->tid; ?>">
	<tr>
		<?php foreach($table->table_header as $k => $head): ?>
            <?php if($sport->name == 'Кёрлинг' && $head == 'Местн'){
	            $canceledKeys[] = $k;
                continue;
            } ?>
			<th><input type="hidden" name="tables[<?php echo $table->tid ?>][head][]" value="<?php echo $head; ?>"><?php echo $head; ?></th>
		<?php endforeach; ?>
	</tr>
	<?php foreach($table->table_content as $r => $row): ?>
		<tr>
			<?php foreach($row as $c => $col): ?>
                <?php if($canceledKeys && in_array($c, $canceledKeys)){
                    continue;
                } ?>
                <td<?php echo is_array($col)? '': ' class="align__center"' ?>>
                <?php if(is_array($col)): ?>
	                <?php $col = $col['name']; ?>
                <?php endif; ?>
                    <input type="text" name="tables[<?php echo $table->tid ?>][body][<?php echo $r ?>][]" value="<?php echo $col; ?>">
                </td>
			<?php endforeach; ?>
		</tr>
	<?php endforeach; ?>
</table>
