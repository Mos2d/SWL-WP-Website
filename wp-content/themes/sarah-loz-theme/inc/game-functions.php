<?php
/**
 * Game Functions
 * Functions related to interactive games
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Track game progress via AJAX
 */
function sarah_loz_track_game_progress() {
    // Check for required data
    if (!isset($_POST['game_id']) || !isset($_POST['score']) || !isset($_POST['nonce'])) {
        wp_send_json_error('Missing required data');
        return;
    }
    
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'sarah_loz_game_progress')) {
        wp_send_json_error('Security check failed');
        return;
    }
    
    // Get user ID (if logged in)
    $user_id = get_current_user_id();
    $game_id = intval($_POST['game_id']);
    
    // Save score and other metrics
    $score = intval($_POST['score']);
    $time = isset($_POST['time']) ? intval($_POST['time']) : 0;
    $completed = isset($_POST['completed']) ? (bool)$_POST['completed'] : false;
    
    // Increment game play count
    $play_count = (int)get_post_meta($game_id, 'game_play_count', true);
    update_post_meta($game_id, 'game_play_count', $play_count + 1);
    
    // Store high score if this is the highest
    $high_score = (int)get_post_meta($game_id, 'game_high_score', true);
    if ($score > $high_score) {
        update_post_meta($game_id, 'game_high_score', $score);
    }
    
    // Record user-specific data if logged in
    if ($user_id > 0) {
        // Get user's game history
        $user_games = get_user_meta($user_id, 'user_games', true);
        if (!is_array($user_games)) {
            $user_games = array();
        }
        
        // Update user's game data
        if (!isset($user_games[$game_id])) {
            $user_games[$game_id] = array(
                'plays' => 0,
                'high_score' => 0,
                'completed' => false,
                'last_played' => current_time('mysql')
            );
        }
        
        $user_games[$game_id]['plays']++;
        $user_games[$game_id]['last_played'] = current_time('mysql');
        
        if ($score > $user_games[$game_id]['high_score']) {
            $user_games[$game_id]['high_score'] = $score;
        }
        
        if ($completed) {
            $user_games[$game_id]['completed'] = true;
            
            // Add to completed games list for progress tracking
            $completed_games = get_user_meta($user_id, 'completed_games', true);
            if (!is_array($completed_games)) {
                $completed_games = array();
            }
            if (!in_array($game_id, $completed_games)) {
                $completed_games[] = $game_id;
                update_user_meta($user_id, 'completed_games', $completed_games);
            }
        }
        
        // Save user game data
        update_user_meta($user_id, 'user_games', $user_games);
        
        // Log this play in a separate log for analytics
        $play_log = get_user_meta($user_id, 'game_play_log', true);
        if (!is_array($play_log)) {
            $play_log = array();
        }
        
        $play_log[] = array(
            'game_id' => $game_id,
            'score' => $score,
            'time' => $time,
            'completed' => $completed,
            'date' => current_time('mysql')
        );
        
        // Keep only the last 100 plays to avoid meta bloat
        if (count($play_log) > 100) {
            $play_log = array_slice($play_log, -100);
        }
        
        update_user_meta($user_id, 'game_play_log', $play_log);
    }
    
    // Return success response
    wp_send_json_success(array(
        'message' => 'Game progress tracked successfully',
        'score' => $score,
        'high_score' => $high_score,
        'is_high_score' => ($score > $high_score)
    ));
}
add_action('wp_ajax_sarah_loz_track_game_progress', 'sarah_loz_track_game_progress');
add_action('wp_ajax_nopriv_sarah_loz_track_game_progress', 'sarah_loz_track_game_progress');

/**
 * Enqueue game scripts
 */
