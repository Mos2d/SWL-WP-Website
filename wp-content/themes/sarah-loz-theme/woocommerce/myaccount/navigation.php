<?php
/**
 * My Account navigation
 *
 * This template overrides /woocommerce/templates/myaccount/navigation.php
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

if (!defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_before_account_navigation');
?>

<nav class="woocommerce-MyAccount-navigation bg-white rounded-lg shadow-lg p-4">
    <!-- Mobile Toggle Button - Only visible on mobile -->
    <button type="button" class="mobile-nav-toggle md:hidden mb-4 cursor-pointer w-full">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold">لوحة التحكم</h2>
        </div>
    </button>
    
    <!-- Navigation Content - Hidden by default on mobile, always visible on desktop -->
    <div class="nav-content md:block mobile-hidden">
        <div class="navigation-header pb-4 mb-4 border-b border-gray-200 text-right">
            <h2 class="text-xl font-bold mb-2 hidden md:block">لوحة التحكم</h2>
            <p class="text-sm text-gray-600">مرحباً <?php echo esc_html(wp_get_current_user()->display_name); ?></p>
        </div>
        
        <ul class="space-y-1 text-right">
            <?php foreach (wc_get_account_menu_items() as $endpoint => $label) : ?>
                <li class="<?php echo wc_get_account_menu_item_classes($endpoint); ?> <?php echo is_wc_endpoint_url($endpoint) ? 'bg-primary/10 rounded-lg' : ''; ?>">
                    <a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>" class="block px-3 py-2 hover:bg-gray-100 rounded-lg transition flex items-center justify-end">
                        <span><?php echo esc_html($label); ?></span>
                        <?php 
                        // Add icons after menu items for RTL layout
                        $icon = '';
                        switch ($endpoint) {
                            case 'dashboard':
                                $icon = '<i class="fas fa-home mr-2"></i>';
                                break;
                            case 'orders':
                                $icon = '<i class="fas fa-shopping-cart mr-2"></i>';
                                break;
                            case 'downloads':
                                $icon = '<i class="fas fa-download mr-2"></i>';
                                break;
                            case 'edit-address':
                                $icon = '<i class="fas fa-map-marker-alt mr-2"></i>';
                                break;
                            case 'edit-account':
                                $icon = '<i class="fas fa-user mr-2"></i>';
                                break;
                            case 'favorites':
                                $icon = '<i class="fas fa-heart mr-2"></i>';
                                break;
                            case 'customer-logout':
                                $icon = '<i class="fas fa-sign-out-alt mr-2"></i>';
                                break;
                            default:
                                $icon = '<i class="fas fa-circle mr-2"></i>';
                        }
                        
                        echo $icon; // Output the icon
                        ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>

<style>
    /* RTL specific styling for account navigation */
    .woocommerce-account .woocommerce {
        display: flex !important;
        flex-direction: row-reverse !important;
        gap: 2rem !important;
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
    
    /* Icon styling for better RTL support */
    .woocommerce-MyAccount-navigation i.fas {
        display: inline-block !important;
    }
    
    /* Reset button styling */
    .mobile-nav-toggle {
        background: none;
        border: none;
        padding: 0;
        text-align: left;
    }
    
    @media (max-width: 768px) {
        .woocommerce-account .woocommerce {
            flex-direction: column !important;
        }
        
        .woocommerce-MyAccount-navigation,
        .woocommerce-MyAccount-content {
            width: 100% !important;
        }
        
        /* Mobile navigation styling */
        .mobile-nav-toggle {
            display: flex !important;
        }
        
        .mobile-hidden {
            display: none !important;
        }
        
        .mobile-visible {
            display: block !important;
        }
        
        /* Toggle icon animation */
        .toggle-icon {
            transition: all 0.3s ease;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .toggle-icon:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        
        .toggle-icon.active {
            background-color: #e74c3c !important;
        }
        
        /* Navigation slide effect */
        .nav-content {
            transition: max-height 0.4s ease, opacity 0.3s ease;
            overflow: hidden;
        }
        
        .nav-content.mobile-visible {
            animation: slideDown 0.4s ease forwards;
        }
        
        @keyframes slideDown {
            from {
                max-height: 0;
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                max-height: 1000px;
                opacity: 1;
                transform: translateY(0);
            }
        }
    }
    
    /* Active menu item styling */
    .woocommerce-MyAccount-navigation .is-active a {
        color: #FF6B6B !important;
        font-weight: bold !important;
    }
</style>

<!-- Add JavaScript for toggle functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.querySelector('.mobile-nav-toggle');
    const navContent = document.querySelector('.nav-content');
    const toggleIcon = document.querySelector('.toggle-icon');
    const openIcon = document.querySelector('.open-icon');
    const closeIcon = document.querySelector('.close-icon');
    
    if (toggleButton && navContent) {
        toggleButton.addEventListener('click', function(e) {
            // Prevent any default behavior
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle navigation visibility
            navContent.classList.toggle('mobile-visible');
            navContent.classList.toggle('mobile-hidden');
            toggleIcon.classList.toggle('active');
            
            // Toggle between icons
            openIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
            
            // Return false to prevent any other actions
            return false;
        });
    }
});
</script>

<?php do_action('woocommerce_after_account_navigation'); ?>