<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template overrides /woocommerce/templates/myaccount/orders.php
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.7.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_account_orders', $has_orders);
?>

<div class="orders-container p-4 bg-white rounded-lg shadow">
	<?php if ($has_orders) : ?>
		<h2 class="text-2xl font-bold mb-6 text-right">
			<?php echo esc_html__('My Orders', 'woocommerce'); ?>
		</h2>

		<div class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders">
			<div class="hidden md:grid grid-cols-5 gap-4 p-3 bg-gray-100 rounded-t-lg text-right font-bold text-gray-700">
				<div class="order-number"><?php esc_html_e('Order', 'woocommerce'); ?></div>
				<div class="order-date"><?php esc_html_e('Date', 'woocommerce'); ?></div>
				<div class="order-status"><?php esc_html_e('Status', 'woocommerce'); ?></div>
				<div class="order-total"><?php esc_html_e('Total', 'woocommerce'); ?></div>
				<div class="order-actions"><?php esc_html_e('Actions', 'woocommerce'); ?></div>
			</div>

			<div class="orders-list space-y-4 mt-4">
				<?php
				foreach ($customer_orders->orders as $customer_order) :
					$order = wc_get_order($customer_order); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$item_count = $order->get_item_count() - $order->get_item_count_refunded();
					$status = $order->get_status();
					
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
					<div class="order-item p-4 border border-gray-200 rounded-lg hover:shadow-sm transition">
						<div class="md:hidden mb-4 border-b pb-2">
							<span class="font-bold"><?php esc_html_e('Order', 'woocommerce'); ?>:</span>
							<a href="<?php echo esc_url($order->get_view_order_url()); ?>" class="text-primary hover:underline">
								<?php echo esc_html(_x('#', 'hash before order number', 'woocommerce') . $order->get_order_number()); ?>
							</a>
						</div>
						
						<div class="md:grid md:grid-cols-5 md:gap-4 md:items-center text-right">
							<div class="order-number hidden md:block">
								<a href="<?php echo esc_url($order->get_view_order_url()); ?>" class="text-primary hover:underline">
									<?php echo esc_html(_x('#', 'hash before order number', 'woocommerce') . $order->get_order_number()); ?>
								</a>
							</div>
							
							<div class="order-date md:order-date mb-2 md:mb-0">
								<div class="md:hidden inline font-bold"><?php esc_html_e('Date', 'woocommerce'); ?>: </div>
								<time datetime="<?php echo esc_attr($order->get_date_created()->date('c')); ?>"><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></time>
							</div>
							
							<div class="order-status md:order-status mb-2 md:mb-0">
								<div class="md:hidden inline font-bold"><?php esc_html_e('Status', 'woocommerce'); ?>: </div>
								<span class="order-status-badge inline-block px-2 py-1 text-xs rounded-full <?php echo esc_attr($class); ?>">
									<?php echo esc_html(wc_get_order_status_name($order->get_status())); ?>
								</span>
							</div>
							
							<div class="order-total md:order-total mb-2 md:mb-0">
								<div class="md:hidden inline font-bold"><?php esc_html_e('Total', 'woocommerce'); ?>: </div>
								<?php
								/* translators: 1: formatted order total 2: total order items */
								echo wp_kses_post(sprintf(_n('%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce'), $order->get_formatted_order_total(), $item_count));
								?>
							</div>
							
							<div class="order-actions md:order-actions text-center md:text-right">
								<?php
								$actions = wc_get_account_orders_actions($order);
								
								if (!empty($actions)) :
									foreach ($actions as $key => $action) :
										$action_class = 'btn-' . $key;
										$btn_class = '';
										
										if ($key === 'view') {
											$btn_class = 'bg-primary text-white hover:bg-primary-dark';
										} elseif ($key === 'pay') {
											$btn_class = 'bg-green-600 text-white hover:bg-green-700';
										} elseif ($key === 'cancel') {
											$btn_class = 'bg-red-600 text-white hover:bg-red-700';
										} else {
											$btn_class = 'bg-gray-600 text-white hover:bg-gray-700';
										}
								?>
										<a href="<?php echo esc_url($action['url']); ?>" class="order-action-<?php echo esc_attr($key); ?> px-3 py-1 rounded text-sm inline-block mb-1 <?php echo esc_attr($btn_class); ?>">
											<?php echo esc_html($action['name']); ?>
										</a>
								<?php
									endforeach;
								endif;
								?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<?php do_action('woocommerce_before_account_orders_pagination'); ?>

		<?php if (1 < $customer_orders->max_num_pages) : ?>
			<div class="woocommerce-pagination woocommerce-pagination--without-numbers flex justify-between items-center pt-6 border-t border-gray-200 mt-6">
				<?php if (1 !== $current_page) : ?>
					<a class="woocommerce-button woocommerce-button--previous woocommerce-Button inline-block px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition" href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page - 1)); ?>">
						<?php esc_html_e('Previous', 'woocommerce'); ?>
					</a>
				<?php endif; ?>

				<?php if (intval($customer_orders->max_num_pages) !== $current_page) : ?>
					<a class="woocommerce-button woocommerce-button--next woocommerce-Button inline-block px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition" href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page + 1)); ?>">
						<?php esc_html_e('Next', 'woocommerce'); ?>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	<?php else : ?>
		<div class="no-orders p-8 text-center bg-gray-50 rounded-lg">
			<div class="no-orders-icon text-5xl text-gray-300 mb-3">
				<i class="fas fa-shopping-bag"></i>
			</div>
			<p class="no-orders-text text-lg text-gray-600">
				<?php esc_html_e('No order has been made yet.', 'woocommerce'); ?>
			</p>
			<a class="woocommerce-Button button mt-4 inline-block px-6 py-2 bg-primary text-white rounded hover:bg-primary-dark transition" href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>">
				<?php esc_html_e('Browse products', 'woocommerce'); ?>
			</a>
		</div>
	<?php endif; ?>
</div>

<?php do_action('woocommerce_after_account_orders', $has_orders); ?> 