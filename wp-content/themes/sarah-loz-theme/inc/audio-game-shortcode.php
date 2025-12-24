<?php
/**
 * Audio Game Shortcode
 * Allow easy embedding of audio games in posts and pages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class AudioGameShortcode {
    
    public function __construct() {
        add_shortcode('audio_game', array($this, 'render_shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_head', array($this, 'add_inline_styles'));
    }

    /**
     * Render audio game shortcode
     */
    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'template' => 'colors',
            'id' => '',
            'width' => '100%',
            'height' => '600px',
            'theme' => 'default',
            'difficulty' => '',
            'auto_start' => 'false',
            'show_timer' => 'false',
            'show_score' => 'true',
            'max_attempts' => '3',
            'shuffle_options' => 'true',
            'language' => 'ar-SA',
            'speech_rate' => '0.8',
            'mobile_responsive' => 'true'
        ), $atts, 'audio_game');

        // Generate unique container ID
        $container_id = 'audio-game-' . uniqid();
        
        // Get game data
        $game_data = $this->get_game_data($atts['template'], $atts['id']);
        
        if (!$game_data) {
            return '<div class="audio-game-error">خطأ: لم يتم العثور على اللعبة المطلوبة</div>';
        }

        // Prepare game options
        $game_options = array(
            'gameData' => $game_data,
            'width' => is_numeric($atts['width']) ? intval($atts['width']) : 800,
            'height' => is_numeric($atts['height']) ? intval($atts['height']) : 600,
            'autoStart' => $atts['auto_start'] === 'true',
            'showTimer' => $atts['show_timer'] === 'true',
            'showScore' => $atts['show_score'] === 'true',
            'maxAttempts' => intval($atts['max_attempts']),
            'shuffleOptions' => $atts['shuffle_options'] === 'true',
            'language' => $atts['language'],
            'speechRate' => floatval($atts['speech_rate']),
            'mobileResponsive' => $atts['mobile_responsive'] === 'true',
            'theme' => $atts['theme']
        );

        // Override difficulty if specified
        if (!empty($atts['difficulty'])) {
            $game_options['gameData']['difficulty'] = $atts['difficulty'];
        }

        // Enqueue required scripts
        $this->enqueue_game_scripts();

        ob_start();
        ?>
        <div class="audio-game-wrapper" style="width: <?php echo esc_attr($atts['width']); ?>; max-width: 100%;">
            <div id="<?php echo esc_attr($container_id); ?>" class="audio-game-container"></div>
        </div>
        
        <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure scripts are loaded
            function initAudioGame() {
                if (typeof AudioMatchingGame !== 'undefined' && typeof GameFramework !== 'undefined') {
                    try {
                        const gameOptions = <?php echo json_encode($game_options); ?>;
                        const audioGame = new AudioMatchingGame('<?php echo esc_js($container_id); ?>', gameOptions);
                        
                        // Auto-start if specified
                        <?php if ($atts['auto_start'] === 'true'): ?>
                        setTimeout(() => {
                            audioGame.start();
                        }, 1000);
                        <?php endif; ?>
                        
                    } catch (error) {
                        console.error('Audio game initialization error:', error);
                        document.getElementById('<?php echo esc_js($container_id); ?>').innerHTML = 
                            '<div class="audio-game-error">خطأ في تحميل اللعبة</div>';
                    }
                } else {
                    console.warn('Audio game scripts not loaded, retrying...');
                    setTimeout(initAudioGame, 500);
                }
            }
            
            initAudioGame();
        });
        </script>
        <?php
        
        return ob_get_clean();
    }

    /**
     * Get game data by template or ID
     */
    private function get_game_data($template, $id) {
        // If specific game ID is provided, load from saved games
        if (!empty($id)) {
            $saved_games = get_option('sarah_loz_audio_games', array());
            if (isset($saved_games[$id])) {
                return $saved_games[$id];
            }
        }

        // Load from default templates
        $templates = $this->get_default_templates();
        if (isset($templates[$template])) {
            return $templates[$template];
        }

        return null;
    }

    /**
     * Get default game templates
     */
    private function get_default_templates() {
        return array(
            'colors' => array(
                'id' => 'colors',
                'title' => 'لعبة ربط الألوان',
                'description' => 'اضغط على الصوت واختر اللون الصحيح',
                'type' => 'color',
                'difficulty' => 'easy',
                'ageGroup' => '3-6',
                'questions' => array(
                    array(
                        'id' => 1,
                        'text' => 'أحمر',
                        'sound' => 'أحمر',
                        'type' => 'color',
                        'options' => array(
                            array('text' => 'أحمر', 'color' => '#dc3545', 'correct' => true, 'emoji' => '🔴'),
                            array('text' => 'أزرق', 'color' => '#007bff', 'correct' => false, 'emoji' => '🔵'),
                            array('text' => 'أخضر', 'color' => '#28a745', 'correct' => false, 'emoji' => '🟢'),
                            array('text' => 'أصفر', 'color' => '#ffc107', 'correct' => false, 'emoji' => '🟡')
                        )
                    ),
                    array(
                        'id' => 2,
                        'text' => 'أزرق',
                        'sound' => 'أزرق',
                        'type' => 'color',
                        'options' => array(
                            array('text' => 'أحمر', 'color' => '#dc3545', 'correct' => false, 'emoji' => '🔴'),
                            array('text' => 'أزرق', 'color' => '#007bff', 'correct' => true, 'emoji' => '🔵'),
                            array('text' => 'أخضر', 'color' => '#28a745', 'correct' => false, 'emoji' => '🟢'),
                            array('text' => 'أصفر', 'color' => '#ffc107', 'correct' => false, 'emoji' => '🟡')
                        )
                    ),
                    array(
                        'id' => 3,
                        'text' => 'أخضر',
                        'sound' => 'أخضر',
                        'type' => 'color',
                        'options' => array(
                            array('text' => 'أحمر', 'color' => '#dc3545', 'correct' => false, 'emoji' => '🔴'),
                            array('text' => 'أزرق', 'color' => '#007bff', 'correct' => false, 'emoji' => '🔵'),
                            array('text' => 'أخضر', 'color' => '#28a745', 'correct' => true, 'emoji' => '🟢'),
                            array('text' => 'أصفر', 'color' => '#ffc107', 'correct' => false, 'emoji' => '🟡')
                        )
                    ),
                    array(
                        'id' => 4,
                        'text' => 'أصفر',
                        'sound' => 'أصفر',
                        'type' => 'color',
                        'options' => array(
                            array('text' => 'أحمر', 'color' => '#dc3545', 'correct' => false, 'emoji' => '🔴'),
                            array('text' => 'أزرق', 'color' => '#007bff', 'correct' => false, 'emoji' => '🔵'),
                            array('text' => 'أخضر', 'color' => '#28a745', 'correct' => false, 'emoji' => '🟢'),
                            array('text' => 'أصفر', 'color' => '#ffc107', 'correct' => true, 'emoji' => '🟡')
                        )
                    )
                )
            ),
            'animals' => array(
                'id' => 'animals',
                'title' => 'لعبة أصوات الحيوانات',
                'description' => 'استمع إلى أصوات الحيوانات واختر الحيوان الصحيح',
                'type' => 'animal',
                'difficulty' => 'medium',
                'ageGroup' => '4-8',
                'questions' => array(
                    array(
                        'id' => 1,
                        'text' => 'قطة',
                        'sound' => 'مياو مياو - هذا صوت القطة',
                        'type' => 'animal',
                        'options' => array(
                            array('text' => 'قطة', 'image' => '🐱', 'correct' => true, 'color' => '#ff9500'),
                            array('text' => 'كلب', 'image' => '🐶', 'correct' => false, 'color' => '#8B4513'),
                            array('text' => 'بقرة', 'image' => '🐄', 'correct' => false, 'color' => '#000000'),
                            array('text' => 'خروف', 'image' => '🐑', 'correct' => false, 'color' => '#f5f5dc')
                        )
                    ),
                    array(
                        'id' => 2,
                        'text' => 'كلب',
                        'sound' => 'هاو هاو - هذا صوت الكلب',
                        'type' => 'animal',
                        'options' => array(
                            array('text' => 'قطة', 'image' => '🐱', 'correct' => false, 'color' => '#ff9500'),
                            array('text' => 'كلب', 'image' => '🐶', 'correct' => true, 'color' => '#8B4513'),
                            array('text' => 'بقرة', 'image' => '🐄', 'correct' => false, 'color' => '#000000'),
                            array('text' => 'خروف', 'image' => '🐑', 'correct' => false, 'color' => '#f5f5dc')
                        )
                    )
                )
            ),
            'numbers' => array(
                'id' => 'numbers',
                'title' => 'لعبة الأرقام',
                'description' => 'استمع إلى الرقم واختر الرقم الصحيح',
                'type' => 'number',
                'difficulty' => 'easy',
                'ageGroup' => '3-7',
                'questions' => array(
                    array(
                        'id' => 1,
                        'text' => 'واحد',
                        'sound' => 'واحد',
                        'type' => 'number',
                        'options' => array(
                            array('text' => '1', 'number' => 1, 'correct' => true, 'color' => '#e74c3c'),
                            array('text' => '2', 'number' => 2, 'correct' => false, 'color' => '#3498db'),
                            array('text' => '3', 'number' => 3, 'correct' => false, 'color' => '#2ecc71'),
                            array('text' => '4', 'number' => 4, 'correct' => false, 'color' => '#f39c12')
                        )
                    ),
                    array(
                        'id' => 2,
                        'text' => 'اثنان',
                        'sound' => 'اثنان',
                        'type' => 'number',
                        'options' => array(
                            array('text' => '1', 'number' => 1, 'correct' => false, 'color' => '#e74c3c'),
                            array('text' => '2', 'number' => 2, 'correct' => true, 'color' => '#3498db'),
                            array('text' => '3', 'number' => 3, 'correct' => false, 'color' => '#2ecc71'),
                            array('text' => '4', 'number' => 4, 'correct' => false, 'color' => '#f39c12')
                        )
                    )
                )
            )
        );
    }

    /**
     * Enqueue scripts for frontend
     */
    public function enqueue_scripts() {
        // Only enqueue if shortcode is used
        if (has_shortcode(get_post()->post_content ?? '', 'audio_game')) {
            $this->enqueue_game_scripts();
        }
    }

    /**
     * Enqueue game scripts
     */
    private function enqueue_game_scripts() {
        static $scripts_enqueued = false;
        
        if ($scripts_enqueued) {
            return;
        }
        
        wp_enqueue_script(
            'sarah-loz-game-framework',
            get_template_directory_uri() . '/assets/js/games/game-framework.js',
            array(),
            '1.0.0',
            true
        );

        wp_enqueue_script(
            'sarah-loz-audio-matching-game',
            get_template_directory_uri() . '/assets/js/games/audio-matching-game.js',
            array('sarah-loz-game-framework'),
            '1.0.0',
            true
        );

        wp_enqueue_style(
            'sarah-loz-audio-matching-game',
            get_template_directory_uri() . '/assets/css/audio-matching-game.css',
            array(),
            '1.0.0'
        );

        $scripts_enqueued = true;
    }

    /**
     * Add inline styles for better integration
     */
    public function add_inline_styles() {
        if (has_shortcode(get_post()->post_content ?? '', 'audio_game')) {
            echo '<style type="text/css">
                .audio-game-wrapper {
                    margin: 20px auto;
                    text-align: center;
                }
                
                .audio-game-container {
                    display: inline-block;
                    width: 100%;
                    max-width: 100%;
                }
                
                .audio-game-error {
                    background: #f8d7da;
                    color: #721c24;
                    padding: 20px;
                    border-radius: 8px;
                    text-align: center;
                    margin: 20px 0;
                    border: 1px solid #f5c6cb;
                }
                
                @media (max-width: 768px) {
                    .audio-game-wrapper {
                        margin: 10px auto;
                    }
                }
            </style>';
        }
    }
}

// Initialize the shortcode
new AudioGameShortcode();

/**
 * Helper function to get available game templates for developers
 */
function get_audio_game_templates() {
    $shortcode = new AudioGameShortcode();
    return $shortcode->get_default_templates();
}

/**
 * Helper function to get saved audio games
 */
function get_saved_audio_games() {
    return get_option('sarah_loz_audio_games', array());
}
?>