function sarah_loz_enqueue_game_scripts() {
    // Only enqueue on game single pages
    if (is_singular('game')) {
        // Enqueue basic scripts
        wp_enqueue_script('sarah-loz-game-framework', get_template_directory_uri() . '/assets/js/games/game-framework.js', array('jquery'), '1.0.0', true);
        
        // Add game-specific scripts based on the game type
        $game_id = get_the_ID();
        $game_type = get_field('game_type', $game_id);
        $interactive_type = get_field('interactive_game_type', $game_id);
        
        if ($game_type === 'interactive') {
            // Add specific game script based on type
            if ($interactive_type === 'memory') {
                // Enqueue memory game CSS
                wp_enqueue_style('sarah-loz-memory-game', get_template_directory_uri() . '/assets/css/memory-game-responsive.css', array(), '1.0.0');
                
                if (WP_DEBUG) {
                    // Use debug version in development
                    wp_enqueue_script('sarah-loz-memory-game', get_template_directory_uri() . '/assets/js/games/memory-game-debug.js', array('sarah-loz-game-framework'), '1.0.0', true);
                } else {
                    // Use production version
                    wp_enqueue_script('sarah-loz-memory-game', get_template_directory_uri() . '/assets/js/games/memory-game.js', array('sarah-loz-game-framework'), '1.0.0', true);
                }
            } else if ($interactive_type === 'quiz') {
                // Enqueue quiz game CSS
                wp_enqueue_style('sarah-loz-quiz-game', get_template_directory_uri() . '/assets/css/quiz-game-responsive.css', array(), '1.0.0');
                
                // Enqueue quiz game JS
                wp_enqueue_script('sarah-loz-quiz-game', get_template_directory_uri() . '/assets/js/games/quiz-game.js', array('sarah-loz-game-framework'), '1.0.0', true);
            } else if ($interactive_type === 'sorting') {
                // Enqueue jQuery UI for sorting game (if not already included by WordPress)
                wp_enqueue_script('jquery-ui-draggable');
                wp_enqueue_script('jquery-ui-droppable');
                
                // Enqueue sorting game CSS
                wp_enqueue_style('sarah-loz-sorting-game', get_template_directory_uri() . '/assets/css/sorting-game-responsive.css', array(), '1.0.0');
                
                // Enqueue sorting game JS
                wp_enqueue_script('sarah-loz-sorting-game', get_template_directory_uri() . '/assets/js/games/sorting-game.js', array('sarah-loz-game-framework', 'jquery-ui-draggable', 'jquery-ui-droppable'), '1.0.0', true);
                
                // Optionally enqueue jQuery UI Touch Punch for better mobile support
                wp_enqueue_script('jquery-ui-touch-punch', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js', array('jquery-ui-draggable', 'jquery-ui-droppable'), '0.2.3', true);
            }
            // Add other game types as needed
        }
        
        // Localize script with AJAX URL and game settings
        $game_settings = sarah_loz_get_game_settings_for_js($game_id);
        
        wp_localize_script('sarah-loz-game-framework', 'sarahLozGame', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sarah_loz_game_progress'),
            'gameId' => $game_id,
            'settings' => $game_settings
        ));
    }
}
add_action('wp_enqueue_scripts', 'sarah_loz_enqueue_game_scripts');

/**
 * Get game settings for JavaScript initialization
 */
function sarah_loz_get_game_settings_for_js($game_id) {
    $game_type = get_field('game_type', $game_id);
    $interactive_type = get_field('interactive_game_type', $game_id);
    $settings = array();
    
    if ($game_type === 'interactive') {
        // Add common game settings
        $settings['gameId'] = $game_id;
        $settings['nonce'] = wp_create_nonce('sarah_loz_game_progress');
        
        // Add specific settings based on game type
        if ($interactive_type === 'memory') {
            // Get memory game settings
            $memory_settings = get_field('memory_game_settings', $game_id);
            
            // Set difficulty
            $difficulty = !empty($memory_settings['memory_game_difficulty']) ? 
                $memory_settings['memory_game_difficulty'] : 'medium';
            $settings['difficulty'] = $difficulty;
            
            // Set card images
            $card_images = !empty($memory_settings['memory_game_card_images']) ? 
                $memory_settings['memory_game_card_images'] : array();
            $settings['cardImages'] = $card_images;
            
            // Set card back image
            $card_back = !empty($memory_settings['memory_game_card_back']) ? 
                $memory_settings['memory_game_card_back'] : '';
            $settings['cardBackImage'] = $card_back;
            
            // Set sounds
            if (!empty($memory_settings['memory_game_sounds'])) {
                $settings['matchSound'] = $memory_settings['memory_game_sounds']['match_sound'] ?? '';
                $settings['errorSound'] = $memory_settings['memory_game_sounds']['error_sound'] ?? '';
            }
            
            // Set animation settings
            if (!empty($memory_settings['memory_game_animation'])) {
                $settings['cardFlipDuration'] = (int)($memory_settings['memory_game_animation']['card_flip_duration'] ?? 500);
            }
            
            // Set responsive settings
            if (!empty($memory_settings['memory_game_responsive'])) {
                $settings['mobileResponsive'] = (bool)($memory_settings['memory_game_responsive']['enable_mobile_responsive'] ?? true);
                $settings['adaptiveGridSize'] = (bool)($memory_settings['memory_game_responsive']['adaptive_grid_size'] ?? true);
                $settings['minMobileWidth'] = (int)($memory_settings['memory_game_responsive']['min_mobile_width'] ?? 320);
            }
        }
        
        // Add other game types as needed
    }
    
    return $settings;
}

