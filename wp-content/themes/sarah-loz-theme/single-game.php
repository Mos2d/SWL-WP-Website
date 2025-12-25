<?php 
// Check if we have an age group selected
$selected_age_group = sarah_loz_get_selected_age_group();
$current_age_data = sarah_loz_get_current_age_group_data();

get_header(); ?>
<script src="https://cdn.tailwindcss.com"></script>
<!-- Enhanced Responsive Game Page Styles -->
<style>
    /* Game Page Container */
    .game-page-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 0;
        margin: 0;
    }
    
    /* Game Header */
    .game-header {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        position: sticky;
        top: 0;
        z-index: 100;
        padding: 1rem 0;
    }
    
    /* Game Main Content */
    .game-main {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
        padding: 2rem 1rem;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    @media (min-width: 1024px) {
        .game-main {
            grid-template-columns: 1fr 350px;
            padding: 2rem;
        }
    }
    
    /* Game Container - Full Responsive */
    .game-container {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        position: relative;
        min-height: 70vh;
    }
    
    @media (max-width: 768px) {
        .game-container {
            border-radius: 16px;
            min-height: 60vh;
        }
    }
    
    @media (max-width: 480px) {
        .game-container {
            border-radius: 12px;
            min-height: 50vh;
            margin: 0 0.5rem;
        }
    }
    
    /* Interactive Game Container - Fully Responsive */
    .interactive-game-container {
        position: relative;
        width: 100%;
        height: 100%;
        min-height: 500px;
        transition: all 0.3s ease;
        border-radius: inherit;
        overflow: hidden;
    }
    
    @media (max-width: 768px) {
        .interactive-game-container {
            min-height: 400px;
        }
    }
    
    @media (max-width: 480px) {
        .interactive-game-container {
            min-height: 350px;
        }
    }
    
    @media (max-height: 600px) and (orientation: landscape) {
        .interactive-game-container {
            min-height: 300px;
        }
    }
    
    /* Game Sidebar */
    .game-sidebar {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    @media (max-width: 1023px) {
        .game-sidebar {
            order: -1;
        }
    }
    
    /* Game Info Card */
    .game-info-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    @media (max-width: 768px) {
        .game-info-card {
            padding: 1rem;
            border-radius: 16px;
        }
    }
    
    /* Age Group Banner - Responsive */
    .age-group-banner {
        background: linear-gradient(45deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 16px;
        text-align: center;
        margin-bottom: 1rem;
        font-weight: bold;
        font-size: clamp(0.9rem, 2.5vw, 1.1rem);
    }
    
    /* Game Title - Responsive */
    .game-title {
        font-size: clamp(1.5rem, 4vw, 2.5rem);
        font-weight: 800;
        margin-bottom: 1rem;
        color: #2d3748;
        line-height: 1.2;
    }
    
    /* Game Meta Tags - Responsive */
    .game-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }
    
    .game-meta-tag {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: clamp(0.75rem, 2vw, 0.875rem);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    /* Game Description - Responsive */
    .game-description {
        font-size: clamp(0.9rem, 2.5vw, 1rem);
        line-height: 1.6;
        color: #4a5568;
        margin-bottom: 1.5rem;
    }
    
    /* Loading Spinner - Enhanced */
    .game-loader {
        position: absolute !important;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        z-index: 200;
        border-radius: inherit;
    }
    
    .loading-spinner {
        width: 60px;
        height: 60px;
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-left-color: #ffffff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 1rem;
    }
    
    .loading-text {
        font-size: clamp(1rem, 3vw, 1.2rem);
        font-weight: 600;
        text-align: center;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Mobile Optimization Messages */
    .mobile-tip {
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.2);
        color: #1e40af;
        padding: 1rem;
        border-radius: 12px;
        font-size: clamp(0.8rem, 2.5vw, 0.9rem);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    
    @media (min-width: 768px) {
        .mobile-tip {
            display: none;
        }
    }
    
    /* Related Games - Responsive Grid */
    .related-games-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 3rem;
    }
    
    @media (max-width: 640px) {
        .related-games-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }
    
    .related-game-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .related-game-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    /* Responsive Image Containers */
    .responsive-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    
    @media (max-width: 640px) {
        .responsive-image {
            height: 150px;
        }
    }
    
    /* Button Styles - Responsive */
    .game-button {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: clamp(0.9rem, 2.5vw, 1rem);
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .game-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }
    
    /* Container Padding for Mobile */
    .container-responsive {
        width: 100%;
        max-width: 100%;
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    @media (min-width: 640px) {
        .container-responsive {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }
    }
    
    @media (min-width: 1024px) {
        .container-responsive {
            padding-left: 2rem;
            padding-right: 2rem;
        }
    }
    
    /* Fullscreen mode for mobile games */
    .game-fullscreen {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1000;
        background: #000;
    }
    
    .game-fullscreen .interactive-game-container {
        width: 100vw;
        height: 100vh;
        min-height: 100vh;
        border-radius: 0;
    }
    
    /* Print styles */
    @media print {
        .game-page-container {
            background: white !important;
        }
        
        .interactive-game-container {
            display: none !important;
        }
    }
</style>

<div class="game-page-container">
    <?php while (have_posts()) : the_post(); ?>
        
        <!-- Game Header -->
        <header class="game-header">
            <div class="container-responsive">
                <!-- Age Group Banner -->
                <?php if ($selected_age_group && $current_age_data) : ?>
                    <div class="age-group-banner">
                        <div class="flex items-center justify-center gap-2">
                            <?php if (isset($current_age_data['custom_image'])) : ?>
                                <img src="<?php echo esc_url($current_age_data['custom_image']); ?>" alt="<?php echo esc_attr($current_age_data['label']); ?>" class="w-6 h-6 object-cover rounded-full">
                            <?php else : ?>
                                <span class="text-xl"><?php echo $current_age_data['icon']; ?></span>
                            <?php endif; ?>
                            <span>
                                لعبة مناسبة لـ<?php echo $current_age_data['label']; ?> (<?php echo $selected_age_group; ?> سنوات)! ✨
                            </span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <!-- Main Game Content -->
        <main class="game-main">
            <!-- Game Container (Left Side) -->
            <div class="game-container">

                    <?php
                    // Get game type
                    $game_type = get_field('game_type');
                    
                    if ($game_type === 'embed') :
                        // External Game Embed
                        if ($game_url = get_field('game_url')) : ?>
                        <div class="aspect-w-16 aspect-h-9 bg-gray-100 rounded-lg overflow-hidden">
                            <?php echo wp_kses_post($game_url); ?>
                        </div>
                        <?php endif;
                    else :
                        // Interactive Game
                        $interactive_game_type = get_field('interactive_game_type');
                        $game_id = 'game-' . get_the_ID();
                        $game_settings = get_field('game_settings');
                        
                        // Set default values if not available
                        $width = isset($game_settings['width']) ? intval($game_settings['width']) : 800;
                        $height = isset($game_settings['height']) ? intval($game_settings['height']) : 600;
                        $bg_color = isset($game_settings['background_color']) ? esc_attr($game_settings['background_color']) : '#f0f0f0';
                        $show_score = isset($game_settings['show_score']) ? (bool)$game_settings['show_score'] : true;
                        $show_timer = isset($game_settings['show_timer']) ? (bool)$game_settings['show_timer'] : true;
                        $auto_start = isset($game_settings['auto_start']) ? (bool)$game_settings['auto_start'] : false;
                        
                        // Determine if game prefers landscape orientation
                        $landscape_preferred = $width > $height ? 'landscape-preferred' : '';
                        ?>
                        
                        <!-- Interactive Game Container -->
                        <div id="<?php echo esc_attr($game_id); ?>" class="interactive-game-container <?php echo $landscape_preferred; ?>">
                            <!-- Enhanced Loading indicator -->
                            <div id="<?php echo esc_attr($game_id); ?>-loader" class="game-loader">
                                <div class="loading-spinner"></div>
                                <div class="loading-text">🎮 جاري تحميل اللعبة...</div>
                                <div style="font-size: 0.9rem; opacity: 0.8; margin-top: 0.5rem;">يرجى الانتظار قليلاً</div>
                            </div>
                        </div>
                        
                        <?php
                        // Enqueue necessary scripts
                        wp_enqueue_script('sarah-loz-game-framework', get_template_directory_uri() . '/assets/js/games/game-framework.js', array('jquery'), '1.0.0', true);
                        
                        // Enqueue specific game script based on type
                        switch ($interactive_game_type) {
                            case 'memory':
                                wp_enqueue_script('sarah-loz-game-framework', get_template_directory_uri() . '/assets/js/games/game-framework.js', array('jquery'), '1.0.0', true);
                                // Use debug version of memory game instead of regular version
                                wp_enqueue_script('sarah-loz-memory-game', get_template_directory_uri() . '/assets/js/games/memory-game-debug.js', array('sarah-loz-game-framework'), '1.0.0', true);
                                // Remove debug script since we're using the debug version of the game
                                // wp_enqueue_script('sarah-loz-memory-game-debug', get_template_directory_uri() . '/assets/js/games/debug-memory-game.js', array('sarah-loz-memory-game'), '1.0.0', true);
                                
                                // Ensure sarahLozGame variable is available to the memory game script
                                wp_localize_script('sarah-loz-memory-game', 'sarahLozGame', array(
                                    'ajaxUrl' => admin_url('admin-ajax.php'),
                                    'nonce' => wp_create_nonce('sarah_loz_game_progress'),
                                    'isMobile' => wp_is_mobile()
                                ));
                                
                                $memory_settings = get_field('memory_game_settings');
                                
                                // Prepare card images array
                                $card_images = array();
                                if (!empty($memory_settings['card_pairs'])) {
                                    foreach ($memory_settings['card_pairs'] as $pair) {
                                        if (!empty($pair['image'])) {
                                            $card_images[] = $pair['image'];
                                        }
                                    }
                                }
                                
                                // Add placeholder images if no images are found
                                if (empty($card_images)) {
                                    $card_images = array(
                                        'https://via.placeholder.com/150/FF5733/FFFFFF?text=1',
                                        'https://via.placeholder.com/150/33FF57/000000?text=2',
                                        'https://via.placeholder.com/150/5733FF/FFFFFF?text=3',
                                        'https://via.placeholder.com/150/FFFF33/000000?text=4',
                                        'https://via.placeholder.com/150/33FFFF/000000?text=5',
                                        'https://via.placeholder.com/150/FF33FF/FFFFFF?text=6',
                                        'https://via.placeholder.com/150/FFFF33/000000?text=7',
                                        'https://via.placeholder.com/150/3333FF/FFFFFF?text=8'
                                    );
                                }
                                
                                // Initialize game
                                ?>
                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    console.log('Initializing Memory Game');
                                    console.log('Game container ID:', '<?php echo esc_attr($game_id); ?>');
                                    console.log('Memory settings:', <?php echo json_encode($memory_settings); ?>);
                                    console.log('Card images:', <?php echo json_encode($card_images); ?>);
                                    
                                    try {
                                        const game = new MemoryGame('<?php echo esc_attr($game_id); ?>', {
                                            width: <?php echo $width; ?>,
                                            height: <?php echo $height; ?>,
                                            backgroundColor: '<?php echo $bg_color; ?>',
                                            showScore: <?php echo $show_score ? 'true' : 'false'; ?>,
                                            showTimer: <?php echo $show_timer ? 'true' : 'false'; ?>,
                                            autoStart: <?php echo $auto_start ? 'true' : 'false'; ?>,
                                            difficulty: '<?php echo esc_js(get_field('difficulty_level')); ?>',
                                            cardBackImage: '<?php echo !empty($memory_settings['card_back_image']) ? esc_url($memory_settings['card_back_image']) : ''; ?>',
                                            cardImages: <?php echo json_encode($card_images); ?>,
                                            matchSound: '<?php echo !empty($memory_settings['match_sound']) ? esc_url($memory_settings['match_sound']) : ''; ?>',
                                            errorSound: '<?php echo !empty($memory_settings['error_sound']) ? esc_url($memory_settings['error_sound']) : ''; ?>',
                                            gameId: <?php echo get_the_ID(); ?>,
                                            nonce: '<?php echo wp_create_nonce('sarah_loz_game_progress'); ?>',
                                            mobileResponsive: true,
                                            adaptiveGridSize: true
                                        });
                                        console.log('Memory game initialized successfully');
                                        
                                        // Hide loader when game is ready
                                        const loader = document.getElementById('<?php echo esc_attr($game_id); ?>-loader');
                                        if (loader) {
                                            loader.style.display = 'none';
                                        }
                                    } catch (error) {
                                        console.error('Error initializing memory game:', error);
                                    }
                                });
                                </script>
                                <?php
                                break;
                                
                            case 'quiz':
                                wp_enqueue_script('sarah-loz-quiz-game', get_template_directory_uri() . '/assets/js/games/quiz-game.js', array('sarah-loz-game-framework'), '1.0.0', true);
                                $quiz_settings = get_field('quiz_game_settings');
                                
                                // Prepare questions array
                                $questions = array();
                                if (!empty($quiz_settings['questions'])) {
                                    foreach ($quiz_settings['questions'] as $question) {
                                        $answers = array();
                                        if (!empty($question['answers'])) {
                                            foreach ($question['answers'] as $answer) {
                                                $answers[] = array(
                                                    'text' => $answer['text'],
                                                    'correct' => (bool)$answer['correct']
                                                );
                                            }
                                        }
                                        
                                        $questions[] = array(
                                            'text' => $question['text'],
                                            'image' => !empty($question['image']) ? $question['image'] : '',
                                            'difficulty' => $question['difficulty'],
                                            'explanation' => !empty($question['explanation']) ? $question['explanation'] : '',
                                            'answers' => $answers
                                        );
                                    }
                                }
                                
                                // Initialize game
                                ?>
                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    try {
                                        const game = new QuizGame('<?php echo esc_attr($game_id); ?>', {
                                            width: <?php echo $width; ?>,
                                            height: <?php echo $height; ?>,
                                            backgroundColor: '<?php echo $bg_color; ?>',
                                            showScore: <?php echo $show_score ? 'true' : 'false'; ?>,
                                            showTimer: <?php echo $show_timer ? 'true' : 'false'; ?>,
                                            autoStart: <?php echo $auto_start ? 'true' : 'false'; ?>,
                                            difficulty: '<?php echo esc_js(get_field('difficulty_level')); ?>',
                                            questions: <?php echo json_encode($questions); ?>,
                                            shuffleQuestions: <?php echo !empty($quiz_settings['shuffle_questions']) ? 'true' : 'false'; ?>,
                                            shuffleAnswers: <?php echo !empty($quiz_settings['shuffle_answers']) ? 'true' : 'false'; ?>,
                                            timePerQuestion: <?php echo !empty($quiz_settings['time_per_question']) ? intval($quiz_settings['time_per_question']) : 0; ?>,
                                            showExplanations: <?php echo !empty($quiz_settings['show_explanations']) ? 'true' : 'false'; ?>,
                                            gameId: <?php echo get_the_ID(); ?>,
                                            nonce: '<?php echo wp_create_nonce('sarah_loz_game_progress'); ?>',
                                            mobileResponsive: true,
                                            adaptiveFontSize: true
                                        });
                                        console.log('Quiz game initialized successfully');
                                        
                                        // Hide loader when game is ready
                                        const loader = document.getElementById('<?php echo esc_attr($game_id); ?>-loader');
                                        if (loader) {
                                            loader.style.display = 'none';
                                        }
                                    } catch (error) {
                                        console.error('Error initializing quiz game:', error);
                                    }
                                });
                                </script>
                                <?php
                                break;
                                
                            case 'assessment':
                                // 1. Get Settings
                                $assess_data = get_field('assessment_settings');
                                
                                // 2. Prepare Config
                                $game_config = array(
                                    'introAudio' => !empty($assess_data['intro_audio']) ? $assess_data['intro_audio'] : '',
                                    'questions'  => array(),
                                    'debugMode'  => true
                                );

                                // 3. Process Questions
                                if (!empty($assess_data['questions']) && is_array($assess_data['questions'])) {
                                    foreach ($assess_data['questions'] as $i => $q) {
                                        
                                        // Category Logic
                                        $cat = 'general';
                                        if (!empty($q['criteria_category'])) { $cat = $q['criteria_category']; }
                                        elseif (!empty($q['criteria'])) { $cat = $q['criteria']; }
                                        elseif (!empty($q['category'])) { $cat = $q['category']; }

                                        // Answer Logic
                                        $answers = array();
                                        if(!empty($q['answers']) && is_array($q['answers'])){
                                            foreach($q['answers'] as $a){
                                                $is_correct_flag = false;
                                                if (!empty($a['correct'])) { $is_correct_flag = true; } 
                                                elseif (!empty($a['is_correct'])) { $is_correct_flag = true; }

                                                $answers[] = array(
                                                    'text'    => !empty($a['text']) ? $a['text'] : '',
                                                    'correct' => $is_correct_flag
                                                );
                                            }
                                        }

                                        // Add to Config
                                        $game_config['questions'][] = array(
                                            'id'       => $i,
                                            'text'     => $q['text'],
                                            'audio'    => !empty($q['audio_prompt']) ? $q['audio_prompt'] : (!empty($q['audio']) ? $q['audio'] : ''),
                                            'image'    => !empty($q['image']) ? $q['image'] : '',
                                            'criteria_category' => $cat,
                                            'answers'  => $answers
                                        );
                                    }
                                }

                                // 4. Initialize Script (TARGETING THE THEME CONTAINER)
                                // Note: We use $game_id which your theme created on line 287
                                ?>
                                <script src="<?php echo get_template_directory_uri(); ?>/assets/js/games/assessment-game.js?v=<?php echo time(); ?>"></script>
                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    // Use the ID generated by the theme (e.g., "game-123")
                                    const containerId = '<?php echo esc_js($game_id); ?>'; 
                                    const config = <?php echo json_encode($game_config); ?>;
                                    
                                    console.log("🚀 Assessment Game Target:", containerId);

                                    // Wait a tiny bit to ensure DOM is ready
                                    setTimeout(() => {
                                        if (typeof AssessmentGame !== 'undefined') {
                                            const game = new AssessmentGame(containerId, config);
                                            game.init();
                                        }
                                    }, 100);
                                });
                                </script>
                                <?php
                                break;

                            case 'sorting':
                                // Enqueue jQuery UI for drag and drop functionality
                                wp_enqueue_script('jquery-ui-core');
                                wp_enqueue_script('jquery-ui-draggable');
                                wp_enqueue_script('jquery-ui-droppable');
                                wp_enqueue_style('jquery-ui-style', 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css', array(), '1.13.2');
                                
                                // Enqueue sorting game script
                                wp_enqueue_script('sarah-loz-sorting-game', get_template_directory_uri() . '/assets/js/games/sorting-game.js', array('sarah-loz-game-framework', 'jquery-ui-draggable', 'jquery-ui-droppable'), '1.0.0', true);
                                $sorting_settings = get_field('sorting_game_settings');
                                
                                // Localize the script with WordPress AJAX URL
                                wp_localize_script('sarah-loz-sorting-game', 'sarahLozGame', array(
                                    'ajaxUrl' => admin_url('admin-ajax.php'),
                                    'nonce' => wp_create_nonce('sarah_loz_game_progress'),
                                    'isMobile' => wp_is_mobile()
                                ));
                                
                                // Prepare categories array
                                $categories = array();
                                if (!empty($sorting_settings['categories'])) {
                                    foreach ($sorting_settings['categories'] as $category) {
                                        $categories[] = array(
                                            'id' => $category['id'],
                                            'name' => $category['name']
                                        );
                                    }
                                }
                                
                                // Prepare items array
                                $items = array();
                                if (!empty($sorting_settings['items'])) {
                                    foreach ($sorting_settings['items'] as $item) {
                                        $items[] = array(
                                            'id' => $item['id'],
                                            'text' => !empty($item['text']) ? $item['text'] : '',
                                            'image' => !empty($item['image']) ? $item['image'] : '',
                                            'difficulty' => $item['difficulty'],
                                            'category' => !empty($item['category']) ? $item['category'] : '',
                                            'position' => !empty($item['position']) ? (int)$item['position'] : 0
                                        );
                                    }
                                }
                                
                                // Ensure we have at least some items for testing if there are none
                                if (empty($items)) {
                                    $default_items = array(
                                        array('id' => 'item1', 'text' => 'Item 1', 'difficulty' => 'easy', 'position' => 1),
                                        array('id' => 'item2', 'text' => 'Item 2', 'difficulty' => 'easy', 'position' => 2),
                                        array('id' => 'item3', 'text' => 'Item 3', 'difficulty' => 'easy', 'position' => 3)
                                    );
                                    
                                    // Set default items based on game mode
                                    if (!empty($sorting_settings['is_sequence'])) {
                                        $items = $default_items;
                                    } else {
                                        if (empty($categories)) {
                                            $categories = array(
                                                array('id' => 'cat1', 'name' => 'Category 1'),
                                                array('id' => 'cat2', 'name' => 'Category 2')
                                            );
                                        }
                                        $items = array(
                                            array('id' => 'item1', 'text' => 'Item 1', 'difficulty' => 'easy', 'category' => 'cat1'),
                                            array('id' => 'item2', 'text' => 'Item 2', 'difficulty' => 'easy', 'category' => 'cat1'),
                                            array('id' => 'item3', 'text' => 'Item 3', 'difficulty' => 'easy', 'category' => 'cat2')
                                        );
                                    }
                                }
                                
                                // Initialize game
                                ?>
                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    console.log('Initializing Sorting Game');
                                    console.log('Game container ID:', '<?php echo esc_attr($game_id); ?>');
                                    console.log('Sorting settings:', <?php echo json_encode($sorting_settings); ?>);
                                    console.log('Categories:', <?php echo json_encode($categories); ?>);
                                    console.log('Items:', <?php echo json_encode($items); ?>);
                                    console.log('jQuery available:', typeof jQuery !== 'undefined');
                                    console.log('jQuery UI available:', typeof jQuery !== 'undefined' && typeof jQuery.ui !== 'undefined');
                                    
                                    try {
                                        const game = new SortingGame('<?php echo esc_attr($game_id); ?>', {
                                            width: <?php echo $width; ?>,
                                            height: <?php echo $height; ?>,
                                            backgroundColor: '<?php echo $bg_color; ?>',
                                            showScore: <?php echo $show_score ? 'true' : 'false'; ?>,
                                            showTimer: <?php echo $show_timer ? 'true' : 'false'; ?>,
                                            autoStart: <?php echo $auto_start ? 'true' : 'false'; ?>,
                                            difficulty: '<?php echo esc_js(get_field('difficulty_level')); ?>',
                                            isSequence: <?php echo !empty($sorting_settings['is_sequence']) ? 'true' : 'false'; ?>,
                                            allowIncorrectPlacements: <?php echo !empty($sorting_settings['allow_incorrect_placements']) ? 'true' : 'false'; ?>,
                                            correctSound: '<?php echo !empty($sorting_settings['correct_sound']) ? esc_url($sorting_settings['correct_sound']) : ''; ?>',
                                            errorSound: '<?php echo !empty($sorting_settings['error_sound']) ? esc_url($sorting_settings['error_sound']) : ''; ?>',
                                            categories: <?php echo json_encode($categories); ?>,
                                            items: <?php echo json_encode($items); ?>,
                                            gameId: <?php echo get_the_ID(); ?>,
                                            nonce: '<?php echo wp_create_nonce('sarah_loz_game_progress'); ?>',
                                            mobileResponsive: true
                                        });
                                        console.log('Sorting game initialized successfully');
                                        
                                        // Hide loader when game is ready
                                        const loader = document.getElementById('<?php echo esc_attr($game_id); ?>-loader');
                                        if (loader) {
                                            loader.style.display = 'none';
                                        }
                                    } catch (error) {
                                        console.error('Error initializing sorting game:', error);
                                    }
                                });
                                </script>
                                <?php
                                break;
                                
                            case 'audio_matching':
                                // Enqueue audio game scripts
                                wp_enqueue_script('sarah-loz-audio-game', get_template_directory_uri() . '/assets/js/games/audio-matching-game.js', array('sarah-loz-game-framework'), '1.0.0', true);
                                wp_enqueue_style('sarah-loz-audio-game', get_template_directory_uri() . '/assets/css/audio-matching-game.css', array(), '1.0.0');
                                
                                $audio_settings = get_field('audio_game_settings');
                                
                                // Prepare game data
                                $game_data = array();
                                $template = !empty($audio_settings['game_template']) ? $audio_settings['game_template'] : 'colors';
                                
                                // Start with empty questions array
                                $questions = array();
                                
                                // Add custom questions if any exist
                                if (!empty($audio_settings['questions'])) {
                                    foreach ($audio_settings['questions'] as $question) {
                                        $options = array();
                                        if (!empty($question['options'])) {
                                            foreach ($question['options'] as $option) {
                                                $opt = array(
                                                    'text' => $option['text'],
                                                    'correct' => (bool)$option['correct']
                                                );
                                                if (!empty($option['color'])) $opt['color'] = $option['color'];
                                                if (!empty($option['emoji'])) $opt['emoji'] = $option['emoji'];
                                                if (!empty($option['image'])) $opt['image'] = $option['image'];
                                                $options[] = $opt;
                                            }
                                        }
                                        
                                        // Determine sound content
                                        $sound_content = '';
                                        $audio_url = null;
                                        if (!empty($question['sound'])) {
                                            if ($question['sound']['type'] === 'file' && !empty($question['sound']['file'])) {
                                                $audio_url = $question['sound']['file'];
                                            } else {
                                                $sound_content = !empty($question['sound']['text']) ? $question['sound']['text'] : $question['text'];
                                            }
                                        } else {
                                            $sound_content = $question['text'];
                                        }
                                        
                                        $questions[] = array(
                                            'id' => count($questions) + 1,
                                            'text' => $question['text'],
                                            'sound' => $sound_content,
                                            'audioUrl' => $audio_url,
                                            'type' => !empty($question['category']) ? $question['category'] : 'custom',
                                            'options' => $options
                                        );
                                    }
                                }
                                
                                // If no custom questions or template is not "custom", add template questions
                                if (empty($questions) || $template !== 'custom') {
                                    $templates = array(
                                        'colors' => array(
                                            'title' => 'لعبة الألوان',
                                            'description' => 'تعلم أسماء الألوان',
                                            'questions' => array(
                                                array(
                                                    'id' => 1, 'text' => 'أحمر', 'sound' => 'أحمر', 'type' => 'color',
                                                    'options' => array(
                                                        array('text' => 'أحمر', 'color' => '#dc3545', 'correct' => true, 'emoji' => '🔴'),
                                                        array('text' => 'أزرق', 'color' => '#007bff', 'correct' => false, 'emoji' => '🔵'),
                                                        array('text' => 'أخضر', 'color' => '#28a745', 'correct' => false, 'emoji' => '🟢'),
                                                        array('text' => 'أصفر', 'color' => '#ffc107', 'correct' => false, 'emoji' => '🟡')
                                                    )
                                                ),
                                                array(
                                                    'id' => 2, 'text' => 'أزرق', 'sound' => 'أزرق', 'type' => 'color',
                                                    'options' => array(
                                                        array('text' => 'أحمر', 'color' => '#dc3545', 'correct' => false, 'emoji' => '🔴'),
                                                        array('text' => 'أزرق', 'color' => '#007bff', 'correct' => true, 'emoji' => '🔵'),
                                                        array('text' => 'أخضر', 'color' => '#28a745', 'correct' => false, 'emoji' => '🟢'),
                                                        array('text' => 'أصفر', 'color' => '#ffc107', 'correct' => false, 'emoji' => '🟡')
                                                    )
                                                )
                                            )
                                        ),
                                        'animals' => array(
                                            'title' => 'أصوات الحيوانات',
                                            'description' => 'تعرف على أصوات الحيوانات',
                                            'questions' => array(
                                                array(
                                                    'id' => 1, 'text' => 'قطة', 'sound' => 'مياو مياو - هذا صوت القطة', 'type' => 'animal',
                                                    'options' => array(
                                                        array('text' => 'قطة', 'image' => '🐱', 'correct' => true),
                                                        array('text' => 'كلب', 'image' => '🐶', 'correct' => false),
                                                        array('text' => 'بقرة', 'image' => '🐄', 'correct' => false),
                                                        array('text' => 'خروف', 'image' => '🐑', 'correct' => false)
                                                    )
                                                )
                                            )
                                        )
                                    );
                                    
                                    // Add template questions to existing custom questions
                                    if (isset($templates[$template])) {
                                        $template_questions = $templates[$template]['questions'];
                                        $questions = array_merge($questions, $template_questions);
                                        
                                        $game_data = array(
                                            'id' => $template . '_' . get_the_ID(),
                                            'title' => !empty($questions) && $template === 'custom' ? get_the_title() : $templates[$template]['title'],
                                            'description' => !empty($questions) && $template === 'custom' ? (get_the_excerpt() ?: 'لعبة ربط صوتية') : $templates[$template]['description'],
                                            'type' => $template,
                                            'difficulty' => get_field('difficulty_level') ?: 'medium',
                                            'questions' => $questions
                                        );
                                    } else {
                                        // Fallback to colors template
                                        $template_questions = $templates['colors']['questions'];
                                        $questions = array_merge($questions, $template_questions);
                                        
                                        $game_data = array(
                                            'id' => 'colors_' . get_the_ID(),
                                            'title' => !empty($questions) ? get_the_title() : $templates['colors']['title'],
                                            'description' => !empty($questions) ? (get_the_excerpt() ?: 'لعبة ربط صوتية') : $templates['colors']['description'],
                                            'type' => 'colors',
                                            'difficulty' => get_field('difficulty_level') ?: 'medium',
                                            'questions' => $questions
                                        );
                                    }
                                } else {
                                    // Only custom questions
                                    $game_data = array(
                                        'id' => 'custom_' . get_the_ID(),
                                        'title' => get_the_title(),
                                        'description' => get_the_excerpt() ?: 'لعبة ربط صوتية مخصصة',
                                        'type' => 'custom',
                                        'difficulty' => get_field('difficulty_level') ?: 'medium',
                                        'questions' => $questions
                                    );
                                }
                                
                                // Speech settings
                                $speech_settings = !empty($audio_settings['speech_settings']) ? $audio_settings['speech_settings'] : array();
                                $game_options = !empty($audio_settings['game_options']) ? $audio_settings['game_options'] : array();
                                $voice_instructions = !empty($audio_settings['voice_instructions']) ? $audio_settings['voice_instructions'] : array();
                                $completion_messages = !empty($audio_settings['completion_messages']) ? $audio_settings['completion_messages'] : array();
                                
                                // Initialize game
                                ?>
                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    console.log('Initializing Audio Matching Game');
                                    console.log('Game container ID:', '<?php echo esc_attr($game_id); ?>');
                                    console.log('Audio settings:', <?php echo json_encode($audio_settings); ?>);
                                    console.log('Game data:', <?php echo json_encode($game_data); ?>);
                                    
                                    try {
                                        const game = new AudioMatchingGame('<?php echo esc_attr($game_id); ?>', {
                                            width: <?php echo $width; ?>,
                                            height: <?php echo $height; ?>,
                                            backgroundColor: '<?php echo $bg_color; ?>',
                                            showScore: <?php echo $show_score ? 'true' : 'false'; ?>,
                                            showTimer: <?php echo $show_timer ? 'true' : 'false'; ?>,
                                            autoStart: <?php echo $auto_start ? 'true' : 'false'; ?>,
                                            gameData: <?php echo json_encode($game_data); ?>,
                                            
                                            // Speech settings
                                            language: '<?php echo !empty($speech_settings['language']) ? esc_js($speech_settings['language']) : 'ar-SA'; ?>',
                                            speechRate: <?php echo !empty($speech_settings['rate']) ? floatval($speech_settings['rate']) : 0.8; ?>,
                                            speechPitch: <?php echo !empty($speech_settings['pitch']) ? floatval($speech_settings['pitch']) : 1.2; ?>,
                                            speechVolume: <?php echo !empty($speech_settings['volume']) ? floatval($speech_settings['volume']) : 1.0; ?>,
                                            
                                            // Game options
                                            shuffleOptions: <?php echo !empty($game_options['shuffle_options']) ? 'true' : 'false'; ?>,
                                            maxAttempts: <?php echo !empty($game_options['max_attempts']) ? intval($game_options['max_attempts']) : 3; ?>,
                                            autoPlayIntro: <?php echo !empty($game_options['auto_play_intro']) ? 'true' : 'false'; ?>,
                                            showFeedback: <?php echo !empty($game_options['show_feedback']) ? 'true' : 'false'; ?>,
                                            
                                            // Voice instructions
                                            voiceInstructions: {
                                                welcomeMessageType: '<?php echo !empty($voice_instructions['welcome_message_type']) ? esc_js($voice_instructions['welcome_message_type']) : 'text'; ?>',
                                                welcomeMessageText: '<?php echo !empty($voice_instructions['welcome_message_text']) ? esc_js($voice_instructions['welcome_message_text']) : 'مرحباً! اضغط على الأصوات واختر الإجابة الصحيحة'; ?>',
                                                welcomeMessageFile: '<?php echo !empty($voice_instructions['welcome_message_file']) ? esc_url($voice_instructions['welcome_message_file']) : ''; ?>',
                                                instructionMessageType: '<?php echo !empty($voice_instructions['instruction_message_type']) ? esc_js($voice_instructions['instruction_message_type']) : 'text'; ?>',
                                                instructionMessageText: '<?php echo !empty($voice_instructions['instruction_message_text']) ? esc_js($voice_instructions['instruction_message_text']) : 'اضغط على الزر لسماع الصوت، ثم اختر الإجابة الصحيحة من الخيارات المتاحة'; ?>',
                                                instructionMessageFile: '<?php echo !empty($voice_instructions['instruction_message_file']) ? esc_url($voice_instructions['instruction_message_file']) : ''; ?>'
                                            },
                                            
                                            // Completion messages
                                            completionMessages: {
                                                correctAnswerType: '<?php echo !empty($completion_messages['correct_answer_type']) ? esc_js($completion_messages['correct_answer_type']) : 'text'; ?>',
                                                correctAnswerText: '<?php echo !empty($completion_messages['correct_answer_text']) ? esc_js($completion_messages['correct_answer_text']) : 'أحسنت! إجابة صحيحة'; ?>',
                                                correctAnswerFiles: <?php echo !empty($completion_messages['correct_answer_files']) ? json_encode($completion_messages['correct_answer_files']) : '[]'; ?>,
                                                wrongAnswerType: '<?php echo !empty($completion_messages['wrong_answer_type']) ? esc_js($completion_messages['wrong_answer_type']) : 'text'; ?>',
                                                wrongAnswerText: '<?php echo !empty($completion_messages['wrong_answer_text']) ? esc_js($completion_messages['wrong_answer_text']) : 'حاول مرة أخرى'; ?>',
                                                wrongAnswerFiles: <?php echo !empty($completion_messages['wrong_answer_files']) ? json_encode($completion_messages['wrong_answer_files']) : '[]'; ?>,
                                                gameCompleteType: '<?php echo !empty($completion_messages['game_complete_type']) ? esc_js($completion_messages['game_complete_type']) : 'text'; ?>',
                                                gameCompleteText: '<?php echo !empty($completion_messages['game_complete_text']) ? esc_js($completion_messages['game_complete_text']) : 'ممتاز! لقد أكملت جميع الأسئلة بنجاح! أحسنت العمل'; ?>',
                                                gameCompleteFile: '<?php echo !empty($completion_messages['game_complete_file']) ? esc_url($completion_messages['game_complete_file']) : ''; ?>'
                                            },
                                            
                                            gameId: <?php echo get_the_ID(); ?>,
                                            nonce: '<?php echo wp_create_nonce('sarah_loz_game_progress'); ?>',
                                            mobileResponsive: true
                                        });
                                        console.log('Audio matching game initialized successfully');
                                        
                                        // Hide loader when game is ready
                                        const loader = document.getElementById('<?php echo esc_attr($game_id); ?>-loader');
                                        if (loader) {
                                            loader.style.display = 'none';
                                        }
                                    } catch (error) {
                                        console.error('Error initializing audio matching game:', error);
                                        const loader = document.getElementById('<?php echo esc_attr($game_id); ?>-loader');
                                        if (loader) {
                                            loader.innerHTML = '<div style="text-align: center; color: red;">خطأ في تحميل اللعبة</div>';
                                        }
                                    }
                                });
                                </script>
                                <?php
                                break;
                                
                            default:
                                echo '<div class="bg-yellow-100 text-yellow-800 p-4 rounded-lg">هذا النوع من الألعاب غير متوفر حالياً.</div>';
                                
                                // Hide loader for unsupported games
                                ?>
                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const loader = document.getElementById('<?php echo esc_attr($game_id); ?>-loader');
                                    if (loader) {
                                        loader.style.display = 'none';
                                    }
                                });
                                </script>
                                <?php
                        }
                    endif;
                    ?>
                </div>

            <!-- Game Sidebar (Right Side) -->
            <aside class="game-sidebar">
                <!-- Game Info Card -->
                <div class="game-info-card">
                    <h1 class="game-title"><?php the_title(); ?></h1>
                    
                    <!-- Game Meta Tags -->
                    <div class="game-meta">
                        <?php if ($age_range = get_field('age_range')) : ?>
                            <span class="game-meta-tag">
                                <i class="fas fa-child"></i>
                                <?php echo esc_html($age_range); ?> سنوات
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($difficulty = get_field('difficulty_level')) : ?>
                            <span class="game-meta-tag">
                                <i class="fas fa-star"></i>
                                <?php echo esc_html($difficulty); ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($interactive_game_type = get_field('interactive_game_type')) : ?>
                            <span class="game-meta-tag">
                                <i class="fas fa-gamepad"></i>
                                <?php 
                                $game_type_labels = array(
                                    'memory' => 'ذاكرة',
                                    'quiz' => 'اختبار',
                                    'sorting' => 'ترتيب',
                                    'audio_matching' => 'ربط صوتي'
                                );
                                echo isset($game_type_labels[$interactive_game_type]) ? $game_type_labels[$interactive_game_type] : $interactive_game_type;
                                ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Game Description -->
                    <?php if (get_the_content()) : ?>
                        <div class="game-description">
                            <?php the_content(); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Game Thumbnail -->
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="mb-4">
                            <?php the_post_thumbnail('medium', ['class' => 'responsive-image rounded-lg']); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Game Controls -->
                    <div class="flex flex-col gap-3">
                        <button onclick="toggleFullscreen()" class="game-button">
                            <i class="fas fa-expand"></i>
                            ملء الشاشة
                        </button>
                        
                        <button onclick="resetCurrentGame()" class="game-button" style="background: linear-gradient(45deg, #f44336, #d32f2f);">
                            <i class="fas fa-redo"></i>
                            إعادة اللعب
                        </button>
                    </div>
                </div>

                <!-- Game Stats Card -->
                <div class="game-info-card">
                    <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 1rem; color: #2d3748;">📊 إحصائيات اللعبة</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span style="color: #4a5568;">مرات اللعب:</span>
                            <span style="font-weight: 600; color: #2d3748;" id="play-count">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span style="color: #4a5568;">أفضل نتيجة:</span>
                            <span style="font-weight: 600; color: #2d3748;" id="best-score">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span style="color: #4a5568;">الوقت المستغرق:</span>
                            <span style="font-weight: 600; color: #2d3748;" id="game-time">-</span>
                        </div>
                    </div>
                </div>

                <!-- Tips Card -->
                <div class="game-info-card">
                    <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 1rem; color: #2d3748;">💡 نصائح للعب</h3>
                    <ul style="list-style: none; padding: 0; space-y: 2;">
                        <li style="margin-bottom: 0.75rem; display: flex; align-items: flex-start; gap: 0.5rem;">
                            <span style="color: #667eea;">🎯</span>
                            <span style="font-size: 0.9rem; color: #4a5568;">استمع جيداً للأصوات</span>
                        </li>
                        <li style="margin-bottom: 0.75rem; display: flex; align-items: flex-start; gap: 0.5rem;">
                            <span style="color: #667eea;">🔊</span>
                            <span style="font-size: 0.9rem; color: #4a5568;">تأكد من تشغيل الصوت</span>
                        </li>
                        <li style="margin-bottom: 0.75rem; display: flex; align-items: flex-start; gap: 0.5rem;">
                            <span style="color: #667eea;">🏆</span>
                            <span style="font-size: 0.9rem; color: #4a5568;">لا تتردد في المحاولة مرة أخرى</span>
                        </li>
                        <li style="display: flex; align-items: flex-start; gap: 0.5rem;">
                            <span style="color: #667eea;">📱</span>
                            <span style="font-size: 0.9rem; color: #4a5568;">جرب اللعب في وضع ملء الشاشة</span>
                        </li>
                    </ul>
                </div>
            </aside>
        </main>

        <!-- Mobile Tips -->
        <div class="container-responsive">
            <div class="mobile-tip">
                <i class="fas fa-mobile-alt"></i>
                <span>💡 للحصول على تجربة أفضل، استخدم وضع ملء الشاشة أو أدر الجهاز أفقياً</span>
            </div>
        </div>

        <!-- Related Games -->
        <?php
        $related_games = new WP_Query(array(
            'post_type' => 'game',
            'posts_per_page' => 3,
            'post__not_in' => array(get_the_ID()),
            'orderby' => 'rand'
        ));

        if ($related_games->have_posts()) : ?>
            <div class="container-responsive" style="margin-top: 3rem;">
                <h2 style="font-size: clamp(1.5rem, 4vw, 2rem); font-weight: 800; margin-bottom: 2rem; text-align: center; color: white;">🎮 ألعاب مشابهة</h2>
                <div class="related-games-grid">
                    <?php while ($related_games->have_posts()) : $related_games->the_post(); ?>
                        <article class="related-game-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <div style="aspect-ratio: 16/9; overflow: hidden;">
                                    <?php the_post_thumbnail('medium', ['class' => 'responsive-image']); ?>
                                </div>
                            <?php else : ?>
                                <div style="aspect-ratio: 16/9; background: linear-gradient(45deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-gamepad" style="font-size: 3rem; color: white; opacity: 0.7;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div style="padding: 1.5rem;">
                                <h3 style="font-size: clamp(1rem, 3vw, 1.25rem); font-weight: 700; margin-bottom: 1rem; line-height: 1.2;">
                                    <a href="<?php the_permalink(); ?>" style="color: #2d3748; text-decoration: none; transition: color 0.3s ease;">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>
                                
                                <!-- Game Meta -->
                                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem;">
                                    <?php if ($age_range = get_field('age_range')) : ?>
                                        <span style="background: #e2e8f0; color: #4a5568; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.75rem;">
                                            <?php echo esc_html($age_range); ?> سنوات
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($difficulty = get_field('difficulty_level')) : ?>
                                        <span style="background: #e2e8f0; color: #4a5568; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.75rem;">
                                            <?php echo esc_html($difficulty); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <a href="<?php the_permalink(); ?>" class="game-button" style="width: 100%; justify-content: center;">
                                    <i class="fas fa-play"></i>
                                    العب الآن
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php wp_reset_postdata();
        endif; ?>
    <?php endwhile; ?>
