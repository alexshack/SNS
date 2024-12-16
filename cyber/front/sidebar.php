<?php 
    $categories = false;
    if(Cyber::isMain()) {
        if (isset(Cyber::$posts_sections['news']['children'])) {
            $categories = Cyber::$posts_sections['news']['children'];
        }
        $main_page_link = get_the_permalink(Cyber::getOption('news_page'));
        $active_page = get_query_var('child');
        $menu_title = 'Новости';
    } 
    if(Cyber::isPosts()) {
        if (isset(Cyber::$posts_sections['posts']['children'])) {
            $categories = Cyber::$posts_sections['posts']['children'];
        }
        $main_page_link = get_the_permalink(Cyber::getOption('posts_page'));
        $active_page = get_query_var('child');
        $menu_title = 'Статьи'; 
    }
    if(Cyber::isPredicts()) {
        if (isset(Cyber::$predicts_sections['predicts']['children'])) {
            $categories = Cyber::$predicts_sections['predicts']['children'];
        }
        $main_page_link = get_the_permalink(Cyber::getOption('predicts_page'));
        $active_page = get_query_var('tournament');
        $menu_title = 'Прогнозы'; 
    }
    if ($categories) :           
?>
    <div class="sidebar-widget sidebar-lenta sidebar-current">
        <div class="sidebar-lenta_title"><?php echo $menu_title; ?></div>
        <div class="sidebar-lenta_body">
            <div class="sidebar_items_with_icon">
                <?php foreach ( $categories as $k => $category ) {
                    $image_id = get_term_meta( $category['cat_id'], '_thumbnail_id', 1 );
                    if ( $image_id ) {
                        $image_url = wp_get_attachment_url( $image_id );
                        $image = '<img class="lazy" src="' . $image_url . '">';
                    } else {
                        $image = '';
                    }                 
                    if ($k == $active_page) {
                         $tag_open  = '<div class="active">';
                         $tag_close = '</div>';
                    } else {
                        $tag_open  = '<a href="' . $main_page_link . $k . '/">';
                        $tag_close = '</a>';
                    }
                    echo $tag_open;
                    echo $image;
                    echo $category['page_name'];
                    echo $tag_close; 
                } ?>
            </div>
        </div>
    </div> 

<?php endif ?>

<?php
if(count($games) && !Cyber::isGames()) {
    $i = 0;
?>
    <div class="sidebar-widget sidebar-lenta sidebar-current">
        <div class="sidebar-lenta_title">
            <span>Матчи</span>
            <a href="<?php echo get_the_permalink(Cyber::getOption('games_page')); ?>">Все</a>
        </div>
        <div class="sidebar-lenta_body">
        <?php foreach ( $games as $k => $game ) {
            Template::render('templates/cyber/loop-games', ['game' => $game, 'bookmaker' => $bookmaker, 'game_id' => $k]);
            $i++;
            if ($i == 4) break;
        } ?>
        </div>
    </div>

<?php } ?>

<?php
    if(Cyber::isGames()) {
        $sports = Cyber::$sports;
        $game_page_link = get_the_permalink(Cyber::getOption('games_page'));
        $sport_page = get_query_var('sport');    
?>

    <div class="sidebar-widget sidebar-lenta sidebar-current">
        <div class="sidebar-lenta_title">Матчи</div>
        <div class="sidebar-lenta_body">
            <div class="sidebar_items_with_icon">
                <?php foreach ( $sports as $k => $sport ) {
                    if ($k == $sport_page) {
                         $tag_open  = '<div class="active">';
                         $tag_close = '</div>';
                    } else {
                        $tag_open  = '<a href="' . $game_page_link . $k . '/">';
                        $tag_close = '</a>';
                    }
                    echo $tag_open;
                    echo '<img class="lazy" src="' . SNS_URL . '/img/cyber/' . $k . '.png">';
                    echo $sport['name'];
                    echo $tag_close; 
                } ?>
            </div>
        </div>
    </div>            

<?php } ?>

<?php Template::render('templates/sidebar/lenta/lenta', ['posts_count' => 40]); ?>