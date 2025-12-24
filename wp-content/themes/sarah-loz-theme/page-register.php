<?php
/**
 * Template Name: Registration Page
 */

// If user is logged in, redirect to account
if (is_user_logged_in()) {
    wp_redirect(wc_get_page_permalink('myaccount'));
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    // Verify nonce
    if (!isset($_POST['register_nonce_field']) || !wp_verify_nonce($_POST['register_nonce_field'], 'register_nonce')) {
        wp_die('Security check failed');
    }

    // Get form data
    $username = sanitize_user($_POST['user_login']);
    $email = sanitize_email($_POST['user_email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $parent_name = sanitize_text_field($_POST['parent_name']);
    $child_name = sanitize_text_field($_POST['child_name']);
    $child_age = intval($_POST['child_age']);

    // Validate inputs
    $errors = new WP_Error();
    
    if (empty($username)) {
        $errors->add('username_empty', 'يرجى إدخال اسم المستخدم');
    }
    
    if (empty($email) || !is_email($email)) {
        $errors->add('email_invalid', 'يرجى إدخال بريد إلكتروني صحيح');
    }
    
    if (email_exists($email)) {
        $errors->add('email_exists', 'هذا البريد الإلكتروني مسجل بالفعل');
    }
    
    if (username_exists($username)) {
        $errors->add('username_exists', 'اسم المستخدم موجود بالفعل');
    }
    
    if (empty($password)) {
        $errors->add('password_empty', 'يرجى إدخال كلمة مرور');
    }
    
    if ($password !== $password_confirm) {
        $errors->add('password_mismatch', 'كلمتا المرور غير متطابقتين');
    }
    
    if (empty($parent_name)) {
        $errors->add('parent_name_empty', 'يرجى إدخال اسم ولي الأمر');
    }
    
    if (empty($child_name)) {
        $errors->add('child_name_empty', 'يرجى إدخال اسم الطفل');
    }
    
    if (empty($child_age)) {
        $errors->add('child_age_empty', 'يرجى اختيار عمر الطفل');
    }
    
    // Check if terms checkbox is checked
    if (!isset($_POST['terms']) || $_POST['terms'] !== 'on') {
        $errors->add('terms_unchecked', 'يجب الموافقة على الشروط والأحكام');
    }
    
    // If no errors, register user
    if (!$errors->has_errors()) {
        $user_id = wp_create_user($username, $password, $email);
        
        if (!is_wp_error($user_id)) {
            // Set user meta
            update_user_meta($user_id, 'parent_name', $parent_name);
            update_user_meta($user_id, 'child_name', $child_name);
            update_user_meta($user_id, 'child_age', $child_age);
            
            // Set user role
            $user = new WP_User($user_id);
            $user->set_role('customer');
            
            // Log user in
            wp_set_auth_cookie($user_id, true);
            wp_set_current_user($user_id);
            
            // Redirect to account page
            wp_redirect(wc_get_page_permalink('myaccount'));
            exit;
        } else {
            $errors = $user_id;
        }
    }
}

get_header();
?>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-lg mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-dark mb-6 text-center">إنشاء حساب جديد</h1>

        <?php if (isset($errors) && $errors->has_errors()) : ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors->get_error_messages() as $error) : ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" id="registerform" class="space-y-6">
            <div>
                <label for="user_login" class="block text-gray-700 mb-1">اسم المستخدم *</label>
                <input type="text" name="user_login" id="user_login" required 
                       value="<?php echo isset($_POST['user_login']) ? esc_attr($_POST['user_login']) : ''; ?>"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label for="user_email" class="block text-gray-700 mb-1">البريد الإلكتروني *</label>
                <input type="email" name="user_email" id="user_email" required 
                       value="<?php echo isset($_POST['user_email']) ? esc_attr($_POST['user_email']) : ''; ?>"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label for="parent_name" class="block text-gray-700 mb-1">اسم ولي الأمر *</label>
                <input type="text" name="parent_name" id="parent_name" required 
                       value="<?php echo isset($_POST['parent_name']) ? esc_attr($_POST['parent_name']) : ''; ?>"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label for="child_name" class="block text-gray-700 mb-1">اسم الطفل *</label>
                <input type="text" name="child_name" id="child_name" required 
                       value="<?php echo isset($_POST['child_name']) ? esc_attr($_POST['child_name']) : ''; ?>"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label for="child_age" class="block text-gray-700 mb-1">عمر الطفل *</label>
                <select name="child_age" id="child_age" required 
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">اختر العمر</option>
                    <?php for ($i = 3; $i <= 9; $i++) : ?>
                        <option value="<?php echo $i; ?>" <?php selected(isset($_POST['child_age']) ? $_POST['child_age'] : '', $i); ?>>
                            <?php echo $i; ?> سنوات
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div>
                <label for="password" class="block text-gray-700 mb-1">كلمة المرور *</label>
                <input type="password" name="password" id="password" required 
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label for="password_confirm" class="block text-gray-700 mb-1">تأكيد كلمة المرور *</label>
                <input type="password" name="password_confirm" id="password_confirm" required 
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                <input type="checkbox" name="terms" id="terms" required class="ml-2">
                <label for="terms" class="text-sm text-gray-600">
                    أوافق على <a href="<?php echo get_privacy_policy_url(); ?>" class="text-primary hover:text-opacity-80">سياسة الخصوصية</a> و
                    <a href="<?php echo home_url('/terms-of-use/'); ?>" class="text-primary hover:text-opacity-80">شروط الاستخدام</a>
                </label>
            </div>

            <?php wp_nonce_field('register_nonce', 'register_nonce_field'); ?>

            <button type="submit" name="register" class="w-full bg-primary text-white px-6 py-3 rounded-full hover:bg-opacity-90 transition-colors">
                إنشاء حساب
            </button>

            <p class="text-center text-sm text-gray-600">
                لديك حساب بالفعل؟ 
                <a href="<?php echo wc_get_page_permalink('myaccount'); ?>" class="text-primary hover:text-opacity-80 transition-colors">
                    تسجيل الدخول
                </a>
            </p>
        </form>
    </div>
</div>

<?php get_footer(); ?>