</div>

<!-- Enhanced Game Page JavaScript -->
<script>
// Global game instance reference
let currentGameInstance = null;

// Fullscreen functionality
function toggleFullscreen() {
    const gameContainer = document.querySelector('.game-container');
    
    if (!document.fullscreenElement) {
        // Enter fullscreen
        if (gameContainer.requestFullscreen) {
            gameContainer.requestFullscreen();
        } else if (gameContainer.webkitRequestFullscreen) {
            gameContainer.webkitRequestFullscreen();
        } else if (gameContainer.msRequestFullscreen) {
            gameContainer.msRequestFullscreen();
        }
        
        // Add fullscreen class for styling
        gameContainer.classList.add('game-fullscreen');
        
        // Update button text
        updateFullscreenButton('compress', 'خروج من ملء الشاشة');
    } else {
        // Exit fullscreen
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }
}

// Update fullscreen button
function updateFullscreenButton(icon, text) {
    const button = document.querySelector('button[onclick="toggleFullscreen()"]');
    if (button) {
        button.innerHTML = `<i class="fas fa-${icon}"></i> ${text}`;
    }
}

// Listen for fullscreen changes
document.addEventListener('fullscreenchange', handleFullscreenChange);
document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
document.addEventListener('msfullscreenchange', handleFullscreenChange);

