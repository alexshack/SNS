<?php get_header(); ?>
<?php global $wp_query;

$page_content = apply_filters( 'the_content', get_the_content() );
$games = Cyber::getGames(true);

$page_title = get_the_title();
$current_section = '';
if (get_query_var('sport')) {
    $page_title .= ' | ' . Cyber::$sports[get_query_var('sport')]['name'];
    $current_section = get_the_permalink(Cyber::getOption('games_page'));
}
?>
<div class="wrapper wrapper-bookmaker">
    <div class="main main-page-cyber">
        <h1><?php echo $page_title; ?></h1>
    </div>
</div> 
<div class="wrapper wrapper-bookmaker">
    <div class="main main-page-cyber">    
		<?php Template::render('templates/cyber/header', ['current_section' => $current_section]); ?>
        <div class="cyber_games_list">
            <?php if(count($games)) :
                $k = 0;
                foreach ( $games as $i => $game ):
                    Template::render('templates/cyber/loop-games', ['game' => $game, 'game_id' => $i]);
                    if ($k == 5) {
                        echo do_shortcode('[online-broadcast type=cyber]');
                    } 
                    $k++;                  
                endforeach;
/*                echo '<pre>';
                print_r($games);
                echo '</pre>';*/
            else: ?>
                <p>Нет записей.</p>
            <?php endif; ?>
        </div>
        <?php Template::render('templates/cyber/footer'); ?> 
        <?php if($page_content) : ?>
            <div class="cyber-content bookmaker-description">
                <?php echo $page_content ?>
            </div>
        <?php endif; ?>            
    </div>
    <aside class="sidebar">
		<?php Template::render('templates/cyber/sidebar', ['games' => $games]); ?>
    </aside>
</div>
<?php get_footer(); ?>
