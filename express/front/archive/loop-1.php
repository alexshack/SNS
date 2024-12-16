<div class="express_item">
	<div class="express_item_header">
		<div class="express_item_image">
			<?php echo $express->getThumbnailImg('attachment-post-thumbnail size-post-thumbnail wp-post-image'); ?>
			<?php if ($express->date): ?>
				<span class="express_item_date"><?php echo $express->date //date( 'd.m.Y', $express->date ); ?></span>
			<?php endif; ?>			
		</div>
		<div class="express_item_name">
			<?php echo $express->post_title; ?>
		</div>
		<a class="express_item_link" href="<?php echo $express->getPermalink(); ?>" aria-label="<?php echo $express->post_title; ?>" title="<?php echo $express->post_title; ?>"></a>
	</div>
	<div class="express_item_content">
       <?php foreach ($express->games as $k => $game) : ?>
            <div class="express_game">
                <div class="express_game_number">
                    <?php echo $game['type_icon']; ?>                        
                </div>
                <div class="express_game_name">
                    <?php echo $game['name']; ?>
                </div>                                        
                <div class="express_game_coef">
                    <div class="express_button">
                        <span class="express_button_kf" data-kf="<?php echo $game['coef']; ?>">
                            <?php echo $game['bet']; ?>
                        </span>
                    </div>
                </div>
            </div>                
        <?php endforeach; ?>
    </div>
    <div class="express_item_footer">
        <a href="<?php echo $express->bookmaker->getPartnerLink(); ?>" target="_blank" rel="nofollow" class="express_bk_link">
            <?php echo $express->bookmaker->thumbnail->getLazyLoadImg('', ['alt' => 'Букмекер'], '195x50'); ?>
        </a>
        <a href="<?php echo $express->url ?>" target="_blank" rel="nofollow" class="express_button">
            <span class="express_button_kf" data-kf="КФ <?php echo $express->coef; ?>">
                Экспресс
            </span>
        </a>
	</div>
</div>
