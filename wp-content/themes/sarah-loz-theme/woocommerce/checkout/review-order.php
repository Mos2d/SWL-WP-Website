<?php
/**
 * Review order table
 */

defined('ABSPATH') || exit;
?>
<div class="woocommerce-checkout-review-order-table">
    <div class="mb-4">
        <div class="p-3 md:p-4 bg-light rounded-xl">
            <h3 class="text-base md:text-lg font-bold text-dark mb-3"><?php esc_html_e('Your Items', 'woocommerce'); ?></h3>
            
            <div class="space-y-3">
                <?php
                do_action('woocommerce_review_order_before_cart_contents');

                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                    $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

                    if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
                        ?>
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center border-b border-gray-200 pb-2 last:border-0 last:pb-0 gap-2">
                            <div class="flex-1 min-w-0">
                                <div class="text-sm md:text-base">
                                    <?php echo apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;'; ?>
                                    <strong class="product-quantity inline-block bg-secondary/10 px-2 rounded-full text-xs md:text-sm ml-2">× <?php echo apply_filters('woocommerce_checkout_cart_item_quantity', $cart_item['quantity'], $cart_item, $cart_item_key); ?></strong>
                                </div>
                            </div>
                            <div class="text-primary font-bold text-sm md:text-base">
                                <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
                            </div>
                        </div>
                        <?php
                    }
                }

                do_action('woocommerce_review_order_after_cart_contents');
                ?>
            </div>
        </div>
    </div>

    <div class="pt-3 md:pt-4 border-t border-gray-200">
        <div class="flex justify-between mb-2 text-sm md:text-base">
            <span><?php esc_html_e('Subtotal', 'woocommerce'); ?></span>
            <span class="text-primary font-bold"><?php wc_cart_totals_subtotal_html(); ?></span>
        </div>

        <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
            <div class="flex justify-between mb-2 text-sm md:text-base">
                <span class="flex items-center">
                    <i class="fas fa-ticket-alt mr-1 text-accent"></i>
                    <?php wc_cart_totals_coupon_label($coupon); ?>
                </span>
                <span class="text-green-600"><?php wc_cart_totals_coupon_html($coupon); ?></span>
            </div>
        <?php endforeach; ?>

        <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
            <div class="flex justify-between mb-2 text-sm md:text-base">
                <span><?php esc_html_e('Shipping', 'woocommerce'); ?></span>
                <span>
                    <?php 
                    $shipping_methods = WC()->session->get('chosen_shipping_methods');
                    $chosen_method = $shipping_methods[0] ?? '';
                    $packages = WC()->shipping()->get_packages();
                    
                    // Find chosen method
                    if (!empty($packages)) {
                        foreach ($packages[0]['rates'] as $id => $method) {
                            if ($id === $chosen_method) {
                                echo wc_price($method->cost);
                                break;
                            }
                        }
                    }
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <?php foreach (WC()->cart->get_fees() as $fee) : ?>
            <div class="flex justify-between mb-2 text-sm md:text-base">
                <span><?php echo esc_html($fee->name); ?></span>
                <span><?php wc_cart_totals_fee_html($fee); ?></span>
            </div>
        <?php endforeach; ?>

        <?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) : ?>
            <?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
                <?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : ?>
                    <div class="flex justify-between mb-2 text-sm md:text-base">
                        <span><?php echo esc_html($tax->label); ?></span>
                        <span><?php echo wp_kses_post($tax->formatted_amount); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="flex justify-between mb-2 text-sm md:text-base">
                    <span><?php echo esc_html(WC()->countries->tax_or_vat()); ?></span>
                    <span><?php wc_cart_totals_taxes_total_html(); ?></span>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php do_action('woocommerce_review_order_before_order_total'); ?>

        <div class="flex justify-between font-bold pt-3 border-t border-gray-200 mt-3">
            <span class="text-base md:text-lg"><?php esc_html_e('Total', 'woocommerce'); ?></span>
            <span class="text-lg md:text-xl text-primary"><?php wc_cart_totals_order_total_html(); ?></span>
        </div>

        <?php do_action('woocommerce_review_order_after_order_total'); ?>
    </div>
</div> 