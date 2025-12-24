<?php
/**
 * The template for displaying single theaters
 *
 * @package Sarah_Loz
 */

// Check if we have an age group selected
$selected_age_group = sarah_loz_get_selected_age_group();
$current_age_data = sarah_loz_get_current_age_group_data();

get_header();

// Get custom fields for the theater
$theater_duration = get_field('theater_duration') ?: '00:00';
$view_count = get_field('view_count') ?: 0;
$is_new = get_field('is_new');
$rating = get_field('rating') ?: 0;
$rating_count = get_field('rating_count') ?: 0;
$learning_materials = get_field('learning_materials');
$age_range = get_field('age_range');
$theater_type = get_field('theater_type'); // e.g., puppet show, musical, drama
$cast_info = get_field('cast_info');
$director = get_field('director');
$theater_embed = get_field('theater_embed'); // For embedded theater content

// Increment view count
if (!is_preview()) {
    $view_count++;
    update_field('view_count', $view_count, get_the_ID());
}

// Track user's watched theaters
if (is_user_logged_in()) {
    do_action('sarah_loz_track_progress', get_the_ID());
}
?>

<style>
/* Theater-specific styles for large, kid-friendly layouts */
.theater-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
}

.theater-content {
    font-size: 1.2rem;
    line-height: 1.8;
    color: #333;
}

.theater-content h2 {
    font-size: 2rem;
    font-weight: bold;
    margin: 2rem 0 1rem 0;
    color: #2d3748;
}

.theater-content h3 {
    font-size: 1.5rem;
    font-weight: bold;
    margin: 1.5rem 0 1rem 0;
    color: #4a5568;
}

.theater-content p {
    margin-bottom: 1.5rem;
}

.theater-content ul, .theater-content ol {
    margin: 1.5rem 0;
    padding-left: 2rem;
}

.theater-content li {
    margin-bottom: 0.5rem;
}

.theater-meta-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.theater-meta-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.theater-meta-item:last-child {
    border-bottom: none;
}

.theater-meta-icon {
    font-size: 1.5rem;
    width: 40px;
    text-align: center;
}

.theater-meta-content h4 {
    font-weight: bold;
    color: #333;
    margin-bottom: 0.25rem;
}

.theater-meta-content p {
    color: #666;
    margin: 0;
}

.theater-embed-container {
    position: relative;
    width: 100%;
    height: 0;
    padding-bottom: 56.25%; /* 16:9 aspect ratio */
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
}

.theater-embed-container iframe,
.theater-embed-container video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 0;
}

.theater-image {
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
}

.related-theater-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.related-theater-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.related-theater-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.related-theater-content {
    padding: 1.5rem;
}

.related-theater-title {
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
    color: #333;
}

.related-theater-excerpt {
    color: #666;
    font-size: 0.9rem;
    line-height: 1.5;
}

@media (max-width: 768px) {
    .theater-content {
        font-size: 1.1rem;
    }
    
    .theater-content h2 {
        font-size: 1.6rem;
    }
    
    .theater-content h3 {
        font-size: 1.3rem;
    }
    
    .theater-image {
        height: 300px;
    }
    
    .theater-meta-card {
        padding: 1.5rem;
    }
}
</style>

