<?php
/**
 * Broadcast and Live Stream Functionality
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register broadcast scripts and styles
 */
function sarah_loz_broadcast_scripts() {
    // Enqueue only on broadcast pages
    if (is_singular('broadcast') || is_post_type_archive('broadcast')) {
        wp_enqueue_style('sarah-loz-broadcast', get_template_directory_uri() . '/assets/css/broadcast.css', array(), '1.0.0');
        wp_enqueue_script('sarah-loz-broadcast', get_template_directory_uri() . '/assets/js/broadcast.js', array('jquery'), '1.0.0', true);
        
        // Add localized script for AJAX
        wp_localize_script('sarah-loz-broadcast', 'sarah_loz_broadcast', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sarah_loz_broadcast_nonce'),
            'is_user_logged_in' => is_user_logged_in() ? 'yes' : 'no',
            'current_user_id' => get_current_user_id(),
        ));
    }
}
add_action('wp_enqueue_scripts', 'sarah_loz_broadcast_scripts');

/**
 * Update broadcast status based on scheduled time
 * Run daily via cron
 */
function sarah_loz_update_broadcast_status() {
    $current_time = current_time('mysql');
    
    // Get broadcasts scheduled to go live
    $args = array(
        'post_type' => 'broadcast',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'broadcast_type',
                'value' => 'scheduled',
            ),
        ),
    );
    
    $scheduled_broadcasts = get_posts($args);
    
    foreach ($scheduled_broadcasts as $broadcast) {
        $scheduled_date = get_field('broadcast_scheduled_date', $broadcast->ID);
        
        // If scheduled time has passed, update status to live
        if ($scheduled_date && strtotime($scheduled_date) <= strtotime($current_time)) {
            update_field('broadcast_type', 'live', $broadcast->ID);
            
            // Optional: Send notifications to subscribers
            sarah_loz_notify_broadcast_subscribers($broadcast->ID);
        }
    }
}
add_action('sarah_loz_daily_cron', 'sarah_loz_update_broadcast_status');

// Make sure the cron job is scheduled on theme activation
function sarah_loz_schedule_broadcast_cron() {
    if (!wp_next_scheduled('sarah_loz_daily_cron')) {
        wp_schedule_event(time(), 'daily', 'sarah_loz_daily_cron');
    }
}
add_action('after_switch_theme', 'sarah_loz_schedule_broadcast_cron');

// Clear the scheduled hook when theme is deactivated
function sarah_loz_clear_broadcast_cron() {
    wp_clear_scheduled_hook('sarah_loz_daily_cron');
}
add_action('switch_theme', 'sarah_loz_clear_broadcast_cron');

/**
 * Send notification to subscribed users when broadcast goes live
 */
