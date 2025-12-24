<?php
/**
 * Template for displaying topic archives
 */

get_header(); 

// Get selected age group
$selected_age_group = function_exists('sarah_loz_get_selected_age_group') ? sarah_loz_get_selected_age_group() : null;
$current_age_data = function_exists('sarah_loz_get_current_age_group_data') ? sarah_loz_get_current_age_group_data() : null;

// Get current topic
$current_topic = get_queried_object();
$topic_image = get_term_meta($current_topic->term_id, 'topic_image', true);
$topic_color = get_term_meta($current_topic->term_id, 'topic_color', true) ?: '#007cba';
$topic_description = get_term_meta($current_topic->term_id, 'topic_description', true);
?>

<div class="topic-archive-page">
    <div class="container">
        <!-- Topic Header -->
        <div class="topic-header" style="background: white; padding: 40px; border-radius: 15px; margin-bottom: 40px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <div class="topic-info-row" style="display: flex; align-items: center; gap: 30px; margin-bottom: 20px;">
                <?php if ($topic_image) : ?>
                    <div class="topic-image-large" style="flex-shrink: 0; width: 120px; height: 120px; border-radius: 50%; overflow: hidden; background: #f0f0f0; border: 4px solid <?php echo esc_attr($topic_color); ?>;">
                        <img src="<?php echo esc_url($topic_image); ?>" alt="<?php echo esc_attr($current_topic->name); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                <?php endif; ?>
                
                <div class="topic-details" style="flex: 1;">
                    <h1 class="topic-title" style="font-size: 2.5rem; color: #333; margin: 0 0 15px 0; font-weight: 700;">
                        <?php echo esc_html($current_topic->name); ?>
                    </h1>
                    
                    <?php if ($topic_description) : ?>
                        <p class="topic-description" style="font-size: 1.2rem; color: #666; margin: 0 0 20px 0; line-height: 1.6;">
                            <?php echo esc_html($topic_description); ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if ($selected_age_group && $current_age_data) : ?>
                        <div class="age-indicator" style="display: inline-block; padding: 10px 20px; background: #e3f2fd; border-radius: 25px; border-left: 4px solid <?php echo esc_attr($topic_color); ?>;">
                            <span style="color: #1976d2; font-weight: 500; font-size: 1.1rem;">
                                <i class="fas fa-child" style="margin-right: 8px;"></i>
                                <?php echo esc_html($current_age_data['name']); ?> - <?php echo esc_html($selected_age_group); ?> سنوات
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <?php
                    // Show topic age groups if available
                    $topic_age_groups = get_term_meta($current_topic->term_id, 'topic_age_groups', true);
                    if (!empty($topic_age_groups) && is_array($topic_age_groups)) : ?>
                        <div class="topic-age-groups" style="margin-top: 15px;">
                            <span style="color: #666; font-size: 0.9rem; margin-right: 10px;"><?php _e('مناسب للأعمار:', 'sarah-loz'); ?></span>
                            <?php 
                            $age_labels = array();
                            foreach ($topic_age_groups as $age_group) {
                                if (function_exists('sarah_loz_get_age_groups')) {
                                    $all_age_groups = sarah_loz_get_age_groups();
                                    if (isset($all_age_groups[$age_group])) {
                                        $age_labels[] = $all_age_groups[$age_group]['name'];
                                    } else {
                                        $age_labels[] = $age_group;
                                    }
                                } else {
                                    $age_labels[] = $age_group;
                                }
                            }
                            ?>
                            <span style="color: #007cba; font-weight: 500;"><?php echo implode(', ', $age_labels); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Content Type Filter Tabs -->
        <div class="content-filter-tabs" style="margin-bottom: 30px;">
            <div class="filter-tabs" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button class="filter-tab active" data-content-type="all" style="padding: 12px 24px; background: #007cba; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; transition: all 0.2s ease;">
                    <?php _e('جميع المحتويات', 'sarah-loz'); ?>
                </button>
                <button class="filter-tab" data-content-type="video" style="padding: 12px 24px; background: #f8f9fa; color: #333; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; transition: all 0.2s ease;">
                    <?php _e('الفيديوهات', 'sarah-loz'); ?>
                </button>
                <button class="filter-tab" data-content-type="activity" style="padding: 12px 24px; background: #f8f9fa; color: #333; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; transition: all 0.2s ease;">
                    <?php _e('الأنشطة', 'sarah-loz'); ?>
                </button>
                <button class="filter-tab" data-content-type="game" style="padding: 12px 24px; background: #f8f9fa; color: #333; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; transition: all 0.2s ease;">
                    <?php _e('الألعاب', 'sarah-loz'); ?>
                </button>
                <button class="filter-tab" data-content-type="theater" style="padding: 12px 24px; background: #f8f9fa; color: #333; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; transition: all 0.2s ease;">
                    <?php _e('المسرحيات', 'sarah-loz'); ?>
                </button>
                <button class="filter-tab" data-content-type="practice" style="padding: 12px 24px; background: #f8f9fa; color: #333; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; transition: all 0.2s ease;">
                    <?php _e('التدريبات', 'sarah-loz'); ?>
                </button>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid" id="content-grid">
            <?php
            // Get content for this topic with age filtering
            $content_items = get_topic_content_with_age_filtering($current_topic->term_id, $selected_age_group);
            
            if (!empty($content_items)) : ?>
                <?php foreach ($content_items as $item) : 
                    $item_type = $item->post_type;
                    $item_title = $item->post_title;
                    $item_excerpt = $item->post_excerpt ?: wp_trim_words($item->post_content, 20);
                    $item_link = get_permalink($item->ID);
                    $item_image = get_the_post_thumbnail_url($item->ID, 'medium');
                    $age_range = get_post_meta($item->ID, 'age_range', true);
                    ?>
                    
                    <div class="content-item" data-content-type="<?php echo esc_attr($item_type); ?>" style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); transition: transform 0.2s ease, box-shadow 0.2s ease;">
                        <div class="item-header" style="display: flex; gap: 20px; margin-bottom: 20px;">
                            <?php if ($item_image) : ?>
                                <div class="item-image" style="flex-shrink: 0; width: 80px; height: 80px; border-radius: 8px; overflow: hidden; background: #f0f0f0;">
                                    <img src="<?php echo esc_url($item_image); ?>" alt="<?php echo esc_attr($item_title); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                            
                            <div class="item-info" style="flex: 1;">
                                <div class="item-type-badge" style="display: inline-block; padding: 4px 12px; background: <?php echo esc_attr($topic_color); ?>; color: white; border-radius: 20px; font-size: 0.8rem; font-weight: 500; margin-bottom: 8px;">
                                    <?php echo get_post_type_label($item_type); ?>
                                </div>
                                
                                <h3 class="item-title" style="margin: 0 0 10px 0; font-size: 1.3rem; font-weight: 600;">
                                    <a href="<?php echo esc_url($item_link); ?>" style="color: #333; text-decoration: none; transition: color 0.2s ease;">
                                        <?php echo esc_html($item_title); ?>
                                    </a>
                                </h3>
                                
                                <p class="item-excerpt" style="color: #666; margin: 0; line-height: 1.5;">
                                    <?php echo esc_html($item_excerpt); ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="item-footer" style="display: flex; justify-content: space-between; align-items: center;">
                            <div class="item-meta">
                                <?php if ($age_range && $age_range !== 'all') : ?>
                                    <span class="age-range" style="background: #e3f2fd; color: #1976d2; padding: 4px 12px; border-radius: 20px; font-size: 0.9rem; font-weight: 500;">
                                        <?php echo esc_html($age_range); ?> سنوات
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <a href="<?php echo esc_url($item_link); ?>" class="btn btn-primary" style="display: inline-block; padding: 10px 20px; background: <?php echo esc_attr($topic_color); ?>; color: white; text-decoration: none; border-radius: 6px; font-weight: 500; transition: all 0.2s ease;">
                                <?php _e('عرض', 'sarah-loz'); ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="no-content" style="text-align: center; padding: 60px 20px; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <?php if ($selected_age_group) : ?>
                        <h3 style="color: #666; margin-bottom: 15px;"><?php printf(__('لا يوجد محتوى مناسب لعمر %s سنوات في هذا الموضوع', 'sarah-loz'), $selected_age_group); ?></h3>
                        <p style="color: #999; margin-bottom: 20px;"><?php _e('جرب تغيير الفئة العمرية أو تحقق لاحقاً من المحتوى الجديد.', 'sarah-loz'); ?></p>
                    <?php else : ?>
                        <h3 style="color: #666; margin-bottom: 15px;"><?php _e('لا يوجد محتوى في هذا الموضوع حالياً', 'sarah-loz'); ?></h3>
                        <p style="color: #999; margin-bottom: 20px;"><?php _e('تحقق لاحقاً من المحتوى الجديد.', 'sarah-loz'); ?></p>
                    <?php endif; ?>
                    
                    <a href="<?php echo esc_url(home_url('/topics/')); ?>" class="btn btn-secondary" style="display: inline-block; padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 6px; font-weight: 500; transition: all 0.2s ease;">
                        <?php _e('العودة إلى الموضوعات', 'sarah-loz'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
/**
 * Get topic content with age filtering
 */
function get_topic_content_with_age_filtering($topic_id, $selected_age_group) {
    $args = array(
        'post_type' => array('game', 'activity', 'video', 'theater', 'practice', 'broadcast', 'product'),
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'tax_query' => array(
            array(
                'taxonomy' => 'topic',
                'field' => 'term_id',
                'terms' => $topic_id,
            ),
        ),
    );
    
    // Add age filtering if age group is selected
    if ($selected_age_group) {
        $args['meta_query'] = sarah_loz_get_age_filter_meta_query($selected_age_group);
    }
    
    return get_posts($args);
}

/**
 * Get post type label in Arabic
 */
function get_post_type_label($post_type) {
    $labels = array(
        'video' => 'فيديو',
        'activity' => 'نشاط',
        'game' => 'لعبة',
        'theater' => 'مسرحية',
        'practice' => 'تدريب',
        'broadcast' => 'بث مباشر',
        'product' => 'منتج'
    );
    
    return isset($labels[$post_type]) ? $labels[$post_type] : $post_type;
}
?>

<style>
.topic-archive-page {
    padding: 40px 0;
    background: #f8f9fa;
    min-height: 60vh;
}

.topic-archive-page .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.content-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 25px;
}

.content-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.item-title a:hover {
    color: #007cba;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.filter-tab:hover {
    background: #e9ecef;
}

.filter-tab.active {
    background: #007cba !important;
    color: white !important;
}

@media (max-width: 768px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .topic-info-row {
        flex-direction: column;
        text-align: center;
    }
    
    .filter-tabs {
        justify-content: center;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Content filtering functionality
    $('.filter-tab').click(function() {
        const contentType = $(this).data('content-type');
        
        // Update active tab
        $('.filter-tab').removeClass('active').css({
            'background': '#f8f9fa',
            'color': '#333'
        });
        $(this).addClass('active').css({
            'background': '#007cba',
            'color': 'white'
        });
        
        // Filter content
        if (contentType === 'all') {
            $('.content-item').show();
        } else {
            $('.content-item').hide();
            $('.content-item[data-content-type="' + contentType + '"]').show();
        }
    });
});
</script>

<?php get_footer(); ?>
