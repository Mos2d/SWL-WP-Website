<?php
/**
 * Template Name: Child Activity Report
 * 
 * This template is used to display a detailed report of a child's activities
 */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

$current_user = wp_get_current_user();
// Check if user has parent role
if (!in_array('parent', (array) $current_user->roles)) {
    // Instead of redirecting, show a message
    get_header();
    ?>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold mb-4">غير مصرح</h1>
            <p class="mb-4">عذراً، هذه الصفحة متاحة فقط للآباء.</p>
            <a href="<?php echo esc_url(home_url()); ?>" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">العودة للصفحة الرئيسية</a>
        </div>
    </div>
    <?php
    get_footer();
    exit;
}

// Get child ID from URL parameter
$child_id = isset($_GET['child_id']) ? intval($_GET['child_id']) : 0;

// If no child ID is provided, redirect to parent dashboard
if ($child_id === 0) {
    wp_redirect(home_url('/parent-dashboard/'));
    exit;
}

// Check if the child belongs to this parent
$child_user = get_user_by('id', $child_id);
$parent_id = get_user_meta($child_id, 'parent_id', true);

if (!$child_user || $parent_id != $current_user->ID) {
    // Instead of redirecting, show a message
    get_header();
    ?>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold mb-4">غير مصرح</h1>
            <p class="mb-4">عذراً، لا يمكنك الوصول إلى تقرير هذا الطفل.</p>
            <a href="<?php echo esc_url(home_url('/parent-dashboard/')); ?>" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">العودة للوحة التحكم</a>
        </div>
    </div>
    <?php
    get_footer();
    exit;
}

get_header();

// Get child's data
$child_name = get_user_meta($child_id, 'child_name', true);
$child_age = get_user_meta($child_id, 'child_age', true);

// Get points and activity data
$activity_points = sarah_loz_get_child_points($child_id);
$achievement_points = sarah_loz_achievements()->get_user_points($child_id);
$total_points = sarah_loz_get_child_total_points($child_id);
$login_streak = get_user_meta($child_id, 'login_streak', true) ?: 0;

// Get activity counts
$completed_games = get_user_meta($child_id, 'completed_games', true);
$completed_games = is_array($completed_games) ? count($completed_games) : 0;

$completed_activities = get_user_meta($child_id, 'completed_activities', true);
$completed_activities = is_array($completed_activities) ? count($completed_activities) : 0;

$watched_videos = get_user_meta($child_id, 'watched_videos', true);
$watched_videos = is_array($watched_videos) ? count($watched_videos) : 0;

// Get activity log
$activity_log = sarah_loz_child_activity_tracker()->get_user_activity_log($child_id);

// Get achievements
$achievements = sarah_loz_achievements()->get_user_achievements($child_id);

// Get weekly activity data for chart
$weekly_data = sarah_loz_child_activity_tracker()->get_weekly_activity_data($child_id);

// Get monthly activity data
$monthly_data = array(
    'labels' => array(),
    'points' => array(),
    'counts' => array()
);

// Calculate monthly data (last 6 months)
$today = current_time('timestamp');
for ($i = 5; $i >= 0; $i--) {
    $month_timestamp = strtotime("-$i months", $today);
    $monthly_data['labels'][] = date_i18n('M Y', $month_timestamp);
    
    // Filter activities for this month
    $month_start = strtotime('first day of this month', $month_timestamp);
    $month_end = strtotime('last day of this month', $month_timestamp);
    
    $month_points = 0;
    $month_count = 0;
    
    foreach ($activity_log as $activity) {
        if ($activity['timestamp'] >= $month_start && $activity['timestamp'] <= $month_end) {
            $month_points += $activity['points'];
            $month_count++;
        }
    }
    
    $monthly_data['points'][] = $month_points;
    $monthly_data['counts'][] = $month_count;
}

// Get activity breakdown by type
$activity_types = array();
foreach ($activity_log as $activity) {
    $type = $activity['type'];
    if (!isset($activity_types[$type])) {
        $activity_types[$type] = array(
            'count' => 0,
            'points' => 0,
            'icon' => $activity['icon'],
            'color' => $activity['color'],
            'title' => $activity['title']
        );
    }
    
    $activity_types[$type]['count']++;
    $activity_types[$type]['points'] += $activity['points'];
}

// Sort activity types by count (descending)
uasort($activity_types, function($a, $b) {
    return $b['count'] - $a['count'];
});

// Get date filter from URL parameter
$date_filter = isset($_GET['date_filter']) ? sanitize_text_field($_GET['date_filter']) : 'all';

// Filter activity log based on date filter
$filtered_activity_log = $activity_log;
if ($date_filter !== 'all') {
    $today = current_time('timestamp');
    $filter_start = 0;
    
    switch ($date_filter) {
        case 'today':
            $filter_start = strtotime('today', $today);
            break;
        case 'week':
            $filter_start = strtotime('-1 week', $today);
            break;
        case 'month':
            $filter_start = strtotime('-1 month', $today);
            break;
        case '3months':
            $filter_start = strtotime('-3 months', $today);
            break;
    }
    
    $filtered_activity_log = array_filter($activity_log, function($activity) use ($filter_start) {
        return $activity['timestamp'] >= $filter_start;
    });
}