function handleFullscreenChange() {
    const gameContainer = document.querySelector('.game-container');
    
    if (!document.fullscreenElement) {
        // Exited fullscreen
        gameContainer.classList.remove('game-fullscreen');
        updateFullscreenButton('expand', 'ملء الشاشة');
    }
}

// Reset current game
function resetCurrentGame() {
    // Try to find and reset the current game instance
    const gameContainers = document.querySelectorAll('[id^="game-"]');
    
    gameContainers.forEach(container => {
        const gameId = container.id;
        
        // Check if there's a global game instance
        if (window[gameId] && typeof window[gameId].resetGame === 'function') {
            window[gameId].resetGame();
        } else if (window.currentGame && typeof window.currentGame.resetGame === 'function') {
            window.currentGame.resetGame();
        } else if (currentGameInstance && typeof currentGameInstance.resetGame === 'function') {
            currentGameInstance.resetGame();
        } else {
            // Fallback: reload the page
            location.reload();
        }
    });
}

// Game statistics tracking
function updateGameStats(playCount, bestScore, gameTime) {
    const playCountEl = document.getElementById('play-count');
    const bestScoreEl = document.getElementById('best-score');
    const gameTimeEl = document.getElementById('game-time');
    
    if (playCountEl && playCount !== undefined) {
        playCountEl.textContent = playCount;
    }
    
    if (bestScoreEl && bestScore !== undefined) {
        bestScoreEl.textContent = bestScore + '%';
    }
    
    if (gameTimeEl && gameTime !== undefined) {
        const minutes = Math.floor(gameTime / 60);
        const seconds = gameTime % 60;
        gameTimeEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    }
}

