<?php
/**
 * Template part for predicts blocks SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

$defaults = array(
   'title'  => 'Прогнозы',
   'link'   => false,
   'posts'  => false
);

extract( $defaults, EXTR_SKIP );


if ($posts) : ?>
    <div class="sp_block">
        <?php if ( $title ) : ?>
            <div class="sp_block_title">
                <h2 class="sp-table-caption"><?php echo wp_kses_post( $title ) ?></h2>
                <?php if ($link) : ?>
                    <a href="<?php echo $link; ?>">Все прогнозы</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php
        $predict = new SP_SNS_Predict( $posts[0] );
        if ( $predict->bookmaker ): ?>
            <a class="predicts-item__promo-code predicts-item__not-promo sp_predicts_bonus" href="<?php echo $predict->bookmaker->link; ?>" target="_blank" rel="nofollow">
                Бонус на матч
            </a>
        <?php endif; ?>     
        <div class=" predicts-list--table sp_predicts_blocks">
            <?php foreach ( $posts as $item ) {
                $predict = new SP_SNS_Predict( $item->ID );
                include 'predict-block.php'; 
            } ?>
        </div>
    </div>
<?php endif; ?>