function sarah_loz_notify_broadcast_subscribers($broadcast_id) {
    $broadcast_title = get_the_title($broadcast_id);
    $broadcast_link = get_permalink($broadcast_id);
    
    // Get users who should be notified (implementation depends on how subscription is managed)
    // This is a simple example - you might use a more complex system
    $users = get_users(array('role__in' => array('subscriber', 'child')));
    
    foreach ($users as $user) {
        $user_email = $user->user_email;
        $user_name = $user->display_name;
        
        $subject = sprintf(__('Broadcast "%s" is now live!', 'sarah-loz'), $broadcast_title);
        
        $message = sprintf(
            __('Hello %s,

The broadcast "%s" is now live! Join us now by clicking the link below:

%s

Happy learning!

Sarah & Loz Team', 'sarah-loz'),
            $user_name,
            $broadcast_title,
            $broadcast_link
        );
        
        wp_mail($user_email, $subject, $message);
    }
}

/**
 * Transition a broadcast from live to recorded status
 */
function sarah_loz_transition_broadcast_to_recorded($broadcast_id) {
    // Verify the broadcast exists and is live
    $broadcast_type = get_field('broadcast_type', $broadcast_id);
    
    if (!$broadcast_type || $broadcast_type !== 'live') {
        return new WP_Error('invalid_broadcast', __('هذا البث ليس مباشراً أو غير موجود', 'sarah-loz'));
    }
    
    // Update the broadcast type to recorded
    update_field('broadcast_type', 'recorded', $broadcast_id);
    
    // Copy the live embed code to the recorded embed code if available
    $live_embed = get_field('broadcast_live_embed', $broadcast_id);
    if ($live_embed) {
        update_field('broadcast_embed_code', $live_embed, $broadcast_id);
    }
    
    // Log the transition
    $transition_time = current_time('mysql');
    update_post_meta($broadcast_id, 'broadcast_ended_at', $transition_time);
    
    // Optional: Send notification to users that the broadcast is now available as a recording
    do_action('sarah_loz_broadcast_transitioned', $broadcast_id, $transition_time);
    
    return true;
}

/**
 * Handle AJAX request to transition broadcast
 */
function sarah_loz_ajax_transition_broadcast() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sarah_loz_broadcast_nonce')) {
        wp_send_json_error(array('message' => __('فشل التحقق الأمني', 'sarah-loz')));
    }
    
    // Check user permissions (only admin or editor)
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(array('message' => __('ليس لديك صلاحية لتنفيذ هذا الإجراء', 'sarah-loz')));
    }
    
    // Get broadcast ID
    $broadcast_id = isset($_POST['broadcast_id']) ? intval($_POST['broadcast_id']) : 0;
    
    if ($broadcast_id <= 0) {
        wp_send_json_error(array('message' => __('معرف البث غير صالح', 'sarah-loz')));
    }
    
    // Transition the broadcast
    $result = sarah_loz_transition_broadcast_to_recorded($broadcast_id);
    
    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => $result->get_error_message()));
    } else {
        wp_send_json_success(array(
            'message' => __('تم تحويل البث المباشر إلى مسجل بنجاح', 'sarah-loz'),
            'redirect' => get_permalink($broadcast_id)
        ));
    }
}
add_action('wp_ajax_sarah_loz_transition_broadcast', 'sarah_loz_ajax_transition_broadcast');

/**
 * Handle chat messages for live broadcasts
 */
function sarah_loz_send_chat_message() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sarah_loz_broadcast_nonce')) {
        wp_send_json_error(array('message' => __('Security check failed', 'sarah-loz')));
    }
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => __('You must be logged in to chat', 'sarah-loz')));
    }
    
    $broadcast_id = isset($_POST['broadcast_id']) ? intval($_POST['broadcast_id']) : 0;
    $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
    
    if (empty($message) || $broadcast_id === 0) {
        wp_send_json_error(array('message' => __('Invalid message or broadcast', 'sarah-loz')));
    }
    
    // Store the message
    $current_user = wp_get_current_user();
    $user_name = $current_user->display_name;
    $user_id = $current_user->ID;
    $timestamp = current_time('timestamp');
    
    // Create a unique message ID
    $message_id = 'msg_' . $user_id . '_' . $timestamp . '_' . wp_generate_password(6, false);
    
    $chat_message = array(
        'id' => $message_id,
        'user_id' => $user_id,
        'user_name' => $user_name,
        'message' => $message,
        'timestamp' => $timestamp,
    );
    
    // Get existing chat messages
    $chat_messages = get_post_meta($broadcast_id, 'broadcast_chat_messages', true);
    if (!is_array($chat_messages)) {
        $chat_messages = array();
    }
    
    // Check for duplicate messages (preventing double submissions)
    $is_duplicate = false;
    if (!empty($chat_messages)) {
        $last_message = end($chat_messages);
        // Consider it a duplicate if same user posted same message within 2 seconds
        if ($last_message['user_id'] == $user_id && 
            $last_message['message'] == $message && 
            ($timestamp - $last_message['timestamp']) < 2) {
            $is_duplicate = true;
        }
    }
    
    if (!$is_duplicate) {
        // Add new message
        $chat_messages[] = $chat_message;
        
        // Limit to last 100 messages to prevent excessive data
        if (count($chat_messages) > 100) {
            $chat_messages = array_slice($chat_messages, -100);
        }
        
        // Save updated messages
        update_post_meta($broadcast_id, 'broadcast_chat_messages', $chat_messages);
    }
    
    // Return success with the formatted message
    wp_send_json_success(array(
        'message' => $message,
        'user_name' => $user_name,
        'timestamp' => date_i18n('H:i', $timestamp),
        'id' => $message_id,
    ));
}
add_action('wp_ajax_sarah_loz_send_chat_message', 'sarah_loz_send_chat_message');

