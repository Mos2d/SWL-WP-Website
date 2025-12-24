<?php
/**
 * The template for displaying product content within loops
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility
if (empty($product) || !$product->is_visible()) {
    return;
}
?>
<div <?php wc_product_class('bg-white rounded-2xl shadow-lg overflow-hidden transition transform hover:-translate-y-2 hover:shadow-xl', $product); ?>>
    <div class="h-48 bg-<?php echo $product->is_on_sale() ? 'primary' : 'secondary'; ?>/10 relative overflow-hidden">
        <a href="<?php the_permalink(); ?>" class="block h-full">
            <?php if (has_post_thumbnail()) : ?>
                <div class="h-full w-full flex items-center justify-center">
                    <?php echo woocommerce_get_product_thumbnail('woocommerce_thumbnail'); ?>
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
                    <i class="<?php echo $icon_class; ?> text-5xl text-<?php echo $product->is_on_sale() ? 'primary' : 'secondary'; ?>/40"></i>
                </div>
            <?php endif; ?>
        </a>
        
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

    <div class="p-6">
        <?php
        $age_range = get_field('age_range', $product->get_id());
        if ($age_range) : ?>
            <span class="inline-block bg-light text-gray-600 px-3 py-1 rounded-full text-sm mb-3">
                <i class="fas fa-child ml-1"></i><?php echo esc_html($age_range); ?> سنوات
            </span>
        <?php endif; ?>

        <h3 class="text-xl font-bold text-dark mb-2">
            <a href="<?php the_permalink(); ?>" class="hover:text-primary transition">
                <?php the_title(); ?>
            </a>
        </h3>

        <p class="text-gray-600 mb-4">
            <?php 
            $short_description = apply_filters('woocommerce_short_description', $product->get_short_description());
            if ($short_description) {
                echo wp_trim_words($short_description, 10, '...');
            } else {
                $categories = get_the_terms($product->get_id(), 'product_cat');
                if ($categories && !is_wp_error($categories)) {
                    $category = reset($categories);
                    echo esc_html($category->name);
                }
            }
            ?>
        </p>

        <div class="flex justify-between items-center">
            <div>
                <span class="text-xl font-bold text-primary"><?php echo wc_price($product->get_price()); ?></span>
                <?php if ($product->is_on_sale()) : ?>
                    <span class="text-sm text-gray-500 line-through mr-2"><?php echo wc_price($product->get_regular_price()); ?></span>
                <?php endif; ?>
            </div>
            
            <div class="flex items-center">
                <?php
                // Just use the standard filter - our function in woocommerce.php will handle the HTML
                echo apply_filters('woocommerce_loop_add_to_cart_link', '', $product);
                ?>
            </div>
        </div>
    </div>
</div>
