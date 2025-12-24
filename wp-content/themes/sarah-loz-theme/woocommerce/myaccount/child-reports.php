<?php
/**
 * Helper function to convert hex color to RGB
 */
function hex_to_rgb($hex) {
    $hex = str_replace('#', '', $hex);
    
    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    
    return "$r, $g, $b";
}

/**
 * Child Reports Template
 *
 * This template displays detailed child activity reports for parents in the WooCommerce account area.
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
    
    // Calculate activity stats by month
    $monthly_stats = array();
    $current_month = date('m');
    $current_year = date('Y');
    
    // Initialize monthly stats for the past 6 months
    for ($i = 0; $i < 6; $i++) {
        $month = date('m', strtotime("-$i months"));
        $year = date('Y', strtotime("-$i months"));
        $month_name = date_i18n('F Y', strtotime("$year-$month-01"));
        
        $monthly_stats[$month_name] = array(
            'points' => 0,
            'activities' => 0,
            'achievements' => 0
        );
    }
    
    // Populate monthly stats
    foreach ($activity_log as $activity) {
        if (isset($activity['timestamp'])) {
            $activity_month = date('m', $activity['timestamp']);
            $activity_year = date('Y', $activity['timestamp']);
            $month_name = date_i18n('F Y', strtotime("$activity_year-$activity_month-01"));
            
            // Only include the past 6 months
            if (array_key_exists($month_name, $monthly_stats)) {
                $monthly_stats[$month_name]['points'] += isset($activity['points']) ? $activity['points'] : 0;
                $monthly_stats[$month_name]['activities']++;
            }
        }
    }
    
    // Count achievements by month
    foreach ($user_achievements as $achievement) {
        if (isset($achievement['date'])) {
            $achievement_month = date('m', strtotime($achievement['date']));
            $achievement_year = date('Y', strtotime($achievement['date']));
            $month_name = date_i18n('F Y', strtotime("$achievement_year-$achievement_month-01"));
            
            // Only include the past 6 months
            if (array_key_exists($month_name, $monthly_stats)) {
                $monthly_stats[$month_name]['achievements']++;
            }
        }
    }
    
    // Reverse the array to show oldest to newest
    $monthly_stats = array_reverse($monthly_stats);
    
    // Get activity types from tracker
    $activity_tracker = sarah_loz_child_activity_tracker();
    $activity_types = $activity_tracker->get_activity_types();
    
    // Count activities by type
    $activity_by_type = array();
    foreach ($activity_log as $activity) {
        if (isset($activity['type']) && isset($activity_types[$activity['type']])) {
            $type = $activity['type'];
            if (!isset($activity_by_type[$type])) {
                $activity_by_type[$type] = array(
                    'count' => 0,
                    'points' => 0,
                    'label' => $activity_types[$type]['label'],
                    'icon' => $activity_types[$type]['icon'],
                    'color' => $activity_types[$type]['color']
                );
            }
            
            $activity_by_type[$type]['count']++;
            $activity_by_type[$type]['points'] += isset($activity['points']) ? $activity['points'] : 0;
        }
    }
    ?>
    
    <div class="child-reports">
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
        
        <!-- Reports Header -->
        <div class="reports-header bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-right mb-4 md:mb-0">
                    <h1 class="text-2xl font-bold mb-2"><?php echo esc_html__('تقارير', 'sarah-loz'); ?> <?php echo esc_html($child_name); ?></h1>
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
                        <div class="text-3xl font-bold"><?php echo number_format(count($activity_log)); ?></div>
                        <div class="text-sm text-gray-600"><?php echo esc_html__('إجمالي الأنشطة', 'sarah-loz'); ?></div>
                    </div>
                    
                    <div class="stat-box bg-green-100 rounded-lg p-4 text-center min-w-[120px]">
                        <div class="text-3xl font-bold"><?php echo number_format(count($user_achievements)); ?></div>
                        <div class="text-sm text-gray-600"><?php echo esc_html__('الإنجازات', 'sarah-loz'); ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Monthly Activity Chart -->
        <div class="monthly-chart bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 text-right"><?php echo esc_html__('النشاط الشهري', 'sarah-loz'); ?></h2>
            
            <div class="chart-container" style="position: relative; height:300px;">
                <canvas id="monthlyActivityChart"></canvas>
            </div>
        </div>
        
        <!-- Activity by Type -->
        <div class="activity-by-type bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 text-right"><?php echo esc_html__('النشاط حسب النوع', 'sarah-loz'); ?></h2>
            
            <?php if (!empty($activity_by_type)) : ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="activityTypeChart"></canvas>
                    </div>
                    
                    <div class="activity-type-list">
                        <div class="grid grid-cols-1 gap-4">
                            <?php foreach ($activity_by_type as $type => $data) : ?>
                                <div class="activity-type-item flex items-center justify-between p-3 rounded-lg" style="background-color: rgba(<?php echo hex_to_rgb($data['color']); ?>, 0.1);">
                                    <div class="flex items-center">
                                        <div class="icon-wrapper mr-3 w-10 h-10 rounded-full flex items-center justify-center" style="background-color: rgba(<?php echo hex_to_rgb($data['color']); ?>, 0.3);">
                                            <i class="<?php echo esc_attr($data['icon']); ?>" style="color: <?php echo esc_attr($data['color']); ?>;"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold"><?php echo esc_html($data['label']); ?></div>
                                            <div class="text-sm text-gray-600"><?php echo number_format($data['count']); ?> <?php echo esc_html__('نشاط', 'sarah-loz'); ?></div>
                                        </div>
                                    </div>
                                    <div class="points text-lg font-bold">
                                        <?php echo number_format($data['points']); ?> <?php echo esc_html__('نقطة', 'sarah-loz'); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="empty-state text-center py-8">
                    <p class="text-gray-600"><?php echo esc_html__('لا توجد أنشطة مسجلة بعد.', 'sarah-loz'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Full Activity Log -->
        <div class="activity-log bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 text-right"><?php echo esc_html__('سجل النشاط الكامل', 'sarah-loz'); ?></h2>
            
            <?php if (!empty($activity_log)) : ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-right"><?php echo esc_html__('النشاط', 'sarah-loz'); ?></th>
                                <th class="py-3 px-6 text-right"><?php echo esc_html__('النوع', 'sarah-loz'); ?></th>
                                <th class="py-3 px-6 text-right"><?php echo esc_html__('النقاط', 'sarah-loz'); ?></th>
                                <th class="py-3 px-6 text-right"><?php echo esc_html__('التاريخ', 'sarah-loz'); ?></th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm">
                            <?php 
                            // Reverse the array to show newest first
                            $activity_log_reversed = array_reverse($activity_log);
                            
                            foreach ($activity_log_reversed as $activity) : 
                                $activity_type = isset($activity['type']) ? $activity['type'] : '';
                                $activity_label = isset($activity_types[$activity_type]['label']) ? $activity_types[$activity_type]['label'] : $activity_type;
                                $activity_icon = isset($activity_types[$activity_type]['icon']) ? $activity_types[$activity_type]['icon'] : 'fas fa-check';
                                $activity_color = isset($activity_types[$activity_type]['color']) ? $activity_types[$activity_type]['color'] : 'blue';
                                $activity_points = isset($activity['points']) ? $activity['points'] : 0;
                                $activity_date = isset($activity['timestamp']) ? date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $activity['timestamp']) : '';
                                
                                // Get content title if available
                                $content_title = '';
                                if (isset($activity['content_id']) && $activity['content_id'] > 0) {
                                    $content_post = get_post($activity['content_id']);
                                    if ($content_post) {
                                        $content_title = $content_post->post_title;
                                    }
                                }
                            ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 text-right">
                                        <div class="flex items-center justify-end">
                                            <span><?php echo esc_html($activity_label); ?></span>
                                            <?php if ($content_title) : ?>
                                                <span class="mr-2 text-gray-500">- <?php echo esc_html($content_title); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 text-right">
                                        <div class="flex items-center justify-end">
                                            <span class="bg-<?php echo esc_attr($activity_color); ?>-100 text-<?php echo esc_attr($activity_color); ?>-800 py-1 px-3 rounded-full text-xs">
                                                <i class="<?php echo esc_attr($activity_icon); ?> mr-1"></i>
                                                <?php 
                                                $type_label = '';
                                                switch ($activity_type) {
                                                    case 'game_started':
                                                    case 'game_completed':
                                                        $type_label = __('لعبة', 'sarah-loz');
                                                        break;
                                                    case 'activity_started':
                                                    case 'activity_completed':
                                                        $type_label = __('نشاط', 'sarah-loz');
                                                        break;
                                                    case 'video_started':
                                                    case 'video_completed':
                                                        $type_label = __('فيديو', 'sarah-loz');
                                                        break;
                                                    case 'practice_started':
                                                    case 'practice_completed':
                                                        $type_label = __('تمرين', 'sarah-loz');
                                                        break;
                                                    default:
                                                        $type_label = $activity_type;
                                                }
                                                echo esc_html($type_label);
                                                ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 text-right">
                                        <div class="flex items-center justify-end">
                                            <span class="font-bold text-green-600">+<?php echo number_format($activity_points); ?></span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 text-right">
                                        <div class="flex items-center justify-end">
                                            <span><?php echo esc_html($activity_date); ?></span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <div class="empty-state text-center py-8">
                    <p class="text-gray-600"><?php echo esc_html__('لا توجد أنشطة مسجلة بعد.', 'sarah-loz'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    jQuery(document).ready(function($) {
        // Monthly Activity Chart
        var monthlyCtx = document.getElementById('monthlyActivityChart').getContext('2d');
        var monthlyData = {
            labels: [
                <?php foreach (array_keys($monthly_stats) as $month) : ?>
                    '<?php echo esc_js($month); ?>',
                <?php endforeach; ?>
            ],
            datasets: [
                {
                    label: '<?php echo esc_js(__('النقاط', 'sarah-loz')); ?>',
                    data: [
                        <?php foreach ($monthly_stats as $month => $stats) : ?>
                            <?php echo esc_js($stats['points']); ?>,
                        <?php endforeach; ?>
                    ],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: '<?php echo esc_js(__('الأنشطة', 'sarah-loz')); ?>',
                    data: [
                        <?php foreach ($monthly_stats as $month => $stats) : ?>
                            <?php echo esc_js($stats['activities']); ?>,
                        <?php endforeach; ?>
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: '<?php echo esc_js(__('الإنجازات', 'sarah-loz')); ?>',
                    data: [
                        <?php foreach ($monthly_stats as $month => $stats) : ?>
                            <?php echo esc_js($stats['achievements']); ?>,
                        <?php endforeach; ?>
                    ],
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1
                }
            ]
        };
        
        var monthlyChart = new Chart(monthlyCtx, {
            type: 'bar',
            data: monthlyData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Activity Type Chart
        var typeCtx = document.getElementById('activityTypeChart').getContext('2d');
        var typeData = {
            labels: [
                <?php foreach ($activity_by_type as $type => $data) : ?>
                    '<?php echo esc_js($data['label']); ?>',
                <?php endforeach; ?>
            ],
            datasets: [{
                label: '<?php echo esc_js(__('عدد الأنشطة', 'sarah-loz')); ?>',
                data: [
                    <?php foreach ($activity_by_type as $type => $data) : ?>
                        <?php echo esc_js($data['count']); ?>,
                    <?php endforeach; ?>
                ],
                backgroundColor: [
                    <?php foreach ($activity_by_type as $type => $data) : ?>
                        'rgba(<?php echo hex_to_rgb($data['color']); ?>, 0.2)',
                    <?php endforeach; ?>
                ],
                borderColor: [
                    <?php foreach ($activity_by_type as $type => $data) : ?>
                        'rgba(<?php echo hex_to_rgb($data['color']); ?>, 1)',
                    <?php endforeach; ?>
                ],
                borderWidth: 1
            }]
        };
        
        var typeChart = new Chart(typeCtx, {
            type: 'doughnut',
            data: typeData,
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
    </script>
<?php
} else {
    // No children found or no child selected
    ?>
    <div class="no-children-message bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4 text-right"><?php echo esc_html__('لا يوجد أطفال', 'sarah-loz'); ?></h2>
        
        <div class="empty-state text-center py-8">
            <p class="text-gray-600 mb-4"><?php echo esc_html__('لم تقم بإضافة أي أطفال إلى حسابك بعد.', 'sarah-loz'); ?></p>
            
            <div class="mt-4">
                <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="inline-block bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition">
                    <?php echo esc_html__('العودة إلى لوحة التحكم', 'sarah-loz'); ?>
                </a>
            </div>
        </div>
    </div>
<?php
}
