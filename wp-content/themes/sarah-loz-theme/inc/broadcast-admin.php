<?php
/**
 * Broadcast Admin Functionality
 * 
 * Registers and handles broadcast admin pages and features
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register broadcast admin menus
 */
function sarah_loz_register_broadcast_admin_menu() {
    // Add broadcast dashboard page
    add_submenu_page(
        'edit.php?post_type=broadcast',
        __('Live Dashboard', 'sarah-loz'),
        __('Live Dashboard', 'sarah-loz'),
        'edit_posts',
        'broadcast-dashboard',
        'sarah_loz_broadcast_dashboard_page'
    );
}
add_action('admin_menu', 'sarah_loz_register_broadcast_admin_menu');

/**
 * Display the broadcast dashboard page
 */
function sarah_loz_broadcast_dashboard_page() {
    require_once(get_template_directory() . '/admin/broadcast-dashboard.php');
}

/**
 * Add live dashboard link to broadcast row actions
 */
function sarah_loz_add_broadcast_row_actions($actions, $post) {
    if ($post->post_type === 'broadcast') {
        $broadcast_type = get_field('broadcast_type', $post->ID);
        
        if ($broadcast_type === 'live') {
            $url = add_query_arg(
                array(
                    'page' => 'broadcast-dashboard',
                    'broadcast_id' => $post->ID
                ),
                admin_url('edit.php?post_type=broadcast')
            );
            
            $actions['live_dashboard'] = sprintf(
                '<a href="%s" style="color:#d63638;"><span class="dashicons dashicons-video-alt3" style="font-size:16px; vertical-align:text-bottom;"></span> %s</a>',
                esc_url($url),
                esc_html__('Live Dashboard', 'sarah-loz')
            );
        }
    }
    
    return $actions;
}
add_filter('post_row_actions', 'sarah_loz_add_broadcast_row_actions', 10, 2);

/**
 * Add admin column for dashboard link
 */
function sarah_loz_add_broadcast_dashboard_column($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        
        if ($key === 'title') {
            $new_columns['dashboard_link'] = __('Dashboard', 'sarah-loz');
        }
    }
    
    return $new_columns;
}
add_filter('manage_broadcast_posts_columns', 'sarah_loz_add_broadcast_dashboard_column');

/**
 * Add dashboard link to admin column
 */
function sarah_loz_broadcast_dashboard_column_content($column, $post_id) {
    if ($column === 'dashboard_link') {
        $broadcast_type = get_field('broadcast_type', $post_id);
        
        if ($broadcast_type === 'live') {
            $url = add_query_arg(
                array(
                    'page' => 'broadcast-dashboard',
                    'broadcast_id' => $post_id
                ),
                admin_url('edit.php?post_type=broadcast')
            );
            
            echo '<a href="' . esc_url($url) . '" class="button button-small" style="background:#d63638; color:white; border-color:#d63638;">
                <span class="dashicons dashicons-video-alt3" style="font-size:16px; vertical-align:text-bottom;"></span> 
                ' . esc_html__('Live Dashboard', 'sarah-loz') . '
            </a>';
        } else {
            echo '<span class="dashicons dashicons-minus"></span>';
        }
    }
}
add_action('manage_broadcast_posts_custom_column', 'sarah_loz_broadcast_dashboard_column_content', 10, 2);

/**
 * Add broadcast admin scripts
 */
function sarah_loz_broadcast_admin_dashboard_scripts($hook) {
    // Only load on our dashboard page
    if ($hook !== 'broadcast_page_broadcast-dashboard') {
        return;
    }
    
    // Enqueue necessary scripts
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js', array(), '3.9.1', true);
    wp_enqueue_script('sarah-loz-broadcast-admin', get_template_directory_uri() . '/assets/js/broadcast-admin.js', array('jquery'), '1.0.1', true);
    
    // Localize the script for AJAX
    wp_localize_script('sarah-loz-broadcast-admin', 'sarah_loz_broadcast_admin', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'transition_confirm' => __('هل أنت متأكد من إنهاء البث المباشر وتحويله إلى بث مسجل؟', 'sarah-loz'),
        'success_message' => __('تم تحويل البث المباشر إلى مسجل بنجاح', 'sarah-loz'),
        'error_message' => __('حدث خطأ أثناء معالجة الطلب. يرجى المحاولة مرة أخرى.', 'sarah-loz')
    ));
}
add_action('admin_enqueue_scripts', 'sarah_loz_broadcast_admin_dashboard_scripts');

/**
 * Add clear chat messages function for admin
 */
