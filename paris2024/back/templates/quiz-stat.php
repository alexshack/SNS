<?php
$bookmaker = Bookmaker::setup_all($bk_id);

$promo_value = false;
$bonus_value = false;

if($bookmaker->getBonusValue()) {
    $bonus_value = $bookmaker->getBonusValue();
}


if ( $bookmaker->getPromoCode() ) {
    $promo_open  = '<div class="bk_item_promo promo-code-action bk-promo-code"' . $bookmaker->getPromoAttr() . '>';
    $promo_close = '</div>';
    $promo_value = $bookmaker->getPromoCode();
    if ( $bookmaker->isBlocked() ) {
        $link_tag = '<span class="bk_item_site promo-code-action bk-promo-code" ' . $bookmaker->getPromoAttr() . '>Промо</span>';
    }
} else {
    if ( !$bookmaker->isBlocked() ) {
        $promo_open  = '<a class="bk_item_promo" href="' . $bookmaker->getPartnerLink() . '" ' .$bookmaker->getKloakaAttr('', '') . ' target="_blank" rel="nofollow">';
        $promo_close = '</a>';
        
    } else {
        $promo_open  = '<a class="bk_item_promo" ' . $bookmaker->getKloakaAttr('', '') . ' href="' . $bookmaker->getPartnerLink() . '" target="_blank" rel="nofollow">';
        $promo_close = '</a>';
    }
}

?>

<div class="quiz24_modal_page" style="background-image: url(<?php echo SNS_URL ?>/img/quiz2024/quiz-bg.jpg);">
	<div class="quiz24_modal_page_header">
		<div class="quiz24_modal_page_left">
			<div class="quiz24_modal_page_number">Квиз</div>
			<div class="quiz24_modal_page_question"><?php echo $question; ?></div>
		</div>
		<div class="quiz24_modal_page_right">
			<div class="quiz24_modal_page_logo">
                <img class="quiz24_modal_page_logo_image"
                     src="<?php print get_template_directory_uri(); ?>/img/olympics/logo-2024.svg"
                     width="261"
                     height="315"
                     alt="<?php the_title(); ?>">
            </div>
		</div>
	</div>
	<div class="quiz24_modal_page_stats">
		<?php foreach ( $stats as $stat ) : ?>
			<div class="quiz24_modal_page_stat">
				<div class="quiz24_modal_page_stat_self"><span style="height: <?php echo $stat[0]; ?>%" title="Ваш голос: <?php echo $stat[2] . ': ' . $stat[0] . '%' ?>"></div>
				<div class="quiz24_modal_page_stat_max"><span style="height: <?php echo $stat[1]; ?>%" title="Большинство: <?php echo $stat[3] . ': ' . $stat[1] . '%' ?>"></div>
			</div>
		<?php endforeach; ?>
	</div>
	<div class="quiz24_modal_page_footer">
		<div class="quiz24_modal_page_footer_bk">
		     <a class="quiz24_modal_page_bk_link" href="<?php echo $bookmaker->getPermalink(); ?>" title="Обзор <?php echo $bookmaker->post_title; ?>">
		          <?php if($bookmaker->thumbnail) {
		               echo $bookmaker->thumbnail->getLazyLoadImg('wp-post-image', ['alt' => $bookmaker->post_title], '131x40');
		          } ?>
		     </a>
	        <?php echo $promo_open; ?>
	            <div class="bk_item_promo-data">
	                <?php if ($bonus_value) : ?>
	                    <div class="bk_item_promo-bonus">
	                        <?php echo $bonus_value; ?>
	                    </div>
	                <?php endif; ?>
	                <?php if ($promo_value) : ?>
	                    <div class="bk_item_promo-promo">
	                        <?php echo $promo_value; ?>
                  
	                    </div>                    
	                <?php endif; ?>
	            </div>          
	        <?php echo $promo_close; ?>		     
			<a class="quiz24_modal_page_bk_site" href="<?php echo $bookmaker->getPartnerLink() . '" ' . $bookmaker->getKloakaAttr('', '') ?>" target="_blank" rel="nofollow">Играть</a>		     
		</div>
		<div class="quiz24_modal_page_buttons">
			<div class="quiz24_modal_page_button" onclick="Modal.close()">Понятно</div>
		</div>		
	</div>
</div>
