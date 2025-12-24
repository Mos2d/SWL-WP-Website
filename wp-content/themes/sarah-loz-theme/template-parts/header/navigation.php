<?php
/**
 * Navigation Menu Template Part
 */
?>

<nav class="flex flex-wrap items-center justify-between">
    <!-- Logo -->
    <div class="flex items-center">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center">
            <div class="w-12 h-12 bg-light rounded-full flex items-center justify-center mr-2 shadow-md">
                <span class="text-2xl font-bold text-primary">س&ل</span>
            </div>
            <span class="text-light text-xl font-bold"><?php bloginfo('name'); ?></span>
        </a>
    </div>

    <!-- Main Navigation -->
    <?php
    wp_nav_menu(array(
        'theme_location' => 'primary',
        'container' => false,
        'menu_class' => 'flex flex-wrap items-center space-x-4 rtl:space-x-reverse',
        'fallback_cb' => false,
    ));
    ?>

    <!-- User Account & Cart -->
    <div class="flex items-center space-x-4 rtl:space-x-reverse">
        <?php if (class_exists('WooCommerce')) : ?>
            <a href="<?php echo wc_get_cart_url(); ?>" class="text-white hover:text-accent transition-colors relative">
                <i class="fas fa-shopping-cart text-xl"></i>
                <span class="cart-count absolute -top-2 -right-2 bg-accent text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">
                    <?php echo WC()->cart->get_cart_contents_count(); ?>
                </span>
            </a>
        <?php endif; ?>

        <?php if (is_user_logged_in()) : ?>
            <div class="relative group">
                <button class="flex items-center text-white hover:text-accent transition-colors">
                    <i class="fas fa-user-circle text-xl mr-2"></i>
                    <span><?php echo wp_get_current_user()->display_name; ?></span>
                </button>
                <div class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden group-hover:block">
                    <a href="<?php echo get_permalink(get_page_by_path('profile')); ?>" class="block px-4 py-2 text-gray-800 hover:bg-primary hover:text-white">
                        <i class="fas fa-user mr-2"></i>الملف الشخصي
                    </a>
                    <?php if (current_user_can('manage_options')) : ?>
                        <a href="<?php echo admin_url(); ?>" class="block px-4 py-2 text-gray-800 hover:bg-primary hover:text-white">
                            <i class="fas fa-cog mr-2"></i>لوحة التحكم
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo wp_logout_url(home_url()); ?>" class="block px-4 py-2 text-gray-800 hover:bg-primary hover:text-white">
                        <i class="fas fa-sign-out-alt mr-2"></i>تسجيل الخروج
                    </a>
                </div>
            </div>
        <?php else : ?>
            <a href="<?php echo wp_login_url(); ?>" class="text-white hover:text-accent transition-colors">
                <i class="fas fa-sign-in-alt mr-2"></i>تسجيل الدخول
            </a>
        <?php endif; ?>
    </div>
</nav>