// Initialize game statistics from localStorage
function initGameStats() {
    const gameId = '<?php echo get_the_ID(); ?>';
    const stats = JSON.parse(localStorage.getItem(`game_stats_${gameId}`) || '{}');
    
    updateGameStats(
        stats.playCount || 0,
        stats.bestScore || 0,
        stats.totalTime || 0
    );
}

// Save game statistics
function saveGameStats(score, time) {
    const gameId = '<?php echo get_the_ID(); ?>';
    const stats = JSON.parse(localStorage.getItem(`game_stats_${gameId}`) || '{}');
    
    stats.playCount = (stats.playCount || 0) + 1;
    stats.bestScore = Math.max(stats.bestScore || 0, score);
    stats.totalTime = (stats.totalTime || 0) + time;
    
    localStorage.setItem(`game_stats_${gameId}`, JSON.stringify(stats));
    updateGameStats(stats.playCount, stats.bestScore, stats.totalTime);
}

// Orientation change handler for mobile
function handleOrientationChange() {
    // Trigger resize event after orientation change
    setTimeout(() => {
        window.dispatchEvent(new Event('resize'));
        
        // Update game layout if current game has updateLayout method
        if (currentGameInstance && typeof currentGameInstance.updateLayout === 'function') {
            currentGameInstance.updateLayout();
        }
    }, 100);
}

