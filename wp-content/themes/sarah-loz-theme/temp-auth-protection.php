<?php
/**
 * Temporary Site Protection System
 * 
 * This file provides temporary authentication protection for the entire website.
 * Only WordPress users can access the site after logging in.
 * 
 * To remove this protection, simply delete this file and remove the require line from functions.php
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Check if user should be allowed access
 */
function temp_auth_check_access() {
    // Allow access to login page, admin area, and AJAX requests
    $allowed_pages = array(
        'temp-login',
        'wp-login.php',
        'wp-admin',
        'admin-ajax.php'
    );
    
    $current_url = $_SERVER['REQUEST_URI'];
    
    // Check if current page is allowed
    foreach ($allowed_pages as $page) {
        if (strpos($current_url, $page) !== false) {
            return true;
        }
    }
    
    // Allow if user is logged in
    if (is_user_logged_in()) {
        return true;
    }
    
    return false;
}

/**
 * Redirect non-authenticated users to login page
 */
function temp_auth_protect_site() {
    // Skip protection for admin area and login processes
    if (is_admin() || 
        strpos($_SERVER['REQUEST_URI'], 'wp-login') !== false ||
        strpos($_SERVER['REQUEST_URI'], 'wp-admin') !== false ||
        strpos($_SERVER['REQUEST_URI'], 'admin-ajax') !== false ||
        (defined('DOING_AJAX') && DOING_AJAX)) {
        return;
    }
    
    // Skip protection for our temp login page
    if (strpos($_SERVER['REQUEST_URI'], 'temp-login') !== false) {
        return;
    }
    
    // Check if user has access
    if (!temp_auth_check_access()) {
        // Store the current URL for redirect after login
        $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        
        // Redirect to our custom login page
        wp_redirect(home_url('/temp-login/?redirect_to=' . urlencode($current_url)));
        exit;
    }
}
add_action('template_redirect', 'temp_auth_protect_site', 1);

/**
 * Handle login form submission
 */
function temp_auth_handle_login() {
    if (!isset($_POST['temp_login_nonce']) || 
        !wp_verify_nonce($_POST['temp_login_nonce'], 'temp_login_form')) {
        return;
    }
    
    $username = sanitize_user($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;
    
    // Attempt to authenticate user
    $user = wp_authenticate($username, $password);
    
    if (is_wp_error($user)) {
        // Login failed
        wp_redirect(add_query_arg('login_error', '1', home_url('/temp-login/')));
        exit;
    }
    
    // Login successful
    wp_set_auth_cookie($user->ID, $remember);
    wp_set_current_user($user->ID);
    do_action('wp_login', $user->user_login, $user);
    
    // Redirect to original page or home
    $redirect_to = isset($_POST['redirect_to']) ? urldecode($_POST['redirect_to']) : home_url();
    
    // Verify redirect URL is on our site
    $site_url = site_url();
    if (strpos($redirect_to, $site_url) !== 0) {
        $redirect_to = home_url();
    }
    
    wp_redirect($redirect_to);
    exit;
}
add_action('init', 'temp_auth_handle_login');

/**
 * Add logout link to admin bar for easy access
 */
function temp_auth_add_logout_link() {
    if (!is_user_logged_in() || !is_admin_bar_showing()) {
        return;
    }
    
    global $wp_admin_bar;
    
    $wp_admin_bar->add_node(array(
        'id' => 'temp-logout',
        'title' => 'تسجيل الخروج من الحماية المؤقتة',
        'href' => wp_logout_url(home_url('/temp-login/')),
        'meta' => array(
            'class' => 'temp-logout-link'
        )
    ));
}
add_action('admin_bar_menu', 'temp_auth_add_logout_link', 999);

/**
 * Enqueue styles for temp login page
 */
function temp_auth_enqueue_styles() {
    if (strpos($_SERVER['REQUEST_URI'], 'temp-login') !== false) {
        // Enqueue the same styles as the theme
        wp_enqueue_style('tailwindcss', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css', array(), '2.2.19');
        wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Harmattan:wght@300;400;500;700&display=swap', array(), null);
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
        wp_enqueue_style('sarah-loz-style', get_stylesheet_uri(), array(), '1.0.0');
    }
}
add_action('wp_enqueue_scripts', 'temp_auth_enqueue_styles');
