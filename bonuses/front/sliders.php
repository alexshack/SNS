<?php

	if ( !isset($title) || empty($title) ) {
		$title = false;
	}

	if ( !isset($type_link) || empty($type_link) ) {
		$type_link = false;
	}
	
	if ( !isset($type_text) || empty($type_text) ) {
		$type_text = 'смотреть все';
	}



	if (isset($show_cats) && $show_cats) {
        global $wp_query;
        global $wp;

        if (isset($home_page) && $home_page) {
	        $menus = [
	            ['type' => 2114, 'name' => 'Все', 'url' => home_url() . '/promokody-bk/'],
	            ['type' => 316, 'name' => 'Бонус на экспресс', 'url' => home_url() . '/vse-bonusy-bukmekerov/?type=316'],
	            ['type' => 313, 'name' => 'Приветственный бонус', 'url' => home_url() . '/vse-bonusy-bukmekerov/?type=313'],
	            ['type' => 312, 'name' => 'Бездепозитный бонус', 'url' => home_url() . '/bk-s-bezdepozitnym-bonusom/'],
	            ['type' => 314, 'name' => 'Фрибеты', 'url' => home_url() . '/bk-s-fribetom/'],
	            ['type' => 318, 'name' => 'Кэшбек ', 'url' => home_url() . '/bk-s-keshbekom/'],
	        ];
        } else {
	        $menus = [
	            ['type' => false, 'name' => 'Все', 'url' => get_the_permalink(9206)],
	            ['type' => 314, 'name' => 'Фрибеты', 'url' => home_url() . '/bk-s-fribetom/'],
	            ['type' => 312, 'name' => 'Бездепозитные', 'url' => home_url() . '/bk-s-bezdepozitnym-bonusom/'],
	            ['type' => 2114, 'name' => 'Промокоды', 'url' => home_url() . '/promokody-bk/'],
	            ['type' => 318, 'name' => 'Кэшбек', 'url' => home_url() . '/bk-s-keshbekom/'],
	        ];
	    }
	} else {
		$show_cats = false;
	}

	$filter = [
	    'status' => 'active',
	];
	if (isset($bonus_type) && $bonus_type == 'best') {
		$home_bonuses = get_option('home_bonuses');
		$filter['ID__in'] = $home_bonuses;
	} else {
		if (isset($id__in) && !empty($id__in)) {
			if (!is_array($id__in)) {
				$id__in = str_replace(' ', '', $id__in);
				$id__in = explode(',', $id__in);
			}
			$filter['ID__in'] = $id__in;
		}
		if (isset($id__not_in) && !empty($id__not_in)) {
			if (!is_array($id__not_in)) {
				$id__not_in = str_replace(' ', '', $id__not_in);
				$id__not_in = explode(',', $id__not_in);
			}			
			$filter['ID__not_in'] = $id__not_in;
		}		
		if (isset($bonus_type) && !empty($bonus_type)) {
			if (!is_array($bonus_type)) {
				$bonus_type = str_replace(' ', '', $bonus_type);
				$bonus_type = explode(',', $bonus_type);
			}			
			$filter['bonus_type'] = $bonus_type;
		}	
		if (isset($bookmaker_id) && !empty($bookmaker_id)) {
			if (!is_array($bookmaker_id)) {
				$bookmaker_id = str_replace(' ', '', $bookmaker_id);
				$bookmaker_id = explode(',', $bookmaker_id);
			}			
			$filter['bookmaker_id'] = $bookmaker_id;
		}
		if (isset($bookmaker_id__not_in) && !empty($bookmaker_id__not_in)) {
			if (!is_array($bookmaker_id__not_in)) {
				$bookmaker_id__not_in = str_replace(' ', '', $bookmaker_id__not_in);
				$bookmaker_id__not_in = explode(',', $bookmaker_id__not_in);
			}			
			$filter['bookmaker_id__not_in'] = $bookmaker_id__not_in;
		}	
	}
	if (!isset($limit) || empty($limit)) {
		$limit = 12;
	}
	$bonuses = Bonuses::setup((new BonusesFilter())->where($filter)->limit($limit)->order('popular')->getResults());

	if ($bonuses) {
		$b_count = count($bonuses);
		if (count($bonuses) > 3 ) {
			$b_count = 4;
		}
	}
?>

<?php if($bonuses) : ?>
<div class="bookmaker_block bookmaker-main-bonuses bonuses_main">
	<?php if ($title) { ?>
		<div class="bookmaker_block-header">
			<h2 class="bookmaker_block-header_title"><?php echo $title ?></h2>
		
			<?php if ($type_link) { ?>
			<div class="bookmaker_block-header_btns">
				<a href="<?php echo home_url() . '/' . $type_link . '/'; ?>" class="bookmaker_block-header_btn"><?php echo $type_text; ?></a>
			</div>
			<?php } ?>
		</div>
	<?php } ?>
	<?php if ($show_cats) {
		echo '<nav class="category_menu">';
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
        echo '</nav>';
	}
	?>
	<div class="bonuses-slider_wrapper">
		<div class="bonuses-slider">
			<?php 
	       	$bonus_position = 1;
			foreach ($bonuses as $bonus) {
				if(!isset($button_text)) {
	        		$button_text = 'Забрать бонус';
	    		}
				Bonuses::template('/items/bonus-loop.php', ['bonus' => $bonus, 'button_text' => $button_text, 'bonus_position' => $bonus_position]);
				$bonus_position++;		  
			}
	        ?>

		</div>
    <?php if ($b_count > 1) : ?>
    	<div class="slider_arrow slider_arrow_prev slider_arrow_<?php echo $b_count; ?>"></div>
    	<div class="slider_arrow slider_arrow_next slider_arrow_<?php echo $b_count; ?>"></div>
    <?php endif; ?>		
	</div>		
</div>
<?php endif; ?>