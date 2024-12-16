<?php if(count($bonuses)) : ?>
<div class="bookmaker-bonuses bookmaker-bonuses--bookmaker">
	<?php foreach ($bonuses as $bonus) :
		include get_template_directory() . '/templates/loop-bonuses-bookmaker.php';
	endforeach; ?>
</div>
<?php endif; ?>
