<?php
/**
 * My Account page
 *
 * This template overrides /woocommerce/templates/myaccount/my-account.php
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined('ABSPATH') || exit;

/**
 * Get custom user meta
 */
$current_user = wp_get_current_user();
$parent_name = get_user_meta($current_user->ID, 'parent_name', true);
$child_name = get_user_meta($current_user->ID, 'child_name', true);
$child_age = get_user_meta($current_user->ID, 'child_age', true);

/**
 * My Account navigation.
 *
 * @since 2.6.0
 */
?>
<div class="woocommerce-account-wrapper">
    <?php do_action('woocommerce_account_navigation'); ?>

    <div class="woocommerce-MyAccount-content bg-white rounded-lg shadow-lg p-8">
        <?php
        /**
         * My Account content.
         *
         * @since 2.6.0
         */
        do_action('woocommerce_account_content');
        ?>

        <?php if (is_account_page() && !is_wc_endpoint_url()): ?>
            <!-- Profile Overview Card (Shown on main account page) -->
            <div class="flex items-center space-x-4 rtl:space-x-reverse mb-6">
                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center text-white text-2xl">
                    <?php echo substr($current_user->display_name, 0, 1); ?>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-dark"><?php echo esc_html($current_user->display_name); ?></h1>
                    <p class="text-gray-600"><?php echo esc_html($current_user->user_email); ?></p>
                </div>
            </div>

            <!-- Child Information Section -->
            <?php if ($child_name || $child_age): ?>
            <div class="border-t border-gray-200 pt-6 mt-6">
                <h2 class="text-xl font-bold text-dark mb-4">معلومات الطفل</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php if ($child_name): ?>
                    <div>
                        <span class="block text-gray-700 mb-1">اسم الطفل:</span>
                        <span class="font-medium"><?php echo esc_html($child_name); ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if ($child_age): ?>
                    <div>
                        <span class="block text-gray-700 mb-1">عمر الطفل:</span>
                        <span class="font-medium"><?php echo esc_html($child_age); ?> سنوات</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Child Progress Section -->
            <div class="border-t border-gray-200 pt-6 mt-6">
                <h2 class="text-xl font-bold text-dark mb-6">تقدم الطفل</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Games Progress -->
                    <div class="bg-light rounded-lg p-6">
                        <h3 class="font-bold text-lg mb-3">الألعاب المكتملة</h3>
                        <?php
                        $completed_games = get_user_meta($current_user->ID, 'completed_games', true);
                        $completed_games = !empty($completed_games) ? count($completed_games) : 0;
                        ?>
                        <div class="text-3xl font-bold text-primary"><?php echo $completed_games; ?></div>
                        <p class="text-gray-600">لعبة</p>
                    </div>

                    <!-- Activities Progress -->
                    <div class="bg-light rounded-lg p-6">
                        <h3 class="font-bold text-lg mb-3">الأنشطة المكتملة</h3>
                        <?php
                        $completed_activities = get_user_meta($current_user->ID, 'completed_activities', true);
                        $completed_activities = !empty($completed_activities) ? count($completed_activities) : 0;
                        ?>
                        <div class="text-3xl font-bold text-secondary"><?php echo $completed_activities; ?></div>
                        <p class="text-gray-600">نشاط</p>
                    </div>

                    <!-- Videos Watched -->
                    <div class="bg-light rounded-lg p-6">
                        <h3 class="font-bold text-lg mb-3">الفيديوهات المشاهدة</h3>
                        <?php
                        $watched_videos = get_user_meta($current_user->ID, 'watched_videos', true);
                        $watched_videos = !empty($watched_videos) ? count($watched_videos) : 0;
                        ?>
                        <div class="text-3xl font-bold text-accent"><?php echo $watched_videos; ?></div>
                        <p class="text-gray-600">فيديو</p>
                    </div>
                </div>

                <!-- Exam Results -->
                <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                    <a href="<?php echo home_url('/exam-results'); ?>" class="inline-flex items-center justify-center bg-purple-600 text-white px-8 py-3 rounded-full hover:bg-purple-700 transition-colors shadow-md text-lg font-bold">
                        <i class="fas fa-chart-bar ml-2"></i>
                        عرض سجل نتائج الاختبارات (My Exams)
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    /* Additional styling for mobile account page */
    @media (max-width: 768px) {
        .woocommerce-account-wrapper {
            width: 100%;
        }
        
        .woocommerce-MyAccount-content {
            margin-top: 1rem;
        }
        
        /* Fix RTL spacing on mobile */
        .rtl .woocommerce-MyAccount-content {
            padding: 1rem !important;
        }
        
        /* Ensure child-friendly styling on mobile */
        .bg-light {
            border-radius: 12px !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05) !important;
            transition: transform 0.2s ease !important;
        }
        
        .bg-light:hover {
            transform: translateY(-5px) !important;
        }
    }
</style>