// Add orientation change listener
window.addEventListener('orientationchange', handleOrientationChange);
window.addEventListener('resize', handleOrientationChange);

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initGameStats();
    
    // Add loading completion handler
    const gameContainers = document.querySelectorAll('[id^="game-"]');
    
    gameContainers.forEach(container => {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                // Check if game loader was removed (game loaded)
                if (mutation.type === 'childList') {
                    const loader = container.querySelector('.game-loader');
                    if (!loader || loader.style.display === 'none') {
                        // Game has loaded
                        console.log('Game loaded successfully');
                        
                        // Try to get reference to game instance
                        const gameId = container.id;
                        if (window[gameId]) {
                            currentGameInstance = window[gameId];
                        }
                    }
                }
            });
        });
        
        observer.observe(container, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['style']
        });
    });
});

// Performance optimization: Preload related games
function preloadRelatedGames() {
    const relatedLinks = document.querySelectorAll('.related-game-card a');
    
    relatedLinks.forEach(link => {
        // Add prefetch link to head
        const prefetchLink = document.createElement('link');
        prefetchLink.rel = 'prefetch';
        prefetchLink.href = link.href;
        document.head.appendChild(prefetchLink);
    });
}

// Initialize preloading after page load
window.addEventListener('load', preloadRelatedGames);
</script>

