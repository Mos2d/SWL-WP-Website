<?php
/**
 * Register Custom Post Types
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function sarah_loz_register_post_types() {
    // Games Post Type
    register_post_type('game', array(
        'labels' => array(
            'name' => __('Games', 'sarah-loz'),
            'singular_name' => __('Game', 'sarah-loz'),
            'menu_name' => __('Games', 'sarah-loz'),
            'name_admin_bar' => __('Game', 'sarah-loz'),
            'add_new' => __('Add New', 'sarah-loz'),
            'add_new_item' => __('Add New Game', 'sarah-loz'),
            'edit_item' => __('Edit Game', 'sarah-loz'),
            'view_item' => __('View Game', 'sarah-loz'),
            'all_items' => __('All Games', 'sarah-loz'),
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-games',
        'rewrite' => array('slug' => 'games'),
        'taxonomies' => array('topic'),
    ));

    // Activities Post Type
    register_post_type('activity', array(
        'labels' => array(
            'name' => __('Activities', 'sarah-loz'),
            'singular_name' => __('Activity', 'sarah-loz'),
            'menu_name' => __('Activities', 'sarah-loz'),
            'name_admin_bar' => __('Activity', 'sarah-loz'),
            'add_new' => __('Add New', 'sarah-loz'),
            'add_new_item' => __('Add New Activity', 'sarah-loz'),
            'edit_item' => __('Edit Activity', 'sarah-loz'),
            'view_item' => __('View Activity', 'sarah-loz'),
            'all_items' => __('All Activities', 'sarah-loz'),
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-welcome-learn-more',
        'rewrite' => array('slug' => 'activities'),
        'taxonomies' => array('topic'),
    ));

    // Videos Post Type
    register_post_type('video', array(
        'labels' => array(
            'name' => __('Videos', 'sarah-loz'),
            'singular_name' => __('Video', 'sarah-loz'),
            'menu_name' => __('Videos', 'sarah-loz'),
            'name_admin_bar' => __('Video', 'sarah-loz'),
            'add_new' => __('Add New', 'sarah-loz'),
            'add_new_item' => __('Add New Video', 'sarah-loz'),
            'edit_item' => __('Edit Video', 'sarah-loz'),
            'view_item' => __('View Video', 'sarah-loz'),
            'all_items' => __('All Videos', 'sarah-loz'),
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
        'menu_icon' => 'dashicons-video-alt3',
        'rewrite' => array('slug' => 'videos'),
        'taxonomies' => array('topic'),
    ));

    // Practices Post Type (تدريبات)
    register_post_type('practice', array(
        'labels' => array(
            'name' => __('Practices', 'sarah-loz'),
            'singular_name' => __('Practice', 'sarah-loz'),
            'menu_name' => __('تدريبات', 'sarah-loz'),
            'name_admin_bar' => __('Practice', 'sarah-loz'),
            'add_new' => __('Add New', 'sarah-loz'),
            'add_new_item' => __('Add New Practice', 'sarah-loz'),
            'edit_item' => __('Edit Practice', 'sarah-loz'),
            'view_item' => __('View Practice', 'sarah-loz'),
            'all_items' => __('All Practices', 'sarah-loz'),
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-welcome-write-blog',
        'rewrite' => array('slug' => 'practices'),
        'taxonomies' => array('topic'),
    ));

    // Theater Post Type (المسرحيات)
    register_post_type('theater', array(
        'labels' => array(
            'name' => __('المسرحيات', 'sarah-loz'),
            'singular_name' => __('مسرحية', 'sarah-loz'),
            'menu_name' => __('المسرحيات', 'sarah-loz'),
            'name_admin_bar' => __('مسرحية', 'sarah-loz'),
            'add_new' => __('إضافة جديدة', 'sarah-loz'),
            'add_new_item' => __('إضافة مسرحية جديدة', 'sarah-loz'),
            'edit_item' => __('تعديل المسرحية', 'sarah-loz'),
            'view_item' => __('عرض المسرحية', 'sarah-loz'),
            'all_items' => __('جميع المسرحيات', 'sarah-loz'),
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
        'menu_icon' => 'dashicons-admin-site-alt3',
        'rewrite' => array('slug' => 'theaters'),
        'taxonomies' => array('topic'),
    ));

    // Broadcast Post Type
    register_post_type('broadcast', array(
        'labels' => array(
            'name' => __('Broadcasts', 'sarah-loz'),
            'singular_name' => __('Broadcast', 'sarah-loz'),
            'menu_name' => __('Broadcasts', 'sarah-loz'),
            'name_admin_bar' => __('Broadcast', 'sarah-loz'),
            'add_new' => __('Add New', 'sarah-loz'),
            'add_new_item' => __('Add New Broadcast', 'sarah-loz'),
            'edit_item' => __('Edit Broadcast', 'sarah-loz'),
            'view_item' => __('View Broadcast', 'sarah-loz'),
            'all_items' => __('All Broadcasts', 'sarah-loz'),
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-megaphone',
        'rewrite' => array('slug' => 'broadcasts'),
        'taxonomies' => array('topic'),
    ));
}
add_action('init', 'sarah_loz_register_post_types');

// Move ACF fields registration to init hook with proper priority
function sarah_loz_setup_acf_fields() {
    // Don't do anything until init hook
}
add_action('after_setup_theme', 'sarah_loz_setup_acf_fields');

// Function to register ACF fields - moved to init with priority 15
function sarah_loz_register_acf_fields() {
    // Verify ACF functions are available
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    
    // Game Fields - Updated for interactive games
    acf_add_local_field_group(array(
        'key' => 'group_game_fields',
        'title' => 'Game Details',
        'fields' => array(
            array(
                'key' => 'field_game_age_range',
                'label' => 'Age Range',
                'name' => 'age_range',
                'type' => 'select',
                'choices' => array(
                    '3-5' => '3-5 years',
                    '6-7' => '6-7 years',
                    '8-9' => '8-9 years',
                ),
            ),
            array(
                'key' => 'field_game_difficulty',
                'label' => 'Difficulty Level',
                'name' => 'difficulty_level',
                'type' => 'select',
                'choices' => array(
                    'easy' => 'Easy',
                    'medium' => 'Medium',
                    'hard' => 'Hard',
                ),
            ),
            array(
                'key' => 'field_game_type',
                'label' => 'Game Type',
                'name' => 'game_type',
                'type' => 'select',
                'choices' => array(
                    'embed' => 'External Embed',
                    'interactive' => 'Interactive Game',
                ),
                'default_value' => 'embed',
                'instructions' => 'Select whether to embed an external game or create an interactive game',
            ),
            array(
                'key' => 'field_game_url',
                'label' => 'Game URL/Embed Code',
                'name' => 'game_url',
                'type' => 'textarea',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_game_type',
                            'operator' => '==',
                            'value' => 'embed',
                        ),
                    ),
                ),
                'instructions' => 'Enter external game URL or embed code',
            ),
            array(
                'key' => 'field_interactive_game_type',
                'label' => 'Interactive Game Type',
                'name' => 'interactive_game_type',
                'type' => 'select',
                'choices' => array(
                    'memory' => 'Memory Game',
                    'quiz' => 'Quiz Game',
                    'sorting' => 'Sorting Game',
                    'drag_drop' => 'Drag and Drop Game',
                    'matching' => 'Matching Game',
                    'word_search' => 'Word Search Game',
                    'audio_matching' => 'Audio Matching Game',
                    'assessment' => 'Assessment Game',
                ),
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_game_type',
                            'operator' => '==',
                            'value' => 'interactive',
                        ),
                    ),
                ),
                'instructions' => 'Select the type of interactive game',
            ),
            
            // Memory Game Settings
            array(
                'key' => 'field_memory_game_settings',
                'label' => 'Memory Game Settings',
                'name' => 'memory_game_settings',
                'type' => 'group',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_game_type',
                            'operator' => '==',
                            'value' => 'interactive',
                        ),
                        array(
                            'field' => 'field_interactive_game_type',
                            'operator' => '==',
                            'value' => 'memory',
                        ),
                    ),
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_memory_card_back',
                        'label' => 'Card Back Image',
                        'name' => 'card_back_image',
                        'type' => 'image',
                        'return_format' => 'url',
                        'preview_size' => 'thumbnail',
                    ),
                    array(
                        'key' => 'field_memory_card_pairs',
                        'label' => 'Card Pairs',
                        'name' => 'card_pairs',
                        'type' => 'repeater',
                        'layout' => 'table',
                        'button_label' => 'Add Pair',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_memory_card_image',
                                'label' => 'Card Image',
                                'name' => 'image',
                                'type' => 'image',
                                'return_format' => 'url',
                                'preview_size' => 'thumbnail',
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_memory_match_sound',
                        'label' => 'Match Sound',
                        'name' => 'match_sound',
                        'type' => 'file',
                        'return_format' => 'url',
                        'library' => 'all',
                        'mime_types' => 'mp3,wav,ogg',
                    ),
                    array(
                        'key' => 'field_memory_error_sound',
                        'label' => 'Error Sound',
                        'name' => 'error_sound',
                        'type' => 'file',
                        'return_format' => 'url',
                        'library' => 'all',
                        'mime_types' => 'mp3,wav,ogg',
                    ),
                ),
            ),
            
            // Quiz Game Settings
            array(
                'key' => 'field_quiz_game_settings',
                'label' => 'Quiz Game Settings',
                'name' => 'quiz_game_settings',
                'type' => 'group',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_game_type',
                            'operator' => '==',
                            'value' => 'interactive',
                        ),
                        array(
                            'field' => 'field_interactive_game_type',
                            'operator' => '==',
                            'value' => 'quiz',
                        ),
                    ),
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_quiz_time_per_question',
                        'label' => 'Time Per Question (seconds)',
                        'name' => 'time_per_question',
                        'type' => 'number',
                        'default_value' => 20,
                        'min' => 0,
                        'instructions' => 'Set to 0 for no time limit',
                    ),
                    array(
                        'key' => 'field_quiz_shuffle_questions',
                        'label' => 'Shuffle Questions',
                        'name' => 'shuffle_questions',
                        'type' => 'true_false',
                        'ui' => 1,
                        'default_value' => 1,
                    ),
                    array(
                        'key' => 'field_quiz_shuffle_answers',
                        'label' => 'Shuffle Answers',
                        'name' => 'shuffle_answers',
                        'type' => 'true_false',
                        'ui' => 1,
                        'default_value' => 1,
                    ),
                    array(
                        'key' => 'field_quiz_show_explanations',
                        'label' => 'Show Explanations',
                        'name' => 'show_explanations',
                        'type' => 'true_false',
                        'ui' => 1,
                        'default_value' => 1,
                    ),
                    array(
                        'key' => 'field_quiz_questions',
                        'label' => 'Questions',
                        'name' => 'questions',
                        'type' => 'repeater',
                        'layout' => 'block',
                        'button_label' => 'Add Question',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_quiz_question_text',
                                'label' => 'Question Text',
                                'name' => 'text',
                                'type' => 'textarea',
                                'rows' => 3,
                                'required' => 1,
                            ),
                            array(
                                'key' => 'field_quiz_question_image',
                                'label' => 'Question Image',
                                'name' => 'image',
                                'type' => 'image',
                                'return_format' => 'url',
                                'preview_size' => 'thumbnail',
                            ),
                            array(
                                'key' => 'field_quiz_question_difficulty',
                                'label' => 'Question Difficulty',
                                'name' => 'difficulty',
                                'type' => 'select',
                                'choices' => array(
                                    'easy' => 'Easy',
                                    'medium' => 'Medium',
                                    'hard' => 'Hard',
                                ),
                                'default_value' => 'medium',
                            ),
                            array(
                                'key' => 'field_quiz_question_explanation',
                                'label' => 'Explanation',
                                'name' => 'explanation',
                                'type' => 'textarea',
                                'rows' => 3,
                                'instructions' => 'Optional explanation to show after answering',
                            ),
                            array(
                                'key' => 'field_quiz_answers',
                                'label' => 'Answers',
                                'name' => 'answers',
                                'type' => 'repeater',
                                'layout' => 'table',
                                'button_label' => 'Add Answer',
                                'min' => 2,
                                'max' => 6,
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_quiz_answer_text',
                                        'label' => 'Answer Text',
                                        'name' => 'text',
                                        'type' => 'text',
                                        'required' => 1,
                                    ),
                                    array(
                                        'key' => 'field_quiz_answer_correct',
                                        'label' => 'Is Correct',
                                        'name' => 'correct',
                                        'type' => 'true_false',
                                        'ui' => 1,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            
            // Sorting Game Settings
            array(
                'key' => 'field_sorting_game_settings',
                'label' => 'Sorting Game Settings',
                'name' => 'sorting_game_settings',
                'type' => 'group',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_game_type',
                            'operator' => '==',
                            'value' => 'interactive',
                        ),
                        array(
                            'field' => 'field_interactive_game_type',
                            'operator' => '==',
                            'value' => 'sorting',
                        ),
                    ),
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_sorting_is_sequence',
                        'label' => 'Sequence Mode',
                        'name' => 'is_sequence',
                        'type' => 'true_false',
                        'ui' => 1,
                        'default_value' => 0,
                        'instructions' => 'Enable for ordering items in sequence, disable for categorizing items',
                    ),
                    array(
                        'key' => 'field_sorting_allow_incorrect',
                        'label' => 'Allow Incorrect Placements',
                        'name' => 'allow_incorrect_placements',
                        'type' => 'true_false',
                        'ui' => 1,
                        'default_value' => 0,
                    ),
                    array(
                        'key' => 'field_sorting_correct_sound',
                        'label' => 'Correct Placement Sound',
                        'name' => 'correct_sound',
                        'type' => 'file',
                        'return_format' => 'url',
                        'library' => 'all',
                        'mime_types' => 'mp3,wav,ogg',
                    ),
                    array(
                        'key' => 'field_sorting_error_sound',
                        'label' => 'Error Sound',
                        'name' => 'error_sound',
                        'type' => 'file',
                        'return_format' => 'url',
                        'library' => 'all',
                        'mime_types' => 'mp3,wav,ogg',
                    ),
                    // Categories (only shown when sequence mode is off)
                    array(
                        'key' => 'field_sorting_categories',
                        'label' => 'Categories',
                        'name' => 'categories',
                        'type' => 'repeater',
                        'layout' => 'table',
                        'button_label' => 'Add Category',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_sorting_is_sequence',
                                    'operator' => '!=',
                                    'value' => '1',
                                ),
                            ),
                        ),
                        'sub_fields' => array(
                            array(
                                'key' => 'field_category_id',
                                'label' => 'Category ID',
                                'name' => 'id',
                                'type' => 'text',
                                'instructions' => 'Unique identifier for this category (no spaces)',
                                'required' => 1,
                            ),
                            array(
                                'key' => 'field_category_name',
                                'label' => 'Category Name',
                                'name' => 'name',
                                'type' => 'text',
                                'required' => 1,
                            ),
                        ),
                    ),
                    // Items
                    array(
                        'key' => 'field_sorting_items',
                        'label' => 'Items',
                        'name' => 'items',
                        'type' => 'repeater',
                        'layout' => 'block',
                        'button_label' => 'Add Item',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_item_id',
                                'label' => 'Item ID',
                                'name' => 'id',
                                'type' => 'text',
                                'instructions' => 'Unique identifier for this item (no spaces)',
                                'required' => 1,
                            ),
                            array(
                                'key' => 'field_item_text',
                                'label' => 'Item Text',
                                'name' => 'text',
                                'type' => 'text',
                                'instructions' => 'Text to display on the item (if no image is used)',
                            ),
                            array(
                                'key' => 'field_item_image',
                                'label' => 'Item Image',
                                'name' => 'image',
                                'type' => 'image',
                                'return_format' => 'url',
                                'preview_size' => 'thumbnail',
                            ),
                            array(
                                'key' => 'field_item_difficulty',
                                'label' => 'Item Difficulty',
                                'name' => 'difficulty',
                                'type' => 'select',
                                'choices' => array(
                                    'easy' => 'Easy',
                                    'medium' => 'Medium',
                                    'hard' => 'Hard',
                                ),
                                'default_value' => 'medium',
                            ),
                            // Category field (only shown when sequence mode is off)
                            array(
                                'key' => 'field_item_category',
                                'label' => 'Item Category',
                                'name' => 'category',
                                'type' => 'text',
                                'instructions' => 'Enter the Category ID this item belongs to',
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_sorting_is_sequence',
                                            'operator' => '!=',
                                            'value' => '1',
                                        ),
                                    ),
                                ),
                            ),
                            // Position field (only shown when sequence mode is on)
                            array(
                                'key' => 'field_item_position',
                                'label' => 'Item Position',
                                'name' => 'position',
                                'type' => 'number',
                                'instructions' => 'Enter the position in the sequence (1, 2, 3, etc)',
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_sorting_is_sequence',
                                            'operator' => '==',
                                            'value' => '1',
                                        ),
                                    ),
                                ),
                                'min' => 1,
                                'step' => 1,
                            ),
                        ),
                    ),
                ),
            ),
            
            // Audio Matching Game Settings
            array(
                'key' => 'field_audio_game_settings',
                'label' => 'Audio Matching Game Settings',
                'name' => 'audio_game_settings',
                'type' => 'group',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_game_type',
                            'operator' => '==',
                            'value' => 'interactive',
                        ),
                        array(
                            'field' => 'field_interactive_game_type',
                            'operator' => '==',
                            'value' => 'audio_matching',
                        ),
                    ),
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_audio_game_template',
                        'label' => 'Game Template',
                        'name' => 'game_template',
                        'type' => 'select',
                        'choices' => array(
                            'colors' => 'Colors (الألوان)',
                            'animals' => 'Animals (الحيوانات)',
                            'numbers' => 'Numbers (الأرقام)',
                            'shapes' => 'Shapes (الأشكال)',
                            'fruits' => 'Fruits (الفواكه)',
                            'custom' => 'Custom (مخصص)',
                        ),
                        'default_value' => 'colors',
                        'instructions' => 'Choose a template or create custom questions',
                    ),
                    array(
                        'key' => 'field_audio_voice_instructions',
                        'label' => 'Voice Instructions',
                        'name' => 'voice_instructions',
                        'type' => 'group',
                        'layout' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_welcome_message_type',
                                'label' => 'Welcome Message Type',
                                'name' => 'welcome_message_type',
                                'type' => 'select',
                                'choices' => array(
                                    'text' => 'Text-to-Speech',
                                    'file' => 'Upload Audio File',
                                    'both' => 'Both (File + Fallback Text)',
                                ),
                                'default_value' => 'text',
                                'instructions' => 'How to deliver the welcome message',
                            ),
                            array(
                                'key' => 'field_welcome_message_text',
                                'label' => 'Welcome Message Text',
                                'name' => 'welcome_message_text',
                                'type' => 'textarea',
                                'default_value' => 'مرحباً! اضغط على الأصوات واختر الإجابة الصحيحة',
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_welcome_message_type',
                                            'operator' => '!=',
                                            'value' => 'file',
                                        ),
                                    ),
                                ),
                                'instructions' => 'Text that will be spoken when the game starts',
                            ),
                            array(
                                'key' => 'field_welcome_message_file',
                                'label' => 'Welcome Message Audio File',
                                'name' => 'welcome_message_file',
                                'type' => 'file',
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_welcome_message_type',
                                            'operator' => '!=',
                                            'value' => 'text',
                                        ),
                                    ),
                                ),
                                'return_format' => 'url',
                                'library' => 'all',
                                'mime_types' => 'mp3,wav,ogg,m4a',
                                'instructions' => 'Upload custom welcome message audio',
                            ),
                            array(
                                'key' => 'field_instruction_message_type',
                                'label' => 'Game Instructions Type',
                                'name' => 'instruction_message_type',
                                'type' => 'select',
                                'choices' => array(
                                    'text' => 'Text-to-Speech',
                                    'file' => 'Upload Audio File',
                                    'both' => 'Both (File + Fallback Text)',
                                ),
                                'default_value' => 'text',
                                'instructions' => 'How to deliver game instructions',
                            ),
                            array(
                                'key' => 'field_instruction_message_text',
                                'label' => 'Game Instructions Text',
                                'name' => 'instruction_message_text',
                                'type' => 'textarea',
                                'default_value' => 'اضغط على الزر لسماع الصوت، ثم اختر الإجابة الصحيحة من الخيارات المتاحة',
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_instruction_message_type',
                                            'operator' => '!=',
                                            'value' => 'file',
                                        ),
                                    ),
                                ),
                                'instructions' => 'Detailed instructions on how to play',
                            ),
                            array(
                                'key' => 'field_instruction_message_file',
                                'label' => 'Game Instructions Audio File',
                                'name' => 'instruction_message_file',
                                'type' => 'file',
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_instruction_message_type',
                                            'operator' => '!=',
                                            'value' => 'text',
                                        ),
                                    ),
                                ),
                                'return_format' => 'url',
                                'library' => 'all',
                                'mime_types' => 'mp3,wav,ogg,m4a',
                                'instructions' => 'Upload custom instructions audio',
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_audio_completion_messages',
                        'label' => 'Completion Messages',
                        'name' => 'completion_messages',
                        'type' => 'group',
                        'layout' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_correct_answer_type',
                                'label' => 'Correct Answer Message Type',
                                'name' => 'correct_answer_type',
                                'type' => 'select',
                                'choices' => array(
                                    'text' => 'Text-to-Speech',
                                    'file' => 'Upload Audio File',
                                    'both' => 'Both (File + Fallback Text)',
                                    'random' => 'Random from Multiple Files',
                                ),
                                'default_value' => 'text',
                            ),
                            array(
                                'key' => 'field_correct_answer_text',
                                'label' => 'Correct Answer Text',
                                'name' => 'correct_answer_text',
                                'type' => 'text',
                                'default_value' => 'أحسنت! إجابة صحيحة',
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_correct_answer_type',
                                            'operator' => '!=',
                                            'value' => 'file',
                                        ),
                                        array(
                                            'field' => 'field_correct_answer_type',
                                            'operator' => '!=',
                                            'value' => 'random',
                                        ),
                                    ),
                                ),
                            ),
                            array(
                                'key' => 'field_correct_answer_files',
                                'label' => 'Correct Answer Audio Files',
                                'name' => 'correct_answer_files',
                                'type' => 'repeater',
                                'layout' => 'table',
                                'button_label' => 'Add Audio File',
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_correct_answer_type',
                                            'operator' => '!=',
                                            'value' => 'text',
                                        ),
                                    ),
                                ),
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_correct_file',
                                        'label' => 'Audio File',
                                        'name' => 'file',
                                        'type' => 'file',
                                        'return_format' => 'url',
                                        'library' => 'all',
                                        'mime_types' => 'mp3,wav,ogg,m4a',
                                    ),
                                    array(
                                        'key' => 'field_correct_file_label',
                                        'label' => 'Label',
                                        'name' => 'label',
                                        'type' => 'text',
                                        'instructions' => 'Optional label to identify this audio file',
                                    ),
                                ),
                            ),
                            array(
                                'key' => 'field_wrong_answer_type',
                                'label' => 'Wrong Answer Message Type',
                                'name' => 'wrong_answer_type',
                                'type' => 'select',
                                'choices' => array(
                                    'text' => 'Text-to-Speech',
                                    'file' => 'Upload Audio File',
                                    'both' => 'Both (File + Fallback Text)',
                                    'random' => 'Random from Multiple Files',
                                ),
                                'default_value' => 'text',
                            ),
                            array(
                                'key' => 'field_wrong_answer_text',
                                'label' => 'Wrong Answer Text',
                                'name' => 'wrong_answer_text',
                                'type' => 'text',
                                'default_value' => 'حاول مرة أخرى',
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_wrong_answer_type',
                                            'operator' => '!=',
                                            'value' => 'file',
                                        ),
                                        array(
                                            'field' => 'field_wrong_answer_type',
                                            'operator' => '!=',
                                            'value' => 'random',
                                        ),
                                    ),
                                ),
                            ),
                            array(
                                'key' => 'field_wrong_answer_files',
                                'label' => 'Wrong Answer Audio Files',
                                'name' => 'wrong_answer_files',
                                'type' => 'repeater',
                                'layout' => 'table',
                                'button_label' => 'Add Audio File',
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_wrong_answer_type',
                                            'operator' => '!=',
                                            'value' => 'text',
                                        ),
                                    ),
                                ),
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_wrong_file',
                                        'label' => 'Audio File',
                                        'name' => 'file',
                                        'type' => 'file',
                                        'return_format' => 'url',
                                        'library' => 'all',
                                        'mime_types' => 'mp3,wav,ogg,m4a',
                                    ),
                                    array(
                                        'key' => 'field_wrong_file_label',
                                        'label' => 'Label',
                                        'name' => 'label',
                                        'type' => 'text',
                                        'instructions' => 'Optional label to identify this audio file',
                                    ),
                                ),
                            ),
                            array(
                                'key' => 'field_game_complete_type',
                                'label' => 'Game Complete Message Type',
                                'name' => 'game_complete_type',
                                'type' => 'select',
                                'choices' => array(
                                    'text' => 'Text-to-Speech',
                                    'file' => 'Upload Audio File',
                                    'both' => 'Both (File + Fallback Text)',
                                ),
                                'default_value' => 'text',
                            ),
                            array(
                                'key' => 'field_game_complete_text',
                                'label' => 'Game Complete Text',
                                'name' => 'game_complete_text',
                                'type' => 'textarea',
                                'default_value' => 'ممتاز! لقد أكملت جميع الأسئلة بنجاح! أحسنت العمل',
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_game_complete_type',
                                            'operator' => '!=',
                                            'value' => 'file',
                                        ),
                                    ),
                                ),
                            ),
                            array(
                                'key' => 'field_game_complete_file',
                                'label' => 'Game Complete Audio File',
                                'name' => 'game_complete_file',
                                'type' => 'file',
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_game_complete_type',
                                            'operator' => '!=',
                                            'value' => 'text',
                                        ),
                                    ),
                                ),
                                'return_format' => 'url',
                                'library' => 'all',
                                'mime_types' => 'mp3,wav,ogg,m4a',
                                'instructions' => 'Upload custom game completion celebration audio',
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_audio_speech_settings',
                        'label' => 'Text-to-Speech Settings',
                        'name' => 'speech_settings',
                        'type' => 'group',
                        'layout' => 'block',
                        'instructions' => 'These settings apply when using text-to-speech for any audio elements',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_speech_language',
                                'label' => 'Speech Language',
                                'name' => 'language',
                                'type' => 'select',
                                'choices' => array(
                                    'ar-SA' => 'Arabic (Saudi)',
                                    'ar-EG' => 'Arabic (Egypt)',
                                    'ar-AE' => 'Arabic (UAE)',
                                    'ar' => 'Arabic (Generic)',
                                ),
                                'default_value' => 'ar-SA',
                            ),
                            array(
                                'key' => 'field_speech_rate',
                                'label' => 'Speech Rate',
                                'name' => 'rate',
                                'type' => 'range',
                                'min' => 0.1,
                                'max' => 2.0,
                                'step' => 0.1,
                                'default_value' => 0.8,
                                'instructions' => 'Speed of speech (0.1 = very slow, 2.0 = very fast)',
                            ),
                            array(
                                'key' => 'field_speech_pitch',
                                'label' => 'Speech Pitch',
                                'name' => 'pitch',
                                'type' => 'range',
                                'min' => 0.0,
                                'max' => 2.0,
                                'step' => 0.1,
                                'default_value' => 1.2,
                                'instructions' => 'Voice pitch (0.0 = low, 2.0 = high)',
                            ),
                            array(
                                'key' => 'field_speech_volume',
                                'label' => 'Speech Volume',
                                'name' => 'volume',
                                'type' => 'range',
                                'min' => 0.0,
                                'max' => 1.0,
                                'step' => 0.1,
                                'default_value' => 1.0,
                                'instructions' => 'Volume level (0.0 = silent, 1.0 = maximum)',
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_audio_game_options',
                        'label' => 'Game Options',
                        'name' => 'game_options',
                        'type' => 'group',
                        'layout' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_shuffle_options',
                                'label' => 'Shuffle Options',
                                'name' => 'shuffle_options',
                                'type' => 'true_false',
                                'ui' => 1,
                                'default_value' => 1,
                            ),
                            array(
                                'key' => 'field_max_attempts',
                                'label' => 'Max Attempts per Question',
                                'name' => 'max_attempts',
                                'type' => 'number',
                                'min' => 1,
                                'max' => 10,
                                'default_value' => 3,
                            ),
                            array(
                                'key' => 'field_auto_play_intro',
                                'label' => 'Auto Play Intro',
                                'name' => 'auto_play_intro',
                                'type' => 'true_false',
                                'ui' => 1,
                                'default_value' => 1,
                            ),
                            array(
                                'key' => 'field_show_feedback',
                                'label' => 'Show Feedback',
                                'name' => 'show_feedback',
                                'type' => 'true_false',
                                'ui' => 1,
                                'default_value' => 1,
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_audio_questions',
                        'label' => 'Game Questions & Answers',
                        'name' => 'questions',
                        'type' => 'repeater',
                        'layout' => 'block',
                        'button_label' => 'Add Question',
                        'instructions' => 'Add your own questions or use template questions. When using templates, you can still add additional custom questions here.',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_audio_question_text',
                                'label' => 'Question Title',
                                'name' => 'text',
                                'type' => 'text',
                                'required' => 1,
                                'instructions' => 'Short title for this question (e.g., "Red Color", "Cat Sound")',
                                'placeholder' => 'Enter question title...',
                            ),
                            array(
                                'key' => 'field_audio_question_sound',
                                'label' => 'Audio Content',
                                'name' => 'sound',
                                'type' => 'group',
                                'layout' => 'block',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_audio_type',
                                        'label' => 'Audio Type',
                                        'name' => 'type',
                                        'type' => 'select',
                                        'choices' => array(
                                            'text' => 'Text-to-Speech',
                                            'file' => 'Audio File',
                                        ),
                                        'default_value' => 'text',
                                    ),
                                    array(
                                        'key' => 'field_audio_text',
                                        'label' => 'Text to Speak',
                                        'name' => 'text',
                                        'type' => 'text',
                                        'conditional_logic' => array(
                                            array(
                                                array(
                                                    'field' => 'field_audio_type',
                                                    'operator' => '==',
                                                    'value' => 'text',
                                                ),
                                            ),
                                        ),
                                        'instructions' => 'Text that will be spoken using speech synthesis',
                                        'placeholder' => 'Enter text to be spoken (e.g., "أحمر" for red color)',
                                    ),
                                    array(
                                        'key' => 'field_audio_file',
                                        'label' => 'Audio File',
                                        'name' => 'file',
                                        'type' => 'file',
                                        'conditional_logic' => array(
                                            array(
                                                array(
                                                    'field' => 'field_audio_type',
                                                    'operator' => '==',
                                                    'value' => 'file',
                                                ),
                                            ),
                                        ),
                                        'return_format' => 'url',
                                        'library' => 'all',
                                        'mime_types' => 'mp3,wav,ogg,m4a',
                                        'instructions' => 'Upload an audio file (MP3, WAV, OGG, M4A)',
                                    ),
                                ),
                            ),
                            array(
                                'key' => 'field_audio_question_type',
                                'label' => 'Question Category',
                                'name' => 'category',
                                'type' => 'select',
                                'choices' => array(
                                    'color' => 'Color',
                                    'animal' => 'Animal',
                                    'number' => 'Number',
                                    'shape' => 'Shape',
                                    'fruit' => 'Fruit',
                                    'custom' => 'Custom',
                                ),
                                'default_value' => 'custom',
                            ),
                            array(
                                'key' => 'field_audio_options',
                                'label' => 'Answer Options',
                                'name' => 'options',
                                'type' => 'repeater',
                                'layout' => 'row',
                                'button_label' => 'Add Answer Option',
                                'min' => 2,
                                'max' => 6,
                                'instructions' => 'Add 2-6 answer options. Mark exactly ONE as correct.',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_option_text',
                                        'label' => 'Answer Text',
                                        'name' => 'text',
                                        'type' => 'text',
                                        'required' => 1,
                                        'placeholder' => 'Enter answer text (e.g., "أحمر", "قطة")',
                                        'wrapper' => array(
                                            'width' => '30',
                                        ),
                                    ),
                                    array(
                                        'key' => 'field_option_correct',
                                        'label' => 'Correct?',
                                        'name' => 'correct',
                                        'type' => 'true_false',
                                        'ui' => 1,
                                        'instructions' => 'Mark this as the correct answer',
                                        'wrapper' => array(
                                            'width' => '15',
                                        ),
                                    ),
                                    array(
                                        'key' => 'field_option_color',
                                        'label' => 'Color',
                                        'name' => 'color',
                                        'type' => 'color_picker',
                                        'instructions' => 'Optional background color',
                                        'wrapper' => array(
                                            'width' => '15',
                                        ),
                                    ),
                                    array(
                                        'key' => 'field_option_emoji',
                                        'label' => 'Emoji',
                                        'name' => 'emoji',
                                        'type' => 'text',
                                        'instructions' => 'Optional emoji (🔴, 🐱)',
                                        'placeholder' => '🔴',
                                        'wrapper' => array(
                                            'width' => '15',
                                        ),
                                    ),
                                    array(
                                        'key' => 'field_option_image',
                                        'label' => 'Image',
                                        'name' => 'image',
                                        'type' => 'image',
                                        'return_format' => 'url',
                                        'preview_size' => 'thumbnail',
                                        'instructions' => 'Optional image',
                                        'wrapper' => array(
                                            'width' => '25',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),

            // Assessment Game Settings (NEW)
            array(
                'key' => 'field_assessment_settings',
                'label' => 'Assessment Exam Settings',
                'name' => 'assessment_settings',
                'type' => 'group',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_game_type',
                            'operator' => '==',
                            'value' => 'interactive',
                        ),
                        array(
                            'field' => 'field_interactive_game_type',
                            'operator' => '==',
                            'value' => 'assessment',
                        ),
                    ),
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    // 1. Global Exam Settings
                    array(
                        'key' => 'field_assess_intro_audio',
                        'label' => 'Exam Instructions Audio',
                        'name' => 'intro_audio',
                        'type' => 'file',
                        'return_format' => 'url',
                        'mime_types' => 'mp3,wav,m4a',
                        'instructions' => 'Upload the general instructions audio (e.g., "Listen and Answer")',
                    ),
                    
                    // 2. The Questions Loop
                    array(
                        'key' => 'field_assess_questions',
                        'label' => 'Exam Questions',
                        'name' => 'questions',
                        'type' => 'repeater',
                        'layout' => 'block',
                        'button_label' => 'Add Question',
                        'sub_fields' => array(
                            // Question Content
                            array(
                                'key' => 'field_q_text',
                                'label' => 'Question Text',
                                'name' => 'text',
                                'type' => 'textarea',
                                'rows' => 2,
                                'required' => 1,
                            ),
                            array(
                                'key' => 'field_q_audio',
                                'label' => 'Question Audio (Listening Task)',
                                'name' => 'audio',
                                'type' => 'file',
                                'return_format' => 'url',
                                'mime_types' => 'mp3,wav,m4a',
                                'instructions' => 'Upload the story or sentence for this question (e.g., The "My Family" text).',
                            ),
                            array(
                                'key' => 'field_q_click_audio',
                                'label' => 'Question Click Audio',
                                'name' => 'click_audio', // Key to access in JS: q.click_audio
                                'type' => 'file',
                                'return_format' => 'url',
                                'mime_types' => 'mp3,wav,m4a',
                                'instructions' => 'Audio to play when the student clicks the question text or image.',
                            ),
                            array(
                                'key' => 'field_q_image',
                                'label' => 'Question Image',
                                'name' => 'image',
                                'type' => 'image',
                                'return_format' => 'url',
                                'preview_size' => 'medium',
                                'instructions' => 'For "Describe the picture" tasks.',
                            ),
                            
                            // Scoring Criteria (Based on your Rubric)
                            array(
                                'key' => 'field_q_criteria',
                                'label' => 'Scoring Criteria',
                                'name' => 'criteria',
                                'type' => 'select',
                                'choices' => array(
                                    'listening' => 'Listening Comprehension (الاستماع)',
                                    'vocabulary' => 'Vocabulary (المفردات)',
                                    'grammar' => 'Grammar/Accuracy (الدقة اللغوية)',
                                    'fluency' => 'Fluency (الطلاقة)',
                                ),
                                'default_value' => 'listening',
                            ),

                            // Answers
                            array(
                                'key' => 'field_q_answers',
                                'label' => 'Answers',
                                'name' => 'answers',
                                'type' => 'repeater',
                                'layout' => 'table',
                                'min' => 1, 
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_a_text',
                                        'label' => 'Answer Text',
                                        'name' => 'text',
                                        'type' => 'text',
                                    ),
                                    array(
                                        'key' => 'field_a_image',
                                        'label' => 'Answer Image',
                                        'name' => 'image',
                                        'type' => 'image',
                                        'return_format' => 'url',
                                        'preview_size' => 'thumbnail',
                                        'instructions' => 'Optional: Add an image for this answer',
                                    ),
                                    array(
                                        'key' => 'field_a_audio',
                                        'label' => 'Answer Audio',
                                        'name' => 'audio', // Key to access in JS: ans.audio
                                        'type' => 'file',
                                        'return_format' => 'url',
                                        'mime_types' => 'mp3,wav,m4a',
                                        'instructions' => 'Audio to play when this answer is selected.',
                                    ),
                                    array(
                                        'key' => 'field_a_is_correct',
                                        'label' => 'Correct?',
                                        'name' => 'is_correct',
                                        'type' => 'true_false',
                                        'ui' => 1,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            
            // Game Settings (General)
            array(
                'key' => 'field_game_settings',
                'label' => 'Game Settings',
                'name' => 'game_settings',
                'type' => 'group',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_game_type',
                            'operator' => '==',
                            'value' => 'interactive',
                        ),
                    ),
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_game_width',
                        'label' => 'Game Width (px)',
                        'name' => 'width',
                        'type' => 'number',
                        'default_value' => 800,
                        'min' => 300,
                        'max' => 1200,
                    ),
                    array(
                        'key' => 'field_game_height',
                        'label' => 'Game Height (px)',
                        'name' => 'height',
                        'type' => 'number',
                        'default_value' => 600,
                        'min' => 300,
                        'max' => 1000,
                    ),
                    array(
                        'key' => 'field_game_background_color',
                        'label' => 'Background Color',
                        'name' => 'background_color',
                        'type' => 'color_picker',
                        'default_value' => '#f0f0f0',
                    ),
                    array(
                        'key' => 'field_game_show_score',
                        'label' => 'Show Score',
                        'name' => 'show_score',
                        'type' => 'true_false',
                        'ui' => 1,
                        'default_value' => 1,
                    ),
                    array(
                        'key' => 'field_game_show_timer',
                        'label' => 'Show Timer',
                        'name' => 'show_timer',
                        'type' => 'true_false',
                        'ui' => 1,
                        'default_value' => 1,
                    ),
                    array(
                        'key' => 'field_game_auto_start',
                        'label' => 'Auto Start Game',
                        'name' => 'auto_start',
                        'type' => 'true_false',
                        'ui' => 1,
                        'default_value' => 0,
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'game',
                ),
            ),
        ),
    ));

    // Activity Fields
    acf_add_local_field_group(array(
        'key' => 'group_activity_fields',
        'title' => 'Activity Details',
        'fields' => array(
            array(
                'key' => 'field_activity_age_range',
                'label' => 'Age Range',
                'name' => 'age_range',
                'type' => 'select',
                'choices' => array(
                    '3-5' => '3-5 سنوات',
                    '4-9' => '4-9 سنوات',
                    '6-7' => '6-7 سنوات',
                    '8-9' => '8-9 سنوات',
                ),
            ),
            array(
                'key' => 'field_activity_duration',
                'label' => 'Activity Duration',
                'name' => 'activity_duration',
                'type' => 'text',
                'placeholder' => '٣٠ دقيقة',
            ),
            array(
                'key' => 'field_activity_rating',
                'label' => 'Activity Rating',
                'name' => 'activity_rating',
                'type' => 'text',
                'placeholder' => '٤.٧',
                'instructions' => 'Enter the rating in Arabic numerals (e.g. ٤.٧)',
            ),
            array(
                'key' => 'field_activity_views',
                'label' => 'View Count',
                'name' => 'activity_views',
                'type' => 'number',
                'default_value' => 0,
            ),
            array(
                'key' => 'field_video_url',
                'label' => 'Video URL',
                'name' => 'video_url',
                'type' => 'url',
                'instructions' => 'Enter YouTube/Vimeo URL or direct video file URL.',
            ),
            array(
                'key' => 'field_activity_materials',
                'label' => 'Required Materials',
                'name' => 'required_materials',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Add Material',
                'sub_fields' => array(
                    array(
                        'key' => 'field_material_name',
                        'label' => 'Material Name',
                        'name' => 'name',
                        'type' => 'text',
                    ),
                ),
            ),
            array(
                'key' => 'field_materials_note',
                'label' => 'Materials Note',
                'name' => 'materials_note',
                'type' => 'textarea',
                'instructions' => 'Add a note for parents about the materials.',
            ),
            array(
                'key' => 'field_activity_steps',
                'label' => 'Activity Steps',
                'name' => 'activity_steps',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Add Step',
                'sub_fields' => array(
                    array(
                        'key' => 'field_step_title',
                        'label' => 'Step Title',
                        'name' => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_step_description',
                        'label' => 'Step Description',
                        'name' => 'description',
                        'type' => 'textarea',
                    ),
                    array(
                        'key' => 'field_step_duration',
                        'label' => 'Step Duration',
                        'name' => 'duration',
                        'type' => 'text',
                        'placeholder' => '٥ دقائق',
                    ),
                ),
            ),
            array(
                'key' => 'field_activity_benefits',
                'label' => 'Educational Benefits',
                'name' => 'educational_benefits',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Add Benefit',
                'sub_fields' => array(
                    array(
                        'key' => 'field_benefit_title',
                        'label' => 'Benefit Title',
                        'name' => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_benefit_description',
                        'label' => 'Benefit Description',
                        'name' => 'description',
                        'type' => 'textarea',
                    ),
                ),
            ),
            array(
                'key' => 'field_activity_pdf',
                'label' => 'PDF Attachments',
                'name' => 'pdf_attachments',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Add PDF',
                'sub_fields' => array(
                    array(
                        'key' => 'field_pdf_title',
                        'label' => 'PDF Title',
                        'name' => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_pdf_file',
                        'label' => 'PDF File',
                        'name' => 'file',
                        'type' => 'file',
                        'return_format' => 'array',
                        'library' => 'all',
                        'mime_types' => 'pdf',
                    ),
                ),
            ),
            array(
                'key' => 'field_activity_instructor',
                'label' => 'Workshop Instructor',
                'name' => 'instructor',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_instructor_name',
                        'label' => 'Instructor Name',
                        'name' => 'name',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_instructor_title',
                        'label' => 'Instructor Title',
                        'name' => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_instructor_bio',
                        'label' => 'Instructor Bio',
                        'name' => 'bio',
                        'type' => 'textarea',
                    ),
                    array(
                        'key' => 'field_instructor_photo',
                        'label' => 'Instructor Photo',
                        'name' => 'photo',
                        'type' => 'image',
                        'return_format' => 'array',
                        'preview_size' => 'thumbnail',
                    ),
                    array(
                        'key' => 'field_instructor_social',
                        'label' => 'Social Links',
                        'name' => 'social_links',
                        'type' => 'repeater',
                        'button_label' => 'Add Social Link',
                        'layout' => 'table',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_social_platform',
                                'label' => 'Platform',
                                'name' => 'platform',
                                'type' => 'select',
                                'choices' => array(
                                    'instagram' => 'Instagram',
                                    'youtube' => 'YouTube',
                                    'twitter' => 'Twitter',
                                    'facebook' => 'Facebook',
                                    'globe' => 'Website',
                                ),
                            ),
                            array(
                                'key' => 'field_social_url',
                                'label' => 'URL',
                                'name' => 'url',
                                'type' => 'url',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_workshop_materials',
                'label' => 'Workshop Materials for Sale',
                'name' => 'workshop_materials',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Add Product',
                'sub_fields' => array(
                    array(
                        'key' => 'field_product_id',
                        'label' => 'Select Product',
                        'name' => 'product_id',
                        'type' => 'post_object',
                        'instructions' => 'Select a WooCommerce product to display in this activity',
                        'required' => 1,
                        'post_type' => array(
                            0 => 'product',
                        ),
                        'return_format' => 'id',
                        'ui' => 1,
                    ),
                    array(
                        'key' => 'field_product_note',
                        'label' => 'Additional Note',
                        'name' => 'note',
                        'type' => 'textarea',
                        'instructions' => 'Optional additional information about this product for this specific activity',
                        'rows' => 3,
                    ),
                ),
            ),
            array(
                'key' => 'field_gallery_images',
                'label' => 'Gallery Images',
                'name' => 'gallery_images',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Add Image',
                'sub_fields' => array(
                    array(
                        'key' => 'field_gallery_title',
                        'label' => 'Image Title',
                        'name' => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_gallery_image',
                        'label' => 'Image',
                        'name' => 'image',
                        'type' => 'image',
                        'return_format' => 'array',
                        'preview_size' => 'thumbnail',
                    ),
                ),
            ),
            array(
                'key' => 'field_enable_reviews',
                'label' => 'Enable Reviews',
                'name' => 'enable_reviews',
                'type' => 'true_false',
                'ui' => 1,
            ),
            array(
                'key' => 'field_reviews',
                'label' => 'Reviews',
                'name' => 'reviews',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Add Review',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_enable_reviews',
                            'operator' => '==',
                            'value' => '1',
                        ),
                    ),
                ),
                'sub_fields' => array(
                    array(
                        'key' => 'field_review_name',
                        'label' => 'Reviewer Name',
                        'name' => 'name',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_review_rating',
                        'label' => 'Rating',
                        'name' => 'rating',
                        'type' => 'number',
                        'min' => 1,
                        'max' => 5,
                    ),
                    array(
                        'key' => 'field_review_date',
                        'label' => 'Date',
                        'name' => 'date',
                        'type' => 'text',
                        'placeholder' => 'منذ يومين',
                    ),
                    array(
                        'key' => 'field_review_content',
                        'label' => 'Review Content',
                        'name' => 'content',
                        'type' => 'textarea',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'activity',
                ),
            ),
        ),
    ));

    // Video Fields
    acf_add_local_field_group(array(
        'key' => 'group_video_fields',
        'title' => 'Video Details',
        'fields' => array(
            array(
                'key' => 'field_video_age_range',
                'label' => 'Age Range',
                'name' => 'age_range',
                'type' => 'select',
                'choices' => array(
                    '3-5' => '3-5 years',
                    '6-7' => '6-7 years',
                    '8-9' => '8-9 years',
                ),
            ),
            array(
                'key' => 'field_video_url',
                'label' => 'Video URL/Embed Code',
                'name' => 'video_url',
                'type' => 'textarea',
                'instructions' => 'Enter YouTube/Vimeo URL or paste full embed code. For YouTube, just paste the URL and the embed will be handled automatically.',
            ),
            array(
                'key' => 'field_video_duration',
                'label' => 'Video Duration',
                'name' => 'video_duration',
                'type' => 'text',
                'instructions' => 'Enter the video duration (e.g. 15:20)',
            ),
            array(
                'key' => 'field_video_views',
                'label' => 'View Count',
                'name' => 'view_count',
                'type' => 'number',
                'default_value' => 0,
            ),
            array(
                'key' => 'field_video_featured',
                'label' => 'Featured Video',
                'name' => 'is_featured',
                'type' => 'true_false',
                'ui' => 1,
            ),
            array(
                'key' => 'field_video_new',
                'label' => 'Mark as New',
                'name' => 'is_new',
                'type' => 'true_false',
                'ui' => 1,
            ),
            array(
                'key' => 'field_video_rating',
                'label' => 'Rating',
                'name' => 'rating',
                'type' => 'number',
                'min' => 0,
                'max' => 5,
                'step' => 0.1,
            ),
            array(
                'key' => 'field_video_rating_count',
                'label' => 'Rating Count',
                'name' => 'rating_count',
                'type' => 'number',
                'default_value' => 0,
            ),
            array(
                'key' => 'field_video_learning_materials',
                'label' => 'Learning Materials',
                'name' => 'learning_materials',
                'type' => 'wysiwyg',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'video',
                ),
            ),
        ),
    ));

    // Practice Fields are now defined in inc/acf-fields.php
}
// Change hook from wp_loaded to init with priority 15
add_action('init', 'sarah_loz_register_acf_fields', 15);

/**
 * User Journey Tracking System
 */

