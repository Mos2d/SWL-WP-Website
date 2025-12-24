<?php
/**
 * Template Name: Login Page
 */

get_header();

// Get WooCommerce login URL and account URL
$wc_account_url = wc_get_page_permalink('myaccount');
$wc_registration_url = add_query_arg('action', 'register', $wc_account_url);
?>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-dark mb-6 text-center">تسجيل الدخول</h1>
        
        <?php if (is_user_logged_in()) : ?>
            <div class="text-center">
                <p class="mb-4">أنت مسجل الدخول بالفعل</p>
                <a href="<?php echo esc_url($wc_account_url); ?>" class="inline-block bg-primary text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition-colors mb-3">
                    الذهاب إلى حسابي
                </a>
                <div>
                    <a href="<?php echo wp_logout_url(home_url()); ?>" class="inline-block text-gray-600 hover:text-primary transition-colors">
                        تسجيل الخروج
                    </a>
                </div>
            </div>
        <?php else : ?>
            <?php 
            $args = array(
                'redirect' => $wc_account_url, // Redirect to WooCommerce account page after login
                'form_id' => 'loginform',
                'label_username' => __('البريد الإلكتروني', 'sarah-loz'),
                'label_password' => __('كلمة المرور', 'sarah-loz'),
                'label_remember' => __('تذكرني', 'sarah-loz'),
                'label_log_in' => __('تسجيل الدخول', 'sarah-loz'),
                'remember' => true
            );
            ?>
            
            <div class="login-form-container">
                <?php wp_login_form($args); ?>
                
                <div class="flex flex-wrap justify-between mt-4 text-sm">
                    <a href="<?php echo esc_url($wc_registration_url); ?>" class="text-primary hover:text-opacity-80 transition-colors">
                        حساب جديد
                    </a>
                    <a href="<?php echo wp_lostpassword_url(); ?>" class="text-primary hover:text-opacity-80 transition-colors">
                        نسيت كلمة المرور؟
                    </a>
                </div>
            </div>

            <?php if (isset($_GET['login']) && $_GET['login'] == 'failed') : ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
                    <p>خطأ في تسجيل الدخول. يرجى التحقق من البريد الإلكتروني وكلمة المرور.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
    /* Custom styling for the WordPress login form */
    #loginform {
        @apply space-y-4;
    }

    #loginform label {
        @apply block text-gray-700 mb-1;
    }

    #loginform input[type="text"],
    #loginform input[type="password"] {
        @apply w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent;
    }

    #loginform .button-primary {
        @apply w-full bg-primary text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition-colors mt-4;
    }

    #loginform .forgetmenot {
        @apply flex items-center space-x-2 rtl:space-x-reverse;
    }

    #loginform .forgetmenot input[type="checkbox"] {
        @apply ml-2;
    }
</style>

<?php get_footer(); ?>
