<?php
/**
 * ACF Field Registrations for Practices
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Helper function to get age group choices for ACF fields
 */
function sarah_loz_get_age_group_choices() {
    $choices = array('all' => 'جميع الأعمار (All Ages)');
    
    // Get age groups from settings
    if (function_exists('sarah_loz_get_age_groups')) {
        $age_groups = sarah_loz_get_age_groups();
        foreach ($age_groups as $range => $group_data) {
            // Use the custom name if available, otherwise use the label
            $display_name = '';
            
            // Check for custom name first
            if (!empty($group_data['custom_name'])) {
                $display_name = $group_data['custom_name'];
            } elseif (!empty($group_data['label'])) {
                $display_name = $group_data['label'];
            } else {
                // Ultimate fallback
                $display_name = 'مجموعة عمر ' . $range;
            }
            
            $choices[$range] = $display_name . ' (' . $range . ' سنوات)';
        }
    } else {
        // Fallback to default choices if function not available
        $choices['3-5'] = 'الأصدقاء الصغار (3-5 سنوات)';
        $choices['6-7'] = 'المستكشفون (6-7 سنوات)';
        $choices['8-9'] = 'الأبطال المتقدمون (8-9 سنوات)';
    }
    
    return $choices;
}

class Sarah_Loz_ACF_Fields {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->add_hooks();
    }

    private function add_hooks() {
        add_action('init', array($this, 'register_practice_fields'), 15);
        add_action('init', array($this, 'register_game_fields'), 15);
        add_action('init', array($this, 'register_theater_fields'), 15);
        add_action('init', array($this, 'register_common_age_fields'), 15);
        add_action('init', array($this, 'register_vocabulary_fields'), 15);
        
        // Add filter to load age group choices dynamically
        add_filter('acf/load_field/name=age_range', array($this, 'load_age_group_choices'));
        add_filter('acf/load_field/name=broadcast_age_range', array($this, 'load_age_group_choices'));
    }

    /**
     * Register common age range fields for all post types
     */
    public function register_common_age_fields() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        // Age Range field for all content types
        acf_add_local_field_group(array(
            'key' => 'group_age_range_fields',
            'title' => 'Age Group Settings',
            'fields' => array(
                array(
                    'key' => 'field_age_range',
                    'label' => 'Suitable Age Group',
                    'name' => 'age_range',
                    'type' => 'select',
                    'instructions' => 'Select the age group this content is suitable for',
                    'choices' => sarah_loz_get_age_group_choices(),
                    'default_value' => 'all',
                    'allow_null' => 0,
                    'multiple' => 0,
                    'ui' => 1,
                    'return_format' => 'value',
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
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'activity',
                    ),
                ),
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'video',
                    ),
                ),
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'practice',
                    ),
                ),
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'product',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'side',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        ));
    }

    /**
     * Register fields for practice post type
     */
    public function register_practice_fields() {
        // Check if ACF is active
        if (!function_exists('acf_add_local_field_group') || !class_exists('ACF')) {
            return;
        }

        // Multiple Choice Practice Fields
        acf_add_local_field_group(array(
            'key' => 'group_practices_multiple_choice',
            'title' => 'Multiple Choice Practice Settings',
            'fields' => array(
                array(
                    'key' => 'field_practice_type',
                    'label' => 'Practice Type',
                    'name' => 'practice_type',
                    'type' => 'select',
                    'instructions' => 'Select the type of practice',
                    'required' => 1,
                    'choices' => array(
                        'multiple_choice' => 'Multiple Choice',
                        'matching' => 'Matching',
                    ),
                    'default_value' => 'multiple_choice',
                ),
                array(
                    'key' => 'field_related_content',
                    'label' => 'Related Content',
                    'name' => 'related_content',
                    'type' => 'post_object',
                    'instructions' => 'Select a video or activity that must be completed before this practice',
                    'post_type' => array(
                        0 => 'video',
                        1 => 'activity',
                    ),
                    'return_format' => 'id',
                ),
                array(
                    'key' => 'field_difficulty_level',
                    'label' => 'Difficulty Level',
                    'name' => 'difficulty_level',
                    'type' => 'select',
                    'instructions' => 'Select the difficulty level of this practice',
                    'choices' => array(
                        'easy' => 'Easy',
                        'medium' => 'Medium',
                        'hard' => 'Hard',
                    ),
                    'default_value' => 'easy',
                ),
                array(
                    'key' => 'field_points',
                    'label' => 'Points',
                    'name' => 'points',
                    'type' => 'number',
                    'instructions' => 'Points awarded for completing this practice (adjusted by score percentage)',
                    'default_value' => 10,
                    'min' => 1,
                    'max' => 100,
                ),
                array(
                    'key' => 'field_questions',
                    'label' => 'Questions',
                    'name' => 'questions',
                    'type' => 'repeater',
                    'instructions' => 'Add questions for multiple choice practice',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_practice_type',
                                'operator' => '==',
                                'value' => 'multiple_choice',
                            ),
                        ),
                    ),
                    'min' => 1,
                    'layout' => 'block',
                    'button_label' => 'Add Question',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_question_text',
                            'label' => 'Question Text',
                            'name' => 'question_text',
                            'type' => 'text',
                            'required' => 1,
                        ),
                        array(
                            'key' => 'field_question_image',
                            'label' => 'Question Image',
                            'name' => 'question_image',
                            'type' => 'image',
                            'instructions' => 'Optional: Add an image for this question',
                            'return_format' => 'id',
                            'preview_size' => 'medium',
                        ),
                        array(
                            'key' => 'field_question_audio',
                            'label' => 'Question Audio',
                            'name' => 'question_audio',
                            'type' => 'file',
                            'instructions' => 'Optional: Add an audio file for this question',
                            'return_format' => 'id',
                            'mime_types' => 'mp3,wav,ogg',
                        ),
                        array(
                            'key' => 'field_answers',
                            'label' => 'Answers',
                            'name' => 'answers',
                            'type' => 'repeater',
                            'required' => 1,
                            'min' => 2,
                            'max' => 4,
                            'layout' => 'block',
                            'button_label' => 'Add Answer',
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_answer_text',
                                    'label' => 'Answer Text',
                                    'name' => 'answer_text',
                                    'type' => 'text',
                                    'instructions' => 'Text for this answer option',
                                ),
                                array(
                                    'key' => 'field_answer_image',
                                    'label' => 'Answer Image',
                                    'name' => 'answer_image',
                                    'type' => 'image',
                                    'instructions' => 'Optional: Add an image for this answer',
                                    'return_format' => 'id',
                                    'preview_size' => 'thumbnail',
                                ),
                                array(
                                    'key' => 'field_answer_audio',
                                    'label' => 'Answer Audio',
                                    'name' => 'answer_audio',
                                    'type' => 'file',
                                    'instructions' => 'Optional: Add an audio file for this answer option',
                                    'return_format' => 'id',
                                    'mime_types' => 'mp3,wav,ogg',
                                ),
                                array(
                                    'key' => 'field_is_correct',
                                    'label' => 'Correct Answer',
                                    'name' => 'is_correct',
                                    'type' => 'true_false',
                                    'instructions' => 'Is this the correct answer?',
                                    'ui' => 1,
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'key' => 'field_matching_pairs',
                    'label' => 'Matching Pairs',
                    'name' => 'matching_pairs',
                    'type' => 'repeater',
                    'instructions' => 'Add matching pairs for matching practice',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_practice_type',
                                'operator' => '==',
                                'value' => 'matching',
                            ),
                        ),
                    ),
                    'min' => 2,
                    'max' => 8,
                    'layout' => 'block',
                    'button_label' => 'Add Matching Pair',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_item_left',
                            'label' => 'Left Item',
                            'name' => 'item_left',
                            'type' => 'group',
                            'layout' => 'block',
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_left_text',
                                    'label' => 'Text',
                                    'name' => 'text',
                                    'type' => 'text',
                                    'instructions' => 'Text for left item (optional if image is provided)',
                                ),
                                array(
                                    'key' => 'field_left_image',
                                    'label' => 'Image',
                                    'name' => 'image',
                                    'type' => 'image',
                                    'instructions' => 'Image for left item (optional if text is provided)',
                                    'return_format' => 'id',
                                    'preview_size' => 'thumbnail',
                                ),
                                array(
                                    'key' => 'field_left_audio',
                                    'label' => 'Audio',
                                    'name' => 'audio',
                                    'type' => 'file',
                                    'instructions' => 'Audio for left item (optional)',
                                    'return_format' => 'id',
                                    'mime_types' => 'mp3,wav,ogg',
                                ),
                            ),
                        ),
                        array(
                            'key' => 'field_item_right',
                            'label' => 'Right Item',
                            'name' => 'item_right',
                            'type' => 'group',
                            'layout' => 'block',
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_right_text',
                                    'label' => 'Text',
                                    'name' => 'text',
                                    'type' => 'text',
                                    'instructions' => 'Text for right item (optional if image is provided)',
                                ),
                                array(
                                    'key' => 'field_right_image',
                                    'label' => 'Image',
                                    'name' => 'image',
                                    'type' => 'image',
                                    'instructions' => 'Image for right item (optional if text is provided)',
                                    'return_format' => 'id',
                                    'preview_size' => 'thumbnail',
                                ),
                                array(
                                    'key' => 'field_right_audio',
                                    'label' => 'Audio',
                                    'name' => 'audio',
                                    'type' => 'file',
                                    'instructions' => 'Audio for right item (optional)',
                                    'return_format' => 'id',
                                    'mime_types' => 'mp3,wav,ogg',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'practice',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        ));
    }
    
    /**
     * Register theater-specific ACF fields
     */
    public function register_theater_fields() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group(array(
            'key' => 'group_theater_fields',
            'title' => 'Theater Settings',
            'fields' => array(
                array(
                    'key' => 'field_theater_type',
                    'label' => 'نوع المسرحية',
                    'name' => 'theater_type',
                    'type' => 'select',
                    'instructions' => 'اختر نوع المسرحية',
                    'choices' => array(
                        'puppet' => 'مسرحية عرائس',
                        'musical' => 'مسرحية موسيقية',
                        'drama' => 'مسرحية درامية',
                        'comedy' => 'مسرحية كوميدية',
                        'educational' => 'مسرحية تعليمية',
                        'story' => 'مسرحية قصصية',
                        'interactive' => 'مسرحية تفاعلية',
                    ),
                    'default_value' => 'story',
                    'allow_null' => 0,
                    'multiple' => 0,
                    'ui' => 1,
                    'return_format' => 'value',
                ),
                array(
                    'key' => 'field_theater_duration',
                    'label' => 'مدة المسرحية',
                    'name' => 'theater_duration',
                    'type' => 'text',
                    'instructions' => 'أدخل مدة المسرحية (مثال: 15:30)',
                    'placeholder' => '15:30',
                    'default_value' => '00:00',
                ),
                array(
                    'key' => 'field_director',
                    'label' => 'المخرج',
                    'name' => 'director',
                    'type' => 'text',
                    'instructions' => 'اسم مخرج المسرحية',
                ),
                array(
                    'key' => 'field_cast_info',
                    'label' => 'طاقم التمثيل',
                    'name' => 'cast_info',
                    'type' => 'textarea',
                    'instructions' => 'أسماء الممثلين أو معلومات طاقم التمثيل',
                    'rows' => 3,
                ),
                array(
                    'key' => 'field_theater_embed',
                    'label' => 'محتوى المسرحية المدمج',
                    'name' => 'theater_embed',
                    'type' => 'textarea',
                    'instructions' => 'أدخل كود HTML للمسرحية (iframe أو video)',
                    'rows' => 4,
                ),
                array(
                    'key' => 'field_is_featured',
                    'label' => 'مسرحية مميزة',
                    'name' => 'is_featured',
                    'type' => 'true_false',
                    'instructions' => 'حدد إذا كانت هذه مسرحية مميزة',
                    'default_value' => 0,
                    'ui' => 1,
                ),
                array(
                    'key' => 'field_view_count',
                    'label' => 'عدد المشاهدات',
                    'name' => 'view_count',
                    'type' => 'number',
                    'instructions' => 'عدد مرات مشاهدة المسرحية',
                    'default_value' => 0,
                    'readonly' => 1,
                ),
                array(
                    'key' => 'field_rating',
                    'label' => 'التقييم',
                    'name' => 'rating',
                    'type' => 'number',
                    'instructions' => 'متوسط تقييم المسرحية (0-5)',
                    'default_value' => 0,
                    'min' => 0,
                    'max' => 5,
                    'step' => 0.1,
                    'readonly' => 1,
                ),
                array(
                    'key' => 'field_rating_count',
                    'label' => 'عدد التقييمات',
                    'name' => 'rating_count',
                    'type' => 'number',
                    'instructions' => 'عدد الأشخاص الذين قيموا المسرحية',
                    'default_value' => 0,
                    'readonly' => 1,
                ),
                array(
                    'key' => 'field_learning_materials',
                    'label' => 'المواد التعليمية',
                    'name' => 'learning_materials',
                    'type' => 'wysiwyg',
                    'instructions' => 'مواد تعليمية إضافية متعلقة بالمسرحية',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'theater',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => 'حقول خاصة بالمسرحيات',
        ));
    }

    /**
     * Register fields for games
     */
    public function register_game_fields() {
        // Check if ACF is active
        if (!function_exists('acf_add_local_field_group') || !class_exists('ACF')) {
            return;
        }
        
        // Memory Game Settings
        acf_add_local_field_group(array(
            'key' => 'group_memory_game_settings',
            'title' => 'Memory Game Settings',
            'fields' => array(
                array(
                    'key' => 'field_memory_game_difficulty',
                    'label' => 'Difficulty',
                    'name' => 'memory_game_difficulty',
                    'type' => 'select',
                    'instructions' => 'Select the difficulty level of the memory game',
                    'required' => 1,
                    'choices' => array(
                        'easy' => 'Easy (2x2 grid)',
                        'medium' => 'Medium (4x4 grid)',
                        'hard' => 'Hard (6x6 grid)'
                    ),
                    'default_value' => 'medium',
                ),
                array(
                    'key' => 'field_memory_game_card_images',
                    'label' => 'Card Images',
                    'name' => 'memory_game_card_images',
                    'type' => 'gallery',
                    'instructions' => 'Upload images for the memory game cards. You need at least as many images as there are pairs in the grid.',
                    'min' => 1,
                    'max' => 18,
                    'return_format' => 'url',
                ),
                array(
                    'key' => 'field_memory_game_card_back',
                    'label' => 'Card Back Image',
                    'name' => 'memory_game_card_back',
                    'type' => 'image',
                    'instructions' => 'Upload an image for the back of the cards',
                    'return_format' => 'url',
                ),
                array(
                    'key' => 'field_memory_game_sounds',
                    'label' => 'Game Sounds',
                    'name' => 'memory_game_sounds',
                    'type' => 'group',
                    'layout' => 'block',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_memory_game_match_sound',
                            'label' => 'Match Sound',
                            'name' => 'match_sound',
                            'type' => 'file',
                            'instructions' => 'Upload a sound to play when a match is found',
                            'return_format' => 'url',
                            'mime_types' => 'mp3,wav,ogg',
                        ),
                        array(
                            'key' => 'field_memory_game_error_sound',
                            'label' => 'Error Sound',
                            'name' => 'error_sound',
                            'type' => 'file',
                            'instructions' => 'Upload a sound to play when no match is found',
                            'return_format' => 'url',
                            'mime_types' => 'mp3,wav,ogg',
                        ),
                    ),
                ),
                array(
                    'key' => 'field_memory_game_responsive',
                    'label' => 'Mobile Responsiveness',
                    'name' => 'memory_game_responsive',
                    'type' => 'group',
                    'layout' => 'block',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_memory_game_mobile_responsive',
                            'label' => 'Enable Mobile Responsiveness',
                            'name' => 'enable_mobile_responsive',
                            'type' => 'true_false',
                            'instructions' => 'Enable automatic adjustments for mobile devices',
                            'default_value' => 1,
                            'ui' => 1,
                        ),
                        array(
                            'key' => 'field_memory_game_adaptive_grid',
                            'label' => 'Adaptive Grid Size',
                            'name' => 'adaptive_grid_size',
                            'type' => 'true_false',
                            'instructions' => 'Automatically adjust grid size based on screen dimensions',
                            'default_value' => 1,
                            'ui' => 1,
                            'conditional_logic' => array(
                                array(
                                    array(
                                        'field' => 'field_memory_game_mobile_responsive',
                                        'operator' => '==',
                                        'value' => 1,
                                    ),
                                ),
                            ),
                        ),
                        array(
                            'key' => 'field_memory_game_min_mobile_width',
                            'label' => 'Minimum Mobile Width',
                            'name' => 'min_mobile_width',
                            'type' => 'number',
                            'instructions' => 'Minimum width in pixels for the game on mobile devices',
                            'default_value' => 320,
                            'min' => 200,
                            'max' => 500,
                            'conditional_logic' => array(
                                array(
                                    array(
                                        'field' => 'field_memory_game_mobile_responsive',
                                        'operator' => '==',
                                        'value' => 1,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'key' => 'field_memory_game_animation',
                    'label' => 'Animation Settings',
                    'name' => 'memory_game_animation',
                    'type' => 'group',
                    'layout' => 'block',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_memory_game_flip_duration',
                            'label' => 'Card Flip Duration',
                            'name' => 'card_flip_duration',
                            'type' => 'number',
                            'instructions' => 'Duration of card flip animation in milliseconds',
                            'default_value' => 500,
                            'min' => 100,
                            'max' => 2000,
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
                    array(
                        'param' => 'post_meta',
                        'operator' => '==',
                        'value' => 'memory',
                        'meta_key' => 'interactive_game_type',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        ));
    }

    /**
     * Register Vocabulary Fields
     */
    public function register_vocabulary_fields() {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group(array(
            'key' => 'group_vocabulary_fields',
            'title' => 'Word Details',
            'fields' => array(
                array(
                    'key' => 'field_vocab_audio',
                    'label' => 'Pronunciation Audio',
                    'name' => 'vocab_audio',
                    'type' => 'file',
                    'instructions' => 'Upload the audio pronunciation for this word',
                    'return_format' => 'url',
                    'mime_types' => 'mp3,wav,ogg,m4a',
                ),
                array(
                    'key' => 'field_vocab_phonetic',
                    'label' => 'Phonetic/Subtitle (Optional)',
                    'name' => 'vocab_phonetic',
                    'type' => 'text',
                    'instructions' => 'e.g. /kat/',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'vocabulary',
                    ),
                ),
            ),
        ));
    }

    /**
     * Load age group choices dynamically for ACF fields
     */
    public function load_age_group_choices($field) {
        $field['choices'] = sarah_loz_get_age_group_choices();
        return $field;
    }
}

// Initialize the ACF fields
function sarah_loz_acf_fields() {
    return Sarah_Loz_ACF_Fields::get_instance();
}
add_action('init', 'sarah_loz_acf_fields', 5);

// Register broadcast fields separately to ensure they're loaded correctly
function sarah_loz_register_broadcast_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    
    // Broadcast Fields
    acf_add_local_field_group(array(
        'key' => 'group_broadcast_fields',
        'title' => 'Broadcast Details',
        'fields' => array(
            array(
                'key' => 'field_broadcast_type',
                'label' => 'Broadcast Type',
                'name' => 'broadcast_type',
                'type' => 'select',
                'choices' => array(
                    'recorded' => 'Recorded Broadcast',
                    'scheduled' => 'Scheduled Live Broadcast',
                    'live' => 'Currently Live',
                ),
                'default_value' => 'recorded',
                'instructions' => 'Select the type of broadcast',
            ),
            // Live Broadcast Note - only displays for live broadcasts
            array(
                'key' => 'field_broadcast_live_note',
                'label' => 'Live Broadcast Note',
                'name' => 'broadcast_live_note',
                'type' => 'message',
                'message' => 'This broadcast will be shown as "Live Now" on the broadcasts page. Make sure to add the embed code below.',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_broadcast_type',
                            'operator' => '==',
                            'value' => 'live',
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_broadcast_video_url',
                'label' => 'Video URL',
                'name' => 'broadcast_video_url',
                'type' => 'url',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_broadcast_type',
                            'operator' => '==',
                            'value' => 'recorded',
                        ),
                    ),
                ),
                'instructions' => 'Enter the URL for the recorded video (YouTube, Vimeo, etc.)',
            ),
            array(
                'key' => 'field_broadcast_embed_code',
                'label' => 'Embed Code',
                'name' => 'broadcast_embed_code',
                'type' => 'textarea',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_broadcast_type',
                            'operator' => '==',
                            'value' => 'recorded',
                        ),
                    ),
                ),
                'instructions' => 'Or enter custom embed code if URL is not sufficient',
            ),
            array(
                'key' => 'field_broadcast_scheduled_date',
                'label' => 'Scheduled Date',
                'name' => 'broadcast_scheduled_date',
                'type' => 'date_time_picker',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_broadcast_type',
                            'operator' => '==',
                            'value' => 'scheduled',
                        ),
                    ),
                ),
                'instructions' => 'Select the date and time of the scheduled broadcast',
                'display_format' => 'd/m/Y g:i a',
                'return_format' => 'Y-m-d H:i:s',
            ),
            array(
                'key' => 'field_broadcast_live_embed',
                'label' => 'Live Stream Embed Code',
                'name' => 'broadcast_live_embed',
                'type' => 'textarea',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_broadcast_type',
                            'operator' => '==',
                            'value' => 'live',
                        ),
                    ),
                    array(
                        array(
                            'field' => 'field_broadcast_type',
                            'operator' => '==',
                            'value' => 'scheduled',
                        ),
                    ),
                ),
                'instructions' => 'Enter the embed code for the live stream. You can get this from YouTube Live, Vimeo Live, or any other streaming service. This is required for live broadcasts.',
                'placeholder' => '<iframe src="https://www.youtube.com/embed/LIVE_ID" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
                'required' => 1,
            ),
            array(
                'key' => 'field_broadcast_age_range',
                'label' => 'Age Range',
                'name' => 'broadcast_age_range',
                'type' => 'select',
                'choices' => sarah_loz_get_age_group_choices(),
                'default_value' => 'all',
            ),
            array(
                'key' => 'field_broadcast_requires_registration',
                'label' => 'Requires Registration',
                'name' => 'broadcast_requires_registration',
                'type' => 'true_false',
                'default_value' => 0,
                'ui' => 1,
            ),
            array(
                'key' => 'field_broadcast_chat_enabled',
                'label' => 'Enable Chat',
                'name' => 'broadcast_chat_enabled',
                'type' => 'true_false',
                'default_value' => 1,
                'ui' => 1,
                'message' => 'Enable or disable chat for this broadcast',
                'instructions' => 'Allow viewers to chat during live broadcasts. Chat is only visible when broadcast is live.',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_broadcast_type',
                            'operator' => '==',
                            'value' => 'live',
                        ),
                    ),
                    array(
                        array(
                            'field' => 'field_broadcast_type',
                            'operator' => '==',
                            'value' => 'scheduled',
                        ),
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'broadcast',
                ),
            ),
        ),
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
    ));
}
add_action('acf/init', 'sarah_loz_register_broadcast_fields', 20); 
/**
 * Register Vocabulary Fields
 */
