<?php
/**
 * Downloads
 *
 * Shows downloads on the account page.
 *
 * This template overrides /woocommerce/templates/myaccount/downloads.php
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$downloads     = WC()->customer->get_downloadable_products();
$has_downloads = (bool) $downloads;

do_action('woocommerce_before_account_downloads', $has_downloads);

if ($has_downloads) : ?>

    <div class="downloads-container p-4 bg-white rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-4">المنتجات القابلة للتنزيل</h2>

        <?php do_action('woocommerce_before_available_downloads'); ?>

        <div class="woocommerce-downloads-table woocommerce-MyAccount-downloads shop_table shop_table_responsive my_account_orders account-orders-table w-full">
            <div class="hidden md:grid grid-cols-4 gap-4 border-b pb-4 mb-4 font-bold text-right">
                <div><?php esc_html_e('Product', 'woocommerce'); ?></div>
                <div><?php esc_html_e('Downloads remaining', 'woocommerce'); ?></div>
                <div><?php esc_html_e('Expires', 'woocommerce'); ?></div>
                <div><?php esc_html_e('Download', 'woocommerce'); ?></div>
            </div>

            <?php foreach ($downloads as $download) : ?>
                <div class="download-item mb-6 md:mb-4 border rounded-lg md:border-0 md:rounded-none overflow-hidden md:grid md:grid-cols-4 md:gap-4 md:items-center text-right">
                    
                    <div class="download-product flex justify-between items-center md:block p-3 md:p-0 bg-gray-50 md:bg-transparent">
                        <span class="md:hidden font-bold"><?php esc_html_e('Product', 'woocommerce'); ?>:</span>
                        <span class="product-link">
                            <a href="<?php echo esc_url(get_permalink($download['product_id'])); ?>">
                                <?php echo esc_html($download['product_name']); ?>
                            </a>
                        </span>
                    </div>
                    
                    <div class="download-remaining p-3 md:p-0 border-t md:border-0 flex justify-between items-center md:block">
                        <span class="md:hidden font-bold"><?php esc_html_e('Downloads remaining', 'woocommerce'); ?>:</span>
                        <span>
                            <?php
                            if ($download['downloads_remaining'] === 'unlimited') {
                                echo esc_html__('Unlimited', 'woocommerce');
                            } else {
                                echo esc_html($download['downloads_remaining']);
                            }
                            ?>
                        </span>
                    </div>
                    
                    <div class="download-expires p-3 md:p-0 border-t md:border-0 flex justify-between items-center md:block">
                        <span class="md:hidden font-bold"><?php esc_html_e('Expires', 'woocommerce'); ?>:</span>
                        <span>
                            <?php
                            if (!empty($download['access_expires'])) {
                                echo '<span class="download-expires">' . esc_html(date_i18n(get_option('date_format'), strtotime($download['access_expires']))) . '</span>';
                            } else {
                                echo esc_html__('Never', 'woocommerce');
                            }
                            ?>
                        </span>
                    </div>
                    
                    <div class="download-actions p-3 md:p-0 border-t md:border-0 flex justify-between items-center md:block">
                        <span class="md:hidden font-bold"><?php esc_html_e('Download', 'woocommerce'); ?>:</span>
                        <div class="flex justify-end md:justify-start">
                            <a href="<?php echo esc_url($download['download_url']); ?>" 
                               class="download-button px-3 py-1 text-sm rounded bg-primary text-white hover:bg-primary-dark transition duration-200">
                                <i class="fas fa-download ml-1"></i>
                                <?php esc_html_e('Download', 'woocommerce'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php do_action('woocommerce_after_available_downloads'); ?>
        
    </div>

<?php else : ?>
    <div class="no-downloads-container p-8 bg-white rounded-lg shadow text-center">
        <div class="mb-4">
            <i class="fas fa-download text-5xl text-gray-300"></i>
        </div>
        <p class="text-lg mb-4"><?php esc_html_e('No downloads available yet.', 'woocommerce'); ?></p>
        <a href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>" 
           class="woocommerce-button px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition">
            <?php esc_html_e('Browse products', 'woocommerce'); ?>
        </a>
    </div>
<?php endif; ?>

<?php do_action('woocommerce_after_account_downloads', $has_downloads); ?> 