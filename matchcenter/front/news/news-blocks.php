<?php
/**
 * Template part for news blocks SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

$defaults = array(
   'title'          => 'Новости',
   'link'           => false,
   'posts'          => false,
   'button'         => 'Все новости'
);

extract( $defaults, EXTR_SKIP );

if ( $posts && count( $posts ) ) : ?>
   <div class="sp_block">
        <?php  if ( $title ) : ?>
            <div class="sp_block_title">
                <h2 class="sp-table-caption"><?php echo wp_kses_post( $title ) ?></h2>
                <?php if ($link) : ?>
                    <a href="<?php echo $link; ?>"><?php echo $button; ?></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="sp_news_block">
            <?php foreach ( $posts as $i => $item ):
                sp_get_template( 'news-block.php', ['item' => $item], SP()->template_path() . 'news/',  ); 
            endforeach; ?>
        </div>
    </div>
<?php endif; ?>