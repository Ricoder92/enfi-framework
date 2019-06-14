<?php

################################################################################################################################################## 
### Cover WIP!!!!!!!!!!!!!!!!!!!!!!!!!!!
##################################################################################################################################################

$cover = new EF_Settings_Page('ef-cover', __('COVER', 'ef'), __('COVER', 'ef'), __('COVER_DESCRIPTION', 'ef'), 'layout', '', 7);

$cover->addSection('settings', __('COVER_SETTINGS', 'ef'));
$cover->addField('settings', 'enable-post-types', __('POST_TYPES', 'ef'), null, 'checkbox-group', null, array( 'post_types' => get_post_types()));

$post_types = ef_get_option('ef-cover');

if(array_key_exists('enable-post-types', $post_types)) {

    foreach($post_types['enable-post-types'] as $post_type) {
        $_cpt = get_post_type_object($post_type);
    
        $cover->addSection($post_type, __($_cpt->labels->name));
        $cover->addField($post_type, $post_type.'-bg-color', __('COVER_BACKGROUND_COLOR', 'ef'), __('COVER_BACKGROUND_COLOR', 'ef'), 'color-picker', null);
        $cover->addField($post_type, $post_type.'-bg-image', __('COVER_BACKGROUND_IMAGE', 'ef'), __('COVER_BACKGROUND_IMAGE', 'ef'), 'image', null);
    }
}

$cover->setDefaultValues();

function ef_cover_get_title() {

    if( is_single() || is_page()) 
        return get_the_title();

    if(is_home()) {
        $_cpt = get_post_type_object('post');
        return $_cpt->labels->name;
    }

    if(is_archive()) {
        return post_type_archive_title();
    }

    if(is_tax()) {
        global $wp_query;
        $term = $wp_query->get_queried_object();
        return $term->name;
    }

}

function ef_cover_get_bg_image() {
    $image = ef_get_option('ef-cover');

    if(is_single()) {
        $post_type = 'post';
    }

    if(is_page()) {
        $post_type = 'page';
    }

    if(is_home()) {
        $post_type = 'post';
    }

    if(is_archive() & !is_tax()) {
        $post_type = get_query_var( 'post_type' );
    }

    if(is_tax()) {
        $post_type = get_query_var( 'taxonomy' );
    }

    return $image[$post_type.'-bg-image'];
}

function ef_cover_get_bg_color() {
    $image = ef_get_option('ef-cover');

    if(is_single()) {
        $post_type = 'post';
    }

    if(is_page()) {
        $post_type = 'page';
    }

    if(is_home()) {
        $post_type = 'post';
    }

    if(is_archive()) {
        $post_type = get_query_var( 'post_type' );
    }

    return $image[$post_type.'-bg-color'];
}
?>