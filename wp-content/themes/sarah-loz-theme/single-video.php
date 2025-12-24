<?php
/**
 * The template for displaying single videos
 *
 * @package Sarah_Loz
 */

// Check if we have an age group selected
$selected_age_group = sarah_loz_get_selected_age_group();
$current_age_data = sarah_loz_get_current_age_group_data();

get_header();

// Get custom fields for the video
$video_url = get_field('video_url');
$duration = get_field('video_duration') ?: '00:00';
$view_count = get_field('view_count') ?: 0;
$is_new = get_field('is_new');
$rating = get_field('rating') ?: 0;
$rating_count = get_field('rating_count') ?: 0;
$learning_materials = get_field('learning_materials');
$age_range = get_field('age_range');

// Format video embed if URL provided
if ($video_url) {
    $video_embed = sarah_loz_format_video_embed($video_url);
}

// Increment view count
if (!is_preview()) {
    $view_count++;
    update_field('view_count', $view_count, get_the_ID());
}

// Track user's watched videos
if (is_user_logged_in()) {
    do_action('sarah_loz_track_progress', get_the_ID());
}
?>

<style>
/* Ensure all video embeds are responsive and properly displayed */
.video-embed-container {
    position: relative;
    width: 100%;
    height: 100%;
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

<!-- Video Content -->
<main class="container mx-auto px-4 py-10" data-id="<?php echo get_the_ID(); ?>">
    <!-- Breadcrumb -->
    <div class="text-sm text-gray-500 mb-6">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-primary transition"><?php _e('الرئيسية', 'sarah-loz'); ?></a>
        <span class="mx-2">/</span>
        <a href="<?php echo esc_url(get_post_type_archive_link('video')); ?>" class="hover:text-primary transition"><?php _e('الفيديوهات', 'sarah-loz'); ?></a>
        <span class="mx-2">/</span>
        <span class="text-primary"><?php the_title(); ?></span>
    </div>

    <!-- Age Group Personalization -->
    <?php if ($selected_age_group && $current_age_data) : ?>
        <div class="bg-<?php echo $current_age_data['color']; ?>/20 rounded-3xl p-6 mb-6 animate__animated animate__bounceIn">
            <div class="flex items-center justify-center gap-4 mb-4">
                <?php if (isset($current_age_data['custom_image'])) : ?>
                    <img src="<?php echo esc_url($current_age_data['custom_image']); ?>" alt="<?php echo esc_attr($current_age_data['label']); ?>" class="w-12 h-12 object-cover rounded-full animate-bounce">
                <?php else : ?>
                    <span class="text-4xl animate-bounce"><?php echo $current_age_data['icon']; ?></span>
                <?php endif; ?>
                <div class="text-center">
                    <h2 class="text-xl font-bold text-<?php echo $current_age_data['color']; ?>">
                        فيديو مناسب لـ<?php echo $current_age_data['label']; ?>!
                    </h2>
                    <p class="text-sm text-gray-600">مخصص للأطفال من عمر <?php echo $selected_age_group; ?> سنوات</p>
                    <?php if ($age_range && strpos($age_range, $selected_age_group) !== false) : ?>
                        <span class="inline-block bg-<?php echo $current_age_data['color']; ?> text-white px-2 py-1 rounded-full text-xs font-bold mt-1">
                            ✨ مناسب تماماً لعمرك!
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Video Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Video Player and Details -->
        <div class="lg:col-span-2">
            <!-- Video Player -->
            <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-6">
                <div class="h-96 bg-dark/10 relative">
                    <?php if (!empty($video_url)) : ?>
                        <div class="video-embed-container">
                            <?php 
                            // Output the formatted video embed
                            echo $video_embed;
                            ?>
                        </div>
                    <?php else : ?>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <button class="bg-primary/90 hover:bg-primary text-white w-20 h-20 rounded-full shadow-lg flex items-center justify-center transition transform hover:scale-110">
                                <i class="fas fa-play text-3xl"></i>
                            </button>
                        </div>
                        <span class="absolute bottom-4 left-4 bg-dark/70 text-white px-2 py-1 rounded text-sm"><?php echo esc_html($duration); ?></span>
                        <?php if ($is_new) : ?>
                            <span class="absolute top-4 right-4 bg-accent text-dark px-3 py-1 rounded-full text-sm font-bold"><?php _e('جديد', 'sarah-loz'); ?></span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Video Info -->
            <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-8">
                <div class="p-6">
                    <h1 class="text-3xl font-bold text-dark mb-2">
                        <?php the_title(); ?>
                    </h1>
                    
                    <div class="flex flex-wrap items-center text-sm text-gray-500 mb-4">
                        <span class="ml-6"><i class="fas fa-eye ml-1"></i> <?php echo esc_html(number_format($view_count)); ?> <?php _e('مشاهدة', 'sarah-loz'); ?></span>
                        <span class="ml-6"><i class="far fa-calendar ml-1"></i> <?php _e('تم النشر:', 'sarah-loz'); ?> <?php echo get_the_date(); ?></span>
                        <span>
                            <i class="fas fa-tag ml-1"></i> 
                            <?php
                            $categories = get_the_terms(get_the_ID(), 'video_category');
                            if ($categories && !is_wp_error($categories)) {
                                $category_names = array();
                                foreach ($categories as $category) {
                                    $category_names[] = $category->name;
                                }
                                echo esc_html(implode(', ', $category_names));
                            }
                            ?>
                        </span>
                    </div>

                    <div class="flex items-center mb-4">
                        <div class="text-accent">
                            <?php
                            // Display star rating
                            $rating_rounded = round($rating * 2) / 2;
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= floor($rating_rounded)) {
                                    echo '<i class="fas fa-star"></i>';
                                } elseif ($i - 0.5 == $rating_rounded) {
                                    echo '<i class="fas fa-star-half-alt"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                        </div>
                        <span class="text-gray-500 mr-2"><?php echo esc_html(number_format($rating, 1)); ?> (<?php echo esc_html(number_format($rating_count)); ?> <?php _e('تقييم', 'sarah-loz'); ?>)</span>
                    </div>

                    <div class="text-gray-600 text-lg mb-6">
                        <?php the_content(); ?>
                    </div>

                    <div class="flex flex-wrap gap-4">
                        <?php 
                        // Add favorite button
                        $product_id = get_the_ID();
                        $is_favorite = function_exists('sarah_loz_is_favorite') ? sarah_loz_is_favorite($product_id) : false;
                        
                        $active_class = $is_favorite ? 'bg-primary text-white' : 'bg-primary text-white';
                        $active_text = $is_favorite ? 'إزالة من المفضلة' : 'أضف للمفضلة';
                        $active_icon = $is_favorite ? 'fas' : 'far';
                        ?>
                        <button type="button" class="sarah-loz-toggle-favorite <?php echo $active_class; ?> px-6 py-3 rounded-full text-lg shadow-lg hover:bg-opacity-90 transition" data-product-id="<?php echo esc_attr($product_id); ?>" data-nonce="<?php echo wp_create_nonce('sarah_loz_favorite_nonce'); ?>">
                            <i class="<?php echo $active_icon; ?> fa-heart ml-2"></i>
                            <?php echo $active_text; ?>
                        </button>
                        <button class="bg-secondary text-white px-6 py-3 rounded-full text-lg shadow-lg hover:bg-secondary/90 transition video-share-btn">
                            <i class="fas fa-share ml-2"></i>
                            <?php _e('مشاركة', 'sarah-loz'); ?>
                        </button>
                        <button class="bg-accent text-dark px-6 py-3 rounded-full text-lg shadow-lg hover:bg-accent/90 transition video-save-btn" data-id="<?php echo get_the_ID(); ?>">
                            <i class="fas fa-save ml-2"></i>
                            <?php _e('حفظ', 'sarah-loz'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Video Description and Learning Materials -->
            <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-8">
                <div class="border-b border-gray-200">
                    <div class="flex overflow-x-auto">
                        <button class="video-tab px-6 py-4 text-primary border-b-2 border-primary font-bold" data-tab="details"><?php _e('تفاصيل الفيديو', 'sarah-loz'); ?></button>
                        <button class="video-tab px-6 py-4 text-gray-500 hover:text-primary transition" data-tab="materials"><?php _e('مواد تعليمية', 'sarah-loz'); ?></button>
                        <button class="video-tab px-6 py-4 text-gray-500 hover:text-primary transition" data-tab="comments"><?php _e('تعليقات', 'sarah-loz'); ?></button>
                    </div>
                </div>
                
                <div class="p-6">
                    <!-- Details Tab Content -->
                    <div id="details-tab" class="video-tab-content">
                        <h2 class="text-xl font-bold text-dark mb-4"><?php _e('تفاصيل الفيديو', 'sarah-loz'); ?></h2>
                        <div class="prose max-w-none">
                            <?php the_excerpt(); ?>
                        </div>
                        
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="border border-gray-200 rounded-xl p-4">
                                <h3 class="font-bold text-dark mb-2"><?php _e('المهارات التعليمية', 'sarah-loz'); ?></h3>
                                <ul class="list-disc list-inside text-gray-600">
                                    <?php
                                    $skills = get_the_terms(get_the_ID(), 'educational_skill');
                                    if ($skills && !is_wp_error($skills)) {
                                        foreach ($skills as $skill) {
                                            echo '<li>' . esc_html($skill->name) . '</li>';
                                        }
                                    } else {
                                        echo '<li>' . __('لا توجد مهارات محددة', 'sarah-loz') . '</li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                            
                            <div class="border border-gray-200 rounded-xl p-4">
                                <h3 class="font-bold text-dark mb-2"><?php _e('الفئة العمرية', 'sarah-loz'); ?></h3>
                                <ul class="list-disc list-inside text-gray-600">
                                    <?php
                                    $age_groups = get_the_terms(get_the_ID(), 'age_group');
                                    if ($age_groups && !is_wp_error($age_groups)) {
                                        foreach ($age_groups as $age_group) {
                                            echo '<li>' . esc_html($age_group->name) . '</li>';
                                        }
                                    } else {
                                        echo '<li>' . __('لا توجد فئة عمرية محددة', 'sarah-loz') . '</li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Materials Tab Content -->
                    <div id="materials-tab" class="video-tab-content hidden">
                        <h2 class="text-xl font-bold text-dark mb-4"><?php _e('المواد التعليمية', 'sarah-loz'); ?></h2>
                        
                        <?php if (!empty($learning_materials)) : ?>
                            <div class="learning-materials-content">
                                <?php echo $learning_materials; ?>
                            </div>
                        <?php else : ?>
                            <p class="text-gray-500 text-center p-4"><?php _e('لا توجد مواد تعليمية متاحة', 'sarah-loz'); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Comments Tab Content -->
                    <div id="comments-tab" class="video-tab-content hidden">
                        <h2 class="text-xl font-bold text-dark mb-4"><?php _e('التعليقات', 'sarah-loz'); ?></h2>
                        
                        <?php 
                        // If comments are open or we have at least one comment
                        if (comments_open() || get_comments_number()) :
                            comments_template();
                        else :
                            echo '<p class="text-gray-500 text-center p-4">' . __('التعليقات مغلقة', 'sarah-loz') . '</p>';
                        endif;
                        ?>
                    </div>
                </div>
            </div>
            
            <?php 
            // Display related practices
            do_action('sarah_loz_after_video_content', get_the_ID()); 
            ?>
            
        </div>
        
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Related Videos -->
            <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-8">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-dark mb-4"><?php _e('فيديوهات ذات صلة', 'sarah-loz'); ?></h2>
                    
                    <div class="space-y-4">
                        <?php
                        // Get video categories from the current post
                        $categories = get_the_terms(get_the_ID(), 'video_category');
                        
                        // Prepare category IDs for the query
                        $category_ids = array();
                        if ($categories && !is_wp_error($categories)) {
                            foreach ($categories as $category) {
                                $category_ids[] = $category->term_id;
                            }
                        }
                        
                        // Get related videos
                        $related_query = new WP_Query(array(
                            'post_type' => 'video',
                            'posts_per_page' => 5,
                            'post__not_in' => array(get_the_ID()),
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'video_category',
                                    'field' => 'term_id',
                                    'terms' => $category_ids,
                                ),
                            ),
                        ));
                        
                        if ($related_query->have_posts()) :
                            while ($related_query->have_posts()) : $related_query->the_post();
                                $rel_duration = get_field('video_duration', get_the_ID()) ?: '00:00';
                                $rel_views = get_field('view_count', get_the_ID()) ?: 0;
                        ?>
                        <div class="flex space-x-4 space-x-reverse">
                            <div class="shrink-0 w-24 h-16 bg-primary/10 rounded-lg relative">
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>" class="block w-full h-full overflow-hidden rounded-lg">
                                        <?php the_post_thumbnail('thumbnail', array('class' => 'w-full h-full object-cover')); ?>
                                    </a>
                                <?php else : ?>
                                    <div class="flex items-center justify-center w-full h-full">
                                        <i class="fas fa-play-circle text-primary/60 text-2xl"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="absolute bottom-1 left-1 bg-dark/70 text-white px-1 text-xs rounded"><?php echo esc_html($rel_duration); ?></span>
                            </div>
                            <div>
                                <h3 class="font-bold text-dark mb-1 line-clamp-2">
                                    <a href="<?php the_permalink(); ?>" class="hover:text-primary transition">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>
                                <div class="text-xs text-gray-500">
                                    <span><i class="fas fa-eye ml-1"></i> <?php echo esc_html(number_format($rel_views)); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php
                            endwhile;
                            wp_reset_postdata();
                        else :
                            echo '<p>' . __('لا توجد فيديوهات ذات صلة', 'sarah-loz') . '</p>';
                        endif;
                        ?>
                    </div>
                </div>
            </div>
            
            <!-- Popular Videos -->
            <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-8">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-dark mb-4"><?php _e('الأكثر مشاهدة', 'sarah-loz'); ?></h2>
                    
                    <div class="space-y-4">
                        <?php
                        // Get popular videos
                        $popular_query = new WP_Query(array(
                            'post_type' => 'video',
                            'posts_per_page' => 5,
                            'post__not_in' => array(get_the_ID()),
                            'meta_key' => 'view_count',
                            'orderby' => 'meta_value_num',
                            'order' => 'DESC',
                        ));
                        
                        if ($popular_query->have_posts()) :
                            while ($popular_query->have_posts()) : $popular_query->the_post();
                                $pop_duration = get_field('video_duration', get_the_ID()) ?: '00:00';
                                $pop_views = get_field('view_count', get_the_ID()) ?: 0;
                        ?>
                        <div class="flex space-x-4 space-x-reverse">
                            <div class="shrink-0 w-24 h-16 bg-primary/10 rounded-lg relative">
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>" class="block w-full h-full overflow-hidden rounded-lg">
                                        <?php the_post_thumbnail('thumbnail', array('class' => 'w-full h-full object-cover')); ?>
                                    </a>
                                <?php else : ?>
                                    <div class="flex items-center justify-center w-full h-full">
                                        <i class="fas fa-play-circle text-primary/60 text-2xl"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="absolute bottom-1 left-1 bg-dark/70 text-white px-1 text-xs rounded"><?php echo esc_html($pop_duration); ?></span>
                            </div>
                            <div>
                                <h3 class="font-bold text-dark mb-1 line-clamp-2">
                                    <a href="<?php the_permalink(); ?>" class="hover:text-primary transition">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>
                                <div class="text-xs text-gray-500">
                                    <span><i class="fas fa-eye ml-1"></i> <?php echo esc_html(number_format($pop_views)); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php
                            endwhile;
                            wp_reset_postdata();
                        else :
                            echo '<p>' . __('لا توجد فيديوهات', 'sarah-loz') . '</p>';
                        endif;
                        ?>
                    </div>
                </div>
            </div>
            
            <!-- Video Categories -->
            <div class="bg-white rounded-3xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-dark mb-4"><?php _e('فئات الفيديو', 'sarah-loz'); ?></h2>
                    
                    <ul class="space-y-2">
                        <?php
                        $video_categories = get_terms(array(
                            'taxonomy' => 'video_category',
                            'hide_empty' => true,
                        ));
                        
                        if ($video_categories && !is_wp_error($video_categories)) :
                            foreach ($video_categories as $category) :
                                $category_link = get_term_link($category);
                        ?>
                        <li>
                            <a href="<?php echo esc_url($category_link); ?>" class="flex items-center justify-between group">
                                <span class="text-gray-700 group-hover:text-primary transition">
                                    <i class="fas fa-folder ml-2 text-accent"></i> <?php echo esc_html($category->name); ?>
                                </span>
                                <span class="bg-accent/20 text-dark px-2 py-1 rounded-full text-xs"><?php echo esc_html($category->count); ?></span>
                            </a>
                        </li>
                        <?php
                            endforeach;
                        else :
                            echo '<li>' . __('لا توجد تصنيفات', 'sarah-loz') . '</li>';
                        endif;
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Cross-Post-Type Related Content -->
    <?php
    // Get current post's categories
    $video_categories = get_the_terms(get_the_ID(), 'video_category');
    if ($video_categories && !is_wp_error($video_categories)) {
        $category_names = array();
        $category_ids = array();
        foreach ($video_categories as $cat) {
            $category_names[] = $cat->name;
            $category_ids[] = $cat->term_id;
        }
        
        // Check for activities with same category name
        $related_activities = array();
        $activity_terms = get_terms(array(
            'taxonomy' => 'activity_category',
            'hide_empty' => true,
        ));
        
        $activity_term_ids = array();
        if ($activity_terms && !is_wp_error($activity_terms)) {
            foreach ($activity_terms as $term) {
                if (in_array($term->name, $category_names)) {
                    $activity_term_ids[] = $term->term_id;
                }
            }
        }
        
        if (!empty($activity_term_ids)) {
            $activities_query = new WP_Query(array(
                'post_type' => 'activity',
                'posts_per_page' => 3,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'activity_category',
                        'field' => 'term_id',
                        'terms' => $activity_term_ids,
                    ),
                ),
            ));
            
            if ($activities_query->have_posts()) {
                while ($activities_query->have_posts()) {
                    $activities_query->the_post();
                    $related_activities[] = array(
                        'id' => get_the_ID(),
                        'title' => get_the_title(),
                        'permalink' => get_permalink(),
                        'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
                        'type' => 'activity',
                        'type_label' => __('نشاط', 'sarah-loz'),
                    );
                }
                wp_reset_postdata();
            }
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
        
        // Display related content if we have any
        $related_content = array_merge($related_activities, $related_games);
        if (!empty($related_content)) :
        ?>
        <div class="my-12">
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
                                    <?php if ($item['type'] === 'activity') : ?>
                                        <i class="fas fa-paint-brush text-6xl text-primary/40"></i>
                                    <?php elseif ($item['type'] === 'game') : ?>
                                        <i class="fas fa-gamepad text-6xl text-primary/40"></i>
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
                                    if ($item['type'] === 'activity') {
                                        _e('شاهد النشاط', 'sarah-loz');
                                    } elseif ($item['type'] === 'game') {
                                        _e('العب الآن', 'sarah-loz');
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
</main>

<script>
jQuery(document).ready(function($) {
    // Tab switching functionality
    $('.video-tab').on('click', function() {
        const tab = $(this).data('tab');
        
        // Update tab buttons
        $('.video-tab').removeClass('text-primary border-b-2 border-primary font-bold').addClass('text-gray-500 hover:text-primary');
        $(this).addClass('text-primary border-b-2 border-primary font-bold').removeClass('text-gray-500 hover:text-primary');
        
        // Show selected tab content
        $('.video-tab-content').addClass('hidden');
        $(`#${tab}-tab`).removeClass('hidden');
    });
    
    // Like button functionality
    $('.video-like-btn').on('click', function() {
        const videoId = $(this).data('id');
        // Ajax call to like the video
        // ...
        
        // For now just visual feedback
        $(this).html('<i class="fas fa-heart ml-2"></i> <?php _e('أعجبني', 'sarah-loz'); ?>');
        $(this).addClass('bg-dark').removeClass('bg-primary');
    });
    
    // Save button functionality
    $('.video-save-btn').on('click', function() {
        const videoId = $(this).data('id');
        // Ajax call to save the video
        // ...
        
        // For now just visual feedback
        $(this).html('<i class="fas fa-check ml-2"></i> <?php _e('تم الحفظ', 'sarah-loz'); ?>');
        $(this).addClass('bg-dark text-white').removeClass('bg-accent text-dark');
    });
    
    // Share button functionality
    $('.video-share-btn').on('click', function() {
        // Show sharing options (could be a modal or dropdown)
        alert('<?php _e('مشاركة الفيديو', 'sarah-loz'); ?>');
    });
});
</script>

<?php
get_footer();
?>
