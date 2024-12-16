<?php 
	$snippet = get_post_meta(get_the_ID(), 'snippet_shortcode', true);
	if (!empty($snippet)) {
		echo '<div class="snippet-table table-wrapper bookmaker-description kws__table">';
		echo do_shortcode($snippet); 
		echo '</div>';
	}
?>
<nav class="category_menu">
	<?php
	global $wp_query;
	global $wp;

	$menu_name   = 'main-menu';
	$locations   = get_nav_menu_locations();
	$root_active = 0;

	$parent_id = 9211;

	$menus = [
		['type' => false, 'name' => 'Все', 'url' => get_the_permalink(9206)],
		['type' => 314, 'name' => 'Фрибеты', 'url' => home_url() . '/bk-s-fribetom/'],
		['type' => 312, 'name' => 'Бездепозитные', 'url' => home_url() . '/bk-s-bezdepozitnym-bonusom/'],
		['type' => 2114, 'name' => 'Промокоды', 'url' => home_url() . '/promokody-bk/'],
		['type' => 318, 'name' => 'Кэшбек', 'url' => home_url() . '/bk-s-keshbekom/'],
		['type' => 5163, 'name' => 'За установку приложения', 'url' => home_url() . '/fribet-za-ustanovku-prilozheniya/'],
	];
	foreach ($menus as $menu) {
		$bonus_count = '';
		if ($menu['type']) {
			$bonus_settings['bonus_type'] = $menu['type'];
			$bonus_settings['status'] = 'active';
			$bonus_filter = (new BonusesFilter())->where($bonus_settings);
			$bonus_count = '<span>' . $bonus_filter->getCount() . '</span>';
		}
		if (get_the_permalink() == $menu['url']) {
			echo '<div class="category_menu_item active">' . $menu['name'] . $bonus_count . '</div>';
		} else {
			echo '<a href="' . $menu['url'] . '" class="category_menu_item">' . $menu['name'] . $bonus_count . '</a>';
		}
	}

 	?>
</nav>


