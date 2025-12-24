<?php
/**
 * The Template for displaying all single products
 */

defined('ABSPATH') || exit;

// Remove any existing breadcrumb actions to prevent duplicates
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
remove_action('woocommerce_before_single_product', 'woocommerce_breadcrumb', 20);
remove_action('woocommerce_before_single_product_summary', 'woocommerce_breadcrumb', 20);

// Add custom styles for the Add to Cart button
add_action('wp_head', function() {
    ?>
    <style>
        /* Custom styles for single product add to cart button */
        .single-product .single_add_to_cart_button {
            background-color: #4ecdc4 !important; /* Teal color from screenshot */
            color: white !important;
            border-radius: 30px !important;
            padding: 12px 30px !important;
            font-weight: 600 !important;
            font-size: 1rem !important;
            transition: all 0.2s ease !important;
            border: none !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 200px !important;
            height: 50px !important;
            text-align: center !important;
        }
        
        .single-product .single_add_to_cart_button:hover {
            opacity: 0.9 !important;
        }
        
        .single-product .quantity .qty {
            border-radius: 30px !important;
            border: 1px solid #e2e8f0 !important;
            padding: 0.5rem !important;
            width: 80px !important;
            height: 50px !important;
            text-align: center !important;
            margin-left: 0.75rem !important;
            font-size: 1rem !important;
        }
        
        /* Cart icon styling */
        .single-product .single_add_to_cart_button:before {
            content: "\f07a"; /* Font Awesome cart icon */
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            margin-left: 8px;
            margin-right: 8px;
        }
        
        /* Product wishlist buttons */
        .sarah-loz-toggle-favorite {
            background-color: #ffd166 !important;
            color: #2d3748 !important;
            border-radius: 30px !important;
            padding: 12px 30px !important;
            font-weight: 600 !important;
            font-size: 1rem !important;
            border: none !important;
            height: 50px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        }
        
        .sarah-loz-toggle-favorite:hover {
            opacity: 0.9 !important;
        }
        
        /* Fix form styling */
        .single-product form.cart {
            display: flex !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            gap: 10px !important;
            margin-bottom: 1.5rem !important;
        }
        
        /* Make sure buttons have proper spacing on mobile */
        @media (max-width: 640px) {
            .single-product form.cart {
                flex-direction: column !important;
                align-items: stretch !important;
            }
            
            .single-product .single_add_to_cart_button,
            .sarah-loz-toggle-favorite {
                width: 100% !important;
                margin-top: 10px !important;
            }
            
            .single-product .quantity {
                width: 100% !important;
                display: flex !important;
                justify-content: center !important;
            }
        }
    </style>
    <?php
});

get_header('shop');

// Get the product
global $product;
?>

