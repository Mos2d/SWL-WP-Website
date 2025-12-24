<?php
/**
 * Practices Functionality
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Sarah_Loz_Practices {
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
        // Add shortcode for rendering a practice
        add_shortcode('practice', array($this, 'practice_shortcode'));
        
        // Ajax action for checking practice answers
        add_action('wp_ajax_check_practice_answers', array($this, 'check_practice_answers'));
        add_action('wp_ajax_nopriv_check_practice_answers', array($this, 'check_practice_answers'));
        
        // Add practices to related content pages
        add_action('sarah_loz_after_video_content', array($this, 'show_related_practices'));
        add_action('sarah_loz_after_activity_content', array($this, 'show_related_practices'));
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Enqueue practice scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'sarah-loz-practices', 
            get_template_directory_uri() . '/assets/js/practices.js', 
            array('jquery'), 
            filemtime(get_template_directory() . '/assets/js/practices.js'), 
            true
        );

        // Add AJAX URL and nonce
        wp_localize_script('sarah-loz-practices', 'sarahLozPractices', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sarah_loz_practices_nonce'),
            'i18n' => array(
                'correct' => __('صحيح!', 'sarah-loz'),
                'incorrect' => __('غير صحيح', 'sarah-loz'),
                'congratulations' => __('تهانينا!', 'sarah-loz'),
                'tryAgain' => __('حاول مرة أخرى', 'sarah-loz'),
                'score' => __('النتيجة: ', 'sarah-loz'),
                'submit' => __('تسليم', 'sarah-loz'),
                'loading' => __('جاري التحميل...', 'sarah-loz'),
                'rotateDevice' => __('يرجى تدوير جهازك إلى الوضع الأفقي للحصول على تجربة أفضل مع تمارين التوصيل.', 'sarah-loz'),
            )
        ));
    }

    /**
     * Practice shortcode callback
     */
    public function practice_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
        ), $atts, 'practice');

        if (empty($atts['id'])) {
            return '';
        }

        $practice_id = intval($atts['id']);
        $practice = get_post($practice_id);

        if (!$practice || $practice->post_type !== 'practice') {
            return '';
        }

        // Check if the practice is related to a video or activity that the user needs to complete first
        $related_content_id = get_field('related_content', $practice_id);
        
        // Only check for completion if user is logged in AND there is related content
        if ($related_content_id && is_user_logged_in()) {
            $related_content_type = get_post_type($related_content_id);
            $user_id = get_current_user_id();
            $content_completed = false;
            
            if ($related_content_type === 'video') {
                // Get user's watched videos
                $watched_videos = get_user_meta($user_id, 'watched_videos', true);
                
                // Convert to array if not already
                if (!is_array($watched_videos)) {
                    $watched_videos = array();
                }
                
                // Check if related video is in the watched list
                $content_completed = in_array($related_content_id, $watched_videos);
                
                // If not watched yet, let's force track it as watched if they're viewing the practice
                // This helps with migration issues where users might have watched videos before this system was in place
                if (!$content_completed) {
                    // Add the video ID to the watched list
                    $watched_videos[] = $related_content_id;
                    update_user_meta($user_id, 'watched_videos', $watched_videos);
                    
                    // Also trigger the watched video action for achievements
                    do_action('sarah_loz_video_watched', $user_id, $related_content_id);
                    
                    // Now it's considered completed
                    $content_completed = true;
                }
            } elseif ($related_content_type === 'activity') {
                // Get user's completed activities
                $completed_activities = get_user_meta($user_id, 'completed_activities', true);
                
                // Convert to array if not already
                if (!is_array($completed_activities)) {
                    $completed_activities = array();
                }
                
                // Check if related activity is in the completed list
                $content_completed = in_array($related_content_id, $completed_activities);
                
                // If not completed yet, let's force mark it as completed if they're viewing the practice
                if (!$content_completed) {
                    // Add the activity ID to the completed list
                    $completed_activities[] = $related_content_id;
                    update_user_meta($user_id, 'completed_activities', $completed_activities);
                    
                    // Also trigger the activity completed action for achievements
                    do_action('sarah_loz_activity_completed', $user_id, $related_content_id);
                    
                    // Now it's considered completed
                    $content_completed = true;
                }
            }
            
            // If all checks still indicate content is not completed, show requirement message
            // (This should no longer happen with the automatic completion above)
            if (!$content_completed) {
                return $this->get_content_requirement_message($related_content_id);
            }
        }

        // Get practice type
        $practice_type = get_field('practice_type', $practice_id);
        
        ob_start();
        
        echo '<div class="sarah-loz-practice" data-practice-id="' . esc_attr($practice_id) . '">';
        echo '<h2 class="practice-title">' . get_the_title($practice_id) . '</h2>';
        
        if ($practice_type === 'multiple_choice') {
            $this->render_multiple_choice_practice($practice_id);
        } elseif ($practice_type === 'matching') {
            $this->render_matching_practice($practice_id);
        }
        
        echo '</div>';
        
        return ob_get_clean();
    }

    /**
     * Display related practices on video and activity pages
     */
    public function show_related_practices($post_id) {
        // Find related practices
        $args = array(
            'post_type' => 'practice',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'related_content',
                    'value' => $post_id,
                    'compare' => '='
                )
            )
        );
        
        $practices = get_posts($args);
        
        if (empty($practices)) {
            return;
        }
        
        echo '<div class="related-practices mt-8">';
        echo '<h3 class="text-xl font-bold mb-4">' . __('تدريبات متعلقة', 'sarah-loz') . '</h3>';
        echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">';
        
        foreach ($practices as $practice) {
            $practice_type = get_field('practice_type', $practice->ID);
            $practice_type_label = $practice_type === 'multiple_choice' ? __('اختيار من متعدد', 'sarah-loz') : __('تمرين توصيل', 'sarah-loz');
            $difficulty = get_field('difficulty_level', $practice->ID);
            $difficulty_label = '';
            
            switch ($difficulty) {
                case 'easy':
                    $difficulty_label = __('سهل', 'sarah-loz');
                    $difficulty_class = 'bg-green-100 text-green-800';
                    break;
                case 'medium':
                    $difficulty_label = __('متوسط', 'sarah-loz');
                    $difficulty_class = 'bg-yellow-100 text-yellow-800';
                    break;
                case 'hard':
                    $difficulty_label = __('صعب', 'sarah-loz');
                    $difficulty_class = 'bg-red-100 text-red-800';
                    break;
                default:
                    $difficulty_label = __('سهل', 'sarah-loz');
                    $difficulty_class = 'bg-green-100 text-green-800';
            }
            
            echo '<div class="practice-card bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">';
            
            if (has_post_thumbnail($practice->ID)) {
                echo '<div class="practice-thumbnail">';
                echo get_the_post_thumbnail($practice->ID, 'medium', array('class' => 'w-full h-48 object-cover'));
                echo '</div>';
            }
            
            echo '<div class="p-4">';
            echo '<h4 class="text-lg font-bold mb-2">' . get_the_title($practice->ID) . '</h4>';
            
            echo '<div class="flex flex-wrap gap-2 mb-3">';
            echo '<span class="inline-block px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">' . $practice_type_label . '</span>';
            echo '<span class="inline-block px-2 py-1 text-xs rounded-full ' . $difficulty_class . '">' . $difficulty_label . '</span>';
            echo '</div>';
            
            echo '<div class="practice-excerpt mb-4">' . get_the_excerpt($practice->ID) . '</div>';
            
            echo '<a href="' . get_permalink($practice->ID) . '" class="inline-block bg-accent hover:bg-accent-dark text-white font-bold py-2 px-4 rounded transition-colors">' . __('بدء التدريب', 'sarah-loz') . '</a>';
            echo '</div>'; // .p-4
            
            echo '</div>'; // .practice-card
        }
        
        echo '</div>'; // .grid
        echo '</div>'; // .related-practices
    }

    /**
     * Render multiple choice practice
     */
    private function render_multiple_choice_practice($practice_id) {
        $questions = get_field('questions', $practice_id);
        
        if (empty($questions)) {
            return;
        }
        
        echo '<form class="practice-form multiple-choice-practice">';
        echo '<div class="practice-questions">';
        
        foreach ($questions as $index => $question) {
            $question_number = $index + 1;
            
            echo '<div class="practice-question mb-6 p-4 bg-gray-50 rounded-lg" data-question-index="' . $index . '">';
            echo '<div class="question-header mb-4">';
            echo '<h3 class="text-lg font-bold">(' . $question_number . ') ' . $question['question_text'] . '</h3>';
            
            // Question Image
            if (!empty($question['question_image'])) {
                $image_url = wp_get_attachment_image_url($question['question_image'], 'medium');
                if ($image_url) {
                    echo '<div class="question-image mt-2">';
                    echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($question['question_text']) . '" class="max-w-full h-auto rounded">';
                    echo '</div>';
                }
            }
            
            // Question Audio
            if (!empty($question['question_audio'])) {
                $audio_url = wp_get_attachment_url($question['question_audio']);
                if ($audio_url) {
                    echo '<div class="question-audio mt-2">';
                    echo '<audio controls class="w-full">';
                    echo '<source src="' . esc_url($audio_url) . '" type="' . get_post_mime_type($question['question_audio']) . '">';
                    echo __('متصفحك لا يدعم عنصر الصوت.', 'sarah-loz');
                    echo '</audio>';
                    echo '</div>';
                }
            }
            
            echo '</div>'; // .question-header
            
            echo '<div class="answers-list grid grid-cols-1 md:grid-cols-2 gap-4">';
            
            if (!empty($question['answers'])) {
                foreach ($question['answers'] as $answer_index => $answer) {
                    $answer_id = 'q' . $question_number . '_a' . ($answer_index + 1);
                    $is_correct = $answer['is_correct'] ? 'true' : 'false';
                    
                    echo '<div class="answer-item">';
                    echo '<label for="' . $answer_id . '" class="flex items-start p-3 bg-white border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">';
                    echo '<input type="radio" id="' . $answer_id . '" name="q' . $question_number . '" value="' . $answer_index . '" class="mt-1 mr-2" data-correct="' . $is_correct . '">';
                    
                    echo '<div class="answer-content w-full">';
                    
                    if (!empty($answer['answer_text'])) {
                        echo '<div class="answer-text">' . $answer['answer_text'] . '</div>';
                    }
                    
                    // Answer Image
                    if (!empty($answer['answer_image'])) {
                        $image_url = wp_get_attachment_image_url($answer['answer_image'], 'thumbnail');
                        if ($image_url) {
                            echo '<div class="answer-image mt-2">';
                            echo '<img src="' . esc_url($image_url) . '" alt="' . (isset($answer['answer_text']) ? esc_attr($answer['answer_text']) : '') . '" class="max-w-full h-auto rounded">';
                            echo '</div>';
                        }
                    }
                    
                    // Answer Audio
                    if (!empty($answer['answer_audio'])) {
                        $audio_url = wp_get_attachment_url($answer['answer_audio']);
                        if ($audio_url) {
                            echo '<div class="answer-audio mt-2">';
                            echo '<audio controls class="w-full">';
                            echo '<source src="' . esc_url($audio_url) . '" type="' . get_post_mime_type($answer['answer_audio']) . '">';
                            echo __('متصفحك لا يدعم عنصر الصوت.', 'sarah-loz');
                            echo '</audio>';
                            echo '</div>';
                        }
                    }
                    
                    echo '</div>'; // .answer-content
                    echo '</label>';
                    echo '</div>'; // .answer-item
                }
            }
            
            echo '</div>'; // .answers-list
            echo '<div class="question-feedback mt-2 hidden"></div>';
            echo '</div>'; // .practice-question
        }
        
        echo '</div>'; // .practice-questions
        
        echo '<div class="practice-actions mt-6 text-center">';
        echo '<button type="submit" class="submit-practice bg-accent hover:bg-accent-dark text-white font-bold py-2 px-6 rounded-lg transition-colors">' . __('تسليم الإجابات', 'sarah-loz') . '</button>';
        echo '</div>';
        
        echo '<div class="practice-results mt-8 p-6 bg-white border border-gray-200 rounded-lg text-center hidden">';
        echo '<div class="results-message text-xl font-bold mb-4"></div>';
        echo '<div class="results-score text-lg mb-4"></div>';
        echo '<div class="results-feedback mb-6"></div>';
        echo '<button type="button" class="try-again bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">' . __('حاول مرة أخرى', 'sarah-loz') . '</button>';
        echo '</div>';
        
        echo '</form>';
    }

    /**
     * Render matching practice
     */
    private function render_matching_practice($practice_id) {
        $pairs = get_field('matching_pairs', $practice_id);
        
        if (empty($pairs)) {
            return;
        }
        
        // Shuffle the right items to make it more challenging
        $right_items = array();
        foreach ($pairs as $index => $pair) {
            $right_items[$index] = $pair['item_right'];
        }
        shuffle($right_items);
        
        echo '<form class="practice-form matching-practice">';
        echo '<div class="matching-instructions mb-6 p-4 bg-blue-50 text-blue-800 rounded-lg">';
        echo '<p>' . __('قم بتوصيل العناصر على اليمين مع العناصر المناسبة على اليسار من خلال السحب والإفلات.', 'sarah-loz') . '</p>';
        echo '</div>';
        
        echo '<div class="matching-container flex flex-col md:flex-row justify-between">';
        
        // Left items (fixed order)
        echo '<div class="left-items w-full md:w-5/12">';
        foreach ($pairs as $index => $pair) {
            $left_item = $pair['item_left'];
            
            echo '<div class="matching-item p-3 mb-4 bg-white border border-gray-200 rounded-lg" data-item-index="' . $index . '">';
            
            if (!empty($left_item['text'])) {
                echo '<div class="item-text font-medium">' . $left_item['text'] . '</div>';
            }
            
            if (!empty($left_item['image'])) {
                $image_url = wp_get_attachment_image_url($left_item['image'], 'thumbnail');
                if ($image_url) {
                    echo '<div class="item-image mt-2">';
                    echo '<img src="' . esc_url($image_url) . '" alt="' . (!empty($left_item['text']) ? esc_attr($left_item['text']) : '') . '" class="max-w-full h-auto rounded">';
                    echo '</div>';
                }
            }
            
            // Left item audio
            if (!empty($left_item['audio'])) {
                $audio_url = wp_get_attachment_url($left_item['audio']);
                if ($audio_url) {
                    echo '<div class="item-audio mt-2">';
                    echo '<audio controls class="w-full">';
                    echo '<source src="' . esc_url($audio_url) . '" type="' . get_post_mime_type($left_item['audio']) . '">';
                    echo __('متصفحك لا يدعم عنصر الصوت.', 'sarah-loz');
                    echo '</audio>';
                    echo '</div>';
                }
            }
            
            echo '<div class="item-match-status mt-2"></div>';
            echo '</div>'; // .matching-item
        }
        echo '</div>'; // .left-items
        
        // Right items (shuffled)
        echo '<div class="right-items w-full md:w-5/12">';
        foreach ($right_items as $index => $right_item) {
            $original_index = array_search($right_item, array_column($pairs, 'item_right'));
            
            echo '<div class="matching-item p-3 mb-4 bg-white border border-gray-200 rounded-lg" data-correct-match="' . $original_index . '">';
            
            if (!empty($right_item['text'])) {
                echo '<div class="item-text font-medium">' . $right_item['text'] . '</div>';
            }
            
            if (!empty($right_item['image'])) {
                $image_url = wp_get_attachment_image_url($right_item['image'], 'thumbnail');
                if ($image_url) {
                    echo '<div class="item-image mt-2">';
                    echo '<img src="' . esc_url($image_url) . '" alt="' . (!empty($right_item['text']) ? esc_attr($right_item['text']) : '') . '" class="max-w-full h-auto rounded">';
                    echo '</div>';
                }
            }
            
            // Right item audio
            if (!empty($right_item['audio'])) {
                $audio_url = wp_get_attachment_url($right_item['audio']);
                if ($audio_url) {
                    echo '<div class="item-audio mt-2">';
                    echo '<audio controls class="w-full">';
                    echo '<source src="' . esc_url($audio_url) . '" type="' . get_post_mime_type($right_item['audio']) . '">';
                    echo __('متصفحك لا يدعم عنصر الصوت.', 'sarah-loz');
                    echo '</audio>';
                    echo '</div>';
                }
            }
            
            echo '</div>'; // .matching-item
        }
        echo '</div>'; // .right-items
        
        echo '</div>'; // .matching-container
        
        echo '<div class="practice-actions mt-6 text-center">';
        echo '<button type="submit" class="submit-practice bg-accent hover:bg-accent-dark text-white font-bold py-2 px-6 rounded-lg transition-colors">' . __('تسليم الإجابات', 'sarah-loz') . '</button>';
        echo '</div>';
        
        echo '<div class="practice-results mt-8 p-6 bg-white border border-gray-200 rounded-lg text-center hidden">';
        echo '<div class="results-message text-xl font-bold mb-4"></div>';
        echo '<div class="results-score text-lg mb-4"></div>';
        echo '<div class="results-feedback mb-6"></div>';
        echo '<button type="button" class="try-again bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">' . __('حاول مرة أخرى', 'sarah-loz') . '</button>';
        echo '</div>';
        
        echo '</form>';
    }

    /**
     * Handle practice answer checking via AJAX
     */
    public function check_practice_answers() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sarah_loz_practices_nonce')) {
            wp_send_json_error(array('message' => __('Security verification failed', 'sarah-loz')));
        }

        // Get practice ID
        $practice_id = isset($_POST['practice_id']) ? intval($_POST['practice_id']) : 0;
        if (!$practice_id) {
            wp_send_json_error(array('message' => __('Invalid practice ID', 'sarah-loz')));
        }

        // Get practice type
        $practice_type = get_field('practice_type', $practice_id);
        if (!$practice_type) {
            wp_send_json_error(array('message' => __('Invalid practice type', 'sarah-loz')));
        }

        // Process answers based on practice type
        if ($practice_type === 'multiple_choice') {
            $this->process_multiple_choice_answers($practice_id);
        } elseif ($practice_type === 'matching') {
            $this->process_matching_answers($practice_id);
        } else {
            wp_send_json_error(array('message' => __('Unsupported practice type', 'sarah-loz')));
        }
    }

    /**
     * Process multiple choice answers
     */
    private function process_multiple_choice_answers($practice_id) {
        $questions = get_field('questions', $practice_id);
        $answers = isset($_POST['answers']) ? $_POST['answers'] : array();
        
        if (empty($questions) || empty($answers)) {
            wp_send_json_error(array('message' => __('No questions or answers provided', 'sarah-loz')));
        }

        $total_questions = count($questions);
        $correct_answers = 0;
        $results = array();

        foreach ($questions as $index => $question) {
            $question_number = $index + 1;
            $question_key = 'q' . $question_number;
            
            if (!isset($answers[$question_key])) {
                $results[$question_key] = array(
                    'correct' => false,
                    'message' => __('لم تتم الإجابة', 'sarah-loz')
                );
                continue;
            }

            $selected_answer = intval($answers[$question_key]);
            $is_correct = false;

            if (isset($question['answers'][$selected_answer]) && $question['answers'][$selected_answer]['is_correct']) {
                $is_correct = true;
                $correct_answers++;
            }

            $results[$question_key] = array(
                'correct' => $is_correct,
                'message' => $is_correct ? __('إجابة صحيحة!', 'sarah-loz') : __('إجابة خاطئة', 'sarah-loz')
            );
        }

        $score_percentage = ($total_questions > 0) ? round(($correct_answers / $total_questions) * 100) : 0;
        
        // Record completion for logged-in users
        if (is_user_logged_in()) {
            $this->record_practice_completion($practice_id, $score_percentage);
        }
        
        wp_send_json_success(array(
            'results' => $results,
            'score' => $correct_answers,
            'total' => $total_questions,
            'percentage' => $score_percentage,
            'message' => $this->get_score_message($score_percentage)
        ));
    }

    /**
     * Process matching answers
     */
    private function process_matching_answers($practice_id) {
        $matching_pairs = get_field('matching_pairs', $practice_id);
        $submitted_matches = isset($_POST['matches']) ? $_POST['matches'] : array();
        
        if (empty($matching_pairs) || empty($submitted_matches)) {
            wp_send_json_error(array('message' => __('No matching pairs or answers provided', 'sarah-loz')));
        }

        $total_pairs = count($matching_pairs);
        $correct_matches = 0;
        $results = array();

        foreach ($submitted_matches as $left_index => $right_correct_match) {
            $left_index = intval($left_index);
            $right_correct_match = intval($right_correct_match);
            
            if ($left_index == $right_correct_match) {
                $correct_matches++;
                $results[$left_index] = array(
                    'correct' => true,
                    'message' => __('توصيل صحيح!', 'sarah-loz')
                );
            } else {
                $results[$left_index] = array(
                    'correct' => false,
                    'message' => __('توصيل خاطئ', 'sarah-loz')
                );
            }
        }

        $score_percentage = ($total_pairs > 0) ? round(($correct_matches / $total_pairs) * 100) : 0;
        
        // Record completion for logged-in users
        if (is_user_logged_in()) {
            $this->record_practice_completion($practice_id, $score_percentage);
        }
        
        wp_send_json_success(array(
            'results' => $results,
            'score' => $correct_matches,
            'total' => $total_pairs,
            'percentage' => $score_percentage,
            'message' => $this->get_score_message($score_percentage)
        ));
    }

    /**
     * Record practice completion for a user
     */
    private function record_practice_completion($practice_id, $score_percentage) {
        $user_id = get_current_user_id();
        
        // Get user's completed practices
        $completed_practices = get_user_meta($user_id, 'completed_practices', true);
        if (!is_array($completed_practices)) {
            $completed_practices = array();
        }
        
        // Add this practice to the completed list if not already there
        if (!in_array($practice_id, $completed_practices)) {
            $completed_practices[] = $practice_id;
            update_user_meta($user_id, 'completed_practices', $completed_practices);
        }
        
        // Update practice scores
        $practice_scores = get_user_meta($user_id, 'practice_scores', true);
        if (!is_array($practice_scores)) {
            $practice_scores = array();
        }
        
        // Record the score (or update if it's better than previous attempt)
        if (!isset($practice_scores[$practice_id]) || $score_percentage > $practice_scores[$practice_id]) {
            $practice_scores[$practice_id] = $score_percentage;
            update_user_meta($user_id, 'practice_scores', $practice_scores);
        }
        
        // Award practice points
        $points = get_field('points', $practice_id);
        if (!$points) {
            $points = 10; // Default points
        }
        
        // Adjust points based on score percentage
        $awarded_points = round(($points * $score_percentage) / 100);
        
        if ($awarded_points > 0) {
            $current_points = get_user_meta($user_id, 'total_points', true);
            $current_points = $current_points ? intval($current_points) + $awarded_points : $awarded_points;
            update_user_meta($user_id, 'total_points', $current_points);
        }
        
        // Trigger achievement check
        do_action('sarah_loz_practice_completed', $user_id, $practice_id, $score_percentage);
    }

    /**
     * Get score message based on percentage
     */
    private function get_score_message($percentage) {
        if ($percentage >= 90) {
            return __('ممتاز! أحسنت!', 'sarah-loz');
        } elseif ($percentage >= 75) {
            return __('جيد جداً! استمر!', 'sarah-loz');
        } elseif ($percentage >= 50) {
            return __('جيد، واصل المحاولة!', 'sarah-loz');
        } else {
            return __('حاول مرة أخرى، أنت تستطيع!', 'sarah-loz');
        }
    }

    /**
     * Get message for content requirement
     */
    private function get_content_requirement_message($content_id) {
        $content_type = get_post_type($content_id);
        $content_title = get_the_title($content_id);
        
        $message = '<div class="practice-requirement p-4 bg-yellow-100 text-yellow-800 border border-yellow-200 rounded-lg">';
        
        if ($content_type === 'video') {
            $message .= '<p>' . sprintf(__('يجب مشاهدة الفيديو "%s" أولاً قبل القيام بهذا التدريب.', 'sarah-loz'), $content_title) . '</p>';
        } elseif ($content_type === 'activity') {
            $message .= '<p>' . sprintf(__('يجب إكمال النشاط "%s" أولاً قبل القيام بهذا التدريب.', 'sarah-loz'), $content_title) . '</p>';
        }
        
        $message .= '<p class="mt-2"><a href="' . get_permalink($content_id) . '" class="font-bold text-accent hover:underline">' . __('انتقل الآن', 'sarah-loz') . ' &raquo;</a></p>';
        $message .= '</div>';
        
        return $message;
    }
}

// Initialize the practices system
function sarah_loz_practices() {
    return Sarah_Loz_Practices::get_instance();
}
add_action('after_setup_theme', 'sarah_loz_practices'); 