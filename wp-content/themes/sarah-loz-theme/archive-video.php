<?php
/**
 * The template for displaying video archives
 *
 * @package Sarah_Loz
 */

// Check if we have an age group selected
$selected_age_group = sarah_loz_get_selected_age_group();
$current_age_data = sarah_loz_get_current_age_group_data();

get_header();

// Get the current taxonomy if we're on a term page
$current_term = get_queried_object();
$is_term_page = is_tax();
$taxonomy_name = '';
$term_name = '';

if ($is_term_page && !is_wp_error($current_term)) {
    $taxonomy_name = $current_term->taxonomy;
    $term_name = $current_term->name;
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

/* Pagination Styles */
.pagination-nav {
    display: flex;
    justify-content: center;
    align-items: center;
}

.pagination-list {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 0.5rem;
}

.pagination-list li {
    margin: 0;
}

.pagination-list a,
.pagination-list span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2.5rem;
    height: 2.5rem;
    padding: 0.5rem;
    text-decoration: none;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
    color: #374151;
    background-color: white;
}

.pagination-list a:hover {
    background-color: #f3f4f6;
    border-color: #d1d5db;
    color: #1f2937;
    transform: translateY(-1px);
}

.pagination-list .current {
    background-color: #3b82f6;
    border-color: #3b82f6;
    color: white;
    font-weight: 600;
}

.pagination-list .prev,
.pagination-list .next {
    background-color: #f3f4f6;
    border-color: #d1d5db;
    color: #6b7280;
}

.pagination-list .prev:hover,
.pagination-list .next:hover {
    background-color: #e5e7eb;
    border-color: #9ca3af;
    color: #374151;
}

.pagination-list .dots {
    border: none;
    background: transparent;
    color: #6b7280;
    cursor: default;
}

.pagination-list .dots:hover {
    background: transparent;
    transform: none;
}

/* RTL Support */
.rtl .pagination-list {
    direction: rtl;
}

/* Responsive Design */
@media (max-width: 640px) {
    .pagination-list {
        gap: 0.25rem;
    }
    
    .pagination-list a,
    .pagination-list span {
        min-width: 2rem;
        height: 2rem;
        padding: 0.25rem;
        font-size: 0.875rem;
    }
}
</style>

