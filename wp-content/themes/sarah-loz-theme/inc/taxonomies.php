<?php
/**
 * Register Custom Taxonomies
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function sarah_loz_register_taxonomies() {
    // Age Groups Taxonomy
    register_taxonomy('age_group', array('game', 'activity', 'video', 'product', 'theater'), array(
        'labels' => array(
            'name' => __('Age Groups', 'sarah-loz'),
            'singular_name' => __('Age Group', 'sarah-loz'),
            'menu_name' => __('Age Groups', 'sarah-loz'),
            'all_items' => __('All Age Groups', 'sarah-loz'),
            'edit_item' => __('Edit Age Group', 'sarah-loz'),
            'add_new_item' => __('Add New Age Group', 'sarah-loz'),
        ),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'age-group'),
    ));

    // Educational Skills Taxonomy
    register_taxonomy('educational_skill', array('game', 'activity', 'video', 'theater'), array(
        'labels' => array(
            'name' => __('Educational Skills', 'sarah-loz'),
            'singular_name' => __('Educational Skill', 'sarah-loz'),
            'menu_name' => __('Educational Skills', 'sarah-loz'),
            'all_items' => __('All Educational Skills', 'sarah-loz'),
            'edit_item' => __('Edit Educational Skill', 'sarah-loz'),
            'add_new_item' => __('Add New Educational Skill', 'sarah-loz'),
        ),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'skill'),
    ));

    // Game Categories Taxonomy
    register_taxonomy('game_category', 'game', array(
        'labels' => array(
            'name' => __('Game Categories', 'sarah-loz'),
            'singular_name' => __('Game Category', 'sarah-loz'),
            'menu_name' => __('Game Categories', 'sarah-loz'),
            'all_items' => __('All Game Categories', 'sarah-loz'),
            'edit_item' => __('Edit Game Category', 'sarah-loz'),
            'add_new_item' => __('Add New Game Category', 'sarah-loz'),
        ),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'game-category'),
    ));

    // Activity Categories Taxonomy
    register_taxonomy('activity_category', 'activity', array(
        'labels' => array(
            'name' => __('Activity Categories', 'sarah-loz'),
            'singular_name' => __('Activity Category', 'sarah-loz'),
            'menu_name' => __('Activity Categories', 'sarah-loz'),
            'all_items' => __('All Activity Categories', 'sarah-loz'),
            'edit_item' => __('Edit Activity Category', 'sarah-loz'),
            'add_new_item' => __('Add New Activity Category', 'sarah-loz'),
        ),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'activity-category'),
    ));

    // Video Categories Taxonomy
    register_taxonomy('video_category', 'video', array(
        'labels' => array(
            'name' => __('Video Categories', 'sarah-loz'),
            'singular_name' => __('Video Category', 'sarah-loz'),
            'menu_name' => __('Video Categories', 'sarah-loz'),
            'all_items' => __('All Video Categories', 'sarah-loz'),
            'edit_item' => __('Edit Video Category', 'sarah-loz'),
            'add_new_item' => __('Add New Video Category', 'sarah-loz'),
        ),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'video-category'),
    ));

    // Theater Categories Taxonomy
    register_taxonomy('theater_category', 'theater', array(
        'labels' => array(
            'name' => __('Theater Categories', 'sarah-loz'),
            'singular_name' => __('Theater Category', 'sarah-loz'),
            'menu_name' => __('Theater Categories', 'sarah-loz'),
            'all_items' => __('All Theater Categories', 'sarah-loz'),
            'edit_item' => __('Edit Theater Category', 'sarah-loz'),
            'add_new_item' => __('Add New Theater Category', 'sarah-loz'),
        ),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'theater-category'),
    ));

    // Practice Categories Taxonomy
    register_taxonomy('practice_category', 'practice', array(
        'labels' => array(
            'name' => __('Practice Categories', 'sarah-loz'),
            'singular_name' => __('Practice Category', 'sarah-loz'),
            'menu_name' => __('Practice Categories', 'sarah-loz'),
            'all_items' => __('All Practice Categories', 'sarah-loz'),
            'edit_item' => __('Edit Practice Category', 'sarah-loz'),
            'add_new_item' => __('Add New Practice Category', 'sarah-loz'),
        ),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'practice-category'),
    ));

    // Also add the practice post type to the age_group and educational_skill taxonomies
    $taxonomy = get_taxonomy('age_group');
    if ($taxonomy) {
        $taxonomy->object_type[] = 'practice';
        register_taxonomy('age_group', $taxonomy->object_type, (array) $taxonomy);
    }
    
    $taxonomy = get_taxonomy('educational_skill');
    if ($taxonomy) {
        $taxonomy->object_type[] = 'practice';
        register_taxonomy('educational_skill', $taxonomy->object_type, (array) $taxonomy);
    }

    // Topics Taxonomy - Main subject classification for all content types
    register_taxonomy('topic', array('game', 'activity', 'video', 'theater', 'practice', 'broadcast', 'product'), array(
        'labels' => array(
            'name' => __('الموضوعات', 'sarah-loz'),
            'singular_name' => __('الموضوع', 'sarah-loz'),
            'menu_name' => __('الموضوعات', 'sarah-loz'),
            'all_items' => __('جميع الموضوعات', 'sarah-loz'),
            'edit_item' => __('تعديل الموضوع', 'sarah-loz'),
            'add_new_item' => __('إضافة موضوع جديد', 'sarah-loz'),
            'new_item_name' => __('اسم الموضوع الجديد', 'sarah-loz'),
            'search_items' => __('البحث في الموضوعات', 'sarah-loz'),
            'popular_items' => __('الموضوعات الشائعة', 'sarah-loz'),
            'separate_items_with_commas' => __('افصل الموضوعات بفواصل', 'sarah-loz'),
            'add_or_remove_items' => __('إضافة أو إزالة موضوعات', 'sarah-loz'),
            'choose_from_most_used' => __('اختر من الموضوعات الأكثر استخداماً', 'sarah-loz'),
        ),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'topic'),
        'description' => __('الموضوعات الرئيسية التي تصنف المحتوى التعليمي', 'sarah-loz'),
    ));
}
add_action('init', 'sarah_loz_register_taxonomies');
