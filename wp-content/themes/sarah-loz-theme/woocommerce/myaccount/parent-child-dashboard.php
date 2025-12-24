<?php
/**
 * Parent Child Dashboard Template
 *
 * This template displays the parent view of child dashboard in the WooCommerce account area.
 *
 * @package Sarah_Loz
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$current_user = wp_get_current_user();
$parent_id = $current_user->ID;

// Check if a specific child is selected
$child_id = isset($_GET['child_id']) ? intval($_GET['child_id']) : 0;

// Get children list
$children = get_user_meta($parent_id, 'children', true);
if (!is_array($children)) {
    $children = array();
}

// If no specific child is selected but we have children, select the first one
if ($child_id === 0 && !empty($children)) {
    $child_id = $children[0];
}

// If we have a valid child ID
if ($child_id > 0) {
    // Verify this child belongs to the parent
    if (!in_array($child_id, $children)) {
        echo '<div class="woocommerce-error">' . esc_html__('غير مصرح لك بالوصول إلى بيانات هذا الطفل.', 'sarah-loz') . '</div>';
        return;
    }
    
    $child = get_user_by('ID', $child_id);
    
    if (!$child) {
        echo '<div class="woocommerce-error">' . esc_html__('لم يتم العثور على الطفل.', 'sarah-loz') . '</div>';
        return;
    }
    
    // Get child meta data
    $child_name = get_user_meta($child_id, 'child_name', true) ?: $child->display_name;
    $child_age = get_user_meta($child_id, 'child_age', true) ?: '';
    $activity_points = get_user_meta($child_id, 'activity_points', true) ?: 0;
    $achievement_points = get_user_meta($child_id, 'achievement_points', true) ?: 0;
    $total_points = get_user_meta($child_id, 'total_points', true) ?: 0;
    $login_streak = get_user_meta($child_id, 'login_streak', true) ?: 0;
    
    // Get activity log
    $activity_log = get_user_meta($child_id, 'child_activity_log', true);
    if (!is_array($activity_log)) {
        $activity_log = array();
    }
    
    // Get achievements
    $user_achievements = get_user_meta($child_id, 'user_achievements', true);
    if (!is_array($user_achievements)) {
        $user_achievements = array();
    }
    
    // Get recent activities (last 10)
    $recent_activities = array_slice(array_reverse($activity_log), 0, 10);
    
    // Calculate activity stats
    $total_activities = count($activity_log);
    $activities_this_week = 0;
    $current_week = date('W');
    $current_year = date('Y');
    
    foreach ($activity_log as $activity) {
        if (isset($activity['date'])) {
            $activity_week = date('W', strtotime($activity['date']));
            $activity_year = date('Y', strtotime($activity['date']));
            
            if ($activity_week === $current_week && $activity_year === $current_year) {
                $activities_this_week++;
            }
        }
    }
    
    // Get activity by type
    $activity_types = array(
        'game' => 0,
        'activity' => 0,
        'video' => 0,
        'practice' => 0
    );
    
    foreach ($activity_log as $activity) {
        if (isset($activity['type']) && array_key_exists($activity['type'], $activity_types)) {
            $activity_types[$activity['type']]++;
        }
    }
    ?>
    
    <div class="parent-child-dashboard">
        <!-- Child Selector -->
        <?php if (count($children) > 1) : ?>
            <div class="child-selector bg-white rounded-lg shadow-lg p-4 mb-6">
                <form method="get" class="flex flex-wrap items-center justify-between">
                    <label for="child_selector" class="font-bold ml-4"><?php echo esc_html__('اختر الطفل:', 'sarah-loz'); ?></label>
                    <div class="flex flex-1 items-center">
                        <select name="child_id" id="child_selector" class="w-full p-2 border border-gray-300 rounded-lg">
                            <?php foreach ($children as $child_option_id) : 
                                $child_option = get_user_by('ID', $child_option_id);
                                if ($child_option) :
                                    $option_name = get_user_meta($child_option_id, 'child_name', true) ?: $child_option->display_name;
                                ?>
                                    <option value="<?php echo esc_attr($child_option_id); ?>" <?php selected($child_option_id, $child_id); ?>>
                                        <?php echo esc_html($option_name); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg mr-2 hover:bg-primary-dark transition">
                            <?php echo esc_html__('عرض', 'sarah-loz'); ?>
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
        
        <!-- Dashboard Header -->
        <div class="dashboard-header bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-right mb-4 md:mb-0">
                    <h1 class="text-2xl font-bold mb-2"><?php echo esc_html($child_name); ?></h1>
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
        
        <!-- Activity Overview -->
        <div class="activity-overview bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 text-right"><?php echo esc_html__('نظرة عامة على النشاط', 'sarah-loz'); ?></h2>
            
            <div class="overview-stats grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="stat-item bg-gray-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold"><?php echo number_format($total_activities); ?></div>
                    <div class="text-sm text-gray-600"><?php echo esc_html__('إجمالي الأنشطة', 'sarah-loz'); ?></div>
                </div>
                
                <div class="stat-item bg-gray-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold"><?php echo number_format($activities_this_week); ?></div>
                    <div class="text-sm text-gray-600"><?php echo esc_html__('أنشطة هذا الأسبوع', 'sarah-loz'); ?></div>
                </div>
                
                <div class="stat-item bg-gray-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold"><?php echo number_format(count($user_achievements)); ?></div>
                    <div class="text-sm text-gray-600"><?php echo esc_html__('الإنجازات المكتسبة', 'sarah-loz'); ?></div>
                </div>
                
                <div class="stat-item bg-gray-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold"><?php echo number_format($login_streak); ?></div>
                    <div class="text-sm text-gray-600"><?php echo esc_html__('أيام متتالية', 'sarah-loz'); ?></div>
                </div>
            </div>
            
            <!-- Activity by Type -->
            <h3 class="text-lg font-bold mb-3 text-right"><?php echo esc_html__('الأنشطة حسب النوع', 'sarah-loz'); ?></h3>
            <div class="activity-types grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="type-item bg-red-50 p-4 rounded-lg text-center">
                    <div class="icon-wrapper mb-2">
                        <i class="fas fa-gamepad text-2xl text-red-500"></i>
                    </div>
                    <div class="text-xl font-bold"><?php echo number_format($activity_types['game']); ?></div>
                    <div class="text-sm text-gray-600"><?php echo esc_html__('ألعاب', 'sarah-loz'); ?></div>
                </div>
                
                <div class="type-item bg-blue-50 p-4 rounded-lg text-center">
                    <div class="icon-wrapper mb-2">
                        <i class="fas fa-tasks text-2xl text-blue-500"></i>
                    </div>
                    <div class="text-xl font-bold"><?php echo number_format($activity_types['activity']); ?></div>
                    <div class="text-sm text-gray-600"><?php echo esc_html__('أنشطة', 'sarah-loz'); ?></div>
                </div>
                
                <div class="type-item bg-green-50 p-4 rounded-lg text-center">
                    <div class="icon-wrapper mb-2">
                        <i class="fas fa-video text-2xl text-green-500"></i>
                    </div>
                    <div class="text-xl font-bold"><?php echo number_format($activity_types['video']); ?></div>
                    <div class="text-sm text-gray-600"><?php echo esc_html__('فيديوهات', 'sarah-loz'); ?></div>
                </div>
                
                <div class="type-item bg-purple-50 p-4 rounded-lg text-center">
                    <div class="icon-wrapper mb-2">
                        <i class="fas fa-book text-2xl text-purple-500"></i>
                    </div>
                    <div class="text-xl font-bold"><?php echo number_format($activity_types['practice']); ?></div>
                    <div class="text-sm text-gray-600"><?php echo esc_html__('تمارين', 'sarah-loz'); ?></div>
                </div>
            </div>
            
            <!-- Activity Chart -->
            <div class="activity-chart-container">
                <canvas id="activityChart" width="400" height="200"></canvas>
            </div>
        </div>
        
        <div class="dashboard-content grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Recent Activities -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4 text-right"><?php echo esc_html__('الأنشطة الأخيرة', 'sarah-loz'); ?></h2>
                
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
                        <a href="<?php echo esc_url(wc_get_account_endpoint_url('child-reports') . '?child_id=' . $child_id); ?>" class="inline-block bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition">
                            <?php echo esc_html__('عرض جميع الأنشطة', 'sarah-loz'); ?>
                        </a>
                    </div>
                <?php else : ?>
                    <div class="empty-state text-center py-8">
                        <p class="text-gray-600"><?php echo esc_html__('لم يقم طفلك بأي أنشطة بعد!', 'sarah-loz'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Achievements -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4 text-right"><?php echo esc_html__('الإنجازات', 'sarah-loz'); ?></h2>
                
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
                        <a href="<?php echo esc_url(wc_get_account_endpoint_url('child-reports') . '?child_id=' . $child_id . '&tab=achievements'); ?>" class="inline-block bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition">
                            <?php echo esc_html__('عرض جميع الإنجازات', 'sarah-loz'); ?>
                        </a>
                    </div>
                <?php else : ?>
                    <div class="empty-state text-center py-8">
                        <p class="text-gray-600"><?php echo esc_html__('لم يحصل طفلك على أي إنجازات بعد!', 'sarah-loz'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Parent Controls -->
        <div class="parent-controls bg-white rounded-lg shadow-lg p-6 mt-6">
            <h2 class="text-xl font-bold mb-4 text-right"><?php echo esc_html__('أدوات الوالد/ة', 'sarah-loz'); ?></h2>
            
            <div class="controls-grid grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="<?php echo esc_url(wc_get_account_endpoint_url('child-reports') . '?child_id=' . $child_id); ?>" class="control-item bg-blue-50 p-4 rounded-lg text-center hover:bg-blue-100 transition">
                    <div class="icon-wrapper mb-2">
                        <i class="fas fa-chart-bar text-2xl text-blue-500"></i>
                    </div>
                    <div class="text-lg font-bold"><?php echo esc_html__('تقارير مفصلة', 'sarah-loz'); ?></div>
                    <div class="text-sm text-gray-600"><?php echo esc_html__('عرض تقارير مفصلة عن نشاط طفلك', 'sarah-loz'); ?></div>
                </a>
                
                <a href="<?php echo esc_url(wc_get_account_endpoint_url('child-settings') . '?child_id=' . $child_id); ?>" class="control-item bg-green-50 p-4 rounded-lg text-center hover:bg-green-100 transition">
                    <div class="icon-wrapper mb-2">
                        <i class="fas fa-cog text-2xl text-green-500"></i>
                    </div>
                    <div class="text-lg font-bold"><?php echo esc_html__('إعدادات الحساب', 'sarah-loz'); ?></div>
                    <div class="text-sm text-gray-600"><?php echo esc_html__('تعديل إعدادات حساب طفلك', 'sarah-loz'); ?></div>
                </a>
                
                <a href="<?php echo esc_url(wc_get_account_endpoint_url('child-settings') . '?child_id=' . $child_id . '&action=reset'); ?>" class="control-item bg-red-50 p-4 rounded-lg text-center hover:bg-red-100 transition" onclick="return confirm('<?php echo esc_js(__('هل أنت متأكد من رغبتك في إعادة تعيين بيانات طفلك؟ لا يمكن التراجع عن هذا الإجراء.', 'sarah-loz')); ?>');">
                    <div class="icon-wrapper mb-2">
                        <i class="fas fa-redo-alt text-2xl text-red-500"></i>
                    </div>
                    <div class="text-lg font-bold"><?php echo esc_html__('إعادة تعيين', 'sarah-loz'); ?></div>
                    <div class="text-sm text-gray-600"><?php echo esc_html__('إعادة تعيين نقاط وإنجازات طفلك', 'sarah-loz'); ?></div>
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    jQuery(document).ready(function($) {
        // Activity chart
        var ctx = document.getElementById('activityChart').getContext('2d');
        
        // Get activity data for last 7 days
        var activityData = <?php 
            $days = 7;
            $labels = array();
            $data = array();
            
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $labels[] = date_i18n('D', strtotime($date));
                $count = 0;
                
                foreach ($activity_log as $activity) {
                    if (isset($activity['date']) && date('Y-m-d', strtotime($activity['date'])) === $date) {
                        $count++;
                    }
                }
                
                $data[] = $count;
            }
            
            echo json_encode(array(
                'labels' => $labels,
                'data' => $data
            ));
        ?>;
        
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: activityData.labels,
                datasets: [{
                    label: '<?php echo esc_js(__('عدد الأنشطة', 'sarah-loz')); ?>',
                    data: activityData.data,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: '<?php echo esc_js(__('نشاط الأسبوع الماضي', 'sarah-loz')); ?>'
                    }
                }
            }
        });
        
        // Auto-submit child selector form on change
        $('#child_selector').on('change', function() {
            $(this).closest('form').submit();
        });
    });
    </script>
    
<?php
} else {
    // No children found or no child selected
    ?>
    <div class="no-children-message bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4 text-right"><?php echo esc_html__('لا يوجد أطفال', 'sarah-loz'); ?></h2>
        
        <div class="message-content text-center py-8">
            <p class="text-gray-600 mb-4"><?php echo esc_html__('ليس لديك أي أطفال مسجلين بعد.', 'sarah-loz'); ?></p>
            <p class="text-gray-600 mb-6"><?php echo esc_html__('استخدم معرف الوالد/ة الخاص بك لإنشاء حساب لطفلك.', 'sarah-loz'); ?></p>
            
            <div class="parent-id-info bg-primary/10 p-4 rounded-lg inline-block">
                <?php
                $parent_id_code = get_user_meta($parent_id, 'parent_id_code', true);
                if ($parent_id_code) {
                    echo '<p class="font-bold mb-2">' . esc_html__('معرف الوالد/ة الخاص بك:', 'sarah-loz') . '</p>';
                    echo '<p class="text-xl">' . esc_html($parent_id_code) . '</p>';
                } else {
                    echo '<p>' . esc_html__('لم يتم إنشاء معرف الوالد/ة بعد.', 'sarah-loz') . '</p>';
                }
                ?>
            </div>
        </div>
    </div>
    <?php
}
?>
