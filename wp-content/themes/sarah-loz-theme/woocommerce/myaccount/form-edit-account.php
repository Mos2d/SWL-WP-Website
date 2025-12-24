<?php
/**
 * Edit account form
 *
 * This template overrides /woocommerce/templates/myaccount/form-edit-account.php
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_edit_account_form');

$user = wp_get_current_user();
$parent_name = get_user_meta($user->ID, 'parent_name', true);
$child_name = get_user_meta($user->ID, 'child_name', true);
$child_age = get_user_meta($user->ID, 'child_age', true);
?>

<form class="woocommerce-EditAccountForm edit-account space-y-6" action="" method="post" <?php do_action('woocommerce_edit_account_form_tag'); ?>>

    <?php do_action('woocommerce_edit_account_form_start'); ?>

    <div class="border-b border-gray-200 pb-6">
        <h2 class="text-xl font-bold text-dark mb-4"><?php esc_html_e('معلومات الحساب', 'woocommerce'); ?></h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="account_first_name" class="block text-gray-700 mb-1"><?php esc_html_e('الاسم الأول', 'woocommerce'); ?> &nbsp;<span class="required">*</span></label>
                <input type="text" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr($user->first_name); ?>" />
            </div>
            <div>
                <label for="account_last_name" class="block text-gray-700 mb-1"><?php esc_html_e('الاسم الأخير', 'woocommerce'); ?> &nbsp;<span class="required">*</span></label>
                <input type="text" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr($user->last_name); ?>" />
            </div>
        </div>

        <div class="mt-4">
            <label for="account_display_name" class="block text-gray-700 mb-1"><?php esc_html_e('اسم العرض', 'woocommerce'); ?> &nbsp;<span class="required">*</span></label>
            <input type="text" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" name="account_display_name" id="account_display_name" value="<?php echo esc_attr($user->display_name); ?>" />
            <span class="text-sm text-gray-500 mt-1 block"><em><?php esc_html_e('هذا هو الاسم الذي سيتم عرضه في حسابك وفي التعليقات', 'woocommerce'); ?></em></span>
        </div>

        <div class="mt-4">
            <label for="account_email" class="block text-gray-700 mb-1"><?php esc_html_e('البريد الإلكتروني', 'woocommerce'); ?> &nbsp;<span class="required">*</span></label>
            <input type="email" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr($user->user_email); ?>" />
        </div>
    </div>

    <!-- Custom Parent/Child Information -->
    <div class="border-b border-gray-200 pb-6">
        <h2 class="text-xl font-bold text-dark mb-4"><?php esc_html_e('معلومات ولي الأمر والطفل', 'woocommerce'); ?></h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="parent_name" class="block text-gray-700 mb-1"><?php esc_html_e('اسم ولي الأمر', 'woocommerce'); ?> &nbsp;<span class="required">*</span></label>
                <input type="text" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" name="parent_name" id="parent_name" value="<?php echo esc_attr($parent_name); ?>" required />
            </div>
            <div>
                <label for="child_name" class="block text-gray-700 mb-1"><?php esc_html_e('اسم الطفل', 'woocommerce'); ?> &nbsp;<span class="required">*</span></label>
                <input type="text" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" name="child_name" id="child_name" value="<?php echo esc_attr($child_name); ?>" required />
            </div>
        </div>

        <div class="mt-4">
            <label for="child_age" class="block text-gray-700 mb-1"><?php esc_html_e('عمر الطفل', 'woocommerce'); ?> &nbsp;<span class="required">*</span></label>
            <select name="child_age" id="child_age" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                <option value=""><?php esc_html_e('اختر العمر', 'woocommerce'); ?></option>
                <?php for ($i = 3; $i <= 9; $i++) : ?>
                    <option value="<?php echo $i; ?>" <?php selected($child_age, $i); ?>>
                        <?php echo $i; ?> <?php esc_html_e('سنوات', 'woocommerce'); ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
    </div>

    <!-- Password Change -->
    <div>
        <h2 class="text-xl font-bold text-dark mb-4"><?php esc_html_e('تغيير كلمة المرور', 'woocommerce'); ?></h2>
        <p class="text-sm text-gray-500 mb-4"><?php esc_html_e('اترك الحقول فارغة إذا كنت لا تريد تغيير كلمة المرور', 'woocommerce'); ?></p>

        <div class="mt-4">
            <label for="password_current" class="block text-gray-700 mb-1"><?php esc_html_e('كلمة المرور الحالية (اتركها فارغة للاحتفاظ بنفس كلمة المرور)', 'woocommerce'); ?></label>
            <input type="password" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" name="password_current" id="password_current" autocomplete="off" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
            <div>
                <label for="password_1" class="block text-gray-700 mb-1"><?php esc_html_e('كلمة المرور الجديدة (اتركها فارغة للاحتفاظ بنفس كلمة المرور)', 'woocommerce'); ?></label>
                <input type="password" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" name="password_1" id="password_1" autocomplete="off" />
            </div>
            <div>
                <label for="password_2" class="block text-gray-700 mb-1"><?php esc_html_e('تأكيد كلمة المرور الجديدة', 'woocommerce'); ?></label>
                <input type="password" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" name="password_2" id="password_2" autocomplete="off" />
            </div>
        </div>
    </div>

    <?php do_action('woocommerce_edit_account_form'); ?>

    <div class="mt-6">
        <?php wp_nonce_field('save_account_details', 'save-account-details-nonce'); ?>
        <button type="submit" class="bg-primary text-white px-8 py-3 rounded-full hover:bg-opacity-90 transition-colors" name="save_account_details" value="<?php esc_attr_e('حفظ التغييرات', 'woocommerce'); ?>"><?php esc_html_e('حفظ التغييرات', 'woocommerce'); ?></button>
        <input type="hidden" name="action" value="save_account_details" />
    </div>

    <?php do_action('woocommerce_edit_account_form_end'); ?>
</form>

<?php do_action('woocommerce_after_edit_account_form'); ?> 