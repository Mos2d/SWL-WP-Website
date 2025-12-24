<?php
/**
 * Cart Page
 */

defined('ABSPATH') || exit;

get_header('shop');

do_action('woocommerce_before_cart'); ?>

<main class="container mx-auto px-4 py-10">
    <!-- Cart Header -->
    <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-10">
        <div class="h-48 bg-accent/20 relative">
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="fas fa-shopping-cart text-8xl text-accent/50"></i>
            </div>
        </div>
        <div class="p-8">
            <h1 class="text-3xl md:text-4xl font-bold text-dark mb-4">
                سلة المشتريات
            </h1>
            <p class="text-gray-600 text-lg">
                مراجعة المنتجات في سلة المشتريات وتعديل الكميات أو إضافة كوبون خصم.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Form -->
        <div class="lg:col-span-2">
            <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
                <?php do_action('woocommerce_before_cart_table'); ?>

                <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                    <?php if (WC()->cart->is_empty()) : ?>
                        <div class="p-8 text-center min-h-[400px] flex flex-col items-center justify-center">
                            <div class="inline-block p-8 bg-light rounded-2xl shadow-md mb-6">
                                <i class="fas fa-shopping-basket text-6xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-dark mb-4">سلة المشتريات فارغة</h3>
                            <p class="text-lg text-gray-600 mb-6 max-w-md"><?php esc_html_e('Your cart is currently empty.', 'woocommerce'); ?></p>
                            <a class="bg-primary text-white px-8 py-3 rounded-full text-lg shadow-lg hover:bg-primary/90 transition inline-flex items-center" href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>">
                                <i class="fas fa-arrow-left mr-2"></i>
                                <?php esc_html_e('Return to shop', 'woocommerce'); ?>
                            </a>
                        </div>
                    <?php else : ?>
                        <div class="overflow-x-auto -mx-6 md:mx-0">
                            <table class="w-full min-w-[600px]">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-3 md:px-6 py-4 text-right min-w-[200px]"><?php esc_html_e('Product', 'woocommerce'); ?></th>
                                        <th class="px-3 md:px-6 py-4 text-right min-w-[80px] hidden sm:table-cell"><?php esc_html_e('Price', 'woocommerce'); ?></th>
                                        <th class="px-3 md:px-6 py-4 text-right min-w-[100px]"><?php esc_html_e('Quantity', 'woocommerce'); ?></th>
                                        <th class="px-3 md:px-6 py-4 text-right min-w-[100px]"><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
                                        <th class="px-3 md:px-6 py-4 text-right min-w-[60px]"><span class="screen-reader-text"><?php esc_html_e('Remove item', 'woocommerce'); ?></span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php do_action('woocommerce_before_cart_contents'); ?>

                                    <?php
                                    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                                        $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                                        $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                                        if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                                            $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                                            ?>
                                            <tr class="border-b border-gray-100">
                                                <td class="px-3 md:px-6 py-4" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
                                                    <div class="flex items-center space-x-2 md:space-x-4 rtl:space-x-reverse flex-wrap">
                                                        <?php
                                                        // Product thumbnail
                                                        $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail', ['class' => 'w-16 h-16 rounded-lg']), $cart_item, $cart_item_key);
                                                        
                                                        if (!$product_permalink) {
                                                            echo $thumbnail;
                                                        } else {
                                                            echo '<div class="ml-2 md:ml-4">' . $thumbnail . '</div>';
                                                        }
                                                        ?>

                                                        <div class="mr-2 md:mr-4 flex-1 min-w-0">
                                                            <?php
                                                            if (!$product_permalink) {
                                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
                                                            } else {
                                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s" class="text-dark hover:text-primary transition font-bold">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                                                            }

                                                            do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

                                                            // Meta data
                                                            echo wc_get_formatted_cart_item_data($cart_item);

                                                            // Backorder notification
                                                            if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>', $product_id));
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="px-3 md:px-6 py-4 text-primary font-bold hidden sm:table-cell" data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
                                                    <?php
                                                        echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                                                    ?>
                                                </td>

                                                <td class="px-3 md:px-6 py-4" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
                                                    <div class="quantity min-w-[80px]">
                                                    <?php
                                                    if ($_product->is_sold_individually()) {
                                                        $product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
                                                    } else {
                                                        $product_quantity = woocommerce_quantity_input(
                                                            array(
                                                                'input_name'   => "cart[{$cart_item_key}][qty]",
                                                                'input_value'  => $cart_item['quantity'],
                                                                'max_value'    => $_product->get_max_purchase_quantity(),
                                                                'min_value'    => '0',
                                                                'product_name' => $_product->get_name(),
                                                                'classes'      => 'w-20 rounded-lg border-gray-300',
                                                            ),
                                                            $_product,
                                                            false
                                                        );
                                                    }

                                                    echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item);
                                                    ?>
                                                    </div>
                                                </td>

                                                <td class="px-3 md:px-6 py-4 text-primary font-bold" data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>">
                                                    <?php
                                                        echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
                                                    ?>
                                                </td>

                                                <td class="px-3 md:px-6 py-4 text-center">
                                                    <?php
                                                        echo apply_filters(
                                                            'woocommerce_cart_item_remove_link',
                                                            sprintf(
                                                                '<a href="%s" class="bg-red-500 text-white hover:bg-red-600 transition rounded-full w-8 h-8 inline-flex items-center justify-center" aria-label="%s" data-product_id="%s" data-product_sku="%s"><i class="fas fa-times"></i></a>',
                                                                esc_url(wc_get_cart_remove_url($cart_item_key)),
                                                                esc_html__('Remove this item', 'woocommerce'),
                                                                esc_attr($product_id),
                                                                esc_attr($_product->get_sku())
                                                            ),
                                                            $cart_item_key
                                                        );
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>

                                    <?php do_action('woocommerce_cart_contents'); ?>
                                    <?php do_action('woocommerce_after_cart_contents'); ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="p-4 md:p-6 border-t border-gray-100">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 w-full">
                                <?php if (wc_coupons_enabled()) : ?>
                                    <div class="coupon flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                                        <input type="text" name="coupon_code" class="input-text rounded-lg border-gray-300 w-full sm:w-auto" id="coupon_code" value="" placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>" />
                                        <button type="submit" class="bg-accent text-dark px-4 py-2 rounded-full shadow-md hover:bg-accent/90 transition whitespace-nowrap" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>">
                                            <i class="fas fa-ticket-alt mr-1"></i>
                                            <?php esc_attr_e('Apply coupon', 'woocommerce'); ?>
                                        </button>
                                        <?php do_action('woocommerce_cart_coupon'); ?>
                                    </div>
                                <?php endif; ?>

                                <button type="submit" class="bg-secondary text-white px-6 py-2 rounded-full shadow-md hover:bg-secondary/90 transition w-full sm:w-auto" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>">
                                    <i class="fas fa-sync-alt mr-1"></i>
                                    <?php esc_html_e('Update cart', 'woocommerce'); ?>
                                </button>
                                
                                <?php do_action('woocommerce_cart_actions'); ?>
                                <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php do_action('woocommerce_after_cart_table'); ?>
            </form>
        </div>

        <!-- Cart Totals -->
        <div class="lg:col-span-1">
            <?php if (!WC()->cart->is_empty()) : ?>
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-xl font-bold text-dark"><?php esc_html_e('Cart totals', 'woocommerce'); ?></h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="cart_totals">
                            <?php woocommerce_cart_totals(); ?>
                        </div>

                        <div class="wc-proceed-to-checkout mt-6">
                            <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="bg-primary text-white px-6 py-3 rounded-full shadow-md hover:bg-primary/90 transition text-center block w-full">
                                <i class="fas fa-credit-card ml-1"></i>
                                <?php esc_html_e('Proceed to checkout', 'woocommerce'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!WC()->cart->is_empty()) : ?>
        <!-- Cross-sells -->
        <div class="mt-12">
            <?php
            /**
             * Display cross-sells
             */
            if (function_exists('woocommerce_cross_sell_display')) {
                // Start a custom output buffer
                ob_start();
                woocommerce_cross_sell_display();
                $cross_sells_content = ob_get_clean();
                
                // Only output if we have cross-sells
                if (!empty($cross_sells_content)) {
                    echo '<section class="mb-16">';
                    echo '<h2 class="text-2xl md:text-3xl font-bold text-dark mb-8"><span class="border-b-4 border-primary pb-2">منتجات قد تعجبك</span></h2>';
                    echo '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">';
                    echo $cross_sells_content;
                    echo '</div>';
                    echo '</section>';
                }
            }
            ?>
        </div>
    <?php endif; ?>
</main>

<?php do_action('woocommerce_after_cart'); ?>

<?php get_footer('shop'); ?>