// Create database tables on theme activation
function sarah_loz_create_tracking_tables() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();
    
    // User journey tracking table
    $table_name = $wpdb->prefix . 'user_journey_tracking';
    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        post_id bigint(20) NOT NULL,
        post_type varchar(50) NOT NULL,
        access_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        access_count int(11) DEFAULT 1 NOT NULL,
        PRIMARY KEY  (id),
        KEY user_id (user_id),
        KEY post_id (post_id),
        KEY post_type (post_type)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(get_template_directory() . '/functions.php', 'sarah_loz_create_tracking_tables');

// Check if table exists and create it if not
function sarah_loz_check_tracking_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_journey_tracking';
    
    // Check if the table exists
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        sarah_loz_create_tracking_tables();
    }
}
add_action('init', 'sarah_loz_check_tracking_table');

// Track user access to single posts
function sarah_loz_track_user_journey() {
    // Only track on single post pages for our custom post types
    if (!is_singular(array('activity', 'game', 'video', 'practice', 'broadcast'))) {
        return;
    }
    
    global $wpdb, $post;
    
    // Get current user ID (0 for non-logged in users)
    $user_id = get_current_user_id();
    
    // Don't track admin users unless force tracking is enabled
    $force_tracking = isset($_GET['force_tracking']) && current_user_can('manage_options');
    if (current_user_can('manage_options') && !$force_tracking) {
        return;
    }
    
    $post_id = $post->ID;
    $post_type = $post->post_type;
    $table_name = $wpdb->prefix . 'user_journey_tracking';
    
    // Check if this user has accessed this post before
    $existing_record = $wpdb->get_row($wpdb->prepare(
        "SELECT id, access_count FROM $table_name 
        WHERE user_id = %d AND post_id = %d",
        $user_id, $post_id
    ));
    
    if ($existing_record) {
        // Update existing record
        $wpdb->update(
            $table_name,
            array(
                'access_date' => current_time('mysql'),
                'access_count' => $existing_record->access_count + 1
            ),
            array('id' => $existing_record->id),
            array('%s', '%d'),
            array('%d')
        );
    } else {
        // Create new record
        $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'post_id' => $post_id,
                'post_type' => $post_type,
                'access_date' => current_time('mysql'),
                'access_count' => 1
            ),
            array('%d', '%d', '%s', '%s', '%d')
        );
    }
    
    // Show notice if force tracking was used
    if ($force_tracking) {
        add_action('wp_footer', function() {
            echo '<div style="position:fixed; bottom:20px; right:20px; background:#007cba; color:white; padding:10px; border-radius:5px; z-index:9999;">
                ' . __('Tracking recorded for this page view.', 'sarah-loz') . '
            </div>';
        });
    }
}
add_action('wp', 'sarah_loz_track_user_journey');

