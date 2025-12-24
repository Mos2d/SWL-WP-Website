<?php
/**
 * Import Test Data
 * 
 * Provides functions to import test data for Games, Activities, and Videos post types
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Add admin page for importing test data
 */
function sarah_loz_add_import_page() {
    add_submenu_page(
        'tools.php',
        'Import Test Data',
        'Import Test Data',
        'manage_options',
        'sarah-loz-import-test-data',
        'sarah_loz_import_test_data_page'
    );
}
add_action('admin_menu', 'sarah_loz_add_import_page');

/**
 * Render the import test data admin page
 */
function sarah_loz_import_test_data_page() {
    $import_notice = '';
    
    // Initialize taxonomies before import
    if (isset($_POST['import_action']) && $_POST['import_action'] == 'init_taxonomies' && check_admin_referer('sarah_loz_import_test_data_nonce')) {
        sarah_loz_initialize_taxonomies();
        $import_notice = __('Taxonomies have been initialized.', 'sarah-loz');
    }
    
    // Check if form was submitted
    if (isset($_POST['import_action']) && check_admin_referer('sarah_loz_import_test_data_nonce')) {
        $action = sanitize_text_field($_POST['import_action']);
        $count = isset($_POST['import_count']) ? intval($_POST['import_count']) : 5;
        
        switch ($action) {
            case 'games':
                $created = sarah_loz_import_test_games($count);
                $embed_count = ceil($created / 2);
                $interactive_count = $created - $embed_count;
                $import_notice = sprintf(
                    __('%d test games have been created (%d embedded games and %d interactive games with Memory, Quiz, and Sorting variations).', 'sarah-loz'), 
                    $created, 
                    $embed_count, 
                    $interactive_count
                );
                break;
                
            case 'activities':
                $created = sarah_loz_import_test_activities($count);
                $import_notice = sprintf(__('%d test activities have been created.', 'sarah-loz'), $created);
                break;
                
            case 'videos':
                $created = sarah_loz_import_test_videos($count);
                $import_notice = sprintf(__('%d test videos have been created.', 'sarah-loz'), $created);
                break;
                
            case 'all':
                $games = sarah_loz_import_test_games($count);
                $embed_count = ceil($games / 2);
                $interactive_count = $games - $embed_count;
                
                $activities = sarah_loz_import_test_activities($count);
                $videos = sarah_loz_import_test_videos($count);
                
                $import_notice = sprintf(
                    __('%d test games (%d embedded, %d interactive), %d test activities, and %d test videos have been created.', 'sarah-loz'),
                    $games, 
                    $embed_count, 
                    $interactive_count,
                    $activities, 
                    $videos
                );
                break;
        }
    }
    
    // Display admin page
    ?>
    <div class="wrap">
        <h1><?php _e('Import Test Data', 'sarah-loz'); ?></h1>
        
        <?php if ($import_notice): ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo $import_notice; ?></p>
            </div>
        <?php endif; ?>
        
        <p><?php _e('Use this tool to import test data for Games, Activities, and Videos post types.', 'sarah-loz'); ?></p>
        
        <form method="post" action="">
            <?php wp_nonce_field('sarah_loz_import_test_data_nonce'); ?>
            
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="import_count"><?php _e('Number of items to import:', 'sarah-loz'); ?></label></th>
                    <td>
                        <input type="number" name="import_count" id="import_count" min="1" max="50" value="5">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('What to import:', 'sarah-loz'); ?></th>
                    <td>
                        <fieldset>
                            <p>
                                <label>
                                    <input type="radio" name="import_action" value="init_taxonomies">
                                    <?php _e('Initialize Taxonomies Only', 'sarah-loz'); ?>
                                </label>
                            </p>
                            <p>
                                <label>
                                    <input type="radio" name="import_action" value="games" checked>
                                    <?php _e('Games', 'sarah-loz'); ?>
                                </label>
                            </p>
                            <p>
                                <label>
                                    <input type="radio" name="import_action" value="activities">
                                    <?php _e('Activities', 'sarah-loz'); ?>
                                </label>
                            </p>
                            <p>
                                <label>
                                    <input type="radio" name="import_action" value="videos">
                                    <?php _e('Videos', 'sarah-loz'); ?>
                                </label>
                            </p>
                            <p>
                                <label>
                                    <input type="radio" name="import_action" value="all">
                                    <?php _e('All Post Types', 'sarah-loz'); ?>
                                </label>
                            </p>
                        </fieldset>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Import Test Data', 'sarah-loz'); ?>">
            </p>
        </form>
        
        <div class="card" style="max-width: 600px; margin-top: 20px; padding: 20px;">
            <h3><?php _e('About Interactive Games', 'sarah-loz'); ?></h3>
            <p><?php _e('When importing test games, the system will create a mix of:', 'sarah-loz'); ?></p>
            <ul style="list-style: disc; padding-left: 20px;">
                <li><?php _e('<strong>Embed Games:</strong> External games embedded via iframe', 'sarah-loz'); ?></li>
                <li><?php _e('<strong>Memory Games:</strong> Card matching games with customizable pairs', 'sarah-loz'); ?></li>
                <li><?php _e('<strong>Quiz Games:</strong> Multiple choice question games with explanations', 'sarah-loz'); ?></li>
                <li><?php _e('<strong>Sorting Games:</strong> Drag-and-drop games for categorization or sequencing', 'sarah-loz'); ?></li>
            </ul>
            <p><?php _e('These games will have randomized settings, difficulties, and content. You can edit them after import to customize further.', 'sarah-loz'); ?></p>
        </div>
    </div>
    <?php
}

