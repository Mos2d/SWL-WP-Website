<?php
/**
 * Sarah and Loz Theme functions and definitions
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// TEMPORARY AUTHENTICATION PROTECTION
// Remove this line when you want to disable site protection
require get_template_directory() . '/temp-auth-protection.php';


// Setup theme
function sarah_loz_setup() {
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');
    
    // Let WordPress manage the document title
    add_theme_support('title-tag');
    
    // Enable support for Post Thumbnails on posts and pages
    add_theme_support('post-thumbnails');
    
    // Add support for RTL
    add_theme_support('align-wide');
    
    // Register nav menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'sarah-loz'),
        'footer' => esc_html__('Footer Menu', 'sarah-loz'),
    ));
    
    // Auto-add topics page to primary menu if it doesn't exist
    add_action('wp_loaded', 'sarah_loz_auto_add_topics_to_menu');
}
add_action('after_setup_theme', 'sarah_loz_setup');

// Enqueue scripts and styles
function sarah_loz_scripts() {
    // Enqueue Tailwind CSS - using a more reliable CDN
    wp_enqueue_script('tailwindcss-cdn', 'https://cdn.tailwindcss.com', array(), '3.4.0', false);
    
    // Enqueue Google Fonts with display=swap for better performance
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Harmattan:wght@300;400;500;700&display=swap', array(), null);
    
    // Enqueue Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
    
    // Enqueue jQuery UI for drag and drop functionality
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-draggable');
    wp_enqueue_script('jquery-ui-droppable');
    wp_enqueue_style('jquery-ui-style', 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css', array(), '1.13.2');
    
    // Enqueue carousel script
    wp_enqueue_script('sarah-loz-carousel', get_template_directory_uri() . '/assets/js/carousel.js', array('jquery'), '1.0.0', true);
    
    // Enqueue theme custom styles
    wp_enqueue_style('sarah-loz-style', get_stylesheet_uri(), array(), '1.0.0');
}
add_action('wp_enqueue_scripts', 'sarah_loz_scripts');

// Enqueue WooCommerce styles
function sarah_loz_woocommerce_styles() {
    if (class_exists('WooCommerce')) {
        wp_enqueue_style('sarah-loz-woocommerce', get_template_directory_uri() . '/assets/css/woocommerce.css', array(), '1.0.0');
    }
}
add_action('wp_enqueue_scripts', 'sarah_loz_woocommerce_styles');

// Load custom post types
require get_template_directory() . '/inc/post-types.php';

// Load custom taxonomies
require get_template_directory() . '/inc/taxonomies.php';

// Load WooCommerce customization
require get_template_directory() . '/inc/woocommerce.php';

// Load practices functionality
require get_template_directory() . '/inc/practices.php';

// Load achievements system
require get_template_directory() . '/inc/achievements.php';

// Load child activity tracker
require get_template_directory() . '/inc/child-activity-tracker.php';

// Load test data import functionality
require get_template_directory() . '/inc/import-test-data.php';

// Load ACF field registrations
require get_template_directory() . '/inc/acf-fields.php';

// Load interactive games functionality
require get_template_directory() . '/inc/game-functions.php';

// Load admin dashboard for games
require get_template_directory() . '/inc/game-admin.php';

// Load newsletter functionality
require get_template_directory() . '/inc/newsletter.php';

// Load broadcast and live stream functionality
require get_template_directory() . '/inc/broadcast-functions.php';

// Load broadcast admin functionality
require get_template_directory() . '/inc/broadcast-admin.php';

// Load login redirect functionality
require get_template_directory() . '/inc/login-redirect.php';

/**
 * Enqueue newsletter scripts
 */