/**
 * Get chat messages for a broadcast
 */
function sarah_loz_get_chat_messages() {
    // Verify nonce
    if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'sarah_loz_broadcast_nonce')) {
        wp_send_json_error(array('message' => __('Security check failed', 'sarah-loz')));
    }
    
    $broadcast_id = isset($_GET['broadcast_id']) ? intval($_GET['broadcast_id']) : 0;
    
    if ($broadcast_id === 0) {
        wp_send_json_error(array('message' => __('Invalid broadcast', 'sarah-loz')));
    }
    
    // Get existing chat messages
    $chat_messages = get_post_meta($broadcast_id, 'broadcast_chat_messages', true);
    if (!is_array($chat_messages)) {
        $chat_messages = array();
    }
    
    // Format messages for display
    $formatted_messages = array();
    foreach ($chat_messages as $message) {
        // Generate an ID if one doesn't exist (for backward compatibility)
        $message_id = isset($message['id']) ? $message['id'] : 
                      'msg_' . $message['user_id'] . '_' . $message['timestamp'] . '_' . 
                      substr(md5($message['user_name'] . $message['message']), 0, 6);
        
        $formatted_messages[] = array(
            'id' => $message_id,
            'user_name' => $message['user_name'],
            'message' => $message['message'],
            'timestamp' => date_i18n('H:i', $message['timestamp']),
            'is_current_user' => get_current_user_id() == $message['user_id'],
        );
    }
    
    wp_send_json_success(array('messages' => $formatted_messages));
}
add_action('wp_ajax_sarah_loz_get_chat_messages', 'sarah_loz_get_chat_messages');
add_action('wp_ajax_nopriv_sarah_loz_get_chat_messages', 'sarah_loz_get_chat_messages');

/**
 * Add broadcast to child dashboard
 */
function sarah_loz_add_broadcasts_to_dashboard($suggested_content) {
    // Get upcoming broadcasts
    $args = array(
        'post_type' => 'broadcast',
        'posts_per_page' => 2,
        'meta_query' => array(
            array(
                'key' => 'broadcast_type',
                'value' => array('scheduled', 'live'),
                'compare' => 'IN',
            ),
        ),
        'meta_key' => 'broadcast_scheduled_date',
        'orderby' => 'meta_value',
        'order' => 'ASC',
    );
    
    $broadcasts = get_posts($args);
    
    if (!empty($broadcasts)) {
        $suggested_content['broadcasts'] = $broadcasts;
    }
    
    return $suggested_content;
}
add_filter('sarah_loz_child_dashboard_content', 'sarah_loz_add_broadcasts_to_dashboard');

/**
 * Track broadcast views and participation for child achievements
 */
function sarah_loz_track_broadcast_view($post_id) {
    if (!is_user_logged_in() || get_post_type($post_id) !== 'broadcast') {
        return;
    }
    
    $user_id = get_current_user_id();
    
    // Add to watched broadcasts
    $watched_broadcasts = get_user_meta($user_id, 'watched_broadcasts', true);
    if (!is_array($watched_broadcasts)) {
        $watched_broadcasts = array();
    }
    
    if (!in_array($post_id, $watched_broadcasts)) {
        $watched_broadcasts[] = $post_id;
        update_user_meta($user_id, 'watched_broadcasts', $watched_broadcasts);
        
        // Add points
        $current_points = (int) get_user_meta($user_id, 'activity_points', true);
        update_user_meta($user_id, 'activity_points', $current_points + 5);
        
        // Update total points
        $achievement_points = (int) get_user_meta($user_id, 'achievement_points', true);
        $total_points = $current_points + 5 + $achievement_points;
        update_user_meta($user_id, 'total_points', $total_points);
        
        // Add to activity timeline
        $timeline = get_user_meta($user_id, 'activity_timeline', true);
        if (!is_array($timeline)) {
            $timeline = array();
        }
        
        $timeline[] = array(
            'type' => 'broadcast_watched',
            'label' => __('Watched a broadcast', 'sarah-loz'),
            'timestamp' => current_time('timestamp'),
            'points' => 5,
            'data' => array(
                'broadcast_title' => get_the_title($post_id),
                'broadcast_id' => $post_id,
            ),
            'icon' => 'fas fa-tv',
            'color' => 'orange',
        );
        
        // Limit timeline size
        if (count($timeline) > 50) {
            $timeline = array_slice($timeline, -50);
        }
        
        update_user_meta($user_id, 'activity_timeline', $timeline);
        
        // Check for achievements
        sarah_loz_check_broadcast_achievements($user_id, $watched_broadcasts);
    }
}
add_action('sarah_loz_track_post_view', 'sarah_loz_track_broadcast_view');