/**
 * Initialize taxonomy terms
 */
function sarah_loz_initialize_taxonomies() {
    // Game Categories
    $game_categories = array(
        'Educational' => 'Educational games focus on learning outcomes',
        'Puzzle' => 'Puzzle games to develop problem-solving skills',
        'Memory' => 'Memory games to enhance cognitive abilities',
        'Adventure' => 'Adventure games for imagination development',
        'Drawing' => 'Drawing games for artistic expression',
        'Quiz' => 'Quiz games to test knowledge and recall',
        'Sorting' => 'Sorting games to practice categorization and order',
        'Interactive' => 'Highly interactive games with rich user engagement',
        'Arabic Language' => 'Games focused on Arabic language learning',
        'Math' => 'Games focused on mathematical concepts'
    );
    
    foreach ($game_categories as $term => $description) {
        if (!term_exists($term, 'game_category')) {
            wp_insert_term($term, 'game_category', array(
                'description' => $description
            ));
        }
    }
    
    // Activity Categories
    $activity_categories = array(
        'Arts & Crafts' => 'Creative activities involving art and craft materials',
        'Science' => 'Science-based activities for exploration and discovery',
        'Outdoor' => 'Activities to enjoy outdoors',
        'Reading' => 'Activities related to reading and storytelling',
        'Cooking' => 'Simple cooking and food preparation activities'
    );
    
    foreach ($activity_categories as $term => $description) {
        if (!term_exists($term, 'activity_category')) {
            wp_insert_term($term, 'activity_category', array(
                'description' => $description
            ));
        }
    }
    
    // Video Categories
    $video_categories = array(
        'Educational' => 'Educational videos focusing on learning',
        'Stories' => 'Storytelling and animated stories',
        'Music' => 'Music videos for children',
        'Science' => 'Science concepts explained through videos',
        'Language' => 'Videos focusing on language development'
    );
    
    foreach ($video_categories as $term => $description) {
        if (!term_exists($term, 'video_category')) {
            wp_insert_term($term, 'video_category', array(
                'description' => $description
            ));
        }
    }
    
    // Age Groups (common taxonomy)
    $age_groups = array(
        '3-5' => 'For children aged 3-5 years',
        '6-7' => 'For children aged 6-7 years',
        '8-9' => 'For children aged 8-9 years',
        '10-12' => 'For children aged 10-12 years',
        'All ages' => 'Suitable for all age groups'
    );
    
    foreach ($age_groups as $term => $description) {
        if (!term_exists($term, 'age_group')) {
            wp_insert_term($term, 'age_group', array(
                'description' => $description
            ));
        }
    }
    
    // Educational Skills
    $educational_skills = array(
        'Math' => 'Mathematical skills and number concepts',
        'Reading' => 'Reading and literacy skills',
        'Science' => 'Scientific concepts and exploration',
        'Art' => 'Artistic expression and creativity',
        'Language' => 'Language development and communication',
        'Motor Skills' => 'Fine and gross motor skill development',
        'Problem Solving' => 'Critical thinking and problem-solving abilities',
        'Memory' => 'Memory retention and recall abilities',
        'Pattern Recognition' => 'Ability to identify and understand patterns',
        'Categorization' => 'Organizing items into categories',
        'Sequencing' => 'Understanding and arranging items in logical order',
        'Arabic Vocabulary' => 'Building Arabic language vocabulary',
        'Arabic Letters' => 'Recognition and writing of Arabic letters',
        'Arabic Grammar' => 'Understanding basic Arabic grammatical structures',
        'Visual Discrimination' => 'Distinguishing between similar visual elements',
        'Concentration' => 'Focus and sustained attention skills',
        'Logical Reasoning' => 'Using logic to solve problems'
    );
    
    foreach ($educational_skills as $term => $description) {
        if (!term_exists($term, 'educational_skill')) {
            wp_insert_term($term, 'educational_skill', array(
                'description' => $description
            ));
        }
    }
}

