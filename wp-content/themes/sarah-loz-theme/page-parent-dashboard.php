<?php
/**
 * Template Name: Parent Dashboard
 * 
 * This template is used to display the parent dashboard with detailed progress reports
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

get_header();
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">لوحة تحكم الوالدين</h1>

    <!-- Children Progress Overview -->
    <?php
    $children = get_users(array(
        'meta_key' => 'parent_id',
        'meta_value' => get_current_user_id(),
        'meta_compare' => '='
    ));

    if (!empty($children)) :
        foreach ($children as $child) :
            // Get child's statistics
            $completed_games = get_user_meta($child->ID, 'completed_games', true) ?: array();
            $completed_activities = get_user_meta($child->ID, 'completed_activities', true) ?: array();
            $watched_videos = get_user_meta($child->ID, 'watched_videos', true) ?: array();
            $activity_points = sarah_loz_get_child_points($child->ID);
            $achievement_points = sarah_loz_achievements()->get_user_points($child->ID);
            $total_points = sarah_loz_get_child_total_points($child->ID);
            $login_streak = get_user_meta($child->ID, 'login_streak', true) ?: 0;
    ?>
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold"><?php echo esc_html($child->display_name); ?></h2>
                    <div class="flex items-center">
                        <div class="text-accent ml-4">
                            <i class="fas fa-star ml-2"></i>
                            <span class="font-bold"><?php echo esc_html($total_points); ?></span> نقطة
                        </div>
                        <?php if ($login_streak > 1) : ?>
                            <div class="bg-success bg-opacity-10 text-success rounded-full px-4 py-1">
                                <i class="fas fa-fire-alt ml-1"></i>
                                <span><?php echo $login_streak; ?> أيام متتالية</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Points Breakdown -->
                <div class="bg-light rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-bold mb-3">تفاصيل النقاط</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-primary"><?php echo $activity_points; ?></div>
                            <p class="text-gray-600">نقاط النشاط</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-secondary"><?php echo $achievement_points; ?></div>
                            <p class="text-gray-600">نقاط الإنجازات</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-accent"><?php echo $total_points; ?></div>
                            <p class="text-gray-600">المجموع</p>
                        </div>
                    </div>
                </div>

                <!-- Progress Overview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <?php
                    get_template_part('template-parts/content/progress-card', null, array(
                        'title' => 'الألعاب المكتملة',
                        'count' => count($completed_games),
                        'label' => 'لعبة',
                        'icon' => 'fa-gamepad',
                        'color' => 'primary'
                    ));

                    get_template_part('template-parts/content/progress-card', null, array(
                        'title' => 'الأنشطة المكتملة',
                        'count' => count($completed_activities),
                        'label' => 'نشاط',
                        'icon' => 'fa-paint-brush',
                        'color' => 'secondary'
                    ));

                    get_template_part('template-parts/content/progress-card', null, array(
                        'title' => 'الفيديوهات المشاهدة',
                        'count' => count($watched_videos),
                        'label' => 'فيديو',
                        'icon' => 'fa-play-circle',
                        'color' => 'accent'
                    ));
                    ?>
                </div>

                <!-- Latest Achievements -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold mb-4">آخر الإنجازات</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php
                        $achievements = sarah_loz_achievements()->get_user_achievements($child->ID);
                        $recent_achievements = array_slice($achievements, -3);
                        
                        if (!empty($recent_achievements)) :
                            foreach ($recent_achievements as $achievement) :
                        ?>
                            <div class="bg-light rounded-lg p-4 flex items-center">
                                <div class="w-12 h-12 bg-<?php echo $achievement['color']; ?> bg-opacity-10 rounded-full flex items-center justify-center mr-4">
                                    <i class="<?php echo $achievement['icon']; ?> text-<?php echo $achievement['color']; ?> text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold"><?php echo $achievement['title']; ?></h4>
                                    <p class="text-sm text-gray-600"><?php echo $achievement['points']; ?> نقطة</p>
                                </div>
                            </div>
                        <?php 
                            endforeach;
                        else :
                        ?>
                            <div class="col-span-3 text-center py-4 text-gray-500">
                                <p>لم يحصل الطفل على أي إنجازات بعد.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Activity Timeline -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold mb-4">النشاط الأخير</h3>
                    <div class="space-y-4">
                        <?php
                        // Get recent activities
                        $recent_activities = sarah_loz_child_activity_tracker()->get_user_activity_log($child->ID, 5);

                        if (!empty($recent_activities)) :
                            foreach ($recent_activities as $activity) :
                        ?>
                            <div class="flex items-start">
                                <div class="w-8 h-8 bg-<?php echo esc_attr($activity['color']); ?> bg-opacity-10 rounded-full flex items-center justify-center mt-1">
                                    <i class="<?php echo esc_attr($activity['icon']); ?> text-<?php echo esc_attr($activity['color']); ?> text-sm"></i>
                                </div>
                                <div class="mr-4 flex-grow">
                                    <div class="flex justify-between">
                                        <p class="font-bold"><?php echo esc_html($activity['title']); ?></p>
                                        <span class="text-sm text-<?php echo esc_attr($activity['color']); ?> font-bold">+<?php echo esc_html($activity['points']); ?> نقطة</span>
                                    </div>
                                    <p class="text-sm text-gray-600"><?php echo esc_html($activity['description']); ?></p>
                                    <span class="text-xs text-gray-500"><?php echo esc_html(human_time_diff($activity['timestamp'], current_time('timestamp'))); ?> مضت</span>
                                </div>
                            </div>
                        <?php 
                            endforeach;
                        else :
                        ?>
                            <div class="text-center py-4 text-gray-500">
                                <p>لم يتم تسجيل أي نشاط للطفل بعد.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Weekly Activity Stats -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold mb-4">إحصائيات النشاط الأسبوعي</h3>
                    <?php
                    // Get activity stats
                    $activity_stats = sarah_loz_child_activity_tracker()->get_activity_stats($child->ID, 'week');
                    
                    if (!empty($activity_stats)) :
                    ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <?php foreach ($activity_stats as $type => $stat) : ?>
                                <div class="bg-light rounded-lg p-4">
                                    <div class="flex items-center mb-2">
                                        <div class="w-8 h-8 bg-<?php echo esc_attr($stat['color']); ?> bg-opacity-10 rounded-full flex items-center justify-center ml-2">
                                            <i class="<?php echo esc_attr($stat['icon']); ?> text-<?php echo esc_attr($stat['color']); ?> text-sm"></i>
                                        </div>
                                        <h4 class="font-bold"><?php echo esc_html($stat['title']); ?></h4>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <div class="text-gray-600">عدد المرات: <span class="font-bold"><?php echo $stat['count']; ?></span></div>
                                        <div class="text-<?php echo esc_attr($stat['color']); ?> font-bold"><?php echo $stat['points']; ?> نقطة</div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="text-center py-4 text-gray-500">
                            <p>لا توجد إحصائيات للنشاط الأسبوعي بعد.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Learning Analytics -->
                <?php
                // Get weekly activity data for chart
                $weekly_data = sarah_loz_child_activity_tracker()->get_weekly_activity_data($child->ID);
                ?>
                <div class="mt-8">
                    <h3 class="text-xl font-bold mb-4">تحليل التعلم الأسبوعي</h3>
                    <div class="bg-light rounded-lg p-4">
                        <canvas id="analytics_<?php echo $child->ID; ?>" width="400" height="200"></canvas>
                    </div>
                    
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var ctx = document.getElementById('analytics_<?php echo $child->ID; ?>').getContext('2d');
                        var chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: <?php echo json_encode($weekly_data['labels']); ?>,
                                datasets: [
                                    {
                                        label: 'النقاط المكتسبة',
                                        data: <?php echo json_encode($weekly_data['points']); ?>,
                                        borderColor: '#4F46E5',
                                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                        tension: 0.3
                                    },
                                    {
                                        label: 'عدد الأنشطة',
                                        data: <?php echo json_encode($weekly_data['counts']); ?>,
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
                </div>
                
                <!-- Quick Actions -->
                <div class="mt-8 flex justify-end">
                    <a href="<?php echo esc_url(home_url('/child-report/' . $child->ID)); ?>" class="inline-block bg-primary text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition-colors ml-4">
                        <i class="fas fa-file-alt ml-2"></i>
                        تقرير مفصل
                    </a>
                    <a href="<?php echo esc_url(home_url('/child-settings/' . $child->ID)); ?>" class="inline-block bg-gray-500 text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition-colors">
                        <i class="fas fa-cog ml-2"></i>
                        إعدادات الطفل
                    </a>
                </div>
            </div>
        <?php
        endforeach;
    else :
        ?>
        <div class="bg-light rounded-lg p-6 text-center">
            <p>لم يتم ربط أي حساب طفل بحسابك بعد.</p>
            <a href="<?php echo esc_url(home_url('/add-child')); ?>" class="inline-block bg-primary text-white px-6 py-2 rounded-full mt-4 hover:bg-opacity-90 transition-colors">
                إضافة حساب طفل
            </a>
        </div>
    <?php endif; ?>
</div>

<?php
get_footer();
?>
