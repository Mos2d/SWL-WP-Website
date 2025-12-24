<?php
/**
 * WooCommerce Integration
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * WooCommerce setup function.
 */
function sarah_loz_woocommerce_setup() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'sarah_loz_woocommerce_setup');

/**
 * Disable the default WooCommerce stylesheet.
 */

add_filter('woocommerce_enqueue_styles', '__return_empty_array');

/**
 * Add custom classes to products
 */
function sarah_loz_woocommerce_product_classes($classes, $product) {
    $classes[] = 'bg-white rounded-2xl shadow-lg overflow-hidden transition transform hover:-translate-y-2 hover:shadow-xl';
    return $classes;
}
add_filter('woocommerce_post_class', 'sarah_loz_woocommerce_product_classes', 10, 2);

/**
 * Customize WooCommerce button styles
 */
function sarah_loz_woocommerce_button_styles() {
    ?>
    <style>
        .woocommerce #respond input#submit,
        .woocommerce a.button,
        .woocommerce button.button,
        .woocommerce input.button {
            @apply bg-primary text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition-colors;
        }

        .woocommerce #respond input#submit.alt,
        .woocommerce a.button.alt,
        .woocommerce button.button.alt,
        .woocommerce input.button.alt {
            @apply bg-secondary;
        }

        /* Cart and checkout form styles */
        .woocommerce form .form-row input.input-text,
        .woocommerce form .form-row textarea {
            @apply rounded-lg border-gray-300 p-2;
        }

        /* Add to cart button styles */
        .woocommerce div.product form.cart .button,
        .woocommerce .single_add_to_cart_button {
            @apply bg-primary text-white px-6 py-3 rounded-full text-lg shadow-lg hover:bg-primary/90 transition;
        }

        /* Star rating styles */
        .woocommerce .star-rating {
            @apply text-accent;
        }

        /* Sale badge styles */
        .woocommerce span.onsale {
            @apply bg-primary text-white px-3 py-1 rounded-full text-sm;
            min-height: auto;
            min-width: auto;
            line-height: normal;
        }

        /* Product gallery thumbnail styles */
        .woocommerce div.product div.images .flex-control-thumbs li {
            @apply overflow-hidden rounded-lg border-2 border-transparent;
        }

        .woocommerce div.product div.images .flex-control-thumbs li:hover,
        .woocommerce div.product div.images .flex-control-thumbs li img.flex-active {
            @apply border-primary;
        }

        /* Quantity input styles */
        .woocommerce .quantity .qty {
            @apply rounded-lg border border-gray-300 p-2;
        }

        /* Messages and notices */
        .woocommerce-message,
        .woocommerce-info {
            @apply border-secondary bg-secondary/10 rounded-lg;
        }

        .woocommerce-error {
            @apply border-primary bg-primary/10 rounded-lg;
        }

        /* Cart and checkout styles */
        .woocommerce-cart-form table th,
        .woocommerce-checkout table th {
            @apply text-right py-2;
        }

        .woocommerce-cart-form table td,
        .woocommerce-checkout table td {
            @apply text-right py-2;
        }

        .woocommerce #payment {
            @apply bg-transparent;
        }
        
        .woocommerce #payment div.payment_box {
            @apply bg-light rounded-lg border border-gray-200;
        }
        
        .woocommerce #payment div.payment_box::before {
            @apply border-b-gray-200;
        }
        
        .woocommerce #payment ul.payment_methods {
            @apply border-gray-200;
        }
        
        .woocommerce-checkout #payment div.form-row {
            @apply bg-transparent;
        }

        .woocommerce #payment #place_order {
            @apply bg-primary text-white w-full text-center px-6 py-3 rounded-full shadow-md hover:bg-primary/90 transition text-lg;
        }
    </style>
    <?php
}
add_action('wp_head', 'sarah_loz_woocommerce_button_styles');

/**
 * Update cart count in real-time using AJAX
 */
function sarah_loz_woocommerce_cart_count($fragments) {
    ob_start();
    ?>
    <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    <?php
    $fragments['.cart-count'] = ob_get_clean();
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'sarah_loz_woocommerce_cart_count');

/**
 * Customize product archive columns
 */
function sarah_loz_woocommerce_loop_columns() {
    return 3;
}
add_filter('loop_shop_columns', 'sarah_loz_woocommerce_loop_columns');

/**
 * Add custom fields to product
 */
function sarah_loz_register_product_fields() {
    if (function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
            'key' => 'group_sarah_loz_product',
            'title' => 'تفاصيل المنتج الإضافية',
            'fields' => array(
                array(
                    'key' => 'field_age_range',
                    'label' => 'العمر المناسب',
                    'name' => 'age_range',
                    'type' => 'text',
                    'instructions' => 'أدخل العمر المناسب للمنتج، مثال: ٥-٩',
                ),
                array(
                    'key' => 'field_product_features',
                    'label' => 'مميزات المنتج',
                    'name' => 'product_features',
                    'type' => 'repeater',
                    'instructions' => 'أضف مميزات المنتج',
                    'layout' => 'table',
                    'button_label' => 'إضافة ميزة',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_feature',
                            'label' => 'الميزة',
                            'name' => 'feature',
                            'type' => 'text',
                        ),
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'product',
                    ),
                ),
            ),
        ));
    }
}
add_action('acf/init', 'sarah_loz_register_product_fields');

/**
 * Modify WooCommerce breadcrumb
 */
function sarah_loz_woocommerce_breadcrumb_args($args) {
    $args['delimiter'] = '<span class="mx-2">/</span>';
    $args['wrap_before'] = '<div class="text-sm text-gray-500 mb-6">';
    $args['wrap_after'] = '</div>';
    $args['home'] = 'الرئيسية';
    return $args;
}
add_filter('woocommerce_breadcrumb_defaults', 'sarah_loz_woocommerce_breadcrumb_args');

/**
 * Customize add to cart button text
 */
function sarah_loz_add_to_cart_text($text) {
    return '<i class="fas fa-cart-plus ml-1"></i> أضف للسلة';
}
// Remove these filters since they're not handling HTML properly
remove_filter('woocommerce_product_single_add_to_cart_text', 'sarah_loz_add_to_cart_text');
remove_filter('woocommerce_product_add_to_cart_text', 'sarah_loz_add_to_cart_text');

/**
 * Add cart icon to add to cart button
 */
function sarah_loz_add_to_cart_button_html($button, $product) {
    $button_text = sprintf(
        '<a href="%s" data-quantity="1" class="%s" %s><i class="fas fa-cart-plus ml-1"></i> أضف للسلة</a>',
        esc_url($product->add_to_cart_url()),
        esc_attr(implode(' ', array_filter(array(
            'button',
            'product_type_' . $product->get_type(),
            $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
            $product->supports('ajax_add_to_cart') ? 'ajax_add_to_cart' : '',
            'bg-primary text-white px-4 py-2 rounded-full hover:bg-primary/90 transition'
        )))),
        wc_implode_html_attributes(array(
            'data-product_id'  => $product->get_id(),
            'data-product_sku' => $product->get_sku(),
            'aria-label'       => $product->add_to_cart_description(),
            'rel'             => 'nofollow',
        ))
    );
    
    return $button_text;
}
add_filter('woocommerce_loop_add_to_cart_link', 'sarah_loz_add_to_cart_button_html', 10, 2);

/**
 * Add cart icon to single product add to cart button
 */
function sarah_loz_single_add_to_cart_button() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Add icon to the single product add to cart button if it doesn't already have one
        if (!$('.single_add_to_cart_button i.fas.fa-cart-plus').length) {
            $('.single_add_to_cart_button').html('<i class="fas fa-cart-plus ml-1"></i> أضف للسلة');
        }
        
        // For variable products, update the button text when variations are selected
        $('form.variations_form').on('show_variation', function() {
            $('.single_add_to_cart_button').html('<i class="fas fa-cart-plus ml-1"></i> أضف للسلة');
        });
    });
    </script>
    <?php
}
add_action('woocommerce_after_add_to_cart_button', 'sarah_loz_single_add_to_cart_button');

/**
 * Add Font Awesome icons to WooCommerce
 */
function sarah_loz_enqueue_fontawesome() {
    // Always load FontAwesome for cart and checkout pages
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
}
add_action('wp_enqueue_scripts', 'sarah_loz_enqueue_fontawesome', 99);

/**
 * Make sure WooCommerce can find our template files
 */
function sarah_loz_woocommerce_template_path() {
    return 'woocommerce/';
}
add_filter('woocommerce_template_path', 'sarah_loz_woocommerce_template_path');

/**
 * Force WooCommerce to use our template files
 */
function sarah_loz_force_woocommerce_templates($located, $template_name, $args, $template_path, $default_path) {
    $search_path = get_template_directory() . '/woocommerce/' . $template_name;
    
    if (file_exists($search_path)) {
        return $search_path;
    }
    
    return $located;
}
add_filter('wc_get_template', 'sarah_loz_force_woocommerce_templates', 10, 5);

/**
 * Add important styles to fix cart and checkout display issues
 */
