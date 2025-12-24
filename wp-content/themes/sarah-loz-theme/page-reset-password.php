<?php
/**
 * Template Name: Password Reset
 */

get_header();
?>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-dark mb-6 text-center">استعادة كلمة المرور</h1>

        <?php if (isset($_GET['action']) && $_GET['action'] == 'rp') : ?>
            <!-- Reset Password Form -->
            <form method="post" action="<?php echo esc_url(site_url('wp-login.php?action=resetpass')); ?>" class="space-y-6">
                <div>
                    <label for="pass1" class="block text-gray-700 mb-1">كلمة المرور الجديدة *</label>
                    <input type="password" name="pass1" id="pass1" required 
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label for="pass2" class="block text-gray-700 mb-1">تأكيد كلمة المرور *</label>
                    <input type="password" name="pass2" id="pass2" required 
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <input type="hidden" name="rp_key" value="<?php echo esc_attr($_GET['key']); ?>">
                <input type="hidden" name="rp_login" value="<?php echo esc_attr($_GET['login']); ?>">

                <button type="submit" class="w-full bg-primary text-white px-6 py-3 rounded-full hover:bg-opacity-90 transition-colors">
                    تغيير كلمة المرور
                </button>
            </form>
        <?php else : ?>
            <!-- Lost Password Form -->
            <form method="post" action="<?php echo esc_url(wp_lostpassword_url()); ?>" class="space-y-6">
                <p class="text-gray-600 mb-4">
                    أدخل بريدك الإلكتروني وسنرسل لك رابطاً لإعادة تعيين كلمة المرور.
                </p>

                <div>
                    <label for="user_login" class="block text-gray-700 mb-1">البريد الإلكتروني *</label>
                    <input type="email" name="user_login" id="user_login" required 
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <?php wp_nonce_field('lost_password_nonce', 'lost_password_nonce_field'); ?>

                <button type="submit" class="w-full bg-primary text-white px-6 py-3 rounded-full hover:bg-opacity-90 transition-colors">
                    إرسال رابط الاستعادة
                </button>

                <p class="text-center text-sm text-gray-600">
                    <a href="<?php echo wp_login_url(); ?>" class="text-primary hover:text-opacity-80 transition-colors">
                        العودة إلى تسجيل الدخول
                    </a>
                </p>
            </form>

            <?php if (isset($_GET['password']) && $_GET['password'] == 'changed') : ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mt-4" role="alert">
                    <p>تم تغيير كلمة المرور بنجاح.</p>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['password']) && $_GET['password'] == 'reset') : ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mt-4" role="alert">
                    <p>تم إرسال رابط استعادة كلمة المرور إلى بريدك الإلكتروني.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