/**
 * Check for broadcast-related achievements
 */
function sarah_loz_check_broadcast_achievements($user_id, $watched_broadcasts) {
    if (!is_array($watched_broadcasts)) {
        $watched_broadcasts = array();
    }
    
    $count = count($watched_broadcasts);
    $achievements = get_user_meta($user_id, 'user_achievements', true);
    
    if (!is_array($achievements)) {
        $achievements = array();
    }
    
    $achievement_added = false;
    
    // First broadcast achievement
    if ($count == 1) {
        $achievement = array(
            'id' => 'first_broadcast',
            'title' => __('First Broadcast', 'sarah-loz'),
            'description' => __('Watched your first broadcast!', 'sarah-loz'),
            'icon' => 'fas fa-tv',
            'color' => 'orange',
            'points' => 10,
            'date' => current_time('timestamp'),
        );
        
        if (!sarah_loz_user_has_achievement($achievements, 'first_broadcast')) {
            $achievements[] = $achievement;
            $achievement_added = true;
        }
    }
    
    // 5 broadcasts achievement
    if ($count >= 5) {
        $achievement = array(
            'id' => 'five_broadcasts',
            'title' => __('Broadcast Enthusiast', 'sarah-loz'),
            'description' => __('Watched 5 broadcasts!', 'sarah-loz'),
            'icon' => 'fas fa-tv',
            'color' => 'orange',
            'points' => 20,
            'date' => current_time('timestamp'),
        );
        
        if (!sarah_loz_user_has_achievement($achievements, 'five_broadcasts')) {
            $achievements[] = $achievement;
            $achievement_added = true;
        }
    }
    
    // 10 broadcasts achievement
    if ($count >= 10) {
        $achievement = array(
            'id' => 'ten_broadcasts',
            'title' => __('Broadcast Expert', 'sarah-loz'),
            'description' => __('Watched 10 broadcasts!', 'sarah-loz'),
            'icon' => 'fas fa-tv',
            'color' => 'orange',
            'points' => 30,
            'date' => current_time('timestamp'),
        );
        
        if (!sarah_loz_user_has_achievement($achievements, 'ten_broadcasts')) {
            $achievements[] = $achievement;
            $achievement_added = true;
        }
    }
    
    if ($achievement_added) {
        update_user_meta($user_id, 'user_achievements', $achievements);
        
        // Update achievement points
        $achievement_points = get_user_meta($user_id, 'achievement_points', true) ?: 0;
        $activity_points = get_user_meta($user_id, 'activity_points', true) ?: 0;
        
        if ($count == 1) {
            $achievement_points += 10;
        } elseif ($count == 5) {
            $achievement_points += 20;
        } elseif ($count == 10) {
            $achievement_points += 30;
        }
        
        update_user_meta($user_id, 'achievement_points', $achievement_points);
        update_user_meta($user_id, 'total_points', $achievement_points + $activity_points);
    }
}

/**
 * Helper function to check if user has an achievement
 */
function sarah_loz_user_has_achievement($achievements, $achievement_id) {
    if (!is_array($achievements)) {
        return false;
    }
    
    foreach ($achievements as $achievement) {
        if (is_array($achievement) && isset($achievement['id']) && $achievement['id'] == $achievement_id) {
            return true;
        }
    }
    return false;
}