<main class="container mx-auto px-4 py-10">
    <?php while (have_posts()) : ?>
        <?php the_post(); ?>

        <!-- Breadcrumb with proper styling -->
        <div class="text-sm text-gray-500 mb-6">
            <?php woocommerce_breadcrumb(); ?>
        </div>

        <!-- Product Main Info -->
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
                <!-- Product Image -->
                <div class="h-96 bg-primary/10 relative overflow-hidden">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="h-full w-full flex items-center justify-center">
                    <?php 
                    // Remove breadcrumbs from product summary to avoid duplication
                    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
                    remove_action('woocommerce_before_single_product_summary', 'woocommerce_breadcrumb', 20);
                    do_action('woocommerce_before_single_product_summary');
                    ?>
                        </div>
                    <?php else : ?>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <?php 
                            // Display different icons based on product category
                            $categories = get_the_terms($product->get_id(), 'product_cat');
                            $icon_class = 'fas fa-tag';
                            
                            if ($categories && !is_wp_error($categories)) {
                                $category_names = wp_list_pluck($categories, 'name');
                                $category_slugs = wp_list_pluck($categories, 'slug');
                                
                                if (in_array('books', $category_slugs) || stripos(implode(' ', $category_names), 'كتاب') !== false || stripos(implode(' ', $category_names), 'قصة') !== false) {
                                    $icon_class = 'fas fa-book';
                                } elseif (in_array('games', $category_slugs) || stripos(implode(' ', $category_names), 'لعبة') !== false || stripos(implode(' ', $category_names), 'ألغاز') !== false) {
                                    $icon_class = 'fas fa-puzzle-piece';
                                } elseif (in_array('art', $category_slugs) || stripos(implode(' ', $category_names), 'رسم') !== false || stripos(implode(' ', $category_names), 'فن') !== false) {
                                    $icon_class = 'fas fa-paint-brush';
                                } elseif (in_array('characters', $category_slugs) || stripos(implode(' ', $category_names), 'شخصية') !== false || stripos(implode(' ', $category_names), 'دمية') !== false) {
                                    $icon_class = 'fas fa-child';
                                }
                            }
                            ?>
                            <i class="<?php echo $icon_class; ?> text-8xl text-primary/40"></i>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!$product->is_in_stock()) : ?>
                        <span class="absolute top-4 right-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">نفذت الكمية</span>
                    <?php endif; ?>
                    
                    <?php if ($product->is_on_sale()) : ?>
                        <?php 
                        $discount_percentage = 0;
                        if ($product->get_regular_price() > 0) {
                            $discount_percentage = round(($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price() * 100);
                        }
                        ?>
                        <span class="absolute top-4 right-4 bg-primary text-white px-3 py-1 rounded-full text-sm font-bold">خصم <?php echo $discount_percentage; ?>٪</span>
                    <?php elseif ($product->is_featured()) : ?>
                        <span class="absolute top-4 right-4 bg-accent text-dark px-3 py-1 rounded-full text-sm font-bold">مميز</span>
                    <?php elseif (strtotime($product->get_date_created()) > strtotime('-14 days')) : ?>
                        <span class="absolute top-4 right-4 bg-accent text-dark px-3 py-1 rounded-full text-sm font-bold">جديد</span>
                    <?php endif; ?>
                </div>

                <!-- Product Details -->
                <div class="p-8 flex flex-col">
                    <div class="mb-4">
                        <div class="text-sm text-accent mb-1">
                            <?php if ($rating_html = wc_get_rating_html($product->get_average_rating())) : ?>
                                <div class="star-rating">
                                    <?php echo $rating_html; ?>
                                    <span class="text-gray-500 mr-1"><?php echo $product->get_average_rating(); ?> (<?php echo $product->get_rating_count(); ?> تقييم)</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-bold text-dark mb-4">
                            <?php the_title(); ?>
                        </h1>
                        <div class="flex items-center mb-4">
                            <span class="text-2xl font-bold text-primary ml-3"><?php echo $product->get_price_html(); ?></span>
                            <span class="text-gray-500">الكمية المتوفرة: <?php echo $product->get_stock_quantity() ? $product->get_stock_quantity() : '∞'; ?></span>
                        </div>
                        <div class="text-gray-600 text-lg mb-6">
                            <?php echo apply_filters('woocommerce_short_description', $product->get_short_description()); ?>
                        </div>
                    </div>

                    <div class="mt-auto">
                        <div class="flex items-center mb-6">
                            <?php 
                            $age_range = get_field('age_range', $product->get_id());
                            if ($age_range) : ?>
                                <div>
                                    <span class="ml-2">العمر المناسب:</span>
                                    <span class="bg-accent text-dark px-3 py-1 rounded-full text-sm"><?php echo esc_html($age_range); ?> سنوات</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex flex-wrap gap-4">
                            <?php 
                            // Product add to cart
                            woocommerce_template_single_add_to_cart(); 
                            ?>
                            
                            <?php 
                            // The favorite button will be automatically added by our hook
                            // sarah_loz_add_favorite_button() in woocommerce.php
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Details Tabs -->
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-10">
            <?php
            // Custom tabs implementation
            $tabs = apply_filters('woocommerce_product_tabs', array());
            
            if (!empty($tabs)) : ?>
                <div class="border-b border-gray-200">
                    <div class="flex overflow-x-auto">
                        <?php $active_tab = array_key_first($tabs); ?>
                        <?php foreach ($tabs as $key => $tab) : ?>
                            <button class="product-tab-button px-6 py-4 <?php echo $key === $active_tab ? 'text-primary border-b-2 border-primary font-bold' : 'text-gray-500 hover:text-primary transition'; ?>" data-tab="<?php echo esc_attr($key); ?>">
                                <?php echo esc_html($tab['title']); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="p-8">
                    <?php foreach ($tabs as $key => $tab) : ?>
                        <div id="tab-<?php echo esc_attr($key); ?>" class="product-tab-content <?php echo $key === $active_tab ? 'block' : 'hidden'; ?>">
                            <h2 class="text-xl font-bold text-dark mb-4"><?php echo esc_html($tab['title']); ?></h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <?php call_user_func($tab['callback'], $key, $tab); ?>
                                </div>
                                
                                <?php if ($key === 'description' && function_exists('get_field')) : ?>
                                    <div>
                                        <h3 class="text-lg font-bold text-dark mb-2">المميزات</h3>
                                        <ul class="space-y-2 text-gray-600">
                                            <?php 
                                            // Display product features if added with ACF
                                            $features = get_field('product_features', $product->get_id());
                                            if ($features) {
                                                foreach ($features as $feature) {
                                                    echo '<li class="flex items-center">';
                                                    echo '<i class="fas fa-check-circle text-secondary ml-2"></i>';
                                                    echo esc_html($feature['feature']);
                                                    echo '</li>';
                                                }
                                            } else {
                                                // Display default features based on product categories
                                                $categories = get_the_terms($product->get_id(), 'product_cat');
                                                if ($categories && !is_wp_error($categories)) {
                                                    $category = reset($categories);
                                                    echo '<li class="flex items-center"><i class="fas fa-check-circle text-secondary ml-2"></i>منتج ذو جودة عالية</li>';
                                                    
                                                    if (stripos($category->name, 'كتاب') !== false || stripos($category->name, 'قصة') !== false) {
                                                        echo '<li class="flex items-center"><i class="fas fa-check-circle text-secondary ml-2"></i>رسومات عالية الجودة</li>';
                                                        echo '<li class="flex items-center"><i class="fas fa-check-circle text-secondary ml-2"></i>لغة عربية سهلة ومناسبة للأطفال</li>';
                                                    } elseif (stripos($category->name, 'لعبة') !== false || stripos($category->name, 'ألغاز') !== false) {
                                                        echo '<li class="flex items-center"><i class="fas fa-check-circle text-secondary ml-2"></i>مواد آمنة للأطفال</li>';
                                                        echo '<li class="flex items-center"><i class="fas fa-check-circle text-secondary ml-2"></i>يساعد على تنمية مهارات الطفل</li>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </ul>
                                        
                                        <div class="mt-6">
                                            <h3 class="text-lg font-bold text-dark mb-2">تفاصيل إضافية</h3>
                                            <div class="grid grid-cols-2 gap-4 text-gray-600">
                                                <div>
                                                    <?php if ($product->get_weight()) : ?>
                                                        <p><span class="font-bold">الوزن:</span> <?php echo $product->get_weight(); ?> <?php echo get_option('woocommerce_weight_unit'); ?></p>
                                                    <?php endif; ?>
                                                    <?php if ($product->get_dimensions()) : ?>
                                                        <p><span class="font-bold">الأبعاد:</span> <?php echo $product->get_dimensions(); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <p><span class="font-bold">SKU:</span> <?php echo $product->get_sku() ? $product->get_sku() : 'غير متوفر'; ?></p>
                                                    <?php if ($product->get_categories()) : ?>
                                                        <p><span class="font-bold">الفئة:</span> <?php echo $product->get_categories(); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Related Products -->
        <?php 
        if (function_exists('woocommerce_output_related_products')) {
            echo '<section class="mb-16">';
            echo '<h2 class="text-2xl md:text-3xl font-bold text-dark mb-8"><span class="border-b-4 border-primary pb-2">منتجات مشابهة</span></h2>';
            echo '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">';
            woocommerce_related_products(array(
                'posts_per_page' => 4,
                'columns'        => 4,
                'orderby'        => 'rand'
            ));
            echo '</div>';
            echo '</section>';
        }
        ?>
    <?php endwhile; ?>
</main>

<!-- Add JavaScript for tabs -->
<script>
jQuery(document).ready(function($) {
    $('.product-tab-button').on('click', function() {
        var tab = $(this).data('tab');
        
        // Update active tab button
        $('.product-tab-button').removeClass('text-primary border-b-2 border-primary font-bold').addClass('text-gray-500 hover:text-primary transition');
        $(this).addClass('text-primary border-b-2 border-primary font-bold').removeClass('text-gray-500 hover:text-primary transition');
        
        // Show active tab content
        $('.product-tab-content').removeClass('block').addClass('hidden');
        $('#tab-' + tab).addClass('block').removeClass('hidden');
    });
});
</script>

<?php get_footer('shop'); ?>