<!-- Videos Content -->
<main class="container mx-auto px-4 py-10">
    <!-- Page Header -->
    <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-10">
        <div class="h-64 relative bg-cover bg-center" style="background-image: url('<?php echo get_template_directory_uri() . '/assets/images/balloons.png'; ?>');">
            <div class="absolute inset-0 flex items-center justify-center">
                <img src="<?php echo get_template_directory_uri() . '/assets/images/sun.png'; ?>" alt="Sun" class="w-24 h-24">
            </div>
        </div>
        <div class="p-8">
            <?php if ($selected_age_group && $current_age_data && !$is_term_page) : ?>
                <!-- Personalized welcome message based on age group -->
                <div class="bg-<?php echo $current_age_data['color']; ?>/20 rounded-3xl p-6 mb-6 animate__animated animate__bounceIn">
                    <div class="flex items-center justify-center gap-4 mb-4">
                        <?php if (isset($current_age_data['custom_image'])) : ?>
                            <img src="<?php echo esc_url($current_age_data['custom_image']); ?>" alt="<?php echo esc_attr($current_age_data['label']); ?>" class="w-16 h-16 object-cover rounded-full animate-bounce">
                        <?php else : ?>
                            <span class="text-6xl animate-bounce"><?php echo $current_age_data['icon']; ?></span>
                        <?php endif; ?>
                        <div>
                            <h1 class="text-3xl font-bold text-<?php echo $current_age_data['color']; ?>">
                                فيديوهات خاصة بـ<?php echo $current_age_data['label']; ?>!
                            </h1>
                            <p class="text-lg text-gray-600"><?php echo $selected_age_group; ?> سنوات</p>
                        </div>
                    </div>
                    <p class="text-lg text-gray-700"><?php echo $current_age_data['description']; ?></p>
                </div>
            <?php elseif ($is_term_page) : ?>
                <h1 class="text-3xl md:text-4xl font-bold text-dark mb-4">
                    <?php 
                    if ($taxonomy_name === 'video_category') {
                        echo __('فيديوهات في تصنيف: ', 'sarah-loz') . esc_html($term_name);
                    } elseif ($taxonomy_name === 'age_group') {
                        echo __('فيديوهات للفئة العمرية: ', 'sarah-loz') . esc_html($term_name);
                    } elseif ($taxonomy_name === 'educational_skill') {
                        echo __('فيديوهات لتنمية مهارة: ', 'sarah-loz') . esc_html($term_name);
                    } else {
                        echo esc_html($term_name);
                    }
                    ?>
                </h1>
                <p class="text-gray-600 text-lg mb-6">
                    <?php echo term_description(); ?>
                </p>
            <?php else : ?>
                <h1 class="text-3xl md:text-4xl font-bold text-dark mb-4">
                    <?php _e('فيديوهات سارة ولوز', 'sarah-loz'); ?>
                </h1>
                <p class="text-gray-600 text-lg mb-6">
                    <?php _e('مجموعة متنوعة من الفيديوهات التعليمية والترفيهية للأطفال. قصص مصورة، أنشطة، تعليم الحروف والأرقام وغيرها الكثير.', 'sarah-loz'); ?>
                </p>
            <?php endif; ?>

            <!-- AJAX Filter Interface -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="flex flex-col md:flex-row gap-4 flex-1">
                        <!-- Category Filter -->
                        <div class="flex-1">
                            <label for="video-category-filter" class="block text-sm font-medium text-gray-700 mb-2">
                                <?php _e('تصنيف الفيديو', 'sarah-loz'); ?>
                            </label>
                            <select id="video-category-filter" class="video-category-filter w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value=""><?php _e('جميع التصنيفات', 'sarah-loz'); ?></option>
                                <?php
                                $categories = get_terms(array(
                                    'taxonomy' => 'video_category',
                                    'hide_empty' => true,
                                ));
                                
                                if ($categories && !is_wp_error($categories)) :
                                    foreach ($categories as $category) :
                                        // Get age groups for this category
                                        $category_videos = get_posts(array(
                                            'post_type' => 'video',
                                            'posts_per_page' => -1,
                                            'tax_query' => array(
                                                array(
                                                    'taxonomy' => 'video_category',
                                                    'field' => 'term_id',
                                                    'terms' => $category->term_id
                                                )
                                            )
                                        ));
                                        
                                        $age_groups_in_category = array();
                                        foreach ($category_videos as $video) {
                                            $video_age_groups = get_the_terms($video->ID, 'age_group');
                                            if ($video_age_groups && !is_wp_error($video_age_groups)) {
                                                foreach ($video_age_groups as $age_group) {
                                                    if (!in_array($age_group->name, $age_groups_in_category)) {
                                                        $age_groups_in_category[] = $age_group->name;
                                                    }
                                                }
                                            }
                                        }
                                        
                                        $age_info = !empty($age_groups_in_category) ? ' (' . implode(', ', $age_groups_in_category) . ')' : '';
                                ?>
                                    <option value="<?php echo esc_attr($category->slug); ?>">
                                        <?php echo esc_html($category->name); ?><?php echo $age_info; ?> (<?php echo $category->count; ?>)
                                    </option>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- Clear Filters Button -->
                    <div class="md:ml-4">
                        <button type="button" class="clear-filters-btn bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                            <i class="fas fa-times ml-2"></i>
                            <?php _e('مسح الفلاتر', 'sarah-loz'); ?>
                        </button>
                    </div>
                </div>

                <!-- Results Count -->
                <div class="mt-4 text-sm text-gray-600">
                    <span class="video-results-count">
                        <?php 
                        if ($selected_age_group) {
                            // Count videos for the selected age group
                            $age_videos_query = new WP_Query(array(
                                'post_type' => 'video',
                                'posts_per_page' => -1,
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'age_group',
                                        'field' => 'slug',
                                        'terms' => $selected_age_group
                                    )
                                )
                            ));
                            $total_videos = $age_videos_query->found_posts;
                        } else {
                            $total_videos = wp_count_posts('video')->publish;
                        }
                        echo sprintf(__('%d فيديو', 'sarah-loz'), $total_videos);
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </div>



    <!-- All Videos or Category Videos -->
    <section class="mb-16">
        <h2 class="text-2xl md:text-3xl font-bold text-dark mb-8 inline-flex items-center">
            <img src="<?php echo get_template_directory_uri() . '/assets/images/sun.png'; ?>" alt="Sun" class="w-8 h-8 mr-2">
            <span class="border-b-4 border-accent pb-2">
                <?php 
                if ($is_term_page) {
                    echo __('فيديوهات ', 'sarah-loz') . esc_html($term_name);
                } elseif ($selected_age_group && $current_age_data) {
                    echo 'فيديوهات للأطفال من عمر ' . $selected_age_group . ' سنوات';
                } else {
                    _e('جميع الفيديوهات', 'sarah-loz');
                }
                ?>
            </span>
        </h2>
        
        <!-- Video Grid -->
        <div class="video-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php 
            // Debug information
            if (current_user_can('administrator') && isset($_GET['debug_videos'])) {
                echo '<div class="col-span-full bg-yellow-100 p-4 rounded-lg mb-4">';
                echo '<h3 class="font-bold">Debug Information:</h3>';
                echo '<p><strong>Selected Age Group:</strong> ' . ($selected_age_group ? $selected_age_group : 'None') . '</p>';
                echo '<p><strong>Is Term Page:</strong> ' . ($is_term_page ? 'Yes' : 'No') . '</p>';
                
                // Check what age group terms exist
                $age_terms = get_terms(array(
                    'taxonomy' => 'age_group',
                    'hide_empty' => false,
                ));
                echo '<p><strong>Available Age Group Terms:</strong> ';
                if ($age_terms && !is_wp_error($age_terms)) {
                    foreach ($age_terms as $term) {
                        echo $term->slug . ' (' . $term->name . '), ';
                    }
                } else {
                    echo 'None found';
                }
                echo '</p>';
                
                // Check if the selected age group term exists
                if ($selected_age_group) {
                    $term_exists = term_exists($selected_age_group, 'age_group');
                    echo '<p><strong>Selected Age Group Term Exists:</strong> ' . ($term_exists ? 'Yes' : 'No') . '</p>';
                }
                
                // Count total videos
                $total_videos = wp_count_posts('video')->publish;
                echo '<p><strong>Total Videos:</strong> ' . $total_videos . '</p>';
                
                // Count videos for selected age group
                if ($selected_age_group) {
                    $age_videos_query = new WP_Query(array(
                        'post_type' => 'video',
                        'posts_per_page' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'age_group',
                                'field' => 'slug',
                                'terms' => $selected_age_group
                            )
                        )
                    ));
                    echo '<p><strong>Videos for Age Group ' . $selected_age_group . ':</strong> ' . $age_videos_query->found_posts . '</p>';
                }
                echo '</div>';
            }
            
            // Modify the main query to include age filtering if on main archive page
            if (!$is_term_page && $selected_age_group) {
                // Create a custom query for age-filtered videos
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                $videos_args = array(
                    'post_type' => 'video',
                    'posts_per_page' => 9,
                    'paged' => $paged,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'age_group',
                            'field' => 'slug',
                            'terms' => $selected_age_group
                        )
                    )
                );
                $videos_query = new WP_Query($videos_args);
                
                if ($videos_query->have_posts()) :
                    while ($videos_query->have_posts()) : $videos_query->the_post();
                        $duration = get_field('video_duration') ?: '00:00';
                        $view_count = get_field('view_count') ?: 0;
                        $is_new = get_field('is_new');
            ?>
            <!-- Video Item -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition transform hover:-translate-y-2 hover:shadow-xl">
                <div class="relative">
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>" class="block w-full aspect-video overflow-hidden">
                            <?php the_post_thumbnail('large', array('class' => 'w-full h-full object-cover transition-transform duration-300 hover:scale-105')); ?>
                            <div class="absolute inset-0 bg-primary/20 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity duration-300">
                                <i class="fas fa-play-circle text-6xl text-primary/80"></i>
                            </div>
                        </a>
                    <?php else : ?>
                        <div class="w-full aspect-video bg-primary/10 flex items-center justify-center">
                            <i class="fas fa-play-circle text-6xl text-primary/60"></i>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($is_new) : ?>
                        <span class="absolute top-4 right-4 bg-accent text-dark px-3 py-1 rounded-full text-sm font-bold"><?php _e('جديد', 'sarah-loz'); ?></span>
                    <?php endif; ?>
                    
                    <?php if ($current_age_data) : ?>
                        <div class="absolute top-2 left-4 bg-<?php echo $current_age_data['color']; ?> text-white px-2 py-1 rounded-full text-xs font-bold">
                            مناسب لعمرك!
                        </div>
                    <?php endif; ?>
                    
                    <span class="absolute bottom-4 left-4 bg-dark/70 text-white px-2 py-1 rounded text-sm"><?php echo esc_html($duration); ?></span>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-xl font-bold text-dark">
                            <a href="<?php the_permalink(); ?>" class="hover:text-primary transition">
                                <?php the_title(); ?>
                            </a>
                        </h3>
                    </div>
                    <p class="text-gray-600 mb-4">
                        <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                    </p>
                    <div class="flex items-center text-sm text-gray-500 justify-between">
                        <span><i class="fas fa-eye ml-1"></i> <?php echo esc_html(number_format($view_count)); ?> <?php _e('مشاهدة', 'sarah-loz'); ?></span>
                        <span><i class="far fa-calendar ml-1"></i> <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?></span>
                    </div>
                </div>
            </div>
            <?php 
                    endwhile;
                    wp_reset_postdata();
                    
                    // Add pagination for age-filtered videos
                    if ($videos_query->max_num_pages > 1) :
                        $big = 999999999;
                        $age_pagination_args = array(
                            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                            'format' => '?paged=%#%',
                            'current' => max(1, $paged),
                            'total' => $videos_query->max_num_pages,
                            'mid_size' => 2,
                            'prev_text' => '<i class="fas fa-arrow-right ml-2"></i>' . __('السابق', 'sarah-loz'),
                            'next_text' => __('التالي', 'sarah-loz') . '<i class="fas fa-arrow-left mr-2"></i>',
                            'screen_reader_text' => __('تنقل بين الصفحات', 'sarah-loz'),
                            'type' => 'array',
                            'class' => 'pagination-list'
                        );
                        
                        $age_pagination = paginate_links($age_pagination_args);
                        
                        if ($age_pagination) :
                            echo '<div class="col-span-full mt-8">';
                            echo '<nav class="pagination-nav" aria-label="' . __('تنقل بين الصفحات', 'sarah-loz') . '">';
                            echo '<ul class="pagination-list flex items-center space-x-2 rtl:space-x-reverse">';
                            foreach ($age_pagination as $link) :
                                echo '<li>' . $link . '</li>';
                            endforeach;
                            echo '</ul>';
                            echo '</nav>';
                            echo '</div>';
                        endif;
                    endif;
                endif;
                
                // Fallback: show all videos if no age-specific videos found
                if (!$is_term_page && $selected_age_group && (!isset($videos_query) || !$videos_query->have_posts())) {
                    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                    $fallback_query = new WP_Query(array(
                        'post_type' => 'video',
                        'posts_per_page' => 9,
                        'paged' => $paged
                    ));
                    
                    if ($fallback_query->have_posts()) :
                        echo '<div class="col-span-full text-center p-8 bg-white rounded-2xl shadow-lg mb-8">';
                        echo '<div class="text-4xl mb-4">⚠️</div>';
                        echo '<h3 class="text-xl font-bold text-orange-600 mb-2">ملاحظة</h3>';
                        echo '<p class="text-gray-600 mb-4">لا توجد فيديوهات محددة لعمر ' . $selected_age_group . ' سنوات. نعرض لك جميع الفيديوهات المتاحة:</p>';
                        echo '</div>';
                        
                        while ($fallback_query->have_posts()) : $fallback_query->the_post();
                            $duration = get_field('video_duration') ?: '00:00';
                            $view_count = get_field('view_count') ?: 0;
                            $is_new = get_field('is_new');
            ?>
            <!-- Video Item -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition transform hover:-translate-y-2 hover:shadow-xl">
                <div class="relative">
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>" class="block w-full aspect-video overflow-hidden">
                            <?php the_post_thumbnail('large', array('class' => 'w-full h-full object-cover transition-transform duration-300 hover:scale-105')); ?>
                            <div class="absolute inset-0 bg-primary/20 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity duration-300">
                                <i class="fas fa-play-circle text-6xl text-primary/80"></i>
                            </div>
                        </a>
                    <?php else : ?>
                        <div class="w-full aspect-video bg-primary/10 flex items-center justify-center">
                            <i class="fas fa-play-circle text-6xl text-primary/60"></i>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($is_new) : ?>
                        <span class="absolute top-4 right-4 bg-accent text-dark px-3 py-1 rounded-full text-sm font-bold"><?php _e('جديد', 'sarah-loz'); ?></span>
                    <?php endif; ?>
                    
                    <span class="absolute bottom-4 left-4 bg-dark/70 text-white px-2 py-1 rounded text-sm"><?php echo esc_html($duration); ?></span>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-xl font-bold text-dark">
                            <a href="<?php the_permalink(); ?>" class="hover:text-primary transition">
                                <?php the_title(); ?>
                            </a>
                        </h3>
                    </div>
                    <p class="text-gray-600 mb-4">
                        <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                    </p>
                    <div class="flex items-center text-sm text-gray-500 justify-between">
                        <span><i class="fas fa-eye ml-1"></i> <?php echo esc_html(number_format($view_count)); ?> <?php _e('مشاهدة', 'sarah-loz'); ?></span>
                        <span><i class="far fa-calendar ml-1"></i> <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?></span>
                    </div>
                </div>
            </div>
            <?php 
                        endwhile;
                        wp_reset_postdata();
                        
                        // Add fallback pagination
                        if ($fallback_query->max_num_pages > 1) :
                            $big = 999999999;
                            $fallback_pagination_args = array(
                                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                                'format' => '?paged=%#%',
                                'current' => max(1, $paged),
                                'total' => $fallback_query->max_num_pages,
                                'mid_size' => 2,
                                'prev_text' => '<i class="fas fa-arrow-right ml-2"></i>' . __('السابق', 'sarah-loz'),
                                'next_text' => __('التالي', 'sarah-loz') . '<i class="fas fa-arrow-left mr-2"></i>',
                                'screen_reader_text' => __('تنقل بين الصفحات', 'sarah-loz'),
                                'type' => 'array',
                                'class' => 'pagination-list'
                            );
                            
                            $fallback_pagination = paginate_links($fallback_pagination_args);
                            
                            if ($fallback_pagination) :
                                echo '<div class="col-span-full mt-8">';
                                echo '<nav class="pagination-nav" aria-label="' . __('تنقل بين الصفحات', 'sarah-loz') . '">';
                                echo '<ul class="pagination-list flex items-center space-x-2 rtl:space-x-reverse">';
                                foreach ($fallback_pagination as $link) :
                                    echo '<li>' . $link . '</li>';
                                endforeach;
                                echo '</ul>';
                                echo '</nav>';
                                echo '</div>';
                            endif;
                        endif;
                    else :
                        echo '<div class="col-span-full text-center p-8 bg-white rounded-2xl shadow-lg">';
                        echo '<div class="text-6xl mb-4 animate-bounce-slow">📹</div>';
                        echo '<h3 class="text-2xl font-bold text-primary mb-4">لا توجد فيديوهات</h3>';
                        echo '<p class="text-gray-600 mb-6">لا توجد فيديوهات مناسبة لعمر ' . $selected_age_group . ' سنوات حالياً. تحقق لاحقاً!</p>';
                        echo '</div>';
                    endif;
                }
            } else {
                // Use the default query for taxonomy pages or when no age group selected
                if (have_posts()) :
                    while (have_posts()) : the_post();
                        $duration = get_field('video_duration') ?: '00:00';
                        $view_count = get_field('view_count') ?: 0;
                        $is_new = get_field('is_new');
            ?>
            <!-- Video Item -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition transform hover:-translate-y-2 hover:shadow-xl">
                <div class="relative">
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>" class="block w-full aspect-video overflow-hidden">
                            <?php the_post_thumbnail('large', array('class' => 'w-full h-full object-cover transition-transform duration-300 hover:scale-105')); ?>
                            <div class="absolute inset-0 bg-primary/20 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity duration-300">
                                <i class="fas fa-play-circle text-6xl text-primary/80"></i>
                            </div>
                        </a>
                    <?php else : ?>
                        <div class="w-full aspect-video bg-primary/10 flex items-center justify-center">
                            <i class="fas fa-play-circle text-6xl text-primary/60"></i>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($is_new) : ?>
                        <span class="absolute top-4 right-4 bg-accent text-dark px-3 py-1 rounded-full text-sm font-bold"><?php _e('جديد', 'sarah-loz'); ?></span>
                    <?php endif; ?>
                    
                    <span class="absolute bottom-4 left-4 bg-dark/70 text-white px-2 py-1 rounded text-sm"><?php echo esc_html($duration); ?></span>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-xl font-bold text-dark">
                            <a href="<?php the_permalink(); ?>" class="hover:text-primary transition">
                                <?php the_title(); ?>
                            </a>
                        </h3>
                    </div>
                    <p class="text-gray-600 mb-4">
                        <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                    </p>
                    <div class="flex items-center text-sm text-gray-500 justify-between">
                        <span><i class="fas fa-eye ml-1"></i> <?php echo esc_html(number_format($view_count)); ?> <?php _e('مشاهدة', 'sarah-loz'); ?></span>
                        <span><i class="far fa-calendar ml-1"></i> <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?></span>
                    </div>
                </div>
            </div>
            <?php 
                    endwhile;
                    
                    // Add pagination for default query
                    if (get_query_var('paged') > 1 || get_query_var('page') > 1) :
                        $paged = max(get_query_var('paged'), get_query_var('page'));
                        $total_pages = $wp_query->max_num_pages;
                        
                        if ($total_pages > 1) :
                            $big = 999999999;
                            $default_pagination_args = array(
                                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                                'format' => '?paged=%#%',
                                'current' => $paged,
                                'total' => $total_pages,
                                'mid_size' => 2,
                                'prev_text' => '<i class="fas fa-arrow-right ml-2"></i>' . __('السابق', 'sarah-loz'),
                                'next_text' => __('التالي', 'sarah-loz') . '<i class="fas fa-arrow-left mr-2"></i>',
                                'screen_reader_text' => __('تنقل بين الصفحات', 'sarah-loz'),
                                'type' => 'array',
                                'class' => 'pagination-list'
                            );
                            
                            $default_pagination = paginate_links($default_pagination_args);
                            
                            if ($default_pagination) :
                                echo '<div class="col-span-full mt-8">';
                                echo '<nav class="pagination-nav" aria-label="' . __('تنقل بين الصفحات', 'sarah-loz') . '">';
                                echo '<ul class="pagination-list flex items-center space-x-2 rtl:space-x-reverse">';
                                foreach ($default_pagination as $link) :
                                    echo '<li>' . $link . '</li>';
                                endforeach;
                                echo '</ul>';
                                echo '</nav>';
                                echo '</div>';
                            endif;
                        endif;
                    endif;
                else :
                    echo '<div class="col-span-full text-center p-8 bg-white rounded-2xl shadow-lg">';
                    echo '<p class="text-xl">' . __('لا توجد فيديوهات في هذا التصنيف حالياً', 'sarah-loz') . '</p>';
                    echo '</div>';
                endif;
            }
            ?>
        </div>
        
        <!-- Pagination -->
        <div class="video-pagination mt-12 flex justify-center">
            <?php 
            // Only show pagination for taxonomy pages and default archive (not for custom queries as they have inline pagination)
            if ($is_term_page) {
                // Pagination for taxonomy pages
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => '<i class="fas fa-arrow-right ml-2"></i>' . __('السابق', 'sarah-loz'),
                    'next_text' => __('التالي', 'sarah-loz') . '<i class="fas fa-arrow-left mr-2"></i>',
                    'screen_reader_text' => __('تنقل بين الصفحات', 'sarah-loz'),
                    'class' => 'pagination-list'
                ));
            } elseif (!$selected_age_group) {
                // Default pagination for main archive when no age group selected
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => '<i class="fas fa-arrow-right ml-2"></i>' . __('السابق', 'sarah-loz'),
                    'next_text' => __('التالي', 'sarah-loz') . '<i class="fas fa-arrow-left mr-2"></i>',
                    'screen_reader_text' => __('تنقل بين الصفحات', 'sarah-loz'),
                    'class' => 'pagination-list'
                ));
            }
            ?>
        </div>
    </section>
</main>

<script>
// Enhanced pagination functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scrolling to pagination links
    const paginationLinks = document.querySelectorAll('.pagination-list a');
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Smooth scroll to top of videos section
            const videosSection = document.querySelector('.video-grid');
            if (videosSection) {
                e.preventDefault();
                videosSection.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Add a small delay before navigation to allow smooth scroll
                setTimeout(() => {
                    window.location.href = this.href;
                }, 500);
            }
        });
    });
    
    // Add loading state to pagination
    const paginationNav = document.querySelector('.pagination-nav');
    if (paginationNav) {
        paginationNav.addEventListener('click', function(e) {
            if (e.target.tagName === 'A') {
                // Add loading indicator
                const loadingDiv = document.createElement('div');
                loadingDiv.className = 'col-span-full text-center p-8';
                loadingDiv.innerHTML = '<div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto"></div><p class="mt-4 text-gray-600">جاري التحميل...</p>';
                
                const videoGrid = document.querySelector('.video-grid');
                if (videoGrid) {
                    videoGrid.innerHTML = '';
                    videoGrid.appendChild(loadingDiv);
                }
            }
        });
    }
});
</script>

<?php
get_footer();
?>