// Paginate activity log
$items_per_page = 20;
$total_items = count($filtered_activity_log);
$total_pages = ceil($total_items / $items_per_page);
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$offset = ($current_page - 1) * $items_per_page;

$paginated_activity_log = array_slice($filtered_activity_log, $offset, $items_per_page);
?>

<div class="container mx-auto px-4 py-8">
    <!-- Report Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold mb-2">تقرير نشاط <?php echo esc_html($child_name); ?></h1>
            <p class="text-gray-600"><?php echo esc_html($child_age); ?> سنوات</p>
        </div>
        
        <div class="mt-4 md:mt-0">
            <a href="<?php echo esc_url(home_url('/parent-dashboard/')); ?>" class="inline-block bg-gray-500 text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للوحة التحكم
            </a>
            
            <button onclick="window.print()" class="inline-block bg-primary text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition-colors mr-2">
                <i class="fas fa-print ml-2"></i>
                طباعة التقرير
            </button>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-primary bg-opacity-10 rounded-full flex items-center justify-center ml-4">
                    <i class="fas fa-star text-primary text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-600">مجموع النقاط</div>
                    <div class="text-2xl font-bold"><?php echo number_format($total_points); ?></div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-secondary bg-opacity-10 rounded-full flex items-center justify-center ml-4">
                    <i class="fas fa-check-circle text-secondary text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-600">الأنشطة المكتملة</div>
                    <div class="text-2xl font-bold"><?php echo $total_items; ?></div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-accent bg-opacity-10 rounded-full flex items-center justify-center ml-4">
                    <i class="fas fa-trophy text-accent text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-600">الإنجازات المكتسبة</div>
                    <div class="text-2xl font-bold"><?php echo count($achievements); ?></div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-success bg-opacity-10 rounded-full flex items-center justify-center ml-4">
                    <i class="fas fa-fire-alt text-success text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-600">أيام متتالية</div>
                    <div class="text-2xl font-bold"><?php echo $login_streak; ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Weekly Activity Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">النشاط الأسبوعي</h2>
            <canvas id="weeklyActivityChart" width="400" height="250"></canvas>
        </div>
        
        <!-- Monthly Activity Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">النشاط الشهري</h2>
            <canvas id="monthlyActivityChart" width="400" height="250"></canvas>
        </div>
    </div>
    
    <!-- Activity Breakdown -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold mb-6">تفاصيل النشاط حسب النوع</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php foreach ($activity_types as $type => $data) : ?>
                <div class="bg-light rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 bg-<?php echo esc_attr($data['color']); ?> bg-opacity-10 rounded-full flex items-center justify-center ml-3">
                            <i class="<?php echo esc_attr($data['icon']); ?> text-<?php echo esc_attr($data['color']); ?>"></i>
                        </div>
                        <h3 class="font-bold"><?php echo esc_html($data['title']); ?></h3>
                    </div>
                    <div class="flex justify-between">
                        <div class="text-gray-600">عدد المرات: <span class="font-bold"><?php echo $data['count']; ?></span></div>
                        <div class="text-<?php echo esc_attr($data['color']); ?> font-bold"><?php echo $data['points']; ?> نقطة</div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Activity Log -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">سجل النشاط</h2>
            
            <div class="flex">
                <form method="get" class="flex">
                    <input type="hidden" name="child_id" value="<?php echo $child_id; ?>">
                    <?php if (isset($_GET['paged'])) : ?>
                        <input type="hidden" name="paged" value="<?php echo intval($_GET['paged']); ?>">
                    <?php endif; ?>
                    
                    <select name="date_filter" class="ml-2 px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" onchange="this.form.submit()">
                        <option value="all" <?php selected($date_filter, 'all'); ?>>كل الوقت</option>
                        <option value="today" <?php selected($date_filter, 'today'); ?>>اليوم</option>
                        <option value="week" <?php selected($date_filter, 'week'); ?>>آخر أسبوع</option>
                        <option value="month" <?php selected($date_filter, 'month'); ?>>آخر شهر</option>
                        <option value="3months" <?php selected($date_filter, '3months'); ?>>آخر 3 أشهر</option>
                    </select>
                </form>
            </div>
        </div>
        
        <?php if (empty($paginated_activity_log)) : ?>
            <div class="text-center py-8 text-gray-500">
                <p>لا توجد أنشطة مسجلة في الفترة المحددة.</p>
            </div>
        <?php else : ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700">
                            <th class="py-3 px-4 text-right">التاريخ</th>
                            <th class="py-3 px-4 text-right">النشاط</th>
                            <th class="py-3 px-4 text-right">الوصف</th>
                            <th class="py-3 px-4 text-right">النقاط</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($paginated_activity_log as $activity) : ?>
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 text-gray-500">
                                    <?php echo date_i18n('Y/m/d - g:i a', $activity['timestamp']); ?>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-<?php echo esc_attr($activity['color']); ?> bg-opacity-10 rounded-full flex items-center justify-center ml-2">
                                            <i class="<?php echo esc_attr($activity['icon']); ?> text-<?php echo esc_attr($activity['color']); ?> text-sm"></i>
                                        </div>
                                        <span class="font-medium"><?php echo esc_html($activity['title']); ?></span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-600">
                                    <?php echo esc_html($activity['description']); ?>
                                </td>
                                <td class="py-3 px-4 text-<?php echo esc_attr($activity['color']); ?> font-bold">
                                    +<?php echo esc_html($activity['points']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1) : ?>
                <div class="flex justify-center mt-6">
                    <div class="flex space-x-1 rtl:space-x-reverse">
                        <?php
                        $pagination_range = 2; // Number of pages to show before and after current page
                        
                        // Previous page link
                        if ($current_page > 1) {
                            $prev_url = add_query_arg(array(
                                'child_id' => $child_id,
                                'paged' => $current_page - 1,
                                'date_filter' => $date_filter
                            ), get_permalink());
                            
                            echo '<a href="' . esc_url($prev_url) . '" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors ml-1">السابق</a>';
                        }
                        
                        // Page numbers
                        for ($i = max(1, $current_page - $pagination_range); $i <= min($total_pages, $current_page + $pagination_range); $i++) {
                            $page_url = add_query_arg(array(
                                'child_id' => $child_id,
                                'paged' => $i,
                                'date_filter' => $date_filter
                            ), get_permalink());
                            
                            $active_class = ($i === $current_page) ? 'bg-primary text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300';
                            
                            echo '<a href="' . esc_url($page_url) . '" class="px-4 py-2 ' . $active_class . ' rounded-lg transition-colors ml-1">' . $i . '</a>';
                        }
                        
                        // Next page link
                        if ($current_page < $total_pages) {
                            $next_url = add_query_arg(array(
                                'child_id' => $child_id,
                                'paged' => $current_page + 1,
                                'date_filter' => $date_filter
                            ), get_permalink());
                            
                            echo '<a href="' . esc_url($next_url) . '" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">التالي</a>';
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <!-- Achievements -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-6">الإنجازات المكتسبة</h2>
        
        <?php if (empty($achievements)) : ?>
            <div class="text-center py-8 text-gray-500">
                <p>لم يحصل الطفل على أي إنجازات بعد.</p>
            </div>
        <?php else : ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($achievements as $achievement) : ?>
                    <div class="bg-light rounded-lg p-4 flex items-center">
                        <div class="w-14 h-14 bg-<?php echo esc_attr($achievement['color']); ?> bg-opacity-10 rounded-full flex items-center justify-center ml-4">
                            <i class="<?php echo esc_attr($achievement['icon']); ?> text-<?php echo esc_attr($achievement['color']); ?> text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg"><?php echo esc_html($achievement['title']); ?></h3>
                            <p class="text-sm text-gray-600"><?php echo esc_html($achievement['description']); ?></p>
                            <div class="text-<?php echo esc_attr($achievement['color']); ?> font-bold mt-1">
                                +<?php echo esc_html($achievement['points']); ?> نقطة
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Weekly Activity Chart
    var weeklyCtx = document.getElementById('weeklyActivityChart').getContext('2d');
    var weeklyActivityChart = new Chart(weeklyCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($weekly_data['labels']); ?>,
            datasets: [
                {
                    label: 'النقاط المكتسبة',
                    data: <?php echo json_encode($weekly_data['points']); ?>,
                    backgroundColor: 'rgba(79, 70, 229, 0.2)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1
                },
                {
                    label: 'عدد الأنشطة',
                    data: <?php echo json_encode($weekly_data['counts']); ?>,
                    backgroundColor: 'rgba(236, 72, 153, 0.2)',
                    borderColor: 'rgba(236, 72, 153, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Monthly Activity Chart
    var monthlyCtx = document.getElementById('monthlyActivityChart').getContext('2d');
    var monthlyActivityChart = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($monthly_data['labels']); ?>,
            datasets: [
                {
                    label: 'النقاط المكتسبة',
                    data: <?php echo json_encode($monthly_data['points']); ?>,
                    borderColor: '#4F46E5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.3
                },
                {
                    label: 'عدد الأنشطة',
                    data: <?php echo json_encode($monthly_data['counts']); ?>,
                    borderColor: '#EC4899',
                    backgroundColor: 'rgba(236, 72, 153, 0.1)',
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>

<style>
@media print {
    .container {
        max-width: 100% !important;
        padding: 0 !important;
    }
    
    button, select, a.inline-block {
        display: none !important;
    }
    
    .shadow-lg {
        box-shadow: none !important;
        border: 1px solid #eee !important;
    }
    
    .mb-8 {
        margin-bottom: 20px !important;
    }
    
    .grid {
        display: block !important;
    }
    
    .grid > div {
        margin-bottom: 20px !important;
    }
}
</style>

<?php get_footer(); ?>
