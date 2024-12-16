<div class="<?php echo $bonus->isFinished() ? 'finished ' : ''; ?> <?php echo isset($style) ? 'bonus-loop__' . $style : ''; ?> bonus-loop <?php echo $bonus->isExclusive() ? 'exclusive' : ''; ?> <?php echo $bonus->isNew() ? 'exclusive new' : ''; ?> bookmaker-bonus-item bonus-loop__<?php echo $bonus->ID; ?> <?php echo $bonus->isType(314) || $bonus->isType(2114) ? 'hide-condition-title' : ''; ?>">
	<div class="bonus-loop__container">
		<?php if($bonus->isFinished()) : ?>
            <div class="bonus-loop__exclusive">Завершен</div>
        <?php else : ?>
			<?php if($bonus->isExclusive()) : ?>
                <div class="bonus-loop__exclusive">Эксклюзив</div>
			<?php endif; ?>
            <?php if($bonus->isNew()) : ?>
                <div class="bonus-loop__exclusive bonus-loop__new">New</div>
            <?php endif; ?>            
		<?php endif; ?>
        <?php 
        if (isset($ismore) && $ismore ) {
            echo '<img src="' . $bonus->getImageURL() . '" alt="' . $bonus->post_title . '" class="bonus-loop__image lozad lazy" width="273" height="193">';            
        } else {
            echo '<img src="' . Thumbnail::$lazy_preview . '" data-src="' . $bonus->getImageURL() . '" alt="' . $bonus->post_title . '" class="bonus-loop__image lozad lazy" width="273" height="193">';
        }
        ?>
        <?php if(isset($bk_link) && $bk_link === false) : ?>
        <span class="bonus-loop__bookmaker-image-link">
            <?php echo $bonus->bookmaker->thumbnail->getLazyLoadImg('bonus-loop__bookmaker-image', ['alt' => do_shortcode($bonus->post_title)], '105x50'); ?>
        </span>
        <?php else : ?>
		<?php echo $bonus->bookmaker->imageLinkMarkup('bonus-loop__bookmaker-'); ?>
        <?php endif; ?>
        <div class="bonus-loop__content-top">
            <div class="bonus-loop__types">
				<?php foreach ($bonus->getTypes() as $type) : ?>
                    <div class="bonus-loop__type" data-after="<?php echo $type->name; ?>"></div>
				<?php endforeach; ?>
            </div>
            <div class="bonus-loop__date">
				<?php
				if(intval($bonus->date_unlimited) === 1) : ?>
                    Бессрочно
				<?php else :
					echo date('d.m.Y', strtotime($bonus->date_end));
				endif; ?>
            </div>
        </div>
		<div class="bonus-loop__content">

			<?php if($bonus->bookmaker->getPromoCode() || $bonus->getPromoCode()) : ?>
                <?php 
                    $promocode = $bonus->getPromoCode() ? $bonus->getPromoCode() : $bonus->bookmaker->getPromoCode();
                    $promoattr = $bonus->getPromoAttr() ? $bonus->getPromoAttr() : $bonus->bookmaker->getPromoAttr();

                ?>
                <div class="bonus-loop__title"><?php echo do_shortcode($bonus->post_title); ?></div>
                <div class="bonus-loop__promocode promo-code-action bk-promo-code" <?php echo $promoattr; ?>>
					<?php echo $promocode; ?>
                </div>
                <a class="bonus-loop__button" target="_blank" rel="nofollow" data-after="<?php echo isset($button_text) ? $button_text : 'Забрать бонус' ?>"  href="<?php echo $bonus->bookmaker->getPartnerLink() ?>" aria-label="<?php echo isset($button_text) ? $button_text : 'Забрать бонус' ?>">
                    <?php if(isset($_POST)) : ?>
                        <img class="bonus-loop__button-img" src="/wp-content/themes/stavkinasport.com/img/cursor.png" alt="">
                    <?php else : ?>
                        <img class="bonus-loop__button-img lozad" data-src="/wp-content/themes/stavkinasport.com/img/cursor.png" src="<?php echo Thumbnail::$lazy_preview ?>" alt="">
                    <?php endif; ?>
                </a>
            <?php else: ?>
                <div class="bonus-loop__title"><?php echo $bonus->post_title; ?></div>
                <?php if($bonus->getBonusValue()) : ?>
                    <div class="bonus-loop__value"><?php echo $bonus->getBonusValue(); ?></div>
                <?php endif; ?>
                <a class="bonus-loop__button" target="_blank" data-after="<?php echo isset($button_text) ? $button_text : 'Забрать бонус' ?>" rel="nofollow" href="<?php echo $bonus->bookmaker->getPartnerLink() ?>" aria-label="<?php echo isset($button_text) ? $button_text : 'Забрать бонус' ?>">
	                <?php if(isset($_POST)) : ?>
                        <img class="bonus-loop__button-img" src="/wp-content/themes/stavkinasport.com/img/cursor.png" alt="">
	                <?php else : ?>
                        <img class="bonus-loop__button-img lozad" data-src="/wp-content/themes/stavkinasport.com/img/cursor.png" src="<?php echo Thumbnail::$lazy_preview ?>" alt="">
	                <?php endif; ?>
                </a>
			<?php endif; ?>
            <div class="bonus-loop__desc-button js_open-bonus-details" data-bonus-url="<?php echo $bonus->bookmaker->getPartnerLink() ?>">Как получить?</div>
        </div>
	</div>
    <div class="bookmaker-bonus-item__modal-content">
        <div class="bookmaker-bonus-item__details">
            <?php if(intval($bonus->amount) !== 0) : ?>
            <div class="bookmaker-bonus-item__details-item">
                <div>Размер бонуса:</div>
                <span><?php echo $bonus->amount; ?>%</span>
            </div>
            <?php endif; ?>
			<?php if ( $bonus->date_start ) : ?>
                <div class="bookmaker-bonus-item__details-item">
                    <div>Начало:</div>
                    <span><?php echo date('d.m.Y', strtotime($bonus->date_start)); ?></span>
                </div>
			<?php endif; ?>
			<?php if ( $bonus->date_end > '2000-01-01' ) : ?>
                <div class="bookmaker-bonus-item__details-item">
                    <div>Окончание:</div>
                    <span><?php echo date('d.m.Y', strtotime($bonus->date_end)); ?></span>
                </div>
			<?php endif; ?>
			<?php if ($bonus->getMaxBonus()) : ?>
                <div class="bookmaker-bonus-item__details-item">
                    <div>Макс. бонус:</div>
                    <span><?php echo $bonus->getMaxBonus() ?></span>
                </div>
			<?php endif; ?>
			<?php if ( $bonus->getMinBonus() ) : ?>
                <div class="bookmaker-bonus-item__details-item">
                    <div>Мин. депозит:</div>
                    <span><?php echo $bonus->getMinBonus() ?></span>
                </div>
			<?php endif; ?>
        </div>
        <div class="bookmaker-bonuses-item__text">
			<?php
            if($bonus->isType(314) || $bonus->isType(2114)) {
                echo strtr(apply_filters( 'the_content', do_shortcode( $bonus->post_content ) ), [
	                '<h2>Условия:</h2>' => '',
	                '<h3>Условия:</h3>' => '<span class="h3">Условия:</span>'
                ]);
            } else {
	            echo strtr(apply_filters( 'the_content', do_shortcode( $bonus->post_content ) ), [
		            '<h3>Условия:</h3>' => '<span class="h3">Условия:</span>'
	            ]);
            }

			?>
        </div>
    </div>
</div>
