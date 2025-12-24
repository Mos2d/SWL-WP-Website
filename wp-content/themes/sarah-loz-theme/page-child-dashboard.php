<?php
/**
 * Template Name: Child Dashboard
 * 
 * This template is used to display the child dashboard with activity tracking and points
 */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

get_header();

$current_user = wp_get_current_user();
$child_name = get_user_meta($current_user->ID, 'child_name', true);
$child_age = get_user_meta($current_user->ID, 'child_age', true);

// Get points and activity data
$activity_points = get_user_meta($current_user->ID, 'activity_points', true) ?: 0;
$achievement_points = get_user_meta($current_user->ID, 'achievement_points', true) ?: 0;
$total_points = get_user_meta($current_user->ID, 'total_points', true) ?: ($activity_points + $achievement_points);
$login_streak = get_user_meta($current_user->ID, 'login_streak', true) ?: 0;

// Get activity counts
$completed_games = get_user_meta($current_user->ID, 'completed_games', true);
$completed_games = is_array($completed_games) ? count($completed_games) : 0;

$completed_activities = get_user_meta($current_user->ID, 'completed_activities', true);
$completed_activities = is_array($completed_activities) ? count($completed_activities) : 0;

$watched_videos = get_user_meta($current_user->ID, 'watched_videos', true);
$watched_videos = is_array($watched_videos) ? count($watched_videos) : 0;

// Get recent activities
$recent_activities = get_user_meta($current_user->ID, 'activity_timeline', true);
if (!is_array($recent_activities)) {
    $recent_activities = array();
} else {
    // Sort by timestamp (newest first)
    usort($recent_activities, function($a, $b) {
        return $b['timestamp'] - $a['timestamp'];
    });
    
    // Limit to 10 most recent
    if (count($recent_activities) > 10) {
        $recent_activities = array_slice($recent_activities, 0, 10);
    }
}

// Get weekly activity data for chart
$weekly_data = array(
    'labels' => array(),
    'points' => array(),
    'counts' => array()
);

// Get dates for the past 7 days
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $weekly_data['labels'][] = date_i18n('D', strtotime($date));
    $weekly_data['points'][] = 0;
    $weekly_data['counts'][] = 0;
}

// Get achievements
$achievements = get_user_meta($current_user->ID, 'user_achievements', true);
if (!is_array($achievements)) {
    $achievements = array();
}
$recent_achievements = array_slice($achievements, -3);
?>