// Add admin menu page for tracking data
function sarah_loz_add_tracking_admin_menu() {
    add_menu_page(
        __('User Journey Tracking', 'sarah-loz'),
        __('User Journey', 'sarah-loz'),
        'manage_options',
        'user-journey-tracking',
        'sarah_loz_tracking_admin_page',
        'dashicons-chart-area',
        30
    );
}
add_action('admin_menu', 'sarah_loz_add_tracking_admin_menu');

// Admin page callback function
function sarah_loz_tracking_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_journey_tracking';
    
    // Check if table exists
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        sarah_loz_create_tracking_tables();
        echo '<div class="notice notice-info"><p>' . __('Tracking database table created. Data will be collected as users browse the site.', 'sarah-loz') . '</p></div>';
    }
    
    // Show testing instructions
    ?>
    <div class="notice notice-info is-dismissible">
        <p><strong><?php _e('How to test the tracking system:', 'sarah-loz'); ?></strong></p>
        <ol>
            <li><?php _e('Visit any single post of type: Activity, Game, Video, Practice, or Broadcast on your site as a non-admin user', 'sarah-loz'); ?></li>
            <li><?php _e('Each visit will be tracked automatically', 'sarah-loz'); ?></li>
            <li><?php _e('Admin users are not tracked by default, but you can add <code>?force_tracking=1</code> to the URL to force tracking for testing', 'sarah-loz'); ?></li>
            <li><?php _e('Alternatively, click the "Generate Sample Data" button below when no data is available', 'sarah-loz'); ?></li>
        </ol>
    </div>
    <?php
    
    // Handle filtering
    $post_type_filter = isset($_GET['post_type_filter']) ? sanitize_text_field($_GET['post_type_filter']) : '';
    $user_filter = isset($_GET['user_filter']) ? intval($_GET['user_filter']) : 0;
    $date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : '';
    $date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : '';
    
    // Build query
    $query = "SELECT jt.*, u.display_name, p.post_title 
              FROM $table_name jt
              LEFT JOIN {$wpdb->users} u ON jt.user_id = u.ID
              LEFT JOIN {$wpdb->posts} p ON jt.post_id = p.ID
              WHERE 1=1";
    
    if ($post_type_filter) {
        $query .= $wpdb->prepare(" AND jt.post_type = %s", $post_type_filter);
    }
    
    if ($user_filter) {
        $query .= $wpdb->prepare(" AND jt.user_id = %d", $user_filter);
    }
    
    if ($date_from) {
        $query .= $wpdb->prepare(" AND jt.access_date >= %s", $date_from . ' 00:00:00');
    }
    
    if ($date_to) {
        $query .= $wpdb->prepare(" AND jt.access_date <= %s", $date_to . ' 23:59:59');
    }
    
    $query .= " ORDER BY jt.access_date DESC LIMIT 100";
    
    // Get tracking data
    $tracking_data = $wpdb->get_results($query);
    
    // Get post types for filter
    $post_types = array('activity', 'game', 'video', 'practice', 'broadcast');
    
    // Get users for filter
    $users = $wpdb->get_results("SELECT ID, display_name FROM {$wpdb->users} ORDER BY display_name");
    
    // Generate sample data if in debug mode and no data exists
    if (defined('WP_DEBUG') && WP_DEBUG && !$tracking_data) {
        sarah_loz_generate_sample_tracking_data();
        // Refresh the data
        $tracking_data = $wpdb->get_results($query);
    }
    
    ?>
    <div class="wrap">
        <h1><?php _e('User Journey Tracking', 'sarah-loz'); ?></h1>
        
        <?php if (empty($tracking_data) && !isset($_GET['generate_sample_data'])): ?>
        <div class="notice notice-warning">
            <p><?php _e('No tracking data available yet. Data will be collected as users browse your custom post types.', 'sarah-loz'); ?></p>
            <?php if (current_user_can('manage_options')): ?>
            <p>
                <a href="<?php echo esc_url(add_query_arg('generate_sample_data', '1')); ?>" class="button">
                    <?php _e('Generate Sample Data (for testing only)', 'sarah-loz'); ?>
                </a>
            </p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="tablenav top">
            <form method="get" action="">
                <input type="hidden" name="page" value="user-journey-tracking">
                
                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <!-- Post Type Filter -->
                    <div>
                        <label for="post_type_filter"><?php _e('Content Type:', 'sarah-loz'); ?></label>
                        <select name="post_type_filter" id="post_type_filter">
                            <option value=""><?php _e('All Types', 'sarah-loz'); ?></option>
                            <?php foreach ($post_types as $type) : ?>
                                <option value="<?php echo esc_attr($type); ?>" <?php selected($post_type_filter, $type); ?>>
                                    <?php echo esc_html(ucfirst($type)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- User Filter -->
                    <div>
                        <label for="user_filter"><?php _e('User:', 'sarah-loz'); ?></label>
                        <select name="user_filter" id="user_filter">
                            <option value="0"><?php _e('All Users', 'sarah-loz'); ?></option>
                            <?php foreach ($users as $user) : ?>
                                <option value="<?php echo esc_attr($user->ID); ?>" <?php selected($user_filter, $user->ID); ?>>
                                    <?php echo esc_html($user->display_name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Date Range -->
                    <div>
                        <label for="date_from"><?php _e('From:', 'sarah-loz'); ?></label>
                        <input type="date" name="date_from" id="date_from" value="<?php echo esc_attr($date_from); ?>">
                    </div>
                    
                    <div>
                        <label for="date_to"><?php _e('To:', 'sarah-loz'); ?></label>
                        <input type="date" name="date_to" id="date_to" value="<?php echo esc_attr($date_to); ?>">
                    </div>
                    
                    <div>
                        <input type="submit" class="button" value="<?php _e('Filter', 'sarah-loz'); ?>">
                        <a href="?page=user-journey-tracking" class="button"><?php _e('Reset', 'sarah-loz'); ?></a>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Dashboard Summary -->
        <div class="user-journey-dashboard" style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 20px;">
            <?php
            // Get summary statistics
            $total_views = $wpdb->get_var("SELECT SUM(access_count) FROM $table_name") ?: 0;
            $unique_visitors = $wpdb->get_var("SELECT COUNT(DISTINCT user_id) FROM $table_name") ?: 0;
            
            // Content type breakdown
            $content_breakdown = $wpdb->get_results("
                SELECT post_type, SUM(access_count) as total_views 
                FROM $table_name 
                GROUP BY post_type
                ORDER BY total_views DESC
            ");
            
            // Most viewed content
            $popular_content = $wpdb->get_results("
                SELECT jt.post_id, p.post_title, jt.post_type, SUM(jt.access_count) as total_views 
                FROM $table_name jt
                LEFT JOIN {$wpdb->posts} p ON jt.post_id = p.ID
                WHERE p.post_title IS NOT NULL
                GROUP BY jt.post_id
                ORDER BY total_views DESC
                LIMIT 5
            ");
            ?>
            
            <!-- Stats cards -->
            <div class="stat-card" style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; min-width: 200px;">
                <h3><?php _e('Total Views', 'sarah-loz'); ?></h3>
                <p style="font-size: 24px; margin: 0;"><?php echo number_format($total_views); ?></p>
            </div>
            
            <div class="stat-card" style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; min-width: 200px;">
                <h3><?php _e('Unique Visitors', 'sarah-loz'); ?></h3>
                <p style="font-size: 24px; margin: 0;"><?php echo number_format($unique_visitors); ?></p>
            </div>
            
            <!-- Content breakdown -->
            <div class="stat-card" style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; min-width: 300px;">
                <h3><?php _e('Content Type Breakdown', 'sarah-loz'); ?></h3>
                <?php if (empty($content_breakdown)): ?>
                    <p><?php _e('No data available yet.', 'sarah-loz'); ?></p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($content_breakdown as $content) : ?>
                            <li>
                                <strong><?php echo esc_html(ucfirst($content->post_type)); ?>:</strong> 
                                <?php echo number_format($content->total_views); ?> views
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            
            <!-- Popular content -->
            <div class="stat-card" style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; min-width: 400px;">
                <h3><?php _e('Most Viewed Content', 'sarah-loz'); ?></h3>
                <?php if (empty($popular_content)): ?>
                    <p><?php _e('No data available yet.', 'sarah-loz'); ?></p>
                <?php else: ?>
                    <ol>
                        <?php foreach ($popular_content as $content) : ?>
                            <li>
                                <a href="<?php echo esc_url(get_permalink($content->post_id)); ?>" target="_blank">
                                    <?php echo esc_html($content->post_title); ?>
                                </a>
                                (<?php echo esc_html(ucfirst($content->post_type)); ?>) - 
                                <?php echo number_format($content->total_views); ?> views
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Detailed data table -->
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('User', 'sarah-loz'); ?></th>
                    <th><?php _e('Content', 'sarah-loz'); ?></th>
                    <th><?php _e('Type', 'sarah-loz'); ?></th>
                    <th><?php _e('Last Access', 'sarah-loz'); ?></th>
                    <th><?php _e('Access Count', 'sarah-loz'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tracking_data)) : ?>
                    <tr>
                        <td colspan="5"><?php _e('No tracking data found.', 'sarah-loz'); ?></td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($tracking_data as $data) : ?>
                        <tr>
                            <td>
                                <?php 
                                if ($data->user_id == 0) {
                                    echo '<em>' . __('Guest User', 'sarah-loz') . '</em>';
                                } else {
                                    echo esc_html($data->display_name ?: __('Unknown User', 'sarah-loz'));
                                    echo ' (ID: ' . esc_html($data->user_id) . ')';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if (!empty($data->post_title)): ?>
                                    <a href="<?php echo esc_url(get_permalink($data->post_id)); ?>" target="_blank">
                                        <?php echo esc_html($data->post_title); ?>
                                    </a>
                                <?php else: ?>
                                    <em><?php printf(__('Post ID: %d (no longer exists)', 'sarah-loz'), $data->post_id); ?></em>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html(ucfirst($data->post_type)); ?></td>
                            <td><?php echo esc_html(get_date_from_gmt($data->access_date, get_option('date_format') . ' ' . get_option('time_format'))); ?></td>
                            <td><?php echo esc_html($data->access_count); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Generate sample tracking data for testing
function sarah_loz_generate_sample_tracking_data() {
    if (!isset($_GET['generate_sample_data']) || !current_user_can('manage_options')) {
        return;
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_journey_tracking';
    
    // Clear existing data
    $wpdb->query("TRUNCATE TABLE $table_name");
    
    // Get users (only if needed)
    $users = get_users(['fields' => ['ID']]);
    $user_ids = [];
    foreach ($users as $user) {
        $user_ids[] = $user->ID;
    }
    // Add guest user
    $user_ids[] = 0;
    
    // Get some posts from our custom post types
    $post_types = ['activity', 'game', 'video', 'practice', 'broadcast'];
    $sample_posts = [];
    
    foreach ($post_types as $post_type) {
        $args = [
            'post_type' => $post_type,
            'posts_per_page' => 5,
            'post_status' => 'publish',
        ];
        
        $query = new WP_Query($args);
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $sample_posts[] = [
                    'id' => get_the_ID(),
                    'type' => $post_type
                ];
            }
        }
        wp_reset_postdata();
    }
    
    // If no posts found, create sample data with dummy posts
    if (empty($sample_posts)) {
        // Create a few sample posts for each type if none exist
        foreach ($post_types as $post_type) {
            for ($i = 1; $i <= 3; $i++) {
                $post_id = wp_insert_post([
                    'post_title' => sprintf('Sample %s %d', ucfirst($post_type), $i),
                    'post_type' => $post_type,
                    'post_status' => 'publish',
                ]);
                
                if (!is_wp_error($post_id)) {
                    $sample_posts[] = [
                        'id' => $post_id,
                        'type' => $post_type
                    ];
                }
            }
        }
    }
    
    // Generate sample data
    if (!empty($sample_posts) && !empty($user_ids)) {
        $now = current_time('mysql');
        $one_day = 24 * 60 * 60;
        
        foreach ($sample_posts as $post) {
            // Random number of views per post (between 1 and 20)
            $view_count = rand(1, 20);
            
            for ($i = 0; $i < $view_count; $i++) {
                // Random user
                $user_id = $user_ids[array_rand($user_ids)];
                
                // Random date in the last 30 days
                $random_time = rand(0, 30 * $one_day);
                $access_date = date('Y-m-d H:i:s', strtotime($now) - $random_time);
                
                // Random access count between 1 and 5
                $access_count = rand(1, 5);
                
                // Insert record
                $wpdb->insert(
                    $table_name,
                    [
                        'user_id' => $user_id,
                        'post_id' => $post['id'],
                        'post_type' => $post['type'],
                        'access_date' => $access_date,
                        'access_count' => $access_count
                    ],
                    ['%d', '%d', '%s', '%s', '%d']
                );
            }
        }
    }
    
    // Redirect to remove the query parameter
    wp_redirect(remove_query_arg('generate_sample_data'));
    exit;
}
add_action('admin_init', 'sarah_loz_generate_sample_tracking_data');

// Export data button and functionality
function sarah_loz_export_tracking_data() {
    if (isset($_GET['page']) && $_GET['page'] === 'user-journey-tracking' && isset($_GET['export']) && $_GET['export'] === 'csv') {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'sarah-loz'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'user_journey_tracking';
        
        // Build query similar to admin page but for export
        $query = "SELECT jt.*, u.display_name, u.user_email, p.post_title 
                  FROM $table_name jt
                  LEFT JOIN {$wpdb->users} u ON jt.user_id = u.ID
                  LEFT JOIN {$wpdb->posts} p ON jt.post_id = p.ID
                  ORDER BY jt.access_date DESC";
        
        $tracking_data = $wpdb->get_results($query);
        
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=user-journey-export-' . date('Y-m-d') . '.csv');
        
        // Create CSV output stream
        $output = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($output, array(
            'User ID',
            'User Name',
            'User Email',
            'Content ID',
            'Content Title',
            'Content Type',
            'Last Access Date',
            'Access Count'
        ));
        
        // Add data rows
        foreach ($tracking_data as $data) {
            $user_name = ($data->user_id === '0') ? 'Guest User' : ($data->display_name ?: 'Unknown User');
            $user_email = ($data->user_id === '0') ? 'N/A' : ($data->user_email ?: 'N/A');
            
            fputcsv($output, array(
                $data->user_id,
                $user_name,
                $user_email,
                $data->post_id,
                $data->post_title,
                ucfirst($data->post_type),
                get_date_from_gmt($data->access_date, 'Y-m-d H:i:s'),
                $data->access_count
            ));
        }
        
        exit;
    }
}
add_action('admin_init', 'sarah_loz_export_tracking_data');

// Add export button to admin page
function sarah_loz_add_export_button() {
    if (isset($_GET['page']) && $_GET['page'] === 'user-journey-tracking') {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('.wrap h1').first().append(' <a href="<?php echo esc_url(add_query_arg('export', 'csv')); ?>" class="page-title-action"><?php _e('Export CSV', 'sarah-loz'); ?></a>');
            });
        </script>
        <?php
    }
}
add_action('admin_footer', 'sarah_loz_add_export_button');