function sarah_loz_register_vocabulary_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_vocabulary_fields',
        'title' => 'Word Flashcard Details',
        'fields' => array(
            // FRONT IMAGE
            array(
                'key' => 'field_vocab_front_image',
                'label' => 'Front Image (Word/Text)',
                'name' => 'vocab_front_image',
                'type' => 'image',
                'instructions' => 'Upload an image of the word text (e.g. calligraphy). If empty, the Title will be used.',
                'return_format' => 'url',
                'preview_size' => 'medium',
                'library' => 'all',
            ),
            // BACK IMAGE
            array(
                'key' => 'field_vocab_back_image',
                'label' => 'Back Image (Meaning/Object)',
                'name' => 'vocab_back_image',
                'type' => 'image',
                'instructions' => 'Upload the image representing the word (e.g. picture of a Lion).',
                'return_format' => 'url',
                'preview_size' => 'medium',
                'library' => 'all',
                'required' => 1,
            ),
            // AUDIO
            array(
                'key' => 'field_vocab_audio',
                'label' => 'Pronunciation Audio',
                'name' => 'vocab_audio',
                'type' => 'file',
                'instructions' => 'Upload the pronunciation audio file.',
                'return_format' => 'url',
                'library' => 'all',
                'mime_types' => 'mp3,wav,ogg,m4a',
                'required' => 1,
            ),
            // OPTIONAL PHONETIC/TEXT
            array(
                'key' => 'field_vocab_phonetic',
                'label' => 'Phonetic/Subtitle (Optional)',
                'name' => 'vocab_phonetic',
                'type' => 'text',
                'instructions' => 'e.g. /kat/ or extra text to show on front.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'vocabulary',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
    ));
}
add_action('acf/init', 'sarah_loz_register_vocabulary_fields', 20);