/**
 * Add broadcast information to the child dashboard
 */
function sarah_loz_dashboard_broadcasts() {
    // Get upcoming broadcasts
    $args = array(
        'post_type' => 'broadcast',
        'posts_per_page' => 3,
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => 'broadcast_type',
                'value' => 'live',
            ),
            array(
                'key' => 'broadcast_type',
                'value' => 'scheduled',
            ),
        ),
        'meta_key' => 'broadcast_scheduled_date',
        'orderby' => 'meta_value',
        'order' => 'ASC',
    );
    
    $broadcasts = new WP_Query($args);
    
    if ($broadcasts->have_posts()) :
    ?>
    <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
        <h2 class="text-2xl font-bold mb-6">البث المباشر والبث المجدول</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php while ($broadcasts->have_posts()) : $broadcasts->the_post(); 
                $broadcast_type = get_field('broadcast_type');
                $scheduled_date = get_field('broadcast_scheduled_date');
                $is_live = $broadcast_type === 'live';
                $color = $is_live ? 'red' : 'blue';
                $icon = $is_live ? 'fas fa-satellite-dish' : 'fas fa-calendar-alt';
                $label = $is_live ? __('Live Now', 'sarah-loz') : __('Coming Soon', 'sarah-loz');
            ?>
            <a href="<?php the_permalink(); ?>" class="bg-gray-50 rounded-lg p-4 hover:shadow-md transition-shadow relative">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="relative mb-3">
                        <?php the_post_thumbnail('medium', array('class' => 'w-full h-40 object-cover rounded')); ?>
                        <div class="absolute top-2 right-2 bg-<?php echo esc_attr($color); ?>-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                            <?php echo esc_html($label); ?>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="rounded-full bg-<?php echo esc_attr($color); ?>-100 p-4 mb-3 inline-block">
                        <i class="<?php echo esc_attr($icon); ?> text-<?php echo esc_attr($color); ?>-500 text-2xl"></i>
                    </div>
                <?php endif; ?>
                <h3 class="font-bold mb-2"><?php the_title(); ?></h3>
                <?php if ($broadcast_type === 'scheduled' && $scheduled_date) : ?>
                    <p class="text-sm text-gray-600 mb-2">
                        <i class="far fa-clock mr-1"></i> 
                        <?php echo date_i18n('j F Y, g:i a', strtotime($scheduled_date)); ?>
                    </p>
                <?php endif; ?>
                <p class="text-sm text-gray-600"><?php echo wp_trim_words(get_the_excerpt(), 10); ?></p>
            </a>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
    <?php
    endif;
}
add_action('sarah_loz_child_dashboard_after_content', 'sarah_loz_dashboard_broadcasts');

/**
 * Add custom broadcast columns in admin
 */
function sarah_loz_add_broadcast_admin_columns($columns) {
    $new_columns = array();
    
    // Add columns before date
    foreach ($columns as $key => $value) {
        if ($key === 'date') {
            $new_columns['broadcast_type'] = __('نوع البث', 'sarah-loz');
            $new_columns['broadcast_status'] = __('حالة البث', 'sarah-loz');
            $new_columns['broadcast_date'] = __('تاريخ البث', 'sarah-loz');
        }
        $new_columns[$key] = $value;
    }
    
    return $new_columns;
}
add_filter('manage_broadcast_posts_columns', 'sarah_loz_add_broadcast_admin_columns');

/**
 * Add content to custom broadcast columns
 */