<?php if(is_page() && ! $is_filter) { 
	Bonuses::template('sliders.php', ['bonus_type' => 'best', 'title' => 'Лучшие бонусы', 'type_link' => '']);
	Bonuses::template('sliders.php', ['bonus_type' => '313', 'title' => 'Приветственные бонусы']);
	echo do_shortcode('[subscription-bonuses]');
	Bonuses::template('sliders.php', ['bonus_type' => '314', 'title' => 'Фрибеты', 'type_link' => 'bk-s-fribetom']);
	Bonuses::template('sliders.php', ['bonus_type' => '312','title' => 'Бездепозитные бонусы', 'type_link' => 'bk-s-bezdepozitnym-bonusom']);
	Bonuses::template('sliders.php', ['bonus_type' => '2114', 'title' => 'Промокоды', 'type_link' => 'promokody-bk']);
	Bonuses::template('sliders.php', ['bonus_type' => '318', 'title' => 'Кешбэк', 'type_link' => 'bk-s-keshbekom']);
	
} else { ?>

<div class="progress-b hidden"></div>
<div class="bookmaker-items-subheader margin">
    <div class="bookmaker-items-subheader__order">
        <div class="bookmaker-items-subheader__order-container">
            <button class="bookmaker-items-subheader__order-link">
                <span>По популярности</span>
            </button>
            <div class="bookmaker-items-subheader__order-data-wrapper bookmaker-items-subheader__order-data-wrapper--left">
                <div class="bookmaker-items-subheader__order-data">
                    <button data-action="order" data-order="popular" class="load-bonuses__action bookmaker-items-subheader__order-item sort-bonus-item bookmaker-items-subheader__order-item_active">По популярности</button>
                    <button data-action="order" data-order="new" class="load-bonuses__action bookmaker-items-subheader__order-item sort-bonus-item">Сначала новые</button>
                    <button data-action="order" data-order="value" class="load-bonuses__action bookmaker-items-subheader__order-item sort-bonus-item">По сумме бонуса</button>
                    <button data-action="order" data-order="type" class="load-bonuses__action bookmaker-items-subheader__order-item sort-bonus-item">По типу</button>
                </div>
            </div>
        </div>
    </div>
<!--     <div class="bookmaker-items-subheader__order">
        <div class="bookmaker-items-subheader__order-container">
            <button class="bookmaker-items-subheader__order-link bookmaker-items-subheader__bk-data">
                <span>Все букмекеры</span>
            </button>
            <div class="bookmaker-items-subheader__order-data-wrapper bookmaker-items-subheader__order-data-wrapper--right">
                <div class="bookmaker-items-subheader__order-search">
                    <div class="bookmaker-items-subheader__order-search-inside">
                        <input type="text" placeholder="Поиск" class="bookmaker-items-subheader__order-search-input">
                        <i class="fa-search"></i>
                    </div>
                </div>
                <div class="bookmaker-items-subheader__order-data">
                    <button data-action="bookmaker" data-id="0" class="load-bonuses__action bookmaker-items-subheader__order-item sort-bonus-item">Все букмекеры</button>
					<?php foreach ($bookmakers as $bookmaker) : ?>
                        <button data-action="bookmaker" data-id="<?php echo $bookmaker->bookmaker_id ?>" class="load-bonuses__action bookmaker-items-subheader__order-item sort-bonus-item"><?php echo $bookmaker->name ?></button>
					<?php endforeach; ?>
                </div>
            </div>
        </div>
    </div> -->
</div>
<div class="bonuses-list">
	<?php
	$filter_settings = BonusesFilter::getFilterSettings(get_the_ID());

	if (isset($filter_settings['bonus_type'])) {
		$page_type_id = get_term_by('id', $filter_settings['bonus_type'][0], 'bonus_type');
		$page_type = $page_type_id->name;
		$text_types = [
			'314' => [
				'name'  => $page_type . ' на фрибет',
				'count' => 0
			],
			'315' => [
				'name'  => $page_type . ' на бесплатную ставку',
				'count' => 0
			],
			'313' => [
				'name'  => $page_type . ' за регистрацию',
				'count' => 0
			],
			'312' => [
				'name'  => $page_type . ' без депозита',
				'count' => 0
			]	
		];
	}
	$bonus_position = 1;
    foreach ($bonuses as $bonus) :
    	if (isset($filter_settings['bonus_type'])) {
	    	foreach ($text_types as $k => $text_type) {
	    		if ($bonus->isType($k)) {
	    			$text_types[$k]['count'] ++;
	    		}
	    	}
	    }
    	if ($bonus->isType(2114)) {
            $schema = new SchemaOrg($bonus, 'bonus');
            echo $schema->schema;   		
    	}
		Bonuses::template('/items/bonus-loop.php', ['bonus' => $bonus, 'button_text' => $button_text, 'bonus_position' => $bonus_position]);
		$bonus_position++;
	endforeach; 
	 ?>
</div>
<?php if($bonuses_count > 30) : ?>
	<div class="loadmore-filter"><div class="btn btn-gray load-bonuses load-bonuses__action" data-action="load">Загрузить еще</div></div>
<?php endif; ?>
<?php if (! isset( $wp_query->query['type'] ) && in_array($filter_settings['bonus_type'][0], [2114, 312]) ) : ?>
<div class="snippet-table table-wrapper bookmaker-description kws__table">
	<table style="margin-bottom: 0;">
		<thead>
			<tr>
				<th><?php echo $page_type; ?></th>
				<th>Количество</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Всего активных бонусов</td>
				<td><?php echo $bonuses_count; ?></td>
			</tr>
			<?php foreach ($text_types as $k => $text_type) : 
				if ( $text_type['count'] && $k != $filter_settings['bonus_type'][0] ) :
			?>
				<tr>
					<td><?php echo $text_type['name']; ?></td>
					<td><?php echo $text_type['count']; ?></td>
				</tr>
			<?php 
				endif;
			endforeach; ?>
		</tbody>
	</table>
</div>
<?php endif; ?>

<?php } ?>