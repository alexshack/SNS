<?php

$bonuses_pages = [
    'Все' => home_url('/vse-bonusy-bukmekerov/'),
    'Фрибеты' => home_url('/bk-s-fribetom/'),
    'Бездепозитные' => home_url('/bk-s-bezdepozitnym-bonusom/'),
    'Промокоды' => home_url('/promokody-bk/'),
    'Кешбэк' => home_url('/bk-s-keshbekom/')
];

?>
<div class="side-widget <?php echo isset($class) ? $class : ''; ?>">
	<div class="side-widget__title">
		<?php echo isset($title) ? $title : 'Лучшие бонусы'; ?>
		<div class="side-widget__links">
            <?php $counter = 0;
            foreach ($bonuses_pages as $text => $url) : ?>
                <a class="side-widget__link <?php echo !$counter ? 'active' : '' ?>" href="<?php echo $url; ?>" title="<?php echo $text; ?>"><?php echo $text; ?></a>
            <?php $counter++;
            endforeach; ?>
		</div>
	</div>
    <div class="side-bonuses">
	    <?php foreach ($bonuses as $bonus) :
            Bonuses::template('loop-3.php', ['bonus' => $bonus]);
        endforeach; ?>
    </div>
</div>