function sarah_loz_broadcast_admin_column_content($column, $post_id) {
    switch ($column) {
        case 'broadcast_type':
            $broadcast_type = get_field('broadcast_type', $post_id);
            $types = array(
                'live' => __('مباشر', 'sarah-loz'),
                'scheduled' => __('مجدول', 'sarah-loz'),
                'recorded' => __('مسجل', 'sarah-loz')
            );
            
            $colors = array(
                'live' => 'red',
                'scheduled' => 'blue',
                'recorded' => 'green'
            );
            
            $type_label = isset($types[$broadcast_type]) ? $types[$broadcast_type] : __('غير محدد', 'sarah-loz');
            $color = isset($colors[$broadcast_type]) ? $colors[$broadcast_type] : 'gray';
            
            echo '<span style="display:inline-block; background-color: ' . esc_attr($color) . '; color: white; padding: 3px 8px; border-radius: 3px;">' . esc_html($type_label) . '</span>';
            break;
            
        case 'broadcast_status':
            $broadcast_type = get_field('broadcast_type', $post_id);
            
            switch ($broadcast_type) {
                case 'live':
                    echo '<span style="color:green;"><strong>' . __('يبث الآن', 'sarah-loz') . '</strong></span>';
                    
                    // Add quick button to end live broadcast
                    if (current_user_can('edit_post', $post_id)) {
                        echo '<br><a href="#" class="transition-broadcast-button" data-id="' . esc_attr($post_id) . '" data-nonce="' . wp_create_nonce('sarah_loz_broadcast_nonce') . '">' . __('إنهاء البث وتحويله إلى مسجل', 'sarah-loz') . '</a>';
                    }
                    break;
                    
                case 'scheduled':
                    $scheduled_date = get_field('broadcast_scheduled_date', $post_id);
                    if ($scheduled_date) {
                        $now = current_time('timestamp');
                        $scheduled_time = strtotime($scheduled_date);
                        
                        if ($scheduled_time > $now) {
                            $time_diff = human_time_diff($now, $scheduled_time);
                            echo __('يبدأ بعد', 'sarah-loz') . ' ' . $time_diff;
                        } else {
                            echo '<span style="color:red;">' . __('متأخر - يجب أن يبدأ البث', 'sarah-loz') . '</span>';
                        }
                    } else {
                        echo __('تاريخ غير محدد', 'sarah-loz');
                    }
                    break;
                    
                case 'recorded':
                    $ended_at = get_post_meta($post_id, 'broadcast_ended_at', true);
                    if ($ended_at) {
                        echo __('تم التسجيل في', 'sarah-loz') . ' ' . date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($ended_at));
                    } else {
                        echo __('مسجل', 'sarah-loz');
                    }
                    break;
                    
                default:
                    echo __('غير محدد', 'sarah-loz');
                    break;
            }
            break;
            
        case 'broadcast_date':
            $broadcast_type = get_field('broadcast_type', $post_id);
            $scheduled_date = get_field('broadcast_scheduled_date', $post_id);
            
            if ($broadcast_type === 'scheduled' && $scheduled_date) {
                echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($scheduled_date));
            } else {
                echo '-';
            }
            break;
    }
}
add_action('manage_broadcast_posts_custom_column', 'sarah_loz_broadcast_admin_column_content', 10, 2);

/**
 * Add admin script for quick broadcast transitions
 */
function sarah_loz_broadcast_admin_scripts($hook) {
    if ($hook !== 'edit.php') {
        return;
    }
    
    global $post_type;
    if ($post_type !== 'broadcast') {
        return;
    }
    
    wp_enqueue_script('sarah-loz-broadcast-admin', get_template_directory_uri() . '/assets/js/broadcast-admin.js', array('jquery'), '1.0.0', true);
    wp_localize_script('sarah-loz-broadcast-admin', 'sarah_loz_broadcast_admin', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'transition_confirm' => __('هل أنت متأكد من إنهاء البث المباشر وتحويله إلى بث مسجل؟', 'sarah-loz'),
        'success_message' => __('تم تحويل البث المباشر إلى مسجل بنجاح', 'sarah-loz'),
        'error_message' => __('حدث خطأ أثناء معالجة الطلب. يرجى المحاولة مرة أخرى.', 'sarah-loz')
    ));
}
add_action('admin_enqueue_scripts', 'sarah_loz_broadcast_admin_scripts');

/**
 * Track active users watching a live broadcast
 */