/**
 * Import test games
 * 
 * @param int $count Number of games to import
 * @return int Number of games created
 */
function sarah_loz_import_test_games($count = 5) {
    // Initialize taxonomies if they don't exist
    sarah_loz_initialize_taxonomies();
    
    $created = 0;
    
    $difficulties = array('easy', 'medium', 'hard');
    $age_ranges = array('3-5', '6-7', '8-9');
    $game_types = array('embed', 'interactive');
    $interactive_game_types = array('memory', 'quiz', 'sorting');
    
    // Arabic quiz questions (to be used for Arabic focused quizzes)
    $arabic_quiz_questions = array(
        array(
            'text' => 'ما هو شكل الحرف "أ"؟',
            'image' => '',
            'difficulty' => 'easy',
            'explanation' => 'الحرف "أ" هو أول حرف في الأبجدية العربية.',
            'answers' => array(
                array('text' => 'أ', 'correct' => true),
                array('text' => 'ب', 'correct' => false),
                array('text' => 'ت', 'correct' => false),
                array('text' => 'ث', 'correct' => false),
            )
        ),
        array(
            'text' => 'كم عدد أيام الأسبوع؟',
            'image' => '',
            'difficulty' => 'easy',
            'explanation' => 'أيام الأسبوع هي: الأحد، الإثنين، الثلاثاء، الأربعاء، الخميس، الجمعة، السبت.',
            'answers' => array(
                array('text' => '5', 'correct' => false),
                array('text' => '6', 'correct' => false),
                array('text' => '7', 'correct' => true),
                array('text' => '8', 'correct' => false),
            )
        ),
        array(
            'text' => 'ما هو اليوم الأول من أيام الأسبوع؟',
            'image' => '',
            'difficulty' => 'medium',
            'explanation' => 'اليوم الأول من أيام الأسبوع هو الأحد.',
            'answers' => array(
                array('text' => 'السبت', 'correct' => false),
                array('text' => 'الأحد', 'correct' => true),
                array('text' => 'الإثنين', 'correct' => false),
                array('text' => 'الجمعة', 'correct' => false),
            )
        ),
        array(
            'text' => 'ما هو لون العلم السعودي؟',
            'image' => '',
            'difficulty' => 'medium',
            'explanation' => 'العلم السعودي أخضر وعليه كلمة التوحيد وسيف.',
            'answers' => array(
                array('text' => 'أحمر', 'correct' => false),
                array('text' => 'أزرق', 'correct' => false),
                array('text' => 'أخضر', 'correct' => true),
                array('text' => 'أصفر', 'correct' => false),
            )
        ),
    );
    
    // Arabic sorting categories
    $arabic_categories = array(
        array('id' => 'fruits', 'name' => 'الفواكه'),
        array('id' => 'vegetables', 'name' => 'الخضروات'),
        array('id' => 'animals', 'name' => 'الحيوانات')
    );
    
    // Arabic sorting items
    $arabic_items = array(
        array(
            'id' => 'apple',
            'text' => 'تفاح',
            'image' => 'https://example.com/apple.jpg',
            'difficulty' => 'easy',
            'category' => 'fruits'
        ),
        array(
            'id' => 'banana',
            'text' => 'موز',
            'image' => 'https://example.com/banana.jpg',
            'difficulty' => 'easy',
            'category' => 'fruits'
        ),
        array(
            'id' => 'orange',
            'text' => 'برتقال',
            'image' => 'https://example.com/orange.jpg',
            'difficulty' => 'medium',
            'category' => 'fruits'
        ),
        array(
            'id' => 'carrot',
            'text' => 'جزر',
            'image' => 'https://example.com/carrot.jpg',
            'difficulty' => 'easy',
            'category' => 'vegetables'
        ),
        array(
            'id' => 'tomato',
            'text' => 'طماطم',
            'image' => 'https://example.com/tomato.jpg',
            'difficulty' => 'medium',
            'category' => 'vegetables'
        ),
        array(
            'id' => 'cucumber',
            'text' => 'خيار',
            'image' => 'https://example.com/cucumber.jpg',
            'difficulty' => 'medium',
            'category' => 'vegetables'
        ),
        array(
            'id' => 'cat',
            'text' => 'قطة',
            'image' => 'https://example.com/cat.jpg',
            'difficulty' => 'easy',
            'category' => 'animals'
        ),
        array(
            'id' => 'dog',
            'text' => 'كلب',
            'image' => 'https://example.com/dog.jpg',
            'difficulty' => 'easy',
            'category' => 'animals'
        ),
        array(
            'id' => 'lion',
            'text' => 'أسد',
            'image' => 'https://example.com/lion.jpg',
            'difficulty' => 'hard',
            'category' => 'animals'
        ),
    );
    
    // Arabic letter sequence items
    $arabic_sequence_items = array(
        array(
            'id' => 'alif',
            'text' => 'أ',
            'image' => 'https://example.com/alif.jpg',
            'difficulty' => 'easy',
            'position' => 1
        ),
        array(
            'id' => 'ba',
            'text' => 'ب',
            'image' => 'https://example.com/ba.jpg',
            'difficulty' => 'easy',
            'position' => 2
        ),
        array(
            'id' => 'ta',
            'text' => 'ت',
            'image' => 'https://example.com/ta.jpg',
            'difficulty' => 'medium',
            'position' => 3
        ),
        array(
            'id' => 'tha',
            'text' => 'ث',
            'image' => 'https://example.com/tha.jpg',
            'difficulty' => 'medium',
            'position' => 4
        ),
        array(
            'id' => 'jim',
            'text' => 'ج',
            'image' => 'https://example.com/jim.jpg',
            'difficulty' => 'hard',
            'position' => 5
        ),
    );
    
    // Get game categories
    $game_categories = get_terms(array(
        'taxonomy' => 'game_category',
        'hide_empty' => false,
    ));
    $category_ids = wp_list_pluck($game_categories, 'term_id');
    
    // Get educational skills
    $educational_skills = get_terms(array(
        'taxonomy' => 'educational_skill',
        'hide_empty' => false,
    ));
    $skill_ids = wp_list_pluck($educational_skills, 'term_id');
    
    for ($i = 1; $i <= $count; $i++) {
        // Determine if this should be an Arabic-focused game
        $is_arabic_focused = (bool)($i % 3 === 0); // Every third game will be Arabic-focused
        
        $title = $is_arabic_focused 
            ? 'لعبة تعليمية ' . $i 
            : 'Test Game ' . $i;
            
        $content = $is_arabic_focused
            ? 'هذا وصف للعبة تعليمية رقم ' . $i . '. يتضمن تفاصيل حول كيفية لعب اللعبة، والقواعد، ومعلومات مهمة أخرى.'
            : 'This is a test game description for game number ' . $i . '. It includes details about how to play the game, rules, and other important information.';
        
        // Create the game post
        $post_id = wp_insert_post(array(
            'post_title'    => $title,
            'post_content'  => $content,
            'post_status'   => 'publish',
            'post_type'     => 'game',
            'post_excerpt'  => $is_arabic_focused 
                ? 'لعبة ممتعة للأطفال للعب والتعلم.'
                : 'A fun test game for children to play and learn.',
        ));
        
        if ($post_id && !is_wp_error($post_id)) {
            // Set a featured image placeholder (optional, requires attachment ID)
            // set_post_thumbnail($post_id, $attachment_id);
            
            // Set taxonomies
            // Randomly assign 1-2 categories
            $rand_cats = array_rand($category_ids, min(2, count($category_ids)));
            if (!is_array($rand_cats)) {
                $rand_cats = array($rand_cats);
            }
            foreach ($rand_cats as $cat_index) {
                wp_set_object_terms($post_id, $category_ids[$cat_index], 'game_category', true);
            }
            
            // For Arabic focused games, also add Arabic Language category if it exists
            if ($is_arabic_focused) {
                $arabic_term = term_exists('Arabic Language', 'game_category');
                if ($arabic_term) {
                    wp_set_object_terms($post_id, $arabic_term['term_id'], 'game_category', true);
                }
                
                // Add Arabic specific skills
                $arabic_skills = array('Arabic Vocabulary', 'Arabic Letters', 'Language');
                foreach ($arabic_skills as $skill_name) {
                    $skill_term = term_exists($skill_name, 'educational_skill');
                    if ($skill_term) {
                        wp_set_object_terms($post_id, $skill_term['term_id'], 'educational_skill', true);
                    }
                }
            } else {
                // Randomly assign 1-3 educational skills
                $rand_skills = array_rand($skill_ids, min(3, count($skill_ids)));
                if (!is_array($rand_skills)) {
                    $rand_skills = array($rand_skills);
                }
                foreach ($rand_skills as $skill_index) {
                    wp_set_object_terms($post_id, $skill_ids[$skill_index], 'educational_skill', true);
                }
            }
            
            // Set age group taxonomy
            $age_range = $age_ranges[array_rand($age_ranges)];
            wp_set_object_terms($post_id, $age_range, 'age_group', false);
            
            // Determine game type (alternate between embed and interactive)
            $game_type = $game_types[$i % 2];
            
            // Set ACF fields
            if (function_exists('update_field')) {
                // Common fields
                update_field('age_range', $age_range, $post_id);
                update_field('difficulty_level', $difficulties[array_rand($difficulties)], $post_id);
                update_field('game_type', $game_type, $post_id);
                
                // Game settings
                $game_settings = array(
                    'width' => rand(600, 1000),
                    'height' => rand(400, 700),
                    'background_color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
                    'show_score' => true,
                    'show_timer' => (bool)rand(0, 1),
                    'auto_start' => (bool)rand(0, 1)
                );
                update_field('game_settings', $game_settings, $post_id);
                
                if ($game_type === 'embed') {
                    // Embed game settings
                    update_field('game_url', '<iframe src="https://example.com/game' . $i . '" width="100%" height="500" frameborder="0"></iframe>', $post_id);
                } else {
                    // Interactive game settings
                    $interactive_type = $interactive_game_types[$i % count($interactive_game_types)];
                    update_field('interactive_game_type', $interactive_type, $post_id);
                    
                    switch ($interactive_type) {
                        case 'memory':
                            // Memory game specific settings
                            $memory_settings = array(
                                'card_back_image' => 'https://via.placeholder.com/150/2196F3/FFFFFF?text=?',
                                'match_sound' => 'https://example.com/match.mp3',
                                'error_sound' => 'https://example.com/error.mp3',
                                'card_pairs' => array(
                                    array('image' => 'https://via.placeholder.com/150/FF5733/FFFFFF?text=1'),
                                    array('image' => 'https://via.placeholder.com/150/33FF57/000000?text=2'),
                                    array('image' => 'https://via.placeholder.com/150/5733FF/FFFFFF?text=3'),
                                    array('image' => 'https://via.placeholder.com/150/FFFF33/000000?text=4'),
                                    array('image' => 'https://via.placeholder.com/150/33FFFF/000000?text=5'),
                                    array('image' => 'https://via.placeholder.com/150/FF33FF/FFFFFF?text=6'),
                                    array('image' => 'https://via.placeholder.com/150/FFFF33/000000?text=7'),
                                    array('image' => 'https://via.placeholder.com/150/3333FF/FFFFFF?text=8')
                                )
                            );
                            update_field('memory_game_settings', $memory_settings, $post_id);
                            break;
                            
                        case 'quiz':
                            // Quiz game specific settings
                            $quiz_settings = array(
                                'shuffle_questions' => true,
                                'shuffle_answers' => true,
                                'time_per_question' => rand(10, 30),
                                'show_explanations' => true,
                                'questions' => $is_arabic_focused 
                                    ? $arabic_quiz_questions
                                    : array(
                                        array(
                                            'text' => 'What is 2+2?',
                                            'image' => '',
                                            'difficulty' => 'easy',
                                            'explanation' => 'Two plus two equals four.',
                                            'answers' => array(
                                                array('text' => '3', 'correct' => false),
                                                array('text' => '4', 'correct' => true),
                                                array('text' => '5', 'correct' => false),
                                                array('text' => '22', 'correct' => false),
                                            )
                                        ),
                                        array(
                                            'text' => 'Which animal can fly?',
                                            'image' => '',
                                            'difficulty' => 'easy',
                                            'explanation' => 'Birds have wings that allow them to fly.',
                                            'answers' => array(
                                                array('text' => 'Dog', 'correct' => false),
                                                array('text' => 'Cat', 'correct' => false),
                                                array('text' => 'Bird', 'correct' => true),
                                                array('text' => 'Fish', 'correct' => false),
                                            )
                                        ),
                                        array(
                                            'text' => 'How many colors are in a rainbow?',
                                            'image' => '',
                                            'difficulty' => 'medium',
                                            'explanation' => 'A rainbow has seven colors: red, orange, yellow, green, blue, indigo, and violet.',
                                            'answers' => array(
                                                array('text' => '5', 'correct' => false),
                                                array('text' => '6', 'correct' => false),
                                                array('text' => '7', 'correct' => true),
                                                array('text' => '8', 'correct' => false),
                                            )
                                        ),
                                    )
                            );
                            update_field('quiz_game_settings', $quiz_settings, $post_id);
                            break;
                            
                        case 'sorting':
                            // Sorting game specific settings
                            $is_sequence = $is_arabic_focused ? ($i % 2 === 0) : (bool)rand(0, 1);
                            
                            $sorting_settings = array(
                                'is_sequence' => $is_sequence,
                                'allow_incorrect_placements' => (bool)rand(0, 1),
                                'correct_sound' => 'https://example.com/correct.mp3',
                                'error_sound' => 'https://example.com/error.mp3'
                            );
                            
                            if ($is_arabic_focused) {
                                if ($is_sequence) {
                                    // Arabic sequence (alphabet)
                                    $sorting_settings['items'] = $arabic_sequence_items;
                                } else {
                                    // Arabic categories
                                    $sorting_settings['categories'] = $arabic_categories;
                                    $sorting_settings['items'] = $arabic_items;
                                }
                            } else {
                                if ($is_sequence) {
                                    // Sequence mode items
                                    $sorting_settings['items'] = array(
                                        array(
                                            'id' => 'item1',
                                            'text' => 'Step 1',
                                            'image' => 'https://example.com/step1.jpg',
                                            'difficulty' => 'easy',
                                            'position' => 1
                                        ),
                                        array(
                                            'id' => 'item2',
                                            'text' => 'Step 2',
                                            'image' => 'https://example.com/step2.jpg',
                                            'difficulty' => 'easy',
                                            'position' => 2
                                        ),
                                        array(
                                            'id' => 'item3',
                                            'text' => 'Step 3',
                                            'image' => 'https://example.com/step3.jpg',
                                            'difficulty' => 'medium',
                                            'position' => 3
                                        ),
                                        array(
                                            'id' => 'item4',
                                            'text' => 'Step 4',
                                            'image' => 'https://example.com/step4.jpg',
                                            'difficulty' => 'medium',
                                            'position' => 4
                                        ),
                                        array(
                                            'id' => 'item5',
                                            'text' => 'Step 5',
                                            'image' => 'https://example.com/step5.jpg',
                                            'difficulty' => 'hard',
                                            'position' => 5
                                        ),
                                    );
                                } else {
                                    // Category mode
                                    $sorting_settings['categories'] = array(
                                        array('id' => 'cat1', 'name' => 'Animals'),
                                        array('id' => 'cat2', 'name' => 'Fruits'),
                                        array('id' => 'cat3', 'name' => 'Vegetables')
                                    );
                                    
                                    $sorting_settings['items'] = array(
                                        array(
                                            'id' => 'item1',
                                            'text' => 'Dog',
                                            'image' => 'https://example.com/dog.jpg',
                                            'difficulty' => 'easy',
                                            'category' => 'cat1'
                                        ),
                                        array(
                                            'id' => 'item2',
                                            'text' => 'Cat',
                                            'image' => 'https://example.com/cat.jpg',
                                            'difficulty' => 'easy',
                                            'category' => 'cat1'
                                        ),
                                        array(
                                            'id' => 'item3',
                                            'text' => 'Apple',
                                            'image' => 'https://example.com/apple.jpg',
                                            'difficulty' => 'easy',
                                            'category' => 'cat2'
                                        ),
                                        array(
                                            'id' => 'item4',
                                            'text' => 'Banana',
                                            'image' => 'https://example.com/banana.jpg',
                                            'difficulty' => 'medium',
                                            'category' => 'cat2'
                                        ),
                                        array(
                                            'id' => 'item5',
                                            'text' => 'Carrot',
                                            'image' => 'https://example.com/carrot.jpg',
                                            'difficulty' => 'medium',
                                            'category' => 'cat3'
                                        ),
                                        array(
                                            'id' => 'item6',
                                            'text' => 'Broccoli',
                                            'image' => 'https://example.com/broccoli.jpg',
                                            'difficulty' => 'hard',
                                            'category' => 'cat3'
                                        ),
                                    );
                                }
                            }
                            
                            update_field('sorting_game_settings', $sorting_settings, $post_id);
                            break;
                    }
                }
            }
            
            $created++;
        }
    }
    
    return $created;
}

