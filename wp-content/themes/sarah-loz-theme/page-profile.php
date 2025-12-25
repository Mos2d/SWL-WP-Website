<?php
/**
 * Template Name: Edit Profile
 */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

get_header();

$current_user = wp_get_current_user();
$parent_name = get_user_meta($current_user->ID, 'parent_name', true);
$child_name = get_user_meta($current_user->ID, 'child_name', true);
$child_age = get_user_meta($current_user->ID, 'child_age', true);
?>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto">
        <!-- Profile Overview Card -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <div class="flex items-center space-x-4 rtl:space-x-reverse mb-6">
                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center text-white text-2xl">
                    <?php echo substr($current_user->display_name, 0, 1); ?>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-dark"><?php echo esc_html($current_user->display_name); ?></h1>
                    <p class="text-gray-600"><?php echo esc_html($current_user->user_email); ?></p>
                </div>
            </div>

            <?php
            if (isset($_GET['updated']) && $_GET['updated'] == 'true') {
                echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <p>تم تحديث معلوماتك بنجاح.</p>
                </div>';
            }
            ?>

            <form action="<?php echo admin_url('admin-post.php'); ?>" method="post" class="space-y-6">
                <input type="hidden" name="action" value="update_user_profile">
                <?php wp_nonce_field('update_user_profile_nonce', 'profile_nonce'); ?>

                <!-- Parent Information Section -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-xl font-bold text-dark mb-4">معلومات ولي الأمر</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="parent_name" class="block text-gray-700 mb-1">اسم ولي الأمر *</label>
                            <input type="text" name="parent_name" id="parent_name" 
                                   value="<?php echo esc_attr($parent_name); ?>" required
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>

                        <div>
                            <label for="email" class="block text-gray-700 mb-1">البريد الإلكتروني *</label>
                            <input type="email" name="email" id="email" 
                                   value="<?php echo esc_attr($current_user->user_email); ?>" required
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Child Information Section -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-xl font-bold text-dark mb-4">معلومات الطفل</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="child_name" class="block text-gray-700 mb-1">اسم الطفل *</label>
                            <input type="text" name="child_name" id="child_name" 
                                   value="<?php echo esc_attr($child_name); ?>" required
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>

                        <div>
                            <label for="child_age" class="block text-gray-700 mb-1">عمر الطفل *</label>
                            <select name="child_age" id="child_age" required
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                <?php for ($i = 3; $i <= 9; $i++) : ?>
                                    <option value="<?php echo $i; ?>" <?php selected($child_age, $i); ?>>
                                        <?php echo $i; ?> سنوات
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Password Change Section -->
                <div>
                    <h2 class="text-xl font-bold text-dark mb-4">تغيير كلمة المرور</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="new_password" class="block text-gray-700 mb-1">كلمة المرور الجديدة</label>
                            <input type="password" name="new_password" id="new_password"
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            <p class="text-sm text-gray-500 mt-1">اتركها فارغة إذا كنت لا تريد تغيير كلمة المرور</p>
                        </div>

                        <div>
                            <label for="confirm_password" class="block text-gray-700 mb-1">تأكيد كلمة المرور</label>
                            <input type="password" name="confirm_password" id="confirm_password"
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-6">
                    <button type="submit" class="bg-primary text-white px-8 py-3 rounded-full hover:bg-opacity-90 transition-colors">
                        حفظ التغييرات
                    </button>

                    <a href="<?php echo wp_logout_url(home_url()); ?>" class="text-gray-600 hover:text-primary transition-colors">
                        <i class="fas fa-sign-out-alt ml-1"></i>
                        تسجيل الخروج
                    </a>
                </div>
            </form>
        </div>

        <!-- Child Progress Section -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-dark mb-6">تقدم الطفل</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Games Progress -->
                <div class="bg-light rounded-lg p-6">
                    <h3 class="font-bold text-lg mb-3">الألعاب المكتملة</h3>
                    <?php
                    $completed_games = get_user_meta($current_user->ID, 'completed_games', true);
                    $completed_games = !empty($completed_games) ? count($completed_games) : 0;
                    ?>
                    <div class="text-3xl font-bold text-primary"><?php echo $completed_games; ?></div>
                    <p class="text-gray-600">لعبة</p>
                </div>

                <!-- Activities Progress -->
                <div class="bg-light rounded-lg p-6">
                    <h3 class="font-bold text-lg mb-3">الأنشطة المكتملة</h3>
                    <?php
                    $completed_activities = get_user_meta($current_user->ID, 'completed_activities', true);
                    $completed_activities = !empty($completed_activities) ? count($completed_activities) : 0;
                    ?>
                    <div class="text-3xl font-bold text-secondary"><?php echo $completed_activities; ?></div>
                    <p class="text-gray-600">نشاط</p>
                </div>

                <!-- Videos Watched -->
                <div class="bg-light rounded-lg p-6">
                    <h3 class="font-bold text-lg mb-3">الفيديوهات المشاهدة</h3>
                    <?php
                    $watched_videos = get_user_meta($current_user->ID, 'watched_videos', true);
                    $watched_videos = !empty($watched_videos) ? count($watched_videos) : 0;
                    ?>
                    <div class="text-3xl font-bold text-accent"><?php echo $watched_videos; ?></div>
                    <p class="text-gray-600">فيديو</p>
                </div>
            </div>

            <!-- Exam Results -->

            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <a href="<?php echo home_url('/exam-results'); ?>" class="inline-flex items-center justify-center bg-purple-600 text-white px-8 py-3 rounded-full hover:bg-purple-700 transition-colors shadow-md text-lg font-bold">
                    <i class="fas fa-chart-bar ml-2"></i>
                    عرض سجل نتائج الاختبارات (My Exams)
                </a>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
