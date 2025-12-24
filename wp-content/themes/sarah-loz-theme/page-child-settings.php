<?php
/**
 * Template Name: Child Settings
 * 
 * This template is used to manage child account settings
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
            <p class="mb-4">عذراً، لا يمكنك الوصول إلى إعدادات هذا الطفل.</p>
            <a href="<?php echo esc_url(home_url('/parent-dashboard/')); ?>" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">العودة للوحة التحكم</a>
        </div>
    </div>
    <?php
    get_footer();
    exit;
}

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_child_settings'])) {
    // Verify nonce
    if (!isset($_POST['child_settings_nonce']) || !wp_verify_nonce($_POST['child_settings_nonce'], 'update_child_settings')) {
        $error_message = 'فشل التحقق الأمني. يرجى المحاولة مرة أخرى.';
    } else {
        // Get form data
        $child_name = sanitize_text_field($_POST['child_name']);
        $child_age = intval($_POST['child_age']);
        $child_username = sanitize_user($_POST['child_username']);
        $child_email = sanitize_email($_POST['child_email']);
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validate inputs
        if (empty($child_name)) {
            $error_message = 'يرجى إدخال اسم الطفل.';
        } elseif (empty($child_age) || $child_age < 3 || $child_age > 12) {
            $error_message = 'يرجى اختيار عمر صحيح للطفل (3-12 سنة).';
        } elseif (empty($child_username)) {
            $error_message = 'يرجى إدخال اسم مستخدم للطفل.';
        } elseif (empty($child_email) || !is_email($child_email)) {
            $error_message = 'يرجى إدخال بريد إلكتروني صحيح.';
        } elseif ($child_username !== $child_user->user_login && username_exists($child_username)) {
            $error_message = 'اسم المستخدم موجود بالفعل. يرجى اختيار اسم آخر.';
        } elseif ($child_email !== $child_user->user_email && email_exists($child_email)) {
            $error_message = 'البريد الإلكتروني مستخدم بالفعل. يرجى استخدام بريد آخر.';
        } elseif (!empty($new_password) && $new_password !== $confirm_password) {
            $error_message = 'كلمتا المرور غير متطابقتين.';
        }
        
        // If no errors, update child settings
        if (empty($error_message)) {
            // Update user meta
            update_user_meta($child_id, 'child_name', $child_name);
            update_user_meta($child_id, 'child_age', $child_age);
            
            // Update user data if changed
            $userdata = array('ID' => $child_id);
            
            if ($child_username !== $child_user->user_login) {
                $userdata['user_login'] = $child_username;
            }
            
            if ($child_email !== $child_user->user_email) {
                $userdata['user_email'] = $child_email;
            }
            
            if (!empty($new_password)) {
                $userdata['user_pass'] = $new_password;
            }
            
            // Update user
            if (count($userdata) > 1) { // More than just ID
                $result = wp_update_user($userdata);
                
                if (is_wp_error($result)) {
                    $error_message = $result->get_error_message();
                }
            }
            
            // Update preferences
            $content_restrictions = isset($_POST['content_restrictions']) ? $_POST['content_restrictions'] : array();
            update_user_meta($child_id, 'content_restrictions', $content_restrictions);
            
            $time_limits = isset($_POST['time_limits']) ? $_POST['time_limits'] : array();
            update_user_meta($child_id, 'time_limits', $time_limits);
            
            $daily_limit_enabled = isset($_POST['daily_limit_enabled']) ? 1 : 0;
            update_user_meta($child_id, 'daily_limit_enabled', $daily_limit_enabled);
            
            $daily_limit_minutes = isset($_POST['daily_limit_minutes']) ? intval($_POST['daily_limit_minutes']) : 60;
            update_user_meta($child_id, 'daily_limit_minutes', $daily_limit_minutes);
            
            $notifications_enabled = isset($_POST['notifications_enabled']) ? 1 : 0;
            update_user_meta($child_id, 'notifications_enabled', $notifications_enabled);
            
            // If no errors occurred during update
            if (empty($error_message)) {
                $success_message = 'تم تحديث إعدادات الطفل بنجاح.';
                
                // Refresh child user data
                $child_user = get_user_by('id', $child_id);
            }
        }
    }
}

// Get child's data
$child_name = get_user_meta($child_id, 'child_name', true);
$child_age = get_user_meta($child_id, 'child_age', true);
$child_username = $child_user->user_login;
$child_email = $child_user->user_email;

// Get preferences
$content_restrictions = get_user_meta($child_id, 'content_restrictions', true) ?: array();
$time_limits = get_user_meta($child_id, 'time_limits', true) ?: array();
$daily_limit_enabled = get_user_meta($child_id, 'daily_limit_enabled', true) ?: 0;
$daily_limit_minutes = get_user_meta($child_id, 'daily_limit_minutes', true) ?: 60;
$notifications_enabled = get_user_meta($child_id, 'notifications_enabled', true) ?: 1;

get_header();
?>

<div class="container mx-auto px-4 py-8">
    <!-- Settings Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold mb-2">إعدادات حساب <?php echo esc_html($child_name); ?></h1>
            <p class="text-gray-600"><?php echo esc_html($child_age); ?> سنوات</p>
        </div>
        
        <div class="mt-4 md:mt-0">
            <a href="<?php echo esc_url(home_url('/parent-dashboard/')); ?>" class="inline-block bg-gray-500 text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للوحة التحكم
            </a>
        </div>
    </div>
    
    <?php if (!empty($success_message)) : ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <p><?php echo esc_html($success_message); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)) : ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <p><?php echo esc_html($error_message); ?></p>
        </div>
    <?php endif; ?>
    
    <form method="post" class="bg-white rounded-lg shadow-lg p-6">
        <?php wp_nonce_field('update_child_settings', 'child_settings_nonce'); ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Account Information -->
            <div>
                <h2 class="text-xl font-bold mb-6 border-b pb-2">معلومات الحساب</h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="child_name" class="block text-gray-700 mb-1">اسم الطفل *</label>
                        <input type="text" name="child_name" id="child_name" required 
                               value="<?php echo esc_attr($child_name); ?>"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="child_age" class="block text-gray-700 mb-1">عمر الطفل *</label>
                        <select name="child_age" id="child_age" required 
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            <?php for ($i = 3; $i <= 12; $i++) : ?>
                                <option value="<?php echo $i; ?>" <?php selected($child_age, $i); ?>>
                                    <?php echo $i; ?> سنوات
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="child_username" class="block text-gray-700 mb-1">اسم المستخدم *</label>
                        <input type="text" name="child_username" id="child_username" required 
                               value="<?php echo esc_attr($child_username); ?>"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="child_email" class="block text-gray-700 mb-1">البريد الإلكتروني *</label>
                        <input type="email" name="child_email" id="child_email" required 
                               value="<?php echo esc_attr($child_email); ?>"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    
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
            
            <!-- Preferences -->
            <div>
                <h2 class="text-xl font-bold mb-6 border-b pb-2">تفضيلات وإعدادات الأمان</h2>
                
                <div class="space-y-6">
                    <!-- Content Restrictions -->
                    <div>
                        <h3 class="font-bold text-lg mb-3">قيود المحتوى</h3>
                        <p class="text-sm text-gray-600 mb-3">حدد أنواع المحتوى التي يمكن للطفل الوصول إليها:</p>
                        
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="checkbox" name="content_restrictions[]" id="restrict_games" value="games" 
                                       <?php checked(in_array('games', $content_restrictions)); ?> class="ml-2">
                                <label for="restrict_games">السماح بالألعاب</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="content_restrictions[]" id="restrict_videos" value="videos" 
                                       <?php checked(in_array('videos', $content_restrictions)); ?> class="ml-2">
                                <label for="restrict_videos">السماح بالفيديوهات</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="content_restrictions[]" id="restrict_activities" value="activities" 
                                       <?php checked(in_array('activities', $content_restrictions)); ?> class="ml-2">
                                <label for="restrict_activities">السماح بالأنشطة</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="content_restrictions[]" id="restrict_quizzes" value="quizzes" 
                                       <?php checked(in_array('quizzes', $content_restrictions)); ?> class="ml-2">
                                <label for="restrict_quizzes">السماح بالاختبارات</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Time Limits -->
                    <div>
                        <h3 class="font-bold text-lg mb-3">حدود الوقت</h3>
                        
                        <div class="mb-4">
                            <div class="flex items-center mb-2">
                                <input type="checkbox" name="daily_limit_enabled" id="daily_limit_enabled" value="1" 
                                       <?php checked($daily_limit_enabled, 1); ?> class="ml-2">
                                <label for="daily_limit_enabled">تفعيل الحد اليومي للاستخدام</label>
                            </div>
                            
                            <div class="flex items-center">
                                <label for="daily_limit_minutes" class="ml-2">الحد اليومي (بالدقائق):</label>
                                <input type="number" name="daily_limit_minutes" id="daily_limit_minutes" 
                                       value="<?php echo esc_attr($daily_limit_minutes); ?>" min="10" max="240" step="5"
                                       class="w-20 px-2 py-1 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-3">حدد الأوقات التي يمكن للطفل استخدام الموقع فيها:</p>
                        
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="checkbox" name="time_limits[]" id="time_limit_morning" value="morning" 
                                       <?php checked(in_array('morning', $time_limits)); ?> class="ml-2">
                                <label for="time_limit_morning">الصباح (6:00 صباحاً - 12:00 ظهراً)</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="time_limits[]" id="time_limit_afternoon" value="afternoon" 
                                       <?php checked(in_array('afternoon', $time_limits)); ?> class="ml-2">
                                <label for="time_limit_afternoon">الظهيرة (12:00 ظهراً - 5:00 مساءً)</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="time_limits[]" id="time_limit_evening" value="evening" 
                                       <?php checked(in_array('evening', $time_limits)); ?> class="ml-2">
                                <label for="time_limit_evening">المساء (5:00 مساءً - 9:00 مساءً)</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="time_limits[]" id="time_limit_night" value="night" 
                                       <?php checked(in_array('night', $time_limits)); ?> class="ml-2">
                                <label for="time_limit_night">الليل (9:00 مساءً - 6:00 صباحاً)</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notifications -->
                    <div>
                        <h3 class="font-bold text-lg mb-3">الإشعارات</h3>
                        
                        <div class="flex items-center">
                            <input type="checkbox" name="notifications_enabled" id="notifications_enabled" value="1" 
                                   <?php checked($notifications_enabled, 1); ?> class="ml-2">
                            <label for="notifications_enabled">تفعيل إشعارات نشاط الطفل</label>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">ستتلقى إشعارات عن نشاط الطفل على الموقع</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Reset Points Section -->
        <div class="mt-8 pt-6 border-t">
            <h2 class="text-xl font-bold mb-4">إعادة تعيين النقاط والإنجازات</h2>
            <p class="text-sm text-gray-600 mb-4">يمكنك إعادة تعيين نقاط وإنجازات الطفل. هذا الإجراء لا يمكن التراجع عنه.</p>
            
            <div class="flex space-x-4 rtl:space-x-reverse">
                <a href="<?php echo esc_url(add_query_arg(array('action' => 'reset_points', 'child_id' => $child_id, '_wpnonce' => wp_create_nonce('reset_child_points')), admin_url('admin-post.php'))); ?>" 
                   class="inline-block bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors"
                   onclick="return confirm('هل أنت متأكد من رغبتك في إعادة تعيين نقاط الطفل؟ لا يمكن التراجع عن هذا الإجراء.');">
                    إعادة تعيين النقاط
                </a>
                
                <a href="<?php echo esc_url(add_query_arg(array('action' => 'reset_achievements', 'child_id' => $child_id, '_wpnonce' => wp_create_nonce('reset_child_achievements')), admin_url('admin-post.php'))); ?>" 
                   class="inline-block bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors"
                   onclick="return confirm('هل أنت متأكد من رغبتك في إعادة تعيين إنجازات الطفل؟ لا يمكن التراجع عن هذا الإجراء.');">
                    إعادة تعيين الإنجازات
                </a>
                
                <a href="<?php echo esc_url(add_query_arg(array('action' => 'reset_activity_log', 'child_id' => $child_id, '_wpnonce' => wp_create_nonce('reset_child_activity_log')), admin_url('admin-post.php'))); ?>" 
                   class="inline-block bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors"
                   onclick="return confirm('هل أنت متأكد من رغبتك في إعادة تعيين سجل نشاط الطفل؟ لا يمكن التراجع عن هذا الإجراء.');">
                    إعادة تعيين سجل النشاط
                </a>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div class="mt-8 text-center">
            <button type="submit" name="update_child_settings" class="bg-primary text-white px-8 py-3 rounded-full hover:bg-opacity-90 transition-colors">
                حفظ التغييرات
            </button>
        </div>
    </form>
</div>

<?php get_footer(); ?>
