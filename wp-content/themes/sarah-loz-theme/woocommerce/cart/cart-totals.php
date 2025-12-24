<?php
/**
 * Cart totals
 */

defined('ABSPATH') || exit;

?>
<div class="cart_totals <?php echo (WC()->customer->has_calculated_shipping()) ? 'calculated_shipping' : ''; ?>">

    <?php do_action('woocommerce_before_cart_totals'); ?>

    <table class="shop_table w-full text-sm md:text-base">
        <tbody>
            <tr class="cart-subtotal border-b border-gray-100">
                <th class="py-2 md:py-3 text-right"><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
                <td class="py-2 md:py-3 text-primary font-bold text-right"><?php wc_cart_totals_subtotal_html(); ?></td>
            </tr>

            <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
                <tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?> border-b border-gray-100">
                    <th class="py-2 md:py-3 text-right">
                        <div class="flex items-center justify-end">
                            <i class="fas fa-ticket-alt mr-1 md:mr-2 text-accent text-xs md:text-sm"></i>
                            <?php wc_cart_totals_coupon_label($coupon); ?>
                        </div>
                    </th>
                    <td class="py-2 md:py-3 text-green-600 font-bold text-right"><?php wc_cart_totals_coupon_html($coupon); ?></td>
                </tr>
            <?php endforeach; ?>

            <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
                <?php do_action('woocommerce_cart_totals_before_shipping'); ?>

                <?php wc_cart_totals_shipping_html(); ?>

                <?php do_action('woocommerce_cart_totals_after_shipping'); ?>
            <?php elseif (WC()->cart->needs_shipping() && 'yes' === get_option('woocommerce_enable_shipping_calc')) : ?>
                <tr class="shipping border-b border-gray-100">
                    <th class="py-2 md:py-3 text-right"><?php esc_html_e('Shipping', 'woocommerce'); ?></th>
                    <td class="py-2 md:py-3 text-right">
                        <?php woocommerce_shipping_calculator(); ?>
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach (WC()->cart->get_fees() as $fee) : ?>
                <tr class="fee border-b border-gray-100">
                    <th class="py-2 md:py-3 text-right"><?php echo esc_html($fee->name); ?></th>
                    <td class="py-2 md:py-3 text-right"><?php wc_cart_totals_fee_html($fee); ?></td>
                </tr>
            <?php endforeach; ?>

            <?php
            if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) {
                $taxable_address = WC()->customer->get_taxable_address();
                $estimated_text = '';

                if (WC()->customer->is_customer_outside_base() && !WC()->customer->has_calculated_shipping()) {
                    /* translators: %s location. */
                    $estimated_text = sprintf(' <small>' . esc_html__('(estimated for %s)', 'woocommerce') . '</small>', WC()->countries->estimated_for_prefix($taxable_address[0]) . WC()->countries->countries[$taxable_address[0]]);
                }

                if ('itemized' === get_option('woocommerce_tax_total_display')) {
                    foreach (WC()->cart->get_tax_totals() as $code => $tax) {
                        ?>
                        <tr class="tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?> border-b border-gray-100">
                            <th class="py-2 md:py-3 text-right"><?php echo esc_html($tax->label) . $estimated_text; ?></th>
                            <td class="py-2 md:py-3 text-right" data-title="<?php echo esc_attr($tax->label); ?>"><?php echo wp_kses_post($tax->formatted_amount); ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr class="tax-total border-b border-gray-100">
                        <th class="py-2 md:py-3 text-right"><?php echo esc_html(WC()->countries->tax_or_vat()) . $estimated_text; ?></th>
                        <td class="py-2 md:py-3 text-right" data-title="<?php echo esc_attr(WC()->countries->tax_or_vat()); ?>"><?php wc_cart_totals_taxes_total_html(); ?></td>
                    </tr>
                    <?php
                }
            }
            ?>

            <?php do_action('woocommerce_cart_totals_before_order_total'); ?>

            <tr class="order-total">
                <th class="py-2 md:py-3 text-base md:text-lg font-bold text-right"><?php esc_html_e('Total', 'woocommerce'); ?></th>
                <td class="py-2 md:py-3 text-lg md:text-xl font-bold text-primary text-right" data-title="<?php esc_attr_e('Total', 'woocommerce'); ?>"><?php wc_cart_totals_order_total_html(); ?></td>
            </tr>

            <?php do_action('woocommerce_cart_totals_after_order_total'); ?>
        </tbody>
    </table>

    <div class="wc-proceed-to-checkout hidden">
        <?php do_action('woocommerce_proceed_to_checkout'); ?>
    </div>

    <?php do_action('woocommerce_after_cart_totals'); ?>

</div> 