<!DOCTYPE html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
    <style>
        /* Preloader styles */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #f9f9f6;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease-in-out;
            background-image: radial-gradient(#1ddede 1px, transparent 1px),
                radial-gradient(#f8c709 1px, transparent 1px);
            background-size: 30px 30px;
            background-position: 0 0, 15px 15px;
        }
        
        .preloader.fade-out {
            opacity: 0;
        }
        
        .preloader-content {
            width: 120px;
            height: 120px;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .balloon {
            width: 250px;
            height: 250px;
            background: url('<?php echo get_template_directory_uri(); ?>/assets/images/SWL.png') no-repeat center;
            background-size: contain;
            position: relative;
            animation: float-balloon 2s ease-in-out infinite;
        }

        .balloon:before {
            content: "جاري التحميل...";
            position: absolute;
            bottom: -30px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 14px;
            color: #ec0a74;
            white-space: nowrap;
            font-family: "Harmattan", sans-serif;
        }

        .star {
            position: absolute;
            background: #f8c709;
            width: 15px;
            height: 15px;
            clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
            animation: twinkle 1s ease-in-out infinite;
        }

        .star:nth-child(1) { top: 20%; left: 20%; animation-delay: 0.3s; }
        .star:nth-child(2) { top: 30%; right: 20%; animation-delay: 0.5s; }
        .star:nth-child(3) { bottom: 30%; left: 30%; animation-delay: 0.7s; }
        .star:nth-child(4) { bottom: 20%; right: 30%; animation-delay: 0.1s; }

        @keyframes float-balloon {
            0%, 100% { transform: translateY(0) rotate(5deg); }
            50% { transform: translateY(-20px) rotate(-5deg); }
        }

        @keyframes twinkle {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.7); }
        }
        
        body {
            font-family: "Harmattan", sans-serif;
            background-color: #f9f9f6;
            /* Create a stacking context so the ::before element stays behind text */
            position: relative;
            z-index: 0;
        }

        /* Create the dots on a separate layer */
        /* body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            
            background-image: radial-gradient(#1ddede 2px, transparent 2px),
                            radial-gradient(#f8c709 2px, transparent 2px);
            background-size: 40px 40px;
            background-position: 0 0, 20px 20px;
            
            filter: blur(4px);
        } */

        .rounded-bubble {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
        }
        .cloud-shape {
            border-radius: 50px;
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

        /* Smart Navbar Styles */
        header.site-header {
            transition: transform 0.3s ease-in-out;
        }
        
        /* Class added by JS to hide the navbar */
        header.site-header.nav-hidden {
            transform: translateY(-100%);
        }

        /* Fix overlap with WordPress Admin Bar on Desktop */
        @media (min-width: 783px) {
            body.admin-bar header.site-header {
                top: 32px !important;
            }
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
<body <?php body_class(); ?>>
    <div class="preloader">
        <div class="preloader-content">
            <div class="balloon"></div>
            <div class="star"></div>
            <div class="star"></div>
            <div class="star"></div>
            <div class="star"></div>
        </div>
    </div>
    <script>
        window.addEventListener('load', function() {
            const preloader = document.querySelector('.preloader');
            preloader.classList.add('fade-out');
            setTimeout(() => {
                preloader.style.display = 'none';
            }, 500);
        });
    </script>

<?php wp_body_open(); ?>

<!-- Header/Navigation -->
<header class="site-header fixed top-0 w-full z-50 shadow-lg" style="background: #007cba !important;">
    <div class="container mx-auto px-4 py-3">
        <nav class="flex flex-wrap items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="<?php echo sarah_loz_get_logo_redirect_url(); ?>" 
                   <?php if (!sarah_loz_is_bot_or_crawler()) : ?>onclick="handleLogoClick(); return false;"<?php endif; ?>
                   class="flex items-center group" title="<?php echo sarah_loz_get_logo_tooltip_text(); ?>">
                    <div class="w-12 h-12 bg-light rounded-full flex items-center justify-center mr-2 shadow-md group-hover:scale-105 transition-transform">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/SWL.png" alt="سارة ولوز" class="w-80 h-80 object-contain">
                    </div>
                    <div class="flex flex-col">
                        <span class="text-white text-xl font-bold"><?php bloginfo('name'); ?></span>
                        <?php 
                        $selected_age_group = function_exists('sarah_loz_get_selected_age_group') ? sarah_loz_get_selected_age_group() : null;
                        $current_age_data = function_exists('sarah_loz_get_current_age_group_data') ? sarah_loz_get_current_age_group_data() : null;
                        
                        if ($selected_age_group && $current_age_data && !sarah_loz_is_bot_or_crawler()) : ?>
                        <?php elseif (!sarah_loz_is_bot_or_crawler()) : ?>
                        <?php endif; ?>
                    </div>
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
                        <span class="cart-count absolute -top-2 -right-0 bg-accent text-dark text-xs w-5 h-5 rounded-full flex items-center justify-center">
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

        let lastScrollTop = 0;
        const header = document.querySelector('.site-header');
        
        window.addEventListener('scroll', function() {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            // Prevent hiding when at the very top or bouncing (negative scroll on iOS)
            if (scrollTop <= 0) {
                header.classList.remove('nav-hidden');
                lastScrollTop = 0;
                return;
            }

            // Logic: If scrolling down AND passed the header height -> Hide
            //        If scrolling up -> Show
            if (scrollTop > lastScrollTop && scrollTop > header.offsetHeight) {
                header.classList.add('nav-hidden');
            } else {
                header.classList.remove('nav-hidden');
            }
            
            lastScrollTop = scrollTop;
        }, { passive: true });
    });
    
    // Logo click handler - similar to age group indicator functionality
    function handleLogoClick() {
        // Check if user has selected an age group
        const hasAgeGroup = document.cookie.includes('swl_selected_age_group') || 
                           (typeof sessionStorage !== 'undefined' && sessionStorage.getItem('swl_selected_age_group'));
        
        if (hasAgeGroup) {
            // If age group is selected, clear it and redirect to age selection
            if (confirm('هل تريد تغيير الفئة العمرية؟')) {
                clearAgeGroupAndRedirect();
            }
        } else {
            // If no age group selected, go to age selection page
            const ageSelectionUrl = '<?php echo sarah_loz_get_age_selection_page_url(); ?>';
            if (ageSelectionUrl) {
                window.location.href = ageSelectionUrl;
            } else {
                window.location.href = '<?php echo home_url(); ?>';
            }
        }
    }
    
    // Clear age group via AJAX and redirect
    function clearAgeGroupAndRedirect() {
        // Clear age group via AJAX
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'clear_age_group'
            })
        })
        .then(response => response.json())
        .then(data => {
            // Clear cookie
            document.cookie = 'swl_selected_age_group=; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            
            // Clear session storage if available
            if (typeof sessionStorage !== 'undefined') {
                sessionStorage.removeItem('swl_selected_age_group');
            }
            
            // Redirect to age selection page
            const ageSelectionUrl = '<?php echo sarah_loz_get_age_selection_page_url(); ?>';
            if (ageSelectionUrl) {
                window.location.href = ageSelectionUrl;
            } else {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error clearing age group:', error);
            // Fallback: just redirect to home
            window.location.href = '<?php echo home_url(); ?>';
        });
    }
</script> 

<main id="content" class="site-content"><?php // Main content will be added here ?>
