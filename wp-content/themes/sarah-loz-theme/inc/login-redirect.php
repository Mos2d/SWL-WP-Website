<?php
/**
 * Login and registration redirect functionality
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Handle WooCommerce login redirect to return users to broadcasts
 */
function sarah_loz_handle_login_redirect($redirect, $user) {
    // Check if there's a redirect parameter
    if (isset($_GET['redirect_to']) && !empty($_GET['redirect_to'])) {
        $redirect_url = urldecode($_GET['redirect_to']);
        
        // Verify this is a valid URL on our site
        $site_url = site_url();
        if (strpos($redirect_url, $site_url) === 0) {
            return $redirect_url;
        }
    }
    
    return $redirect;
}
add_filter('woocommerce_login_redirect', 'sarah_loz_handle_login_redirect', 10, 2);

/**
 * Handle WordPress standard login redirect
 */
function sarah_loz_login_redirect($redirect_to, $request, $user) {
    // If it's an error or the user doesn't exist, use default
    if (is_wp_error($user) || !$user) {
        return $redirect_to;
    }

    // If there's a redirect_to parameter in the request
    if (isset($_REQUEST['redirect_to']) && !empty($_REQUEST['redirect_to'])) {
        $redirect_url = urldecode($_REQUEST['redirect_to']);
        
        // Verify this is a valid URL on our site
        $site_url = site_url();
        if (strpos($redirect_url, $site_url) === 0) {
            return $redirect_url;
        }
    }
    
    return $redirect_to;
}
add_filter('login_redirect', 'sarah_loz_login_redirect', 10, 3);

/**
 * Handle WooCommerce registration redirect
 */
function sarah_loz_registration_redirect($redirect) {
    if (isset($_GET['redirect_to']) && !empty($_GET['redirect_to'])) {
        $redirect_url = urldecode($_GET['redirect_to']);
        
        // Verify this is a valid URL on our site
        $site_url = site_url();
        if (strpos($redirect_url, $site_url) === 0) {
            return $redirect_url;
        }
    }
    
    return $redirect;
}
add_filter('woocommerce_registration_redirect', 'sarah_loz_registration_redirect', 10, 1); 