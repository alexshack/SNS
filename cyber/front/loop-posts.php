<?php
$btn_text =  'Подробнее';
echo '<div class="articles-item">';
if($item->getCategory()) {
	echo '<div class="articles-item__term">' . $item->getCategoryName() . '</div>';
}
echo '<div class="articles-item__inside">';
if($item->thumbnail) {
	echo '<a href="'. $item->getPermalink() .'" class="articles-item__image">'. $item->thumbnail->getLazyLoadImg('', ['alt' => $item->getTitle()], '355x355') . '</a>';
}
echo '<div class="articles-item__content">';
echo '<a href="'. $item->getPermalink() .'" class="articles-item__title">'. $item->getTitle() .'</a>';
echo '<div class="articles-item__date">'.$item->getDate('j F Y').'</div>';
echo '<a href="'.$item->getPermalink().'" class="articles-item__link">' . $btn_text . ' <i class="fa-arrow-right"></i></a>';
echo '</div>';
echo '</div>';
echo '</div>';