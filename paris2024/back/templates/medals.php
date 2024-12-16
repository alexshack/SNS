<?php
Enqueue::footer('/olympics/olympics-medals.css');
$medals = $controller->getMedals();

/*if(!empty($top) && $top && !$controller->medalsEnabled()){
	$medals = array_slice($medals, 0, 4);
}

$russia_in_top = false;*/

?>

<div class="olympics_table olympics_block_inside more-wrapper">
    <table class="olympics_medals">
        <thead>
        <tr>
            <th>#</th>
            <th class="olympics_team-head">Команда</th>
            <th >
                <div class="olympics_medals-head">
                    <svg class="olympics_medals-icon">
                        <use xlink:href="<?php echo SNS_URL;  ?>/img/olympics/final.svg#final"></use>
                    </svg>
                    <span>Золото</span>
                </div>
            </th>
            <th>
                <div class="olympics_medals-head">
                    <svg class="olympics_medals-icon">
                        <use xlink:href="<?php echo SNS_URL;  ?>/img/olympics/silver.svg#silver"></use>
                    </svg>
                    <span>Серебро</span>
                </div>
            </th>
            <th>
                <div class="olympics_medals-head">
                    <svg class="olympics_medals-icon">
                        <use xlink:href="<?php echo SNS_URL;  ?>/img/olympics/third.svg#third"></use>
                    </svg>
                    <span>Бронза</span>
                </div>
            </th>
            <th>
                <div class="olympics_medals-head">
                    <svg class="olympics_medals-icon">
                        <use xlink:href="<?php echo SNS_URL;  ?>/img/olympics/final.svg#final"></use>
                    </svg>
                    <span>Всего</span>
                </div>
            </th>
        </tr>
        </thead>
        <tbody class="olympics_medals-body">
		<?php if ( $medals ):
			foreach ( $medals as $k => $medal ):
/*                if(!empty($top) && $top && $controller->medalsEnabled()) {
                    if($k < 3 && $medal->country_name === 'Россия') {
                        $russia_in_top = true;
                    }
                    if($k > 2 && !$russia_in_top && $medal->country_name !== 'Россия') {
                        continue;
                    }
                }*/
				echo $controller->getSingleMedalContent( $medal, $k + 1 );
/*				if($k > 2 && $medal->country_name === 'Россия' && $top || $russia_in_top && $k > 2 && $top) {
				    break;
                }*/
			endforeach;
		endif; ?>
        </tbody>
    </table>
    <?php if(count($medals) > 10) : ?>
    <div class="olympics_medals-more">
        <button class="olympics_medals-more_button more-btn">Показать все</button>
    </div>
    <?php endif; ?>    
</div>
