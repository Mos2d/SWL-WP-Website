<?php
/**
 * The template for displaying single activities
 *
 * @package Sarah_Loz
 */

get_header();

// Get custom fields
$age_range = get_field('age_range');
$materials = get_field('required_materials');
$pdf_attachments = get_field('pdf_attachments');
$activity_duration = get_field('activity_duration');
$activity_rating = get_field('activity_rating');
$activity_views = get_field('activity_views');
$activity_steps = get_field('activity_steps');
$activity_benefits = get_field('educational_benefits');
$instructor = get_field('instructor');
$workshop_materials = get_field('workshop_materials');
$gallery_images = get_field('gallery_images');
$reviews = get_field('reviews');

// Update view count
if ($activity_views) {
    update_field('activity_views', $activity_views + 1);
} else {
    update_field('activity_views', 1);
}
?>

<style>
/* Ensure all video embeds are responsive and properly displayed */
.video-embed-container {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%; /* 16:9 aspect ratio */
    height: 0;
    overflow: hidden;
}

.video-embed-container iframe,
.video-embed-container video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 0;
}
</style>

<!-- Main Content -->
<main class="container mx-auto px-4 py-10">
    <?php while (have_posts()) : the_post(); ?>
        <!-- Breadcrumb -->
        <div class="text-sm text-gray-500 mb-6">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-primary transition"><?php _e('الرئيسية', 'sarah-loz'); ?></a>
            <span class="mx-2">/</span>
            <a href="<?php echo esc_url(get_post_type_archive_link('activity')); ?>" class="hover:text-primary transition"><?php _e('الأنشطة', 'sarah-loz'); ?></a>
            <span class="mx-2">/</span>
            <span class="text-primary"><?php the_title(); ?></span>
        </div>

        <!-- Activity Header -->
        <article class="bg-white rounded-3xl shadow-lg overflow-hidden mb-10">
            <div class="relative">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="h-64 md:h-96 overflow-hidden">
                        <?php the_post_thumbnail('large', array('class' => 'w-full h-full object-cover')); ?>
                    </div>
                <?php else : ?>
                    <div class="h-64 md:h-96 bg-secondary/20 relative">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="fas fa-paint-brush text-8xl text-secondary/50"></i>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="p-8">
                <div class="flex flex-wrap justify-between items-center mb-4">
                    <h1 class="text-3xl md:text-4xl font-bold text-dark"><?php the_title(); ?></h1>
                    <div class="flex items-center mt-2 md:mt-0">
                        <?php if ($age_range) : ?>
                            <span class="bg-accent text-dark px-3 py-1 rounded-full text-sm ml-2">
                                <?php echo esc_html($age_range); ?> <?php _e('سنوات', 'sarah-loz'); ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php
                        $categories = get_the_terms(get_the_ID(), 'activity_category');
                        if ($categories && !is_wp_error($categories)) :
                            foreach (array_slice($categories, 0, 1) as $category) : ?>
                                <span class="bg-secondary/10 text-secondary px-3 py-1 rounded-full text-sm">
                                    <?php echo esc_html($category->name); ?>
                                </span>
                            <?php endforeach;
                        endif; ?>
                    </div>
                </div>
                
                <p class="text-gray-600 text-lg mb-6">
                    <?php echo get_the_excerpt(); ?>
                </p>

                <div class="flex flex-wrap gap-4 mb-8">
                    <a href="#watch" class="bg-secondary text-white px-6 py-3 rounded-full text-lg shadow-lg hover:bg-secondary/90 transition transform hover:-translate-y-1">
                        <i class="fas fa-play-circle ml-2"></i>
                        <?php _e('شاهد الآن', 'sarah-loz'); ?>
                    </a>
                    <a href="#materials" class="bg-primary text-white px-6 py-3 rounded-full text-lg shadow-lg hover:bg-primary/90 transition transform hover:-translate-y-1">
                        <i class="fas fa-list-ul ml-2"></i>
                        <?php _e('المواد المطلوبة', 'sarah-loz'); ?>
                    </a>
                    <?php 
                    // Add favorite button
                    $product_id = get_the_ID();
                    $is_favorite = function_exists('sarah_loz_is_favorite') ? sarah_loz_is_favorite($product_id) : false;
                    
                    $active_class = $is_favorite ? 'bg-primary text-white' : 'bg-accent text-dark';
                    $active_text = $is_favorite ? 'إزالة من المفضلة' : 'أضف للمفضلة';
                    $active_icon = $is_favorite ? 'fas' : 'far';
                    ?>
                    <button type="button" class="sarah-loz-toggle-favorite <?php echo $active_class; ?> px-6 py-3 rounded-full text-lg shadow-lg hover:bg-opacity-90 transition transform hover:-translate-y-1" data-product-id="<?php echo esc_attr($product_id); ?>" data-nonce="<?php echo wp_create_nonce('sarah_loz_favorite_nonce'); ?>">
                        <i class="<?php echo $active_icon; ?> fa-heart ml-2"></i>
                        <?php echo $active_text; ?>
                    </button>
                </div>

                <div class="flex items-center text-sm text-gray-500 justify-between">
                    <div class="flex items-center">
                        <?php if ($activity_rating) : ?>
                            <span class="ml-4"><i class="fas fa-star text-accent ml-1"></i> <?php echo esc_html($activity_rating); ?></span>
                        <?php endif; ?>
                        <?php if ($activity_views) : ?>
                            <span><i class="fas fa-users ml-1"></i> <?php echo esc_html($activity_views); ?> <?php _e('مشاهدة', 'sarah-loz'); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center">
                        <?php if ($activity_duration) : ?>
                            <span class="ml-4"><i class="far fa-clock ml-1"></i> <?php echo esc_html($activity_duration); ?></span>
                        <?php endif; ?>
                        <span class="activity-share-btn cursor-pointer"><i class="fas fa-share-alt ml-1"></i> <?php _e('مشاركة', 'sarah-loz'); ?></span>
                    </div>
                </div>
            </div>
        </article>

        <!-- Activity Information -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <!-- Activity Main Section -->
            <div class="lg:col-span-2">
                <!-- Video Preview -->
                <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-8" id="watch">
                    <h2 class="text-2xl font-bold p-6 bg-secondary/10 text-secondary">
                        <i class="fas fa-play-circle ml-2"></i>
                        <?php _e('مشاهدة الورشة', 'sarah-loz'); ?>
                    </h2>
                    <div class="p-6">
                        <?php 
                        $video_url = get_field('video_url');
                        if ($video_url) : 
                            // Create responsive container
                            echo '<div class="video-embed-container">';
                            
                            // Extract YouTube or Vimeo ID and create embedded player
                            if (strpos($video_url, 'youtube') !== false || strpos($video_url, 'youtu.be') !== false) {
                                // YouTube embed code
                                echo wp_oembed_get($video_url);
                            } elseif (strpos($video_url, 'vimeo') !== false) {
                                // Vimeo embed code
                                echo wp_oembed_get($video_url);
                            } else {
                                echo '<video controls class="w-full h-full" src="'.esc_url($video_url).'"></video>';
                            }
                            
                            echo '</div>'; // Close video-embed-container
                        else : ?>
                            <div class="h-96 bg-gray-200 rounded-xl flex items-center justify-center">
                                <div class="text-center">
                                    <i class="fas fa-play-circle text-6xl text-gray-400 mb-4"></i>
                                    <p class="text-xl text-gray-500"><?php _e('لا يوجد فيديو لهذا النشاط', 'sarah-loz'); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Required Materials -->
                <?php if ($materials) : ?>
                    <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-8" id="materials">
                        <h2 class="text-2xl font-bold p-6 bg-primary/10 text-primary">
                            <i class="fas fa-list-ul ml-2"></i>
                            <?php _e('المواد المطلوبة', 'sarah-loz'); ?>
                        </h2>
                        <div class="p-6">
                            <?php 
                            // Check if materials is an array
                            if (is_array($materials)) : ?>
                                <ul class="space-y-4">
                                    <?php foreach ($materials as $index => $material) : 
                                        $icons = ['pencil-alt', 'palette', 'eraser', 'scroll', 'ruler'];
                                        $colors = ['primary', 'accent', 'secondary', 'primary', 'accent'];
                                        $icon_index = $index % count($icons);
                                    ?>
                                        <li class="flex items-center p-3 bg-light rounded-lg">
                                            <div class="w-10 h-10 bg-<?php echo $colors[$icon_index]; ?>/20 rounded-full flex items-center justify-center ml-3">
                                                <i class="fas fa-<?php echo $icons[$icon_index]; ?> text-<?php echo $colors[$icon_index]; ?>"></i>
                                            </div>
                                            <span class="text-gray-600"><?php echo esc_html($material['name']); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else : ?>
                                <div class="prose prose-lg">
                                    <?php echo wp_kses_post($materials); ?>
                                </div>
                            <?php endif; ?>

                            <?php if (get_field('materials_note')) : ?>
                                <div class="mt-6 p-4 bg-accent/10 rounded-xl">
                                    <h3 class="text-xl font-bold text-dark mb-2"><?php _e('ملاحظة للأهل:', 'sarah-loz'); ?></h3>
                                    <p class="text-gray-600">
                                        <?php echo wp_kses_post(get_field('materials_note')); ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Workshop Steps -->
                <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-8">
                    <h2 class="text-2xl font-bold p-6 bg-secondary/10 text-secondary">
                        <i class="fas fa-list-ol ml-2"></i>
                        <?php _e('خطوات الورشة', 'sarah-loz'); ?>
                    </h2>
                    <div class="p-6">
                        <?php if (is_array($activity_steps) && !empty($activity_steps)) : ?>
                            <ol class="space-y-6">
                                <?php foreach ($activity_steps as $index => $step) : ?>
                                    <li>
                                        <div class="bg-light p-4 rounded-xl">
                                            <div class="flex items-center mb-2">
                                                <div class="w-8 h-8 bg-secondary flex items-center justify-center rounded-full text-white font-bold ml-2">
                                                    <?php echo $index + 1; ?>
                                                </div>
                                                <h3 class="text-xl font-bold text-dark">
                                                    <?php echo esc_html($step['title']); ?>
                                                </h3>
                                            </div>
                                            <p class="text-gray-600 mb-2">
                                                <?php echo wp_kses_post($step['description']); ?>
                                            </p>
                                            <?php if (!empty($step['duration'])) : ?>
                                                <div class="mt-2">
                                                    <span class="inline-block bg-secondary/10 text-secondary text-sm px-2 py-1 rounded-full">
                                                        <?php echo esc_html($step['duration']); ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        <?php else : ?>
                            <!-- Default steps if no custom ones defined -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-light rounded-2xl p-6">
                                    <div class="flex items-center mb-3">
                                        <span class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center ml-3">1</span>
                                        <h3 class="text-xl font-bold text-dark"><?php _e('تجهيز الأدوات', 'sarah-loz'); ?></h3>
                                    </div>
                                    <p class="text-gray-600">
                                        <?php _e('قم بتجهيز جميع الأدوات المطلوبة للنشاط قبل البدء مع الطفل.', 'sarah-loz'); ?>
                                    </p>
                                </div>
                                
                                <div class="bg-light rounded-2xl p-6">
                                    <div class="flex items-center mb-3">
                                        <span class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center ml-3">2</span>
                                        <h3 class="text-xl font-bold text-dark"><?php _e('شرح النشاط', 'sarah-loz'); ?></h3>
                                    </div>
                                    <p class="text-gray-600">
                                        <?php _e('اشرح للطفل طريقة النشاط والهدف منه بطريقة واضحة وبسيطة.', 'sarah-loz'); ?>
                                    </p>
                                </div>
                                
                                <div class="bg-light rounded-2xl p-6">
                                    <div class="flex items-center mb-3">
                                        <span class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center ml-3">3</span>
                                        <h3 class="text-xl font-bold text-dark"><?php _e('البدء بالنشاط', 'sarah-loz'); ?></h3>
                                    </div>
                                    <p class="text-gray-600">
                                        <?php _e('ساعد الطفل في بداية النشاط ثم اتركه يكمل بنفسه مع المراقبة.', 'sarah-loz'); ?>
                                    </p>
                                </div>
                                
                                <div class="bg-light rounded-2xl p-6">
                                    <div class="flex items-center mb-3">
                                        <span class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center ml-3">4</span>
                                        <h3 class="text-xl font-bold text-dark"><?php _e('التشجيع والتحفيز', 'sarah-loz'); ?></h3>
                                    </div>
                                    <p class="text-gray-600">
                                        <?php _e('شجع الطفل خلال النشاط وامدحه على إنجازاته مهما كانت بسيطة.', 'sarah-loz'); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Educational Benefits -->
                <?php if ($activity_benefits) : ?>
                    <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-8">
                        <h2 class="text-2xl font-bold p-6 bg-accent/10 text-accent">
                            <i class="fas fa-graduation-cap ml-2"></i>
                            <?php _e('الفوائد التعليمية', 'sarah-loz'); ?>
                        </h2>
                        <div class="p-6">
                            <?php if (is_array($activity_benefits)) : ?>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <?php 
                                    $colors = ['secondary', 'primary', 'accent', 'secondary'];
                                    foreach ($activity_benefits as $index => $benefit) : 
                                        $color = $colors[$index % count($colors)];
                                    ?>
                                        <div class="p-4 bg-light rounded-xl">
                                            <h3 class="font-bold text-<?php echo $color; ?> text-lg mb-2">
                                                <?php echo esc_html($benefit['title']); ?>
                                            </h3>
                                            <p class="text-gray-600">
                                                <?php echo wp_kses_post($benefit['description']); ?>
                                            </p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else : ?>
                                <div class="prose prose-lg">
                                    <?php echo wp_kses_post($activity_benefits); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- PDF Attachments -->
                <?php if ($pdf_attachments) : ?>
                    <div class="bg-primary/10 rounded-2xl p-6 mb-10">
                        <h2 class="text-2xl font-bold text-dark mb-4">
                            <i class="fas fa-file-pdf ml-2 text-primary"></i>
                            <?php _e('ملفات للتحميل', 'sarah-loz'); ?>
                        </h2>
                        <div class="flex flex-wrap gap-4">
                            <?php 
                            // Check if PDFs is an array
                            if (is_array($pdf_attachments)) {
                                foreach ($pdf_attachments as $pdf) : ?>
                                    <a 
                                        href="<?php echo esc_url($pdf['file']['url']); ?>" 
                                        class="inline-flex items-center bg-white text-primary border border-primary px-6 py-3 rounded-full hover:bg-primary hover:text-white transition shadow-lg"
                                        target="_blank"
                                        download
                                    >
                                        <i class="fas fa-download ml-2"></i>
                                        <?php echo esc_html($pdf['title'] ?: __('تحميل الملف', 'sarah-loz')); ?>
                                    </a>
                                <?php endforeach;
                            } else { ?>
                                <a 
                                    href="<?php echo esc_url($pdf_attachments['url']); ?>" 
                                    class="inline-flex items-center bg-white text-primary border border-primary px-6 py-3 rounded-full hover:bg-primary hover:text-white transition shadow-lg"
                                    target="_blank"
                                    download
                                >
                                    <i class="fas fa-download ml-2"></i>
                                    <?php _e('تحميل ورقة النشاط', 'sarah-loz'); ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Workshop Instructor -->
                <?php if ($instructor) : ?>
                    <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-8">
                        <h2 class="text-xl font-bold p-4 bg-secondary/10 text-secondary">
                            <?php _e('مقدم الورشة', 'sarah-loz'); ?>
                        </h2>
                        <div class="p-4 text-center">
                            <?php if (!empty($instructor['photo'])) : ?>
                                <img src="<?php echo esc_url($instructor['photo']['url']); ?>" alt="<?php echo esc_attr($instructor['name']); ?>" class="w-24 h-24 mx-auto rounded-full object-cover mb-3">
                            <?php else : ?>
                                <div class="w-24 h-24 mx-auto bg-secondary/20 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-user text-3xl text-secondary"></i>
                                </div>
                            <?php endif; ?>
                            
                            <h3 class="font-bold text-xl text-dark mb-1"><?php echo esc_html($instructor['name']); ?></h3>
                            <p class="text-gray-500 mb-3"><?php echo esc_html($instructor['title']); ?></p>
                            <p class="text-gray-600 text-sm mb-4"><?php echo wp_kses_post($instructor['bio']); ?></p>
                            
                            <?php if (!empty($instructor['social_links'])) : ?>
                                <div class="flex justify-center space-x-2 space-x-reverse">
                                    <?php foreach ($instructor['social_links'] as $link) : ?>
                                        <a href="<?php echo esc_url($link['url']); ?>" class="text-secondary hover:text-secondary/80 text-xl transition">
                                            <i class="fab fa-<?php echo esc_attr($link['platform']); ?>"></i>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Related Activities -->
                <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-8">
                    <h2 class="text-xl font-bold p-4 bg-secondary/10 text-secondary">
                        <?php _e('أنشطة مشابهة', 'sarah-loz'); ?>
                    </h2>
                    <div class="p-4">
                        <div class="space-y-4">
                            <?php
                            // Get current post's terms
                            $activity_categories = wp_get_post_terms(get_the_ID(), 'activity_category', array('fields' => 'ids'));
                            
                            // Set up tax query
                            $tax_query = array();
                            
                            if (!empty($activity_categories)) {
                                $tax_query[] = array(
                                    'taxonomy' => 'activity_category',
                                    'field' => 'term_id',
                                    'terms' => $activity_categories
                                );
                            }
                            
                            $related_query = new WP_Query(array(
                                'post_type' => 'activity',
                                'posts_per_page' => 3,
                                'post__not_in' => array(get_the_ID()),
                                'tax_query' => $tax_query,
                                'orderby' => 'rand'
                            ));
                            
                            if ($related_query->have_posts()) :
                                while ($related_query->have_posts()) : $related_query->the_post(); 
                                    $rel_age_range = get_field('age_range');
                                    $icons = ['cut', 'theatre-masks', 'paint-roller'];
                                    $colors = ['primary', 'accent', 'secondary'];
                                    $icon_index = $related_query->current_post % 3;
                            ?>
                                    <div class="flex items-center p-2 rounded-xl hover:bg-light transition">
                                        <div class="w-16 h-16 bg-<?php echo $colors[$icon_index]; ?>/20 rounded-xl flex items-center justify-center ml-3">
                                            <i class="fas fa-<?php echo $icons[$icon_index]; ?> text-<?php echo $colors[$icon_index]; ?>"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-dark">
                                                <a href="<?php the_permalink(); ?>" class="hover:text-primary">
                                                    <?php the_title(); ?>
                                                </a>
                                            </h3>
                                            <p class="text-sm text-gray-500">
                                                <?php if ($rel_age_range) : ?>
                                                    <?php echo esc_html($rel_age_range); ?> <?php _e('سنوات', 'sarah-loz'); ?>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endwhile;
                                wp_reset_postdata();
                            endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Workshop Materials for Sale -->
                <?php if ($workshop_materials) : ?>
                    <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-8">
                        <h2 class="text-xl font-bold p-4 bg-accent/10 text-accent">
                            <?php _e('مستلزمات الورشة', 'sarah-loz'); ?>
                        </h2>
                        <div class="p-4">
                            <div class="space-y-4">
                                <?php 
                                // Make sure WooCommerce is active
                                if (class_exists('WooCommerce')) :
                                    foreach ($workshop_materials as $item) : 
                                        // Get product data using the stored product ID
                                        $product_id = $item['product_id'];
                                        $product = wc_get_product($product_id);
                                        
                                        // Only display if it's a valid product
                                        if ($product) :
                                            $product_name = $product->get_name();
                                            $product_price = $product->get_price_html();
                                            $product_image_id = $product->get_image_id();
                                            $product_desc = $product->get_short_description();
                                            $product_url = get_permalink($product_id);
                                        ?>
                                            <div class="p-3 border border-gray-100 rounded-xl hover:bg-light transition">
                                                <?php if ($product_image_id) : ?>
                                                <div class="flex items-center mb-2">
                                                    <div class="w-16 h-16 bg-light rounded-xl overflow-hidden ml-3">
                                                        <?php echo wp_get_attachment_image($product_image_id, 'thumbnail', false, array('class' => 'w-full h-full object-cover')); ?>
                                                    </div>
                                                    <div>
                                                        <h3 class="font-bold text-dark"><?php echo esc_html($product_name); ?></h3>
                                                        <div class="font-bold text-accent"><?php echo $product_price; ?></div>
                                                    </div>
                                                </div>
                                                <?php else : ?>
                                                <div class="flex justify-between items-center mb-2">
                                                    <h3 class="font-bold text-dark"><?php echo esc_html($product_name); ?></h3>
                                                    <div class="font-bold text-accent"><?php echo $product_price; ?></div>
                                                </div>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($item['note'])) : ?>
                                                    <p class="text-sm text-gray-600 mb-2">
                                                        <?php echo wp_kses_post($item['note']); ?>
                                                    </p>
                                                <?php elseif (!empty($product_desc)) : ?>
                                                    <p class="text-sm text-gray-600 mb-2">
                                                        <?php echo wp_kses_post($product_desc); ?>
                                                    </p>
                                                <?php endif; ?>
                                                
                                                <div class="flex gap-2">
                                                    <button class="add-to-cart-btn flex-1 bg-accent text-white py-1 px-3 rounded-full text-sm hover:bg-accent/90 transition" 
                                                            data-product-id="<?php echo esc_attr($product_id); ?>">
                                                        <i class="fas fa-cart-plus ml-1"></i> <?php _e('إضافة للسلة', 'sarah-loz'); ?>
                                                    </button>
                                                    <a href="<?php echo esc_url($product_url); ?>" class="inline-block bg-light text-accent py-1 px-3 rounded-full text-sm hover:bg-accent/10 transition">
                                                        <i class="fas fa-eye ml-1"></i> <?php _e('عرض', 'sarah-loz'); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <p class="text-center text-gray-500"><?php _e('يجب تفعيل إضافة ووكومرس لعرض المنتجات', 'sarah-loz'); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php 
        // Display related practices
        do_action('sarah_loz_after_activity_content', get_the_ID()); 
        ?>

        <!-- Gallery Section -->
        <?php if ($gallery_images) : ?>
            <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-10">
                <h2 class="text-2xl font-bold p-6 bg-secondary/10 text-secondary">
                    <i class="fas fa-images ml-2"></i>
                    <?php _e('معرض رسومات المشاركين', 'sarah-loz'); ?>
                </h2>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        <?php foreach ($gallery_images as $image) : ?>
                            <div class="bg-light rounded-xl h-32 overflow-hidden relative hover:shadow-md transition">
                                <?php if (!empty($image['image'])) : ?>
                                    <img src="<?php echo esc_url($image['image']['sizes']['thumbnail']); ?>" 
                                         alt="<?php echo esc_attr($image['title']); ?>"
                                         class="w-full h-full object-cover">
                                <?php else : ?>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <i class="fas fa-image text-secondary/50 text-3xl"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-6 text-center">
                        <button class="inline-block bg-secondary text-white px-6 py-2 rounded-full hover:bg-secondary/90 transition" id="share-gallery-btn">
                            <?php _e('شارك رسمتك', 'sarah-loz'); ?>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Reviews Section -->
        <?php 
        $reviews_enabled = get_field('enable_reviews');
        if ($reviews_enabled && $reviews) : 
            $reviews_count = count($reviews);
            $average_rating = 0;
            
            // Calculate average rating
            if ($reviews_count > 0) {
                $total_rating = 0;
                foreach ($reviews as $review) {
                    $total_rating += $review['rating'];
                }
                $average_rating = number_format($total_rating / $reviews_count, 1);
            }
        ?>
            <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-10">
                <h2 class="text-2xl font-bold p-6 bg-secondary/10 text-secondary">
                    <i class="fas fa-comments ml-2"></i>
                    <?php _e('آراء المشاركين', 'sarah-loz'); ?>
                </h2>
                <div class="p-6">
                    <div class="flex flex-col md:flex-row items-start gap-6 mb-8">
                        <!-- Rating Summary -->
                        <div class="md:w-1/3 text-center p-4 bg-light rounded-xl">
                            <div class="text-5xl font-bold text-secondary mb-2"><?php echo esc_html($average_rating); ?></div>
                            <div class="flex justify-center items-center text-accent mb-3">
                                <?php 
                                // Display stars based on rating
                                $full_stars = floor($average_rating);
                                $half_star = $average_rating - $full_stars >= 0.5;
                                
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $full_stars) {
                                        echo '<i class="fas fa-star"></i>';
                                    } elseif ($i == $full_stars + 1 && $half_star) {
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <p class="text-gray-500 mb-4"><?php printf(__('من %s تقييم', 'sarah-loz'), $reviews_count); ?></p>
                            <button class="bg-secondary text-white px-4 py-2 rounded-full text-sm hover:bg-secondary/90 transition w-full" id="add-review-btn">
                                <i class="fas fa-plus-circle ml-1"></i>
                                <?php _e('أضف تقييمك', 'sarah-loz'); ?>
                            </button>
                        </div>

                        <!-- Reviews -->
                        <div class="md:w-2/3">
                            <div class="space-y-4">
                                <?php 
                                // Show first 3 reviews
                                $display_reviews = array_slice($reviews, 0, 3);
                                foreach ($display_reviews as $index => $review) : 
                                    $colors = ['secondary', 'primary', 'accent'];
                                    $color = $colors[$index % 3];
                                    
                                    // Create initials from name
                                    $name_parts = explode(' ', $review['name']);
                                    $initials = '';
                                    foreach ($name_parts as $part) {
                                        $initials .= mb_substr($part, 0, 1, 'UTF-8');
                                    }
                                    if (strlen($initials) > 2) {
                                        $initials = mb_substr($initials, 0, 2, 'UTF-8');
                                    }
                                ?>
                                    <div class="p-4 <?php echo $index < count($display_reviews) - 1 ? 'border-b border-gray-100' : ''; ?>">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-<?php echo $color; ?>/20 rounded-full flex items-center justify-center ml-2">
                                                    <span class="text-<?php echo $color; ?> font-bold"><?php echo esc_html($initials); ?></span>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold"><?php echo esc_html($review['name']); ?></h4>
                                                    <div class="flex text-accent text-sm">
                                                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                            <i class="fas fa-star<?php echo $i > $review['rating'] ? '-o' : ''; ?>"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-500"><?php echo esc_html($review['date']); ?></span>
                                        </div>
                                        <p class="text-gray-600">
                                            <?php echo wp_kses_post($review['content']); ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if ($reviews_count > 3) : ?>
                                <div class="mt-4 text-center">
                                    <button class="text-secondary hover:underline" id="view-all-reviews-btn">
                                        <?php printf(__('عرض كل التقييمات (%s)', 'sarah-loz'), $reviews_count); ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Related Activities Grid (For Mobile Screens) -->
        <div class="mb-16 lg:hidden">
            <h2 class="text-2xl md:text-3xl font-bold text-dark mb-8">
                <span class="border-b-4 border-primary pb-2"><?php _e('أنشطة مشابهة', 'sarah-loz'); ?></span>
            </h2>
            
            <?php
            // Reuse the query from above but reset it first
            wp_reset_postdata();
            
            $related_query = new WP_Query(array(
                'post_type' => 'activity',
                'posts_per_page' => 3,
                'post__not_in' => array(get_the_ID()),
                'tax_query' => $tax_query,
                'orderby' => 'rand'
            ));
            
            if ($related_query->have_posts()) : ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php while ($related_query->have_posts()) : $related_query->the_post(); 
                        $rel_age_range = get_field('age_range');
                    ?>
                        <div class="bg-white rounded-3xl shadow-lg overflow-hidden transition transform hover:-translate-y-2 hover:shadow-xl">
                            <div class="relative">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="h-48 overflow-hidden">
                                        <?php the_post_thumbnail('medium_large', array('class' => 'w-full h-full object-cover')); ?>
                                    </div>
                                <?php else : ?>
                                    <div class="h-48 bg-primary/10 flex items-center justify-center">
                                        <i class="fas fa-paint-brush text-6xl text-primary/40"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-dark mb-3">
                                    <a href="<?php the_permalink(); ?>" class="hover:text-primary">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>
                                <p class="text-gray-600 mb-4">
                                    <?php echo wp_trim_words(get_the_excerpt(), 10); ?>
                                </p>
                                
                                <div class="flex justify-between items-center mt-4">
                                    <?php if ($rel_age_range) : ?>
                                        <span class="text-sm text-gray-500">
                                            <i class="fas fa-child ml-1"></i> <?php echo esc_html($rel_age_range); ?> <?php _e('سنوات', 'sarah-loz'); ?>
                                        </span>
                                    <?php endif; ?>
                                    <a 
                                        href="<?php the_permalink(); ?>" 
                                        class="bg-primary text-white px-4 py-2 rounded-full hover:bg-primary/90 transition"
                                    >
                                        <?php _e('عرض النشاط', 'sarah-loz'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                <?php wp_reset_postdata();
            endif; ?>
        </div>
        
        <!-- Cross-Post-Type Related Content -->
        <?php
        // Get current post's categories
        $activity_categories = get_the_terms(get_the_ID(), 'activity_category');
        if ($activity_categories && !is_wp_error($activity_categories)) {
            $category_names = array();
            $category_ids = array();
            foreach ($activity_categories as $cat) {
                $category_names[] = $cat->name;
                $category_ids[] = $cat->term_id;
            }
            
            // Check for games with same category name
            $related_games = array();
            $game_terms = get_terms(array(
                'taxonomy' => 'game_category',
                'hide_empty' => true,
            ));
            
            $game_term_ids = array();
            if ($game_terms && !is_wp_error($game_terms)) {
                foreach ($game_terms as $term) {
                    if (in_array($term->name, $category_names)) {
                        $game_term_ids[] = $term->term_id;
                    }
                }
            }
            
            if (!empty($game_term_ids)) {
                $games_query = new WP_Query(array(
                    'post_type' => 'game',
                    'posts_per_page' => 3,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'game_category',
                            'field' => 'term_id',
                            'terms' => $game_term_ids,
                        ),
                    ),
                ));
                
                if ($games_query->have_posts()) {
                    while ($games_query->have_posts()) {
                        $games_query->the_post();
                        $related_games[] = array(
                            'id' => get_the_ID(),
                            'title' => get_the_title(),
                            'permalink' => get_permalink(),
                            'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
                            'type' => 'game',
                            'type_label' => __('لعبة', 'sarah-loz'),
                        );
                    }
                    wp_reset_postdata();
                }
            }
            
            // Check for videos with same category name
            $related_videos = array();
            $video_terms = get_terms(array(
                'taxonomy' => 'video_category',
                'hide_empty' => true,
            ));
            
            $video_term_ids = array();
            if ($video_terms && !is_wp_error($video_terms)) {
                foreach ($video_terms as $term) {
                    if (in_array($term->name, $category_names)) {
                        $video_term_ids[] = $term->term_id;
                    }
                }
            }
            
            if (!empty($video_term_ids)) {
                $videos_query = new WP_Query(array(
                    'post_type' => 'video',
                    'posts_per_page' => 3,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'video_category',
                            'field' => 'term_id',
                            'terms' => $video_term_ids,
                        ),
                    ),
                ));
                
                if ($videos_query->have_posts()) {
                    while ($videos_query->have_posts()) {
                        $videos_query->the_post();
                        $related_videos[] = array(
                            'id' => get_the_ID(),
                            'title' => get_the_title(),
                            'permalink' => get_permalink(),
                            'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
                            'type' => 'video',
                            'type_label' => __('فيديو', 'sarah-loz'),
                        );
                    }
                    wp_reset_postdata();
                }
            }
            
            // Display related content if we have any
            $related_content = array_merge($related_games, $related_videos);
            if (!empty($related_content)) :
            ?>
            <div class="mb-16">
                <h2 class="text-2xl md:text-3xl font-bold text-dark mb-8">
                    <span class="border-b-4 border-primary pb-2"><?php _e('محتوى ذو صلة', 'sarah-loz'); ?></span>
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php foreach ($related_content as $item) : ?>
                        <div class="bg-white rounded-3xl shadow-lg overflow-hidden transition transform hover:-translate-y-2 hover:shadow-xl">
                            <div class="relative">
                                <?php if (!empty($item['thumbnail'])) : ?>
                                    <div class="h-48 overflow-hidden">
                                        <img src="<?php echo esc_url($item['thumbnail']); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="w-full h-full object-cover">
                                    </div>
                                <?php else : ?>
                                    <div class="h-48 bg-primary/10 flex items-center justify-center">
                                        <?php if ($item['type'] === 'game') : ?>
                                            <i class="fas fa-gamepad text-6xl text-primary/40"></i>
                                        <?php elseif ($item['type'] === 'video') : ?>
                                            <i class="fas fa-play-circle text-6xl text-primary/40"></i>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <span class="absolute top-4 right-4 bg-accent text-dark px-3 py-1 rounded-full text-sm font-bold">
                                    <?php echo esc_html($item['type_label']); ?>
                                </span>
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-dark mb-3">
                                    <a href="<?php echo esc_url($item['permalink']); ?>" class="hover:text-primary">
                                        <?php echo esc_html($item['title']); ?>
                                    </a>
                                </h3>
                                
                                <div class="flex justify-end mt-4">
                                    <a 
                                        href="<?php echo esc_url($item['permalink']); ?>" 
                                        class="bg-primary text-white px-4 py-2 rounded-full hover:bg-primary/90 transition"
                                    >
                                        <?php 
                                        if ($item['type'] === 'game') {
                                            _e('العب الآن', 'sarah-loz');
                                        } elseif ($item['type'] === 'video') {
                                            _e('شاهد الفيديو', 'sarah-loz');
                                        }
                                        ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php
            endif;
        }
        ?>
    <?php endwhile; ?>