function sarah_loz_clear_chat_messages() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sarah_loz_broadcast_nonce')) {
        wp_send_json_error(array('message' => __('Security check failed', 'sarah-loz')));
    }
    
    // Check user permissions
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(array('message' => __('You do not have permission to perform this action', 'sarah-loz')));
    }
    
    $broadcast_id = isset($_POST['broadcast_id']) ? intval($_POST['broadcast_id']) : 0;
    
    if ($broadcast_id <= 0) {
        wp_send_json_error(array('message' => __('Invalid broadcast ID', 'sarah-loz')));
    }
    
    // Clear chat messages
    update_post_meta($broadcast_id, 'broadcast_chat_messages', array());
    
    // Return success
    wp_send_json_success(array('message' => __('Chat messages cleared successfully', 'sarah-loz')));
}
add_action('wp_ajax_sarah_loz_clear_chat_messages', 'sarah_loz_clear_chat_messages');

/**
 * Get chat messages for admin dashboard
 */
function sarah_loz_get_admin_chat_messages() {
    // Check nonce
    if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'sarah_loz_broadcast_nonce')) {
        wp_send_json_error(array('message' => __('Security check failed', 'sarah-loz')));
    }
    
    // Check user permissions
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(array('message' => __('You do not have permission to perform this action', 'sarah-loz')));
    }
    
    $broadcast_id = isset($_GET['broadcast_id']) ? intval($_GET['broadcast_id']) : 0;
    
    if ($broadcast_id <= 0) {
        wp_send_json_error(array('message' => __('Invalid broadcast ID', 'sarah-loz')));
    }
    
    // Get chat messages
    $chat_messages = get_post_meta($broadcast_id, 'broadcast_chat_messages', true);
    
    if (!is_array($chat_messages)) {
        $chat_messages = array();
    }
    
    // Format messages for display
    $formatted_messages = array();
    foreach ($chat_messages as $message) {
        $formatted_messages[] = array(
            'id' => isset($message['id']) ? $message['id'] : '',
            'user_name' => $message['user_name'],
            'message' => $message['message'],
            'timestamp' => date_i18n('H:i', $message['timestamp']),
            'is_admin' => isset($message['is_admin']) ? $message['is_admin'] : false,
        );
    }
    
    wp_send_json_success(array('messages' => $formatted_messages));
}
add_action('wp_ajax_sarah_loz_get_admin_chat_messages', 'sarah_loz_get_admin_chat_messages');

/**
 * Send admin message in chat
 */
function sarah_loz_send_admin_chat_message() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sarah_loz_broadcast_nonce')) {
        wp_send_json_error(array('message' => __('Security check failed', 'sarah-loz')));
    }
    
    // Check user permissions
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(array('message' => __('You do not have permission to perform this action', 'sarah-loz')));
    }
    
    $broadcast_id = isset($_POST['broadcast_id']) ? intval($_POST['broadcast_id']) : 0;
    $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
    
    if ($broadcast_id <= 0 || empty($message)) {
        wp_send_json_error(array('message' => __('Invalid broadcast ID or empty message', 'sarah-loz')));
    }
    
    // Get current user
    $current_user = wp_get_current_user();
    $user_name = $current_user->display_name . ' (Admin)';
    $user_id = $current_user->ID;
    $timestamp = current_time('timestamp');
    
    // Create a unique message ID
    $message_id = 'admin_' . $user_id . '_' . $timestamp . '_' . wp_generate_password(6, false);
    
    // Create chat message
    $chat_message = array(
        'id' => $message_id,
        'user_id' => $user_id,
        'user_name' => $user_name,
        'message' => $message,
        'timestamp' => $timestamp,
        'is_admin' => true
    );
    
    // Get existing messages
    $chat_messages = get_post_meta($broadcast_id, 'broadcast_chat_messages', true);
    
    if (!is_array($chat_messages)) {
        $chat_messages = array();
    }
    
    // Add new message
    $chat_messages[] = $chat_message;
    
    // Limit to last 100 messages
    if (count($chat_messages) > 100) {
        $chat_messages = array_slice($chat_messages, -100);
    }
    
    // Save updated messages
    update_post_meta($broadcast_id, 'broadcast_chat_messages', $chat_messages);
    
    // Return success
    wp_send_json_success(array(
        'message' => __('Message sent successfully', 'sarah-loz'),
        'chat_message' => array(
            'id' => $message_id,
            'user_name' => $user_name,
            'message' => $message,
            'timestamp' => date_i18n('H:i', $timestamp),
            'is_admin' => true
        )
    ));
}
add_action('wp_ajax_sarah_loz_send_admin_chat_message', 'sarah_loz_send_admin_chat_message'); 