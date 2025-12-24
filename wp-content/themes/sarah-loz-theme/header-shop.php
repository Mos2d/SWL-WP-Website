<!DOCTYPE html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
    <style>
        body {
            font-family: "Harmattan", sans-serif;
            background-color: #f9f9f6;
            background-image: radial-gradient(#1ddede 2px, transparent 2px),
                radial-gradient(#f8c709 2px, transparent 2px);
            background-size: 40px 40px;
            background-position: 0 0, 20px 20px;
        }
        .rounded-bubble {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
        }
        .floating {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        /* Header background styles */
        header.site-header {
            background:  #ec0a74;
        }
    </style>
    
    <script type="text/javascript">
        /* Ensure WooCommerce cart fragments are properly handled */
        window.addEventListener('load', function() {
            if (typeof jQuery !== 'undefined' && typeof wc_cart_fragments_params !== 'undefined') {
                // Force refresh fragments on page load
                jQuery(document.body).trigger('wc_fragment_refresh');
                
                // Re-bind fragment refresh events
                jQuery(document.body).on('added_to_cart removed_from_cart', function() {
                    jQuery(document.body).trigger('wc_fragment_refresh');
                    
                    // Ensure the cart counts are updated
                    setTimeout(function() {
                        if (typeof jQuery.ajax === 'function') {
                            jQuery.ajax({
                                url: woocommerce_params.ajax_url,
                                type: 'POST',
                                data: {
                                    action: 'sarah_loz_get_cart_count'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        jQuery('.cart-count').text(response.data.count);
                                    }
                                }
                            });
                        }
                    }, 100);
                });
            }
        });
    </script>
</head>
<body <?php body_class('antialiased woocommerce'); ?>>
<?php wp_body_open(); ?>

<!-- Header/Navigation -->
<header class="site-header bg-gradient-to-l from-primary to-accent sticky top-0 z-50 shadow-lg">
    <div class="container mx-auto px-4 py-3">
        <nav class="flex flex-wrap items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center">
                    <div class="w-12 h-12 bg-light rounded-full flex items-center justify-center mr-2 shadow-md">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/SWL.png" alt="سارة ولوز" class="w-80 h-80 object-contain">
                    </div>
                    <span class="text-white text-xl font-bold"><?php bloginfo('name'); ?></span>
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <div class="lg:hidden">
                <button id="menuButton" class="text-white focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>

            <!-- Desktop Navigation -->
            <div id="navMenu" class="hidden w-full lg:flex lg:w-auto lg:items-center mt-2 lg:mt-0">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'flex flex-col lg:flex-row space-y-2 lg:space-y-0 lg:space-x-4 lg:rtl:space-x-reverse',
                    'fallback_cb' => false,
                    'link_before' => '<span class="text-white hover:text-light block py-2 px-3 rounded-full hover:bg-primary transition">',
                    'link_after' => '</span>',
                ));
                ?>
                
                <!-- Mobile Cart/Account Icons -->
                <?php if (class_exists('WooCommerce')) : ?>
                <div class="flex items-center justify-center space-x-4 rtl:space-x-reverse mt-4 lg:hidden">
                    <a href="<?php echo wc_get_cart_url(); ?>" class="text-white hover:text-light transition relative px-3 cart-contents">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span class="cart-count absolute -top-2 -right-0 bg-bright text-dark text-xs w-5 h-5 rounded-full flex items-center justify-center">
                            <?php echo WC()->cart->get_cart_contents_count(); ?>
                        </span>
                    </a>
                    
                    <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" class="bg-light text-primary py-2 px-4 rounded-full hover:bg-white transition shadow-md">
                        <i class="fas fa-user mr-1"></i> 
                        <?php echo is_user_logged_in() ? 'حسابي' : 'تسجيل الدخول'; ?>
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Cart/Account Icons -->
            <div class="hidden lg:flex items-center space-x-4 rtl:space-x-reverse">
                <?php if (class_exists('WooCommerce')) : ?>
                    <a href="<?php echo wc_get_cart_url(); ?>" class="text-white hover:text-light transition relative px-3 cart-contents">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span class="cart-count absolute -top-2 -right-0 bg-bright text-dark text-xs w-5 h-5 rounded-full flex items-center justify-center">
                            <?php echo WC()->cart->get_cart_contents_count(); ?>
                        </span>
                    </a>
                    
                    <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" class="bg-light text-primary py-2 px-4 rounded-full hover:bg-white transition shadow-md">
                        <i class="fas fa-user mr-1"></i> 
                        <?php echo is_user_logged_in() ? 'حسابي' : 'تسجيل الدخول'; ?>
                    </a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>

<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const menuButton = document.getElementById("menuButton");
        const navMenu = document.getElementById("navMenu");

        if (menuButton && navMenu) {
            menuButton.addEventListener("click", function() {
                navMenu.classList.toggle("hidden");
            });
        }
    });
</script>

<!-- WooCommerce Breadcrumb -->
<?php if (function_exists('woocommerce_breadcrumb') && !is_shop() && !is_product_category() && !is_product()) : ?>
<div class="bg-light py-4 shadow-sm">
    <div class="container mx-auto px-4">
        <?php woocommerce_breadcrumb(); ?>
    </div>
</div>
<?php endif; ?>

<main id="content" class="site-content woocommerce-content py-8"><?php // Main content will be added here ?>
</main> 