/**
 * Import test activities
 * 
 * @param int $count Number of activities to import
 * @return int Number of activities created
 */
function sarah_loz_import_test_activities($count = 5) {
    // Initialize taxonomies if they don't exist
    sarah_loz_initialize_taxonomies();
    
    $created = 0;
    
    $age_ranges = array('3-5', '6-7', '8-9');
    $materials = array(
        'Scissors, paper, glue, and markers',
        'Construction paper, tape, and string',
        'Cardboard, paint, and brushes',
        'Colored pencils, ruler, and paper',
        'Clay, water, and clay tools'
    );
    
    // Get activity categories
    $activity_categories = get_terms(array(
        'taxonomy' => 'activity_category',
        'hide_empty' => false,
    ));
    $category_ids = wp_list_pluck($activity_categories, 'term_id');
    
    // Get educational skills
    $educational_skills = get_terms(array(
        'taxonomy' => 'educational_skill',
        'hide_empty' => false,
    ));
    $skill_ids = wp_list_pluck($educational_skills, 'term_id');
    
    for ($i = 1; $i <= $count; $i++) {
        $title = 'Test Activity ' . $i;
        $content = 'This is a test activity description for activity number ' . $i . '. It includes details about how to do the activity, step-by-step instructions, and expected outcomes.';
        
        // Create the activity post
        $post_id = wp_insert_post(array(
            'post_title'    => $title,
            'post_content'  => $content,
            'post_status'   => 'publish',
            'post_type'     => 'activity',
            'post_excerpt'  => 'A creative test activity for children to develop skills and have fun.',
        ));
        
        if ($post_id && !is_wp_error($post_id)) {
            // Set taxonomies
            // Randomly assign 1-2 categories
            $rand_cats = array_rand($category_ids, min(2, count($category_ids)));
            if (!is_array($rand_cats)) {
                $rand_cats = array($rand_cats);
            }
            foreach ($rand_cats as $cat_index) {
                wp_set_object_terms($post_id, $category_ids[$cat_index], 'activity_category', true);
            }
            
            // Randomly assign 1-3 educational skills
            $rand_skills = array_rand($skill_ids, min(3, count($skill_ids)));
            if (!is_array($rand_skills)) {
                $rand_skills = array($rand_skills);
            }
            foreach ($rand_skills as $skill_index) {
                wp_set_object_terms($post_id, $skill_ids[$skill_index], 'educational_skill', true);
            }
            
            // Set age group taxonomy
            $age_range = $age_ranges[array_rand($age_ranges)];
            wp_set_object_terms($post_id, $age_range, 'age_group', false);
            
            // Set ACF fields
            if (function_exists('update_field')) {
                update_field('age_range', $age_range, $post_id);
                update_field('required_materials', $materials[array_rand($materials)], $post_id);
                // Note: PDF attachments would require actual file uploads
            }
            
            $created++;
        }
    }
    
    return $created;
}

