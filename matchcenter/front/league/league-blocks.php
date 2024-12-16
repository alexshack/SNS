<?php
/**
 * Template part for Leagues block SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

$defaults = array(
    'type' => null,
    'slug'  => '',
    'title' => 'Турниры:',
    'hide_transfers' => false
);

extract( $defaults, EXTR_SKIP );

$league_args = [
    'taxonomy' => 'sp_league',
    'meta_query' => [
        'relation' => 'AND'
    ]
];

if ( $type ) {
    $league_args['meta_query'][] = [
        'key' => 'sport_type',
        'value' => $type
    ];
}

if ( $hide_transfers ) {
    $league_args['meta_query'][] = [
        'key' => 'hide_transfers',
        'value' => 'no',
    ];
}

$leagues = get_terms( $league_args );

if ($leagues) :

    usort( $leagues, 'sp_sort_terms' );

    if ( $title ) {
        echo '<div class="sp_block_title">';
        echo '<h2>' . wp_kses_post( $title ) . '</h2>';
        echo '</div>';
    }
    ?>

    <div class="sp_league_blocks sp_league_blocks-<?php echo count($leagues); ?>">

        <?php foreach ( $leagues as $league ) {
            $image_id = get_term_meta( $league->term_id, '_thumbnail_id', 1 );
            $league_slug = get_term_link($league) . $slug;
            if ( $image_id ) {
                $image_url = wp_get_attachment_image_url( $image_id, 'w70h70' );
                $image = '<img class="lazy lozad sp_league_block_img" src="' . Thumbnail::$lazy_preview . '" data-src="' . $image_url . '" alt="' . $league->name . '" width="40" height="40">';
            } else {
                $image = '<div class="sp_league_block_img"></div>';
            }                 

            echo '<a href="' . $league_slug . '" class="sp_league_block" title="' . $league->name . '">';
            echo $image;
            echo '<div class="sp_league_block_name">' . $league->name . '</div>';
            echo '</a>';
        } ?>

    </div>

<?php endif; ?>