/**
 * Add game data to REST API
 */
function sarah_loz_register_game_rest_fields() {
    // Add game settings to REST API
    register_rest_field('game', 'game_settings', array(
        'get_callback' => 'sarah_loz_get_game_settings',
        'schema' => null,
    ));
}
add_action('rest_api_init', 'sarah_loz_register_game_rest_fields');

/**
 * Get game settings for REST API
 */
function sarah_loz_get_game_settings($post) {
    $game_type = get_field('game_type', $post['id']);
    $interactive_type = get_field('interactive_game_type', $post['id']);
    $game_settings = get_field('game_settings', $post['id']);
    
    $settings = array(
        'game_type' => $game_type,
        'interactive_type' => $interactive_type,
        'settings' => $game_settings
    );
    
    // Add type-specific settings
    if ($game_type === 'interactive') {
        switch ($interactive_type) {
            case 'memory':
                $settings['memory_settings'] = get_field('memory_game_settings', $post['id']);
                break;
                
            case 'quiz':
                $settings['quiz_settings'] = get_field('quiz_game_settings', $post['id']);
                break;
        }
    }
    
    return $settings;
}

/**
 * Display game leaderboard
 */
function sarah_loz_game_leaderboard($game_id, $limit = 10) {
    global $wpdb;
    
    // Get users with logged scores for this game
    $users_with_scores = $wpdb->get_results($wpdb->prepare(
        "SELECT user_id, meta_value FROM {$wpdb->usermeta}
         WHERE meta_key = 'user_games'
         ORDER BY meta_id DESC",
    ));
    
    $leaderboard = array();
    
    foreach ($users_with_scores as $user_data) {
        $games = maybe_unserialize($user_data->meta_value);
        
        if (is_array($games) && isset($games[$game_id]) && isset($games[$game_id]['high_score'])) {
            $user_info = get_userdata($user_data->user_id);
            
            if ($user_info) {
                $leaderboard[] = array(
                    'user_id' => $user_data->user_id,
                    'display_name' => $user_info->display_name,
                    'high_score' => $games[$game_id]['high_score'],
                    'plays' => $games[$game_id]['plays'],
                    'last_played' => isset($games[$game_id]['last_played']) ? $games[$game_id]['last_played'] : ''
                );
            }
        }
    }
    
    // Sort by high score (descending)
    usort($leaderboard, function($a, $b) {
        return $b['high_score'] - $a['high_score'];
    });
    
    // Limit results
    $leaderboard = array_slice($leaderboard, 0, $limit);
    
    return $leaderboard;
}

/**
 * Game leaderboard shortcode
 */
function sarah_loz_leaderboard_shortcode($atts) {
    $atts = shortcode_atts(array(
        'game_id' => 0,
        'limit' => 10,
        'title' => 'قائمة المتصدرين'
    ), $atts, 'game_leaderboard');
    
    $game_id = intval($atts['game_id']);
    
    if ($game_id === 0 && is_singular('game')) {
        $game_id = get_the_ID();
    }
    
    if ($game_id === 0) {
        return '<div class="bg-yellow-100 text-yellow-800 p-4 rounded-lg">يرجى تحديد معرف اللعبة.</div>';
    }
    
    $leaderboard = sarah_loz_game_leaderboard($game_id, intval($atts['limit']));
    
    if (empty($leaderboard)) {
        return '<div class="bg-gray-100 p-4 rounded-lg text-center">لا توجد بيانات متاحة في قائمة المتصدرين حتى الآن.</div>';
    }
    
    ob_start();
    ?>
    <div class="sarah-loz-leaderboard bg-white rounded-lg shadow-lg overflow-hidden">
        <h3 class="text-xl font-bold p-4 bg-primary text-white"><?php echo esc_html($atts['title']); ?></h3>
        
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr class="text-right">
                    <th class="p-3">#</th>
                    <th class="p-3">اللاعب</th>
                    <th class="p-3">النقاط</th>
                    <th class="p-3">عدد اللعب</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leaderboard as $index => $player) : ?>
                    <tr class="border-b <?php echo ($index === 0) ? 'bg-yellow-50' : ''; ?>">
                        <td class="p-3 font-bold"><?php echo $index + 1; ?></td>
                        <td class="p-3"><?php echo esc_html($player['display_name']); ?></td>
                        <td class="p-3 font-bold"><?php echo esc_html($player['high_score']); ?></td>
                        <td class="p-3"><?php echo esc_html($player['plays']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('game_leaderboard', 'sarah_loz_leaderboard_shortcode'); 