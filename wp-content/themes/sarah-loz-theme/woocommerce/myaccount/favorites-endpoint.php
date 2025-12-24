<?php
/**
 * My Account Favorites Endpoint
 *
 * Shows the favorite products in the My Account favorites endpoint
 */

defined('ABSPATH') || exit;

// Get current user's favorites
$user_id = get_current_user_id();
$favorites = sarah_loz_get_favorite_products($user_id);

// Group favorites by type
$products = [];
$activities = [];
$videos = [];

if (!empty($favorites) && is_array($favorites)) {
    foreach ($favorites as $item_id) {
        $post_type = get_post_type($item_id);
        
        if ($post_type === 'product') {
            $products[] = $item_id;
        } elseif ($post_type === 'activity') {
            $activities[] = $item_id;
        } elseif ($post_type === 'video') {
            $videos[] = $item_id;
        }
    }
}

// Check if there are any favorites
$has_favorites = !empty($products) || !empty($activities) || !empty($videos);
?>

<div class="woocommerce-favorites">
    <h2 class="woocommerce-favorites-title">المنتجات المفضلة</h2>
    
    <?php
    // Debug information (only visible to administrators)
    if (current_user_can('administrator')) : ?>
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
            <h3 class="font-bold mb-2">معلومات التصحيح (للمسؤولين فقط):</h3>
            <p>المستخدم: <?php echo esc_html($user_id); ?></p>
            <p>جميع المفضلة: <?php echo !empty($favorites) ? implode(', ', $favorites) : 'فارغ'; ?></p>
            <p>المنتجات: <?php echo !empty($products) ? implode(', ', $products) : 'فارغ'; ?></p>
            <p>الأنشطة: <?php echo !empty($activities) ? implode(', ', $activities) : 'فارغ'; ?></p>
            <p>الفيديوهات: <?php echo !empty($videos) ? implode(', ', $videos) : 'فارغ'; ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (!$has_favorites) : ?>
        <div class="favorites-empty">
            <i class="far fa-heart"></i>
            <p class="text-lg text-gray-500 mb-2">لا توجد عناصر في المفضلة</p>
            <p class="text-gray-500 mb-4">يمكنك إضافة المنتجات والأنشطة والفيديوهات إلى المفضلة من خلال الضغط على زر "أضف للمفضلة"</p>
            <div class="flex justify-center gap-4 mt-6">
                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="bg-primary text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition inline-block">تصفح المنتجات</a>
                <?php if (post_type_exists('activity')) : ?>
                    <a href="<?php echo esc_url(get_post_type_archive_link('activity')); ?>" class="bg-secondary text-white px-6 py-2 rounded-full hover:bg-opacity-90 transition inline-block">تصفح الأنشطة</a>
                <?php endif; ?>
                <?php if (post_type_exists('video')) : ?>
                    <a href="<?php echo esc_url(get_post_type_archive_link('video')); ?>" class="bg-accent text-dark px-6 py-2 rounded-full hover:bg-opacity-90 transition inline-block">تصفح الفيديوهات</a>
                <?php endif; ?>
            </div>
        </div>
    <?php else : ?>
        
        <!-- Products Section -->
        <?php if (!empty($products)) : ?>
            <h3 class="text-xl font-bold text-dark mb-4 mt-8 border-r-4 border-primary pr-2">المنتجات</h3>
            <div class="favorites-products-grid">
                <?php 
                // Loop through favorite products
                foreach ($products as $product_id) {
                    $product = wc_get_product($product_id);
                    
                    // Skip if product doesn't exist or is not visible
                    if (!$product || !$product->is_visible()) {
                        continue;
                    }
                    
                    // Get product data
                    $product_permalink = $product->get_permalink();
                    $product_name = $product->get_name();
                    $product_price = $product->get_price_html();
                    $product_image_id = $product->get_image_id();
                    
                    // Get product image
                    if ($product_image_id) {
                        $product_image = wp_get_attachment_image($product_image_id, 'woocommerce_thumbnail', false, ['class' => 'h-full w-full object-contain']);
                    } else {
                        // Display placeholder if no image
                        $categories = get_the_terms($product_id, 'product_cat');
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
                        
                        $product_image = '<div class="flex items-center justify-center h-full"><i class="' . $icon_class . ' text-5xl text-primary/40"></i></div>';
                    }
                    
                    // Check if product is on sale
                    $on_sale = $product->is_on_sale();
                    $sale_badge = '';
                    
                    if ($on_sale) {
                        $regular_price = $product->get_regular_price();
                        $sale_price = $product->get_sale_price();
                        
                        if ($regular_price > 0) {
                            $discount_percentage = round(($regular_price - $sale_price) / $regular_price * 100);
                            $sale_badge = '<span class="absolute top-4 right-4 bg-primary text-white px-3 py-1 rounded-full text-sm font-bold">خصم ' . $discount_percentage . '٪</span>';
                        }
                    }
                    
                    // Output product card
                    ?>
                    <div class="favorite-product-card">
                        <div class="product-image">
                            <a href="<?php echo esc_url($product_permalink); ?>" class="block h-full">
                                <?php echo $product_image; ?>
                            </a>
                            <?php echo $sale_badge; ?>
                            
                            <?php if (!$product->is_in_stock()) : ?>
                                <span class="absolute top-4 right-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">نفذت الكمية</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-content">
                            <h3 class="product-title">
                                <a href="<?php echo esc_url($product_permalink); ?>" class="hover:text-primary transition">
                                    <?php echo esc_html($product_name); ?>
                                </a>
                            </h3>
                            
                            <div class="product-price">
                                <?php echo $product_price; ?>
                            </div>
                            
                            <div class="product-actions">
                                <a href="<?php echo esc_url($product_permalink); ?>" class="button">
                                    <i class="fas fa-eye ml-1"></i>
                                    عرض التفاصيل
                                </a>
                                
                                <button type="button" class="sarah-loz-remove-favorite" data-product-id="<?php echo esc_attr($product_id); ?>" data-nonce="<?php echo wp_create_nonce('sarah_loz_favorite_nonce'); ?>">
                                    <i class="fas fa-heart-broken"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        <?php endif; ?>
        
        <!-- Activities Section -->
        <?php if (!empty($activities)) : ?>
            <h3 class="text-xl font-bold text-dark mb-4 mt-8 border-r-4 border-secondary pr-2">الأنشطة</h3>
            <div class="favorites-products-grid">
                <?php 
                // Loop through favorite activities
                foreach ($activities as $activity_id) {
                    $activity = get_post($activity_id);
                    
                    // Skip if activity doesn't exist
                    if (!$activity) {
                        continue;
                    }
                    
                    // Get activity data
                    $activity_permalink = get_permalink($activity);
                    $activity_name = get_the_title($activity);
                    $activity_image = get_the_post_thumbnail($activity, 'medium', ['class' => 'h-full w-full object-cover']);
                    $age_range = get_field('age_range', $activity_id);
                    
                    // Display placeholder if no image
                    if (!$activity_image) {
                        $activity_image = '<div class="flex items-center justify-center h-full"><i class="fas fa-paint-brush text-5xl text-secondary/40"></i></div>';
                    }
                    
                    // Output activity card
                    ?>
                    <div class="favorite-product-card">
                        <div class="product-image">
                            <a href="<?php echo esc_url($activity_permalink); ?>" class="block h-full">
                                <?php echo $activity_image; ?>
                            </a>
                            
                            <?php if ($age_range) : ?>
                                <span class="absolute top-4 right-4 bg-secondary text-white px-3 py-1 rounded-full text-sm font-bold">
                                    <?php echo esc_html($age_range); ?> سنوات
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-content">
                            <h3 class="product-title">
                                <a href="<?php echo esc_url($activity_permalink); ?>" class="hover:text-secondary transition">
                                    <?php echo esc_html($activity_name); ?>
                                </a>
                            </h3>
                            
                            <div class="text-gray-600 mb-4 line-clamp-2">
                                <?php echo get_the_excerpt($activity); ?>
                            </div>
                            
                            <div class="product-actions">
                                <a href="<?php echo esc_url($activity_permalink); ?>" class="button bg-secondary hover:bg-secondary/90">
                                    <i class="fas fa-play-circle ml-1"></i>
                                    عرض النشاط
                                </a>
                                
                                <button type="button" class="sarah-loz-remove-favorite" data-product-id="<?php echo esc_attr($activity_id); ?>" data-nonce="<?php echo wp_create_nonce('sarah_loz_favorite_nonce'); ?>">
                                    <i class="fas fa-heart-broken"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        <?php endif; ?>
        
        <!-- Videos Section -->
        <?php if (!empty($videos)) : ?>
            <h3 class="text-xl font-bold text-dark mb-4 mt-8 border-r-4 border-accent pr-2">الفيديوهات</h3>
            <div class="favorites-products-grid">
                <?php 
                // Loop through favorite videos
                foreach ($videos as $video_id) {
                    $video = get_post($video_id);
                    
                    // Skip if video doesn't exist
                    if (!$video) {
                        continue;
                    }
                    
                    // Get video data
                    $video_permalink = get_permalink($video);
                    $video_name = get_the_title($video);
                    $video_image = get_the_post_thumbnail($video, 'medium', ['class' => 'h-full w-full object-cover']);
                    $video_duration = get_field('video_duration', $video_id) ?: '00:00';
                    
                    // Display placeholder if no image
                    if (!$video_image) {
                        $video_image = '<div class="flex items-center justify-center h-full"><i class="fas fa-play-circle text-5xl text-accent/40"></i></div>';
                    }
                    
                    // Output video card
                    ?>
                    <div class="favorite-product-card">
                        <div class="product-image">
                            <a href="<?php echo esc_url($video_permalink); ?>" class="block h-full">
                                <?php echo $video_image; ?>
                                <!-- Play icon overlay -->
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity bg-dark/50">
                                    <i class="fas fa-play-circle text-4xl text-white"></i>
                                </div>
                            </a>
                            
                            <?php if ($video_duration) : ?>
                                <span class="absolute bottom-2 right-2 bg-dark/70 text-white px-2 py-1 rounded text-xs">
                                    <?php echo esc_html($video_duration); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-content">
                            <h3 class="product-title">
                                <a href="<?php echo esc_url($video_permalink); ?>" class="hover:text-accent transition">
                                    <?php echo esc_html($video_name); ?>
                                </a>
                            </h3>
                            
                            <div class="text-gray-600 mb-4 line-clamp-2">
                                <?php echo get_the_excerpt($video); ?>
                            </div>
                            
                            <div class="product-actions">
                                <a href="<?php echo esc_url($video_permalink); ?>" class="button bg-accent text-dark hover:bg-accent/90">
                                    <i class="fas fa-play ml-1"></i>
                                    مشاهدة الفيديو
                                </a>
                                
                                <button type="button" class="sarah-loz-remove-favorite" data-product-id="<?php echo esc_attr($video_id); ?>" data-nonce="<?php echo wp_create_nonce('sarah_loz_favorite_nonce'); ?>">
                                    <i class="fas fa-heart-broken"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        <?php endif; ?>
        
    <?php endif; ?>
</div> 