function sarah_loz_newsletter_scripts() {
    // Admin scripts
    if (is_admin()) {
        $screen = get_current_screen();
        if ($screen && $screen->id === 'toplevel_page_swl-newsletter') {
            wp_enqueue_script('swl-newsletter-admin');
        }
    }
    
    // Frontend scripts
    if (!is_admin()) {
        wp_enqueue_script('swl-newsletter-frontend', get_template_directory_uri() . '/assets/js/newsletter-frontend.js', array('jquery'), '1.0.0', true);
        wp_localize_script('swl-newsletter-frontend', 'swl_newsletter_vars', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('swl_newsletter_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'sarah_loz_newsletter_scripts');
add_action('admin_enqueue_scripts', 'sarah_loz_newsletter_scripts');

/**
 * Update cart count in real-time using AJAX
 */
function sarah_loz_add_to_cart_fragment($fragments) {
    // Update the cart count
    ob_start();
    ?>
    <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    <?php
    $fragments['.cart-count'] = ob_get_clean();
    
    // Also update the entire cart link for better compatibility
    ob_start();
    ?>
    <a href="<?php echo wc_get_cart_url(); ?>" class="text-white hover:text-light transition relative px-3 cart-contents">
        <i class="fas fa-shopping-cart text-xl"></i>
        <span class="cart-count absolute -top-2 -right-0 bg-accent text-dark text-xs w-5 h-5 rounded-full flex items-center justify-center">
            <?php echo WC()->cart->get_cart_contents_count(); ?>
        </span>
    </a>
    <?php
    $fragments['a.cart-contents'] = ob_get_clean();
    
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'sarah_loz_add_to_cart_fragment');

/**
 * Get cart count via AJAX
 */
function sarah_loz_get_cart_count() {
    if (function_exists('WC') && isset(WC()->cart)) {
        wp_send_json_success(array(
            'count' => WC()->cart->get_cart_contents_count()
        ));
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_sarah_loz_get_cart_count', 'sarah_loz_get_cart_count');
add_action('wp_ajax_nopriv_sarah_loz_get_cart_count', 'sarah_loz_get_cart_count');

// Modify header cart
function sarah_loz_woocommerce_cart_link() {
    ?>
    <a class="cart-contents" href="<?php echo esc_url(wc_get_cart_url()); ?>" title="<?php esc_attr_e('View your shopping cart', 'sarah-loz'); ?>">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    </a>
    <?php
}

// Configure Tailwind
function sarah_loz_tailwind_config() {
    ?>
    <script>
        tailwind.config = {
            theme: {
                colors: {
                    primary: "#3B82F6",   // Bright Blue
                    secondary: "#EC4899", // Hot Pink
                    accent: "#F59E0B",    // Golden Yellow
                    highlight: "#10B981", // Emerald Green
                    light: "#F3F4F6",     // Cool Light Gray
                    bright: "#FDE047",    // Bright Lemon
                    soft: "#F472B6",      // Soft Pink
                    dark: "#1F2937",      // Dark Gray
                    white: "#ffffff",
                },
                extend: {
                    fontFamily: {
                        arabic: ["Harmattan", "sans-serif"],
                    },
                    animation: {
                        "bounce-slow": "bounce 3s infinite",
                        float: "float 3s ease-in-out infinite",
                    },
                    keyframes: {
                        float: {
                            "0%, 100%": { transform: "translateY(0)" },
                            "50%": { transform: "translateY(-10px)" },
                        },
                    },
                    backgroundImage: {
                        'gradient-primary': 'linear-gradient(135deg, #3B82F6, #EC4899)',
                        'gradient-secondary': 'linear-gradient(135deg, #EC4899, #F59E0B)',
                        'gradient-accent': 'linear-gradient(135deg, #F59E0B, #FDE047)',
                        'gradient-dark': 'linear-gradient(135deg, #1F2937, #4B5563)',
                    },
                },
            },
            safelist: [
                'text-primary',
                'text-secondary',
                'text-accent',
                'text-highlight',
                'text-light',
                'text-bright',
                'text-soft',
                'text-dark',
                'text-white',
                'bg-primary',
                'bg-secondary',
                'bg-accent',
                'bg-highlight',
                'bg-light',
                'bg-bright',
                'bg-soft',
                'bg-dark',
                'bg-white',
                'border-primary',
                'border-secondary',
                'border-accent',
                'border-highlight',
                'border-light',
                'border-bright',
                'border-soft',
                'border-dark',
                'border-white',
                'bg-gradient-primary',
                'bg-gradient-secondary',
                'bg-gradient-accent',
                'bg-gradient-dark',
                'font-arabic'
            ],
        };
    </script>
    <style>
        /* Ensure Harmattan font is properly applied */
        html, body, h1, h2, h3, h4, h5, h6, p, div, a, button, input, textarea, select, option {
            font-family: 'Harmattan', sans-serif !important;
        }
        
        :root {
            --color-primary: #3B82F6;
            --color-secondary: #EC4899;
            --color-accent: #F59E0B;
            --color-highlight: #10B981;
            --color-light: #F3F4F6;
            --color-bright: #FDE047;
            --color-soft: #F472B6;
            --color-dark: #1F2937;
            --color-white: #ffffff;
        }
        
        /* Direct color class definitions in case Tailwind doesn't apply them properly */
        .text-primary { color: #3B82F6 !important; }
        .text-secondary { color: #EC4899 !important; }
        .text-accent { color: #F59E0B !important; }
        .text-highlight { color: #10B981 !important; }
        .text-light { color: #F3F4F6 !important; }
        .text-bright { color: #f9f31e !important; }
        .text-soft { color: #ff66cc !important; }
        .text-dark { color: #231f20 !important; }
        .text-white { color: #ffffff !important; }
        
        .bg-primary { background-color: #3B82F6 !important; }
        .bg-secondary { background-color: #EC4899 !important; }
        .bg-accent { background-color: #F59E0B !important; }
        .bg-highlight { background-color: #10B981 !important; }
        .bg-light { background-color: #F3F4F6 !important; }
        .bg-bright { background-color: #f9f31e !important; }
        .bg-soft { background-color: #ff66cc !important; }
        .bg-dark { background-color: #231f20 !important; }
        .bg-white { background-color: #ffffff !important; }
        
        .border-primary { border-color: #007cba !important; }
        .border-secondary { border-color: #1ddede !important; }
        .border-accent { border-color: #f8c709 !important; }
        .border-highlight { border-color: #aff124 !important; }
        .border-light { border-color: #f9f9f6 !important; }
        .border-bright { border-color: #f9f31e !important; }
        .border-soft { border-color: #ff66cc !important; }
        .border-dark { border-color: #231f20 !important; }
        .border-white { border-color: #ffffff !important; }
        
        /* Adding opacity variants manually */
        .bg-primary\/10 { background-color: rgba(59, 130, 246, 0.1) !important; }
        .bg-secondary\/10 { background-color: rgba(236, 72, 153, 0.1) !important; }
        .bg-accent\/10 { background-color: rgba(245, 158, 11, 0.1) !important; }
        .bg-primary\/20 { background-color: rgba(59, 130, 246, 0.2) !important; }
        .bg-secondary\/20 { background-color: rgba(236, 72, 153, 0.2) !important; }
        .bg-accent\/20 { background-color: rgba(245, 158, 11, 0.2) !important; }
        
        .hover\:bg-primary:hover { background-color: #3B82F6 !important; }
        .hover\:bg-secondary:hover { background-color: #EC4899 !important; }
        .hover\:bg-accent:hover { background-color: #F59E0B !important; }
        .hover\:bg-highlight:hover { background-color: #10B981 !important; }
        .hover\:bg-primary\/90:hover { background-color: rgba(59, 130, 246, 0.9) !important; }
        .hover\:bg-secondary\/90:hover { background-color: rgba(236, 72, 153, 0.9) !important; }
        .hover\:bg-accent\/90:hover { background-color: rgba(245, 158, 11, 0.9) !important; }
        .hover\:bg-primary\/20:hover { background-color: rgba(59, 130, 246, 0.2) !important; }
        .hover\:bg-secondary\/20:hover { background-color: rgba(236, 72, 153, 0.2) !important; }
        .hover\:bg-accent\/20:hover { background-color: rgba(245, 158, 11, 0.2) !important; }

        .hover\:text-primary:hover { color: #007cba !important; }
        .hover\:text-secondary:hover { color: #1ddede !important; }
        .hover\:text-accent:hover { color: #f8c709 !important; }
        .hover\:text-white:hover { color: #ffffff !important; }
        
        /* Gradient definitions */
        .bg-gradient-primary { background-image: linear-gradient(135deg, #3B82F6, #EC4899) !important; }
        .bg-gradient-secondary { background-image: linear-gradient(135deg, #EC4899, #F59E0B) !important; }
        .bg-gradient-accent { background-image: linear-gradient(135deg, #F59E0B, #FDE047) !important; }
        
        .bg-gradient-dark {
            background-image: linear-gradient(135deg, #231f20, #444444) !important;
        }
        
        /* Animation classes */
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        .animate-bounce-slow {
            animation: bounce 3s infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
    </style>
    <?php
}
add_action('wp_footer', 'sarah_loz_tailwind_config', 1);

/**
 * Handle user profile updates
 */
function sarah_loz_handle_profile_update() {
    if (!isset($_POST['profile_nonce']) || !wp_verify_nonce($_POST['profile_nonce'], 'update_user_profile_nonce')) {
        wp_die('Invalid nonce');
    }

    $user_id = get_current_user_id();
    $user = get_userdata($user_id);

    // Update user email if changed
    if (!empty($_POST['email']) && $_POST['email'] !== $user->user_email) {
        wp_update_user(array(
            'ID' => $user_id,
            'user_email' => sanitize_email($_POST['email'])
        ));
    }

    // Update parent name
    if (!empty($_POST['parent_name'])) {
        update_user_meta($user_id, 'parent_name', sanitize_text_field($_POST['parent_name']));
    }

    // Update child details
    if (!empty($_POST['child_name'])) {
        update_user_meta($user_id, 'child_name', sanitize_text_field($_POST['child_name']));
    }
    if (!empty($_POST['child_age'])) {
        update_user_meta($user_id, 'child_age', intval($_POST['child_age']));
    }

    // Handle password change
    if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        if ($_POST['new_password'] === $_POST['confirm_password']) {
            wp_set_password($_POST['new_password'], $user_id);
            // Log the user back in
            $user = get_user_by('id', $user_id);
            wp_set_current_user($user_id, $user->user_login);
            wp_set_auth_cookie($user_id);
            do_action('wp_login', $user->user_login, $user);
        }
    }

    wp_redirect(add_query_arg('updated', 'true', wp_get_referer()));
    exit;
}
add_action('admin_post_update_user_profile', 'sarah_loz_handle_profile_update');

/**
 * Handle custom registration with parent/child details
 */
function sarah_loz_custom_registration() {
    if (isset($_POST['user_login']) && isset($_POST['user_email']) && 'POST' === $_SERVER['REQUEST_METHOD']) {
        // Store parent name, child name, and child age in a transient
        // This will be used later when the user is actually created
        $registration_data = array(
            'parent_name' => isset($_POST['parent_name']) ? sanitize_text_field($_POST['parent_name']) : '',
            'child_name' => isset($_POST['child_name']) ? sanitize_text_field($_POST['child_name']) : '',
            'child_age' => isset($_POST['child_age']) ? intval($_POST['child_age']) : '',
            'user_email' => sanitize_email($_POST['user_email']),
        );
        
        // Store data for 30 minutes
        set_transient('sarah_loz_registration_' . md5($_POST['user_email']), $registration_data, 30 * MINUTE_IN_SECONDS);
    }
}
add_action('login_form_register', 'sarah_loz_custom_registration');

/**
 * Save custom user meta after registration
 */
function sarah_loz_save_registration_meta($user_id) {
    // Check if we have stored data for this user
    $user = get_userdata($user_id);
    
    if ($user) {
        $transient_key = 'sarah_loz_registration_' . md5($user->user_email);
        $registration_data = get_transient($transient_key);
        
        if ($registration_data) {
            // Save parent name
            if (!empty($registration_data['parent_name'])) {
                update_user_meta($user_id, 'parent_name', $registration_data['parent_name']);
            }
            
            // Save child name
            if (!empty($registration_data['child_name'])) {
                update_user_meta($user_id, 'child_name', $registration_data['child_name']);
            }
            
            // Save child age
            if (!empty($registration_data['child_age'])) {
                update_user_meta($user_id, 'child_age', $registration_data['child_age']);
            }
            
            // Clean up the transient
            delete_transient($transient_key);
        }
    }
}
add_action('user_register', 'sarah_loz_save_registration_meta', 10, 1);

/**
 * Add necessary hidden fields to the registration form
 */
function sarah_loz_add_registration_fields() {
    if (isset($_POST['user_login'])) {
        echo '<input type="hidden" name="sarah_loz_custom_register" value="1" />';
    }
}
add_action('register_form', 'sarah_loz_add_registration_fields');

/**
 * Track user progress
 */
function sarah_loz_track_progress($post_id) {
    if (!is_user_logged_in()) {
        return;
    }

    $user_id = get_current_user_id();
    $post_type = get_post_type($post_id);
    
    switch ($post_type) {
        case 'game':
            $completed_games = get_user_meta($user_id, 'completed_games', true);
            if (!is_array($completed_games)) {
                $completed_games = array();
            }
            if (!in_array($post_id, $completed_games)) {
                $completed_games[] = $post_id;
                update_user_meta($user_id, 'completed_games', $completed_games);
            }
            break;
            
        case 'activity':
            $completed_activities = get_user_meta($user_id, 'completed_activities', true);
            if (!is_array($completed_activities)) {
                $completed_activities = array();
            }
            if (!in_array($post_id, $completed_activities)) {
                $completed_activities[] = $post_id;
                update_user_meta($user_id, 'completed_activities', $completed_activities);
            }
            break;
            
        case 'video':
            $watched_videos = get_user_meta($user_id, 'watched_videos', true);
            if (!is_array($watched_videos)) {
                $watched_videos = array();
            }
            if (!in_array($post_id, $watched_videos)) {
                $watched_videos[] = $post_id;
                update_user_meta($user_id, 'watched_videos', $watched_videos);
            }
            break;
    }
}
add_action('wp_ajax_sarah_loz_track_progress', 'sarah_loz_track_progress');

/**
 * Customize registration email
 */
function sarah_loz_custom_registration_email($wp_new_user_notification_email, $user, $blogname) {
    $wp_new_user_notification_email['subject'] = sprintf('مرحباً بك في %s!', $blogname);
    $wp_new_user_notification_email['message'] = sprintf(
        "مرحباً بك في موقع سارة ولوز!\n\n" .
        "شكراً لتسجيلك في موقعنا. يمكنك الآن تسجيل الدخول باستخدام البريد الإلكتروني وكلمة المرور التي اخترتها.\n\n" .
        "رابط تسجيل الدخول: %s\n\n" .
        "نتمنى لك ولطفلك تجربة تعليمية ممتعة!\n\n" .
        "فريق سارة ولوز",
        wp_login_url()
    );
    return $wp_new_user_notification_email;
}
add_filter('wp_new_user_notification_email', 'sarah_loz_custom_registration_email', 10, 3);

/**
 * Add video functionality and JavaScript
 */
function sarah_loz_video_scripts() {
    if (is_singular('video') || is_post_type_archive('video') || is_tax(array('video_category', 'age_group', 'educational_skill'))) {
        // Enqueue video-specific scripts
        wp_enqueue_script('sarah-loz-video', get_template_directory_uri() . '/assets/js/video.js', array('jquery'), '1.0.0', true);
        
        // Localize script for AJAX
        wp_localize_script('sarah-loz-video', 'sarah_loz_video', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sarah_loz_video_nonce'),
        ));
    }
}
add_action('wp_enqueue_scripts', 'sarah_loz_video_scripts');

/**
 * Track video views via AJAX
 */
function sarah_loz_track_video_view() {
    // Check nonce
    check_ajax_referer('sarah_loz_video_nonce', 'nonce');
    
    // Get video ID
    $video_id = isset($_POST['video_id']) ? intval($_POST['video_id']) : 0;
    
    if ($video_id > 0) {
        // Get current view count
        $view_count = get_field('view_count', $video_id);
        
        // Increment view count
        $view_count = ($view_count) ? $view_count + 1 : 1;
        
        // Update view count
        update_field('view_count', $view_count, $video_id);
        
        // Return success
        wp_send_json_success(array(
            'view_count' => $view_count,
        ));
    } else {
        wp_send_json_error(array(
            'message' => __('Invalid video ID', 'sarah-loz'),
        ));
    }
    
    die();
}
add_action('wp_ajax_sarah_loz_track_video_view', 'sarah_loz_track_video_view');
add_action('wp_ajax_nopriv_sarah_loz_track_video_view', 'sarah_loz_track_video_view');

/**
 * Handle video like/unlike
 */
function sarah_loz_video_like() {
    // Check nonce
    check_ajax_referer('sarah_loz_video_nonce', 'nonce');
    
    // Only for logged in users
    if (!is_user_logged_in()) {
        wp_send_json_error(array(
            'message' => __('عليك تسجيل الدخول أولاً', 'sarah-loz'),
        ));
        die();
    }
    
    // Get video ID
    $video_id = isset($_POST['video_id']) ? intval($_POST['video_id']) : 0;
    $user_id = get_current_user_id();
    
    if ($video_id > 0) {
        // Get user's liked videos
        $liked_videos = get_user_meta($user_id, 'liked_videos', true);
        
        if (!is_array($liked_videos)) {
            $liked_videos = array();
        }
        
        // Check if video is already liked
        $is_liked = in_array($video_id, $liked_videos);
        
        if ($is_liked) {
            // Unlike video
            $liked_videos = array_diff($liked_videos, array($video_id));
            update_user_meta($user_id, 'liked_videos', $liked_videos);
            
            wp_send_json_success(array(
                'action' => 'unliked',
                'message' => __('تم إلغاء الإعجاب', 'sarah-loz'),
            ));
        } else {
            // Like video
            $liked_videos[] = $video_id;
            update_user_meta($user_id, 'liked_videos', $liked_videos);
            
            wp_send_json_success(array(
                'action' => 'liked',
                'message' => __('تم الإعجاب', 'sarah-loz'),
            ));
        }
    } else {
        wp_send_json_error(array(
            'message' => __('Invalid video ID', 'sarah-loz'),
        ));
    }
    
    die();
}
add_action('wp_ajax_sarah_loz_video_like', 'sarah_loz_video_like');
add_action('wp_ajax_nopriv_sarah_loz_video_like', 'sarah_loz_video_like');

/**
 * Handle video save/unsave
 */
function sarah_loz_video_save() {
    // Check nonce
    check_ajax_referer('sarah_loz_video_nonce', 'nonce');
    
    // Only for logged in users
    if (!is_user_logged_in()) {
        wp_send_json_error(array(
            'message' => __('عليك تسجيل الدخول أولاً', 'sarah-loz'),
        ));
        die();
    }
    
    // Get video ID
    $video_id = isset($_POST['video_id']) ? intval($_POST['video_id']) : 0;
    $user_id = get_current_user_id();
    
    if ($video_id > 0) {
        // Get user's saved videos
        $saved_videos = get_user_meta($user_id, 'saved_videos', true);
        
        if (!is_array($saved_videos)) {
            $saved_videos = array();
        }
        
        // Check if video is already saved
        $is_saved = in_array($video_id, $saved_videos);
        
        if ($is_saved) {
            // Unsave video
            $saved_videos = array_diff($saved_videos, array($video_id));
            update_user_meta($user_id, 'saved_videos', $saved_videos);
            
            wp_send_json_success(array(
                'action' => 'unsaved',
                'message' => __('تم إلغاء الحفظ', 'sarah-loz'),
            ));
        } else {
            // Save video
            $saved_videos[] = $video_id;
            update_user_meta($user_id, 'saved_videos', $saved_videos);
            
            wp_send_json_success(array(
                'action' => 'saved',
                'message' => __('تم الحفظ', 'sarah-loz'),
            ));
        }
    } else {
        wp_send_json_error(array(
            'message' => __('Invalid video ID', 'sarah-loz'),
        ));
    }
    
    die();
}
add_action('wp_ajax_sarah_loz_video_save', 'sarah_loz_video_save');
add_action('wp_ajax_nopriv_sarah_loz_video_save', 'sarah_loz_video_save');

/**
 * Allow more HTML tags for video embeds
 */
function sarah_loz_allow_more_html_tags($tags, $context) {
    if ($context === 'post') {
        // Allow iframes with needed attributes
        $tags['iframe'] = array(
            'src'             => true,
            'width'           => true,
            'height'          => true,
            'frameborder'     => true,
            'allowfullscreen' => true,
            'scrolling'       => true,
            'class'           => true,
            'id'              => true,
            'style'           => true,
            'title'           => true,
            'allow'           => true,
        );
        
        // Allow video and source tags
        $tags['video'] = array(
            'autoplay'        => true,
            'controls'        => true,
            'height'          => true,
            'loop'            => true,
            'muted'           => true,
            'poster'          => true,
            'preload'         => true,
            'src'             => true,
            'width'           => true,
            'class'           => true,
            'id'              => true,
            'style'           => true,
        );
        
        $tags['source'] = array(
            'src'             => true,
            'type'            => true,
        );
    }
    
    return $tags;
}
add_filter('wp_kses_allowed_html', 'sarah_loz_allow_more_html_tags', 10, 2);

/**
 * Format video URL to embed code
 */
function sarah_loz_format_video_embed($video_url) {
    // Check if it's already an embed code (contains iframe tag)
    if (strpos($video_url, '<iframe') !== false) {
        return $video_url;
    }
    
    // YouTube URL patterns
    $youtube_patterns = array(
        '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
        '/youtu\.be\/([a-zA-Z0-9_-]+)/',
        '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/'
    );
    
    // Check for YouTube URLs
    foreach ($youtube_patterns as $pattern) {
        if (preg_match($pattern, $video_url, $matches)) {
            $video_id = $matches[1];
            return '<iframe width="100%" height="100%" src="https://www.youtube.com/embed/' . $video_id . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        }
    }
    
    // Vimeo URL patterns
    $vimeo_pattern = '/vimeo\.com\/([0-9]+)/';
    if (preg_match($vimeo_pattern, $video_url, $matches)) {
        $video_id = $matches[1];
        return '<iframe src="https://player.vimeo.com/video/' . $video_id . '" width="100%" height="100%" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
    }
    
    // If no patterns matched, return original URL
    return $video_url;
}

/**
 * Ensure WooCommerce parameters are loaded
 */
function sarah_loz_woocommerce_scripts() {
    if (class_exists('WooCommerce')) {
        // Make sure the woocommerce_params are available
        wp_localize_script('jquery', 'woocommerce_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'wc_ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%')
        ));
    }
}
add_action('wp_enqueue_scripts', 'sarah_loz_woocommerce_scripts');

// Remove the WooCommerce debugging function
function sarah_loz_debug_woocommerce_templates() {
    // Empty function to replace the debugging function
}

// Ensure WooCommerce page containers work correctly
function sarah_loz_woocommerce_wrapper_before() {
    if (is_cart() || is_checkout()) {
        echo '<div class="woocommerce-page-wrapper container mx-auto px-4 py-10">';
    }
}
add_action('woocommerce_before_main_content', 'sarah_loz_woocommerce_wrapper_before', 5);

function sarah_loz_woocommerce_wrapper_after() {
    if (is_cart() || is_checkout()) {
        echo '</div><!-- .woocommerce-page-wrapper -->';
    }
}
add_action('woocommerce_after_main_content', 'sarah_loz_woocommerce_wrapper_after', 50);

// Fix deprecated WooCommerce functions and ACF loading issues
function sarah_loz_fix_warnings_and_errors() {
    // Fix for WooCommerce deprecated is_store_page function
    if (class_exists('Automattic\WooCommerce\Admin\WCAdminHelper')) {
        // Remove the deprecated notice by defining a custom error handler temporarily
        set_error_handler(function($errno, $errstr) {
            return strpos($errstr, 'is_store_page') !== false;
        }, E_DEPRECATED);
        
        // Use the new function if available
        if (!function_exists('wc_admin_is_store_page_replacement')) {
            function wc_admin_is_store_page_replacement() {
                if (function_exists('Automattic\WooCommerce\Admin\Features\Navigation\is_current_page_store_page')) {
                    return Automattic\WooCommerce\Admin\Features\Navigation\is_current_page_store_page();
                }
                return false;
            }
        }
        
        // Restore normal error handling
        restore_error_handler();
    }
    
    // Fix ACF translation loading timing issue
    if (class_exists('ACF')) {
        remove_action('plugins_loaded', array('ACF', 'init'), 5);
        add_action('init', array('ACF', 'init'), 5);
    }
}
add_action('plugins_loaded', 'sarah_loz_fix_warnings_and_errors', 1);

// Add a custom error handler for certain warnings and notices
function sarah_loz_custom_error_handler() {
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        // Ignore specific ACF textdomain errors
        if (strpos($errstr, 'Translation loading for the <code>acf</code> domain was triggered too early') !== false) {
            return true; // Suppress this error
        }
        
        // Ignore specific WooCommerce deprecated function warnings
        if (strpos($errstr, 'Automattic\WooCommerce\Admin\WCAdminHelper::is_store_page') !== false) {
            return true; // Suppress this error
        }
        
        // Let PHP handle all other errors
        return false;
    }, E_NOTICE | E_DEPRECATED);
}
add_action('init', 'sarah_loz_custom_error_handler', 1);

// Force WooCommerce to use proper templates for cart and checkout pages
function sarah_loz_force_woocommerce_page_templates($template) {
    // Check if it's a cart, checkout, or account page
    if (is_cart() || is_checkout() || is_account_page()) {
        // Remove any filters that might be overriding the template
        remove_all_filters('template_include', 100);
        
        // Define paths
        $wc_template_path = WC()->plugin_path() . '/templates/';
        $theme_template_path = get_stylesheet_directory() . '/woocommerce/';
        
        if (is_cart()) {
            // Check if theme has a cart template
            $cart_file = 'cart/cart.php';
            $cart_theme_file = $theme_template_path . $cart_file;
            
            if (file_exists($cart_theme_file)) {
                return $cart_theme_file;
            } else {
                return $wc_template_path . $cart_file;
            }
        }
        
        if (is_checkout()) {
            // Check if theme has a checkout template
            $checkout_file = 'checkout/form-checkout.php';
            $checkout_theme_file = $theme_template_path . $checkout_file;
            
            if (file_exists($checkout_theme_file)) {
                return $checkout_theme_file;
            } else {
                return $wc_template_path . $checkout_file;
            }
        }
        
        if (is_account_page()) {
            // For account pages, use our custom template
            $template_path = get_stylesheet_directory() . '/woocommerce-page.php';
            if (file_exists($template_path)) {
                return $template_path;
            }
        }
    }
    
    return $template;
}
add_filter('template_include', 'sarah_loz_force_woocommerce_page_templates', 999);

/**
 * Handle saving of custom fields in WooCommerce account
 */
function sarah_loz_save_account_details($user_id) {
    // Save parent and child information
    if (isset($_POST['parent_name']) && !empty($_POST['parent_name'])) {
        update_user_meta($user_id, 'parent_name', sanitize_text_field($_POST['parent_name']));
    }
    
    if (isset($_POST['child_name']) && !empty($_POST['child_name'])) {
        update_user_meta($user_id, 'child_name', sanitize_text_field($_POST['child_name']));
    }
    
    if (isset($_POST['child_age']) && !empty($_POST['child_age'])) {
        update_user_meta($user_id, 'child_age', intval($_POST['child_age']));
    }
}
add_action('woocommerce_save_account_details', 'sarah_loz_save_account_details');

/**
 * Redirect old account pages to WooCommerce account
 */
function sarah_loz_redirect_account_pages() {
    if (!is_user_logged_in()) {
        return;
    }

    $current_page_id = get_queried_object_id();
    $profile_page_id = get_page_by_path('profile');
    $my_account_url = wc_get_page_permalink('myaccount');

    // If we're on the old profile page, redirect to WooCommerce account
    if ($profile_page_id && $profile_page_id->ID === $current_page_id) {
        wp_redirect($my_account_url);
        exit;
    }
}
add_action('template_redirect', 'sarah_loz_redirect_account_pages');

/**
 * Modify WooCommerce account menu items to include Arabic translations
 */
function sarah_loz_account_menu_items($items) {
    // Customize the labels with Arabic text
    $items['dashboard'] = 'لوحة التحكم';
    $items['orders'] = 'الطلبات';
    $items['downloads'] = 'التنزيلات';
    $items['edit-address'] = 'العناوين';
    $items['edit-account'] = 'تعديل الحساب';
    $items['customer-logout'] = 'تسجيل الخروج';
    
    return $items;
}
add_filter('woocommerce_account_menu_items', 'sarah_loz_account_menu_items');

/**
 * Add form validation for custom fields in WooCommerce account
 */
function sarah_loz_woocommerce_save_account_validation($errors, $user) {
    if (empty($_POST['parent_name'])) {
        $errors->add('parent_name_error', __('يرجى إدخال اسم ولي الأمر', 'sarah-loz'));
    }
    
    if (empty($_POST['child_name'])) {
        $errors->add('child_name_error', __('يرجى إدخال اسم الطفل', 'sarah-loz'));
    }
    
    if (empty($_POST['child_age'])) {
        $errors->add('child_age_error', __('يرجى اختيار عمر الطفل', 'sarah-loz'));
    }
    
    return $errors;
}
add_filter('woocommerce_save_account_details_errors', 'sarah_loz_woocommerce_save_account_validation', 10, 2);

// Add debugging for WooCommerce templates
function sarah_loz_debug_woocommerce_template_include($template) {
    if (is_user_logged_in() && current_user_can('administrator') && (is_account_page() || is_checkout() || is_cart())) {
        // Add a comment at the top of the page showing which template is being used
        add_action('wp_footer', function() use ($template) {
            echo '<!-- Template used: ' . $template . ' -->';
            
            // Show additional debugging info for the account page
            if (is_account_page()) {
                echo '<!-- 
                Debug info:
                is_account_page(): true
                WooCommerce shortcode: [woocommerce_my_account]
                -->';
            }
        });
    }
    return $template;
}
add_filter('template_include', 'sarah_loz_debug_woocommerce_template_include', 1000);

/**
 * Force enable user registration even if it's disabled in WordPress settings
 */
function sarah_loz_enable_registration() {
    // Force enable registration for our custom form
    add_filter('pre_option_users_can_register', function() {
        return 1; // Enable registration
    });
}
add_action('init', 'sarah_loz_enable_registration');

/**
 * Handle custom registration form submission
 */
function sarah_loz_custom_register_user() {
    // Only process on our custom registration page
    if (!isset($_POST['woocommerce-register-nonce']) || 
        !wp_verify_nonce($_POST['woocommerce-register-nonce'], 'woocommerce-register')) {
        return;
    }
    
    // Check if all required fields are present
    if (empty($_POST['user_login']) || empty($_POST['user_email']) || empty($_POST['user_pass'])) {
        return;
    }
    
    // Create the user
    $user_id = wp_create_user(
        sanitize_user($_POST['user_login']),
        $_POST['user_pass'],
        sanitize_email($_POST['user_email'])
    );
    
    if (is_wp_error($user_id)) {
        // If there's an error, redirect back to the registration form with error message
        wp_redirect(add_query_arg('registration_error', urlencode($user_id->get_error_message()), wc_get_page_permalink('myaccount') . '?action=register'));
        exit;
    }
    
    // Save custom user meta
    if (!empty($_POST['parent_name'])) {
        update_user_meta($user_id, 'parent_name', sanitize_text_field($_POST['parent_name']));
    }
    
    if (!empty($_POST['child_name'])) {
        update_user_meta($user_id, 'child_name', sanitize_text_field($_POST['child_name']));
    }
    
    if (!empty($_POST['child_age'])) {
        update_user_meta($user_id, 'child_age', intval($_POST['child_age']));
    }
    
    // Log the user in
    $user = get_user_by('id', $user_id);
    wp_set_current_user($user_id, $user->user_login);
    wp_set_auth_cookie($user_id);
    do_action('wp_login', $user->user_login, $user);
    
    // Redirect to the account page
    wp_redirect(wc_get_page_permalink('myaccount'));
    exit;
}
add_action('template_redirect', 'sarah_loz_custom_register_user');

/**
 * Log registration errors for debugging purposes
 */
function sarah_loz_log_registration_errors($user_id, $message, $error_code) {
    // Only log errors in debug mode
    if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
        error_log('Registration error: ' . $error_code . ' - ' . $message);
    }
}
add_action('register_post', 'sarah_loz_log_registration_errors', 10, 3);

/**
 * Show admin notice to enable user registration if it's disabled
 */
function sarah_loz_admin_notice_registration() {
    if (is_admin() && current_user_can('manage_options')) {
        if (get_option('users_can_register') != 1) {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p><strong>Sarah Loz Theme:</strong> User registration is disabled in WordPress settings. The theme will override this for the custom registration form, but it's recommended to enable it in <a href="<?php echo admin_url('options-general.php'); ?>">Settings → General</a> by checking "Anyone can register".</p>
            </div>
            <?php
        }
    }
}
add_action('admin_notices', 'sarah_loz_admin_notice_registration');

/**
 * Set up theme
 */
function sarah_loz_theme_setup() {
    // Make theme available for translation
    load_theme_textdomain('sarah-loz-theme', get_template_directory() . '/languages');
    
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');
    
    // Let WordPress manage the document title
    add_theme_support('title-tag');
    
    // Enable support for Post Thumbnails on posts and pages
    add_theme_support('post-thumbnails');
    
    // Add support for RTL
    add_theme_support('align-wide');
    
    // Register nav menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'sarah-loz'),
        'footer' => esc_html__('Footer Menu', 'sarah-loz'),
    ));
    
    // Auto-add topics page to primary menu if it doesn't exist
    add_action('wp_loaded', 'sarah_loz_auto_add_topics_to_menu');
}
add_action('after_setup_theme', 'sarah_loz_theme_setup');

/**
 * Flush rewrite rules when theme is activated
 */
function sarah_loz_theme_activate() {
    // Call the function that adds the favorites endpoint
    if (function_exists('sarah_loz_add_favorites_endpoint')) {
        sarah_loz_add_favorites_endpoint();
    }
    
    // Call the query vars function
    if (function_exists('sarah_loz_favorites_query_vars')) {
        add_filter('query_vars', 'sarah_loz_favorites_query_vars', 0);
    }
    
    // Call the menu item function
    if (function_exists('sarah_loz_add_favorites_menu_item')) {
        add_filter('woocommerce_account_menu_items', 'sarah_loz_add_favorites_menu_item');
    }
    
    // Call the content display function
    if (function_exists('sarah_loz_favorites_content')) {
        add_action('woocommerce_account_favorites_endpoint', 'sarah_loz_favorites_content');
    }
    
    // Setup the favorites endpoint
    if (function_exists('sarah_loz_setup_account_favorites')) {
        sarah_loz_setup_account_favorites();
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'sarah_loz_theme_activate');

/**
 * Force refresh favorites endpoint and rewrite rules on admin page load
 * This helps ensure the endpoint is properly registered after theme switching
 */
function sarah_loz_force_refresh_endpoints() {
    // Only run this once per session
    if (!get_transient('sarah_loz_endpoints_refreshed')) {
        // Make sure the favorites endpoint is registered
        if (function_exists('sarah_loz_add_favorites_endpoint')) {
            sarah_loz_add_favorites_endpoint();
        }
        
        // Add query vars
        if (function_exists('sarah_loz_favorites_query_vars')) {
            add_filter('query_vars', 'sarah_loz_favorites_query_vars');
        }
        
        // Force flush rewrite rules
        flush_rewrite_rules();
        
        // Set a transient to prevent running this on every page load
        set_transient('sarah_loz_endpoints_refreshed', true, HOUR_IN_SECONDS);
        
        // Log for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Sarah Loz Theme: Favorites endpoint refreshed and rewrite rules flushed');
        }
    }
}
add_action('admin_init', 'sarah_loz_force_refresh_endpoints');

/**
 * Add debugging tools for the favorites endpoint
 * This will show information in the footer for admins only
 */
function sarah_loz_debug_favorites_endpoint() {
    // Only show for admin users
    if (!current_user_can('administrator')) {
        return;
    }
    
    // Check if we're on the My Account page
    if (!is_account_page()) {
        return;
    }
    
    global $wp_rewrite;
    
    // Check if the favorites endpoint exists
    $endpoints = $wp_rewrite->endpoints;
    $favorites_endpoint_exists = false;
    
    foreach ($endpoints as $endpoint) {
        if ($endpoint[1] === 'favorites') {
            $favorites_endpoint_exists = true;
            break;
        }
    }
    
    // Get query vars
    global $wp;
    $query_vars = $wp->public_query_vars;
    $favorites_query_var_exists = in_array('favorites', $query_vars);
    
    // Get the woocommerce endpoint
    $wc_endpoints = WC()->query->get_query_vars();
    $wc_endpoint_exists = isset($wc_endpoints['favorites']);
    
    // Check if the favorites tab exists in the menu
    $menu_items = wc_get_account_menu_items();
    $favorites_menu_exists = isset($menu_items['favorites']);
    
    // Output debug info
    ?>
    <div style="margin-top: 50px; padding: 15px; background: #f8f8f8; border: 1px solid #ddd; display: none;">
        <h3>Favorites Endpoint Debug (Admin Only)</h3>
        <button onclick="jQuery('#sarah_loz_debug_info').toggle(); return false;">Show/Hide Debug Info</button>
        
        <div id="sarah_loz_debug_info" style="display: none; margin-top: 10px;">
            <ul>
                <li>WP Rewrite Endpoint Exists: <?php echo $favorites_endpoint_exists ? 'Yes' : 'No'; ?></li>
                <li>Query Var Exists: <?php echo $favorites_query_var_exists ? 'Yes' : 'No'; ?></li>
                <li>WooCommerce Endpoint Exists: <?php echo $wc_endpoint_exists ? 'Yes' : 'No'; ?></li>
                <li>Menu Item Exists: <?php echo $favorites_menu_exists ? 'Yes' : 'No'; ?></li>
            </ul>
            
            <p><strong>Fix Endpoints:</strong></p>
            <a href="<?php echo esc_url(admin_url('admin.php?page=wc-settings&tab=advanced&section=sarah_loz_force_refresh')); ?>" 
               style="display: inline-block; padding: 5px 10px; background: #0073aa; color: white; text-decoration: none; border-radius: 3px;">
                Force Refresh Endpoints
            </a>
            
            <p><strong>Menu Items:</strong></p>
            <pre><?php print_r($menu_items); ?></pre>
        </div>
    </div>
    <script>
    jQuery(document).ready(function($) {
        // Move debug panel to bottom of page
        $('div.woocommerce').after($('div.woocommerce + div').detach().show());
    });
    </script>
    <?php
}
add_action('wp_footer', 'sarah_loz_debug_favorites_endpoint');

/**
 * Handle forcing endpoint refresh
 */
function sarah_loz_handle_force_endpoint_refresh() {
    // Check if we're on the settings page with our parameter
    if (isset($_GET['page']) && $_GET['page'] === 'wc-settings' && 
        isset($_GET['tab']) && $_GET['tab'] === 'advanced' && 
        isset($_GET['section']) && $_GET['section'] === 'sarah_loz_force_refresh') {
        
        // Register the endpoint
        sarah_loz_add_favorites_endpoint();
        
        // Add query vars
        add_filter('query_vars', 'sarah_loz_favorites_query_vars');
        
        // Force flush rewrite rules
        flush_rewrite_rules();
        
        // Set success message
        WC_Admin_Notices::add_custom_notice('sarah_loz_endpoints_refreshed', 'Favorites endpoint refreshed successfully.');
        
        // Redirect to WooCommerce settings page
        wp_redirect(admin_url('admin.php?page=wc-settings&tab=advanced'));
        exit;
    }
}
add_action('admin_init', 'sarah_loz_handle_force_endpoint_refresh');

/**
 * Custom AJAX handler for adding to cart with immediate cart count response
 */
function sarah_loz_ajax_add_to_cart() {
    // Check nonce
    check_ajax_referer('sarah-loz-add-to-cart', 'security');
    
    // Get product data
    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;
    $variation_id = isset($_POST['variation_id']) ? absint($_POST['variation_id']) : 0;
    $variations = isset($_POST['variations']) ? (array) $_POST['variations'] : array();
    
    // Validate product ID
    if ($product_id <= 0) {
        wp_send_json_error(array('message' => __('Invalid product ID', 'sarah-loz')));
        exit;
    }
    
    // Add to cart
    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations);
    
    if ($passed_validation) {
        // Add the item to the cart
        if (WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variations)) {
            // Return success with cart count
            wp_send_json_success(array(
                'message' => __('Product added to cart', 'sarah-loz'),
                'cart_count' => WC()->cart->get_cart_contents_count(),
                'product_id' => $product_id
            ));
        } else {
            wp_send_json_error(array('message' => __('Error adding product to cart', 'sarah-loz')));
        }
    } else {
        wp_send_json_error(array('message' => __('Product validation failed', 'sarah-loz')));
    }
    
    exit;
}
add_action('wp_ajax_sarah_loz_ajax_add_to_cart', 'sarah_loz_ajax_add_to_cart');
add_action('wp_ajax_nopriv_sarah_loz_ajax_add_to_cart', 'sarah_loz_ajax_add_to_cart');

/**
 * Handle reset child points
 */
function sarah_loz_reset_child_points() {
    // Check if user is logged in and has parent role
    if (!is_user_logged_in() || !in_array('parent', (array) wp_get_current_user()->roles)) {
        wp_die('غير مصرح لك بهذا الإجراء.');
    }
    
    // Verify nonce
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'reset_child_points')) {
        wp_die('فشل التحقق الأمني.');
    }
    
    // Get child ID
    $child_id = isset($_GET['child_id']) ? intval($_GET['child_id']) : 0;
    if ($child_id === 0) {
        wp_die('معرف الطفل غير صحيح.');
    }
    
    // Check if the child belongs to this parent
    $parent_id = get_user_meta($child_id, 'parent_id', true);
    if ($parent_id != get_current_user_id()) {
        wp_die('غير مصرح لك بإدارة هذا الطفل.');
    }
    
    // Reset points
    update_user_meta($child_id, 'activity_points', 0);
    update_user_meta($child_id, 'total_points', get_user_meta($child_id, 'achievement_points', true) ?: 0);
    
    // Redirect back to child settings
    wp_redirect(add_query_arg(array(
        'child_id' => $child_id,
        'reset' => 'points'
    ), home_url('/child-settings/')));
    exit;
}
add_action('admin_post_reset_points', 'sarah_loz_reset_child_points');

/**
 * Handle reset child achievements
 */
function sarah_loz_reset_child_achievements() {
    // Check if user is logged in and has parent role
    if (!is_user_logged_in() || !in_array('parent', (array) wp_get_current_user()->roles)) {
        wp_die('غير مصرح لك بهذا الإجراء.');
    }
    
    // Verify nonce
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'reset_child_achievements')) {
        wp_die('فشل التحقق الأمني.');
    }
    
    // Get child ID
    $child_id = isset($_GET['child_id']) ? intval($_GET['child_id']) : 0;
    if ($child_id === 0) {
        wp_die('معرف الطفل غير صحيح.');
    }
    
    // Check if the child belongs to this parent
    $parent_id = get_user_meta($child_id, 'parent_id', true);
    if ($parent_id != get_current_user_id()) {
        wp_die('غير مصرح لك بإدارة هذا الطفل.');
    }
    
    // Reset achievements
    update_user_meta($child_id, 'user_achievements', array());
    update_user_meta($child_id, 'achievement_points', 0);
    update_user_meta($child_id, 'total_points', get_user_meta($child_id, 'activity_points', true) ?: 0);
    
    // Redirect back to child settings
    wp_redirect(add_query_arg(array(
        'child_id' => $child_id,
        'reset' => 'achievements'
    ), home_url('/child-settings/')));
    exit;
}
add_action('admin_post_reset_achievements', 'sarah_loz_reset_child_achievements');

/**
 * Handle reset child activity log
 */
function sarah_loz_reset_child_activity_log() {
    // Check if user is logged in and has parent role
    if (!is_user_logged_in() || !in_array('parent', (array) wp_get_current_user()->roles)) {
        wp_die('غير مصرح لك بهذا الإجراء.');
    }
    
    // Verify nonce
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'reset_child_activity_log')) {
        wp_die('فشل التحقق الأمني.');
    }
    
    // Get child ID
    $child_id = isset($_GET['child_id']) ? intval($_GET['child_id']) : 0;
    if ($child_id === 0) {
        wp_die('معرف الطفل غير صحيح.');
    }
    
    // Check if the child belongs to this parent
    $parent_id = get_user_meta($child_id, 'parent_id', true);
    if ($parent_id != get_current_user_id()) {
        wp_die('غير مصرح لك بإدارة هذا الطفل.');
    }
    
    // Reset activity log
    update_user_meta($child_id, 'child_activity_log', array());
    update_user_meta($child_id, 'activity_timeline', array());
    
    // Redirect back to child settings
    wp_redirect(add_query_arg(array(
        'child_id' => $child_id,
        'reset' => 'activity_log'
    ), home_url('/child-settings/')));
    exit;
}
add_action('admin_post_reset_activity_log', 'sarah_loz_reset_child_activity_log');

/**
 * Add child activity tracking hooks
 */
function sarah_loz_add_activity_tracking_hooks() {
    if (!function_exists('sarah_loz_child_activity_tracker')) {
        return;
    }

    // Game tracking hooks
    add_action('wp_ajax_sarah_loz_start_game', function() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sarah_loz_activity_nonce')) {
            wp_send_json_error('Invalid security token');
            return;
        }
        
        $game_id = isset($_POST['game_id']) ? intval($_POST['game_id']) : 0;
        if ($game_id > 0) {
            do_action('sarah_loz_game_started', get_current_user_id(), $game_id);
        }
        wp_send_json_success();
    });
    
    add_action('wp_ajax_sarah_loz_complete_game', function() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sarah_loz_activity_nonce')) {
            wp_send_json_error('Invalid security token');
            return;
        }
        
        $game_id = isset($_POST['game_id']) ? intval($_POST['game_id']) : 0;
        if ($game_id > 0) {
            do_action('sarah_loz_game_completed', get_current_user_id(), $game_id);
        }
        wp_send_json_success();
    });
    
    // Activity tracking hooks
    add_action('wp_ajax_sarah_loz_start_activity', function() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sarah_loz_activity_nonce')) {
            wp_send_json_error('Invalid security token');
            return;
        }
        
        $activity_id = isset($_POST['activity_id']) ? intval($_POST['activity_id']) : 0;
        if ($activity_id > 0) {
            do_action('sarah_loz_activity_started', get_current_user_id(), $activity_id);
        }
        wp_send_json_success();
    });
    
    add_action('wp_ajax_sarah_loz_complete_activity', function() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sarah_loz_activity_nonce')) {
            wp_send_json_error('Invalid security token');
            return;
        }
        
        $activity_id = isset($_POST['activity_id']) ? intval($_POST['activity_id']) : 0;
        if ($activity_id > 0) {
            do_action('sarah_loz_activity_completed', get_current_user_id(), $activity_id);
        }
        wp_send_json_success();
    });
    
    // Video tracking hooks
    add_action('wp_ajax_sarah_loz_start_video', function() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sarah_loz_activity_nonce')) {
            wp_send_json_error('Invalid security token');
            return;
        }
        
        $video_id = isset($_POST['video_id']) ? intval($_POST['video_id']) : 0;
        if ($video_id > 0) {
            do_action('sarah_loz_video_started', get_current_user_id(), $video_id);
        }
        wp_send_json_success();
    });
    
    add_action('wp_ajax_sarah_loz_complete_video', function() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sarah_loz_activity_nonce')) {
            wp_send_json_error('Invalid security token');
            return;
        }
        
        $video_id = isset($_POST['video_id']) ? intval($_POST['video_id']) : 0;
        if ($video_id > 0) {
            do_action('sarah_loz_video_completed', get_current_user_id(), $video_id);
        }
        wp_send_json_success();
    });
    
    // Practice tracking hooks
    add_action('wp_ajax_sarah_loz_start_practice', function() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sarah_loz_activity_nonce')) {
            wp_send_json_error('Invalid security token');
            return;
        }
        
        $practice_id = isset($_POST['practice_id']) ? intval($_POST['practice_id']) : 0;
        if ($practice_id > 0) {
            do_action('sarah_loz_practice_started', get_current_user_id(), $practice_id);
        }
        wp_send_json_success();
    });
    
    add_action('wp_ajax_sarah_loz_complete_practice', function() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sarah_loz_activity_nonce')) {
            wp_send_json_error('Invalid security token');
            return;
        }
        
        $practice_id = isset($_POST['practice_id']) ? intval($_POST['practice_id']) : 0;
        if ($practice_id > 0) {
            do_action('sarah_loz_practice_completed', get_current_user_id(), $practice_id);
        }
        wp_send_json_success();
    });
    
    // Share content hook
    add_action('wp_ajax_sarah_loz_share_content', function() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sarah_loz_activity_nonce')) {
            wp_send_json_error('Invalid security token');
            return;
        }
        
        $content_id = isset($_POST['content_id']) ? intval($_POST['content_id']) : 0;
        $share_type = isset($_POST['share_type']) ? sanitize_text_field($_POST['share_type']) : '';
        
        if ($content_id > 0 && !empty($share_type)) {
            do_action('sarah_loz_content_shared', get_current_user_id(), $content_id, $share_type);
        }
        wp_send_json_success();
    });
}
add_action('init', 'sarah_loz_add_activity_tracking_hooks');

/**
 * Add activity tracking scripts
 */
function sarah_loz_activity_tracking_scripts() {
    if (is_user_logged_in()) {
        wp_enqueue_script('sarah-loz-activity-tracking', get_template_directory_uri() . '/assets/js/activity-tracking.js', array('jquery'), '1.0.0', true);
        wp_localize_script('sarah-loz-activity-tracking', 'sarah_loz_activity', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sarah_loz_activity_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'sarah_loz_activity_tracking_scripts');

/**
 * Add custom user roles for parent and child
 */
function sarah_loz_add_user_roles() {
    // Add parent role if it doesn't exist
    if (!get_role('parent')) {
        add_role(
            'parent',
            'Parent',
            array(
                'read' => true,
                'edit_posts' => false,
                'delete_posts' => false,
                'publish_posts' => false,
                'upload_files' => false,
            )
        );
    }
    
    // Add child role if it doesn't exist
    if (!get_role('child')) {
        add_role(
            'child',
            'Child',
            array(
                'read' => true,
                'edit_posts' => false,
                'delete_posts' => false,
                'publish_posts' => false,
                'upload_files' => false,
            )
        );
    }
}
add_action('init', 'sarah_loz_add_user_roles');

/**
 * Add custom endpoints to WooCommerce My Account
 */
function sarah_loz_add_woocommerce_endpoints() {
    // Add endpoints for parent
    add_rewrite_endpoint('child-dashboard', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('child-reports', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('child-settings', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('add-child', EP_ROOT | EP_PAGES);
    
    // Force flush rewrite rules
    flush_rewrite_rules();
    
    // Update option to indicate we've flushed the rules
    update_option('sarah_loz_flush_rewrite_rules', true);
}
add_action('init', 'sarah_loz_add_woocommerce_endpoints');

/**
 * Add new items to WooCommerce account menu
 */
function sarah_loz_add_woocommerce_account_menu_items($items) {
    $current_user = wp_get_current_user();
    
    // Add menu items based on user role
    if (in_array('parent', (array) $current_user->roles)) {
        // For parent users, add child management tabs
        $new_items = array(
            'dashboard' => $items['dashboard'],
            'child-dashboard' => 'لوحة تحكم الطفل',
            'add-child' => 'إضافة طفل جديد',
            'child-reports' => 'تقارير الطفل',
            'child-settings' => 'إعدادات الطفل'
        );
        
        // Add remaining items
        foreach ($items as $endpoint => $label) {
            if (!isset($new_items[$endpoint])) {
                $new_items[$endpoint] = $label;
            }
        }
        
        return $new_items;
    } elseif (in_array('child', (array) $current_user->roles)) {
        // For child users, simplify the menu
        $new_items = array(
            'dashboard' => $items['dashboard'],
            'child-dashboard' => 'لوحة التحكم الخاصة بي',
            'edit-account' => $items['edit-account'],
            'customer-logout' => $items['customer-logout']
        );
        
        return $new_items;
    }
    
    return $items;
}
add_filter('woocommerce_account_menu_items', 'sarah_loz_add_woocommerce_account_menu_items', 10);

/**
 * Add endpoint content for child dashboard
 */
function sarah_loz_child_dashboard_endpoint_content() {
    $current_user = wp_get_current_user();
    
    if (in_array('child', (array) $current_user->roles)) {
        // Display child dashboard for child users
        include(get_template_directory() . '/woocommerce/myaccount/child-dashboard.php');
    } elseif (in_array('parent', (array) $current_user->roles)) {
        // Display parent view of child dashboard
        include(get_template_directory() . '/woocommerce/myaccount/parent-child-dashboard.php');
    } else {
        echo '<p>غير مصرح لك بالوصول إلى هذه الصفحة.</p>';
    }
}
add_action('woocommerce_account_child-dashboard_endpoint', 'sarah_loz_child_dashboard_endpoint_content');

/**
 * Add endpoint content for child reports
 */
function sarah_loz_child_reports_endpoint_content() {
    $current_user = wp_get_current_user();
    
    if (in_array('parent', (array) $current_user->roles)) {
        include(get_template_directory() . '/woocommerce/myaccount/child-reports.php');
    } else {
        echo '<p>غير مصرح لك بالوصول إلى هذه الصفحة.</p>';
    }
}
add_action('woocommerce_account_child-reports_endpoint', 'sarah_loz_child_reports_endpoint_content');

/**
 * Add endpoint content for child settings
 */
function sarah_loz_child_settings_endpoint_content() {
    $current_user = wp_get_current_user();
    
    if (in_array('parent', (array) $current_user->roles)) {
        include(get_template_directory() . '/woocommerce/myaccount/child-settings.php');
    } else {
        echo '<p>غير مصرح لك بالوصول إلى هذه الصفحة.</p>';
    }
}
add_action('woocommerce_account_child-settings_endpoint', 'sarah_loz_child_settings_endpoint_content');

/**
 * Add endpoint content for add child
 */
function sarah_loz_add_child_endpoint_content() {
    $current_user = wp_get_current_user();
    
    if (in_array('parent', (array) $current_user->roles)) {
        include(get_template_directory() . '/woocommerce/myaccount/add-child.php');
    } else {
        echo '<p>غير مصرح لك بالوصول إلى هذه الصفحة.</p>';
    }
}
add_action('woocommerce_account_add-child_endpoint', 'sarah_loz_add_child_endpoint_content');

/**
 * Add icons to WooCommerce account menu items
 */
function sarah_loz_add_woocommerce_account_menu_icons($items) {
    if (isset($items['child-dashboard'])) {
        $items['child-dashboard'] = '<i class="fas fa-child ml-2"></i>' . $items['child-dashboard'];
    }
    
    if (isset($items['child-reports'])) {
        $items['child-reports'] = '<i class="fas fa-chart-bar ml-2"></i>' . $items['child-reports'];
    }
    
    if (isset($items['child-settings'])) {
        $items['child-settings'] = '<i class="fas fa-cog ml-2"></i>' . $items['child-settings'];
    }
    
    if (isset($items['add-child'])) {
        $items['add-child'] = '<i class="fas fa-plus ml-2"></i>' . $items['add-child'];
    }
    
    return $items;
}
add_filter('woocommerce_account_menu_items', 'sarah_loz_add_woocommerce_account_menu_icons', 30);

/**
 * Modify WooCommerce registration form to include parent/child selection
 */
function sarah_loz_woocommerce_registration_form_fields() {
    ?>
    <p class="form-row form-row-wide">
        <label for="user_role"><?php _e('نوع الحساب', 'sarah-loz'); ?> <span class="required">*</span></label>
        <select name="user_role" id="user_role" class="woocommerce-Input woocommerce-Input--select input-select">
            <option value="parent"><?php _e('والد/ة', 'sarah-loz'); ?></option>
            <option value="child"><?php _e('طفل', 'sarah-loz'); ?></option>
        </select>
    </p>
    
    <div id="parent_fields" style="display:block;">
        <p class="form-row form-row-wide">
            <label for="parent_name"><?php _e('اسم الوالد/ة', 'sarah-loz'); ?> <span class="required">*</span></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="parent_name" id="parent_name" />
        </p>
    </div>
    
    <div id="child_fields" style="display:none;">
        <p class="form-row form-row-wide">
            <label for="child_name"><?php _e('اسم الطفل', 'sarah-loz'); ?> <span class="required">*</span></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="child_name" id="child_name" />
        </p>
        
        <p class="form-row form-row-wide">
            <label for="child_age"><?php _e('عمر الطفل', 'sarah-loz'); ?> <span class="required">*</span></label>
            <select name="child_age" id="child_age" class="woocommerce-Input woocommerce-Input--select input-select">
                <?php for ($i = 5; $i <= 15; $i++) : ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?> سنة</option>
                <?php endfor; ?>
            </select>
        </p>
        
        <p class="form-row form-row-wide">
            <label for="parent_id"><?php _e('معرف الوالد/ة', 'sarah-loz'); ?> <span class="required">*</span></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="parent_id" id="parent_id" placeholder="أدخل معرف الوالد/ة" />
            <span class="description"><?php _e('أدخل معرف الوالد/ة الذي تم إنشاؤه عند تسجيل حساب الوالد/ة', 'sarah-loz'); ?></span>
        </p>
    </div>
    
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#user_role').on('change', function() {
                if ($(this).val() === 'parent') {
                    $('#parent_fields').show();
                    $('#child_fields').hide();
                } else {
                    $('#parent_fields').hide();
                    $('#child_fields').show();
                }
            });
        });
    </script>
    <?php
}
add_action('woocommerce_register_form', 'sarah_loz_woocommerce_registration_form_fields', 10);

/**
 * Validate WooCommerce registration form fields
 */
function sarah_loz_woocommerce_registration_form_validate($errors, $username, $email) {
    if (isset($_POST['user_role']) && $_POST['user_role'] === 'parent') {
        if (empty($_POST['parent_name'])) {
            $errors->add('parent_name_error', __('يرجى إدخال اسم الوالد/ة', 'sarah-loz'));
        }
    } else if (isset($_POST['user_role']) && $_POST['user_role'] === 'child') {
        if (empty($_POST['child_name'])) {
            $errors->add('child_name_error', __('يرجى إدخال اسم الطفل', 'sarah-loz'));
        }
        
        if (empty($_POST['parent_id'])) {
            $errors->add('parent_id_error', __('يرجى إدخال معرف الوالد/ة', 'sarah-loz'));
        } else {
            // Verify parent ID exists
            $parent_id = sanitize_text_field($_POST['parent_id']);
            $parent = get_user_by('ID', $parent_id);
            
            if (!$parent || !in_array('parent', (array) $parent->roles)) {
                $errors->add('invalid_parent_id', __('معرف الوالد/ة غير صحيح', 'sarah-loz'));
            }
        }
    }
    
    return $errors;
}
add_filter('woocommerce_registration_errors', 'sarah_loz_woocommerce_registration_form_validate', 10, 3);

/**
 * Save custom registration fields
 */
function sarah_loz_woocommerce_save_registration_fields($customer_id) {
    if (isset($_POST['user_role'])) {
        $user_role = sanitize_text_field($_POST['user_role']);
        
        // Remove default role
        $user = new WP_User($customer_id);
        $user->remove_role('customer');
        
        // Add new role
        $user->add_role($user_role);
        
        // Save additional fields
        if ($user_role === 'parent') {
            if (isset($_POST['parent_name'])) {
                update_user_meta($customer_id, 'parent_name', sanitize_text_field($_POST['parent_name']));
            }
            
            // Generate and save parent ID for child accounts
            $parent_id = 'P' . str_pad($customer_id, 5, '0', STR_PAD_LEFT);
            update_user_meta($customer_id, 'parent_id_code', $parent_id);
            
        } else if ($user_role === 'child') {
            if (isset($_POST['child_name'])) {
                update_user_meta($customer_id, 'child_name', sanitize_text_field($_POST['child_name']));
            }
            
            if (isset($_POST['child_age'])) {
                update_user_meta($customer_id, 'child_age', intval($_POST['child_age']));
            }
            
            if (isset($_POST['parent_id'])) {
                $parent_id = intval($_POST['parent_id']);
                update_user_meta($customer_id, 'parent_id', $parent_id);
                
                // Add child to parent's children list
                $children = get_user_meta($parent_id, 'children', true);
                if (!is_array($children)) {
                    $children = array();
                }
                
                $children[] = $customer_id;
                update_user_meta($parent_id, 'children', $children);
            }
            
            // Initialize activity points and achievements
            update_user_meta($customer_id, 'activity_points', 0);
            update_user_meta($customer_id, 'achievement_points', 0);
            update_user_meta($customer_id, 'total_points', 0);
            update_user_meta($customer_id, 'login_streak', 0);
            update_user_meta($customer_id, 'child_activity_log', array());
            update_user_meta($customer_id, 'activity_timeline', array());
            update_user_meta($customer_id, 'user_achievements', array());
        }
    }
}
add_action('woocommerce_created_customer', 'sarah_loz_woocommerce_save_registration_fields');

/**
 * Display parent ID on account dashboard for parents
 */
function sarah_loz_woocommerce_account_dashboard_parent_id() {
    $current_user = wp_get_current_user();
    
    if (in_array('parent', (array) $current_user->roles)) {
        $parent_id_code = get_user_meta($current_user->ID, 'parent_id_code', true);
        
        if ($parent_id_code) {
            echo '<div class="woocommerce-message bg-primary/10 p-4 rounded-lg mb-6">';
            echo '<p class="font-bold mb-2">' . __('معرف الوالد/ة الخاص بك', 'sarah-loz') . '</p>';
            echo '<p>' . __('استخدم هذا المعرف عند إنشاء حساب لطفلك:', 'sarah-loz') . ' <strong>' . $parent_id_code . '</strong></p>';
            echo '</div>';
        }
    }
}
add_action('woocommerce_account_dashboard', 'sarah_loz_woocommerce_account_dashboard_parent_id', 10);

/**
 * Display children list on account dashboard for parents
 */
function sarah_loz_woocommerce_account_dashboard_children_list() {
    $current_user = wp_get_current_user();
    
    if (in_array('parent', (array) $current_user->roles)) {
        $children = get_user_meta($current_user->ID, 'children', true);
        
        if (is_array($children) && !empty($children)) {
            echo '<h2 class="text-xl font-bold mb-4">' . __('أطفالك', 'sarah-loz') . '</h2>';
            echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">';
            
            foreach ($children as $child_id) {
                $child = get_user_by('ID', $child_id);
                
                if ($child) {
                    $child_name = get_user_meta($child_id, 'child_name', true);
                    $child_age = get_user_meta($child_id, 'child_age', true);
                    $total_points = get_user_meta($child_id, 'total_points', true) ?: 0;
                    
                    echo '<div class="bg-white rounded-lg shadow-lg p-4">';
                    echo '<div class="flex justify-between items-center mb-2">';
                    echo '<div class="text-xl font-bold">' . esc_html($child_name) . '</div>';
                    echo '<div class="text-sm bg-primary/10 px-2 py-1 rounded">' . esc_html($child_age) . ' سنة</div>';
                    echo '</div>';
                    
                    echo '<div class="flex justify-between items-center mb-4">';
                    echo '<div class="text-sm text-gray-600">' . __('مجموع النقاط:', 'sarah-loz') . ' <strong>' . number_format($total_points) . '</strong></div>';
                    echo '</div>';
                    
                    echo '<div class="flex justify-between">';
                    echo '<a href="' . esc_url(wc_get_account_endpoint_url('child-dashboard') . '?child_id=' . $child_id) . '" class="text-primary hover:underline">' . __('عرض لوحة التحكم', 'sarah-loz') . '</a>';
                    echo '<a href="' . esc_url(wc_get_account_endpoint_url('child-reports') . '?child_id=' . $child_id) . '" class="text-primary hover:underline">' . __('عرض التقارير', 'sarah-loz') . '</a>';
                    echo '<a href="' . esc_url(wc_get_account_endpoint_url('child-settings') . '?child_id=' . $child_id) . '" class="text-primary hover:underline">' . __('الإعدادات', 'sarah-loz') . '</a>';
                    echo '</div>';
                    echo '</div>';
                }
            }
            
            echo '</div>';
        } else {
            echo '<div class="woocommerce-message bg-yellow-100 p-4 rounded-lg mb-6">';
            echo '<p>' . __('ليس لديك أي أطفال مسجلين بعد. استخدم معرف الوالد/ة الخاص بك لإنشاء حساب لطفلك.', 'sarah-loz') . '</p>';
            echo '</div>';
        }
    }
}
add_action('woocommerce_account_dashboard', 'sarah_loz_woocommerce_account_dashboard_children_list', 20);

/**
 * Force add child endpoints to WooCommerce account menu
 * This is a more direct approach to ensure the tabs appear
 */
function sarah_loz_force_account_endpoints() {
    // Only run on the account page
    if (!is_account_page()) {
        return;
    }
    
    // Get current user
    $current_user = wp_get_current_user();
    
    // Check if user is logged in
    if (!$current_user->exists()) {
        return;
    }
    
    // Add endpoints for parent
    add_rewrite_endpoint('child-dashboard', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('child-reports', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('child-settings', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('add-child', EP_ROOT | EP_PAGES);
    
    // Force flush rewrite rules if needed
    if (!get_option('sarah_loz_endpoints_forced', false)) {
        flush_rewrite_rules();
        update_option('sarah_loz_endpoints_forced', true);
    }
    
    // Override account menu items with a higher priority
    add_filter('woocommerce_account_menu_items', 'sarah_loz_force_account_menu_items', 999);
}
add_action('template_redirect', 'sarah_loz_force_account_endpoints');

/**
 * Force account menu items based on user role
 */
function sarah_loz_force_account_menu_items($items) {
    $current_user = wp_get_current_user();
    
    // For parent users
    if (in_array('parent', (array) $current_user->roles)) {
        // Create a new array with our custom order
        $new_items = array(
            'dashboard' => isset($items['dashboard']) ? $items['dashboard'] : __('Dashboard', 'woocommerce'),
            'child-dashboard' => 'لوحة تحكم الطفل',
            'add-child' => 'إضافة طفل جديد',
            'child-reports' => 'تقارير الطفل',
            'child-settings' => 'إعدادات الطفل'
        );
        
        // Add remaining default items
        if (isset($items['orders'])) $new_items['orders'] = $items['orders'];
        if (isset($items['downloads'])) $new_items['downloads'] = $items['downloads'];
        if (isset($items['edit-address'])) $new_items['edit-address'] = $items['edit-address'];
        if (isset($items['edit-account'])) $new_items['edit-account'] = $items['edit-account'];
        if (isset($items['customer-logout'])) $new_items['customer-logout'] = $items['customer-logout'];
        
        return $new_items;
    }
    
    // For child users
    if (in_array('child', (array) $current_user->roles)) {
        return array(
            'dashboard' => isset($items['dashboard']) ? $items['dashboard'] : __('Dashboard', 'woocommerce'),
            'child-dashboard' => 'لوحة التحكم الخاصة بي',
            'edit-account' => isset($items['edit-account']) ? $items['edit-account'] : __('Account details', 'woocommerce'),
            'customer-logout' => isset($items['customer-logout']) ? $items['customer-logout'] : __('Logout', 'woocommerce')
        );
    }
    
    // Return original items for other users
    return $items;
}

/**
 * Age Group Management System
 * Handles age group selection and content filtering
 */

/**
 * Start session for age group management
 */
function sarah_loz_start_session() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'sarah_loz_start_session');

/**
 * AJAX handler to set age group
 */
function sarah_loz_set_age_group() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'set_age_group_nonce')) {
        wp_die('Security check failed');
    }
    
    $age_group = sanitize_text_field($_POST['age_group']);
    
    // Validate age group
    $valid_age_groups = array('3-5', '6-7', '8-9');
    if (!in_array($age_group, $valid_age_groups)) {
        wp_send_json_error('Invalid age group');
        return;
    }
    
    // Set session
    $_SESSION['swl_selected_age_group'] = $age_group;
    
    // Set cookie for persistence (30 days)
    setcookie('swl_selected_age_group', $age_group, time() + (30 * 24 * 60 * 60), '/');
    
    wp_send_json_success(array(
        'age_group' => $age_group,
        'message' => 'Age group set successfully'
    ));
}
add_action('wp_ajax_set_age_group', 'sarah_loz_set_age_group');
add_action('wp_ajax_nopriv_set_age_group', 'sarah_loz_set_age_group');

/**
 * Get the currently selected age group
 */
function sarah_loz_get_selected_age_group() {
    // First check session
    if (isset($_SESSION['swl_selected_age_group'])) {
        return $_SESSION['swl_selected_age_group'];
    }
    
    // Then check cookie
    if (isset($_COOKIE['swl_selected_age_group'])) {
        $_SESSION['swl_selected_age_group'] = $_COOKIE['swl_selected_age_group'];
        return $_COOKIE['swl_selected_age_group'];
    }
    
    return null;
}

/**
 * Clear selected age group
 */
function sarah_loz_clear_age_group() {
    unset($_SESSION['swl_selected_age_group']);
    setcookie('swl_selected_age_group', '', time() - 3600, '/');
}

/**
 * AJAX handler to clear age group
 */
function sarah_loz_clear_age_group_ajax() {
    sarah_loz_clear_age_group();
    wp_send_json_success('Age group cleared');
}
add_action('wp_ajax_clear_age_group', 'sarah_loz_clear_age_group_ajax');
add_action('wp_ajax_nopriv_clear_age_group', 'sarah_loz_clear_age_group_ajax');

/**
 * Redirect to age selection page if no age group is selected
 */
function sarah_loz_check_age_group_redirect() {
    // Don't redirect in admin or during AJAX requests
    if (is_admin() || wp_doing_ajax()) {
        return;
    }
    
    // Don't redirect during REST API requests
    if (defined('REST_REQUEST') && REST_REQUEST) {
        return;
    }
    
    // Don't redirect for social media crawlers and bots (for sharing purposes)
    if (sarah_loz_is_bot_or_crawler()) {
        return;
    }
    
    // Don't redirect if already on age selection page
    global $post;
    if ($post && get_page_template_slug($post->ID) === 'page-age-selection.php') {
        return;
    }
    
    // Don't redirect for login/register pages
    if (is_page(array('login', 'register', 'my-account')) || (function_exists('is_account_page') && is_account_page())) {
        return;
    }
    
    // Don't redirect for WooCommerce pages that don't need age selection
    if (function_exists('is_woocommerce') && (is_shop() || is_product_category() || is_product_tag() || is_cart() || is_checkout())) {
        return;
    }
    
    // Don't redirect for specific pages that don't need age selection
    $excluded_pages = array('contact', 'about', 'privacy', 'terms', 'help', 'faq', 'sitemap');
    if (is_page($excluded_pages)) {
        return;
    }
    
    // Only redirect for main content pages
    if (!is_home() && !is_front_page() && !is_page() && !is_single() && !is_archive()) {
        return;
    }
    
    // Check if age group is selected
    $selected_age_group = sarah_loz_get_selected_age_group();
    
    if (!$selected_age_group) {
        // Try multiple methods to find age selection page
        $age_selection_page = null;
        
        // Method 1: Find by template
        $pages_with_template = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => 'page-age-selection.php',
            'number' => 1,
            'post_status' => 'publish'
        ));
        
        if (!empty($pages_with_template)) {
            $age_selection_page = $pages_with_template[0];
        } else {
            // Method 2: Find by slug
            $page_by_slug = get_page_by_path('age-selection');
            if ($page_by_slug && $page_by_slug->post_status === 'publish') {
                $age_selection_page = $page_by_slug;
            } else {
                // Method 3: Find by title
                $page_by_title = get_page_by_title('Age Selection');
                if ($page_by_title && $page_by_title->post_status === 'publish') {
                    $age_selection_page = $page_by_title;
                }
            }
        }
        
        if ($age_selection_page) {
            $age_selection_url = get_permalink($age_selection_page->ID);
            $current_url = home_url(add_query_arg(array(), $_SERVER['REQUEST_URI']));
            
            // Add redirect parameter to return to current page after selection
            $redirect_url = add_query_arg('redirect_to', urlencode($current_url), $age_selection_url);
            
            wp_redirect($redirect_url);
            exit;
        }
    }
}
add_action('template_redirect', 'sarah_loz_check_age_group_redirect');

/**
 * Detect if the current request is from a bot, crawler, or social media platform
 */
function sarah_loz_is_bot_or_crawler() {
    // Check if user agent exists
    if (!isset($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }
    
    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    
    // List of known bots, crawlers, and social media platforms
    $bot_patterns = array(
        // Social Media Crawlers
        'facebookexternalhit',
        'facebookcatalog',
        'twitterbot',
        'linkedinbot',
        'whatsapp',
        'telegrambot',
        'skypeuripreview',
        'viberbot',
        'discordbot',
        'slackbot',
        'snapchat',
        'pinterestbot',
        'redditbot',
        
        // Search Engine Crawlers
        'googlebot',
        'bingbot',
        'slurp', // Yahoo
        'duckduckbot',
        'baiduspider',
        'yandexbot',
        'sogou',
        
        // Other Common Crawlers
        'bot',
        'crawler',
        'spider',
        'scraper',
        'curl',
        'wget',
        'python-requests',
        'postman',
        'insomnia',
        'httpie',
        'ruby',
        'java',
        'go-http',
        'node-fetch',
        'axios',
        
        // SEO Tools
        'semrushbot',
        'ahrefsbot',
        'mj12bot',
        'dotbot',
        'blexbot',
        'screaming frog',
        
        // Preview/Unfurl Services
        'preview',
        'unfurl',
        'link',
        'embed',
        'thumbnail',
        'meta',
        'og:',
        'opengraph'
    );
    
    // Check if user agent matches any bot pattern
    foreach ($bot_patterns as $pattern) {
        if (strpos($user_agent, $pattern) !== false) {
            return true;
        }
    }
    
    // Check for headless browsers (often used by crawlers)
    if (strpos($user_agent, 'headless') !== false || 
        strpos($user_agent, 'phantom') !== false ||
        strpos($user_agent, 'selenium') !== false) {
        return true;
    }
    
    // Check for missing or suspicious user agents
    if (empty($user_agent) || strlen($user_agent) < 10) {
        return true;
    }
    
    // Additional check for requests without typical browser headers
    $typical_browser_headers = array('accept', 'accept-language', 'accept-encoding');
    $missing_headers = 0;
    
    foreach ($typical_browser_headers as $header) {
        if (!isset($_SERVER['HTTP_' . strtoupper(str_replace('-', '_', $header))])) {
            $missing_headers++;
        }
    }
    
    // If missing too many typical browser headers, likely a bot
    if ($missing_headers >= 2) {
        return true;
    }
    
    return false;
}

/**
 * Filter content by age group - Enhanced version is implemented at the end of this file
 * The old function has been replaced with the enhanced version below
 */
/*
// OLD FUNCTION REMOVED - Enhanced version is at the end of this file
// function sarah_loz_filter_content_by_age($query) { ... }
*/

// The add_action for the enhanced function is at the end of the file

/**
 * Get age group data - MOVED TO END OF FILE (Enhanced Version)
 * The enhanced version with custom image support is at the end of this file
 */

/**
 * Get current age group data
 */
function sarah_loz_get_current_age_group_data() {
    $selected_age_group = sarah_loz_get_selected_age_group();
    $age_groups = sarah_loz_get_age_groups();
    
    return isset($age_groups[$selected_age_group]) ? $age_groups[$selected_age_group] : null;
}

/**
 * Add age group indicator to header
 */
function sarah_loz_add_age_group_indicator() {
    $selected_age_group = sarah_loz_get_selected_age_group();
    $age_groups = sarah_loz_get_age_groups();
    
    if ($selected_age_group && isset($age_groups[$selected_age_group])) {
        $group_data = $age_groups[$selected_age_group];
        ?>
        <div id="age-group-indicator" class="fixed top-4 left-4 z-50 bg-<?php echo $group_data['color']; ?> text-white px-4 py-2 rounded-full shadow-lg flex items-center gap-2 text-sm font-bold">
            <span class="text-lg"><?php echo $group_data['icon']; ?></span>
            <span><?php echo $selected_age_group; ?> سنوات</span>
            <button onclick="changeAgeGroup()" class="ml-2 hover:text-gray-200" title="تغيير الفئة العمرية">
                <i class="fas fa-cog"></i>
            </button>
        </div>
        
        <script>
        function changeAgeGroup() {
            if (confirm('هل تريد تغيير الفئة العمرية؟')) {
                // Clear age group and redirect to selection page
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'clear_age_group'
                    })
                })
                .then(() => {
                    window.location.reload();
                });
            }
        }
        </script>
        <?php
    }
}
add_action('wp_footer', 'sarah_loz_add_age_group_indicator');

/**
 * Shortcode to display age-appropriate content
 */
function sarah_loz_age_content_shortcode($atts, $content = '') {
    $atts = shortcode_atts(array(
        'ages' => '', // Comma-separated list of age groups
        'exclude' => '' // Comma-separated list of age groups to exclude
    ), $atts);
    
    $selected_age_group = sarah_loz_get_selected_age_group();
    
    if (!$selected_age_group) {
        return ''; // Don't show content if no age group selected
    }
    
    $allowed_ages = array_map('trim', explode(',', $atts['ages']));
    $excluded_ages = array_map('trim', explode(',', $atts['exclude']));
    
    // Check if current age group is allowed
    $show_content = false;
    
    if (!empty($atts['ages'])) {
        $show_content = in_array($selected_age_group, $allowed_ages);
    } else {
        $show_content = true; // Show by default if no specific ages set
    }
    
    // Check if current age group is excluded
    if (!empty($atts['exclude']) && in_array($selected_age_group, $excluded_ages)) {
        $show_content = false;
    }
    
    return $show_content ? do_shortcode($content) : '';
}
add_shortcode('age_content', 'sarah_loz_age_content_shortcode');

/**
 * Helper function to check if current page should show age-restricted content
 */
function sarah_loz_should_show_age_content($required_ages = array()) {
    $selected_age_group = sarah_loz_get_selected_age_group();
    
    if (!$selected_age_group) {
        return false;
    }
    
    if (empty($required_ages)) {
        return true; // Show all content if no specific ages required
    }
    
    return in_array($selected_age_group, $required_ages);
}

/**
 * Debug function to test age selection setup
 * Add ?debug_age_selection=1 to any URL to test
 */
function sarah_loz_debug_age_selection() {
    if (!isset($_GET['debug_age_selection']) || !current_user_can('manage_options')) {
        return;
    }
    
    echo '<div style="background: #fff; border: 2px solid #0073aa; padding: 20px; margin: 20px; position: fixed; top: 0; right: 0; z-index: 9999; width: 400px;">';
    echo '<h3>Age Selection Debug Info</h3>';
    
    // Check current age group
    $selected_age_group = sarah_loz_get_selected_age_group();
    echo '<p><strong>Current Age Group:</strong> ' . ($selected_age_group ?: 'None selected') . '</p>';
    
    // Check session
    echo '<p><strong>Session:</strong> ' . (isset($_SESSION['swl_selected_age_group']) ? $_SESSION['swl_selected_age_group'] : 'Not set') . '</p>';
    
    // Check cookie
    echo '<p><strong>Cookie:</strong> ' . (isset($_COOKIE['swl_selected_age_group']) ? $_COOKIE['swl_selected_age_group'] : 'Not set') . '</p>';
    
    // Find age selection page
    $pages_with_template = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => 'page-age-selection.php',
        'number' => 1,
        'post_status' => 'publish'
    ));
    
    echo '<p><strong>Age Selection Page (by template):</strong> ';
    if (!empty($pages_with_template)) {
        echo 'Found: ' . get_permalink($pages_with_template[0]->ID);
    } else {
        echo 'Not found';
    }
    echo '</p>';
    
    $page_by_slug = get_page_by_path('age-selection');
    echo '<p><strong>Age Selection Page (by slug):</strong> ';
    if ($page_by_slug && $page_by_slug->post_status === 'publish') {
        echo 'Found: ' . get_permalink($page_by_slug->ID);
    } else {
        echo 'Not found';
    }
    echo '</p>';
    
    // Clear age group buttons
    echo '<p>';
    echo '<a href="' . add_query_arg('clear_age_debug', '1') . '" style="background: #dc3545; color: white; padding: 5px 10px; text-decoration: none; margin-right: 10px;">Clear Age Group</a>';
    echo '<a href="' . remove_query_arg('debug_age_selection') . '" style="background: #6c757d; color: white; padding: 5px 10px; text-decoration: none;">Close Debug</a>';
    echo '</p>';
    
    echo '</div>';
    
    // Handle clear age group
    if (isset($_GET['clear_age_debug'])) {
        sarah_loz_clear_age_group();
        wp_redirect(remove_query_arg(array('clear_age_debug', 'debug_age_selection')));
        exit;
    }
}
add_action('wp_footer', 'sarah_loz_debug_age_selection');

/**
 * Taxonomy Image Management System
 * Allows admins to upload custom images for taxonomies and age groups
 */

/**
 * Add image fields to taxonomy forms
 */
function sarah_loz_add_taxonomy_image_fields($taxonomy) {
    ?>
    <div class="form-field term-image-wrap">
        <label for="taxonomy-image"><?php _e('صورة التصنيف', 'sarah-loz'); ?></label>
        <input type="hidden" id="taxonomy-image" name="taxonomy_image" value="">
        <div id="taxonomy-image-container" style="margin: 10px 0;">
            <img id="taxonomy-image-preview" src="" style="max-width: 150px; height: auto; display: none; border: 1px solid #ddd; padding: 5px;">
        </div>
        <button type="button" class="button" id="upload-taxonomy-image"><?php _e('اختيار صورة', 'sarah-loz'); ?></button>
        <button type="button" class="button" id="remove-taxonomy-image" style="display: none; margin-right: 10px;"><?php _e('إزالة الصورة', 'sarah-loz'); ?></button>
        <p class="description"><?php _e('اختر صورة لتمثيل هذا التصنيف (مقاس مُفضّل: 150x150 بكسل)', 'sarah-loz'); ?></p>
    </div>
    <?php
}

/**
 * Add image fields to taxonomy edit forms
 */
function sarah_loz_edit_taxonomy_image_fields($term, $taxonomy) {
    $image_id = get_term_meta($term->term_id, 'taxonomy_image', true);
    $image_url = '';
    if ($image_id) {
        $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
    }
    ?>
    <tr class="form-field term-image-wrap">
        <th scope="row"><label for="taxonomy-image"><?php _e('صورة التصنيف', 'sarah-loz'); ?></label></th>
        <td>
            <input type="hidden" id="taxonomy-image" name="taxonomy_image" value="<?php echo esc_attr($image_id); ?>">
            <div id="taxonomy-image-container" style="margin: 10px 0;">
                <img id="taxonomy-image-preview" src="<?php echo esc_url($image_url); ?>" style="max-width: 150px; height: auto; <?php echo $image_url ? '' : 'display: none;'; ?> border: 1px solid #ddd; padding: 5px;">
            </div>
            <button type="button" class="button" id="upload-taxonomy-image"><?php _e('اختيار صورة', 'sarah-loz'); ?></button>
            <button type="button" class="button" id="remove-taxonomy-image" style="<?php echo $image_url ? '' : 'display: none;'; ?> margin-right: 10px;"><?php _e('إزالة الصورة', 'sarah-loz'); ?></button>
            <p class="description"><?php _e('اختر صورة لتمثيل هذا التصنيف (مقاس مُفضّل: 150x150 بكسل)', 'sarah-loz'); ?></p>
        </td>
    </tr>
    <?php
}

/**
 * Save taxonomy image
 */
function sarah_loz_save_taxonomy_image($term_id, $tt_id, $taxonomy) {
    if (isset($_POST['taxonomy_image'])) {
        $image_id = intval($_POST['taxonomy_image']);
        if ($image_id > 0) {
            update_term_meta($term_id, 'taxonomy_image', $image_id);
        } else {
            delete_term_meta($term_id, 'taxonomy_image');
        }
    }
}

/**
 * Add image fields to all relevant taxonomies
 */
function sarah_loz_add_taxonomy_image_support() {
    $taxonomies = array('game_category', 'activity_category', 'video_category', 'product_cat');
    
    foreach ($taxonomies as $taxonomy) {
        // Add fields to create form
        add_action($taxonomy . '_add_form_fields', 'sarah_loz_add_taxonomy_image_fields');
        
        // Add fields to edit form
        add_action($taxonomy . '_edit_form_fields', 'sarah_loz_edit_taxonomy_image_fields', 10, 2);
        
        // Save fields
        add_action('created_' . $taxonomy, 'sarah_loz_save_taxonomy_image', 10, 3);
        add_action('edited_' . $taxonomy, 'sarah_loz_save_taxonomy_image', 10, 3);
    }
}
add_action('admin_init', 'sarah_loz_add_taxonomy_image_support');

/**
 * Enqueue media scripts for taxonomy image upload
 */
function sarah_loz_taxonomy_image_scripts($hook) {
    if ($hook === 'edit-tags.php' || $hook === 'term.php') {
        wp_enqueue_media();
        wp_enqueue_script('sarah-loz-taxonomy-image', get_template_directory_uri() . '/assets/js/taxonomy-image.js', array('jquery'), '1.0.0', true);
        wp_localize_script('sarah-loz-taxonomy-image', 'taxonomyImageAjax', array(
            'upload_title' => __('اختيار صورة التصنيف', 'sarah-loz'),
            'upload_button' => __('استخدام هذه الصورة', 'sarah-loz'),
        ));
    }
}
add_action('admin_enqueue_scripts', 'sarah_loz_taxonomy_image_scripts');

/**
 * Age Group Image Management
 */

/**
 * Get age group image URL
 */
function sarah_loz_get_age_group_image($age_group, $size = 'thumbnail') {
    // Check if custom image is set for this age group
    $image_id = get_option('sarah_loz_age_group_image_' . str_replace('-', '_', $age_group));
    
    if ($image_id) {
        $image_url = wp_get_attachment_image_url($image_id, $size);
        if ($image_url) {
            return $image_url;
        }
    }
    
    // Fallback to default icons
    $default_images = array(
        '3-5' => get_template_directory_uri() . '/assets/images/age-3-5.png',
        '6-7' => get_template_directory_uri() . '/assets/images/age-6-7.png',
        '8-9' => get_template_directory_uri() . '/assets/images/age-8-9.png'
    );
    
    return isset($default_images[$age_group]) ? $default_images[$age_group] : '';
}

/**
 * Get age group name (label)
 */
function sarah_loz_get_age_group_name($age_group) {
    $safe_name = str_replace('-', '_', $age_group);
    $custom_name = get_option('sarah_loz_age_group_name_' . $safe_name);
    
    if (!empty($custom_name)) {
        return $custom_name;
    }
    
    // Fallback to default names
    $default_names = array(
        '3-5' => 'الأصدقاء الصغار',
        '6-7' => 'المستكشفون',
        '8-9' => 'الأبطال المتقدمون'
    );
    
    return isset($default_names[$age_group]) ? $default_names[$age_group] : $age_group . ' سنوات';
}

/**
 * Get age group description
 */
function sarah_loz_get_age_group_description($age_group) {
    $safe_name = str_replace('-', '_', $age_group);
    $custom_description = get_option('sarah_loz_age_group_description_' . $safe_name);
    
    if (!empty($custom_description)) {
        return $custom_description;
    }
    
    // Fallback to default descriptions
    $default_descriptions = array(
        '3-5' => 'مغامرات بسيطة وممتعة للمبتدئين الصغار!',
        '6-7' => 'تحديات جديدة ومغامرات أكبر للاستكشاف!',
        '8-9' => 'تحديات مثيرة ومهارات متقدمة للأبطال!'
    );
    
    return isset($default_descriptions[$age_group]) ? $default_descriptions[$age_group] : 'محتوى مناسب للأطفال في هذا العمر';
}

/**
 * Get taxonomy image URL
 */
function sarah_loz_get_taxonomy_image($term_id, $size = 'thumbnail') {
    $image_id = get_term_meta($term_id, 'taxonomy_image', true);
    
    if ($image_id) {
        return wp_get_attachment_image_url($image_id, $size);
    }
    
    return false;
}

/**
 * Age Group Settings Page
 */
function sarah_loz_age_group_settings_page() {
    add_submenu_page(
        'options-general.php',
        'إعدادات الفئات العمرية',
        'الفئات العمرية',
        'manage_options',
        'age-group-settings',
        'sarah_loz_age_group_settings_page_content'
    );
}
add_action('admin_menu', 'sarah_loz_age_group_settings_page');

/**
 * Age Group Settings Page Content
 */
function sarah_loz_age_group_settings_page_content() {
    if (isset($_POST['submit'])) {
        // Save age group images, names, and descriptions
        $age_groups = array('3-5', '6-7', '8-9');
        foreach ($age_groups as $age_group) {
            $safe_name = str_replace('-', '_', $age_group);
            
            // Save image
            $image_field = 'age_group_image_' . $safe_name;
            if (isset($_POST[$image_field])) {
                update_option('sarah_loz_age_group_image_' . $safe_name, intval($_POST[$image_field]));
            }
            
            // Save custom name
            $name_field = 'age_group_name_' . $safe_name;
            if (isset($_POST[$name_field])) {
                update_option('sarah_loz_age_group_name_' . $safe_name, sanitize_text_field($_POST[$name_field]));
            }
            
            // Save custom description
            $desc_field = 'age_group_description_' . $safe_name;
            if (isset($_POST[$desc_field])) {
                update_option('sarah_loz_age_group_description_' . $safe_name, sanitize_textarea_field($_POST[$desc_field]));
            }
        }
        echo '<div class="notice notice-success"><p>تم حفظ الإعدادات بنجاح!</p></div>';
    }
    
    wp_enqueue_media();
    ?>
    <div class="wrap">
        <h1>إعدادات الفئات العمرية</h1>
        <p>يمكنك تخصيص أسماء وأوصاف وصور كل فئة عمرية لتظهر في صفحة اختيار العمر وفي المحتوى.</p>
        
        <form method="post">
            <table class="form-table">
                <?php
                $age_groups = sarah_loz_get_age_groups();
                foreach ($age_groups as $age_range => $group_data) :
                    $safe_name = str_replace('-', '_', $age_range);
                    
                    // Get current values
                    $image_id = get_option('sarah_loz_age_group_image_' . $safe_name);
                    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
                    $custom_name = get_option('sarah_loz_age_group_name_' . $safe_name, $group_data['label']);
                    $custom_description = get_option('sarah_loz_age_group_description_' . $safe_name, $group_data['description']);
                ?>
                <tr>
                    <th scope="row" colspan="2">
                        <h2 style="margin: 20px 0 10px 0; color: #0073aa;"><?php echo esc_html($age_range); ?> سنوات</h2>
                    </th>
                </tr>
                
                <!-- Name Field -->
                <tr>
                    <th scope="row">
                        <label for="age_group_name_<?php echo esc_attr($safe_name); ?>">اسم الفئة العمرية</label>
                    </th>
                    <td>
                        <input type="text" 
                               id="age_group_name_<?php echo esc_attr($safe_name); ?>"
                               name="age_group_name_<?php echo esc_attr($safe_name); ?>" 
                               value="<?php echo esc_attr($custom_name); ?>" 
                               class="regular-text" 
                               placeholder="<?php echo esc_attr($group_data['label']); ?>">
                        <p class="description">الاسم الذي سيظهر للأطفال (مثل: الأصدقاء الصغار، المستكشفون، إلخ)</p>
                    </td>
                </tr>
                
                <!-- Description Field -->
                <tr>
                    <th scope="row">
                        <label for="age_group_description_<?php echo esc_attr($safe_name); ?>">وصف الفئة العمرية</label>
                    </th>
                    <td>
                        <textarea id="age_group_description_<?php echo esc_attr($safe_name); ?>"
                                  name="age_group_description_<?php echo esc_attr($safe_name); ?>" 
                                  rows="3" 
                                  cols="50" 
                                  class="large-text"
                                  placeholder="<?php echo esc_attr($group_data['description']); ?>"><?php echo esc_textarea($custom_description); ?></textarea>
                        <p class="description">وصف تشجيعي للأطفال في هذه الفئة العمرية</p>
                    </td>
                </tr>
                
                <!-- Image Field -->
                <tr>
                    <th scope="row">
                        <label>صورة الفئة العمرية</label>
                    </th>
                    <td>
                        <div class="age-group-image-field">
                            <input type="hidden" name="age_group_image_<?php echo esc_attr($safe_name); ?>" value="<?php echo esc_attr($image_id); ?>" class="age-group-image-id">
                            <div class="age-group-image-preview" style="margin: 10px 0;">
                                <img src="<?php echo esc_url($image_url); ?>" style="max-width: 150px; height: auto; <?php echo $image_url ? '' : 'display: none;'; ?> border: 1px solid #ddd; padding: 5px; border-radius: 8px;">
                            </div>
                            <button type="button" class="button upload-age-group-image">اختيار صورة</button>
                            <button type="button" class="button remove-age-group-image" style="<?php echo $image_url ? '' : 'display: none;'; ?> margin-right: 10px;">إزالة الصورة</button>
                            <p class="description">مقاس مُفضّل: 200x200 بكسل - ستظهر مع الرمز الافتراضي (<?php echo esc_html($group_data['icon']); ?>) إذا لم يتم اختيار صورة</p>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2"><hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;"></td>
                </tr>
                
                <?php endforeach; ?>
            </table>
            
            <div style="background: #f1f1f1; padding: 15px; border-left: 4px solid #0073aa; margin: 20px 0;">
                <h3 style="margin-top: 0;">معاينة الألوان والرموز الافتراضية:</h3>
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <?php foreach ($age_groups as $age_range => $group_data) : ?>
                        <div style="text-align: center; padding: 10px;">
                            <div style="font-size: 40px; margin-bottom: 8px;"><?php echo esc_html($group_data['icon']); ?></div>
                            <div style="font-weight: bold; color: #0073aa;"><?php echo esc_html($age_range); ?> سنوات</div>
                            <div style="font-size: 12px; color: #666;">لون: <?php echo esc_html($group_data['color']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p style="margin-bottom: 0; font-style: italic; color: #666;">
                    <strong>ملاحظة:</strong> الألوان والرموز الافتراضية لا يمكن تغييرها من هذه الصفحة، لكن يمكنك تخصيص الأسماء والأوصاف والصور.
                </p>
            </div>
            
            <?php submit_button('حفظ الإعدادات'); ?>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Handle age group image upload
        $(document).on('click', '.upload-age-group-image', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var field = button.siblings('.age-group-image-id');
            var preview = button.siblings('.age-group-image-preview').find('img');
            var removeBtn = button.siblings('.remove-age-group-image');
            
            var mediaUploader = wp.media({
                title: 'اختيار صورة الفئة العمرية',
                button: {
                    text: 'استخدام هذه الصورة'
                },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                field.val(attachment.id);
                preview.attr('src', attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url).show();
                removeBtn.show();
            });
            
            mediaUploader.open();
        });
        
        // Handle age group image removal
        $(document).on('click', '.remove-age-group-image', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var field = button.siblings('.age-group-image-id');
            var preview = button.siblings('.age-group-image-preview').find('img');
            
            field.val('');
            preview.hide();
            button.hide();
        });
    });
    </script>
    <?php
}

/**
 * Update age group data to use custom images, names, and descriptions
 */
function sarah_loz_get_age_groups() {
    $base_groups = array(
        '3-5' => array(
            'label' => 'الأصدقاء الصغار',
            'icon' => '🦁',
            'color' => 'primary',
            'description' => 'مغامرات بسيطة وممتعة للمبتدئين الصغار!'
        ),
        '6-7' => array(
            'label' => 'المستكشفون',
            'icon' => '🦊',
            'color' => 'secondary',
            'description' => 'تحديات جديدة ومغامرات أكبر للاستكشاف!'
        ),
        '8-9' => array(
            'label' => 'الأبطال المتقدمون',
            'icon' => '🦉',
            'color' => 'accent',
            'description' => 'تحديات مثيرة ومهارات متقدمة للأبطال!'
        )
    );
    
    // Add custom images, names, and descriptions to each age group
    foreach ($base_groups as $age_range => &$group_data) {
        $safe_name = str_replace('-', '_', $age_range);
        
        // Add custom image if set
        $custom_image = sarah_loz_get_age_group_image($age_range, 'medium');
        if ($custom_image) {
            $group_data['custom_image'] = $custom_image;
        }
        
        // Add custom name if set
        $custom_name = get_option('sarah_loz_age_group_name_' . $safe_name);
        if (!empty($custom_name)) {
            $group_data['label'] = $custom_name;
        }
        
        // Add custom description if set
        $custom_description = get_option('sarah_loz_age_group_description_' . $safe_name);
        if (!empty($custom_description)) {
            $group_data['description'] = $custom_description;
        }
    }
    
    return $base_groups;
}

/**
 * Helper function to check if a post should be shown for the selected age group
 */
function sarah_loz_should_show_post_for_age_group($post_id, $selected_age_group) {
    if (!$selected_age_group) {
        return true; // Show all content if no age group selected
    }
    
    $age_range = get_field('age_range', $post_id);
    
    if (!$age_range) {
        return true; // Show content without age restrictions
    }
    
    // If the content is marked for "all" ages, show it for any age group
    if ($age_range === 'all') {
        return true;
    }
    
    // Check if the selected age group is included in the age range
    // This handles both exact matches and ranges like "3-9" that include multiple age groups
    if (strpos($age_range, $selected_age_group) !== false) {
        return true;
    }
    
    // Handle ranges that span multiple age groups (e.g., "3-9" should include "3-5", "6-7", "8-9")
    if (strpos($age_range, '-') !== false) {
        // Extract the start and end ages from both the range and selected group
        $range_parts = explode('-', $age_range);
        $selected_parts = explode('-', $selected_age_group);
        
        if (count($range_parts) === 2 && count($selected_parts) === 2) {
            $range_start = (int) $range_parts[0];
            $range_end = (int) $range_parts[1];
            $selected_start = (int) $selected_parts[0];
            $selected_end = (int) $selected_parts[1];
            
            // Check if there's any overlap between the ranges
            if ($selected_start <= $range_end && $selected_end >= $range_start) {
                return true;
            }
        }
    }
    
    return false;
}

/**
 * Enhanced meta query for age filtering
 */
function sarah_loz_get_age_filter_meta_query($selected_age_group) {
    if (!$selected_age_group) {
        return array();
    }
    
    return array(
        'relation' => 'OR',
        array(
            'key' => 'age_range',
            'value' => 'all',
            'compare' => '='
        ),
        array(
            'key' => 'age_range',
            'value' => $selected_age_group,
            'compare' => 'LIKE'
        ),
        array(
            'key' => 'age_range',
            'compare' => 'NOT EXISTS'
        ),
        // Handle broader age ranges like "3-9" that should include specific groups
        array(
            'key' => 'age_range',
            'value' => '3-9',
            'compare' => '='
        ),
        array(
            'key' => 'age_range',
            'value' => '2-9',
            'compare' => '='
        ),
        array(
            'key' => 'age_range',
            'value' => '3-10',
            'compare' => '='
        )
    );
}

/**
 * Filter content by age group - Enhanced version
 */
function sarah_loz_filter_content_by_age($query) {
    // Only modify main queries on front-end for games, activities, and videos
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    
    // Don't filter content for bots/crawlers (for sharing purposes)
    if (sarah_loz_is_bot_or_crawler()) {
        return;
    }
    
    // Get selected age group
    $selected_age_group = sarah_loz_get_selected_age_group();
    
    if (!$selected_age_group) {
        return;
    }
    
    // Apply filter to specific post types
    $filtered_post_types = array('game', 'activity', 'video', 'theater');
    
    if (is_home() || is_front_page() || 
        (isset($query->query_vars['post_type']) && in_array($query->query_vars['post_type'], $filtered_post_types))) {
        
        // Get existing meta query
        $meta_query = $query->get('meta_query') ?: array();
        
        // Add our enhanced age filtering
        $age_meta_query = sarah_loz_get_age_filter_meta_query($selected_age_group);
        
        if (!empty($age_meta_query)) {
            $meta_query[] = $age_meta_query;
            $query->set('meta_query', $meta_query);
        }
    }
}
add_action('pre_get_posts', 'sarah_loz_filter_content_by_age');

/**
 * Create sample theater data for testing
 */
function sarah_loz_create_sample_theater_data() {
    // Only run once
    if (get_option('sarah_loz_sample_theater_created')) {
        return;
    }
    
    // Create sample theater categories
    $categories = array(
        'مسرحيات عرائس' => 'puppet',
        'مسرحيات موسيقية' => 'musical',
        'مسرحيات تعليمية' => 'educational',
        'مسرحيات قصصية' => 'story'
    );
    
    foreach ($categories as $name => $slug) {
        if (!term_exists($name, 'theater_category')) {
            wp_insert_term($name, 'theater_category', array('slug' => $slug));
        }
    }
    
    // Create sample theaters
    $sample_theaters = array(
        array(
            'title' => 'مغامرة الأرنب الصغير',
            'content' => 'مسرحية تفاعلية عن أرنب صغير يكتشف العالم من حوله. تتضمن الأغاني والرقصات المناسبة للأطفال الصغار.',
            'excerpt' => 'مغامرة ممتعة مع أرنب صغير يكتشف العالم من حوله',
            'theater_type' => 'puppet',
            'theater_duration' => '15:30',
            'director' => 'أحمد محمد',
            'cast_info' => 'أرنب صغير، ثعلب ذكي، دب لطيف',
            'age_range' => '3-5',
            'is_featured' => 1
        ),
        array(
            'title' => 'رحلة إلى الفضاء',
            'content' => 'مسرحية موسيقية عن رحلة فضائية ممتعة. يتعلم الأطفال عن الكواكب والنجوم من خلال الأغاني والحركة.',
            'excerpt' => 'رحلة فضائية ممتعة مع الأغاني والحركة',
            'theater_type' => 'musical',
            'theater_duration' => '20:00',
            'director' => 'فاطمة علي',
            'cast_info' => 'رائد فضاء، نجوم، كواكب',
            'age_range' => '6-7',
            'is_featured' => 1
        ),
        array(
            'title' => 'حكاية الألوان',
            'content' => 'مسرحية تعليمية عن الألوان وكيفية مزجها. يتعلم الأطفال الألوان الأساسية والثانوية بطريقة ممتعة.',
            'excerpt' => 'تعلم الألوان من خلال مسرحية تفاعلية',
            'theater_type' => 'educational',
            'theater_duration' => '12:45',
            'director' => 'سارة أحمد',
            'cast_info' => 'ألوان متحركة، فرشاة سحرية',
            'age_range' => '3-5',
            'is_featured' => 0
        )
    );
    
    foreach ($sample_theaters as $theater) {
        $post_data = array(
            'post_title' => $theater['title'],
            'post_content' => $theater['content'],
            'post_excerpt' => $theater['excerpt'],
            'post_status' => 'publish',
            'post_type' => 'theater',
            'post_author' => 1
        );
        
        $post_id = wp_insert_post($post_data);
        
        if ($post_id) {
            // Set ACF fields
            update_field('theater_type', $theater['theater_type'], $post_id);
            update_field('theater_duration', $theater['theater_duration'], $post_id);
            update_field('director', $theater['director'], $post_id);
            update_field('cast_info', $theater['cast_info'], $post_id);
            update_field('age_range', $theater['age_range'], $post_id);
            update_field('is_featured', $theater['is_featured'], $post_id);
            update_field('view_count', 0, $post_id);
            update_field('rating', 0, $post_id);
            update_field('rating_count', 0, $post_id);
            
            // Set theater category
            $category_name = '';
            switch ($theater['theater_type']) {
                case 'puppet':
                    $category_name = 'مسرحيات عرائس';
                    break;
                case 'musical':
                    $category_name = 'مسرحيات موسيقية';
                    break;
                case 'educational':
                    $category_name = 'مسرحيات تعليمية';
                    break;
                case 'story':
                    $category_name = 'مسرحيات قصصية';
                    break;
            }
            
            if ($category_name) {
                $term = get_term_by('name', $category_name, 'theater_category');
                if ($term) {
                    wp_set_object_terms($post_id, $term->term_id, 'theater_category');
                }
            }
            
            // Set age group
            $age_term = get_term_by('name', $theater['age_range'] . ' سنوات', 'age_group');
            if ($age_term) {
                wp_set_object_terms($post_id, $age_term->term_id, 'age_group');
            }
        }
    }
    
    update_option('sarah_loz_sample_theater_created', true);
}
add_action('init', 'sarah_loz_create_sample_theater_data');

/**
 * Ensure proper meta tags for social sharing when accessed by bots
 */
function sarah_loz_add_sharing_meta_tags() {
    // Only add meta tags on front page and for bots
    if (!is_front_page() && !is_home()) {
        return;
    }
    
    // Get site information
    $site_name = get_bloginfo('name');
    $site_description = get_bloginfo('description');
    $site_url = home_url();
    
    // Try to get a featured image or default image
    $image_url = '';
    if (has_post_thumbnail()) {
        $image_url = get_the_post_thumbnail_url(null, 'large');
    } else {
        // Use a default image from theme
        $default_image = get_template_directory_uri() . '/assets/images/SWL.png';
        if (file_exists(get_template_directory() . '/assets/images/SWL.png')) {
            $image_url = $default_image;
        }
    }
    
    // Custom meta description for sharing
    $meta_description = 'موقع ترفيهي وتعليمي للأطفال من عمر ٣ إلى ٩ سنوات. ألعاب تفاعلية، أنشطة تعليمية، وفيديوهات ممتعة مع سارة ولوز!';
    
    ?>
    <!-- Enhanced Meta Tags for Social Sharing -->
    <meta name="description" content="<?php echo esc_attr($meta_description); ?>">
    <meta name="keywords" content="أطفال, ألعاب تعليمية, أنشطة للأطفال, سارة ولوز, تعليم الأطفال, ترفيه الأطفال">
    <meta name="author" content="<?php echo esc_attr($site_name); ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo esc_attr($site_name); ?> - عالم المرح والتعلم للأطفال">
    <meta property="og:description" content="<?php echo esc_attr($meta_description); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo esc_url($site_url); ?>">
    <meta property="og:site_name" content="<?php echo esc_attr($site_name); ?>">
    <meta property="og:locale" content="ar_AR">
    <?php if ($image_url) : ?>
    <meta property="og:image" content="<?php echo esc_url($image_url); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="<?php echo esc_attr($site_name); ?> - عالم المرح والتعلم للأطفال">
    <?php endif; ?>
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr($site_name); ?> - عالم المرح والتعلم للأطفال">
    <meta name="twitter:description" content="<?php echo esc_attr($meta_description); ?>">
    <?php if ($image_url) : ?>
    <meta name="twitter:image" content="<?php echo esc_url($image_url); ?>">
    <?php endif; ?>
    
    <!-- WhatsApp Meta Tags -->
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:secure_url" content="<?php echo esc_url($image_url); ?>">
    
    <!-- Additional Meta Tags for Better Sharing -->
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#ff6b9d">
    <link rel="canonical" href="<?php echo esc_url($site_url); ?>">
    
    <!-- Arabic Language Support -->
    <meta property="og:locale:alternate" content="en_US">
    <meta name="language" content="Arabic">
    <meta name="content-language" content="ar">
    <?php
}
add_action('wp_head', 'sarah_loz_add_sharing_meta_tags', 1);

/**
 * Debug function to test bot detection
 * Add ?debug_bot_detection=1 to any URL to test
 */
function sarah_loz_debug_bot_detection() {
    if (!isset($_GET['debug_bot_detection']) || !current_user_can('manage_options')) {
        return;
    }
    
    echo '<div style="background: #fff; border: 2px solid #0073aa; padding: 20px; margin: 20px; position: fixed; top: 0; left: 0; z-index: 9999; width: 400px; max-height: 80vh; overflow-y: auto;">';
    echo '<h3>Bot Detection Debug Info</h3>';
    
    // Check if detected as bot
    $is_bot = sarah_loz_is_bot_or_crawler();
    echo '<p><strong>Detected as Bot:</strong> ' . ($is_bot ? 'YES' : 'NO') . '</p>';
    
    // Show user agent
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Not set';
    echo '<p><strong>User Agent:</strong> ' . esc_html($user_agent) . '</p>';
    
    // Show headers
    echo '<p><strong>Headers:</strong></p>';
    echo '<ul style="font-size: 12px; max-height: 200px; overflow-y: auto;">';
    foreach ($_SERVER as $key => $value) {
        if (strpos($key, 'HTTP_') === 0) {
            echo '<li>' . esc_html($key) . ': ' . esc_html($value) . '</li>';
        }
    }
    echo '</ul>';
    
    // Show current page info
    echo '<p><strong>Current Page:</strong> ';
    if (is_front_page()) echo 'Front Page';
    elseif (is_home()) echo 'Home Page';
    elseif (is_page()) echo 'Page: ' . get_the_title();
    else echo 'Other';
    echo '</p>';
    
    // Test sharing meta
    echo '<p><strong>Meta Tags Active:</strong> ' . (is_front_page() || is_home() ? 'YES' : 'NO') . '</p>';
    
    echo '<p><a href="' . remove_query_arg('debug_bot_detection') . '" style="background: #6c757d; color: white; padding: 5px 10px; text-decoration: none;">Close Debug</a></p>';
    
    echo '</div>';
}
add_action('wp_footer', 'sarah_loz_debug_bot_detection');

/**
 * Get the appropriate redirect URL for the logo click
 */
function sarah_loz_get_logo_redirect_url() {
    // If it's a bot/crawler, always redirect to home page for sharing purposes
    if (function_exists('sarah_loz_is_bot_or_crawler') && sarah_loz_is_bot_or_crawler()) {
        return esc_url(home_url('/'));
    }
    
    // If user is on the age selection page, redirect to home
    global $post;
    if ($post && get_page_template_slug($post->ID) === 'page-age-selection.php') {
        return esc_url(home_url('/'));
    }
    
    // Check if age group is already selected
    $selected_age_group = null;
    if (function_exists('sarah_loz_get_selected_age_group')) {
        $selected_age_group = sarah_loz_get_selected_age_group();
    }
    
    // If age group is selected, redirect to home page
    if ($selected_age_group) {
        return esc_url(home_url('/'));
    }
    
    // Try to find the age selection page
    $age_selection_url = sarah_loz_get_age_selection_page_url();
    
    // If age selection page exists, redirect there
    if ($age_selection_url) {
        return esc_url($age_selection_url);
    }
    
    // Fallback to home page if age selection page not found
    return esc_url(home_url('/'));
}

/**
 * Get the URL of the age selection page
 */
function sarah_loz_get_age_selection_page_url() {
    // Method 1: Find by template
    $pages_with_template = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => 'page-age-selection.php',
        'number' => 1,
        'post_status' => 'publish'
    ));
    
    if (!empty($pages_with_template)) {
        return get_permalink($pages_with_template[0]->ID);
    }
    
    // Method 2: Find by slug
    $page_by_slug = get_page_by_path('age-selection');
    if ($page_by_slug && $page_by_slug->post_status === 'publish') {
        return get_permalink($page_by_slug->ID);
    }
    
    // Method 3: Find by title
    $page_by_title = get_page_by_title('Age Selection');
    if ($page_by_title && $page_by_title->post_status === 'publish') {
        return get_permalink($page_by_title->ID);
    }
    
    // Method 4: Find any page using the age selection template
    $all_pages = get_pages(array(
        'post_status' => 'publish',
        'number' => 100
    ));
    
    foreach ($all_pages as $page) {
        if (get_page_template_slug($page->ID) === 'page-age-selection.php') {
            return get_permalink($page->ID);
        }
    }
    
    return null;
}

/**
 * Get tooltip text for the logo link
 */
function sarah_loz_get_logo_tooltip_text() {
    // Don't show tooltips for bots
    if (function_exists('sarah_loz_is_bot_or_crawler') && sarah_loz_is_bot_or_crawler()) {
        return get_bloginfo('name');
    }
    
    // Check if age group is selected
    $selected_age_group = null;
    if (function_exists('sarah_loz_get_selected_age_group')) {
        $selected_age_group = sarah_loz_get_selected_age_group();
    }
    
    // If on age selection page
    global $post;
    if ($post && get_page_template_slug($post->ID) === 'page-age-selection.php') {
        return 'العودة للصفحة الرئيسية';
    }
    
    // If age group is selected
    if ($selected_age_group) {
        return 'اضغط لتغيير الفئة العمرية أو العودة للصفحة الرئيسية';
    }
    
    // If no age group selected
    return 'اضغط لاختيار فئتك العمرية والبدء في المغامرة!';
}

/**
 * AJAX handler for video category filtering
 */
function sarah_loz_filter_videos_ajax() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'sarah_loz_video_filter_nonce')) {
        wp_send_json_error('Security check failed');
        return;
    }
    
    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    
    // Get the selected age group
    $selected_age_group = sarah_loz_get_selected_age_group();
    
    // Build query arguments
    $args = array(
        'post_type' => 'video',
        'posts_per_page' => 9,
        'paged' => $paged,
        'post_status' => 'publish'
    );
    
    // Build taxonomy query
    $tax_query = array();
    
    // Add category filter
    if (!empty($category)) {
        $tax_query[] = array(
            'taxonomy' => 'video_category',
            'field' => 'slug',
            'terms' => $category
        );
    }
    
    // Add age group filter if age group is selected
    if ($selected_age_group) {
        $tax_query[] = array(
            'taxonomy' => 'age_group',
            'field' => 'slug',
            'terms' => $selected_age_group
        );
    }
    
    // Set taxonomy query if we have any filters
    if (!empty($tax_query)) {
        if (count($tax_query) > 1) {
            $tax_query['relation'] = 'AND';
        }
        $args['tax_query'] = $tax_query;
    }
    
    $videos_query = new WP_Query($args);
    
    ob_start();
    
    if ($videos_query->have_posts()) :
        while ($videos_query->have_posts()) : $videos_query->the_post();
            $duration = get_field('video_duration') ?: '00:00';
            $view_count = get_field('view_count') ?: 0;
            $is_new = get_field('is_new');
            ?>
            <!-- Video Item -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition transform hover:-translate-y-2 hover:shadow-xl">
                <div class="relative">
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>" class="block h-48 overflow-hidden">
                            <?php the_post_thumbnail('medium_large', array('class' => 'w-full h-full object-cover')); ?>
                            <div class="absolute inset-0 bg-primary/20 flex items-center justify-center">
                                <i class="fas fa-play-circle text-6xl text-primary/80"></i>
                            </div>
                        </a>
                    <?php else : ?>
                        <div class="h-48 bg-primary/10 flex items-center justify-center">
                            <i class="fas fa-play-circle text-6xl text-primary/60"></i>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($is_new) : ?>
                        <span class="absolute top-4 right-4 bg-accent text-dark px-3 py-1 rounded-full text-sm font-bold"><?php _e('جديد', 'sarah-loz'); ?></span>
                    <?php endif; ?>
                    
                    <span class="absolute bottom-4 left-4 bg-dark/70 text-white px-2 py-1 rounded text-sm"><?php echo esc_html($duration); ?></span>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-xl font-bold text-dark">
                            <a href="<?php the_permalink(); ?>" class="hover:text-primary transition">
                                <?php the_title(); ?>
                            </a>
                        </h3>
                    </div>
                    <p class="text-gray-600 mb-4">
                        <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                    </p>
                    <div class="flex items-center text-sm text-gray-500 justify-between">
                        <span><i class="fas fa-eye ml-1"></i> <?php echo esc_html(number_format($view_count)); ?> <?php _e('مشاهدة', 'sarah-loz'); ?></span>
                        <span><i class="far fa-calendar ml-1"></i> <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?></span>
                    </div>
                </div>
            </div>
            <?php
        endwhile;
        wp_reset_postdata();
    else :
        // Fallback: show all videos if no filtered videos found
        $fallback_args = array(
            'post_type' => 'video',
            'posts_per_page' => 9,
            'paged' => $paged,
            'post_status' => 'publish'
        );
        
        // Only add category filter to fallback if category is selected
        if (!empty($category)) {
            $fallback_args['tax_query'] = array(
                array(
                    'taxonomy' => 'video_category',
                    'field' => 'slug',
                    'terms' => $category
                )
            );
        }
        
        $fallback_query = new WP_Query($fallback_args);
        
        if ($fallback_query->have_posts()) :
            ?>
            <div class="col-span-full text-center p-8 bg-white rounded-2xl shadow-lg mb-8">
                <div class="text-4xl mb-4">⚠️</div>
                <h3 class="text-xl font-bold text-orange-600 mb-2">ملاحظة</h3>
                <p class="text-gray-600 mb-4">لا توجد فيديوهات محددة لعمرك. نعرض لك جميع الفيديوهات المتاحة:</p>
            </div>
            <?php
            while ($fallback_query->have_posts()) : $fallback_query->the_post();
                $duration = get_field('video_duration') ?: '00:00';
                $view_count = get_field('view_count') ?: 0;
                $is_new = get_field('is_new');
                ?>
                <!-- Video Item -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition transform hover:-translate-y-2 hover:shadow-xl">
                    <div class="relative">
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>" class="block h-48 overflow-hidden">
                                <?php the_post_thumbnail('medium_large', array('class' => 'w-full h-full object-cover')); ?>
                                <div class="absolute inset-0 bg-primary/20 flex items-center justify-center">
                                    <i class="fas fa-play-circle text-6xl text-primary/80"></i>
                                </div>
                            </a>
                        <?php else : ?>
                            <div class="h-48 bg-primary/10 flex items-center justify-center">
                                <i class="fas fa-play-circle text-6xl text-primary/60"></i>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($is_new) : ?>
                            <span class="absolute top-4 right-4 bg-accent text-dark px-3 py-1 rounded-full text-sm font-bold"><?php _e('جديد', 'sarah-loz'); ?></span>
                        <?php endif; ?>
                        
                        <span class="absolute bottom-4 left-4 bg-dark/70 text-white px-2 py-1 rounded text-sm"><?php echo esc_html($duration); ?></span>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-xl font-bold text-dark">
                                <a href="<?php the_permalink(); ?>" class="hover:text-primary transition">
                                    <?php the_title(); ?>
                                </a>
                            </h3>
                        </div>
                        <p class="text-gray-600 mb-4">
                            <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                        </p>
                        <div class="flex items-center text-sm text-gray-500 justify-between">
                            <span><i class="fas fa-eye ml-1"></i> <?php echo esc_html(number_format($view_count)); ?> <?php _e('مشاهدة', 'sarah-loz'); ?></span>
                            <span><i class="far fa-calendar ml-1"></i> <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?></span>
                        </div>
                    </div>
                </div>
                <?php
            endwhile;
            wp_reset_postdata();
        else :
            ?>
            <div class="col-span-full text-center p-8 bg-white rounded-2xl shadow-lg">
                <div class="text-6xl mb-4 animate-bounce-slow">📹</div>
                <h3 class="text-2xl font-bold text-primary mb-4"><?php _e('لا توجد فيديوهات', 'sarah-loz'); ?></h3>
                <p class="text-gray-600 mb-6"><?php _e('لا توجد فيديوهات في هذا التصنيف حالياً.', 'sarah-loz'); ?></p>
            </div>
            <?php
        endif;
    endif;
    
    $html = ob_get_clean();
    
    // Get pagination info
    $max_pages = $videos_query->max_num_pages;
    
    wp_send_json_success(array(
        'html' => $html,
        'max_pages' => $max_pages,
        'found_posts' => $videos_query->found_posts,
        'current_page' => $paged
    ));
}
add_action('wp_ajax_sarah_loz_filter_videos', 'sarah_loz_filter_videos_ajax');
add_action('wp_ajax_nopriv_sarah_loz_filter_videos', 'sarah_loz_filter_videos_ajax');

