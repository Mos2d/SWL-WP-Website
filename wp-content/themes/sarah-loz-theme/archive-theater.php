<?php
/**
 * The template for displaying theater archives
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

// Get featured theaters with age filtering if applicable
$featured_theaters_args = array(
    'post_type' => 'theater',
    'posts_per_page' => 3,
    'meta_query' => array(
        array(
            'key' => 'is_featured',
            'value' => '1',
            'compare' => '=',
        ),
    ),
);

// Add age filtering for featured theaters if age group is selected
if ($selected_age_group && !$is_term_page) {
    $enhanced_age_filter = sarah_loz_get_age_filter_meta_query($selected_age_group);
    if (!empty($enhanced_age_filter)) {
        $featured_theaters_args['meta_query'][] = $enhanced_age_filter;
    }
}

$featured_theaters = new WP_Query($featured_theaters_args);
?>

<style>
/* Theater-specific styles for large, kid-friendly layouts */
.theater-card {
    transition: all 0.3s ease;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.theater-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.theater-image {
    width: 100%;
    height: 300px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.theater-card:hover .theater-image {
    transform: scale(1.05);
}

.theater-content {
    padding: 2rem;
    background: white;
}

.theater-title {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 1rem;
    color: #333;
    line-height: 1.3;
}

.theater-excerpt {
    font-size: 1.1rem;
    line-height: 1.6;
    color: #666;
    margin-bottom: 1.5rem;
}

.theater-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 0.9rem;
    color: #888;
}

.theater-categories {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 1rem;
}

.theater-category {
    background: #f0f0f0;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    color: #666;
}

.featured-theater {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
}

.featured-theater .theater-content {
    background: transparent;
}

.featured-theater .theater-title {
    color: white;
    font-size: 2rem;
}

.featured-theater .theater-excerpt {
    color: rgba(255,255,255,0.9);
    font-size: 1.2rem;
}

.featured-theater .theater-category {
    background: rgba(255,255,255,0.2);
    color: white;
}

/* Responsive grid for theaters */
.theaters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

@media (max-width: 768px) {
    .theaters-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .theater-image {
        height: 250px;
    }
    
    .theater-content {
        padding: 1.5rem;
    }
    
    .theater-title {
        font-size: 1.3rem;
    }
    
    .featured-theater .theater-title {
        font-size: 1.6rem;
    }
}
</style>

<!-- Theaters Content -->
<main class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-10">
        <div class="h-64 relative bg-cover bg-center" style="background-image: url('<?php echo get_template_directory_uri() . '/assets/images/theater-bg.png'; ?>');">
            <div class="absolute inset-0 flex items-center justify-center">
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
                                مسرحيات خاصة بـ<?php echo $current_age_data['label']; ?>!
                            </h1>
                            <p class="text-lg text-gray-600"><?php echo $selected_age_group; ?> سنوات</p>
                        </div>
                    </div>
                    <p class="text-lg text-gray-700"><?php echo $current_age_data['description']; ?></p>
                </div>
            <?php elseif ($is_term_page) : ?>
                <h1 class="text-3xl md:text-4xl font-bold text-dark mb-4">
                    <?php 
                    if ($taxonomy_name === 'theater_category') {
                        echo 'مسرحيات في: ' . esc_html($term_name);
                    } elseif ($taxonomy_name === 'age_group') {
                        echo 'مسرحيات لـ: ' . esc_html($term_name);
                    } else {
                        echo esc_html($term_name);
                    }
                    ?>
                </h1>
                <p class="text-lg text-gray-600">اكتشف مسرحيات رائعة وممتعة!</p>
            <?php else : ?>
                <h1 class="text-3xl md:text-4xl font-bold text-dark mb-4">
                    المسرحيات
                </h1>
                <p class="text-lg text-gray-600">اكتشف عالم المسرح المليء بالمغامرات والقصص الرائعة!</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Featured Theaters Section -->
    <?php if ($featured_theaters->have_posts() && !$is_term_page) : ?>
        <section class="mb-12">
            <h2 class="text-2xl font-bold text-dark mb-6 text-center">مسرحيات مميزة</h2>
            <div class="theaters-grid">
                <?php while ($featured_theaters->have_posts()) : $featured_theaters->the_post(); ?>
                    <article class="theater-card featured-theater">
                        <?php if (has_post_thumbnail()) : ?>
                            <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'large'); ?>" 
                                 alt="<?php echo get_the_title(); ?>" 
                                 class="theater-image">
                        <?php endif; ?>
                        <div class="theater-content">
                            <h3 class="theater-title">
                                <a href="<?php echo get_permalink(); ?>" class="hover:underline">
                                    <?php echo get_the_title(); ?>
                                </a>
                            </h3>
                            <div class="theater-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                            </div>
                            <div class="theater-meta">
                                <span>📅 <?php echo get_the_date(); ?></span>
                                <span>👁️ <?php echo get_comments_number(); ?> تعليق</span>
                            </div>
                            <div class="theater-categories">
                                <?php
                                $categories = get_the_terms(get_the_ID(), 'theater_category');
                                if ($categories && !is_wp_error($categories)) {
                                    foreach ($categories as $category) {
                                        echo '<span class="theater-category">' . esc_html($category->name) . '</span>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        </section>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>

    <!-- All Theaters Section -->
    <section>
        <h2 class="text-2xl font-bold text-dark mb-6 text-center">
            <?php if ($is_term_page) : ?>
                جميع المسرحيات
            <?php else : ?>
                جميع المسرحيات
            <?php endif; ?>
        </h2>
        
        <?php if (have_posts()) : ?>
            <div class="theaters-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="theater-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'large'); ?>" 
                                 alt="<?php echo get_the_title(); ?>" 
                                 class="theater-image">
                        <?php endif; ?>
                        <div class="theater-content">
                            <h3 class="theater-title">
                                <a href="<?php echo get_permalink(); ?>" class="hover:underline">
                                    <?php echo get_the_title(); ?>
                                </a>
                            </h3>
                            <div class="theater-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 25); ?>
                            </div>
                            <div class="theater-meta">
                                <span>📅 <?php echo get_the_date(); ?></span>
                                <span>👁️ <?php echo get_comments_number(); ?> تعليق</span>
                            </div>
                            <div class="theater-categories">
                                <?php
                                $categories = get_the_terms(get_the_ID(), 'theater_category');
                                if ($categories && !is_wp_error($categories)) {
                                    foreach ($categories as $category) {
                                        echo '<span class="theater-category">' . esc_html($category->name) . '</span>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <div class="mt-12 flex justify-center">
                <?php
                echo paginate_links(array(
                    'prev_text' => '&larr; السابق',
                    'next_text' => 'التالي &rarr;',
                    'class' => 'pagination',
                ));
                ?>
            </div>
        <?php else : ?>
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🎭</div>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">لا توجد مسرحيات متاحة</h3>
                <p class="text-gray-500">سيتم إضافة مسرحيات جديدة قريباً!</p>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php get_footer(); ?>
