<?php
/**
 * View Order
 *
 * Shows the details of a particular order on the account page.
 *
 * This template overrides /woocommerce/templates/myaccount/view-order.php
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

defined('ABSPATH') || exit;

$notes = $order->get_customer_order_notes();
?>

<div class="view-order-container p-4 bg-white rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-4">
        <?php
        printf(
            /* translators: %s: Order ID. */
            esc_html__('Order #%s', 'woocommerce'),
            $order->get_order_number()
        );
        ?>
    </h2>

    <div class="order-meta mb-6 p-4 bg-gray-50 rounded-lg">
        <div class="grid md:grid-cols-4 gap-4 text-right">
            <div class="order-date">
                <span class="font-bold"><?php esc_html_e('Date:', 'woocommerce'); ?></span>
                <span><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></span>
            </div>
            
            <div class="order-status">
                <span class="font-bold"><?php esc_html_e('Status:', 'woocommerce'); ?></span>
                <?php
                $status = $order->get_status();
                $status_name = wc_get_order_status_name($status);
                
                // Define status colors
                $status_classes = [
                    'completed'  => 'bg-green-100 text-green-800',
                    'processing' => 'bg-blue-100 text-blue-800',
                    'on-hold'    => 'bg-yellow-100 text-yellow-800',
                    'cancelled'  => 'bg-red-100 text-red-800',
                    'failed'     => 'bg-red-100 text-red-800',
                    'pending'    => 'bg-gray-100 text-gray-800',
                ];
                
                $class = isset($status_classes[$status]) ? $status_classes[$status] : 'bg-gray-100 text-gray-800';
                ?>
                <span class="order-status-badge inline-block px-2 py-1 text-xs rounded-full <?php echo esc_attr($class); ?>">
                    <?php echo esc_html($status_name); ?>
                </span>
            </div>
            
            <div class="order-total">
                <span class="font-bold"><?php esc_html_e('Total:', 'woocommerce'); ?></span>
                <span><?php echo wp_kses_post($order->get_formatted_order_total()); ?></span>
            </div>
            
            <?php if ($order->get_payment_method_title()) : ?>
            <div class="order-payment">
                <span class="font-bold"><?php esc_html_e('Payment method:', 'woocommerce'); ?></span>
                <span><?php echo esc_html($order->get_payment_method_title()); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php do_action('woocommerce_view_order', $order_id); ?>

    <?php if ($notes) : ?>
        <div class="order-notes mt-8">
            <h3 class="text-xl font-bold mb-4"><?php esc_html_e('Order updates', 'woocommerce'); ?></h3>
            <div class="order-notes-list space-y-4">
                <?php foreach ($notes as $note) : ?>
                    <div class="order-note p-4 border-r-4 border-primary bg-gray-50 rounded">
                        <div class="order-note-meta text-sm text-gray-600 mb-2">
                            <?php echo date_i18n(esc_html__('l jS \o\f F Y, h:ia', 'woocommerce'), strtotime($note->comment_date)); ?>
                        </div>
                        <div class="order-note-content text-right">
                            <?php echo wpautop(wptexturize($note->comment_content)); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="order-actions mt-6 text-center">
        <a href="<?php echo esc_url(wc_get_endpoint_url('orders')); ?>" class="back-to-orders px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">
            <i class="fas fa-arrow-right ml-1"></i>
            <?php esc_html_e('Back to orders', 'woocommerce'); ?>
        </a>
    </div>
</div> 