function sarah_loz_track_broadcast_viewer() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sarah_loz_broadcast_nonce')) {
        wp_send_json_error(array('message' => __('Security check failed', 'sarah-loz')));
    }
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => __('User not logged in', 'sarah-loz')));
    }
    
    $broadcast_id = isset($_POST['broadcast_id']) ? intval($_POST['broadcast_id']) : 0;
    
    if ($broadcast_id === 0) {
        wp_send_json_error(array('message' => __('Invalid broadcast', 'sarah-loz')));
    }
    
    // Get current user info
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    $user_name = $current_user->display_name;
    $user_email = $current_user->user_email;
    $user_role = !empty($current_user->roles) ? $current_user->roles[0] : 'subscriber';
    
    // Get broadcast active viewers
    $active_viewers = get_post_meta($broadcast_id, 'broadcast_active_viewers', true);
    
    if (!is_array($active_viewers)) {
        $active_viewers = array();
    }
    
    // Update or add current user to active viewers
    $timestamp = current_time('timestamp');
    $active_viewers[$user_id] = array(
        'user_id' => $user_id,
        'user_name' => $user_name,
        'user_email' => $user_email,
        'user_role' => $user_role,
        'last_active' => $timestamp,
    );
    
    // Clean up inactive viewers (inactive for more than 2 minutes)
    foreach ($active_viewers as $id => $viewer) {
        if ($timestamp - $viewer['last_active'] > 120) {
            unset($active_viewers[$id]);
        }
    }
    
    // Save updated viewers
    update_post_meta($broadcast_id, 'broadcast_active_viewers', $active_viewers);
    
    // Also update total viewers count (unique users who have viewed)
    $total_viewers = get_post_meta($broadcast_id, 'broadcast_total_viewers', true);
    if (!is_array($total_viewers)) {
        $total_viewers = array();
    }
    
    if (!in_array($user_id, $total_viewers)) {
        $total_viewers[] = $user_id;
        update_post_meta($broadcast_id, 'broadcast_total_viewers', $total_viewers);
        
        // Also update peak concurrent viewers count
        $active_count = count($active_viewers);
        $peak_viewers = get_post_meta($broadcast_id, 'broadcast_peak_viewers', true);
        
        if ($active_count > intval($peak_viewers)) {
            update_post_meta($broadcast_id, 'broadcast_peak_viewers', $active_count);
        }
    }
    
    // Return success
    wp_send_json_success();
}
add_action('wp_ajax_sarah_loz_track_broadcast_viewer', 'sarah_loz_track_broadcast_viewer');

/**
 * Get active broadcast viewers - admin only
 */
function sarah_loz_get_broadcast_viewers() {
    // Verify nonce
    if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'sarah_loz_broadcast_nonce')) {
        wp_send_json_error(array('message' => __('Security check failed', 'sarah-loz')));
    }
    
    // Check if user has admin privileges
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(array('message' => __('You do not have permission to access this data', 'sarah-loz')));
    }
    
    $broadcast_id = isset($_GET['broadcast_id']) ? intval($_GET['broadcast_id']) : 0;
    
    if ($broadcast_id === 0) {
        wp_send_json_error(array('message' => __('Invalid broadcast', 'sarah-loz')));
    }
    
    // Get active viewers
    $active_viewers = get_post_meta($broadcast_id, 'broadcast_active_viewers', true);
    $total_viewers = get_post_meta($broadcast_id, 'broadcast_total_viewers', true);
    $peak_viewers = get_post_meta($broadcast_id, 'broadcast_peak_viewers', true);
    
    if (!is_array($active_viewers)) {
        $active_viewers = array();
    }
    
    if (!is_array($total_viewers)) {
        $total_viewers = array();
    }
    
    // Format response
    $response = array(
        'active_viewers' => array_values($active_viewers), // Convert to indexed array for easier handling in JS
        'active_count' => count($active_viewers),
        'total_count' => count($total_viewers),
        'peak_viewers' => intval($peak_viewers) ?: 0,
    );
    
    wp_send_json_success($response);
}
add_action('wp_ajax_sarah_loz_get_broadcast_viewers', 'sarah_loz_get_broadcast_viewers');

/**
 * Add broadcast stats metabox for admins
 */
