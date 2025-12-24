<?php
/**
 * The template for displaying activity archives
 *
 * @package Sarah_Loz
 */

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

// Check if we have an age group selected
$selected_age_group = sarah_loz_get_selected_age_group();
$current_age_data = sarah_loz_get_current_age_group_data();
?>

<!-- Main Content -->
<main class="container mx-auto px-4 py-10">
    <!-- Hero Section -->
    <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
            <div class="p-8 md:p-12 flex flex-col justify-center">
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
                                    أنشطة خاصة بـ<?php echo $current_age_data['label']; ?>!
                                </h1>
                                <p class="text-lg text-gray-600"><?php echo $selected_age_group; ?> سنوات</p>
                            </div>
                        </div>
                        <p class="text-lg text-gray-700"><?php echo $current_age_data['description']; ?></p>
                    </div>
                <?php endif; ?>

                <h1 class="text-4xl md:text-5xl font-bold text-dark mb-4">
                    <?php if ($is_term_page) : ?>
                        <?php 
                        if ($taxonomy_name === 'activity_category') {
                            echo __('أنشطة في تصنيف: ', 'sarah-loz') . esc_html($term_name);
                        } elseif ($taxonomy_name === 'age_group') {
                            echo __('أنشطة للفئة العمرية: ', 'sarah-loz') . esc_html($term_name);
                        } elseif ($taxonomy_name === 'educational_skill') {
                            echo __('أنشطة لتنمية مهارة: ', 'sarah-loz') . esc_html($term_name);
                        } else {
                            echo esc_html($term_name);
                        }
                        ?>
                    <?php elseif ($selected_age_group && $current_age_data) : ?>
                        <?php echo 'أنشطة للأطفال من عمر ' . $selected_age_group . ' سنوات'; ?>
                    <?php else : ?>
                        <?php _e('أنشطة تعليمية ممتعة', 'sarah-loz'); ?>
                    <?php endif; ?>
                </h1>
                <p class="text-xl text-gray-600 mb-6">
                    <?php if ($is_term_page) : ?>
                        <?php echo term_description(); ?>
                    <?php elseif ($selected_age_group && $current_age_data) : ?>
                        <?php echo 'أنشطة مصممة خصيصاً للأطفال من عمر ' . $selected_age_group . ' سنوات لتنمية مهاراتهم وتشجيع الإبداع والتعلم النشط'; ?>
                    <?php else : ?>
                        <?php _e('مجموعة متنوعة من الأنشطة التعليمية والترفيهية لتنمية مهارات الأطفال وتشجيع الإبداع والتعلم النشط', 'sarah-loz'); ?>
                    <?php endif; ?>
                </p>
                <div class="flex flex-wrap gap-4">
                    <a 
                        href="#latest" 
                        class="bg-primary text-white px-6 py-3 rounded-full text-lg shadow-lg hover:bg-primary/90 transition inline-block"
                    >
                        <i class="fas fa-clock ml-2"></i>
                        <?php _e('أحدث الأنشطة', 'sarah-loz'); ?>
                    </a>
                    <a 
                        href="#categories"
                        class="bg-secondary text-white px-6 py-3 rounded-full text-lg shadow-lg hover:bg-secondary/90 transition inline-block"
                    >
                        <i class="fas fa-th-large ml-2"></i>
                        <?php _e('تصفح الفئات', 'sarah-loz'); ?>
                    </a>
                </div>
            </div>
            <div class="h-64 md:h-auto bg-accent/20 relative overflow-hidden">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <div class="flex justify-center">
                            <i class="fas fa-paint-brush text-5xl text-accent mb-4 floating"></i>
                            <i class="fas fa-puzzle-piece text-5xl text-primary mb-4 floating" style="animation-delay: 0.5s"></i>
                            <i class="fas fa-music text-5xl text-secondary mb-4 floating" style="animation-delay: 1s"></i>
                        </div>
                        <p class="text-lg font-bold text-dark">
                            <?php _e('طور مهارات طفلك مع أكثر من ٥٠ نشاط تعليمي', 'sarah-loz'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <section id="categories" class="mb-12">
        <h2 class="text-2xl md:text-3xl font-bold text-dark mb-8">
            <span class="border-b-4 border-primary pb-2"><?php _e('فئات الأنشطة', 'sarah-loz'); ?></span>
        </h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $categories = get_terms(array(
                'taxonomy' => 'activity_category',
                'hide_empty' => true,
            ));
            
            if ($categories && !is_wp_error($categories)) :
                foreach ($categories as $index => $category) :
                    $term_link = get_term_link($category);
                    $icons = array(
                        0 => '<i class="fas fa-pencil-alt text-6xl text-primary/50"></i>',
                        1 => '<i class="fas fa-puzzle-piece text-6xl text-secondary/50"></i>',
                        2 => '<i class="fas fa-music text-6xl text-accent/50"></i>',
                        3 => '<i class="fas fa-flask text-6xl text-dark/50"></i>'
                    );
                    $backgrounds = array(
                        0 => 'bg-primary/20',
                        1 => 'bg-secondary/20',
                        2 => 'bg-accent/20',
                        3 => 'bg-dark/10'
                    );
                    $idx = $index % 4;
            ?>
            <a href="<?php echo esc_url($term_link); ?>" class="bg-white rounded-3xl shadow-lg overflow-hidden transition transform hover:-translate-y-2 hover:shadow-xl">
                <div class="h-40 <?php echo $backgrounds[$idx]; ?> relative">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <?php echo $icons[$idx]; ?>
                    </div>
                </div>
                <div class="p-6 text-center">
                    <h3 class="text-xl font-bold text-dark mb-2"><?php echo esc_html($category->name); ?></h3>
                    <p class="text-gray-600"><?php echo esc_html($category->count); ?> <?php _e('نشاط', 'sarah-loz'); ?></p>
                </div>
            </a>
            <?php
                endforeach;
            endif;
            ?>
        </div>
    </section>

    <!-- Latest Activities Section -->
    <section id="latest" class="mb-12">
        <h2 class="text-2xl md:text-3xl font-bold text-dark mb-8">
            <span class="border-b-4 border-primary pb-2"><?php _e('جميع الأنشطة', 'sarah-loz'); ?></span>
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php 
            // If we're on the main archive page, use the main query with age filtering
            if (is_post_type_archive('activity') && !$is_term_page) {
                // Modify the main query to include age filtering if age group selected
                if ($selected_age_group) {
                    $activities_args = array(
                        'post_type' => 'activity',
                        'paged' => get_query_var('paged') ?: 1,
                        'meta_query' => sarah_loz_get_age_filter_meta_query($selected_age_group)
                    );
                    $activities_query = new WP_Query($activities_args);
                    $posts_to_display = $activities_query;
                } else {
                    // Use the original query when no age filtering
                    global $wp_query;
                    $posts_to_display = $wp_query;
                }
                
                if ($posts_to_display->have_posts()) {
                    while ($posts_to_display->have_posts()) {
                        $posts_to_display->the_post();
                        $age_range = get_field('age_range');
                        $categories = get_the_terms(get_the_ID(), 'activity_category');
                        $category_name = '';
                        $category_class = 'bg-primary/10 text-primary';
                        $icon_class = 'fas fa-paint-brush text-6xl text-primary/40';
                        
                        if ($categories && !is_wp_error($categories)) {
                            $category = reset($categories);
                            $category_name = $category->name;
                            
                            // Rotate category styles
                            $cat_id = $category->term_id % 3;
                            switch ($cat_id) {
                                case 0:
                                    $category_class = 'bg-primary/10 text-primary';
                                    $icon_class = 'fas fa-paint-brush text-6xl text-primary/40';
                                    break;
                                case 1:
                                    $category_class = 'bg-secondary/10 text-secondary';
                                    $icon_class = 'fas fa-puzzle-piece text-6xl text-secondary/40';
                                    break;
                                case 2:
                                    $category_class = 'bg-accent/10 text-accent';
                                    $icon_class = 'fas fa-flask text-6xl text-accent/40';
                                    break;
                            }
                        }
            ?>
            <div class="bg-white rounded-3xl shadow-lg overflow-hidden transition transform hover:-translate-y-2 hover:shadow-xl">
                <div class="relative">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="h-48 overflow-hidden">
                            <?php the_post_thumbnail('medium_large', array('class' => 'w-full h-full object-cover')); ?>
                        </div>
                    <?php else : ?>
                        <div class="h-48 bg-primary/10 flex items-center justify-center">
                            <i class="<?php echo $icon_class; ?>"></i>
                        </div>
                    <?php endif; ?>
                    <?php if (get_post_time() > strtotime('-7 days')) : ?>
                        <span class="absolute top-4 right-4 bg-accent text-dark px-3 py-1 rounded-full text-sm font-bold"><?php _e('جديد', 'sarah-loz'); ?></span>
                    <?php endif; ?>
                    <?php if ($current_age_data && $age_range && strpos($age_range, $selected_age_group) !== false) : ?>
                        <div class="absolute top-4 left-4 bg-<?php echo $current_age_data['color']; ?> text-white px-2 py-1 rounded-full text-xs font-bold">
                            مناسب لعمرك!
                        </div>
                    <?php endif; ?>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <span class="<?php echo $category_class; ?> px-3 py-1 rounded-full text-sm"><?php echo esc_html($category_name); ?></span>
                        <div class="text-accent text-sm">
                            <?php 
                            // Random rating for demo
                            $rating = mt_rand(35, 50) / 10;
                            $stars = floor($rating);
                            $half = ($rating - $stars) >= 0.5;
                            
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $stars) {
                                    echo '<i class="fas fa-star"></i>';
                                } elseif ($i == $stars + 1 && $half) {
                                    echo '<i class="fas fa-star-half-alt"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                    
                    <h3 class="text-xl font-bold text-dark mb-2">
                        <a href="<?php the_permalink(); ?>" class="hover:text-primary">
                            <?php the_title(); ?>
                        </a>
                    </h3>
                    <p class="text-gray-600 mb-4">
                        <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                    </p>
                    
                    <div class="flex justify-between items-center mt-4">
                        <div>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-child ml-1"></i> <?php echo esc_html($age_range); ?> <?php _e('سنوات', 'sarah-loz'); ?>
                                <?php if ($current_age_data && strpos($age_range, $selected_age_group) !== false) : ?>
                                    <span class="mr-1">✨</span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <a 
                            href="<?php the_permalink(); ?>" 
                            class="bg-primary text-white px-4 py-2 rounded-full hover:bg-primary/90 transition"
                        >
                            <?php _e('عرض النشاط', 'sarah-loz'); ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php 
                    }
                    
                    // Reset post data if using custom query
                    if ($selected_age_group) {
                        wp_reset_postdata();
                    }
                } else {
                    echo '<div class="col-span-3 text-center py-10 bg-light rounded-lg">';
                    echo '<i class="fas fa-palette text-6xl text-secondary/30 mb-4"></i>';
                    echo '<h3 class="text-2xl font-bold text-gray-600 mb-2">لا توجد أنشطة متاحة</h3>';
                    echo '<p class="text-gray-500">';
                    if ($selected_age_group) {
                        echo 'لا توجد أنشطة مناسبة لعمر ' . $selected_age_group . ' سنوات حالياً. تحقق لاحقاً!';
                    } else {
                        echo 'لم يتم العثور على أنشطة. يرجى المحاولة لاحقاً.';
                    }
                    echo '</p>';
                    echo '</div>';
                }
            } else {
                // For taxonomy pages or when we want to just show latest on the homepage
                $latest_query = new WP_Query(array(
                    'post_type' => 'activity',
                    'posts_per_page' => 9,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
                
                if ($latest_query->have_posts()) {
                    while ($latest_query->have_posts()) {
                        $latest_query->the_post();
                        $age_range = get_field('age_range');
                        $categories = get_the_terms(get_the_ID(), 'activity_category');
                        $category_name = '';
                        $category_class = 'bg-primary/10 text-primary';
                        $icon_class = 'fas fa-paint-brush text-6xl text-primary/40';
                        
                        if ($categories && !is_wp_error($categories)) {
                            $category = reset($categories);
                            $category_name = $category->name;
                            
                            // Rotate category styles
                            $cat_id = $category->term_id % 3;
                            switch ($cat_id) {
                                case 0:
                                    $category_class = 'bg-primary/10 text-primary';
                                    $icon_class = 'fas fa-paint-brush text-6xl text-primary/40';
                                    break;
                                case 1:
                                    $category_class = 'bg-secondary/10 text-secondary';
                                    $icon_class = 'fas fa-puzzle-piece text-6xl text-secondary/40';
                                    break;
                                case 2:
                                    $category_class = 'bg-accent/10 text-accent';
                                    $icon_class = 'fas fa-flask text-6xl text-accent/40';
                                    break;
                            }
                        }
            ?>
            <div class="bg-white rounded-3xl shadow-lg overflow-hidden transition transform hover:-translate-y-2 hover:shadow-xl">
                <div class="relative">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="h-48 overflow-hidden">
                            <?php the_post_thumbnail('medium_large', array('class' => 'w-full h-full object-cover')); ?>
                        </div>
                    <?php else : ?>
                        <div class="h-48 bg-primary/10 flex items-center justify-center">
                            <i class="<?php echo $icon_class; ?>"></i>
                        </div>
                    <?php endif; ?>
                    <?php if (get_post_time() > strtotime('-7 days')) : ?>
                        <span class="absolute top-4 right-4 bg-accent text-dark px-3 py-1 rounded-full text-sm font-bold"><?php _e('جديد', 'sarah-loz'); ?></span>
                    <?php endif; ?>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <span class="<?php echo $category_class; ?> px-3 py-1 rounded-full text-sm"><?php echo esc_html($category_name); ?></span>
                        <div class="text-accent text-sm">
                            <?php 
                            // Random rating for demo
                            $rating = mt_rand(35, 50) / 10;
                            $stars = floor($rating);
                            $half = ($rating - $stars) >= 0.5;
                            
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $stars) {
                                    echo '<i class="fas fa-star"></i>';
                                } elseif ($i == $stars + 1 && $half) {
                                    echo '<i class="fas fa-star-half-alt"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                    
                    <h3 class="text-xl font-bold text-dark mb-2">
                        <a href="<?php the_permalink(); ?>" class="hover:text-primary">
                            <?php the_title(); ?>
                        </a>
                    </h3>
                    <p class="text-gray-600 mb-4">
                        <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                    </p>
                    
                    <div class="flex justify-between items-center mt-4">
                        <div>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-child ml-1"></i> <?php echo esc_html($age_range); ?> <?php _e('سنوات', 'sarah-loz'); ?>
                            </span>
                        </div>
                        <a 
                            href="<?php the_permalink(); ?>" 
                            class="bg-primary text-white px-4 py-2 rounded-full hover:bg-primary/90 transition"
                        >
                            <?php _e('عرض النشاط', 'sarah-loz'); ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php 
                    }
                    wp_reset_postdata();
                } else {
                    echo '<div class="col-span-3 text-center py-10 bg-light rounded-lg">';
                    echo '<i class="fas fa-palette text-6xl text-secondary/30 mb-4"></i>';
                    echo '<h3 class="text-2xl font-bold text-gray-600 mb-2">لا توجد أنشطة متاحة</h3>';
                    echo '<p class="text-gray-500">لم يتم العثور على أنشطة. يرجى المحاولة لاحقاً.</p>';
                    echo '</div>';
                }
            }
            ?>
        </div>
        
        <!-- Pagination if on an archive page with many results -->
        <?php if (is_post_type_archive('activity') && !$is_term_page && $wp_query->max_num_pages > 1) : ?>
            <div class="flex justify-center mt-10">
                <?php
                echo paginate_links(array(
                    'prev_text' => '<i class="fas fa-arrow-right ml-2"></i>' . __('السابق', 'sarah-loz'),
                    'next_text' => __('التالي', 'sarah-loz') . '<i class="fas fa-arrow-left mr-2"></i>',
                    'class' => 'bg-secondary text-white px-6 py-3 rounded-full shadow-lg hover:bg-secondary/90 transition',
                ));
                ?>
            </div>
        <?php elseif (!is_post_type_archive('activity')) : ?>
            <!-- View All Activities Button for taxonomy pages or home page -->
            <div class="flex justify-center mt-10">
                <a href="<?php echo esc_url(get_post_type_archive_link('activity')); ?>" class="bg-secondary text-white px-6 py-3 rounded-full shadow-lg hover:bg-secondary/90 transition">
                    <?php _e('عرض جميع الأنشطة', 'sarah-loz'); ?>
                    <i class="fas fa-arrow-left mr-2"></i>
                </a>
            </div>
        <?php endif; ?>
    </section>
      
    <!-- Featured Activity -->
    <section class="mb-16">
        <h2 class="text-2xl md:text-3xl font-bold text-dark mb-8">
            <span class="border-b-4 border-primary pb-2"><?php _e('أنشطة مميزة', 'sarah-loz'); ?></span>
        </h2>
        
        <?php
        // Check if ACF plugin is active before using get_field
        $featured_args = array(
            'post_type' => 'activity',
            'posts_per_page' => 1,
        );
        
        // If ACF is active, use meta query, otherwise just get the latest post as featured
        if (function_exists('get_field')) {
            $featured_args['meta_query'] = array(
                array(
                    'key' => 'is_featured',
                    'value' => '1',
                    'compare' => '='
                )
            );
        }
        
        $featured_query = new WP_Query($featured_args);
        
        if ($featured_query->have_posts()) : 
            while ($featured_query->have_posts()) : $featured_query->the_post();
                // Get age range if ACF function exists
                $age_range = function_exists('get_field') ? get_field('age_range') : '3-8';
        ?>
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
                <div class="h-64 md:h-auto bg-primary/10 relative overflow-hidden">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="h-full">
                            <?php the_post_thumbnail('large', array('class' => 'w-full h-full object-cover')); ?>
                        </div>
                    <?php else : ?>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="fas fa-rocket text-8xl text-primary/30 floating"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="p-8 flex flex-col">
                    <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-sm self-start mb-4"><?php _e('النشاط المميز', 'sarah-loz'); ?></span>
                    <h3 class="text-2xl font-bold text-dark mb-4"><?php the_title(); ?></h3>
                    <p class="text-gray-600 mb-6">
                        <?php echo wp_trim_words(get_the_excerpt(), 40); ?>
                    </p>
                    
                    <div class="mt-auto flex flex-wrap gap-4 items-center">
                        <div>
                            <span class="text-sm text-gray-500 ml-4">
                                <i class="fas fa-child ml-1"></i> <?php echo esc_html($age_range); ?> <?php _e('سنة', 'sarah-loz'); ?>
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-clock ml-1"></i> <?php _e('٦٠ دقيقة', 'sarah-loz'); ?>
                            </span>
                        </div>
                        <a 
                            href="<?php the_permalink(); ?>" 
                            class="bg-primary text-white px-6 py-3 rounded-full shadow-lg hover:bg-primary/90 transition"
                        >
                            <?php _e('عرض النشاط', 'sarah-loz'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
            endwhile;
            wp_reset_postdata();
        else:
            // If no featured activities found, display a message or a call-to-action
        ?>
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-10 p-8 text-center">
            <i class="fas fa-rocket text-6xl text-primary/30 mb-4"></i>
            <h3 class="text-2xl font-bold text-dark mb-4"><?php _e('لا توجد أنشطة مميزة حالياً', 'sarah-loz'); ?></h3>
            <p class="text-gray-600 mb-6">
                <?php _e('سيتم إضافة أنشطة مميزة قريباً. تصفح الأنشطة المتاحة حالياً.', 'sarah-loz'); ?>
            </p>
            <div class="mt-6">
                <a 
                    href="#latest" 
                    class="bg-primary text-white px-6 py-3 rounded-full shadow-lg hover:bg-primary/90 transition inline-block"
                >
                    <?php _e('تصفح الأنشطة', 'sarah-loz'); ?>
                </a>
            </div>
        </div>
        <?php
        endif;
        ?>
    </section>
</main>

<?php
get_footer();
?>