/**
 * Enqueue video filter scripts
 */
function sarah_loz_enqueue_video_filter_scripts() {
    if (is_post_type_archive('video') || is_tax('video_category') || is_tax('age_group')) {
        wp_enqueue_script('sarah-loz-video-filter', get_template_directory_uri() . '/assets/js/video-filter.js', array('jquery'), '1.0.0', true);
        wp_localize_script('sarah-loz-video-filter', 'sarah_loz_video_filter', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sarah_loz_video_filter_nonce'),
            'loading_text' => __('جاري التحميل...', 'sarah-loz'),
            'no_videos_text' => __('لا توجد فيديوهات', 'sarah-loz')
        ));
    }
}
add_action('wp_enqueue_scripts', 'sarah_loz_enqueue_video_filter_scripts');

// Include additional functionality
require_once get_template_directory() . '/inc/achievements.php';
require_once get_template_directory() . '/inc/broadcast-admin.php';
require_once get_template_directory() . '/inc/broadcast-functions.php';
require_once get_template_directory() . '/inc/child-activity-tracker.php';
require_once get_template_directory() . '/inc/game-admin.php';
require_once get_template_directory() . '/inc/game-functions.php';
require_once get_template_directory() . '/inc/import-test-data.php';
require_once get_template_directory() . '/inc/login-redirect.php';
require_once get_template_directory() . '/inc/newsletter.php';
require_once get_template_directory() . '/inc/post-types.php';
require_once get_template_directory() . '/inc/practices.php';
require_once get_template_directory() . '/inc/taxonomies.php';
require_once get_template_directory() . '/inc/woocommerce.php';
require_once get_template_directory() . '/inc/topic-admin.php';
require_once get_template_directory() . '/inc/audio-game-shortcode.php';

