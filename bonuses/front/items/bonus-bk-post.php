<?php
    if(get_post_bk()) {
        $bk         = get_post_bk(1);
	    $bk_bonus   = $bk->getBonusValue();
        $bonus      = $bk->getBonusValue();
        $bonus_type = '';
        if (is_array($bk->bonus)) {
            $bonus_type = $bk->bonus->getBonusType();
        }
    } else {
        $ad_unit    = get_option('_ad_unit');
        $bk         = Bookmaker::setup($ad_unit['bk']);
	    $bonus      = $ad_unit['bonus_value'];
	    $bonus_type = $ad_unit['bonus'];
    }

	$bk_title  = $bk->name;
	$bk_rating = $bk->rate;
	$bk_apps   = get_the_terms( $bk->ID, 'site_versions' );
    $btn_text  = (preg_match('/регистр/', mb_strtolower(get_the_title()))) ? 'Зарегистрироваться' : 'Скачать приложение';
    
    $filter = (new BonusesFilter())->where(['ID__in' => $bk->bonus->ID]);
    $bonusModel = Bonuses::setup($filter->limit(1)->getResults())[0];
    $wager = get_post_meta($bk->bonus->ID, 'bs_wager', true);
    if (!empty($wager)) {
        $wager_time = get_post_meta($bk->bonus->ID, 'bs_wager_time', true);
        if (empty($wager_time)) {
           $wager_time = 'Бессрочно'; 
        } else {
           $wager_time .= ' д.';
        }
    } else {
        $wager = '-';
        $wager_time = '-';
    }
    $kf_start = get_post_meta($bk->bonus->ID, 'bs_kf_start', true);
    $kf_end = get_post_meta($bk->bonus->ID, 'bs_kf_end', true);
    $kf = '';
    if (!empty($kf_start) || !empty($kf_end)) {
        if (!empty($kf_start)) $kf .= 'от ' . $kf_start . ' ';
        if (!empty($kf_end))   $kf .= 'до ' . $kf_end;
    } else {
       $kf = '-'; 
    }
?>
<?php if( isset($bk->bonus) && ! $bk->isBlocked() ) : ?>
<div class="bonus_item bonus_item_bk">
    <div class="bonus_item_head lazy" data-background-image="<?php echo SNS_URL ?>/img/bonus-bk-bg.jpg">
        <div class="bonus_item_top">
            <div class="bonus_item_rating">
                <div class="bonus_item_position <?php echo (!get_post_bk()) ? 'not-bk-post' : ''; ?>"><?php echo '#' . get_rating_number_bk( $bk->ID ); ?> в рейтинге</div>
                <div class="bonus_item_rate fa-star-full"><?php echo float_val($bk_rating); ?></div>
            </div>
            <a href="<?php echo $bk->getPermalink() ?>" class="bonus_item_link">
                <?php if(get_post_bk()) : ?>
                    <?php echo $bk->thumbnail->getLazyLoadImg('bonus_item_logo', ['alt' => $bk_title], '264x85') ?>
                <?php endif; ?>            
            </a>
        </div>
        <div class="bonus_item_bonus">
            <svg class="bonus_item_bonus_img"><use xlink:href="<?php echo SNS_URL; ?>/img/icons/gift-bk.svg#gift-bk"></use></svg>
            <div class="bonus_item_value_block">
                <div class="bonus_item_type"><?php echo $bonusModel->getBonusType(); ?></div>
                <div class="bonus_item_value"><?php echo $bonus; ?></div>
            </div>
        </div>
        <div class="bonus_item_buttons">
            <?php
            if($bk->getPromoCode() && get_post_bk() || $bk->getPromoCode() && in_ad_unit_category(get_the_ID()) && get_option('_ad_unit')['promocode'] === 'yes') {
                echo '<div class="bonus_item_promo promo-code-action bk-promo-code"' . $bk->getPromoAttr() . '>';
                echo $bk->getPromoCode();
                echo '<svg class="bonus_item_promo_img"><use xlink:href="' . SNS_URL . '/img/icons/copy2.svg#copy2"></use></svg>';
                echo '</div>';
            }
            echo $bk->partnerLinkMarkup('Получить бонус', ['class' => 'bonus_item_get']); 
            ?>
        </div>        
    </div>
    <?php if(get_post_bk()) : ?>
    <div class="bonus_item_items">
        <div class="bonus_item_item">
            <div class="bonus_item_item-title">Отыгрыш:</div>
            <div class="bonus_item_item-value"><?php echo $wager; ?></div>
        </div>
        <div class="bonus_item_item">
            <div class="bonus_item_item-title">Срок отыгрыша:</div>
            <div class="bonus_item_item-value"><?php echo $wager_time; ?></div>
        </div>
        <div class="bonus_item_item">
            <div class="bonus_item_item-title">Срок действия:</div>
            <div class="bonus_item_item-value"><?php echo ($bonusModel->getDateEnd()) ? $bonusModel->getDateEnd() : '-'; ?></div>
        </div>
        <div class="bonus_item_item">
            <div class="bonus_item_item-title">Мин. депозит:</div>
            <div class="bonus_item_item-value"><?php echo ($bonusModel->getMinBonus()) ? $bonusModel->getMinBonus() : '-'; ?></div>
        </div>
        <div class="bonus_item_item">
            <div class="bonus_item_item-title">Мин. коэф.:</div>
            <div class="bonus_item_item-value"><?php echo $kf; ?></div>
        </div>                                
    </div>
    <?php endif; ?>
    <div class="bonus_item_all">
        <a href="<?php echo home_url() . '/bonusy-' . $bk->post_name . '/'; ?>">Все бонусы</a>
    </div>
</div>
<?php endif; ?>