<!-- Cross-Post-Type Related Content -->
<?php
// Get current post's categories
$game_categories = get_the_terms(get_the_ID(), 'game_category');
if ($game_categories && !is_wp_error($game_categories)) {
    $category_names = array();
    $category_ids = array();
    foreach ($game_categories as $cat) {
        $category_names[] = $cat->name;
        $category_ids[] = $cat->term_id;
    }
    
    // Check for activities with same category name
    $related_activities = array();
    $activity_terms = get_terms(array(
        'taxonomy' => 'activity_category',
        'hide_empty' => true,
    ));
    
    $activity_term_ids = array();
    if ($activity_terms && !is_wp_error($activity_terms)) {
        foreach ($activity_terms as $term) {
            if (in_array($term->name, $category_names)) {
                $activity_term_ids[] = $term->term_id;
            }
        }
    }
    
    if (!empty($activity_term_ids)) {
        $activities_query = new WP_Query(array(
            'post_type' => 'activity',
            'posts_per_page' => 3,
            'tax_query' => array(
                array(
                    'taxonomy' => 'activity_category',
                    'field' => 'term_id',
                    'terms' => $activity_term_ids,
                ),
            ),
        ));
        
        if ($activities_query->have_posts()) {
            while ($activities_query->have_posts()) {
                $activities_query->the_post();
                $related_activities[] = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'permalink' => get_permalink(),
                    'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
                    'type' => 'activity',
                    'type_label' => __('نشاط', 'sarah-loz'),
                );
            }
            wp_reset_postdata();
        }
    }
    
    // Check for videos with same category name
    $related_videos = array();
    $video_terms = get_terms(array(
        'taxonomy' => 'video_category',
        'hide_empty' => true,
    ));
    
    $video_term_ids = array();
    if ($video_terms && !is_wp_error($video_terms)) {
        foreach ($video_terms as $term) {
            if (in_array($term->name, $category_names)) {
                $video_term_ids[] = $term->term_id;
            }
        }
    }
    
    if (!empty($video_term_ids)) {
        $videos_query = new WP_Query(array(
            'post_type' => 'video',
            'posts_per_page' => 3,
            'tax_query' => array(
                array(
                    'taxonomy' => 'video_category',
                    'field' => 'term_id',
                    'terms' => $video_term_ids,
                ),
            ),
        ));
        
        if ($videos_query->have_posts()) {
            while ($videos_query->have_posts()) {
                $videos_query->the_post();
                $related_videos[] = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'permalink' => get_permalink(),
                    'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
                    'type' => 'video',
                    'type_label' => __('فيديو', 'sarah-loz'),
                );
            }
            wp_reset_postdata();
        }
    }
    
    // Display related content if we have any
    $related_content = array_merge($related_activities, $related_videos);
    if (!empty($related_content)) :
    ?>
    <div class="container mx-auto px-4 mt-12 mb-8">
        <h2 class="text-2xl font-bold mb-6"><?php _e('محتوى ذو صلة', 'sarah-loz'); ?></h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ($related_content as $item) : ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="relative">
                        <?php if (!empty($item['thumbnail'])) : ?>
                            <div class="aspect-w-16 aspect-h-9">
                                <img src="<?php echo esc_url($item['thumbnail']); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="w-full h-full object-cover">
                            </div>
                        <?php else : ?>
                            <div class="aspect-w-16 aspect-h-9 bg-primary/10 flex items-center justify-center">
                                <?php if ($item['type'] === 'activity') : ?>
                                    <i class="fas fa-paint-brush text-6xl text-primary/40"></i>
                                <?php elseif ($item['type'] === 'video') : ?>
                                    <i class="fas fa-play-circle text-6xl text-primary/40"></i>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <span class="absolute top-2 right-2 bg-accent text-dark px-2 py-1 rounded-full text-xs font-bold">
                            <?php echo esc_html($item['type_label']); ?>
                        </span>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-3">
                            <a href="<?php echo esc_url($item['permalink']); ?>" class="text-dark hover:text-primary transition-colors">
                                <?php echo esc_html($item['title']); ?>
                            </a>
                        </h3>
                        
                        <a href="<?php echo esc_url($item['permalink']); ?>" class="inline-block bg-primary text-white px-4 py-2 rounded-full hover:bg-opacity-90 transition-colors">
                            <?php 
                            if ($item['type'] === 'activity') {
                                _e('شاهد النشاط', 'sarah-loz');
                            } elseif ($item['type'] === 'video') {
                                _e('شاهد الفيديو', 'sarah-loz');
                            }
                            ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    endif;
}
?>

<?php get_footer(); ?>
