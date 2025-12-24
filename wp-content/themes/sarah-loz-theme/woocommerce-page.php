<?php
/**
 * Template Name: WooCommerce Page
 * Template Post Type: page
 *
 * A template specifically designed to handle WooCommerce pages like cart, checkout and account
 */

get_header();

?>
<main id="content" class="site-main woocommerce-page">
    <div class="container mx-auto px-4 py-10">
        <?php
        if (function_exists('is_woocommerce') && function_exists('is_account_page')) {
            if (is_cart()) {
                // Cart page content
                echo '<div class="cart-content-wrapper">';
                echo do_shortcode('[woocommerce_cart]');
                echo '</div>';
            } elseif (is_checkout()) {
                // Checkout page content
                echo '<div class="checkout-content-wrapper">';
                echo do_shortcode('[woocommerce_checkout]');
                echo '</div>';
            } elseif (is_account_page()) {
                // Account page content
                echo '<div class="account-content-wrapper">';
                
                // Check if it's the registration page
                if (isset($_GET['action']) && $_GET['action'] === 'register') {
                    // Show registration form
                    echo '<div class="woocommerce-account-register max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">';
                    echo '<h2 class="text-2xl font-bold text-dark mb-6 text-center">إنشاء حساب جديد</h2>';
                    
                    // Display registration errors
                    if (isset($_GET['registration_error'])) {
                        echo '<div class="woocommerce-error mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">';
                        echo urldecode($_GET['registration_error']);
                        echo '</div>';
                    }
                    
                    // Custom registration form - post to current page
                    echo '<form method="post" class="woocommerce-form woocommerce-form-register register" action="' . esc_url($_SERVER['REQUEST_URI']) . '">';
                    
                    // Add nonce for security
                    wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce');
                    
                    // Email
                    echo '<div class="woocommerce-form-row">';
                    echo '<label for="user_email">' . __('البريد الإلكتروني', 'sarah-loz') . ' <span class="required">*</span></label>';
                    echo '<input type="email" name="user_email" id="user_email" autocomplete="email" class="input" value="" required />';
                    echo '</div>';
                    
                    // Username
                    echo '<div class="woocommerce-form-row">';
                    echo '<label for="user_login">' . __('اسم المستخدم', 'sarah-loz') . ' <span class="required">*</span></label>';
                    echo '<input type="text" name="user_login" id="user_login" autocomplete="username" class="input" value="" required />';
                    echo '</div>';
                    
                    // Password
                    echo '<div class="woocommerce-form-row">';
                    echo '<label for="user_pass">' . __('كلمة المرور', 'sarah-loz') . ' <span class="required">*</span></label>';
                    echo '<input type="password" name="user_pass" id="user_pass" autocomplete="new-password" class="input" required />';
                    echo '</div>';
                    
                    // Parent name
                    echo '<div class="woocommerce-form-row">';
                    echo '<label for="parent_name">' . __('اسم ولي الأمر', 'sarah-loz') . ' <span class="required">*</span></label>';
                    echo '<input type="text" name="parent_name" id="parent_name" class="input" value="" required />';
                    echo '</div>';
                    
                    // Child name
                    echo '<div class="woocommerce-form-row">';
                    echo '<label for="child_name">' . __('اسم الطفل', 'sarah-loz') . ' <span class="required">*</span></label>';
                    echo '<input type="text" name="child_name" id="child_name" class="input" value="" required />';
                    echo '</div>';
                    
                    // Child age
                    echo '<div class="woocommerce-form-row">';
                    echo '<label for="child_age">' . __('عمر الطفل', 'sarah-loz') . ' <span class="required">*</span></label>';
                    echo '<select name="child_age" id="child_age" class="input" required>';
                    echo '<option value="">' . __('اختر العمر', 'sarah-loz') . '</option>';
                    for ($i = 1; $i <= 15; $i++) {
                        echo '<option value="' . $i . '">' . $i . '</option>';
                    }
                    echo '</select>';
                    echo '</div>';
                    
                    // Submit button
                    echo '<div class="woocommerce-form-row submit-row">';
                    echo '<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="' . __('إنشاء حساب', 'sarah-loz') . '" />';
                    echo '<input type="hidden" name="redirect_to" value="' . esc_url(wc_get_page_permalink('myaccount')) . '" />';
                    echo '</div>';
                    
                    echo '</form>';
                    
                    // Login link
                    echo '<div class="flex justify-center mt-5 text-sm">';
                    echo '<a href="' . esc_url(wc_get_page_permalink('myaccount')) . '" class="text-primary hover:text-opacity-80 transition-colors">العودة إلى تسجيل الدخول</a>';
                    echo '</div>';
                    
                    echo '</div>';
                } 
                // Check if it's the lost password page
                elseif (is_wc_endpoint_url('lost-password') || 
                        (isset($_GET['key']) && isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] === 'reset_password')) {
                    
                    // Show lost password form
                    echo '<div class="woocommerce-account-lost-password max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">';
                    
                    if (isset($_GET['key']) && isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] === 'reset_password') {
                        // Reset password form
                        echo '<h2 class="text-2xl font-bold text-dark mb-6 text-center">إعادة تعيين كلمة المرور</h2>';
                        
                        // Display errors if any
                        if (isset($_GET['error'])) {
                            echo '<div class="woocommerce-error mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">';
                            echo urldecode($_GET['error']);
                            echo '</div>';
                        }
                        
                        // Reset password form
                        echo '<form method="post" class="woocommerce-ResetPassword lost_reset_password">';
                        
                        echo '<p>' . esc_html__('أدخل كلمة المرور الجديدة أدناه.', 'woocommerce') . '</p>';
                        
                        // Password
                        echo '<div class="woocommerce-form-row">';
                        echo '<label for="password_1">' . esc_html__('كلمة المرور الجديدة', 'woocommerce') . ' <span class="required">*</span></label>';
                        echo '<input type="password" class="input" name="password_1" id="password_1" autocomplete="new-password" required />';
                        echo '</div>';
                        
                        // Confirm Password
                        echo '<div class="woocommerce-form-row">';
                        echo '<label for="password_2">' . esc_html__('تأكيد كلمة المرور الجديدة', 'woocommerce') . ' <span class="required">*</span></label>';
                        echo '<input type="password" class="input" name="password_2" id="password_2" autocomplete="new-password" required />';
                        echo '</div>';
                        
                        echo '<input type="hidden" name="reset_key" value="' . esc_attr($_GET['key']) . '" />';
                        echo '<input type="hidden" name="reset_login" value="' . esc_attr($_GET['id']) . '" />';
                        
                        // Submit button
                        echo '<div class="woocommerce-form-row mt-4">';
                        echo '<button type="submit" class="button button-primary button-large w-full bg-primary text-white px-6 py-3 rounded-full hover:bg-opacity-90 transition-colors">' . esc_html__('حفظ كلمة المرور الجديدة', 'woocommerce') . '</button>';
                        echo '</div>';
                        
                        echo '<input type="hidden" name="wc_reset_password" value="true" />';
                        wp_nonce_field('reset_password', 'woocommerce-reset-password-nonce');
                        
                        echo '</form>';
                        
                    } else {
                        // Lost password form
                        echo '<h2 class="text-2xl font-bold text-dark mb-6 text-center">استعادة كلمة المرور</h2>';
                        
                        // Display errors if any
                        if (isset($_GET['reset-link-sent'])) {
                            echo '<div class="woocommerce-message mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">';
                            echo esc_html__('تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني.', 'woocommerce');
                            echo '</div>';
                        }
                        
                        // Lost password form
                        echo '<form method="post" class="woocommerce-ResetPassword lost_reset_password">';
                        
                        echo '<p>' . esc_html__('الرجاء إدخال اسم المستخدم أو البريد الإلكتروني. ستتلقى رابطًا لإنشاء كلمة مرور جديدة عبر البريد الإلكتروني.', 'woocommerce') . '</p>';
                        
                        // Username or Email
                        echo '<div class="woocommerce-form-row">';
                        echo '<label for="user_login">' . esc_html__('اسم المستخدم أو البريد الإلكتروني', 'woocommerce') . ' <span class="required">*</span></label>';
                        echo '<input class="input" type="text" name="user_login" id="user_login" autocomplete="username" required />';
                        echo '</div>';
                        
                        // Submit button
                        echo '<div class="woocommerce-form-row mt-4">';
                        echo '<button type="submit" class="button button-primary button-large w-full bg-primary text-white px-6 py-3 rounded-full hover:bg-opacity-90 transition-colors">' . esc_html__('إعادة تعيين كلمة المرور', 'woocommerce') . '</button>';
                        echo '</div>';
                        
                        echo '<input type="hidden" name="wc_reset_password" value="true" />';
                        wp_nonce_field('lost_password', 'woocommerce-lost-password-nonce');
                        
                        echo '</form>';
                    }
                    
                    // Back to login link
                    echo '<div class="flex justify-center mt-5 text-sm">';
                    echo '<a href="' . esc_url(wc_get_page_permalink('myaccount')) . '" class="text-primary hover:text-opacity-80 transition-colors">العودة إلى تسجيل الدخول</a>';
                    echo '</div>';
                    
                    echo '</div>';
                } 
                else {
                    // First, check if user is logged in
                    if (!is_user_logged_in()) {
                        // If not logged in, show the login form
                        echo '<div class="woocommerce-account-login max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">';
                        echo '<h2 class="text-2xl font-bold text-dark mb-6 text-center">تسجيل الدخول</h2>';
                        
                        // Custom login form to ensure better styling
                        echo '<form name="loginform" id="loginform" action="' . esc_url(site_url('wp-login.php', 'login_post')) . '" method="post">';
                        
                        echo '<div class="login-username">';
                        echo '<label for="user_login">' . __('البريد الإلكتروني', 'sarah-loz') . '</label>';
                        echo '<input type="text" name="log" id="user_login" autocomplete="username" class="input" value="" size="20" />';
                        echo '</div>';
                        
                        echo '<div class="login-password">';
                        echo '<label for="user_pass">' . __('كلمة المرور', 'sarah-loz') . '</label>';
                        echo '<input type="password" name="pwd" id="user_pass" autocomplete="current-password" spellcheck="false" class="input" value="" size="20" />';
                        echo '</div>';
                        
                        echo '<div class="login-remember">';
                        echo '<label><input name="rememberme" type="checkbox" id="rememberme" value="forever" /> ' . __('تذكرني', 'sarah-loz') . '</label>';
                        echo '</div>';
                        
                        echo '<div class="login-submit">';
                        echo '<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="' . __('تسجيل الدخول', 'sarah-loz') . '" />';
                        echo '<input type="hidden" name="redirect_to" value="' . esc_url(wc_get_page_permalink('myaccount')) . '" />';
                        echo '</div>';
                        
                        echo '</form>';
                        
                        echo '<div class="flex flex-wrap justify-between mt-5 text-sm">';
                        echo '<a href="' . add_query_arg('action', 'register', wc_get_page_permalink('myaccount')) . '" class="text-primary hover:text-opacity-80 transition-colors">حساب جديد</a>';
                        echo '<a href="' . wp_lostpassword_url() . '" class="text-primary hover:text-opacity-80 transition-colors">نسيت كلمة المرور؟</a>';
                        echo '</div>';
                        
                        echo '</div>';
                    } else {
                        // If logged in, show the account content
                        echo do_shortcode('[woocommerce_my_account]');
                    }
                }
                
                echo '</div>';
                
                // Add additional styles for login form
                ?>
                <style>
                    /* Custom styling for the WordPress login form */
                    #loginform {
                        @apply space-y-4;
                    }

                    #loginform label {
                        @apply block text-gray-700 mb-1 font-medium;
                    }

                    #loginform input[type="text"],
                    #loginform input[type="password"] {
                        @apply w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent;
                    }

                    #loginform .button-primary {
                        @apply w-full bg-primary text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition-colors mt-4 cursor-pointer font-medium;
                    }

                    #loginform .forgetmenot {
                        @apply flex items-center space-x-2 rtl:space-x-reverse my-3;
                    }

                    #loginform .forgetmenot input[type="checkbox"] {
                        @apply ml-2 h-4 w-4 text-primary focus:ring-primary;
                    }
                    
                    /* Individual form elements */
                    .login-username,
                    .login-password {
                        @apply mb-4;
                    }
                    
                    /* Add right-to-left support */
                    body.rtl .login-username,
                    body.rtl .login-password {
                        text-align: right;
                    }
                    
                    /* Login form links */
                    .woocommerce-account-login a {
                        @apply text-primary font-medium;
                    }
                    
                    .woocommerce-account-login a:hover {
                        @apply text-primary/80;
                    }
                    
                    /* Add a small animation to the button on hover */
                    #loginform .button-primary:hover {
                        @apply transform -translate-y-0.5 shadow-md;
                    }
                </style>
                <?php
            } else {
                // Fallback to regular page content
                while (have_posts()) : the_post();
                    the_content();
                endwhile;
            }
        } else {
            // If WooCommerce functions are not available, display regular content
            while (have_posts()) : the_post();
                the_content();
            endwhile;
        }
        ?>
    </div>
</main>

<?php
get_footer();
?> 