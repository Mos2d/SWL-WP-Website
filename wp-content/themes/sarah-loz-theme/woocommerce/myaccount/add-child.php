<?php
/**
 * Add Child Template
 *
 * This template allows parents to add children from their dashboard.
 *
 * @package Sarah_Loz
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Check if user is a parent
$current_user = wp_get_current_user();
if (!in_array('parent', (array) $current_user->roles)) {
    echo '<p>غير مصرح لك بالوصول إلى هذه الصفحة.</p>';
    return;
}

$parent_id = $current_user->ID;
$parent_name = $current_user->display_name;

// Process form submission
$message = '';
$error = '';

if (isset($_POST['add_child']) && isset($_POST['add_child_nonce']) && wp_verify_nonce($_POST['add_child_nonce'], 'add_child_action')) {
    $child_username = sanitize_user($_POST['child_username']);
    $child_email = sanitize_email($_POST['child_email']);
    $child_password = $_POST['child_password'];
    $child_name = sanitize_text_field($_POST['child_name']);
    $child_age = intval($_POST['child_age']);
    
    // Validate inputs
    if (empty($child_username)) {
        $error = 'يرجى إدخال اسم مستخدم للطفل.';
    } elseif (empty($child_email)) {
        $error = 'يرجى إدخال بريد إلكتروني صالح للطفل.';
    } elseif (empty($child_password)) {
        $error = 'يرجى إدخال كلمة مرور للطفل.';
    } elseif (empty($child_name)) {
        $error = 'يرجى إدخال اسم الطفل.';
    } elseif (username_exists($child_username)) {
        $error = 'اسم المستخدم مستخدم بالفعل. يرجى اختيار اسم مستخدم آخر.';
    } elseif (email_exists($child_email)) {
        $error = 'البريد الإلكتروني مستخدم بالفعل. يرجى استخدام بريد إلكتروني آخر.';
    } else {
        // Create child user
        $child_id = wp_create_user($child_username, $child_password, $child_email);
        
        if (is_wp_error($child_id)) {
            $error = $child_id->get_error_message();
        } else {
            // Set user role to child
            $child_user = new WP_User($child_id);
            $child_user->set_role('child');
            
            // Add user meta
            update_user_meta($child_id, 'child_name', $child_name);
            update_user_meta($child_id, 'child_age', $child_age);
            update_user_meta($child_id, 'parent_id', $parent_id);
            
            // Add child to parent's children list
            $children = get_user_meta($parent_id, 'children', true);
            if (!is_array($children)) {
                $children = array();
            }
            $children[] = $child_id;
            update_user_meta($parent_id, 'children', $children);
            
            // Initialize child activity data
            update_user_meta($child_id, 'activity_points', 0);
            update_user_meta($child_id, 'achievement_points', 0);
            update_user_meta($child_id, 'total_points', 0);
            update_user_meta($child_id, 'login_streak', 0);
            update_user_meta($child_id, 'child_activity_log', array());
            update_user_meta($child_id, 'user_achievements', array());
            
            $message = 'تمت إضافة الطفل بنجاح.';
        }
    }
}
?>

<div class="add-child-form bg-white rounded-lg shadow-lg p-6">
    <h1 class="text-2xl font-bold mb-4 text-right"><?php echo esc_html__('إضافة طفل جديد', 'sarah-loz'); ?></h1>
    
    <?php if ($message) : ?>
        <div class="woocommerce-message bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?php echo esc_html($message); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error) : ?>
        <div class="woocommerce-error bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?php echo esc_html($error); ?>
        </div>
    <?php endif; ?>
    
    <form method="post" class="woocommerce-form">
        <?php wp_nonce_field('add_child_action', 'add_child_nonce'); ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="form-section">
                <h2 class="text-xl font-bold mb-4 text-right"><?php echo esc_html__('معلومات الحساب', 'sarah-loz'); ?></h2>
                
                <div class="form-row mb-4">
                    <label for="child_username" class="block text-right mb-2 font-medium"><?php echo esc_html__('اسم المستخدم', 'sarah-loz'); ?> <span class="required">*</span></label>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded-lg" name="child_username" id="child_username" required />
                    <p class="text-sm text-gray-600 text-right mt-1"><?php echo esc_html__('سيستخدم الطفل هذا لتسجيل الدخول.', 'sarah-loz'); ?></p>
                </div>
                
                <div class="form-row mb-4">
                    <label for="child_email" class="block text-right mb-2 font-medium"><?php echo esc_html__('البريد الإلكتروني', 'sarah-loz'); ?> <span class="required">*</span></label>
                    <input type="email" class="w-full p-2 border border-gray-300 rounded-lg" name="child_email" id="child_email" required />
                </div>
                
                <div class="form-row mb-4">
                    <label for="child_password" class="block text-right mb-2 font-medium"><?php echo esc_html__('كلمة المرور', 'sarah-loz'); ?> <span class="required">*</span></label>
                    <input type="password" class="w-full p-2 border border-gray-300 rounded-lg" name="child_password" id="child_password" required />
                </div>
            </div>
            
            <div class="form-section">
                <h2 class="text-xl font-bold mb-4 text-right"><?php echo esc_html__('معلومات الطفل', 'sarah-loz'); ?></h2>
                
                <div class="form-row mb-4">
                    <label for="child_name" class="block text-right mb-2 font-medium"><?php echo esc_html__('اسم الطفل', 'sarah-loz'); ?> <span class="required">*</span></label>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded-lg" name="child_name" id="child_name" required />
                </div>
                
                <div class="form-row mb-4">
                    <label for="child_age" class="block text-right mb-2 font-medium"><?php echo esc_html__('عمر الطفل', 'sarah-loz'); ?> <span class="required">*</span></label>
                    <select name="child_age" id="child_age" class="w-full p-2 border border-gray-300 rounded-lg" required>
                        <option value=""><?php echo esc_html__('اختر العمر', 'sarah-loz'); ?></option>
                        <?php for ($i = 5; $i <= 15; $i++) : ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo esc_html__('سنة', 'sarah-loz'); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-row mb-4">
                    <label for="parent_info" class="block text-right mb-2 font-medium"><?php echo esc_html__('معلومات الوالد/ة', 'sarah-loz'); ?></label>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded-lg bg-gray-100" id="parent_info" value="<?php echo esc_attr($parent_name); ?> (ID: <?php echo esc_attr($parent_id); ?>)" readonly />
                    <input type="hidden" name="parent_id" value="<?php echo esc_attr($parent_id); ?>" />
                </div>
            </div>
        </div>
        
        <div class="form-actions mt-6 text-center">
            <button type="submit" name="add_child" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-dark transition">
                <?php echo esc_html__('إضافة الطفل', 'sarah-loz'); ?>
            </button>
        </div>
    </form>
</div>
