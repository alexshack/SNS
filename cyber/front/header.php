<?php 
	$header_items = Cyber::getMenu(); 
	$current_section = $current_section ?? '';
?>
<div class="cyber_header">
	<?php foreach ($header_items as $header_item) : ?>
		<?php if (get_the_permalink() == $header_item['link'] && empty(get_query_var('sport')) && empty(get_query_var('child')) && empty(get_query_var('tournament')) ) {
			$header_item_tag_open = '<div class="cyber_header-item active">';
			$header_item_tag_close = '</div>';
		} else {
			if ($current_section == $header_item['link'] ) {
				$header_item_tag_open = '<a class="cyber_header-item active" href="' . $header_item['link'] . '">';
				$header_item_tag_close = '</a>';				
			} else {
				$header_item_tag_open = '<a class="cyber_header-item" href="' . $header_item['link'] . '">';
				$header_item_tag_close = '</a>';
			}
		} ?>
		<?php echo $header_item_tag_open; ?>
        <svg class="cyber_header-icon"><use xlink:href="<?php echo SNS_URL; ?>/img/cyber/<?php echo $header_item['img']; ?>.svg#<?php echo $header_item['img']; ?>"></use></svg>
        <div class="cyber_header-title"><?php echo $header_item['name']; ?></div>
        <svg class="cyber_header-arrow"><use xlink:href="<?php echo SNS_URL; ?>/img/cyber/arrow.svg#arrow"></use></svg>
		<?php echo $header_item_tag_close; ?>
	<?php endforeach; ?>
</div>