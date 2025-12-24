<?php
/**
 * Checkout Form
 */

defined('ABSPATH') || exit;

// Initialize the checkout object
$checkout = WC()->checkout();

get_header('shop');

do_action('woocommerce_before_checkout_form', $checkout);

// If checkout registration is disabled and not logged in, the user cannot checkout.
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}
?>

<main class="container mx-auto px-4 py-10 overflow-hidden">
    <!-- Checkout Header -->
    <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-10">
        <div class="h-48 bg-secondary/20 relative">
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="fas fa-credit-card text-8xl text-secondary/50"></i>
            </div>
        </div>
        <div class="p-8">
            <h1 class="text-3xl md:text-4xl font-bold text-dark mb-4">
                <?php esc_html_e('Checkout', 'woocommerce'); ?>
            </h1>
            <p class="text-gray-600 text-lg">
                <?php esc_html_e('Complete your order by providing your details and payment information.', 'woocommerce'); ?>
            </p>
        </div>
    </div>

    <form name="checkout" method="post" class="checkout woocommerce-checkout max-w-full" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <?php if ($checkout->get_checkout_fields()) : ?>
                <div class="lg:col-span-2">
                    <?php do_action('woocommerce_checkout_before_customer_details'); ?>

                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8" id="customer_details">
                        <div class="p-4 md:p-6 border-b border-gray-100">
                            <h2 class="text-lg md:text-xl font-bold text-dark">
                                <i class="fas fa-user-circle mr-2 text-secondary"></i>
                                <?php esc_html_e('Billing details', 'woocommerce'); ?>
                            </h2>
                        </div>
                        <div class="p-4 md:p-6">
                            <?php do_action('woocommerce_checkout_billing'); ?>
                        </div>
                    </div>

                    <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                            <div class="p-4 md:p-6 border-b border-gray-100">
                                <h2 class="text-lg md:text-xl font-bold text-dark">
                                    <i class="fas fa-shipping-fast mr-2 text-secondary"></i>
                                    <?php esc_html_e('Shipping details', 'woocommerce'); ?>
                                </h2>
                            </div>
                            <div class="p-4 md:p-6">
                                <?php do_action('woocommerce_checkout_shipping'); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (wc_coupons_enabled()) : ?>
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                            <div class="p-4 md:p-6 border-b border-gray-100">
                                <h2 class="text-lg md:text-xl font-bold text-dark">
                                    <i class="fas fa-ticket-alt mr-2 text-accent"></i>
                                    <?php esc_html_e('Coupon', 'woocommerce'); ?>
                                </h2>
                            </div>
                            <div class="p-4 md:p-6">
                                <div class="coupon checkout-coupon">
                                    <label for="coupon_code" class="block text-gray-600 mb-2"><?php esc_html_e('Have a coupon?', 'woocommerce'); ?></label>
                                    <div class="flex flex-col sm:flex-row gap-2 w-full">
                                        <input type="text" name="coupon_code" class="input-text rounded-lg border-gray-300 flex-grow min-w-0" id="coupon_code" value="" placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>" />
                                        <button type="button" class="bg-accent text-dark px-4 py-2 rounded-full shadow-md hover:bg-accent/90 transition whitespace-nowrap" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>">
                                            <i class="fas fa-check mr-1"></i>
                                            <?php esc_html_e('Apply', 'woocommerce'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php do_action('woocommerce_checkout_after_customer_details'); ?>
                </div>
            <?php endif; ?>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden lg:sticky lg:top-24">
                    <div class="p-4 md:p-6 border-b border-gray-100">
                        <h2 class="text-lg md:text-xl font-bold text-dark">
                            <i class="fas fa-shopping-basket mr-2 text-primary"></i>
                            <?php esc_html_e('Your order', 'woocommerce'); ?>
                        </h2>
                    </div>

                    <div class="p-4 md:p-6">
                        <?php do_action('woocommerce_checkout_before_order_review'); ?>

                        <div id="order_review" class="woocommerce-checkout-review-order">
                            <?php do_action('woocommerce_checkout_order_review'); ?>
                        </div>

                        <?php do_action('woocommerce_checkout_after_order_review'); ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

<!-- Payment method script -->
<script>
jQuery(document).ready(function($) {
    // Style payment methods
    $('.wc_payment_methods li').addClass('py-3 border-b border-gray-100 last:border-0');
    $('.wc_payment_methods li > label').addClass('font-bold text-dark cursor-pointer');
    
    // Fix responsive tables in order review
    $('.woocommerce-checkout-review-order-table').addClass('w-full');
    $('.woocommerce-checkout-review-order-table td, .woocommerce-checkout-review-order-table th').addClass('px-3 py-2');
    
    // Apply coupon button functionality
    $('.checkout-coupon button[name="apply_coupon"]').on('click', function(e) {
        e.preventDefault();
        var coupon = $('#coupon_code').val();
        if (coupon) {
            // Add a hidden field for the coupon and submit the form
            $('form.checkout').append('<input type="hidden" name="coupon_code" value="' + coupon + '">');
            $('form.checkout').submit();
        }
    });
});
</script>

<?php get_footer('shop'); ?>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>