function sarah_loz_fix_cart_checkout_styles() {
    if (is_cart() || is_checkout()) {
        ?>
        <style>
            /* Fix display issues for cart and checkout */
            .woocommerce-cart-form,
            .woocommerce-checkout {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
            }
            
            .woocommerce form {
                display: block !important;
            }
            
            /* Only show cart contents inside header */
            header .cart-contents,
            .woocommerce-cart-form__contents,
            .woocommerce-checkout-review-order-table,
            .woocommerce-checkout-payment {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
            }
            
            /* Hide any cart-contents outside of header */
            body > .cart-contents:not(header .cart-contents) {
                display: none !important;
            }
            
            /* Fix container widths */
            .woocommerce-cart .container,
            .woocommerce-checkout .container {
                width: 100% !important;
                max-width: 1200px !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }
            
            /* Add some debugging outlines to help see structure */
            .debug-woo-template .woocommerce {
                border: 2px dashed rgba(255, 107, 107, 0.5);
                padding: 10px;
            }
            
            .debug-woo-template .woocommerce-cart-form, 
            .debug-woo-template form.checkout {
                border: 2px dashed rgba(78, 205, 196, 0.5);
                padding: 10px;
            }
            
            .debug-woo-template .cart_totals, 
            .debug-woo-template #order_review {
                border: 2px dashed rgba(255, 209, 102, 0.5);
                padding: 10px;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'sarah_loz_fix_cart_checkout_styles', 999);

/**
 * WooCommerce Template Troubleshooter
 * Checks if we're trying to load a template that doesn't exist and falls back to default
 */
function sarah_loz_woocommerce_template_fallback($template, $template_name, $template_path) {
    // Get the template file path
    $template_file = trailingslashit(get_template_directory()) . $template_path . $template_name;
    
    // If our theme doesn't have this template, use the default WooCommerce template
    if (!file_exists($template_file)) {
        $template = WC()->plugin_path() . '/templates/' . $template_name;
    }
    
    return $template;
}
add_filter('woocommerce_locate_template', 'sarah_loz_woocommerce_template_fallback', 20, 3);

/**
 * Fix template loading for cart and checkout
 * This ensures the template is always found and loads from the correct location
 */
function sarah_loz_woocommerce_template_loader($template, $template_name, $args, $template_path, $default_path) {
    // Special handling for cart and checkout templates
    if (in_array($template_name, array('cart/cart.php', 'checkout/form-checkout.php'))) {
        $theme_template = get_stylesheet_directory() . '/woocommerce/' . $template_name;
        
        if (file_exists($theme_template)) {
            return $theme_template;
        }
    }
    
    return $template;
}
add_filter('wc_get_template', 'sarah_loz_woocommerce_template_loader', 999, 5);

/**
 * Ensure WooCommerce templates and scripts are loaded correctly
 */
function sarah_loz_ensure_woocommerce_templates() {
    if (is_cart() || is_checkout()) {
        // Remove any filters that might be interfering with template loading
        remove_all_filters('wc_get_template_part', 999);
        
        // Make sure jQuery is loaded
        wp_enqueue_script('jquery');
        
        // Make sure WooCommerce JS is loaded
        if (function_exists('WC')) {
            wp_enqueue_script('woocommerce', WC()->plugin_url() . '/assets/js/frontend/woocommerce.min.js', array('jquery'), WC()->version, true);
            
            if (is_cart()) {
                wp_enqueue_script('wc-cart', WC()->plugin_url() . '/assets/js/frontend/cart.min.js', array('jquery', 'woocommerce'), WC()->version, true);
            }
            
            if (is_checkout()) {
                wp_enqueue_script('wc-checkout', WC()->plugin_url() . '/assets/js/frontend/checkout.min.js', array('jquery', 'woocommerce'), WC()->version, true);
            }
        }
    }
}
add_action('wp_enqueue_scripts', 'sarah_loz_ensure_woocommerce_templates', 999);

/**
 * Direct cart content rendering
 * Only render if no WooCommerce content is present
 */
function sarah_loz_direct_cart_content() {
    // Don't output if using our custom template
    global $wp_query;
    if ($wp_query->is_page && get_page_template_slug() === 'woocommerce-page.php') {
        return;
    }

    if (is_cart()) {
        // Only add cart content if it's not already present
        $content_rendered = false;
        
        ob_start();
        do_action('woocommerce_before_cart');
        $before_cart = ob_get_clean();
        
        // If woocommerce_before_cart didn't output anything, we need to add our content
        if (empty(trim($before_cart))) {
            // Avoid duplication
            remove_action('woocommerce_before_main_content', 'sarah_loz_woocommerce_wrapper_before', 5);
            remove_action('woocommerce_after_main_content', 'sarah_loz_woocommerce_wrapper_after', 50);
            
            echo '<div class="woocommerce-cart-container container mx-auto px-4 py-10">';
            echo do_shortcode('[woocommerce_cart]');
            echo '</div>';
            
            $content_rendered = true;
        }
        
        return $content_rendered;
    }
    
    if (is_checkout()) {
        // Only add checkout content if it's not already present
        $content_rendered = false;
        
        ob_start();
        do_action('woocommerce_before_checkout_form', WC()->checkout());
        $before_checkout = ob_get_clean();
        
        // If woocommerce_before_checkout_form didn't output anything, we need to add our content
        if (empty(trim($before_checkout))) {
            // Avoid duplication
            remove_action('woocommerce_before_main_content', 'sarah_loz_woocommerce_wrapper_before', 5);
            remove_action('woocommerce_after_main_content', 'sarah_loz_woocommerce_wrapper_after', 50);
            
            echo '<div class="woocommerce-checkout-container container mx-auto px-4 py-10">';
            echo do_shortcode('[woocommerce_checkout]');
            echo '</div>';
            
            $content_rendered = true;
        }
        
        return $content_rendered;
    }
    
    return false;
}
add_action('wp_head', 'sarah_loz_direct_cart_content', 9999);

/**
 * Add body class for custom CSS targeting
 */
function sarah_loz_woocommerce_body_class($classes) {
    if (is_cart()) {
        $classes[] = 'woocommerce-cart-custom';
    }
    if (is_checkout()) {
        $classes[] = 'woocommerce-checkout-custom';
    }
    return $classes;
}
add_filter('body_class', 'sarah_loz_woocommerce_body_class');

/**
 * Add emergency CSS fixes for cart and checkout pages
 */
function sarah_loz_woocommerce_emergency_css() {
    if (is_cart() || is_checkout()) {
        ?>
        <style>
            /* Force display of WooCommerce elements */
            .woocommerce-cart-form,
            .cart-collaterals,
            .woocommerce-checkout-review-order-table,
            .woocommerce-checkout-payment,
            .woocommerce-checkout,
            .checkout_coupon,
            .woocommerce-notices-wrapper {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            /* Prevent duplicate content by hiding extra instances */
            .woocommerce-page .entry-content .woocommerce,
            .woocommerce-page .entry-content .woocommerce-cart-form,
            .woocommerce-page .entry-content .cart-collaterals,
            .woocommerce-page .entry-content .woocommerce-checkout,
            .site-content article.page .entry-content .woocommerce,
            .site-content article.page .entry-content .woocommerce-cart-form,
            .site-content article.page .entry-content .cart-collaterals,
            .site-content article.page .entry-content .woocommerce-checkout {
                display: none !important;
            }
            
            /* Allow only one instance of WooCommerce content */
            .direct-cart-content .woocommerce,
            .direct-checkout-content .woocommerce,
            .cart-content-wrapper .woocommerce,
            .checkout-content-wrapper .woocommerce,
            .woocommerce-page-wrapper .woocommerce {
                display: block !important;
            }
            
            /* Hide duplicate direct content if template is working */
            body.woocommerce-cart-custom .direct-cart-content,
            body.woocommerce-checkout-custom .direct-checkout-content {
                display: none !important;
            }
            
            /* Remove article styling from cart pages */
            body.woocommerce-cart article.page,
            body.woocommerce-checkout article.page {
                background: transparent !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'sarah_loz_woocommerce_emergency_css', 10000);

/**
 * Customize WooCommerce account endpoints
 */
function sarah_loz_woocommerce_account_endpoints() {
    // Set standard WooCommerce endpoints
    add_rewrite_endpoint('orders', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('edit-address', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('edit-account', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('downloads', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('view-order', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('customer-logout', EP_ROOT | EP_PAGES);
    
    // Flush rewrite rules - only use during development or when updating endpoints
    // flush_rewrite_rules();
}
add_action('init', 'sarah_loz_woocommerce_account_endpoints');

/**
 * Add additional CSS for WooCommerce account pages
 */
function sarah_loz_woocommerce_account_styles() {
    if (is_account_page()) {
        ?>
        <style>
            /* Account page container */
            .woocommerce-account .woocommerce {
                @apply flex flex-wrap gap-8;
            }

            /* Account navigation */
            .woocommerce-MyAccount-navigation {
                @apply w-full md:w-1/4;
            }

            /* Account content */
            .woocommerce-MyAccount-content {
                @apply w-full md:w-[calc(75%-2rem)];
            }

            /* Orders table */
            .woocommerce-orders-table {
                @apply w-full border-collapse;
            }

            .woocommerce-orders-table th,
            .woocommerce-orders-table td {
                @apply border border-gray-200 p-2 text-right;
            }

            .woocommerce-orders-table thead {
                @apply bg-light;
            }

            /* Address fields */
            .woocommerce-address-fields__field-wrapper,
            .woocommerce-MyAccount-content .edit-account {
                @apply grid gap-4;
            }

            .woocommerce-Address {
                @apply bg-light p-4 rounded-lg mb-4;
            }

            /* Buttons */
            .woocommerce-MyAccount-content .button,
            .woocommerce-address-fields .button {
                @apply bg-primary text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition-colors inline-block;
            }

            /* Downloads table */
            .woocommerce-table--downloads {
                @apply w-full border-collapse;
            }

            .woocommerce-table--downloads th,
            .woocommerce-table--downloads td {
                @apply border border-gray-200 p-2 text-right;
            }

            .woocommerce-table--downloads thead {
                @apply bg-light;
            }

            /* Notices */
            .woocommerce-notices-wrapper .woocommerce-message,
            .woocommerce-notices-wrapper .woocommerce-info {
                @apply bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4;
            }

            .woocommerce-notices-wrapper .woocommerce-error {
                @apply bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4;
            }

            /* Required field indicator */
            .required {
                @apply text-primary;
            }

            /* Form fields */
            .woocommerce form .form-row {
                @apply mb-4;
            }

            .woocommerce form .form-row label {
                @apply block text-gray-700 mb-1;
            }

            .woocommerce form .form-row input.input-text,
            .woocommerce form .form-row textarea,
            .woocommerce form .form-row select {
                @apply w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'sarah_loz_woocommerce_account_styles');

/**
 * Modify WooCommerce account menu order
 */
function sarah_loz_account_menu_items_order($items) {
    // Rearrange items if needed
    $dashboard = isset($items['dashboard']) ? $items['dashboard'] : '';
    $orders = isset($items['orders']) ? $items['orders'] : '';
    $downloads = isset($items['downloads']) ? $items['downloads'] : '';
    $edit_address = isset($items['edit-address']) ? $items['edit-address'] : '';
    $edit_account = isset($items['edit-account']) ? $items['edit-account'] : '';
    $logout = isset($items['customer-logout']) ? $items['customer-logout'] : '';
    
    // Set custom order
    $items = array(
        'dashboard' => $dashboard,
        'orders' => $orders,
        'downloads' => $downloads,
        'edit-account' => $edit_account,
        'edit-address' => $edit_address,
        'customer-logout' => $logout
    );
    
    return $items;
}
add_filter('woocommerce_account_menu_items', 'sarah_loz_account_menu_items_order', 20);

/**
 * Direct account content rendering
 * Only render if no WooCommerce content is present
 */
function sarah_loz_direct_account_content() {
    // Don't output if using our custom template
    global $wp_query;
    if ($wp_query->is_page && get_page_template_slug() === 'woocommerce-page.php') {
        return;
    }

    if (is_account_page()) {
        // Only add account content if it's not already present
        $content_rendered = false;
        
        ob_start();
        do_action('woocommerce_account_content');
        $account_content = ob_get_clean();
        
        // If woocommerce_account_content didn't output anything, we need to add our content
        if (empty(trim($account_content))) {
            echo '<div class="woocommerce-account-container container mx-auto px-4 py-10">';
            
            if (!is_user_logged_in()) {
                // Show login form
                echo '<div class="woocommerce-account-login max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">';
                echo '<h2 class="text-2xl font-bold text-dark mb-6 text-center">تسجيل الدخول</h2>';
                
                $args = array(
                    'redirect' => wc_get_page_permalink('myaccount'),
                    'form_id' => 'loginform',
                    'label_username' => __('البريد الإلكتروني', 'sarah-loz'),
                    'label_password' => __('كلمة المرور', 'sarah-loz'),
                    'label_remember' => __('تذكرني', 'sarah-loz'),
                    'label_log_in' => __('تسجيل الدخول', 'sarah-loz'),
                    'remember' => true
                );
                
                wp_login_form($args);
                
                echo '<div class="flex flex-wrap justify-between mt-4 text-sm">';
                echo '<a href="' . add_query_arg('action', 'register', wc_get_page_permalink('myaccount')) . '" class="text-primary hover:text-opacity-80 transition-colors">حساب جديد</a>';
                echo '<a href="' . wp_lostpassword_url() . '" class="text-primary hover:text-opacity-80 transition-colors">نسيت كلمة المرور؟</a>';
                echo '</div>';
                
                echo '</div>';
            } else {
                // If logged in, show the account content
                echo do_shortcode('[woocommerce_my_account]');
            }
            
            echo '</div>';
            
            $content_rendered = true;
        }
        
        return $content_rendered;
    }
    
    return false;
}
add_action('woocommerce_before_main_content', 'sarah_loz_direct_account_content', 9999);

/**
 * Fix order completion redirect to thank you page
 */
function sarah_loz_fix_order_received_endpoint() {
    // Make sure the order-received endpoint works correctly
    add_rewrite_endpoint('order-received', EP_ROOT | EP_PAGES);
    
    // Force refresh rewrite rules
    if (!get_option('sarah_loz_flushed_rules')) {
        flush_rewrite_rules();
        update_option('sarah_loz_flushed_rules', true);
    }
}
add_action('init', 'sarah_loz_fix_order_received_endpoint', 5);

/**
 * Ensure proper thank you page redirection after checkout
 */
function sarah_loz_ensure_order_received_template($template, $template_name, $args, $template_path) {
    if ($template_name === 'checkout/thankyou.php') {
        // If WooCommerce can't find the template in the theme, use the default
        if (!file_exists(get_stylesheet_directory() . '/woocommerce/' . $template_name)) {
            $template = WC()->plugin_path() . '/templates/' . $template_name;
        }
    }
    return $template;
}
add_filter('wc_get_template', 'sarah_loz_ensure_order_received_template', 999, 5);

/**
 * Add styling for the order received / thank you page
 */
function sarah_loz_order_received_styles() {
    if (is_wc_endpoint_url('order-received')) {
        ?>
        <style>
            /* Order received container */
            .woocommerce-order-received .woocommerce {
                @apply container mx-auto px-4 py-10;
            }
            
            /* Thank you message */
            .woocommerce-order-received .woocommerce-thankyou-order-received {
                @apply text-2xl font-bold text-center mb-8 text-primary;
            }
            
            /* Order details */
            .woocommerce-order-received .woocommerce-order-details {
                @apply bg-white p-6 rounded-lg shadow-lg mb-8;
            }
            
            .woocommerce-order-received .woocommerce-order-details__title {
                @apply text-xl font-bold mb-4 text-right;
            }
            
            /* Order overview boxes */
            .woocommerce-order-received .woocommerce-order-overview {
                @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8;
            }
            
            .woocommerce-order-received .woocommerce-order-overview li {
                @apply bg-light p-4 rounded-lg text-right;
            }
            
            .woocommerce-order-received .woocommerce-order-overview li strong {
                @apply block text-lg font-bold mt-1;
            }
            
            /* Order details table */
            .woocommerce-order-received .woocommerce-table--order-details {
                @apply w-full;
            }
            
            .woocommerce-order-received .woocommerce-table--order-details th,
            .woocommerce-order-received .woocommerce-table--order-details td {
                @apply border border-gray-200 p-3 text-right;
            }
            
            .woocommerce-order-received .woocommerce-table--order-details thead {
                @apply bg-light;
            }
            
            /* Customer details */
            .woocommerce-order-received .woocommerce-customer-details {
                @apply bg-white p-6 rounded-lg shadow-lg;
            }
            
            .woocommerce-order-received .woocommerce-customer-details .woocommerce-column__title {
                @apply text-xl font-bold mb-4 text-right;
            }
            
            .woocommerce-order-received address {
                @apply text-right bg-light p-4 rounded-lg not-italic;
            }
            
            /* Force display */
            .woocommerce-order,
            .woocommerce-order-overview,
            .woocommerce-order-details,
            .woocommerce-customer-details,
            .woocommerce-column--billing-address,
            .woocommerce-column--shipping-address {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            /* Apply direct styling to ensure it works */
            .woocommerce-thankyou-order-received {
                text-align: center !important;
                font-size: 1.5rem !important;
                font-weight: 700 !important;
                margin-bottom: 2rem !important;
                color: #FF6B6B !important;
            }
            
            .woocommerce-order-overview {
                display: flex !important;
                flex-wrap: wrap !important;
                gap: 1rem !important;
                margin-bottom: 2rem !important;
                padding-left: 0 !important;
                list-style: none !important;
            }
            
            .woocommerce-order-overview li {
                flex: 1 1 200px !important;
                background-color: #f9fafb !important;
                padding: 1rem !important;
                border-radius: 0.5rem !important;
                text-align: right !important;
            }
            
            .woocommerce-order-overview li strong {
                display: block !important;
                font-size: 1.125rem !important;
                font-weight: 700 !important;
                margin-top: 0.25rem !important;
            }
            
            .woocommerce-order-details__title,
            .woocommerce-column__title {
                font-size: 1.25rem !important;
                font-weight: 700 !important;
                margin-bottom: 1rem !important;
                text-align: right !important;
            }
            
            .woocommerce-table--order-details {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            
            .woocommerce-table--order-details th,
            .woocommerce-table--order-details td {
                border: 1px solid #e5e7eb !important;
                padding: 0.75rem !important;
                text-align: right !important;
            }
            
            .woocommerce-table--order-details thead th {
                background-color: #f9fafb !important;
            }
            
            .woocommerce-customer-details address {
                padding: 1rem !important;
                background-color: #f9fafb !important;
                border-radius: 0.5rem !important;
                border: 1px solid #e5e7eb !important;
                text-align: right !important;
                font-style: normal !important;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'sarah_loz_order_received_styles', 111);

/**
 * Fix WooCommerce checkout redirect issue
 */
function sarah_loz_fix_checkout_redirect() {
    // Use JavaScript as a backup method to ensure proper redirection
    if (is_checkout() && !is_wc_endpoint_url('order-received')) {
        ?>
        <script>
        jQuery(document).ready(function($) {
            // Watch for form submission and handle redirect manually if needed
            $('form.checkout').on('checkout_place_order_success', function(event, result) {
                if (result.redirect && result.redirect.indexOf('order-received') > -1) {
                    window.location.href = result.redirect;
                    return false;
                }
                return true;
            });
            
            // Fix for some themes that might intercept the redirect
            $(document.body).on('checkout_error', function() {
                $('html, body').animate({
                    scrollTop: $('.woocommerce-error').offset().top - 100
                }, 500);
            });
        });
        </script>
        <?php
    }
}
add_action('wp_footer', 'sarah_loz_fix_checkout_redirect');

/**
 * Force WooCommerce redirect to the thank you page
 */
function sarah_loz_force_order_received_redirect($order_id) {
    // Get the order
    $order = wc_get_order($order_id);
    
    if ($order) {
        // Make sure we redirect to the correct thank you page
        $redirect_url = $order->get_checkout_order_received_url();
        
        // Add a parameter to help with debugging
        $redirect_url = add_query_arg('sarah_loz_redirect', '1', $redirect_url);
        
        // Force the redirect
        wp_safe_redirect($redirect_url);
        exit;
    }
}
add_action('woocommerce_thankyou', 'sarah_loz_force_order_received_redirect', 5);

/**
 * Additional styling for WooCommerce login and registration forms
 */
function sarah_loz_woocommerce_login_register_styles() {
    if (is_account_page() || (isset($_GET['action']) && $_GET['action'] === 'register')) {
        ?>
        <style>
            /* Login form styles */
            #loginform, 
            .woocommerce-form-login,
            .woocommerce-form-register {
                @apply space-y-4 w-full;
            }

            /* Form fields */
            #loginform input[type="text"],
            #loginform input[type="password"],
            #loginform input[type="email"],
            .woocommerce-form-login input[type="text"],
            .woocommerce-form-login input[type="password"],
            .woocommerce-form-login input[type="email"],
            .woocommerce-form-register input[type="text"],
            .woocommerce-form-register input[type="password"],
            .woocommerce-form-register input[type="email"] {
                @apply w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent;
            }

            /* Labels */
            #loginform label,
            .woocommerce-form-login label,
            .woocommerce-form-register label {
                @apply block text-gray-700 mb-1 font-medium;
            }

            /* Remember me checkbox */
            .login-remember {
                @apply flex items-center space-x-2 rtl:space-x-reverse my-3;
            }

            .login-remember input[type="checkbox"] {
                @apply ml-2 h-4 w-4 text-primary focus:ring-primary;
            }

            /* Submit buttons */
            #loginform .button-primary,
            .woocommerce-form-login .woocommerce-form-login__submit,
            .woocommerce-form-register .woocommerce-form-register__submit {
                @apply w-full bg-primary text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition-colors mt-4 cursor-pointer;
            }

            /* Links */
            .woocommerce-form-login .lost_password a,
            .woocommerce-LostPassword a {
                @apply text-primary hover:text-opacity-80 transition-colors;
            }

            /* Registration form specific */
            .woocommerce-form-register,
            .register.woocommerce-form.woocommerce-form-register {
                @apply bg-white rounded-lg shadow-lg p-8 max-w-md mx-auto;
            }

            .woocommerce-form-register p,
            .register p {
                @apply my-4;
            }

            /* Registration privacy policy */
            .woocommerce-privacy-policy-text {
                @apply text-sm text-gray-600 my-4;
            }

            /* Custom field wrappers */
            .woocommerce-form-row--first,
            .woocommerce-form-row--last,
            .woocommerce-form-row--wide {
                @apply mb-4;
            }

            /* Registration title */
            .woocommerce h2,
            .woocommerce-page h2 {
                @apply text-2xl font-bold text-dark mb-6 text-center;
            }

            /* Registration hints */
            .woocommerce form .form-row .description {
                @apply text-sm text-gray-600 mt-1;
            }

            /* Error messages */
            .woocommerce-error {
                @apply bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 list-none;
            }

            /* Woocommerce login/register tab styling if applicable */
            .u-column1,
            .u-column2 {
                @apply bg-white rounded-lg shadow-lg p-8 max-w-md mx-auto;
            }

            /* Login wrapper custom styling */
            .woocommerce-account-login {
                @apply space-y-4;
            }
            
            /* Add Right-to-Left support for Arabic text */
            body.rtl .login-username,
            body.rtl .login-password,
            body.rtl .login-remember,
            body.rtl .login-submit,
            body.rtl .woocommerce-form-row {
                text-align: right;
            }
            
            /* Specific styling for the individual form elements */
            .login-username,
            .login-password {
                @apply mb-4;
            }
            
            /* Make the input field sizing more consistent */
            .input {
                @apply w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent;
            }
            
            /* Additional spacing for form groupings */
            #loginform p, 
            .woocommerce-form-login p,
            .woocommerce-form-register p {
                @apply mb-4;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'sarah_loz_woocommerce_login_register_styles', 100);

/**
 * Additional styling for WooCommerce login form to address specific layout issues
 */
function sarah_loz_login_form_fixes() {
    if (is_account_page() || (isset($_GET['action']) && $_GET['action'] === 'register')) {
        ?>
        <style>
            /* Fix spacing and layout issues in the login form */
            .woocommerce-account-login {
                @apply max-w-md mx-auto;
            }
            
            /* Center the form titles */
            .woocommerce-account-login h2 {
                @apply text-center mb-6 text-2xl font-bold;
            }
            
            /* Remove default styling on WordPress login form elements */
            .login-username, 
            .login-password, 
            .login-remember, 
            .login-submit {
                @apply m-0 p-0 block w-full;
            }
            
            /* Style the labels to be consistent */
            .login-username label, 
            .login-password label {
                @apply block w-full text-right mb-2 font-medium text-gray-700;
            }
            
            /* Style the input fields */
            .login-username input, 
            .login-password input {
                @apply w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent;
            }
            
            /* Fix remember me checkbox layout */
            .login-remember {
                @apply flex justify-start items-center my-4;
            }
            
            .login-remember label {
                @apply flex items-center cursor-pointer;
            }
            
            .login-remember input[type="checkbox"] {
                @apply ml-0 ml-0 h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary;
            }
            
            /* Override WordPress default styling for remember me text */
            .login-remember label {
                @apply mr-2 font-normal;
            }
            
            /* Make the button more prominent */
            .login-submit input[type="submit"] {
                @apply w-full bg-primary text-white px-6 py-3 rounded-full hover:bg-opacity-90 transition-colors font-medium text-center cursor-pointer;
            }
            
            /* Add hover effect to the button */
            .login-submit input[type="submit"]:hover {
                @apply shadow-md transform -translate-y-0.5;
            }
            
            /* Style the links section better */
            .woocommerce-account-login .flex {
                @apply justify-between mt-4;
            }
            
            .woocommerce-account-login .flex a {
                @apply text-primary hover:text-primary/80 transition-colors;
            }
            
            /* Fix RTL-specific issues */
            body.rtl .login-remember {
                @apply mr-0;
            }
            
            body.rtl .login-remember input[type="checkbox"] {
                @apply ml-2;
            }
            
            /* Override any theme conflicts */
            #loginform input[type="text"],
            #loginform input[type="password"] {
                min-height: 45px;
                height: auto;
            }
            
            /* Ensure checkbox is visible */
            #rememberme {
                @apply opacity-100 visible static pointer-events-auto;
                min-width: 16px;
                min-height: 16px;
            }
            
            /* Fix the registration form links */
            .woocommerce-account-login .flex {
                @apply mt-6 text-center;
            }
            
            /* Force direct styling for better compatibility */
            .login-username, .login-password, .login-remember, .login-submit {
                margin: 0 0 1rem 0 !important;
                padding: 0 !important;
                display: block !important;
                width: 100% !important;
            }
            
            .login-username label, .login-password label {
                display: block !important;
                width: 100% !important;
                text-align: right !important;
                margin-bottom: 0.5rem !important;
                font-weight: 500 !important;
                color: #4a5568 !important;
            }
            
            .login-username input, .login-password input {
                width: 100% !important;
                padding: 0.75rem 1rem !important;
                border-radius: 0.5rem !important;
                border: 1px solid #e2e8f0 !important;
                background-color: #fff !important;
            }
            
            .login-remember {
                display: flex !important;
                align-items: center !important;
                margin: 1rem 0 !important;
            }
            
            .login-remember input[type="checkbox"] {
                margin-left: 0 !important;
                margin-right: 0.5rem !important;
            }
            
            .login-submit input[type="submit"] {
                width: 100% !important;
                background-color: #FF6B6B !important;
                color: white !important;
                padding: 0.75rem 1.5rem !important;
                border-radius: 9999px !important;
                font-weight: 500 !important;
                text-align: center !important;
                cursor: pointer !important;
                border: none !important;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'sarah_loz_login_form_fixes', 101);

/**
 * Add styling for the custom registration form
 */
function sarah_loz_registration_form_styles() {
    if (isset($_GET['action']) && $_GET['action'] === 'register' && is_account_page()) {
        ?>
        <style>
            /* Registration form container */
            .woocommerce-account-register {
                @apply max-w-md mx-auto;
            }
            
            /* Registration form row */
            .woocommerce-form-register .woocommerce-form-row {
                @apply mb-4 block w-full;
            }
            
            /* Labels */
            .woocommerce-form-register label {
                @apply block w-full text-right mb-2 font-medium text-gray-700;
            }
            
            /* Required indicator */
            .woocommerce-form-register .required {
                @apply text-primary;
            }
            
            /* Input fields */
            .woocommerce-form-register input[type="text"],
            .woocommerce-form-register input[type="email"],
            .woocommerce-form-register input[type="password"],
            .woocommerce-form-register select {
                @apply w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent;
                min-height: 45px;
                height: auto;
            }
            
            /* Submit button */
            .woocommerce-form-register .submit-row input[type="submit"] {
                @apply w-full bg-primary text-white px-6 py-3 rounded-full hover:bg-opacity-90 transition-colors font-medium text-center cursor-pointer mt-6;
            }
            
            /* Add hover effect to the button */
            .woocommerce-form-register .submit-row input[type="submit"]:hover {
                @apply shadow-md transform -translate-y-0.5;
            }
            
            /* Force direct styling for better compatibility */
            .woocommerce-form-register .woocommerce-form-row {
                margin: 0 0 1rem 0 !important;
                padding: 0 !important;
                display: block !important;
                width: 100% !important;
            }
            
            .woocommerce-form-register label {
                display: block !important;
                width: 100% !important;
                text-align: right !important;
                margin-bottom: 0.5rem !important;
                font-weight: 500 !important;
                color: #4a5568 !important;
            }
            
            .woocommerce-form-register input[type="text"],
            .woocommerce-form-register input[type="email"],
            .woocommerce-form-register input[type="password"],
            .woocommerce-form-register select {
                width: 100% !important;
                padding: 0.75rem 1rem !important;
                border-radius: 0.5rem !important;
                border: 1px solid #e2e8f0 !important;
                background-color: #fff !important;
                min-height: 45px !important;
            }
            
            .woocommerce-form-register .submit-row input[type="submit"] {
                width: 100% !important;
                background-color: #FF6B6B !important;
                color: white !important;
                padding: 0.75rem 1.5rem !important;
                border-radius: 9999px !important;
                font-weight: 500 !important;
                text-align: center !important;
                cursor: pointer !important;
                border: none !important;
                margin-top: 1.5rem !important;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'sarah_loz_registration_form_styles', 102);

/**
 * Add styling for the lost password form
 */
function sarah_loz_lost_password_styles() {
    if (is_wc_endpoint_url('lost-password') || 
        (isset($_GET['key']) && isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] === 'reset_password')) {
        ?>
        <style>
            /* Lost password form container */
            .woocommerce-account-lost-password {
                @apply max-w-md mx-auto;
            }
            
            /* Form description */
            .woocommerce-account-lost-password p {
                @apply mb-6 text-gray-600;
            }
            
            /* Message boxes */
            .woocommerce-account-lost-password .woocommerce-message,
            .woocommerce-account-lost-password .woocommerce-error {
                @apply mb-6 p-4 rounded;
            }
            
            /* Form rows */
            .woocommerce-account-lost-password .woocommerce-form-row {
                @apply mb-4 block w-full;
            }
            
            /* Labels */
            .woocommerce-account-lost-password label {
                @apply block w-full text-right mb-2 font-medium text-gray-700;
            }
            
            /* Required indicator */
            .woocommerce-account-lost-password .required {
                @apply text-primary;
            }
            
            /* Input fields */
            .woocommerce-account-lost-password input[type="text"],
            .woocommerce-account-lost-password input[type="password"] {
                @apply w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent;
                min-height: 45px;
                height: auto;
            }
            
            /* Submit button */
            .woocommerce-account-lost-password button[type="submit"] {
                @apply w-full bg-primary text-white px-6 py-3 rounded-full hover:bg-opacity-90 transition-colors font-medium text-center cursor-pointer mt-6;
            }
            
            /* Add hover effect to the button */
            .woocommerce-account-lost-password button[type="submit"]:hover {
                @apply shadow-md transform -translate-y-0.5;
            }
            
            /* Force direct styling for better compatibility */
            .woocommerce-account-lost-password .woocommerce-form-row {
                margin: 0 0 1rem 0 !important;
                padding: 0 !important;
                display: block !important;
                width: 100% !important;
            }
            
            .woocommerce-account-lost-password label {
                display: block !important;
                width: 100% !important;
                text-align: right !important;
                margin-bottom: 0.5rem !important;
                font-weight: 500 !important;
                color: #4a5568 !important;
            }
            
            .woocommerce-account-lost-password input[type="text"],
            .woocommerce-account-lost-password input[type="password"] {
                width: 100% !important;
                padding: 0.75rem 1rem !important;
                border-radius: 0.5rem !important;
                border: 1px solid #e2e8f0 !important;
                background-color: #fff !important;
                min-height: 45px !important;
            }
            
            .woocommerce-account-lost-password button[type="submit"] {
                width: 100% !important;
                background-color: #FF6B6B !important;
                color: white !important;
                padding: 0.75rem 1.5rem !important;
                border-radius: 9999px !important;
                font-weight: 500 !important;
                text-align: center !important;
                cursor: pointer !important;
                border: none !important;
                margin-top: 1.5rem !important;
            }
            
            /* Style the back to login link */
            .woocommerce-account-lost-password .flex a {
                @apply text-primary font-medium;
            }
            
            .woocommerce-account-lost-password .flex a:hover {
                @apply text-primary/80;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'sarah_loz_lost_password_styles', 103);

/**
 * Improve account page layout with navigation on the right
 */
function sarah_loz_account_page_layout() {
    if (is_account_page()) {
        ?>
        <style>
            /* Main container layout */
            .woocommerce-account .woocommerce {
                @apply flex flex-wrap md:flex-nowrap flex-row-reverse gap-8 container mx-auto;
            }
            
            /* Navigation panel */
            .woocommerce-MyAccount-navigation {
                @apply w-full md:w-1/4 flex-shrink-0 bg-white rounded-lg shadow-lg p-6 self-start sticky top-8;
                max-height: calc(100vh - 100px);
                overflow-y: auto;
            }
            
            /* Content panel */
            .woocommerce-MyAccount-content {
                @apply w-full md:w-3/4 flex-grow;
            }
            
            /* Ensure navigation links are properly styled */
            .woocommerce-MyAccount-navigation ul {
                @apply list-none p-0 m-0 space-y-2;
            }
            
            .woocommerce-MyAccount-navigation li {
                @apply mb-1 border-b border-gray-100 pb-2;
            }
            
            .woocommerce-MyAccount-navigation li:last-child {
                @apply border-b-0 pb-0;
            }
            
            .woocommerce-MyAccount-navigation li a {
                @apply block py-2 px-3 text-gray-700 hover:text-primary transition-colors text-right;
            }
            
            .woocommerce-MyAccount-navigation li.is-active a {
                @apply text-primary font-bold bg-primary/10 rounded;
            }
            
            /* Fix any potential conflicts */
            .woocommerce-account .container {
                width: 100% !important;
                max-width: 1200px !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }
            
            /* For smaller screens, stack the layout */
            @media (max-width: 768px) {
                .woocommerce-account .woocommerce {
                    @apply flex-col;
                }
                
                .woocommerce-MyAccount-navigation,
                .woocommerce-MyAccount-content {
                    @apply w-full;
                }
                
                .woocommerce-MyAccount-navigation {
                    @apply mb-6 static;
                    max-height: none;
                }
            }
            
            /* Force direct styling to override theme defaults */
            .woocommerce-account .woocommerce {
                display: flex !important;
                flex-wrap: nowrap !important;
                flex-direction: row-reverse !important;
                gap: 2rem !important;
                width: 100% !important;
                max-width: 1200px !important;
                margin-left: auto !important;
                margin-right: auto !important;
                padding: 0 1rem !important;
            }
            
            .woocommerce-MyAccount-navigation {
                width: 25% !important;
                min-width: 250px !important;
                position: sticky !important;
                top: 2rem !important;
                align-self: flex-start !important;
            }
            
            .woocommerce-MyAccount-content {
                width: 75% !important;
                flex-grow: 1 !important;
            }
            
            /* Ensure proper spacing in the content area */
            .woocommerce-MyAccount-content > * {
                margin-bottom: 1.5rem !important;
            }
            
            /* Make sure navigation icons are properly aligned */
            .woocommerce-MyAccount-navigation li a i {
                margin-left: 0.5rem !important;
                width: 1rem !important;
                text-align: center !important;
            }
            
            /* Makes headers and sections in account page consistent */
            .woocommerce-MyAccount-content h2, 
            .woocommerce-MyAccount-content .woocommerce-order-details__title,
            .woocommerce-MyAccount-content .woocommerce-column__title {
                font-size: 1.5rem !important;
                font-weight: 700 !important;
                margin-bottom: 1rem !important;
                color: #2A2A72 !important;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'sarah_loz_account_page_layout', 104);

/**
 * Remove coupon form from the top of checkout page
 */
function sarah_loz_remove_checkout_coupon_form() {
    remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
    // Optionally add it back somewhere else if needed
    // add_action('woocommerce_review_order_before_payment', 'woocommerce_checkout_coupon_form', 10);
}
add_action('init', 'sarah_loz_remove_checkout_coupon_form');

/**
 * Additional styling for checkout fields and buttons
 */
function sarah_loz_checkout_specific_styles() {
    if (is_checkout()) {
        ?>
        <style>
            /* Improve checkout fields styling */
            .woocommerce-checkout .form-row label {
                @apply block text-right font-medium text-gray-700 mb-2;
            }
            
            .woocommerce-checkout .form-row .woocommerce-input-wrapper input, 
            .woocommerce-checkout .form-row .woocommerce-input-wrapper textarea,
            .woocommerce-checkout .form-row .woocommerce-input-wrapper select {
                @apply w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent;
                min-height: 45px;
            }
            
            /* Style checkout button */
            #place_order {
                @apply bg-primary text-white w-full text-center px-6 py-3 rounded-full shadow-md hover:bg-primary/90 transition text-lg font-medium cursor-pointer;
                min-height: 50px;
            }
            
            /* Style the order review section */
            #order_review {
                @apply bg-white p-6 rounded-lg shadow-lg;
            }
            
            #order_review_heading {
                @apply text-xl font-bold mb-4 text-right;
            }
            
            /* Style payment methods */
            #payment {
                @apply bg-transparent;
            }
            
            #payment .payment_methods {
                @apply border-gray-200 border-t border-b py-4;
            }
            
            #payment .payment_methods li {
                @apply mb-3 text-right;
            }
            
            #payment .payment_methods label {
                @apply font-medium cursor-pointer;
            }
            
            #payment .payment_box {
                @apply bg-light p-4 rounded-lg mt-2 text-right;
            }
            
            /* Force specific styling to override theme defaults */
            .woocommerce-checkout .form-row label {
                display: block !important;
                text-align: right !important;
                font-weight: 500 !important;
                color: #4a5568 !important;
                margin-bottom: 0.5rem !important;
            }
            
            .woocommerce-checkout .form-row .woocommerce-input-wrapper input, 
            .woocommerce-checkout .form-row .woocommerce-input-wrapper textarea,
            .woocommerce-checkout .form-row .woocommerce-input-wrapper select {
                width: 100% !important;
                padding: 0.75rem 1rem !important;
                border-radius: 0.5rem !important;
                border: 1px solid #e2e8f0 !important;
                background-color: #fff !important;
                min-height: 45px !important;
            }
            
            #place_order {
                width: 100% !important;
                background-color: #FF6B6B !important;
                color: white !important;
                padding: 0.75rem 1.5rem !important;
                border-radius: 9999px !important;
                font-weight: 500 !important;
                text-align: center !important;
                cursor: pointer !important;
                border: none !important;
                min-height: 50px !important;
                margin-top: 1rem !important;
            }
            
            /* Fix potential flexbox issues */
            .woocommerce-checkout .col2-set {
                display: block !important;
            }
            
            /* Ensure form is visible */
            .checkout.woocommerce-checkout {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            /* Handle select fields specifically */
            .woocommerce-checkout .select2-container--default .select2-selection--single {
                height: 45px !important;
                padding: 0.5rem !important;
                border-radius: 0.5rem !important;
                border: 1px solid #e2e8f0 !important;
            }
            
            .woocommerce-checkout .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 28px !important;
                text-align: right !important;
                padding-right: 0 !important;
            }
            
            .woocommerce-checkout .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 43px !important;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'sarah_loz_checkout_specific_styles', 110);

/**
 * Fix order-received endpoint showing checkout page
 */
function sarah_loz_force_thankyou_template() {
    // Check if we're on the order-received endpoint
    if (is_wc_endpoint_url('order-received')) {
        // Remove checkout hooks that might be causing the checkout form to display
        remove_all_actions('woocommerce_checkout_before_customer_details');
        remove_all_actions('woocommerce_checkout_after_customer_details');
        remove_all_actions('woocommerce_checkout_billing');
        remove_all_actions('woocommerce_checkout_shipping');
        remove_all_actions('woocommerce_checkout_before_order_review');
        remove_all_actions('woocommerce_checkout_order_review');
        remove_all_actions('woocommerce_checkout_after_order_review');
        
        // Make sure we have the correct order-received actions
        add_action('woocommerce_thankyou', 'woocommerce_order_details_table', 10);
        add_action('woocommerce_thankyou', 'woocommerce_order_details_table', 10);
        
        // Add direct template output as a fallback
        add_action('wp_head', 'sarah_loz_ensure_thankyou_content', 9999);
    }
}
add_action('template_redirect', 'sarah_loz_force_thankyou_template', 5);

/**
 * Emergency fallback to ensure thank you content is shown
 */
function sarah_loz_ensure_thankyou_content() {
    if (is_wc_endpoint_url('order-received')) {
        // Only run this if the page doesn't already have the thank you content
        ?>
        <script>
        jQuery(document).ready(function($) {
            if ($('.woocommerce-order').length === 0 && $('.woocommerce-checkout').length > 0) {
                // We're on the order-received endpoint but the checkout form is showing
                // Hide the checkout form
                $('.woocommerce-checkout').hide();
                $('.woocommerce-form-coupon-toggle').hide();
                
                // Try to get the order ID and key from the URL
                var url = window.location.href;
                var orderIdMatch = url.match(/order-received\/(\d+)/);
                var keyMatch = url.match(/key=([^&]*)/);
                
                if (orderIdMatch && orderIdMatch[1]) {
                    var orderId = orderIdMatch[1];
                    var key = keyMatch ? keyMatch[1] : '';
                    
                    // Create a container for the thank you content
                    var $thankYouContainer = $('<div class="woocommerce-order-received-container container mx-auto px-4 py-10 bg-white rounded-lg shadow-lg"></div>');
                    
                    // Add a thank you message with order number
                    $thankYouContainer.append('<div class="woocommerce-thankyou-order-received text-2xl font-bold text-center mb-8 text-primary">شكراً لطلبك. تم استلام طلبك بنجاح.</div>');
                    
                    // Add order number
                    $thankYouContainer.append('<p class="text-center mb-6">رقم الطلب: <strong>#' + orderId + '</strong></p>');
                    
                    // Add a simple order summary while we fetch details
                    var $orderSummary = $('<div class="woocommerce-order-overview woocommerce-thankyou-order-details order_details flex flex-wrap gap-4 mb-8"></div>');
                    
                    // Add order date
                    var today = new Date();
                    var date = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
                    $orderSummary.append('<div class="bg-light p-4 rounded-lg text-right flex-1"><span>تاريخ الطلب:</span><strong class="block mt-1">' + date + '</strong></div>');
                    
                    // Add order number again
                    $orderSummary.append('<div class="bg-light p-4 rounded-lg text-right flex-1"><span>رقم الطلب:</span><strong class="block mt-1">#' + orderId + '</strong></div>');
                    
                    // Add a payment method placeholder
                    $orderSummary.append('<div class="bg-light p-4 rounded-lg text-right flex-1"><span>طريقة الدفع:</span><strong class="block mt-1">الدفع عند الاستلام</strong></div>');
                    
                    // Add order summary to container
                    $thankYouContainer.append($orderSummary);
                    
                    // Add a loading indicator while we fetch details
                    var $loadingIndicator = $('<div class="text-center py-8"><div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent"></div><p class="mt-2">جاري تحميل تفاصيل الطلب...</p></div>');
                    $thankYouContainer.append($loadingIndicator);
                    
                    // Add the order details via AJAX
                    $.ajax({
                        url: '/wp-admin/admin-ajax.php',
                        type: 'POST',
                        data: {
                            action: 'sarah_loz_get_order_details',
                            order_id: orderId,
                            order_key: key,
                            security: '<?php echo wp_create_nonce('sarah-loz-order-details'); ?>'
                        },
                        success: function(response) {
                            // Remove loading indicator
                            $loadingIndicator.remove();
                            
                            if (response.success && response.data) {
                                // Add the order details
                                $thankYouContainer.append('<div class="woocommerce-order-details bg-white p-6 rounded-lg shadow-lg mb-8"><h2 class="text-xl font-bold mb-4 text-right">تفاصيل الطلب</h2>' + response.data + '</div>');
                            } else {
                                // Fallback content if API fails
                                $thankYouContainer.append(
                                    '<div class="woocommerce-order-details bg-white p-6 rounded-lg shadow-lg mb-8">' +
                                    '<h2 class="text-xl font-bold mb-4 text-right">تفاصيل الطلب</h2>' +
                                    '<p class="text-right mb-4">تم تسجيل طلبك بنجاح. سنتواصل معك قريباً بخصوص تفاصيل الطلب والشحن.</p>' +
                                    '<div class="flex justify-between items-center mt-6 pt-6 border-t border-gray-200">' +
                                    '<a href="/" class="bg-primary text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition-colors">العودة للمتجر</a>' +
                                    '<a href="/my-account/orders/" class="text-primary hover:underline">عرض الطلبات السابقة</a>' +
                                    '</div>' +
                                    '</div>'
                                );
                            }
                            
                            // Add customer support info
                            $thankYouContainer.append(
                                '<div class="bg-light p-6 rounded-lg text-center mb-8">' +
                                '<h3 class="font-bold mb-2">لديك استفسار؟</h3>' +
                                '<p class="mb-4">يمكنك التواصل معنا عبر البريد الإلكتروني أو الواتساب</p>' +
                                '<div class="flex justify-center gap-4">' +
                                '<a href="mailto:info@example.com" class="text-primary hover:underline"><i class="fas fa-envelope ml-1"></i> info@example.com</a>' +
                                '<a href="https://wa.me/966500000000" class="text-green-600 hover:underline"><i class="fab fa-whatsapp ml-1"></i> +966 50 000 0000</a>' +
                                '</div>' +
                                '</div>'
                            );
                            
                            // Add back to store button
                            $thankYouContainer.append(
                                '<div class="text-center">' +
                                '<a href="/" class="bg-primary text-white px-8 py-3 rounded-full hover:bg-opacity-90 transition-colors inline-block text-lg">العودة للمتجر</a>' +
                                '</div>'
                            );
                        },
                        error: function() {
                            // Remove loading indicator
                            $loadingIndicator.remove();
                            
                            // Fallback content if AJAX fails
                            $thankYouContainer.append(
                                '<div class="woocommerce-order-details bg-white p-6 rounded-lg shadow-lg mb-8">' +
                                '<h2 class="text-xl font-bold mb-4 text-right">تفاصيل الطلب</h2>' +
                                '<p class="text-right mb-4">تم تسجيل طلبك بنجاح. سنتواصل معك قريباً بخصوص تفاصيل الطلب والشحن.</p>' +
                                '<div class="flex justify-between items-center mt-6 pt-6 border-t border-gray-200">' +
                                '<a href="/" class="bg-primary text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition-colors">العودة للمتجر</a>' +
                                '<a href="/my-account/orders/" class="text-primary hover:underline">عرض الطلبات السابقة</a>' +
                                '</div>' +
                                '</div>'
                            );
                            
                            // Add customer support info
                            $thankYouContainer.append(
                                '<div class="bg-light p-6 rounded-lg text-center mb-8">' +
                                '<h3 class="font-bold mb-2">لديك استفسار؟</h3>' +
                                '<p class="mb-4">يمكنك التواصل معنا عبر البريد الإلكتروني أو الواتساب</p>' +
                                '<div class="flex justify-center gap-4">' +
                                '<a href="mailto:info@example.com" class="text-primary hover:underline"><i class="fas fa-envelope ml-1"></i> info@example.com</a>' +
                                '<a href="https://wa.me/966500000000" class="text-green-600 hover:underline"><i class="fab fa-whatsapp ml-1"></i> +966 50 000 0000</a>' +
                                '</div>' +
                                '</div>'
                            );
                            
                            // Add back to store button
                            $thankYouContainer.append(
                                '<div class="text-center">' +
                                '<a href="/" class="bg-primary text-white px-8 py-3 rounded-full hover:bg-opacity-90 transition-colors inline-block text-lg">العودة للمتجر</a>' +
                                '</div>'
                            );
                        }
                    });
                    
                    // Replace the entire woocommerce content with our thank you container
                    $('.woocommerce').html($thankYouContainer);
                    
                    // Apply page-wide styles
                    $('body').addClass('custom-thank-you-page');
                    $('main').addClass('py-10');
                }
            }
        });
        </script>
        <style>
        /* Apply these styles directly to ensure they work */
        body.custom-thank-you-page {
            background-color: #f9fafb;
        }
        
        .woocommerce-order-received-container {
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .woocommerce-thankyou-order-received {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: #FF6B6B;
        }
        
        .woocommerce-order-overview {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .woocommerce-order-overview > div {
            flex: 1;
            min-width: 200px;
            background-color: #f9fafb;
            padding: 1rem;
            border-radius: 0.5rem;
            text-align: right;
        }
        
        .woocommerce-order-overview strong {
            display: block;
            font-size: 1.125rem;
            font-weight: 700;
            margin-top: 0.25rem;
        }
        
        .woocommerce-order-details {
            margin-bottom: 2rem;
        }
        
        .woocommerce-order-details h2 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-align: right;
        }
        
        .bg-light {
            background-color: #f9fafb;
        }
        
        .text-primary {
            color: #FF6B6B;
        }
        
        .bg-primary {
            background-color: #FF6B6B;
        }
        
        /* Responsive fixes */
        @media (max-width: 640px) {
            .woocommerce-order-overview {
                display: block;
            }
            
            .woocommerce-order-overview > div {
                margin-bottom: 1rem;
            }
        }
        </style>
        <?php
    }
}

/**
 * AJAX handler to get order details
 */
function sarah_loz_ajax_get_order_details() {
    check_ajax_referer('sarah-loz-order-details', 'security');
    
    $order_id = isset($_POST['order_id']) ? absint($_POST['order_id']) : 0;
    $order_key = isset($_POST['order_key']) ? wc_clean($_POST['order_key']) : '';
    
    if ($order_id > 0) {
        $order = wc_get_order($order_id);
        
        if ($order && hash_equals($order->get_order_key(), $order_key)) {
            ob_start();
            
            // Generate custom order details HTML
            $items = $order->get_items();
            
            // Start building the order details
            echo '<table class="woocommerce-table woocommerce-table--order-details shop_table order_details w-full border-collapse mb-6">';
            echo '<thead class="bg-light">';
            echo '<tr>';
            echo '<th class="product-name text-right p-3 border border-gray-200">المنتج</th>';
            echo '<th class="product-total text-right p-3 border border-gray-200">المجموع</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            // Add order items
            foreach ($items as $item_id => $item) {
                $product = $item->get_product();
                $purchase_note = $product ? $product->get_purchase_note() : '';
                
                echo '<tr class="woocommerce-table__line-item order_item">';
                
                // Product name
                echo '<td class="product-name text-right p-3 border border-gray-200">';
                echo $item->get_name();
                
                // Item meta
                echo wc_display_item_meta($item);
                
                // Quantity
                echo '<strong class="product-quantity block mt-1">' . sprintf('&times; %s', $item->get_quantity()) . '</strong>';
                
                // Purchase Note
                if ($purchase_note) {
                    echo '<div class="woocommerce-purchase-note mt-2 text-sm text-gray-600">' . wpautop(do_shortcode($purchase_note)) . '</div>';
                }
                
                echo '</td>';
                
                // Product total
                echo '<td class="product-total text-right p-3 border border-gray-200">';
                echo $order->get_formatted_line_subtotal($item);
                echo '</td>';
                
                echo '</tr>';
            }
            
            echo '</tbody>';
            
            // Add order totals
            echo '<tfoot>';
            
            // Subtotal
            echo '<tr>';
            echo '<th scope="row" class="text-right p-3 border border-gray-200">المجموع الفرعي:</th>';
            echo '<td class="text-right p-3 border border-gray-200">' . $order->get_subtotal_to_display() . '</td>';
            echo '</tr>';
            
            // Shipping
            if ($order->get_shipping_method()) {
                echo '<tr>';
                echo '<th scope="row" class="text-right p-3 border border-gray-200">الشحن:</th>';
                echo '<td class="text-right p-3 border border-gray-200">' . $order->get_shipping_to_display() . '</td>';
                echo '</tr>';
            }
            
            // Tax (if showing)
            if (wc_tax_enabled()) {
                $tax_totals = $order->get_tax_totals();
                if ($tax_totals) {
                    foreach ($tax_totals as $code => $tax) {
                        echo '<tr>';
                        echo '<th scope="row" class="text-right p-3 border border-gray-200">' . esc_html($tax->label) . ':</th>';
                        echo '<td class="text-right p-3 border border-gray-200">' . wp_kses_post($tax->formatted_amount) . '</td>';
                        echo '</tr>';
                    }
                }
            }
            
            // Payment method
            echo '<tr>';
            echo '<th scope="row" class="text-right p-3 border border-gray-200">طريقة الدفع:</th>';
            echo '<td class="text-right p-3 border border-gray-200">' . $order->get_payment_method_title() . '</td>';
            echo '</tr>';
            
            // Total
            echo '<tr>';
            echo '<th scope="row" class="text-right p-3 border border-gray-200 font-bold">الإجمالي:</th>';
            echo '<td class="text-right p-3 border border-gray-200 font-bold">' . $order->get_formatted_order_total() . '</td>';
            echo '</tr>';
            
            echo '</tfoot>';
            echo '</table>';
            
            // Add billing and shipping addresses if available
            if ($order->get_billing_address_1() || $order->get_shipping_address_1()) {
                echo '<div class="woocommerce-customer-details mt-8">';
                echo '<h2 class="text-xl font-bold mb-4 text-right">عنوان الشحن</h2>';
                
                echo '<div class="flex flex-wrap gap-6">';
                
                // Billing address
                if ($order->get_billing_address_1()) {
                    echo '<div class="woocommerce-column woocommerce-column--billing-address flex-1 min-w-[300px]">';
                    echo '<h3 class="font-bold mb-2 text-right">عنوان الفاتورة</h3>';
                    echo '<address class="text-right bg-light p-4 rounded-lg not-italic">';
                    echo wp_kses_post($order->get_formatted_billing_address());
                    
                    if ($order->get_billing_phone()) {
                        echo '<br>الهاتف: ' . esc_html($order->get_billing_phone());
                    }
                    
                    if ($order->get_billing_email()) {
                        echo '<br>البريد الإلكتروني: ' . esc_html($order->get_billing_email());
                    }
                    
                    echo '</address>';
                    echo '</div>';
                }
                
                // Shipping address
                if ($order->get_shipping_address_1()) {
                    echo '<div class="woocommerce-column woocommerce-column--shipping-address flex-1 min-w-[300px]">';
                    echo '<h3 class="font-bold mb-2 text-right">عنوان الشحن</h3>';
                    echo '<address class="text-right bg-light p-4 rounded-lg not-italic">';
                    echo wp_kses_post($order->get_formatted_shipping_address());
                    echo '</address>';
                    echo '</div>';
                }
                
                echo '</div>';
                echo '</div>';
            }
            
            $content = ob_get_clean();
            
            wp_send_json_success($content);
        }
    }
    
    wp_send_json_error();
}
add_action('wp_ajax_sarah_loz_get_order_details', 'sarah_loz_ajax_get_order_details');
add_action('wp_ajax_nopriv_sarah_loz_get_order_details', 'sarah_loz_ajax_get_order_details');

/**
 * More aggressive template override for order-received endpoint
 */
function sarah_loz_override_order_received_template($template, $template_name, $args, $template_path, $default_path) {
    if ($template_name === 'checkout/thankyou.php') {
        // Always use the default WooCommerce template for the thank you page
        return WC()->plugin_path() . '/templates/checkout/thankyou.php';
    }
    
    return $template;
}
add_filter('wc_get_template', 'sarah_loz_override_order_received_template', 999, 5);

/**
 * Add body class to help target the order received page
 */
function sarah_loz_order_received_body_class($classes) {
    if (is_wc_endpoint_url('order-received')) {
        $classes[] = 'woocommerce-order-received-custom';
        
        // Remove checkout classes that might be causing conflicts
        $key = array_search('woocommerce-checkout', $classes);
        if ($key !== false) {
            unset($classes[$key]);
        }
    }
    return $classes;
}
add_filter('body_class', 'sarah_loz_order_received_body_class', 20);

/**
 * Force WooCommerce to recognize we're on the thank you page
 */
function sarah_loz_override_is_checkout($is_checkout) {
    if (is_wc_endpoint_url('order-received')) {
        // Force WooCommerce to use the thank you template instead of checkout
        global $wp;
        if (!empty($wp->query_vars['order-received'])) {
            return false; // This is NOT a checkout page, it's an order-received page
        }
    }
    return $is_checkout;
}
add_filter('woocommerce_is_checkout', 'sarah_loz_override_is_checkout', 9999);

/**
 * Favorites System
 */

/**
 * Add Endpoint for Favorites
 */
function sarah_loz_add_favorites_endpoint() {
    add_rewrite_endpoint('favorites', EP_ROOT | EP_PAGES);
}
add_action('init', 'sarah_loz_add_favorites_endpoint');

/**
 * Register favorites as a WooCommerce endpoint
 */
function sarah_loz_register_wc_favorites_endpoint() {
    // Add favorites to WooCommerce query vars
    add_filter('woocommerce_get_query_vars', function($vars) {
        $vars['favorites'] = 'favorites';
        return $vars;
    });
}
add_action('init', 'sarah_loz_register_wc_favorites_endpoint');

/**
 * Add query vars
 */
function sarah_loz_favorites_query_vars($vars) {
    $vars[] = 'favorites';
    return $vars;
}
add_filter('query_vars', 'sarah_loz_favorites_query_vars');

/**
 * Add Favorites to My Account menu
 */
function sarah_loz_add_favorites_menu_item($items) {
    // Add favorites item after the dashboard
    $new_items = array();
    
    // If items is empty for some reason, create a basic structure
    if (empty($items)) {
        return array(
            'dashboard' => 'لوحة التحكم',
            'favorites' => 'المفضلة',
            'customer-logout' => 'تسجيل الخروج'
        );
    }
    
    foreach ($items as $key => $value) {
        $new_items[$key] = $value;
        
        if ($key === 'dashboard') {
            $new_items['favorites'] = 'المفضلة';
        }
    }
    
    // If favorites wasn't added (maybe because dashboard wasn't in the menu),
    // add it after the first item
    if (!isset($new_items['favorites'])) {
        $first_key = array_key_first($new_items);
        $temp_items = array();
        
        $temp_items[$first_key] = $new_items[$first_key];
        $temp_items['favorites'] = 'المفضلة';
        
        // Add the rest of the items
        foreach ($new_items as $key => $value) {
            if ($key !== $first_key) {
                $temp_items[$key] = $value;
            }
        }
        
        $new_items = $temp_items;
    }
    
    // Log the menu items for debugging
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Sarah Loz Theme: Account menu items after adding favorites: ' . print_r($new_items, true));
    }
    
    return $new_items;
}
add_filter('woocommerce_account_menu_items', 'sarah_loz_add_favorites_menu_item', 999);

/**
 * Display Favorites Content
 */
function sarah_loz_favorites_content() {
    // Get the right template for the favorites endpoint
    if (is_wc_endpoint_url('favorites')) {
        // Include our dedicated favorites template
        wc_get_template('myaccount/favorites-endpoint.php');
    }
}
// Hook into the endpoint action
add_action('woocommerce_account_favorites_endpoint', 'sarah_loz_favorites_content');

/**
 * Set up favorites endpoint - add this early to override default content
 */
function sarah_loz_setup_account_favorites() {
    global $wp;
    
    if (isset($wp->query_vars['favorites'])) {
        // Remove dashboard content
        remove_action('woocommerce_account_content', 'woocommerce_account_content');
        // Add favorites content with higher priority
        add_action('woocommerce_account_content', 'sarah_loz_render_favorites', 5);
    }
}
add_action('template_redirect', 'sarah_loz_setup_account_favorites', 5);

/**
 * Render favorites content
 */
function sarah_loz_render_favorites() {
    echo '<div class="woocommerce-favorites-wrapper">';
    do_action('woocommerce_account_favorites_endpoint');
    echo '</div>';
}

/**
 * Add favorites icon to My Account menu
 */
function sarah_loz_add_favorites_menu_icon() {
    ?>
    <style>
    /* Enhanced styling for favorites menu item */
    .woocommerce-MyAccount-navigation-link--favorites a {
        position: relative;
        display: flex !important;
        align-items: center;
    }
    
    .woocommerce-MyAccount-navigation-link--favorites a:before {
        content: '\f004';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        margin-left: 8px;
        margin-right: 4px;
        font-size: 1.1em;
        color: var(--color-primary);
        transition: all 0.3s ease;
    }
    
    .woocommerce-MyAccount-navigation-link--favorites:hover a:before {
        transform: scale(1.2);
    }
    
    .woocommerce-MyAccount-navigation-link--favorites.is-active a:before {
        color: var(--color-primary);
    }
    
    /* Add a small badge for favorites count */
    .woocommerce-MyAccount-navigation-link--favorites .favorites-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: var(--color-primary);
        color: white;
        border-radius: 50%;
        font-size: 0.7em;
        width: 18px;
        height: 18px;
        position: absolute;
        top: 50%;
        left: 10px;
        transform: translateY(-50%);
        font-weight: bold;
    }
    
    /* Add some special styling to make it stand out */
    .woocommerce-MyAccount-navigation-link--favorites a {
        background-color: rgba(255, 107, 107, 0.05);
        border-right: 3px solid var(--color-primary);
    }
    
    .woocommerce-MyAccount-navigation-link--favorites:hover a {
        background-color: rgba(255, 107, 107, 0.1);
    }
    
    /* RTL specific adjustments */
    .rtl .woocommerce-MyAccount-navigation-link--favorites a:before {
        margin-right: 0;
        margin-left: 8px;
    }
    
    .rtl .woocommerce-MyAccount-navigation-link--favorites .favorites-count {
        right: 10px;
        left: auto;
    }
    
    .rtl .woocommerce-MyAccount-navigation-link--favorites a {
        border-right: none;
        border-left: 3px solid var(--color-primary);
    }
    </style>
    <script>
    jQuery(document).ready(function($) {
        // Remove any existing icons if they were added manually before
        $('li.woocommerce-MyAccount-navigation-link--favorites a i.fa-heart').remove();
        
        // Get favorites count
        var favoritesCount = <?php 
            $user_id = get_current_user_id();
            $favorites = get_user_meta($user_id, 'sarah_loz_favorites', true);
            echo is_array($favorites) ? count($favorites) : 0;
        ?>;
        
        // Add count badge if there are favorites
        if (favoritesCount > 0) {
            $('li.woocommerce-MyAccount-navigation-link--favorites a').append(
                '<span class="favorites-count">' + favoritesCount + '</span>'
            );
        }
        
        // Highlight the favorites tab if we're on that page
        if (window.location.href.indexOf('/favorites') > -1) {
            $('.woocommerce-MyAccount-navigation-link--favorites').addClass('is-active');
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'sarah_loz_add_favorites_menu_icon');

/**
 * Add to favorites AJAX handler
 */
function sarah_loz_toggle_favorite() {
    // Check for nonce and user being logged in
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sarah_loz_favorite_nonce')) {
        wp_send_json_error(['message' => 'خطأ في التحقق الأمني']);
        exit;
    }
    
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'يجب تسجيل الدخول أولا']);
        exit;
    }
    
    // Get product ID
    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    
    if ($product_id < 1) {
        wp_send_json_error(['message' => 'معرف غير صالح']);
        exit;
    }
    
    // Check if post exists
    $post = get_post($product_id);
    if (!$post) {
        wp_send_json_error(['message' => 'العنصر غير موجود']);
        exit;
    }
    
    // Get current user
    $user_id = get_current_user_id();
    
    // Get current favorites
    $favorites = get_user_meta($user_id, 'sarah_loz_favorites', true);
    
    if (!is_array($favorites)) {
        $favorites = [];
    }
    
    // Log the current favorites
    error_log('User ID: ' . $user_id . ' | Current favorites: ' . print_r($favorites, true));
    
    // Check if product is already in favorites
    $index = array_search($product_id, $favorites);
    
    if ($index !== false) {
        // Remove from favorites
        unset($favorites[$index]);
        $favorites = array_values($favorites); // Re-index array
        $status = 'removed';
        $message = 'تمت إزالة العنصر من المفضلة';
    } else {
        // Add to favorites
        $favorites[] = $product_id;
        $status = 'added';
        $message = 'تمت إضافة العنصر إلى المفضلة';
    }
    
    // Save updated favorites
    $update_result = update_user_meta($user_id, 'sarah_loz_favorites', array_values($favorites));
    
    // Log the update result
    error_log('Update result: ' . ($update_result ? 'success' : 'failed') . ' | New favorites: ' . print_r($favorites, true));
    
    wp_send_json_success([
        'status' => $status,
        'message' => $message,
        'count' => count($favorites),
        'favorites' => $favorites
    ]);
    exit;
}
add_action('wp_ajax_sarah_loz_toggle_favorite', 'sarah_loz_toggle_favorite');
add_action('wp_ajax_nopriv_sarah_loz_toggle_favorite', function() {
    wp_send_json_error(['message' => 'يجب تسجيل الدخول لإضافة منتج إلى المفضلة']);
});

/**
 * Check if a product is in the user's favorites
 */
function sarah_loz_is_favorite($product_id) {
    if (!is_user_logged_in()) {
        return false;
    }
    
    $user_id = get_current_user_id();
    $favorites = get_user_meta($user_id, 'sarah_loz_favorites', true);
    
    if (!is_array($favorites)) {
        return false;
    }
    
    return in_array($product_id, $favorites);
}

/**
 * Get all favorite products for a user
 */
function sarah_loz_get_favorite_products($user_id = 0) {
    if ($user_id === 0) {
        if (!is_user_logged_in()) {
            return [];
        }
        $user_id = get_current_user_id();
    }
    
    $favorites = get_user_meta($user_id, 'sarah_loz_favorites', true);
    
    // Make sure we're working with an array
    if (!is_array($favorites)) {
        $favorites = [];
    }
    
    // Filter out any invalid IDs (posts that don't exist anymore)
    if (!empty($favorites)) {
        foreach ($favorites as $key => $id) {
            if (!get_post($id)) {
                unset($favorites[$key]);
            }
        }
        
        // Re-index the array
        $favorites = array_values($favorites);
        
        // Update user meta to remove invalid IDs
        update_user_meta($user_id, 'sarah_loz_favorites', $favorites);
    }
    
    return $favorites;
}

/**
 * Add favorite button to single product template
 */
function sarah_loz_add_favorite_button() {
    global $product;
    
    if (!$product) {
        return;
    }
    
    $product_id = $product->get_id();
    $is_favorite = sarah_loz_is_favorite($product_id);
    
    $active_class = $is_favorite ? 'bg-primary text-white' : 'bg-accent text-dark';
    $active_text = $is_favorite ? 'إزالة من المفضلة' : 'أضف للمفضلة';
    $active_icon = $is_favorite ? 'fas' : 'far'; // Use solid heart for favorites, outline for non-favorites
    
    echo '<button type="button" class="sarah-loz-toggle-favorite ' . $active_class . ' px-6 py-3 rounded-full text-lg shadow-lg hover:bg-opacity-90 transition flex-grow md:flex-grow-0" data-product-id="' . esc_attr($product_id) . '" data-nonce="' . wp_create_nonce('sarah_loz_favorite_nonce') . '">';
    echo '<i class="' . $active_icon . ' fa-heart ml-2"></i>';
    echo $active_text;
    echo '</button>';
}
add_action('woocommerce_after_add_to_cart_button', 'sarah_loz_add_favorite_button', 20);

/**
 * Add favorite button to product cards in loops
 */
function sarah_loz_add_favorite_button_to_loop($html, $product) {
    if (!$product) {
        return $html;
    }
    
    $product_id = $product->get_id();
    $is_favorite = sarah_loz_is_favorite($product_id);
    
    $active_class = $is_favorite ? 'text-primary' : 'text-gray-400';
    $active_icon = $is_favorite ? 'fas' : 'far'; // Use solid heart for favorites, outline for non-favorites
    
    $favorite_button = '<button type="button" class="sarah-loz-toggle-favorite-loop ml-2 text-lg ' . $active_class . ' hover:text-primary transition" data-product-id="' . esc_attr($product_id) . '" data-nonce="' . wp_create_nonce('sarah_loz_favorite_nonce') . '">';
    $favorite_button .= '<i class="' . $active_icon . ' fa-heart"></i>';
    $favorite_button .= '</button>';
    
    return $html . $favorite_button;
}
add_filter('woocommerce_loop_add_to_cart_link', 'sarah_loz_add_favorite_button_to_loop', 20, 2);

/**
 * Add JavaScript for favorites functionality
 */
function sarah_loz_favorites_scripts() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Toggle favorite on single product page
        $(document).on('click', '.sarah-loz-toggle-favorite', function() {
            var $this = $(this);
            var productId = $this.data('product-id');
            var nonce = $this.data('nonce');
            
            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {
                    action: 'sarah_loz_toggle_favorite',
                    product_id: productId,
                    nonce: nonce
                },
                beforeSend: function() {
                    $this.prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        // Update button appearance
                        if (response.data.status === 'added') {
                            $this.removeClass('bg-accent text-dark').addClass('bg-primary text-white');
                            $this.find('i').removeClass('far').addClass('fas');
                            $this.html('<i class="fas fa-heart ml-2"></i> إزالة من المفضلة');
                        } else {
                            $this.removeClass('bg-primary text-white').addClass('bg-accent text-dark');
                            $this.find('i').removeClass('fas').addClass('far');
                            $this.html('<i class="far fa-heart ml-2"></i> أضف للمفضلة');
                        }
                        
                        // Show notification
                        $('<div class="sarah-loz-notification bg-green-500 text-white px-4 py-2 rounded-lg fixed top-4 left-4 z-50 shadow-lg">')
                            .text(response.data.message)
                            .appendTo('body')
                            .delay(3000)
                            .fadeOut(400, function() {
                                $(this).remove();
                            });
                    } else {
                        // Show error
                        $('<div class="sarah-loz-notification bg-red-500 text-white px-4 py-2 rounded-lg fixed top-4 left-4 z-50 shadow-lg">')
                            .text(response.data.message)
                            .appendTo('body')
                            .delay(3000)
                            .fadeOut(400, function() {
                                $(this).remove();
                            });
                    }
                },
                error: function() {
                    // Show error
                    $('<div class="sarah-loz-notification bg-red-500 text-white px-4 py-2 rounded-lg fixed top-4 left-4 z-50 shadow-lg">')
                        .text('حدث خطأ. يرجى المحاولة مرة أخرى.')
                        .appendTo('body')
                        .delay(3000)
                        .fadeOut(400, function() {
                            $(this).remove();
                        });
                },
                complete: function() {
                    $this.prop('disabled', false);
                }
            });
        });
        
        // Toggle favorite on product loops
        $(document).on('click', '.sarah-loz-toggle-favorite-loop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $this = $(this);
            var productId = $this.data('product-id');
            var nonce = $this.data('nonce');
            
            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {
                    action: 'sarah_loz_toggle_favorite',
                    product_id: productId,
                    nonce: nonce
                },
                beforeSend: function() {
                    $this.prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        // Update all instances of this product's favorite button
                        $('.sarah-loz-toggle-favorite-loop[data-product-id="' + productId + '"]').each(function() {
                            var $btn = $(this);
                            
                            if (response.data.status === 'added') {
                                $btn.removeClass('text-gray-400').addClass('text-primary');
                                $btn.find('i').removeClass('far').addClass('fas');
                            } else {
                                $btn.removeClass('text-primary').addClass('text-gray-400');
                                $btn.find('i').removeClass('fas').addClass('far');
                            }
                        });
                        
                        // Show notification
                        $('<div class="sarah-loz-notification bg-green-500 text-white px-4 py-2 rounded-lg fixed top-4 left-4 z-50 shadow-lg">')
                            .text(response.data.message)
                            .appendTo('body')
                            .delay(3000)
                            .fadeOut(400, function() {
                                $(this).remove();
                            });
                    } else {
                        // Show error
                        $('<div class="sarah-loz-notification bg-red-500 text-white px-4 py-2 rounded-lg fixed top-4 left-4 z-50 shadow-lg">')
                            .text(response.data.message)
                            .appendTo('body')
                            .delay(3000)
                            .fadeOut(400, function() {
                                $(this).remove();
                            });
                    }
                },
                error: function() {
                    // Show error
                    $('<div class="sarah-loz-notification bg-red-500 text-white px-4 py-2 rounded-lg fixed top-4 left-4 z-50 shadow-lg">')
                        .text('حدث خطأ. يرجى المحاولة مرة أخرى.')
                        .appendTo('body')
                        .delay(3000)
                        .fadeOut(400, function() {
                            $(this).remove();
                        });
                },
                complete: function() {
                    $this.prop('disabled', false);
                }
            });
        });
        
        // Remove from favorites on the favorites page
        $(document).on('click', '.sarah-loz-remove-favorite', function() {
            var $this = $(this);
            var productId = $this.data('product-id');
            var nonce = $this.data('nonce');
            
            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {
                    action: 'sarah_loz_toggle_favorite',
                    product_id: productId,
                    nonce: nonce
                },
                beforeSend: function() {
                    $this.prop('disabled', true);
                },
                success: function(response) {
                    if (response.success && response.data.status === 'removed') {
                        // Remove the product card from favorites page
                        $this.closest('.favorite-product-card').fadeOut(400, function() {
                            $(this).remove();
                            
                            // Check if there are any favorites left
                            if ($('.favorite-product-card').length === 0) {
                                $('.favorites-empty').removeClass('hidden');
                            }
                        });
                        
                        // Show notification
                        $('<div class="sarah-loz-notification bg-green-500 text-white px-4 py-2 rounded-lg fixed top-4 left-4 z-50 shadow-lg">')
                            .text(response.data.message)
                            .appendTo('body')
                            .delay(3000)
                            .fadeOut(400, function() {
                                $(this).remove();
                            });
                    }
                },
                error: function() {
                    // Show error
                    $('<div class="sarah-loz-notification bg-red-500 text-white px-4 py-2 rounded-lg fixed top-4 left-4 z-50 shadow-lg">')
                        .text('حدث خطأ. يرجى المحاولة مرة أخرى.')
                        .appendTo('body')
                        .delay(3000)
                        .fadeOut(400, function() {
                            $(this).remove();
                        });
                },
                complete: function() {
                    $this.prop('disabled', false);
                }
            });
        });
    });
    </script>
    
    <style>
    /* Favorites notification */
    .sarah-loz-notification {
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    /* Favorites button animation */
    .sarah-loz-toggle-favorite i,
    .sarah-loz-toggle-favorite-loop i,
    .sarah-loz-remove-favorite i {
        transition: transform 0.3s ease;
    }
    
    .sarah-loz-toggle-favorite:hover i,
    .sarah-loz-toggle-favorite-loop:hover i,
    .sarah-loz-remove-favorite:hover i {
        transform: scale(1.2);
    }
    
    /* Empty favorites message */
    .favorites-empty {
        text-align: center;
        padding: 3rem 0;
    }
    
    .favorites-empty i {
        font-size: 3rem;
        color: #e2e8f0;
        margin-bottom: 1rem;
    }
    
    /* Product loop favorite button */
    .sarah-loz-toggle-favorite-loop {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.8);
        transition: all 0.3s ease;
    }
    
    .sarah-loz-toggle-favorite-loop:hover {
        background-color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    </style>
    <?php
}
add_action('wp_footer', 'sarah_loz_favorites_scripts');

/**
 * Add styling for the favorites page
 */
function sarah_loz_favorites_page_styles() {
    if (is_wc_endpoint_url('favorites')) {
        ?>
        <style>
            /* Favorites page container */
            .woocommerce-favorites {
                @apply bg-white p-6 rounded-lg shadow-lg;
            }
            
            /* Page title */
            .woocommerce-favorites-title {
                @apply text-2xl font-bold text-dark mb-6 text-right;
            }
            
            /* Products grid */
            .favorites-products-grid {
                @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6;
            }
            
            /* Product card */
            .favorite-product-card {
                @apply bg-white rounded-lg shadow-lg overflow-hidden transition transform hover:-translate-y-1 hover:shadow-xl border border-gray-100;
                display: flex !important;
                flex-direction: column !important;
            }
            
            /* Product image container */
            .favorite-product-card .product-image {
                @apply h-48 bg-primary/10 relative overflow-hidden;
            }
            
            /* Product content container */
            .favorite-product-card .product-content {
                @apply p-4 flex-grow flex flex-col;
            }
            
            /* Product title */
            .favorite-product-card .product-title {
                @apply text-lg font-bold text-dark mb-2 text-right;
            }
            
            /* Product price */
            .favorite-product-card .product-price {
                @apply text-primary font-bold text-right;
            }
            
            /* Actions container */
            .favorite-product-card .product-actions {
                @apply flex justify-between items-center mt-auto pt-4;
            }
            
            /* Product actions */
            .favorite-product-card .product-actions .button {
                @apply bg-primary text-white px-4 py-2 rounded-full text-sm hover:bg-opacity-90 transition inline-block;
            }
            
            /* Remove button */
            .favorite-product-card .sarah-loz-remove-favorite {
                @apply text-gray-400 hover:text-primary transition w-10 h-10 flex items-center justify-center;
            }
            
            /* Debug info box */
            .woocommerce-favorites .bg-yellow-100 {
                direction: ltr;
                text-align: left;
            }
            
            /* Empty favorites message */
            .favorites-empty {
                @apply text-center py-10;
            }
            
            .favorites-empty i {
                @apply text-6xl text-gray-300 mb-4;
            }
            
            /* Fix for excessive white space */
            .woocommerce-MyAccount-content {
                overflow: hidden !important;
            }
            
            /* Fix RTL layout for cards */
            .favorite-product-card * {
                direction: rtl !important;
            }
            
            /* Fix line-clamp utility */
            .line-clamp-2 {
                overflow: hidden;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
            }
            
            /* Force direct styles for debugging box */
            .bg-yellow-100 {
                background-color: #fef9c3 !important;
                border: 1px solid #fbbf24 !important;
                color: #92400e !important;
                padding: 0.75rem 1rem !important;
                border-radius: 0.375rem !important;
                margin-bottom: 1rem !important;
            }
            
            .bg-yellow-100 h3 {
                font-weight: bold !important;
                margin-bottom: 0.5rem !important;
            }
            
            /* Force card layout */
            @media (min-width: 768px) {
                .favorites-products-grid {
                    display: grid !important;
                    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                    gap: 1.5rem !important;
                }
            }
            
            @media (min-width: 1024px) {
                .favorites-products-grid {
                    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                }
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'sarah_loz_favorites_page_styles');

/**
 * Enhanced favorites page styling
 */
function sarah_loz_enhanced_favorites_page_styles() {
    if (!is_wc_endpoint_url('favorites')) {
        return;
    }
    ?>
    <style>
    /* Enhanced Favorites Page Styling */
    .woocommerce-favorites {
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .woocommerce-favorites-title {
        font-size: 1.8rem;
        color: var(--color-dark);
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--color-primary);
        position: relative;
    }
    
    .woocommerce-favorites-title:before {
        content: '\f004';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        color: var(--color-primary);
        margin-left: 8px;
        font-size: 1.2em;
    }
    
    /* Empty state */
    .favorites-empty {
        text-align: center;
        padding: 40px 20px;
        background-color: rgba(255, 107, 107, 0.05);
        border-radius: 8px;
        margin: 20px 0;
    }
    
    .favorites-empty i.far.fa-heart {
        font-size: 3rem;
        color: var(--color-primary);
        margin-bottom: 15px;
        opacity: 0.7;
    }
    
    /* Products grid */
    .favorites-products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    
    .favorite-product-card {
        border: 1px solid #eee;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        background-color: #fff;
        position: relative;
    }
    
    .favorite-product-card:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transform: translateY(-5px);
    }
    
    .favorite-product-card .product-image {
        height: 220px;
        position: relative;
        background-color: #f9f9f9;
    }
    
    .favorite-product-card .product-content {
        padding: 15px;
    }
    
    .favorite-product-card .product-title {
        font-size: 1.1rem;
        margin-bottom: 10px;
        font-weight: bold;
        min-height: 2.4em;
    }
    
    .favorite-product-card .product-price {
        font-size: 1.1rem;
        font-weight: bold;
        color: var(--color-primary);
        margin-bottom: 15px;
    }
    
    .favorite-product-card .product-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .favorite-product-card .button {
        background-color: var(--color-primary);
        color: white;
        padding: 8px 15px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        font-weight: bold;
        transition: all 0.3s ease;
    }
    
    .favorite-product-card .button:hover {
        background-color: var(--color-dark);
    }
    
    .favorite-product-card .sarah-loz-remove-favorite {
        background: none;
        border: none;
        color: #888;
        cursor: pointer;
        font-size: 1.2rem;
        padding: 5px 10px;
        transition: all 0.3s ease;
    }
    
    .favorite-product-card .sarah-loz-remove-favorite:hover {
        color: var(--color-primary);
    }
    
    /* Category headers */
    .woocommerce-favorites h3 {
        position: relative;
        display: inline-block;
        padding: 8px 15px 8px 0;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .favorites-products-grid {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        }
    }
    
    @media (max-width: 576px) {
        .favorites-products-grid {
            grid-template-columns: 1fr;
        }
    }
    
    /* Loading animation */
    .sarah-loz-favorites-loading {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }
    
    .sarah-loz-favorites-loading:after {
        content: '';
        width: 30px;
        height: 30px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid var(--color-primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    </style>
    <script>
    jQuery(document).ready(function($) {
        // Add loading animation when removing items
        $('.sarah-loz-remove-favorite').on('click', function() {
            var card = $(this).closest('.favorite-product-card');
            card.append('<div class="sarah-loz-favorites-loading"></div>');
            
            // Update count in menu when item is removed
            var currentCount = parseInt($('.woocommerce-MyAccount-navigation-link--favorites .favorites-count').text());
            if (currentCount > 1) {
                $('.woocommerce-MyAccount-navigation-link--favorites .favorites-count').text(currentCount - 1);
            } else {
                $('.woocommerce-MyAccount-navigation-link--favorites .favorites-count').remove();
            }
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'sarah_loz_enhanced_favorites_page_styles');
