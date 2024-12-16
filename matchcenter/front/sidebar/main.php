<?php 
/**
 * Template part for main sidebar SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

    $sports = SP_SNS_Theme::getSports();

    $main_page_slug = get_option('sportspress_sns_main_page_page', '');

    $term_id = 0;
    if (is_archive()) {
        $term_id = get_queried_object()->term_id;
    }

    $main_link = '<a href="/' . $main_page_slug . '/">Матч-центр</a>';
    if ( SP_SNS_Theme::isMainMC() ) {
        $main_link = '<span>Матч-центр</span>';
    }

   
?>
    <div class="sidebar-widget sidebar-lenta">
        <div class="sidebar-lenta_title">
            <?php echo $main_link; ?>  
        </div>
        <div class="sidebar-lenta_body">
            <div class="sidebar_items_with_icon">
                <?php foreach ( $sports as $sport ) : ?>
                    <a href="<?php echo $sport->url ?>" class="sidebar_items_title"><?php echo $sport->name; ?></a>
                    <?php 
                    $leagues = $sport->getLeagues();
                    foreach ( $leagues as $league ) {

                        if ( $league->image_url ) {
                             $image = '<img class="lazy" data-src="' . $league->image_url . '">';
                        } else {
                            $image = '';
                        }                 
                        if ( $term_id == $league->ID ) {
                             $tag_open  = '<div class="active">';
                             $tag_close = '</div>';
                        } else {
                            $tag_open  = '<a href="' . $league->url . '">';
                            $tag_close = '</a>';
                        }
                        echo $tag_open;
                        echo $image;
                        echo $league->name;
                        echo $tag_close; 
                    } ?>
                <?php endforeach; ?>
            </div>
         </div>
    </div> 