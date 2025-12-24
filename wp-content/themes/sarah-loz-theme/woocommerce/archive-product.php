<?php
/**
 * The Template for displaying product archives, including the main shop page
 */

defined('ABSPATH') || exit;

// Add custom styles for WooCommerce elements
add_action('wp_head', function() {
    ?>
    <style>
        /* Fun Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        @keyframes wiggle {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(5deg); }
            75% { transform: rotate(-5deg); }
        }
        
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        .animate-wiggle {
            animation: wiggle 2s ease-in-out infinite;
        }
        
        .animate-spin-slow {
            animation: spin-slow 8s linear infinite;
        }
        
        /* Playful Patterns */
        .pattern-dots {
            background-image: radial-gradient(#4ecdc4 1px, transparent 1px);
            background-size: 20px 20px;
        }
        
        .pattern-wavy {
            background: repeating-linear-gradient(45deg, #4ecdc4 0, #4ecdc4 10px, transparent 10px, transparent 20px);
            opacity: 0.1;
        }
        
        .pattern-zigzag {
            background: linear-gradient(135deg, #4ecdc4 25%, transparent 25%) -10px 0,
                        linear-gradient(225deg, #4ecdc4 25%, transparent 25%) -10px 0,
                        linear-gradient(315deg, #4ecdc4 25%, transparent 25%),
                        linear-gradient(45deg, #4ecdc4 25%, transparent 25%);
            background-size: 20px 20px;
            opacity: 0.1;
        }
        
        .pattern-checks {
            background-image: repeating-linear-gradient(45deg, #4ecdc4 0, #4ecdc4 1px, transparent 1px, transparent 50%);
            background-size: 10px 10px;
            opacity: 0.1;
        }

        /* Child-friendly Product Cards */
        .product {
            transition: all 0.3s ease;
        }
        
        .product:hover {
            transform: translateY(-5px);
        }
        
        .product img {
            border-radius: 15px;
            transition: all 0.3s ease;
        }
        
        .product:hover img {
            transform: scale(1.05);
        }
        
        .product .price {
            font-size: 1.2em;
            color: #4ecdc4;
            font-weight: bold;
        }
        
        .product .button {
            background: linear-gradient(45deg, #4ecdc4, #96e6a1);
            border-radius: 999px;
            color: white;
            font-weight: bold;
            padding: 0.8em 1.5em;
            transition: all 0.3s ease;
        }
        
        .product .button:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(78, 205, 196, 0.3);
        }
        
        /* WooCommerce Notices Styling */
        .woocommerce-notices-wrapper {
            margin-bottom: 1.5rem;
        }
        
        .woocommerce-notices-wrapper .woocommerce-message,
        .woocommerce-notices-wrapper .woocommerce-info,
        .woocommerce-notices-wrapper .woocommerce-error {
            padding: 1rem 1.5rem;
            border-radius: 1rem;
            border: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .woocommerce-notices-wrapper .woocommerce-message {
            background-color: rgba(78, 205, 196, 0.1);
            color: #4ecdc4;
        }
        
        .woocommerce-notices-wrapper .woocommerce-info {
            background-color: rgba(255, 209, 102, 0.1);
            color: #ffd166;
        }
        
        .woocommerce-notices-wrapper .woocommerce-error {
            background-color: rgba(255, 107, 107, 0.1);
            color: #ff6b6b;
            flex-direction: column;
            align-items: flex-start;
        }
        
        .woocommerce-notices-wrapper .woocommerce-error li {
            margin-bottom: 0.5rem;
        }
        
        /* Result Count and Ordering Styling */
        .woocommerce-result-count {
            margin: 0 !important;
            padding: 0.5rem 0;
            color: #718096;
            font-size: 0.9rem;
        }
        
        .woocommerce-ordering {
            margin: 0 !important;
            position: relative;
        }
        
        .woocommerce-ordering select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-color: rgba(78, 205, 196, 0.1);
            border: 1px solid rgba(78, 205, 196, 0.2);
            border-radius: 9999px;
            padding: 0.5rem 2.5rem 0.5rem 1rem;
            font-size: 0.9rem;
            color: #4ecdc4;
            cursor: pointer;
            font-weight: 500;
            outline: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        .rtl .woocommerce-ordering select {
            padding: 0.5rem 1rem 0.5rem 2.5rem;
        }
        
        .woocommerce-ordering:after {
            content: "\f107";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #4ecdc4;
            pointer-events: none;
        }
        
        .rtl .woocommerce-ordering:after {
            left: auto;
            right: 1rem;
        }
        
        @media (max-width: 768px) {
            .woocommerce-result-count,
            .woocommerce-ordering {
                text-align: center;
                width: 100%;
                margin-bottom: 0.5rem !important;
            }
            
            .woocommerce-ordering select {
                width: 100%;
                max-width: 250px;
            }
        }
        
        /* Pagination Styling */
        .woocommerce-pagination {
            margin-top: 2rem !important;
            text-align: center !important;
        }
        
        .woocommerce-pagination ul {
            display: inline-flex !important;
            border: none !important;
            background: white !important;
            border-radius: 9999px !important;
            padding: 0.5rem !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
        }
        
        .woocommerce-pagination ul li {
            border: none !important;
            margin: 0 0.25rem !important;
        }
        
        .woocommerce-pagination ul li a,
        .woocommerce-pagination ul li span {
            padding: 0.5rem 1rem !important;
            border-radius: 9999px !important;
            color: #718096 !important;
            font-weight: 500 !important;
            min-width: 2.5rem !important;
            min-height: 2.5rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        .woocommerce-pagination ul li a:hover {
            background-color: rgba(78, 205, 196, 0.1) !important;
            color: #4ecdc4 !important;
        }
        
        .woocommerce-pagination ul li span.current {
            background-color: #4ecdc4 !important;
            color: white !important;
        }
    </style>
    <?php
});

get_header('shop');
?>

<main class="container mx-auto px-4 py-10">
    <!-- Store Header -->
    <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-10">
        <div class="h-72 bg-gradient-to-r from-primary/20 to-accent/20 relative">
            <!-- Decorative elements -->
            <div class="absolute top-4 left-4 w-16 h-16 animate-bounce">
                <img src="<?php echo get_theme_file_uri('assets/images/star.jpg'); ?>" alt="Star" class="w-full h-full object-contain">
            </div>
            <div class="absolute top-8 right-8 w-20 h-20 animate-float">
                <img src="<?php echo get_theme_file_uri('assets/images/balloons.png'); ?>" alt="Balloon" class="w-full h-full object-contain">
            </div>
            <div class="absolute bottom-4 right-4 w-16 h-16 animate-spin-slow">
                <img src="<?php echo get_theme_file_uri('assets/images/sun.png'); ?>" alt="Sun" class="w-full h-full object-contain">
            </div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-store text-8xl text-primary/50 mb-4"></i>
                    <h1 class="text-4xl font-bold text-primary animate-bounce">مرحباً بكم في المتجر!</h1>
                </div>
            </div>
        </div>
        <div class="p-8">
            <h2 class="text-3xl md:text-4xl font-bold text-dark mb-4 text-center">
                <?php woocommerce_page_title(); ?>
            </h2>
            <p class="text-center text-lg text-gray-600 mb-6">اكتشف عالماً من المرح والتعلم مع منتجاتنا الممتعة!</p>
            <?php do_action('woocommerce_archive_description'); ?>

            <div class="flex flex-wrap gap-4 mt-6 justify-center">
                <?php if (is_product_category()) : ?>
                    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="bg-primary text-white px-6 py-3 rounded-full text-lg shadow-lg hover:bg-primary/90 transition transform hover:-translate-y-1">
                        <i class="fas fa-th-large ml-2"></i>
                        جميع المنتجات
                    </a>
                <?php endif; ?>

                <?php
                // Display category links
                $product_categories = get_terms(array(
                    'taxonomy' => 'product_cat',
                    'hide_empty' => true,
                    'parent' => 0,
                    'number' => 4,
                ));

                if (!empty($product_categories)) {
                    foreach ($product_categories as $category) {
                        echo '<a href="' . esc_url(get_term_link($category)) . '" class="bg-accent text-dark px-6 py-3 rounded-full text-lg shadow-lg hover:bg-accent/90 transition transform hover:-translate-y-1">';
                        echo '<i class="fas fa-tag ml-2"></i>';
                        echo esc_html($category->name);
                        echo '</a>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
    
    <?php
    // Display notices with proper styling
    echo '<div class="woocommerce-notices-wrapper-container">';
    do_action('woocommerce_before_main_content');
    echo '</div>';
    ?>

    <?php
    // Add product category grid if we're on the main shop page
    if (is_shop() && !is_search()) :
        $product_categories = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
            'parent' => 0,
        ));

        if (!empty($product_categories)) :
    ?>
        <!-- Product Categories -->
        <section id="categories" class="mb-16">
            <div class="text-center mb-8">
                <h2 class="text-2xl md:text-3xl font-bold text-primary inline-block relative">
                    <span class="relative z-10">استكشف عالمك المفضل</span>
                    <div class="absolute -bottom-2 left-0 right-0 h-3 bg-accent/30 -rotate-1"></div>
                </h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php 
                // Define fun background patterns
                $bg_patterns = array(
                    'pattern-dots',
                    'pattern-wavy',
                    'pattern-zigzag',
                    'pattern-checks'
                );
                
                $i = 0;
                foreach ($product_categories as $category) :
                    $pattern_class = $bg_patterns[$i % count($bg_patterns)];
                    $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                    $category_image = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'woocommerce_thumbnail') : get_theme_file_uri('assets/images/placeholder.jpg');
                ?>
                <a href="<?php echo esc_url(get_term_link($category)); ?>" 
                   class="transform hover:-translate-y-2 transition-all duration-300 group">
                    <div class="bg-white rounded-2xl p-6 text-center shadow-lg border-2 border-primary/20 relative overflow-hidden">
                        <div class="absolute inset-0 opacity-10 <?php echo $pattern_class; ?>"></div>
                        <div class="relative">
                            <div class="w-32 h-32 rounded-full overflow-hidden mx-auto mb-4 group-hover:scale-110 transition-transform duration-300 bg-gradient-to-r from-primary/20 to-accent/20 p-1">
                                <div class="w-full h-full rounded-full overflow-hidden">
                                    <img src="<?php echo esc_url($category_image); ?>" 
                                         alt="<?php echo esc_attr($category->name); ?>"
                                         class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-300"
                                         loading="lazy">
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-primary mb-2 group-hover:text-accent transition-colors">
                                <?php echo esc_html($category->name); ?>
                            </h3>
                            <p class="text-gray-600">
                                <?php 
                                $description = $category->description ? $category->description : 'استكشف كنوز ' . $category->name;
                                echo esc_html($description);
                                ?>
                            </p>
                        </div>
                    </div>
                </a>
                <?php 
                    $i++;
                endforeach; 
                ?>
            </div>
        </section>
    <?php 
        endif;
    endif; 
    ?>

    <?php
    if (woocommerce_product_loop()) {
        // Add a styled container for ordering and results
        echo '<div class="flex flex-wrap md:flex-row justify-between items-center mb-8 bg-white rounded-xl shadow-sm p-4">';
        
        // Remove existing hooks for result count and ordering
        remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
        remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
        
        // Re-add them with custom HTML wrapping
        echo '<div class="w-full md:w-auto mb-2 md:mb-0">';
        woocommerce_result_count();
        echo '</div>';
        
        echo '<div class="w-full md:w-auto">';
        woocommerce_catalog_ordering();
        echo '</div>';
        
        // Handle other actions that might be hooked
        ob_start();
        do_action('woocommerce_before_shop_loop');
        $other_actions = ob_get_clean();
        if (trim($other_actions) !== '') {
            echo $other_actions;
        }
        
        echo '</div>';
    ?>
        <!-- Product List -->
        <section id="products" class="mb-16">
            <div class="text-center mb-8">
                <h2 class="text-2xl md:text-3xl font-bold text-accent inline-block relative">
                    <i class="fas fa-sparkles ml-2 text-yellow-400"></i>
                    <span class="relative z-10"><?php echo is_product_category() ? single_term_title('', false) : 'منتجاتنا المميزة'; ?></span>
                    <div class="absolute -bottom-2 left-0 right-0 h-3 bg-primary/30 rotate-1"></div>
                </h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                while (have_posts()) {
                    the_post();
                    do_action('woocommerce_shop_loop');
                    wc_get_template_part('content', 'product');
                }
                ?>
            </div>
            
            <?php do_action('woocommerce_after_shop_loop'); ?>
        </section>
    <?php
    } else {
    ?>
        <div class="text-center py-12">
            <div class="inline-block p-8 bg-gradient-to-r from-primary/10 to-accent/10 rounded-2xl shadow-lg mb-6 relative">
                <div class="absolute top-4 right-4 w-12 h-12 animate-bounce">
                    <img src="<?php echo get_theme_file_uri('assets/images/star.jpg'); ?>" alt="Star" class="w-full h-full object-contain">
                </div>
                <i class="fas fa-search text-6xl text-primary mb-4 animate-pulse"></i>
                <div class="w-16 h-16 absolute bottom-4 left-4 animate-spin-slow opacity-50">
                    <img src="<?php echo get_theme_file_uri('assets/images/sun.jpg'); ?>" alt="Sun" class="w-full h-full object-contain">
                </div>
            </div>
            <h2 class="text-2xl font-bold text-primary mb-4">عذراً! لم نجد ما تبحث عنه</h2>
            <p class="text-lg text-gray-600 mb-6">لا تقلق! يمكنك تجربة البحث عن شيء آخر أو تصفح الفئات المميزة</p>
            <?php do_action('woocommerce_no_products_found'); ?>
            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="inline-block bg-primary text-white px-6 py-3 rounded-full text-lg shadow-lg hover:bg-primary/90 transition transform hover:-translate-y-1">
                <i class="fas fa-magic ml-2"></i>
                اكتشف المزيد من المنتجات
            </a>
        </div>
    <?php
    }
    ?>

    <?php
    // Display special offers if we have products on sale
    $sale_products = new WP_Query(array(
        'post_type' => 'product',
        'posts_per_page' => 3,
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => '_sale_price',
                'value' => 0,
                'compare' => '>',
                'type' => 'NUMERIC'
            ),
            array(
                'key' => '_min_variation_sale_price',
                'value' => 0,
                'compare' => '>',
                'type' => 'NUMERIC'
            )
        )
    ));

    if ($sale_products->have_posts()) :
    ?>
    <!-- Special Offers -->
    <section class="bg-gradient-to-r from-primary/5 to-accent/5 rounded-3xl shadow-lg overflow-hidden mb-16 relative">
        <!-- Decorative elements -->
        <div class="absolute top-0 left-0 w-24 h-24">
            <img src="<?php echo get_theme_file_uri('assets/images/balloons.png'); ?>" alt="Balloons" class="w-full h-full object-contain animate-float">
        </div>
        
        <div class="p-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl md:text-3xl font-bold text-primary inline-block relative">
                    <i class="fas fa-gift text-accent animate-bounce ml-2"></i>
                    <span class="relative z-10">عروض مميزة للأطفال</span>
                    <div class="absolute -bottom-2 left-0 right-0 h-3 bg-yellow-300/30 -rotate-1"></div>
                </h2>
            </div>
            
            <div class="bg-white rounded-2xl p-6 md:p-8 relative overflow-hidden shadow-inner">
                <!-- Fun decorative shapes -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary/10 rounded-full -mt-16 -mr-16 animate-spin-slow"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-accent/10 rounded-full -mb-12 -ml-12"></div>
                
                <div class="relative flex flex-col md:flex-row items-center">
                    <div class="md:w-2/3 mb-6 md:mb-0 md:ml-8 text-center md:text-right">
                        <h3 class="text-2xl font-bold text-primary mb-4">
                            <span class="relative">
                                مفاجآت وهدايا رائعة
                                <div class="absolute -bottom-1 left-0 right-0 h-2 bg-yellow-300/50"></div>
                            </span>
                        </h3>
                        <p class="text-gray-600 mb-6 text-lg">
                            اكتشف عالماً من المرح مع عروضنا المميزة! منتجات تعليمية وترفيهية بأسعار خاصة
                        </p>
                        <a href="<?php echo esc_url(add_query_arg('on_sale', '1', get_permalink(wc_get_page_id('shop')))); ?>" class="mt-4 bg-gradient-to-r from-primary to-accent text-white px-8 py-4 rounded-full text-lg hover:shadow-lg transform hover:-translate-y-1 transition-all inline-flex items-center group">
                            <i class="fas fa-star ml-2 group-hover:rotate-180 transition-transform"></i>
                            اكتشف العروض
                            <i class="fas fa-star mr-2 group-hover:-rotate-180 transition-transform"></i>
                        </a>
                    </div>
                    <div class="md:w-1/3 flex justify-center">
                        <div class="w-48 h-48 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-full flex items-center justify-center shadow-xl relative group">
                            <i class="fas fa-gift text-7xl text-primary group-hover:scale-110 transition-transform"></i>
                            <div class="absolute inset-0 bg-primary/10 rounded-full animate-ping opacity-75"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php 
    endif;
    wp_reset_postdata();
    ?>
</main>

<?php get_footer('shop'); ?>