/**
 * Import test videos
 * 
 * @param int $count Number of videos to import
 * @return int Number of videos created
 */
function sarah_loz_import_test_videos($count = 5) {
    // Initialize taxonomies if they don't exist
    sarah_loz_initialize_taxonomies();
    
    $created = 0;
    
    $age_ranges = array('3-5', '6-7', '8-9');
    $video_embeds = array(
        '<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
        '<iframe width="560" height="315" src="https://www.youtube.com/embed/9bZkp7q19f0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
        '<iframe width="560" height="315" src="https://www.youtube.com/embed/kJQP7kiw5Fk" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
        '<iframe width="560" height="315" src="https://www.youtube.com/embed/UfcAVejslrU" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
        '<iframe width="560" height="315" src="https://www.youtube.com/embed/JGwWNGJdvx8" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
    );
    
    // Get video categories
    $video_categories = get_terms(array(
        'taxonomy' => 'video_category',
        'hide_empty' => false,
    ));
    $category_ids = wp_list_pluck($video_categories, 'term_id');
    
    // Get educational skills
    $educational_skills = get_terms(array(
        'taxonomy' => 'educational_skill',
        'hide_empty' => false,
    ));
    $skill_ids = wp_list_pluck($educational_skills, 'term_id');
    
    for ($i = 1; $i <= $count; $i++) {
        $title = 'Test Video ' . $i;
        $content = 'This is a test video description for video number ' . $i . '. It includes details about what the video is about, its educational value, and other relevant information.';
        
        // Create the video post
        $post_id = wp_insert_post(array(
            'post_title'    => $title,
            'post_content'  => $content,
            'post_status'   => 'publish',
            'post_type'     => 'video',
            'post_excerpt'  => 'An entertaining and educational test video for children.',
        ));
        
        if ($post_id && !is_wp_error($post_id)) {
            // Set taxonomies
            // Randomly assign 1-2 categories
            $rand_cats = array_rand($category_ids, min(2, count($category_ids)));
            if (!is_array($rand_cats)) {
                $rand_cats = array($rand_cats);
            }
            foreach ($rand_cats as $cat_index) {
                wp_set_object_terms($post_id, $category_ids[$cat_index], 'video_category', true);
            }
            
            // Randomly assign 1-3 educational skills
            $rand_skills = array_rand($skill_ids, min(3, count($skill_ids)));
            if (!is_array($rand_skills)) {
                $rand_skills = array($rand_skills);
            }
            foreach ($rand_skills as $skill_index) {
                wp_set_object_terms($post_id, $skill_ids[$skill_index], 'educational_skill', true);
            }
            
            // Set age group taxonomy
            $age_range = $age_ranges[array_rand($age_ranges)];
            wp_set_object_terms($post_id, $age_range, 'age_group', false);
            
            // Set ACF fields
            if (function_exists('update_field')) {
                update_field('age_range', $age_range, $post_id);
                update_field('video_url', $video_embeds[array_rand($video_embeds)], $post_id);
            }
            
            $created++;
        }
    }
    
    return $created;
} 