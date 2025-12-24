<?php
/**
 * Child Dashboard Template
 *
 * This template displays the child dashboard in the WooCommerce account area.
 *
 * @package Sarah_Loz
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Get user meta data
$activity_points = get_user_meta($user_id, 'activity_points', true) ?: 0;
$achievement_points = get_user_meta($user_id, 'achievement_points', true) ?: 0;
$total_points = get_user_meta($user_id, 'total_points', true) ?: 0;
$login_streak = get_user_meta($user_id, 'login_streak', true) ?: 0;
$child_name = get_user_meta($user_id, 'child_name', true) ?: $current_user->display_name;
$child_age = get_user_meta($user_id, 'child_age', true) ?: '';

// Get activity log
$activity_log = get_user_meta($user_id, 'child_activity_log', true);
if (!is_array($activity_log)) {
    $activity_log = array();
}

// Get achievements
$user_achievements = get_user_meta($user_id, 'user_achievements', true);
if (!is_array($user_achievements)) {
    $user_achievements = array();
}

// Get recent activities (last 10)
$recent_activities = array_slice(array_reverse($activity_log), 0, 10);
?>

<div class="child-dashboard">
    <div class="dashboard-header bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="text-center md:text-right mb-4 md:mb-0">
                <h1 class="text-2xl font-bold mb-2"><?php echo esc_html__('مرحباً', 'sarah-loz'); ?> <?php echo esc_html($child_name); ?>!</h1>
                <?php if ($child_age) : ?>
                    <p class="text-gray-600"><?php echo esc_html($child_age); ?> <?php echo esc_html__('سنة', 'sarah-loz'); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="flex flex-wrap justify-center md:justify-end gap-4">
                <div class="stat-box bg-primary/10 rounded-lg p-4 text-center min-w-[120px]">
                    <div class="text-3xl font-bold"><?php echo number_format($total_points); ?></div>
                    <div class="text-sm text-gray-600"><?php echo esc_html__('مجموع النقاط', 'sarah-loz'); ?></div>
                </div>
                
                <div class="stat-box bg-yellow-100 rounded-lg p-4 text-center min-w-[120px]">
                    <div class="text-3xl font-bold"><?php echo number_format($activity_points); ?></div>
                    <div class="text-sm text-gray-600"><?php echo esc_html__('نقاط النشاط', 'sarah-loz'); ?></div>
                </div>
                
                <div class="stat-box bg-green-100 rounded-lg p-4 text-center min-w-[120px]">
                    <div class="text-3xl font-bold"><?php echo number_format($achievement_points); ?></div>
                    <div class="text-sm text-gray-600"><?php echo esc_html__('نقاط الإنجازات', 'sarah-loz'); ?></div>
                </div>
                
                <div class="stat-box bg-blue-100 rounded-lg p-4 text-center min-w-[120px]">
                    <div class="text-3xl font-bold"><?php echo number_format($login_streak); ?></div>
                    <div class="text-sm text-gray-600"><?php echo esc_html__('أيام متتالية', 'sarah-loz'); ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="dashboard-content grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4 text-right"><?php echo esc_html__('أنشطتك الأخيرة', 'sarah-loz'); ?></h2>
            
            <?php if (!empty($recent_activities)) : ?>
                <div class="activities-list space-y-4">
                    <?php foreach ($recent_activities as $activity) : ?>
                        <div class="activity-item border-b border-gray-100 pb-3 flex justify-between items-center">
                            <div class="activity-points bg-primary/10 px-2 py-1 rounded text-sm">
                                +<?php echo isset($activity['points']) ? number_format($activity['points']) : 0; ?> <?php echo esc_html__('نقطة', 'sarah-loz'); ?>
                            </div>
                            
                            <div class="activity-details text-right">
                                <div class="activity-title font-semibold">
                                    <?php echo isset($activity['title']) ? esc_html($activity['title']) : ''; ?>
                                </div>
                                <div class="activity-date text-sm text-gray-600">
                                    <?php echo isset($activity['date']) ? esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($activity['date']))) : ''; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-4 text-center">
                    <a href="<?php echo esc_url(home_url('/activities/')); ?>" class="inline-block bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition">
                        <?php echo esc_html__('استكشف المزيد من الأنشطة', 'sarah-loz'); ?>
                    </a>
                </div>
            <?php else : ?>
                <div class="empty-state text-center py-8">
                    <p class="text-gray-600 mb-4"><?php echo esc_html__('لم تقم بأي أنشطة بعد!', 'sarah-loz'); ?></p>
                    <a href="<?php echo esc_url(home_url('/activities/')); ?>" class="inline-block bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition">
                        <?php echo esc_html__('ابدأ الآن', 'sarah-loz'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Achievements -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4 text-right"><?php echo esc_html__('إنجازاتك', 'sarah-loz'); ?></h2>
            
            <?php if (!empty($user_achievements)) : ?>
                <div class="achievements-grid grid grid-cols-2 md:grid-cols-3 gap-4">
                    <?php foreach ($user_achievements as $achievement) : ?>
                        <div class="achievement-item text-center">
                            <div class="achievement-icon bg-yellow-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-2">
                                <i class="<?php echo isset($achievement['icon']) ? esc_attr($achievement['icon']) : 'fas fa-trophy'; ?> text-2xl text-yellow-600"></i>
                            </div>
                            <div class="achievement-title font-semibold text-sm">
                                <?php echo isset($achievement['title']) ? esc_html($achievement['title']) : ''; ?>
                            </div>
                            <div class="achievement-points text-xs text-gray-600">
                                +<?php echo isset($achievement['points']) ? number_format($achievement['points']) : 0; ?> <?php echo esc_html__('نقطة', 'sarah-loz'); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-4 text-center">
                    <a href="<?php echo esc_url(home_url('/achievements/')); ?>" class="inline-block bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition">
                        <?php echo esc_html__('عرض جميع الإنجازات', 'sarah-loz'); ?>
                    </a>
                </div>
            <?php else : ?>
                <div class="empty-state text-center py-8">
                    <p class="text-gray-600 mb-4"><?php echo esc_html__('لم تحصل على أي إنجازات بعد!', 'sarah-loz'); ?></p>
                    <a href="<?php echo esc_url(home_url('/achievements/')); ?>" class="inline-block bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition">
                        <?php echo esc_html__('استكشف الإنجازات', 'sarah-loz'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Recommended Activities -->
    <div class="recommended-activities bg-white rounded-lg shadow-lg p-6 mt-6">
        <h2 class="text-xl font-bold mb-4 text-right"><?php echo esc_html__('أنشطة موصى بها', 'sarah-loz'); ?></h2>
        
        <?php
        // Query for recommended activities
        $args = array(
            'post_type' => array('game', 'activity', 'video', 'practice'),
            'posts_per_page' => 3,
            'orderby' => 'rand',
        );
        
        $recommended_query = new WP_Query($args);
        
        if ($recommended_query->have_posts()) :
        ?>
            <div class="recommended-grid grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php while ($recommended_query->have_posts()) : $recommended_query->the_post(); ?>
                    <div class="recommended-item bg-gray-50 rounded-lg overflow-hidden">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="recommended-image">
                                <?php the_post_thumbnail('medium', array('class' => 'w-full h-40 object-cover')); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="recommended-content p-4">
                            <h3 class="font-bold mb-2 text-right"><?php the_title(); ?></h3>
                            <div class="recommended-type text-sm text-gray-600 mb-3 text-right">
                                <?php 
                                $post_type = get_post_type();
                                $type_label = '';
                                
                                switch ($post_type) {
                                    case 'game':
                                        $type_label = __('لعبة', 'sarah-loz');
                                        break;
                                    case 'activity':
                                        $type_label = __('نشاط', 'sarah-loz');
                                        break;
                                    case 'video':
                                        $type_label = __('فيديو', 'sarah-loz');
                                        break;
                                    case 'practice':
                                        $type_label = __('تمرين', 'sarah-loz');
                                        break;
                                }
                                
                                echo esc_html($type_label);
                                ?>
                            </div>
                            
                            <a href="<?php the_permalink(); ?>" class="block bg-primary text-white text-center px-4 py-2 rounded-lg hover:bg-primary-dark transition">
                                <?php echo esc_html__('ابدأ الآن', 'sarah-loz'); ?>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <div class="mt-4 text-center">
                <a href="<?php echo esc_url(home_url('/activities/')); ?>" class="inline-block bg-white border border-primary text-primary px-4 py-2 rounded-lg hover:bg-primary hover:text-white transition">
                    <?php echo esc_html__('عرض جميع الأنشطة', 'sarah-loz'); ?>
                </a>
            </div>
        <?php
        else :
        ?>
            <div class="empty-state text-center py-8">
                <p class="text-gray-600"><?php echo esc_html__('لا توجد أنشطة موصى بها حالياً.', 'sarah-loz'); ?></p>
            </div>
        <?php
        endif;
        wp_reset_postdata();
        ?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Update login streak on dashboard load
    $.ajax({
        url: sarah_loz_activity.ajax_url,
        type: 'POST',
        data: {
            action: 'sarah_loz_update_login_streak',
            nonce: sarah_loz_activity.nonce
        },
        success: function(response) {
            if (response.success && response.data.streak_updated) {
                // Optionally show a notification or update the UI
                console.log('Login streak updated to: ' + response.data.current_streak);
            }
        }
    });
});
</script>
