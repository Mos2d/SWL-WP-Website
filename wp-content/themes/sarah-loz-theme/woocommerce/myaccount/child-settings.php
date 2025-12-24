<?php
/**
 * Child Settings Template
 *
 * This template displays child settings for parents in the WooCommerce account area.
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
    $child_email = $child->user_email;
    $child_username = $child->user_login;
    
    // Get child settings
    $daily_time_limit = get_user_meta($child_id, 'daily_time_limit', true) ?: 60; // Default 60 minutes
    $content_restrictions = get_user_meta($child_id, 'content_restrictions', true) ?: array();
    $notification_settings = get_user_meta($child_id, 'notification_settings', true) ?: array(
        'login' => true,
        'achievement' => true,
        'daily_report' => true
    );
    
    // Handle form submission
    $message = '';
    $error = '';
    
    if (isset($_POST['update_child_settings']) && wp_verify_nonce($_POST['child_settings_nonce'], 'update_child_settings')) {
        // Update child name
        if (isset($_POST['child_name']) && !empty($_POST['child_name'])) {
            update_user_meta($child_id, 'child_name', sanitize_text_field($_POST['child_name']));
            $child_name = sanitize_text_field($_POST['child_name']);
        }
        
        // Update child age
        if (isset($_POST['child_age']) && !empty($_POST['child_age'])) {
            update_user_meta($child_id, 'child_age', intval($_POST['child_age']));
            $child_age = intval($_POST['child_age']);
        }
        
        // Update daily time limit
        if (isset($_POST['daily_time_limit'])) {
            update_user_meta($child_id, 'daily_time_limit', intval($_POST['daily_time_limit']));
            $daily_time_limit = intval($_POST['daily_time_limit']);
        }
        
        // Update content restrictions
        $content_restrictions = array();
        if (isset($_POST['content_restrictions']) && is_array($_POST['content_restrictions'])) {
            $content_restrictions = array_map('sanitize_text_field', $_POST['content_restrictions']);
        }
        update_user_meta($child_id, 'content_restrictions', $content_restrictions);
        
        // Update notification settings
        $notification_settings = array(
            'login' => isset($_POST['notify_login']),
            'achievement' => isset($_POST['notify_achievement']),
            'daily_report' => isset($_POST['notify_daily_report'])
        );
        update_user_meta($child_id, 'notification_settings', $notification_settings);
        
        $message = __('تم تحديث إعدادات الطفل بنجاح.', 'sarah-loz');
    }
    ?>
    
    <div class="child-settings">
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
        
        <!-- Settings Header -->
        <div class="settings-header bg-white rounded-lg shadow-lg p-6 mb-6">
            <h1 class="text-2xl font-bold text-right"><?php echo esc_html__('إعدادات', 'sarah-loz'); ?> <?php echo esc_html($child_name); ?></h1>
            
            <?php if ($message) : ?>
                <div class="woocommerce-message bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mt-4" role="alert">
                    <?php echo esc_html($message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error) : ?>
                <div class="woocommerce-error bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
                    <?php echo esc_html($error); ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Settings Form -->
        <div class="settings-form bg-white rounded-lg shadow-lg p-6">
            <form method="post" class="woocommerce-EditAccountForm edit-account">
                <?php wp_nonce_field('update_child_settings', 'child_settings_nonce'); ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="settings-section">
                        <h2 class="text-xl font-bold mb-4 text-right"><?php echo esc_html__('المعلومات الأساسية', 'sarah-loz'); ?></h2>
                        
                        <div class="form-row mb-4">
                            <label for="child_name" class="block text-right mb-2 font-medium"><?php echo esc_html__('اسم الطفل', 'sarah-loz'); ?></label>
                            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg" name="child_name" id="child_name" value="<?php echo esc_attr($child_name); ?>" />
                        </div>
                        
                        <div class="form-row mb-4">
                            <label for="child_age" class="block text-right mb-2 font-medium"><?php echo esc_html__('عمر الطفل', 'sarah-loz'); ?></label>
                            <select name="child_age" id="child_age" class="w-full p-2 border border-gray-300 rounded-lg">
                                <?php for ($i = 5; $i <= 15; $i++) : ?>
                                    <option value="<?php echo $i; ?>" <?php selected($child_age, $i); ?>><?php echo $i; ?> <?php echo esc_html__('سنة', 'sarah-loz'); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="form-row mb-4">
                            <label for="child_username" class="block text-right mb-2 font-medium"><?php echo esc_html__('اسم المستخدم', 'sarah-loz'); ?></label>
                            <input type="text" class="w-full p-2 border border-gray-300 rounded-lg bg-gray-100" id="child_username" value="<?php echo esc_attr($child_username); ?>" readonly />
                            <p class="text-sm text-gray-600 text-right mt-1"><?php echo esc_html__('لا يمكن تغيير اسم المستخدم.', 'sarah-loz'); ?></p>
                        </div>
                        
                        <div class="form-row mb-4">
                            <label for="child_email" class="block text-right mb-2 font-medium"><?php echo esc_html__('البريد الإلكتروني', 'sarah-loz'); ?></label>
                            <input type="email" class="w-full p-2 border border-gray-300 rounded-lg bg-gray-100" id="child_email" value="<?php echo esc_attr($child_email); ?>" readonly />
                            <p class="text-sm text-gray-600 text-right mt-1"><?php echo esc_html__('لا يمكن تغيير البريد الإلكتروني من هنا.', 'sarah-loz'); ?></p>
                        </div>
                    </div>
                    
                    <!-- Usage Settings -->
                    <div class="settings-section">
                        <h2 class="text-xl font-bold mb-4 text-right"><?php echo esc_html__('إعدادات الاستخدام', 'sarah-loz'); ?></h2>
                        
                        <div class="form-row mb-4">
                            <label for="daily_time_limit" class="block text-right mb-2 font-medium"><?php echo esc_html__('الحد اليومي للاستخدام (بالدقائق)', 'sarah-loz'); ?></label>
                            <input type="number" class="w-full p-2 border border-gray-300 rounded-lg" name="daily_time_limit" id="daily_time_limit" value="<?php echo esc_attr($daily_time_limit); ?>" min="0" max="240" />
                            <p class="text-sm text-gray-600 text-right mt-1"><?php echo esc_html__('0 = غير محدود', 'sarah-loz'); ?></p>
                        </div>
                        
                        <div class="form-row mb-4">
                            <label class="block text-right mb-2 font-medium"><?php echo esc_html__('قيود المحتوى', 'sarah-loz'); ?></label>
                            
                            <div class="space-y-2">
                                <div class="flex items-center justify-end">
                                    <label for="restrict_games" class="ml-2"><?php echo esc_html__('الألعاب', 'sarah-loz'); ?></label>
                                    <input type="checkbox" name="content_restrictions[]" id="restrict_games" value="games" <?php checked(in_array('games', $content_restrictions)); ?> />
                                </div>
                                
                                <div class="flex items-center justify-end">
                                    <label for="restrict_videos" class="ml-2"><?php echo esc_html__('الفيديوهات', 'sarah-loz'); ?></label>
                                    <input type="checkbox" name="content_restrictions[]" id="restrict_videos" value="videos" <?php checked(in_array('videos', $content_restrictions)); ?> />
                                </div>
                                
                                <div class="flex items-center justify-end">
                                    <label for="restrict_activities" class="ml-2"><?php echo esc_html__('الأنشطة', 'sarah-loz'); ?></label>
                                    <input type="checkbox" name="content_restrictions[]" id="restrict_activities" value="activities" <?php checked(in_array('activities', $content_restrictions)); ?> />
                                </div>
                                
                                <div class="flex items-center justify-end">
                                    <label for="restrict_practice" class="ml-2"><?php echo esc_html__('التمارين', 'sarah-loz'); ?></label>
                                    <input type="checkbox" name="content_restrictions[]" id="restrict_practice" value="practice" <?php checked(in_array('practice', $content_restrictions)); ?> />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Notification Settings -->
                <div class="settings-section mt-6">
                    <h2 class="text-xl font-bold mb-4 text-right"><?php echo esc_html__('إعدادات الإشعارات', 'sarah-loz'); ?></h2>
                    
                    <div class="space-y-2">
                        <div class="flex items-center justify-end">
                            <label for="notify_login" class="ml-2"><?php echo esc_html__('إشعار عند تسجيل دخول الطفل', 'sarah-loz'); ?></label>
                            <input type="checkbox" name="notify_login" id="notify_login" <?php checked($notification_settings['login']); ?> />
                        </div>
                        
                        <div class="flex items-center justify-end">
                            <label for="notify_achievement" class="ml-2"><?php echo esc_html__('إشعار عند حصول الطفل على إنجاز', 'sarah-loz'); ?></label>
                            <input type="checkbox" name="notify_achievement" id="notify_achievement" <?php checked($notification_settings['achievement']); ?> />
                        </div>
                        
                        <div class="flex items-center justify-end">
                            <label for="notify_daily_report" class="ml-2"><?php echo esc_html__('تقرير يومي عن نشاط الطفل', 'sarah-loz'); ?></label>
                            <input type="checkbox" name="notify_daily_report" id="notify_daily_report" <?php checked($notification_settings['daily_report']); ?> />
                        </div>
                    </div>
                </div>
                
                <!-- Reset Options -->
                <div class="settings-section mt-6">
                    <h2 class="text-xl font-bold mb-4 text-right"><?php echo esc_html__('خيارات إعادة الضبط', 'sarah-loz'); ?></h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="reset-option bg-red-50 p-4 rounded-lg">
                            <h3 class="font-bold mb-2 text-right"><?php echo esc_html__('إعادة ضبط النقاط', 'sarah-loz'); ?></h3>
                            <p class="text-sm text-gray-600 mb-3 text-right"><?php echo esc_html__('سيؤدي هذا إلى إعادة تعيين جميع نقاط الطفل إلى الصفر.', 'sarah-loz'); ?></p>
                            <div class="text-center">
                                <a href="<?php echo esc_url(admin_url('admin-post.php?action=reset_points&child_id=' . $child_id . '&_wpnonce=' . wp_create_nonce('reset_points'))); ?>" class="inline-block bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition" onclick="return confirm('<?php echo esc_js(__('هل أنت متأكد من رغبتك في إعادة ضبط نقاط الطفل؟ لا يمكن التراجع عن هذا الإجراء.', 'sarah-loz')); ?>');">
                                    <?php echo esc_html__('إعادة ضبط النقاط', 'sarah-loz'); ?>
                                </a>
                            </div>
                        </div>
                        
                        <div class="reset-option bg-red-50 p-4 rounded-lg">
                            <h3 class="font-bold mb-2 text-right"><?php echo esc_html__('إعادة ضبط الإنجازات', 'sarah-loz'); ?></h3>
                            <p class="text-sm text-gray-600 mb-3 text-right"><?php echo esc_html__('سيؤدي هذا إلى إزالة جميع إنجازات الطفل.', 'sarah-loz'); ?></p>
                            <div class="text-center">
                                <a href="<?php echo esc_url(admin_url('admin-post.php?action=reset_achievements&child_id=' . $child_id . '&_wpnonce=' . wp_create_nonce('reset_achievements'))); ?>" class="inline-block bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition" onclick="return confirm('<?php echo esc_js(__('هل أنت متأكد من رغبتك في إعادة ضبط إنجازات الطفل؟ لا يمكن التراجع عن هذا الإجراء.', 'sarah-loz')); ?>');">
                                    <?php echo esc_html__('إعادة ضبط الإنجازات', 'sarah-loz'); ?>
                                </a>
                            </div>
                        </div>
                        
                        <div class="reset-option bg-red-50 p-4 rounded-lg">
                            <h3 class="font-bold mb-2 text-right"><?php echo esc_html__('إعادة ضبط سجل النشاط', 'sarah-loz'); ?></h3>
                            <p class="text-sm text-gray-600 mb-3 text-right"><?php echo esc_html__('سيؤدي هذا إلى مسح سجل نشاط الطفل بالكامل.', 'sarah-loz'); ?></p>
                            <div class="text-center">
                                <a href="<?php echo esc_url(admin_url('admin-post.php?action=reset_activity_log&child_id=' . $child_id . '&_wpnonce=' . wp_create_nonce('reset_activity_log'))); ?>" class="inline-block bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition" onclick="return confirm('<?php echo esc_js(__('هل أنت متأكد من رغبتك في إعادة ضبط سجل نشاط الطفل؟ لا يمكن التراجع عن هذا الإجراء.', 'sarah-loz')); ?>');">
                                    <?php echo esc_html__('إعادة ضبط سجل النشاط', 'sarah-loz'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions mt-6 text-center">
                    <button type="submit" name="update_child_settings" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-dark transition">
                        <?php echo esc_html__('حفظ التغييرات', 'sarah-loz'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
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