// Audio game admin is now integrated with the existing game post type via ACF fields

// Function to automatically add topics page to primary menu
function sarah_loz_auto_add_topics_to_menu() {
    // Check if topics page exists
    $topics_page = get_page_by_path('topics');
    
    if (!$topics_page) {
        // Create topics page if it doesn't exist
        $topics_page_id = wp_insert_post(array(
            'post_title' => __('الموضوعات', 'sarah-loz'),
            'post_name' => 'topics',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => '',
            'page_template' => 'page-topics.php'
        ));
    } else {
        $topics_page_id = $topics_page->ID;
    }
    
    // Get primary menu
    $menu_locations = get_nav_menu_locations();
    $primary_menu_id = isset($menu_locations['primary']) ? $menu_locations['primary'] : 0;
    
    if ($primary_menu_id) {
        // Check if topics page is already in the menu
        $menu_items = wp_get_nav_menu_items($primary_menu_id);
        $topics_in_menu = false;
        
        if ($menu_items) {
            foreach ($menu_items as $item) {
                if ($item->object_id == $topics_page_id && $item->object == 'page') {
                    $topics_in_menu = true;
                    break;
                }
            }
        }
        
        // Add topics page to menu if not already there
        if (!$topics_in_menu) {
            wp_update_nav_menu_item($primary_menu_id, 0, array(
                'menu-item-title' => __('الموضوعات', 'sarah-loz'),
                'menu-item-object' => 'page',
                'menu-item-object-id' => $topics_page_id,
                'menu-item-status' => 'publish',
                'menu-item-type' => 'post_type'
            ));
        }
    }
}
