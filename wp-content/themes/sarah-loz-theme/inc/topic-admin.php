<?php
/**
 * Topic Administration Functions
 * Allows admins to customize topic appearance and settings
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add custom fields to topic add/edit forms
function sarah_loz_add_topic_custom_fields($term) {
    $topic_image = '';
    $topic_color = '';
    $topic_description = '';
    $topic_order = '';
    $topic_featured = '';
    $topic_icon = '';
    $topic_age_groups = array();
    
    if (is_object($term)) {
        $topic_image = get_term_meta($term->term_id, 'topic_image', true);
        $topic_color = get_term_meta($term->term_id, 'topic_color', true);
        $topic_description = get_term_meta($term->term_id, 'topic_description', true);
        $topic_order = get_term_meta($term->term_id, 'topic_order', true);
        $topic_featured = get_term_meta($term->term_id, 'topic_featured', true);
        $topic_icon = get_term_meta($term->term_id, 'topic_icon', true);
        $topic_age_groups = get_term_meta($term->term_id, 'topic_age_groups', true);
        
        if (!is_array($topic_age_groups)) {
            $topic_age_groups = array();
        }
    }
    
    // Get available age groups
    $available_age_groups = array();
    if (function_exists('sarah_loz_get_age_groups')) {
        $age_groups = sarah_loz_get_age_groups();
        foreach ($age_groups as $range => $group_data) {
            $available_age_groups[$range] = $group_data['name'];
        }
    }
    
    ?>
    <div class="form-field">
        <label for="topic_image"><?php _e('صورة الموضوع', 'sarah-loz'); ?></label>
        <input type="url" name="topic_image" id="topic_image" value="<?php echo esc_attr($topic_image); ?>" class="regular-text" />
        <p class="description"><?php _e('أدخل رابط URL للصورة', 'sarah-loz'); ?></p>
    </div>
    
    <div class="form-field">
        <label for="topic_color"><?php _e('لون الموضوع', 'sarah-loz'); ?></label>
        <input type="color" name="topic_color" id="topic_color" value="<?php echo esc_attr($topic_color ?: '#007cba'); ?>" />
        <p class="description"><?php _e('اختر لوناً للموضوع', 'sarah-loz'); ?></p>
    </div>
    
    <div class="form-field">
        <label for="topic_description"><?php _e('وصف الموضوع', 'sarah-loz'); ?></label>
        <textarea name="topic_description" id="topic_description" rows="3" class="large-text"><?php echo esc_textarea($topic_description); ?></textarea>
        <p class="description"><?php _e('أدخل وصفاً مختصراً للموضوع', 'sarah-loz'); ?></p>
    </div>
    
    <div class="form-field">
        <label for="topic_order"><?php _e('ترتيب الموضوع', 'sarah-loz'); ?></label>
        <input type="number" name="topic_order" id="topic_order" value="<?php echo esc_attr($topic_order ?: '0'); ?>" min="0" step="1" />
        <p class="description"><?php _e('أدخل رقماً للترتيب (الأقل يظهر أولاً)', 'sarah-loz'); ?></p>
    </div>
    
    <div class="form-field">
        <label for="topic_featured">
            <input type="checkbox" name="topic_featured" id="topic_featured" value="1" <?php checked($topic_featured, '1'); ?> />
            <?php _e('موضوع مميز', 'sarah-loz'); ?>
        </label>
        <p class="description"><?php _e('حدد إذا كان هذا الموضوع مميزاً', 'sarah-loz'); ?></p>
    </div>
    
    <div class="form-field">
        <label for="topic_icon"><?php _e('أيقونة الموضوع', 'sarah-loz'); ?></label>
        <input type="text" name="topic_icon" id="topic_icon" value="<?php echo esc_attr($topic_icon); ?>" class="regular-text" placeholder="fas fa-book" />
        <p class="description"><?php _e('أدخل اسم أيقونة Font Awesome (مثال: fas fa-book)', 'sarah-loz'); ?></p>
    </div>
    
    <?php if (!empty($available_age_groups)) : ?>
    <div class="form-field">
        <label><?php _e('الفئات العمرية المناسبة', 'sarah-loz'); ?></label>
        <div class="age-groups-checkboxes" style="margin-top: 10px;">
            <?php foreach ($available_age_groups as $range => $name) : ?>
                <label style="display: inline-block; margin-right: 15px; margin-bottom: 10px;">
                    <input type="checkbox" name="topic_age_groups[]" value="<?php echo esc_attr($range); ?>" 
                           <?php checked(in_array($range, $topic_age_groups)); ?> />
                    <?php echo esc_html($name); ?>
                </label>
            <?php endforeach; ?>
        </div>
        <p class="description"><?php _e('حدد الفئات العمرية التي يناسبها هذا الموضوع', 'sarah-loz'); ?></p>
    </div>
    <?php endif; ?>
    
    <style>
    .age-groups-checkboxes label {
        cursor: pointer;
    }
    .age-groups-checkboxes input[type="checkbox"] {
        margin-right: 5px;
    }
    </style>
    <?php
}

// Save custom fields for topic taxonomy
function sarah_loz_save_topic_custom_fields($term_id) {
    if (isset($_POST['topic_image'])) {
        update_term_meta($term_id, 'topic_image', sanitize_url($_POST['topic_image']));
    }
    
    if (isset($_POST['topic_color'])) {
        update_term_meta($term_id, 'topic_color', sanitize_hex_color($_POST['topic_color']));
    }
    
    if (isset($_POST['topic_description'])) {
        update_term_meta($term_id, 'topic_description', sanitize_textarea_field($_POST['topic_description']));
    }
    
    if (isset($_POST['topic_order'])) {
        update_term_meta($term_id, 'topic_order', intval($_POST['topic_order']));
    }
    
    if (isset($_POST['topic_featured'])) {
        update_term_meta($term_id, 'topic_featured', '1');
    } else {
        delete_term_meta($term_id, 'topic_featured');
    }
    
    if (isset($_POST['topic_icon'])) {
        update_term_meta($term_id, 'topic_icon', sanitize_text_field($_POST['topic_icon']));
    }

    if (isset($_POST['topic_age_groups'])) {
        update_term_meta($term_id, 'topic_age_groups', array_map('sanitize_text_field', $_POST['topic_age_groups']));
    } else {
        delete_term_meta($term_id, 'topic_age_groups');
    }
}

// Add custom columns to topic list table
function sarah_loz_add_topic_admin_columns($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'description') {
            $new_columns['topic_image'] = __('الصورة', 'sarah-loz');
            $new_columns['topic_color'] = __('اللون', 'sarah-loz');
            $new_columns['topic_order'] = __('الترتيب', 'sarah-loz');
            $new_columns['topic_featured'] = __('مميز', 'sarah-loz');
            $new_columns['topic_age_groups'] = __('الفئات العمرية', 'sarah-loz');
        }
    }
    
    return $new_columns;
}

// Display custom column content
function sarah_loz_topic_admin_column_content($content, $column_name, $term_id) {
    switch ($column_name) {
        case 'topic_image':
            $image = get_term_meta($term_id, 'topic_image', true);
            if ($image) {
                echo '<img src="' . esc_url($image) . '" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" />';
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;
            
        case 'topic_color':
            $color = get_term_meta($term_id, 'topic_color', true) ?: '#007cba';
            echo '<div style="width: 20px; height: 20px; background: ' . esc_attr($color) . '; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 1px #ddd;"></div>';
            break;
            
        case 'topic_order':
            $order = get_term_meta($term_id, 'topic_order', true) ?: 0;
            echo esc_html($order);
            break;
            
        case 'topic_featured':
            $featured = get_term_meta($term_id, 'topic_featured', true);
            if ($featured) {
                echo '<span style="color: #007cba;">✓</span>';
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;
            
        case 'topic_age_groups':
            $age_groups = get_term_meta($term_id, 'topic_age_groups', true);
            if (!empty($age_groups) && is_array($age_groups)) {
                $age_labels = array();
                foreach ($age_groups as $age_group) {
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
                echo '<span style="color: #007cba;">' . implode(', ', $age_labels) . '</span>';
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;
    }
}

// Make topic order column sortable
function sarah_loz_make_topic_columns_sortable($sortable) {
    $sortable['topic_order'] = 'topic_order';
    return $sortable;
}

// Add topic management page to admin menu
function sarah_loz_add_topic_management_menu() {
    add_submenu_page(
        'edit.php?post_type=activity',
        __('إدارة الموضوعات', 'sarah-loz'),
        __('إدارة الموضوعات', 'sarah-loz'),
        'manage_options',
        'topic-management',
        'sarah_loz_topic_management_page'
    );
}

// Topic management page content
function sarah_loz_topic_management_page() {
    if (isset($_POST['submit_topic_settings'])) {
        // Save global topic settings
        update_option('topics_page_title', sanitize_text_field($_POST['topics_page_title']));
        update_option('topics_page_description', sanitize_textarea_field($_POST['topics_page_description']));
        update_option('topics_per_page', intval($_POST['topics_per_page']));
        update_option('topics_show_featured_first', isset($_POST['topics_show_featured_first']));
        
        echo '<div class="notice notice-success"><p>' . __('تم حفظ الإعدادات بنجاح.', 'sarah-loz') . '</p></div>';
    }
    
    $page_title = get_option('topics_page_title', __('الموضوعات', 'sarah-loz'));
    $page_description = get_option('topics_page_description', __('اختر موضوعاً لاستكشاف المحتوى التعليمي', 'sarah-loz'));
    $topics_per_page = get_option('topics_per_page', 12);
    $show_featured_first = get_option('topics_show_featured_first', true);
    
    ?>
    <div class="wrap">
        <h1><?php _e('إدارة الموضوعات', 'sarah-loz'); ?></h1>
        
        <div class="topic-management-tabs">
            <h2 class="nav-tab-wrapper">
                <a href="#settings" class="nav-tab nav-tab-active"><?php _e('الإعدادات العامة', 'sarah-loz'); ?></a>
                <a href="#topics" class="nav-tab"><?php _e('جميع الموضوعات', 'sarah-loz'); ?></a>
                <a href="#add-new" class="nav-tab"><?php _e('إضافة موضوع جديد', 'sarah-loz'); ?></a>
            </h2>
            
            <div id="settings" class="tab-content active">
                <form method="post" action="">
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="topics_page_title"><?php _e('عنوان صفحة الموضوعات', 'sarah-loz'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="topics_page_title" name="topics_page_title" value="<?php echo esc_attr($page_title); ?>" class="regular-text" />
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="topics_page_description"><?php _e('وصف صفحة الموضوعات', 'sarah-loz'); ?></label>
                            </th>
                            <td>
                                <textarea id="topics_page_description" name="topics_page_description" rows="3" cols="50" class="large-text"><?php echo esc_textarea($page_description); ?></textarea>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="topics_per_page"><?php _e('عدد الموضوعات في الصفحة', 'sarah-loz'); ?></label>
                            </th>
                            <td>
                                <input type="number" id="topics_per_page" name="topics_per_page" value="<?php echo esc_attr($topics_per_page); ?>" min="1" max="50" />
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="topics_show_featured_first"><?php _e('عرض الموضوعات المميزة أولاً', 'sarah-loz'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" id="topics_show_featured_first" name="topics_show_featured_first" value="1" <?php checked($show_featured_first, true); ?> />
                                <label for="topics_show_featured_first"><?php _e('ترتيب الموضوعات المميزة في البداية', 'sarah-loz'); ?></label>
                            </td>
                        </tr>
                    </table>
                    
                    <?php submit_button(__('حفظ الإعدادات', 'sarah-loz'), 'primary', 'submit_topic_settings'); ?>
                </form>
            </div>
            
            <div id="topics" class="tab-content">
                <p><?php _e('يمكنك إدارة الموضوعات من', 'sarah-loz'); ?> <a href="<?php echo admin_url('edit-tags.php?taxonomy=topic'); ?>"><?php _e('صفحة الموضوعات', 'sarah-loz'); ?></a></p>
            </div>
            
            <div id="add-new" class="tab-content">
                <p><?php _e('يمكنك إضافة موضوع جديد من', 'sarah-loz'); ?> <a href="<?php echo admin_url('edit-tags.php?taxonomy=topic'); ?>"><?php _e('صفحة الموضوعات', 'sarah-loz'); ?></a></p>
            </div>
        </div>
    </div>
    
    <style>
    .topic-management-tabs .tab-content {
        display: none;
        padding: 20px 0;
    }
    
    .topic-management-tabs .tab-content.active {
        display: block;
    }
    
    .nav-tab-wrapper {
        margin-bottom: 20px;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        $('.nav-tab').click(function(e) {
            e.preventDefault();
            
            var target = $(this).attr('href').substring(1);
            
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            $('.tab-content').removeClass('active');
            $('#' + target).addClass('active');
        });
    });
    </script>
    <?php
}

// Hook everything up
add_action('topic_add_form_fields', 'sarah_loz_add_topic_custom_fields');
add_action('topic_edit_form_fields', 'sarah_loz_add_topic_custom_fields');
add_action('created_topic', 'sarah_loz_save_topic_custom_fields');
add_action('edited_topic', 'sarah_loz_save_topic_custom_fields');
add_filter('manage_edit-topic_columns', 'sarah_loz_add_topic_admin_columns');
add_action('manage_topic_custom_column', 'sarah_loz_topic_admin_column_content', 10, 3);
add_filter('manage_edit-topic_sortable_columns', 'sarah_loz_make_topic_columns_sortable');
add_action('admin_menu', 'sarah_loz_add_topic_management_menu');

// Function to get topics with custom ordering
function sarah_loz_get_ordered_topics($args = array()) {
    $defaults = array(
        'taxonomy' => 'topic',
        'hide_empty' => true,
        'meta_query' => array(),
        'orderby' => 'meta_value_num',
        'meta_key' => 'topic_order',
        'order' => 'ASC',
    );
    
    $args = wp_parse_args($args, $defaults);
    
    // If showing featured first, we need to handle this specially
    if (get_option('topics_show_featured_first', true)) {
        $featured_topics = get_terms(array(
            'taxonomy' => 'topic',
            'hide_empty' => true,
            'meta_query' => array(
                array(
                    'key' => 'topic_featured',
                    'value' => '1',
                    'compare' => '='
                )
            ),
            'orderby' => 'meta_value_num',
            'meta_key' => 'topic_order',
            'order' => 'ASC',
        ));
        
        $regular_topics = get_terms(array(
            'taxonomy' => 'topic',
            'hide_empty' => true,
            'meta_query' => array(
                array(
                    'key' => 'topic_featured',
                    'value' => '1',
                    'compare' => '!='
                )
            ),
            'orderby' => 'meta_value_num',
            'meta_key' => 'topic_order',
            'order' => 'ASC',
        ));
        
        return array_merge($featured_topics, $regular_topics);
    }
    
    return get_terms($args);
}