<!-- Theater Content -->
<main class="container mx-auto px-4 py-8" data-id="<?php echo get_the_ID(); ?>">
    <!-- Breadcrumb -->
    <div class="text-sm text-gray-500 mb-6">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-primary transition"><?php _e('الرئيسية', 'sarah-loz'); ?></a>
        <span class="mx-2">/</span>
        <a href="<?php echo esc_url(get_post_type_archive_link('theater')); ?>" class="hover:text-primary transition"><?php _e('المسرحيات', 'sarah-loz'); ?></a>
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
                        مسرحية مناسبة لـ<?php echo $current_age_data['label']; ?>!
                    </h2>
                    <p class="text-sm text-gray-600">مخصصة للأطفال من عمر <?php echo $selected_age_group; ?> سنوات</p>
                    <?php if ($age_range && strpos($age_range, $selected_age_group) !== false) : ?>
                        <span class="inline-block bg-<?php echo $current_age_data['color']; ?> text-white px-2 py-1 rounded-full text-xs font-bold mt-1">
                            ✨ مناسبة تماماً لعمرك!
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Theater Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Theater Player and Details -->
        <div class="lg:col-span-2">
            <!-- Theater Hero Section -->
            <div class="theater-hero p-8 mb-8">
                <div class="text-center text-white">
                    <h1 class="text-4xl font-bold mb-4"><?php the_title(); ?></h1>
                    <div class="flex justify-center items-center gap-6 text-lg">
                        <span>🎭 <?php echo $theater_type ?: 'مسرحية'; ?></span>
                        <span>⏱️ <?php echo $theater_duration; ?></span>
                        <span>👁️ <?php echo number_format($view_count); ?> مشاهدة</span>
                    </div>
                </div>
            </div>

            <!-- Theater Embed or Image -->
            <?php if ($theater_embed) : ?>
                <div class="theater-embed-container mb-8">
                    <?php echo $theater_embed; ?>
                </div>
            <?php elseif (has_post_thumbnail()) : ?>
                <div class="mb-8">
                    <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'large'); ?>" 
                         alt="<?php echo get_the_title(); ?>" 
                         class="theater-image">
                </div>
            <?php endif; ?>

            <!-- Theater Content -->
            <div class="theater-content bg-white rounded-3xl p-8 shadow-lg">
                <?php the_content(); ?>
            </div>

            <!-- Learning Materials -->
            <?php if ($learning_materials) : ?>
                <div class="bg-blue-50 rounded-3xl p-8 mt-8">
                    <h3 class="text-2xl font-bold text-blue-800 mb-4">📚 مواد تعليمية</h3>
                    <div class="prose prose-lg">
                        <?php echo $learning_materials; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Comments Section -->
            <div class="bg-white rounded-3xl p-8 mt-8 shadow-lg">
                <h3 class="text-2xl font-bold text-gray-800 mb-6">💬 التعليقات</h3>
                <?php
                if (comments_open() || get_comments_number()) {
                    comments_template();
                }
                ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Theater Meta Information -->
            <div class="theater-meta-card">
                <h3 class="text-xl font-bold text-gray-800 mb-4">🎬 معلومات المسرحية</h3>
                
                <?php if ($director) : ?>
                    <div class="theater-meta-item">
                        <div class="theater-meta-icon">🎬</div>
                        <div class="theater-meta-content">
                            <h4>المخرج</h4>
                            <p><?php echo esc_html($director); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($cast_info) : ?>
                    <div class="theater-meta-item">
                        <div class="theater-meta-icon">👥</div>
                        <div class="theater-meta-content">
                            <h4>طاقم التمثيل</h4>
                            <p><?php echo esc_html($cast_info); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="theater-meta-item">
                    <div class="theater-meta-icon">⏱️</div>
                    <div class="theater-meta-content">
                        <h4>المدة</h4>
                        <p><?php echo $theater_duration; ?></p>
                    </div>
                </div>

                <div class="theater-meta-item">
                    <div class="theater-meta-icon">👁️</div>
                    <div class="theater-meta-content">
                        <h4>عدد المشاهدات</h4>
                        <p><?php echo number_format($view_count); ?></p>
                    </div>
                </div>

                <?php if ($rating > 0) : ?>
                    <div class="theater-meta-item">
                        <div class="theater-meta-icon">⭐</div>
                        <div class="theater-meta-content">
                            <h4>التقييم</h4>
                            <p><?php echo $rating; ?>/5 (<?php echo $rating_count; ?> تقييم)</p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="theater-meta-item">
                    <div class="theater-meta-icon">📅</div>
                    <div class="theater-meta-content">
                        <h4>تاريخ النشر</h4>
                        <p><?php echo get_the_date(); ?></p>
                    </div>
                </div>
            </div>

            <!-- Theater Categories -->
            <?php
            $categories = get_the_terms(get_the_ID(), 'theater_category');
            if ($categories && !is_wp_error($categories)) : ?>
                <div class="theater-meta-card">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">🏷️ التصنيفات</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($categories as $category) : ?>
                            <a href="<?php echo get_term_link($category); ?>" 
                               class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm hover:bg-blue-200 transition">
                                <?php echo esc_html($category->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Age Groups -->
            <?php
            $age_groups = get_the_terms(get_the_ID(), 'age_group');
            if ($age_groups && !is_wp_error($age_groups)) : ?>
                <div class="theater-meta-card">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">👶 الفئات العمرية</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($age_groups as $age_group) : ?>
                            <a href="<?php echo get_term_link($age_group); ?>" 
                               class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm hover:bg-green-200 transition">
                                <?php echo esc_html($age_group->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Related Theaters -->
    <?php
    $related_theaters = new WP_Query(array(
        'post_type' => 'theater',
        'posts_per_page' => 3,
        'post__not_in' => array(get_the_ID()),
        'meta_query' => array(
            array(
                'key' => 'age_range',
                'value' => $selected_age_group,
                'compare' => 'LIKE',
            ),
        ),
    ));

    if ($related_theaters->have_posts()) : ?>
        <section class="mt-16">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">🎭 مسرحيات مشابهة</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php while ($related_theaters->have_posts()) : $related_theaters->the_post(); ?>
                    <article class="related-theater-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" 
                                 alt="<?php echo get_the_title(); ?>" 
                                 class="related-theater-image">
                        <?php endif; ?>
                        <div class="related-theater-content">
                            <h3 class="related-theater-title">
                                <a href="<?php echo get_permalink(); ?>" class="hover:text-blue-600 transition">
                                    <?php echo get_the_title(); ?>
                                </a>
                            </h3>
                            <p class="related-theater-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                            </p>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        </section>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>
</main>

<?php get_footer(); ?>