</main>

<script>
jQuery(document).ready(function($) {
    // Ensure woocommerce_params is defined
    if (typeof woocommerce_params === 'undefined') {
        window.woocommerce_params = {
            ajax_url: '<?php echo esc_js(admin_url('admin-ajax.php')); ?>',
            wc_ajax_url: '<?php echo esc_js(add_query_arg('wc-ajax', '%%endpoint%%', home_url('/'))); ?>'
        };
    }
    
    // Share activity functionality
    $('.activity-share-btn').on('click', function() {
        // Get current URL
        const url = window.location.href;
        
        // Create a temporary input element
        const $temp = $('<input>');
        $('body').append($temp);
        $temp.val(url).select();
        
        // Copy URL to clipboard
        document.execCommand('copy');
        $temp.remove();
        
        // Show message
        alert('<?php _e('تم نسخ رابط النشاط للمشاركة', 'sarah-loz'); ?>');
    });
    
    // Add to cart functionality
    $('.add-to-cart-btn').on('click', function() {
        const $button = $(this);
        const productId = $button.data('product-id');
        
        // Show loading state
        const originalText = $button.html();
        $button.html('<i class="fas fa-spinner fa-spin ml-1"></i> <?php _e('جاري الإضافة...', 'sarah-loz'); ?>');
        $button.prop('disabled', true);
        
        // Use our custom AJAX endpoint to get cart count in response
        $.ajax({
            url: woocommerce_params.ajax_url,
            type: 'POST',
            data: {
                action: 'sarah_loz_ajax_add_to_cart',
                product_id: productId,
                quantity: 1,
                security: '<?php echo wp_create_nonce('sarah-loz-add-to-cart'); ?>'
            },
            success: function(response) {
                // Success - update button and show success message
                $button.html('<i class="fas fa-check ml-1"></i> <?php _e('تمت الإضافة', 'sarah-loz'); ?>');
                $button.removeClass('bg-accent').addClass('bg-green-500');
                
                // Immediately update cart count in header without waiting for fragment refresh
                if (response.success && response.data && response.data.cart_count) {
                    $('.cart-count').text(response.data.cart_count);
                }
                
                // Update cart fragments
                $(document.body).trigger('wc_fragment_refresh');
                
                // Also trigger our custom event
                $(document.body).trigger('sarah_loz_added_to_cart', [response]);
                
                // Reset button after 2 seconds
                setTimeout(function() {
                    $button.html(originalText);
                    $button.removeClass('bg-green-500').addClass('bg-accent');
                    $button.prop('disabled', false);
                }, 2000);
            },
            error: function() {
                // AJAX error
                $button.html('<i class="fas fa-exclamation-circle ml-1"></i> <?php _e('حدث خطأ', 'sarah-loz'); ?>');
                $button.removeClass('bg-accent').addClass('bg-red-500');
                
                // Reset button after 2 seconds
                setTimeout(function() {
                    $button.html(originalText);
                    $button.removeClass('bg-red-500').addClass('bg-accent');
                    $button.prop('disabled', false);
                }, 2000);
            }
        });
    });
    
    // Share gallery image button
    $('#share-gallery-btn').on('click', function() {
        // Implement your gallery sharing functionality here
        alert('<?php _e('سيتم فتح نافذة مشاركة الصور قريباً', 'sarah-loz'); ?>');
    });
    
    // Add review button
    $('#add-review-btn').on('click', function() {
        // Implement your review form opening functionality here
        alert('<?php _e('سيتم فتح نموذج التقييم قريباً', 'sarah-loz'); ?>');
    });
    
    // View all reviews button
    $('#view-all-reviews-btn').on('click', function() {
        // Implement your "view all reviews" functionality here
        alert('<?php _e('سيتم عرض جميع التقييمات قريباً', 'sarah-loz'); ?>');
    });
    
    // Initialize cart fragments to ensure cart count is updated after AJAX calls
    $(document.body).on('added_to_cart', function() {
        $(document.body).trigger('wc_fragment_refresh');
    });
    
    // Also listen for our custom add to cart success event
    $(document.body).on('sarah_loz_added_to_cart', function() {
        $(document.body).trigger('wc_fragment_refresh');
    });
});
</script>

<?php
get_footer();
?>
