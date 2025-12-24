<?php
/**
 * Login Form
 *
 * This template overrides /woocommerce/templates/myaccount/form-login.php
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

do_action('woocommerce_before_customer_login_form'); ?>

<?php if ('yes' === get_option('woocommerce_enable_myaccount_registration')) : ?>

<div class="u-columns woocommerce-account mx-auto max-w-5xl" id="customer_login">

	<div class="u-column1 mb-8 md:mb-0">

<?php endif; ?>

		<h2 class="text-2xl font-bold text-dark mb-6 text-center"><?php esc_html_e('تسجيل الدخول', 'woocommerce'); ?></h2>

		<form class="woocommerce-form woocommerce-form-login login bg-white rounded-lg shadow-lg p-8 max-w-md mx-auto" method="post">

			<?php do_action('woocommerce_login_form_start'); ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="username"><?php esc_html_e('البريد الإلكتروني', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo (!empty($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>" />
			</p>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="password"><?php esc_html_e('كلمة المرور', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
				<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" />
			</p>

			<?php do_action('woocommerce_login_form'); ?>

			<div class="form-row flex items-center justify-between mb-4">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e('تذكرني', 'woocommerce'); ?></span>
				</label>
				<p class="woocommerce-LostPassword lost_password">
					<a href="<?php echo esc_url(wp_lostpassword_url()); ?>"><?php esc_html_e('نسيت كلمة المرور؟', 'woocommerce'); ?></a>
				</p>
			</div>

			<p class="form-row">
				<?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
				<button type="submit" class="woocommerce-button button woocommerce-form-login__submit w-full bg-primary text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition-colors" name="login" value="<?php esc_attr_e('تسجيل الدخول', 'woocommerce'); ?>"><?php esc_html_e('تسجيل الدخول', 'woocommerce'); ?></button>
			</p>

			<?php do_action('woocommerce_login_form_end'); ?>

		</form>

<?php if ('yes' === get_option('woocommerce_enable_myaccount_registration')) : ?>

	</div>

	<div class="u-column2">

		<h2 class="text-2xl font-bold text-dark mb-6 text-center"><?php esc_html_e('إنشاء حساب جديد', 'woocommerce'); ?></h2>

		<form method="post" class="woocommerce-form woocommerce-form-register register bg-white rounded-lg shadow-lg p-8 max-w-md mx-auto" <?php do_action('woocommerce_register_form_tag'); ?> >

			<?php do_action('woocommerce_register_form_start'); ?>

			<?php if ('no' === get_option('woocommerce_registration_generate_username')) : ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_username"><?php esc_html_e('اسم المستخدم', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo (!empty($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>" />
				</p>

			<?php endif; ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_email"><?php esc_html_e('البريد الإلكتروني', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
				<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo (!empty($_POST['email'])) ? esc_attr(wp_unslash($_POST['email'])) : ''; ?>" />
			</p>

			<?php if ('no' === get_option('woocommerce_registration_generate_password')) : ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_password"><?php esc_html_e('كلمة المرور', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
				</p>

			<?php else : ?>

				<p><?php esc_html_e('سيتم إرسال كلمة مرور إلى بريدك الإلكتروني.', 'woocommerce'); ?></p>

			<?php endif; ?>

			<!-- Custom fields for parent name, child name, and child age -->
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="parent_name"><?php esc_html_e('اسم ولي الأمر', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="parent_name" id="parent_name" value="<?php echo (!empty($_POST['parent_name'])) ? esc_attr(wp_unslash($_POST['parent_name'])) : ''; ?>" />
			</p>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="child_name"><?php esc_html_e('اسم الطفل', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="child_name" id="child_name" value="<?php echo (!empty($_POST['child_name'])) ? esc_attr(wp_unslash($_POST['child_name'])) : ''; ?>" />
			</p>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="child_age"><?php esc_html_e('عمر الطفل', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
				<select name="child_age" id="child_age" class="woocommerce-Input woocommerce-Input--select input-select">
					<option value=""><?php esc_html_e('اختر العمر', 'woocommerce'); ?></option>
					<?php for ($i = 1; $i <= 15; $i++) : ?>
						<option value="<?php echo $i; ?>" <?php selected((!empty($_POST['child_age']) ? $_POST['child_age'] : ''), $i); ?>><?php echo $i; ?></option>
					<?php endfor; ?>
				</select>
			</p>

			<?php do_action('woocommerce_register_form'); ?>

			<p class="woocommerce-form-row form-row">
				<?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
				<button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit w-full bg-primary text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition-colors" name="register" value="<?php esc_attr_e('إنشاء حساب', 'woocommerce'); ?>"><?php esc_html_e('إنشاء حساب', 'woocommerce'); ?></button>
			</p>

			<?php do_action('woocommerce_register_form_end'); ?>

		</form>

	</div>

</div>
<?php endif; ?>

<?php do_action('woocommerce_after_customer_login_form'); ?> 