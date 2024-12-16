<?php

function bonuses_add_custom_box()
{
    $screens = ['bonuses'];
    foreach ($screens as $screen) {
        add_meta_box(
            'bonuses_box_id',
            'Информация',
            'bonuses_custom_box_html',
            $screen
        );
    }
}
add_action('add_meta_boxes', 'bonuses_add_custom_box');