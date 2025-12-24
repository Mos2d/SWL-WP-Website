<?php
/**
 * My Account Dashboard
 *
 * Shows the dashboard section with child information and progress.
 *
 * This template overrides /woocommerce/templates/myaccount/dashboard.php
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$allowed_html = array(
    'a' => array(
        'href' => array(),
    ),
);

// Get user meta information
$current_user = wp_get_current_user();
$parent_name = get_user_meta($current_user->ID, 'parent_name', true);
$child_name = get_user_meta($current_user->ID, 'child_name', true);
$child_age = get_user_meta($current_user->ID, 'child_age', true);

// Get child progress data from activity tracker
$user_id = $current_user->ID;
$activity_log = get_user_meta($user_id, 'child_activity_log', true);
if (!is_array($activity_log)) {
    $activity_log = array();
}

// Initialize counters
$completed_games = 0;
$completed_activities = 0;
$watched_videos = 0;

// Count activities by type
foreach ($activity_log as $activity) {
    if (isset($activity['type'])) {
        if ($activity['type'] === 'game_completed') {
            $completed_games++;
        } elseif ($activity['type'] === 'activity_completed') {
            $completed_activities++;
        } elseif ($activity['type'] === 'video_completed') {
            $watched_videos++;
        }
    }
}

// If user is a parent, get child data instead
if (in_array('parent', (array) $current_user->roles)) {
    // Get children list
    $children = get_user_meta($user_id, 'children', true);
    if (is_array($children) && !empty($children)) {
        // Use the first child for dashboard stats
        $child_id = $children[0];
        $child_user = get_user_by('ID', $child_id);
        
        if ($child_user) {
            $child_name = get_user_meta($child_id, 'child_name', true) ?: $child_user->display_name;
            $child_age = get_user_meta($child_id, 'child_age', true) ?: '';
            
            // Get child activity log
            $child_activity_log = get_user_meta($child_id, 'child_activity_log', true);
            if (is_array($child_activity_log)) {
                // Reset counters
                $completed_games = 0;
                $completed_activities = 0;
                $watched_videos = 0;
                
                // Count activities by type for child
                foreach ($child_activity_log as $activity) {
                    if (isset($activity['type'])) {
                        if ($activity['type'] === 'game_completed') {
                            $completed_games++;
                        } elseif ($activity['type'] === 'activity_completed') {
                            $completed_activities++;
                        } elseif ($activity['type'] === 'video_completed') {
                            $watched_videos++;
                        }
                    }
                }
            }
        }
    }
}
?>

<div class="dashboard-container">
    <div class="welcome-section p-4 mb-4 bg-white rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-3"><?php echo esc_html__('مرحباً', 'woocommerce') . ' ' . esc_html($current_user->display_name); ?></h2>
        <p><?php
            printf(
                /* translators: 1: user display name 2: logout url */
                wp_kses(__('From your account dashboard you can view your recent orders, manage your shipping and billing addresses, and <a href="%1$s">edit your password and account details</a>.', 'woocommerce'), $allowed_html),
                esc_url(wc_get_endpoint_url('edit-account'))
            );
        ?></p>
    </div>

    <?php if ($child_name) : ?>
    <div class="child-info-section p-4 mb-4 bg-white rounded-lg shadow">
        <h3 class="text-xl font-bold mb-3">معلومات الطفل</h3>
        <div class="child-details grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php if ($parent_name) : ?>
            <div class="detail-item">
                <span class="font-semibold">اسم الوالد:</span> <?php echo esc_html($parent_name); ?>
            </div>
            <?php endif; ?>
            
            <div class="detail-item">
                <span class="font-semibold">اسم الطفل:</span> <?php echo esc_html($child_name); ?>
            </div>
            
            <?php if ($child_age) : ?>
            <div class="detail-item">
                <span class="font-semibold">عمر الطفل:</span> <?php echo esc_html($child_age); ?> سنوات
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="child-progress-section p-4 bg-white rounded-lg shadow">
        <h3 class="text-xl font-bold mb-3">تقدم الطفل</h3>
        <div class="progress-stats grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
            <div class="stat-card p-3 bg-blue-50 rounded-lg">
                <i class="fas fa-gamepad text-3xl text-blue-500 mb-2"></i>
                <h4 class="font-bold">الألعاب المكتملة</h4>
                <p class="text-2xl font-bold text-blue-600"><?php echo esc_html($completed_games); ?></p>
            </div>
            
            <div class="stat-card p-3 bg-green-50 rounded-lg">
                <i class="fas fa-tasks text-3xl text-green-500 mb-2"></i>
                <h4 class="font-bold">الأنشطة المكتملة</h4>
                <p class="text-2xl font-bold text-green-600"><?php echo esc_html($completed_activities); ?></p>
            </div>
            
            <div class="stat-card p-3 bg-purple-50 rounded-lg">
                <i class="fas fa-video text-3xl text-purple-500 mb-2"></i>
                <h4 class="font-bold">الفيديوهات المشاهدة</h4>
                <p class="text-2xl font-bold text-purple-600"><?php echo esc_html($watched_videos); ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
    /**
     * My Account dashboard.
     *
     * @since 2.6.0
     */
    do_action('woocommerce_account_dashboard');

    /**
     * Deprecated woocommerce_before_my_account action.
     *
     * @deprecated 2.6.0
     */
    do_action('woocommerce_before_my_account');

    /**
     * Deprecated woocommerce_after_my_account action.
     *
     * @deprecated 2.6.0
     */
    do_action('woocommerce_after_my_account');
?> 