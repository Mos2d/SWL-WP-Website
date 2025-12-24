<?php
/**
 * Template Name: الموضوعات - Topics
 */

get_header(); 

// Get selected age group
$selected_age_group = function_exists('sarah_loz_get_selected_age_group') ? sarah_loz_get_selected_age_group() : null;
$current_age_data = function_exists('sarah_loz_get_current_age_group_data') ? sarah_loz_get_current_age_group_data() : null;
?>

<div class="topics-page">
    <div class="container">
        <div class="page-header">
            <h1 class="page-title"><?php echo esc_html(get_option('topics_page_title', __('الموضوعات', 'sarah-loz'))); ?></h1>
            <p class="page-description"><?php echo esc_html(get_option('topics_page_description', __('اختر موضوعاً لاستكشاف المحتوى التعليمي', 'sarah-loz'))); ?></p>
            
            <?php if ($selected_age_group && $current_age_data) : ?>
                <div class="age-indicator" style="margin-top: 15px; padding: 10px 20px; background: #e3f2fd; border-radius: 25px; display: inline-block;">
                    <span style="color: #1976d2; font-weight: 500;">
                        <i class="fas fa-child" style="margin-right: 8px;"></i>
                        <?php echo esc_html($current_age_data['name']); ?> - <?php echo esc_html($selected_age_group); ?> سنوات
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <?php
        // Get topics with age filtering
        $topics = get_topics_with_age_filtering($selected_age_group);

        if (!empty($topics) && !is_wp_error($topics)) : ?>
            <div class="topics-grid">
                <?php foreach ($topics as $topic) : 
                    $topic_image = get_term_meta($topic->term_id, 'topic_image', true);
                    $topic_color = get_term_meta($topic->term_id, 'topic_color', true) ?: '#007cba';
                    $topic_description = get_term_meta($topic->term_id, 'topic_description', true);
                    $content_count = $topic->count;
                    ?>
                    
                    <div class="topic-card" style="border-left: 4px solid <?php echo esc_attr($topic_color); ?>">
                        <div class="topic-header">
                            <?php if ($topic_image) : ?>
                                <div class="topic-image">
                                    <img src="<?php echo esc_url($topic_image); ?>" alt="<?php echo esc_attr($topic->name); ?>">
                                </div>
                            <?php endif; ?>
                            
                            <div class="topic-info">
                                <h3 class="topic-title">
                                    <a href="<?php echo esc_url(get_term_link($topic)); ?>">
                                        <?php echo esc_html($topic->name); ?>
                                    </a>
                                </h3>
                                
                                <?php if ($topic_description) : ?>
                                    <p class="topic-description"><?php echo esc_html($topic_description); ?></p>
                                <?php endif; ?>
                                
                                <div class="topic-stats">
                                    <span class="content-count">
                                        <?php printf(_n('%s محتوى', '%s محتوى', $content_count, 'sarah-loz'), number_format_i18n($content_count)); ?>
                                    </span>
                                    <?php if ($selected_age_group) : ?>
                                        <span class="age-appropriate" style="background: #4caf50; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.9rem; font-weight: 500; margin-left: 10px;">
                                            <?php echo esc_html($selected_age_group); ?> سنوات
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="topic-actions">
                            <a href="<?php echo esc_url(get_term_link($topic)); ?>" class="btn btn-primary">
                                <?php _e('استكشاف', 'sarah-loz'); ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="no-topics">
                <?php if ($selected_age_group) : ?>
                    <p><?php printf(__('لا توجد موضوعات مناسبة لعمر %s سنوات حالياً.', 'sarah-loz'), $selected_age_group); ?></p>
                    <p style="color: #666; margin-top: 10px;"><?php _e('جرب تغيير الفئة العمرية أو تحقق لاحقاً من المحتوى الجديد.', 'sarah-loz'); ?></p>
                <?php else : ?>
                    <p><?php _e('لا توجد موضوعات متاحة حالياً.', 'sarah-loz'); ?></p>
                <?php endif; ?>
                
                <?php if (is_wp_error($topics)) : ?>
                    <p style="color: red;">Error: <?php echo $topics->get_error_message(); ?></p>
                <?php endif; ?>
                
                <div class="admin-links" style="margin-top: 20px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <h3><?php _e('للمديرين:', 'sarah-loz'); ?></h3>
                    <p><?php _e('لا توجد موضوعات في قاعدة البيانات. يمكنك:', 'sarah-loz'); ?></p>
                    <ul style="margin-left: 20px;">
                        <li><a href="<?php echo admin_url('edit-tags.php?taxonomy=topic'); ?>"><?php _e('إنشاء موضوعات جديدة', 'sarah-loz'); ?></a></li>
                        <li><a href="<?php echo admin_url('edit.php?post_type=activity&page=topic-management'); ?>"><?php _e('إدارة إعدادات الموضوعات', 'sarah-loz'); ?></a></li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