function sarah_loz_add_broadcast_stats_metabox() {
    add_meta_box(
        'broadcast_stats_metabox',
        __('إحصائيات البث', 'sarah-loz'),
        'sarah_loz_broadcast_stats_metabox_callback',
        'broadcast',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'sarah_loz_add_broadcast_stats_metabox');

/**
 * Broadcast stats metabox callback
 */
function sarah_loz_broadcast_stats_metabox_callback($post) {
    // Get broadcast stats
    $broadcast_id = $post->ID;
    $broadcast_type = get_field('broadcast_type', $broadcast_id);
    $total_viewers = get_post_meta($broadcast_id, 'broadcast_total_viewers', true);
    $peak_viewers = get_post_meta($broadcast_id, 'broadcast_peak_viewers', true);
    
    if (!is_array($total_viewers)) {
        $total_viewers = array();
    }
    
    // Output stats
    ?>
    <p><strong><?php _e('إجمالي المشاهدين:', 'sarah-loz'); ?></strong> <?php echo count($total_viewers); ?></p>
    <p><strong><?php _e('ذروة المشاهدة المتزامنة:', 'sarah-loz'); ?></strong> <?php echo intval($peak_viewers) ?: 0; ?></p>
    
    <?php if ($broadcast_type === 'live') : ?>
        <hr>
        <div id="broadcast-live-stats">
            <p><strong><?php _e('المشاهدون النشطون حالياً:', 'sarah-loz'); ?></strong> <span id="active-viewers-count">-</span></p>
            <button type="button" id="view-active-viewers" class="button button-secondary">
                <?php _e('عرض المشاهدين النشطين', 'sarah-loz'); ?>
            </button>
            <div id="active-viewers-list" class="hidden" style="margin-top: 10px; max-height: 200px; overflow-y: auto;"></div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // Get active viewers function
            function getActiveViewers() {
                $.ajax({
                    url: ajaxurl,
                    type: 'GET',
                    data: {
                        action: 'sarah_loz_get_broadcast_viewers',
                        broadcast_id: <?php echo $broadcast_id; ?>,
                        nonce: '<?php echo wp_create_nonce('sarah_loz_broadcast_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#active-viewers-count').text(response.data.active_count);
                            
                            // Format viewers list
                            var viewersList = $('#active-viewers-list');
                            viewersList.empty();
                            
                            if (response.data.active_viewers.length === 0) {
                                viewersList.append('<p><?php _e('لا يوجد مشاهدون نشطون حالياً', 'sarah-loz'); ?></p>');
                            } else {
                                var list = $('<ul style="margin: 0; padding: 0 0 0 20px;"></ul>');
                                
                                $.each(response.data.active_viewers, function(index, viewer) {
                                    var lastActive = new Date(viewer.last_active * 1000);
                                    var timeString = lastActive.toLocaleTimeString();
                                    
                                    list.append(
                                        '<li style="margin-bottom: 5px;">' +
                                        '<strong>' + viewer.user_name + '</strong><br>' +
                                        '<small>' + viewer.user_email + ' (' + viewer.user_role + ')<br>' +
                                        '<?php _e('آخر نشاط:', 'sarah-loz'); ?> ' + timeString + '</small>' +
                                        '</li>'
                                    );
                                });
                                
                                viewersList.append(list);
                            }
                        }
                    }
                });
            }
            
            // Initial load and periodic refresh
            getActiveViewers();
            var refreshInterval = setInterval(getActiveViewers, 30000); // Refresh every 30 seconds
            
            // Toggle viewers list
            $('#view-active-viewers').on('click', function() {
                var viewersList = $('#active-viewers-list');
                
                if (viewersList.hasClass('hidden')) {
                    viewersList.removeClass('hidden');
                    getActiveViewers(); // Refresh data when showing
                    $(this).text('<?php _e('إخفاء المشاهدين النشطين', 'sarah-loz'); ?>');
                } else {
                    viewersList.addClass('hidden');
                    $(this).text('<?php _e('عرض المشاهدين النشطين', 'sarah-loz'); ?>');
                }
            });
            
            // Clean up interval when metabox is removed
            $(document).on('remove', '#broadcast_stats_metabox', function() {
                clearInterval(refreshInterval);
            });
        });
        </script>
    <?php endif; ?>
    <?php
} 