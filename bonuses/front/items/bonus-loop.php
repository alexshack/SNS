<?php 
$bonus_link = '';
$has_link = false;
$banner_tag = '<div class="bonus_item_banner">';
$value_tag  = '<div class="bonus_item_content">';
$close_tag  = '</div>';

$is_public = get_post_meta( $bonus->ID, 'bonus_is_public', true );
if ($is_public == 'yes') {
   $bonus_link = '<a href="' . get_the_permalink($bonus->ID) . '" class="bonus_item_link">Подробнее</a>';
   $has_link = true;
	$banner_tag = '<a class="bonus_item_banner" href="' . get_the_permalink($bonus->ID) . '">';
	$value_tag = '<a class="bonus_item_content" href="' . get_the_permalink($bonus->ID) . '">';   
	$close_tag  = '</a>';
}

?>

<div class="bonus_item bonus_item-loop">
	<?php echo $banner_tag; ?>
        <?php if (isset($ismore) && $ismore ) : ?>
            <img src="<?php echo $bonus->getImageURL(); ?>" alt="<?php echo $bonus->post_title; ?>" class="bonus_item_banner-img lozad lazy" width="273" height="193">';            
        <?php else : ?>
            <img src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $bonus->getImageURL(); ?>" alt="<?php echo $bonus->post_title; ?>" class="bonus_item_banner-img lozad lazy" width="273" height="193">
        <?php endif; ?>		
	<?php echo $close_tag; ?>
	
	<div class="bonus_item_head">
		<?php 
		echo ($bonus->bookmaker->thumbnail) ? $bonus->bookmaker->imageLinkMarkup('bonus_item_', '', '105x70') : ''; 		
		if($bonus->getBonusValue()) {
			echo '<div class="bonus_item_value">';
    		echo $bonus->getBonusValue();
    		echo '</div>';
		}
		?>
	</div>
	<?php echo $value_tag; ?>
		<?php echo do_shortcode($bonus->post_title); ?>
	<?php echo $close_tag; ?>
	<div class="bonus_item_grow"><?php echo $bonus_link; ?></div>
	<div class="bonus_item_buttons">
		<?php
		if ( $bonus->getPromoCode() ) {
			echo '<div class="bonus_item_promo promo-code-action bk-promo-code"' . $bonus->getPromoAttr() . '>';
			echo $bonus->getPromoCode();
			echo '<svg class="bonus_item_promo_img"><use xlink:href="' . SNS_URL . '/img/icons/copy2.svg#copy2"></use></svg>';
			echo '</div>';
		}
		echo $bonus->bookmaker->partnerLinkMarkup('Получить бонус', ['class' => 'bonus_item_get']); 
		?>
	</div>
</div>