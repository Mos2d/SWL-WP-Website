<?php
/**
 * Content Card Template Part
 * 
 * @param string $post_type The type of post (game, activity, video)
 * @param string $button_text Custom button text
 * @param string $button_color Color class for the button (primary, secondary, accent)
 */

$post_type = isset($args['post_type']) ? $args['post_type'] : get_post_type();
$button_text = isset($args['button_text']) ? $args['button_text'] : __('اقرأ المزيد', 'sarah-loz');
$button_color = isset($args['button_color']) ? $args['button_color'] : 'primary';
?>

<article class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow h-full flex flex-col">
    <?php if (has_post_thumbnail()) : ?>
        <div class="aspect-w-16 aspect-h-9 relative group">
            <?php the_post_thumbnail('medium', ['class' => 'w-full h-full object-cover']); ?>
            <?php if ($post_type === 'video') : ?>
                <div class="absolute inset-0 bg-dark bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-play-circle text-4xl text-white"></i>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <div class="p-6 flex-grow flex flex-col">
        <?php if ($age_range = get_field('age_range')) : ?>
            <span class="inline-block bg-light text-gray-600 px-3 py-1 rounded-full text-sm mb-3">
                <i class="fas fa-child ml-1"></i><?php echo esc_html($age_range); ?> سنوات
            </span>
        <?php endif; ?>

        <h2 class="text-xl font-bold mb-3">
            <a href="<?php the_permalink(); ?>" class="text-dark hover:text-<?php echo $button_color; ?> transition-colors">
                <?php the_title(); ?>
            </a>
        </h2>

        <?php if ($post_type === 'game' && ($difficulty = get_field('difficulty_level'))) : ?>
            <span class="inline-block bg-light text-gray-600 px-3 py-1 rounded-full text-sm mb-3">
                <i class="fas fa-star ml-1"></i><?php echo esc_html($difficulty); ?>
            </span>
        <?php endif; ?>
        
        <div class="text-gray-600 mb-4 flex-grow">
            <?php the_excerpt(); ?>
        </div>
        
        <div class="mt-auto">
            <?php
            $terms = get_the_terms(get_the_ID(), $post_type . '_category');
            if ($terms && !is_wp_error($terms)) : ?>
                <div class="flex flex-wrap gap-2 mb-4">
                    <?php foreach ($terms as $term) : ?>
                        <span class="text-sm text-gray-600">
                            <i class="fas fa-folder ml-1"></i><?php echo esc_html($term->name); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="flex items-center justify-between">
                <a href="<?php the_permalink(); ?>" 
                class="inline-block bg-<?php echo $button_color; ?> text-white px-4 py-2 rounded-full hover:bg-opacity-90 transition-colors">
                    <?php echo esc_html($button_text); ?>
                </a>
                
                <?php 
                // Add favorite button for activities and videos
                if ($post_type === 'activity' || $post_type === 'video') :
                    $product_id = get_the_ID();
                    $is_favorite = function_exists('sarah_loz_is_favorite') ? sarah_loz_is_favorite($product_id) : false;
                    
                    $active_class = $is_favorite ? 'text-primary' : 'text-gray-400';
                    $active_icon = $is_favorite ? 'fas' : 'far';
                ?>
                    <button type="button" class="sarah-loz-toggle-favorite-loop ml-2 text-lg <?php echo $active_class; ?> hover:text-primary transition" data-product-id="<?php echo esc_attr($product_id); ?>" data-nonce="<?php echo wp_create_nonce('sarah_loz_favorite_nonce'); ?>">
                        <i class="<?php echo $active_icon; ?> fa-heart"></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</article>
