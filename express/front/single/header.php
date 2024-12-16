<?php
    //$bookmaker = new Bookmaker($express->$bookmaker);
    //$bookmaker = Bookmaker::setup($express->$bookmaker)
?>
<div class="express_header">
	<div class="express_header_container">
		
        <h1 class="express_h1"><?php echo $express->post_title; ?></h1>
        <div class="express_games">
            <?php foreach ($express->games as $k => $game) : ?>
                <div class="express_game">
                    <div class="express_game_number">
                        <div>№<?php echo $k + 1; ?></div>
                        <?php echo $game['type_icon']; ?>                        
                    </div>
                    <div class="express_game_center">
                        <div class="express_game_sport">
                            <?php echo $game['type'] . '. ' . $game['tournament']; ?>
                        </div>
                        <div class="express_game_date">
                            <?php echo $express->date . ' ' . $game['time']; ?>
                        </div>                        
                        <div class="express_game_name">
                            <?php echo $game['name']; ?>
                        </div>                                        
                    </div>
                    <div class="express_game_coef">
                        <div class="express_button">
                            <span class="express_button_kf" data-kf="КФ <?php echo $game['coef']; ?>">
                                <?php echo $game['bet']; ?>
                            </span>
                        </div>
                    </div>
                </div>                
            <?php endforeach; ?>
        </div>
        <div class="express_bottom">
            <div>Итоговый коэффициент:</div>
            <div class="express_bottom_buttons">
                <a href="<?php echo $express->bookmaker->getPartnerLink(); ?>" target="_blank" rel="nofollow" class="express_bk_link">
                    <?php echo $express->bookmaker->thumbnail->getLazyLoadImg('', ['alt' => 'Букмекер'], '195x50'); ?>
                </a>
                <a href="<?php echo $express->url ?>" target="_blank" rel="nofollow" class="express_button">
                    <span class="express_button_kf" data-kf="КФ <?php echo $express->coef; ?>">
                        Сделать ставку
                    </span>
                </a>                
            </div>
        </div>
    </div>
</div>