<div class="container mx-auto px-4 py-8">
    <!-- Hero Section with Points Summary -->
    <div class="bg-gradient-to-r from-primary to-secondary rounded-xl shadow-xl p-6 mb-8 text-white">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="text-center md:text-right mb-6 md:mb-0">
                <h1 class="text-3xl font-bold mb-2">مرحباً <?php echo esc_html($child_name); ?>!</h1>
                <p class="text-xl opacity-90">مرحباً بك في لوحة التحكم الخاصة بك</p>
            </div>
            
            <div class="flex flex-wrap justify-center gap-4">
                <div class="bg-white bg-opacity-20 rounded-lg p-4 text-center min-w-[120px]">
                    <div class="text-3xl font-bold"><?php echo esc_html(number_format($activity_points)); ?></div>
                    <div class="text-sm opacity-90">نقاط النشاط</div>
                </div>
                
                <div class="bg-white bg-opacity-20 rounded-lg p-4 text-center min-w-[120px]">
                    <div class="text-3xl font-bold"><?php echo esc_html(number_format($achievement_points)); ?></div>
                    <div class="text-sm opacity-90">نقاط الإنجازات</div>
                </div>
                
                <div class="bg-white bg-opacity-20 rounded-lg p-4 text-center min-w-[120px]">
                    <div class="text-3xl font-bold"><?php echo esc_html(number_format($total_points)); ?></div>
                    <div class="text-sm opacity-90">المجموع</div>
                </div>
                
                <div class="bg-white bg-opacity-20 rounded-lg p-4 text-center min-w-[120px]">
                    <div class="text-3xl font-bold"><?php echo esc_html($login_streak); ?></div>
                    <div class="text-sm opacity-90">أيام متتالية</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Activity Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-lg p-6 flex items-center">
            <div class="rounded-full bg-blue-100 p-3 mr-4">
                <i class="fas fa-gamepad text-blue-500 text-xl"></i>
            </div>
            <div>
                <div class="text-sm text-gray-500">ألعاب مكتملة</div>
                <div class="text-2xl font-bold"><?php echo esc_html($completed_games); ?></div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6 flex items-center">
            <div class="rounded-full bg-purple-100 p-3 mr-4">
                <i class="fas fa-tasks text-purple-500 text-xl"></i>
            </div>
            <div>
                <div class="text-sm text-gray-500">أنشطة مكتملة</div>
                <div class="text-2xl font-bold"><?php echo esc_html($completed_activities); ?></div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6 flex items-center">
            <div class="rounded-full bg-red-100 p-3 mr-4">
                <i class="fas fa-play-circle text-red-500 text-xl"></i>
            </div>
            <div>
                <div class="text-sm text-gray-500">فيديوهات مشاهدة</div>
                <div class="text-2xl font-bold"><?php echo esc_html($watched_videos); ?></div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6">النشاط الأخير</h2>
            
            <?php if (empty($recent_activities)) : ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-info-circle text-3xl mb-3"></i>
                    <p>لم يتم تسجيل أي نشاط بعد. ابدأ باستكشاف المحتوى!</p>
                </div>
            <?php else : ?>
                <div class="space-y-4">
                    <?php foreach ($recent_activities as $activity) : ?>
                        <div class="flex items-start border-b border-gray-100 pb-4">
                            <div class="rounded-full bg-<?php echo esc_attr($activity['color']); ?>-100 p-3 mr-4 flex-shrink-0">
                                <i class="<?php echo esc_attr($activity['icon']); ?> text-<?php echo esc_attr($activity['color']); ?>-500"></i>
                            </div>
                            <div class="flex-grow">
                                <div class="font-medium"><?php echo esc_html($activity['label']); ?></div>
                                <?php if (isset($activity['data']['game_title'])) : ?>
                                    <div class="text-sm text-gray-600"><?php echo esc_html($activity['data']['game_title']); ?></div>
                                <?php elseif (isset($activity['data']['activity_title'])) : ?>
                                    <div class="text-sm text-gray-600"><?php echo esc_html($activity['data']['activity_title']); ?></div>
                                <?php elseif (isset($activity['data']['video_title'])) : ?>
                                    <div class="text-sm text-gray-600"><?php echo esc_html($activity['data']['video_title']); ?></div>
                                <?php endif; ?>
                                <div class="text-xs text-gray-500 mt-1">
                                    <span><?php echo esc_html(date_i18n('j F Y, g:i a', $activity['timestamp'])); ?></span>
                                    <span class="ml-2">+<?php echo esc_html($activity['points']); ?> نقطة</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Weekly Progress -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6">التقدم الأسبوعي</h2>
            <div class="h-64">
                <canvas id="weeklyActivityChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Recent Achievements -->
    <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
        <h2 class="text-2xl font-bold mb-6">الإنجازات الأخيرة</h2>
        
        <?php if (empty($recent_achievements)) : ?>
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-trophy text-3xl mb-3"></i>
                <p>لم تحصل على أي إنجازات بعد. واصل التعلم واللعب لكسب الإنجازات!</p>
            </div>
        <?php else : ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php foreach ($recent_achievements as $achievement) : ?>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="rounded-full bg-<?php echo esc_attr($achievement['color']); ?>-100 p-4 mx-auto mb-3 inline-block">
                            <i class="<?php echo esc_attr($achievement['icon']); ?> text-<?php echo esc_attr($achievement['color']); ?>-500 text-2xl"></i>
                        </div>
                        <h3 class="font-bold mb-1"><?php echo esc_html($achievement['title']); ?></h3>
                        <p class="text-sm text-gray-600 mb-2"><?php echo esc_html($achievement['description']); ?></p>
                        <div class="text-xs text-gray-500">
                            <span>+<?php echo esc_html($achievement['points']); ?> نقطة</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Suggested Content -->
    <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
        <h2 class="text-2xl font-bold mb-6">محتوى مقترح لك</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php
            // Get suggested content based on user interests and past activities
            $args = array(
                'post_type' => array('game', 'activity', 'video'),
                'posts_per_page' => 3,
                'orderby' => 'rand'
            );
            $suggested_query = new WP_Query($args);
            
            if ($suggested_query->have_posts()) :
                while ($suggested_query->have_posts()) : $suggested_query->the_post();
                    $post_type = get_post_type();
                    $icon_class = 'fas fa-gamepad';
                    $color = 'blue';
                    
                    if ($post_type === 'activity') {
                        $icon_class = 'fas fa-tasks';
                        $color = 'purple';
                    } elseif ($post_type === 'video') {
                        $icon_class = 'fas fa-play-circle';
                        $color = 'red';
                    }
            ?>
                <a href="<?php the_permalink(); ?>" class="bg-gray-50 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="rounded-full bg-<?php echo esc_attr($color); ?>-100 p-4 mb-3 inline-block">
                        <i class="<?php echo esc_attr($icon_class); ?> text-<?php echo esc_attr($color); ?>-500 text-2xl"></i>
                    </div>
                    <h3 class="font-bold mb-2"><?php the_title(); ?></h3>
                    <p class="text-sm text-gray-600"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                </a>
            <?php
                endwhile;
                wp_reset_postdata();
            else :
            ?>
                <div class="col-span-3 text-center py-8 text-gray-500">
                    <i class="fas fa-info-circle text-3xl mb-3"></i>
                    <p>لا يوجد محتوى مقترح حالياً. يرجى التحقق مرة أخرى لاحقاً.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Weekly Activity Chart
    var ctx = document.getElementById('weeklyActivityChart').getContext('2d');
    var weeklyActivityChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($weekly_data['labels']); ?>,
            datasets: [{
                label: 'النقاط',
                data: <?php echo json_encode($weekly_data['points']); ?>,
                backgroundColor: 'rgba(99, 102, 241, 0.5)',
                borderColor: 'rgba(99, 102, 241, 1)',
                borderWidth: 1
            }]
        },
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
});
</script>

<?php get_footer(); ?>