/**
 * FORCE UPDATE: Assessment Game Criteria List
 * This updates the dropdown options in the Admin Panel to match the new requirements.
 */
function sarah_loz_update_assessment_criteria( $field ) {
    $field['choices'] = array(
        'general_meaning' => 'يحدد المعنى العام حتى لو لم يفهم جميع التفاصيل',
        'specific_info'   => 'يستخلص معلومات محددة من نص مسموع',
        'true_false'      => 'يميز المعلومة الصحيحة من المعلومة الخاطئة في نص مسموع',
        'common_phrases'  => 'يميز العبارات الشائعة والمحفوظة التي تظهر في مواقف التواصل الأساسية',
        'vocab_meaning'   => 'يربط المفردات المسموعة بمدلولها',
        'sequence_events' => 'يتتبع تسلسل أحداث بسيطة في قصة أو حوار قصير مسموع',
        'form_opinion'    => 'يكون رأيا فيما يسمع',
        'none'            => 'غير محتسب (لا يدخل في النتيجة)'
    );
    return $field;
}

// Target the specific field name "criteria_category"
add_filter('acf/load_field/name=criteria_category', 'sarah_loz_update_assessment_criteria');

// Also target "criteria" just in case the field name varies in some versions
add_filter('acf/load_field/name=criteria', 'sarah_loz_update_assessment_criteria');


/**
 * FORCE UPDATE: Allow Empty Answers
 * This disables the 'Required' validation for the answers field
 * so you can create "Info Slides" with 0 answers.
 */
function sarah_loz_disable_answers_requirement( $field ) {
    $field['required'] = 0; // Turn off "Required"
    $field['min'] = 0;      // Set minimum rows to 0
    return $field;
}

// Target the "answers" field
add_filter('acf/load_field/name=answers', 'sarah_loz_disable_answers_requirement');