/**
 * Get topics with age filtering
 */
function get_topics_with_age_filtering($selected_age_group) {
    if (!$selected_age_group) {
        // If no age group selected, return all topics
        return get_terms(array(
            'taxonomy' => 'topic',
            'hide_empty' => false,
        ));
    }
    
    // Get all topics first
    $all_topics = get_terms(array(
        'taxonomy' => 'topic',
        'hide_empty' => false,
    ));
    
    if (empty($all_topics) || is_wp_error($all_topics)) {
        return $all_topics;
    }
    
    $age_appropriate_topics = array();
    
    foreach ($all_topics as $topic) {
        // Check if this topic has content appropriate for the selected age group
        if (topic_has_age_appropriate_content($topic->term_id, $selected_age_group)) {
            $age_appropriate_topics[] = $topic;
        }
    }
    
    return $age_appropriate_topics;
}

/**
 * Check if a topic has content appropriate for a specific age group
 */
function topic_has_age_appropriate_content($topic_id, $age_group) {
    // First check if the topic itself has age group restrictions
    $topic_age_groups = get_term_meta($topic_id, 'topic_age_groups', true);
    if (!empty($topic_age_groups) && is_array($topic_age_groups)) {
        // If topic has specific age groups, check if the selected age is included
        if (!in_array($age_group, $topic_age_groups)) {
            return false;
        }
    }
    
    // Get all posts in this topic
    $posts = get_posts(array(
        'post_type' => array('game', 'activity', 'video', 'theater', 'practice', 'broadcast', 'product'),
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'topic',
                'field' => 'term_id',
                'terms' => $topic_id,
            ),
        ),
    ));
    
    if (empty($posts)) {
        return false;
    }
    
    // Check if any post is appropriate for the age group
    foreach ($posts as $post) {
        if (post_is_appropriate_for_age($post->ID, $age_group)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Check if a post is appropriate for a specific age group
 */
function post_is_appropriate_for_age($post_id, $age_group) {
    // Get the age range meta field
    $age_range = get_post_meta($post_id, 'age_range', true);
    
    if (empty($age_range)) {
        return true; // If no age range specified, consider it appropriate
    }
    
    if ($age_range === 'all') {
        return true; // If marked as "all ages", it's appropriate
    }
    
    // Check if the age group is in the range
    if (strpos($age_range, $age_group) !== false) {
        return true;
    }
    
    // Handle broader ranges like "3-9" that should include specific groups
    $broad_ranges = array('3-9', '2-9', '3-10');
    if (in_array($age_range, $broad_ranges)) {
        return true;
    }
    
    return false;
}
?>

<style>
.topics-page {
    padding: 40px 0;
    background: #f8f9fa;
    min-height: 60vh;
}

.topics-page .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.page-header {
    text-align: center;
    margin-bottom: 40px;
}

.page-title {
    font-size: 2.5rem;
    color: #333;
    margin-bottom: 15px;
    font-weight: 700;
}

.page-description {
    font-size: 1.1rem;
    color: #666;
    margin: 0;
}

.topics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 25px;
    margin-top: 30px;
}

.topic-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.topic-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.topic-header {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.topic-image {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    background: #f0f0f0;
}

.topic-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.topic-info {
    flex: 1;
}

.topic-title {
    margin: 0 0 10px 0;
    font-size: 1.4rem;
    font-weight: 600;
}

.topic-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.2s ease;
}

.topic-title a:hover {
    color: #007cba;
}

.topic-description {
    color: #666;
    margin: 0 0 15px 0;
    line-height: 1.5;
}

.content-count {
    background: #e3f2fd;
    color: #1976d2;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

.btn {
    display: inline-block;
    padding: 12px 24px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    text-align: center;
    transition: all 0.2s ease;
}

.btn-primary {
    background: #007cba;
    color: white;
}

.btn-primary:hover {
    background: #005a87;
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .topics-grid {
        grid-template-columns: 1fr;
    }
    
    .topic-header {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<?php get_footer(); ?>
