<?php
/**
 * Newsletter Functionality
 * 
 * Handles subscriber management and email sending
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SWL_Newsletter {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Register custom post type for subscribers
        add_action('init', array($this, 'register_subscriber_post_type'));
        
        // Register newsletter post type to save sent newsletters
        add_action('init', array($this, 'register_newsletter_post_type'));
        
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Handle form submission on frontend
        add_action('wp_ajax_swl_newsletter_subscribe', array($this, 'handle_subscription'));
        add_action('wp_ajax_nopriv_swl_newsletter_subscribe', array($this, 'handle_subscription'));
        
        // Admin AJAX handlers
        add_action('wp_ajax_swl_send_newsletter', array($this, 'send_newsletter'));
        
        // Add settings page
        add_action('admin_init', array($this, 'register_settings'));
        
        // Add metaboxes
        add_action('add_meta_boxes', array($this, 'add_subscriber_meta_boxes'));
        
        // Add subscriber columns
        add_filter('manage_subscriber_posts_columns', array($this, 'subscriber_columns'));
        add_action('manage_subscriber_posts_custom_column', array($this, 'subscriber_column_content'), 10, 2);
        add_filter('manage_edit-subscriber_sortable_columns', array($this, 'subscriber_sortable_columns'));
        
        // Add bulk actions
        add_filter('bulk_actions-edit-subscriber', array($this, 'register_bulk_actions'));
        add_filter('handle_bulk_actions-edit-subscriber', array($this, 'handle_bulk_actions'), 10, 3);
        
        // Add admin notices for bulk actions
        add_action('admin_notices', array($this, 'bulk_action_admin_notice'));
    }
    
    /**
     * Register subscriber post type
     */
    public function register_subscriber_post_type() {
        $labels = array(
            'name'               => _x('المشتركين', 'post type general name', 'sarah-loz-theme'),
            'singular_name'      => _x('مشترك', 'post type singular name', 'sarah-loz-theme'),
            'menu_name'          => _x('المشتركين', 'admin menu', 'sarah-loz-theme'),
            'name_admin_bar'     => _x('مشترك', 'add new on admin bar', 'sarah-loz-theme'),
            'add_new'            => _x('إضافة مشترك جديد', 'subscriber', 'sarah-loz-theme'),
            'add_new_item'       => __('إضافة مشترك جديد', 'sarah-loz-theme'),
            'new_item'           => __('مشترك جديد', 'sarah-loz-theme'),
            'edit_item'          => __('تعديل مشترك', 'sarah-loz-theme'),
            'view_item'          => __('عرض مشترك', 'sarah-loz-theme'),
            'all_items'          => __('جميع المشتركين', 'sarah-loz-theme'),
            'search_items'       => __('بحث المشتركين', 'sarah-loz-theme'),
            'not_found'          => __('لم يتم العثور على أي مشتركين', 'sarah-loz-theme'),
            'not_found_in_trash' => __('لم يتم العثور على أي مشتركين في سلة المهملات', 'sarah-loz-theme')
        );
        
        $args = array(
            'labels'              => $labels,
            'description'         => __('مشتركي النشرة البريدية', 'sarah-loz-theme'),
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'query_var'           => false,
            'rewrite'             => false,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => null,
            'supports'            => array('title'),
        );
        
        register_post_type('subscriber', $args);
    }
    
    /**
     * Register newsletter post type
     */
    public function register_newsletter_post_type() {
        $labels = array(
            'name'               => _x('النشرات البريدية', 'post type general name', 'sarah-loz-theme'),
            'singular_name'      => _x('نشرة بريدية', 'post type singular name', 'sarah-loz-theme'),
            'menu_name'          => _x('النشرات البريدية', 'admin menu', 'sarah-loz-theme'),
            'name_admin_bar'     => _x('نشرة بريدية', 'add new on admin bar', 'sarah-loz-theme'),
            'add_new'            => _x('إضافة نشرة جديدة', 'newsletter', 'sarah-loz-theme'),
            'add_new_item'       => __('إضافة نشرة جديدة', 'sarah-loz-theme'),
            'new_item'           => __('نشرة جديدة', 'sarah-loz-theme'),
            'edit_item'          => __('تعديل نشرة', 'sarah-loz-theme'),
            'view_item'          => __('عرض نشرة', 'sarah-loz-theme'),
            'all_items'          => __('جميع النشرات', 'sarah-loz-theme'),
            'search_items'       => __('بحث النشرات', 'sarah-loz-theme'),
            'not_found'          => __('لم يتم العثور على أي نشرات', 'sarah-loz-theme'),
            'not_found_in_trash' => __('لم يتم العثور على أي نشرات في سلة المهملات', 'sarah-loz-theme')
        );
        
        $args = array(
            'labels'              => $labels,
            'description'         => __('النشرات البريدية المرسلة', 'sarah-loz-theme'),
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'query_var'           => false,
            'rewrite'             => false,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => null,
            'supports'            => array('title', 'editor'),
        );
        
        register_post_type('newsletter', $args);
    }
    
    /**
     * Add subscriber meta boxes
     */
    public function add_subscriber_meta_boxes() {
        add_meta_box(
            'swl_subscriber_info',
            __('معلومات المشترك', 'sarah-loz-theme'),
            array($this, 'render_subscriber_meta_box'),
            'subscriber',
            'normal',
            'high'
        );
    }
    
    /**
     * Render subscriber meta box
     */
    public function render_subscriber_meta_box($post) {
        $email = get_post_meta($post->ID, 'email', true);
        $subscribe_date = get_post_meta($post->ID, 'subscribe_date', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="subscriber_email"><?php _e('البريد الإلكتروني', 'sarah-loz-theme'); ?></label></th>
                <td>
                    <input type="email" id="subscriber_email" name="subscriber_email" value="<?php echo esc_attr($email); ?>" class="regular-text" readonly>
                </td>
            </tr>
            <tr>
                <th><label><?php _e('تاريخ الاشتراك', 'sarah-loz-theme'); ?></label></th>
                <td>
                    <?php echo !empty($subscribe_date) ? date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($subscribe_date)) : __('غير معروف', 'sarah-loz-theme'); ?>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Define subscriber columns
     */
    public function subscriber_columns($columns) {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('المشترك', 'sarah-loz-theme'),
            'email' => __('البريد الإلكتروني', 'sarah-loz-theme'),
            'subscribe_date' => __('تاريخ الاشتراك', 'sarah-loz-theme'),
        );
        
        return $columns;
    }
    
    /**
     * Subscriber column content
     */
    public function subscriber_column_content($column, $post_id) {
        switch ($column) {
            case 'email':
                echo esc_html(get_post_meta($post_id, 'email', true));
                break;
            case 'subscribe_date':
                $subscribe_date = get_post_meta($post_id, 'subscribe_date', true);
                echo !empty($subscribe_date) ? date_i18n(get_option('date_format'), strtotime($subscribe_date)) : __('غير معروف', 'sarah-loz-theme');
                break;
        }
    }
    
    /**
     * Make subscriber columns sortable
     */
    public function subscriber_sortable_columns($columns) {
        $columns['email'] = 'email';
        $columns['subscribe_date'] = 'subscribe_date';
        
        return $columns;
    }
    
    /**
     * Add admin menu for newsletter
     */
    public function add_admin_menu() {
        add_menu_page(
            __('النشرة البريدية', 'sarah-loz-theme'),
            __('النشرة البريدية', 'sarah-loz-theme'),
            'manage_options',
            'swl-newsletter',
            array($this, 'render_admin_page'),
            'dashicons-email',
            30
        );
        
        add_submenu_page(
            'swl-newsletter',
            __('المشتركين', 'sarah-loz-theme'),
            __('المشتركين', 'sarah-loz-theme'),
            'manage_options',
            'edit.php?post_type=subscriber'
        );
        
        add_submenu_page(
            'swl-newsletter',
            __('النشرات المرسلة', 'sarah-loz-theme'),
            __('النشرات المرسلة', 'sarah-loz-theme'),
            'manage_options',
            'edit.php?post_type=newsletter'
        );
        
        add_submenu_page(
            'swl-newsletter',
            __('إعدادات النشرة البريدية', 'sarah-loz-theme'),
            __('الإعدادات', 'sarah-loz-theme'),
            'manage_options',
            'swl-newsletter-settings',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('swl_newsletter_settings', 'swl_newsletter_from_name');
        register_setting('swl_newsletter_settings', 'swl_newsletter_from_email');
        register_setting('swl_newsletter_settings', 'swl_newsletter_header_image');
        register_setting('swl_newsletter_settings', 'swl_newsletter_footer_text');
        register_setting('swl_newsletter_settings', 'swl_newsletter_auto_welcome');
    }
    
    /**
     * Render main admin page
     */
    public function render_admin_page() {
        // Enqueue admin scripts and styles
        wp_enqueue_editor();
        wp_enqueue_script('swl-newsletter-admin', get_template_directory_uri() . '/assets/js/newsletter-admin.js', array('jquery'), '1.0.0', true);
        wp_localize_script('swl-newsletter-admin', 'swl_newsletter', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('swl_newsletter_nonce')
        ));
        
        // Get subscriber count
        $subscriber_count = wp_count_posts('subscriber');
        ?>
        <div class="wrap swl-newsletter-admin">
            <h1><?php _e('النشرة البريدية', 'sarah-loz-theme'); ?></h1>
            
            <div class="swl-newsletter-dashboard">
                <div class="swl-newsletter-stats">
                    <div class="swl-stat-box">
                        <h3><?php _e('المشتركين النشطين', 'sarah-loz-theme'); ?></h3>
                        <p class="swl-big-number"><?php echo $subscriber_count->publish; ?></p>
                    </div>
                    
                    <div class="swl-stat-box">
                        <h3><?php _e('إحصائيات', 'sarah-loz-theme'); ?></h3>
                        <ul class="swl-stats-list">
                            <li>
                                <span class="stat-label"><?php _e('النشرات المرسلة:', 'sarah-loz-theme'); ?></span>
                                <span class="stat-value"><?php echo wp_count_posts('newsletter')->publish; ?></span>
                            </li>
                            <li>
                                <span class="stat-label"><?php _e('اشتراكات اليوم:', 'sarah-loz-theme'); ?></span>
                                <span class="stat-value">
                                    <?php 
                                    $today_subscribers = count(get_posts(array(
                                        'post_type' => 'subscriber',
                                        'post_status' => 'publish',
                                        'date_query' => array(
                                            array(
                                                'after' => '1 day ago'
                                            )
                                        ),
                                        'fields' => 'ids',
                                        'posts_per_page' => -1
                                    ))); 
                                    echo $today_subscribers;
                                    ?>
                                </span>
                            </li>
                        </ul>
                    </div>

                    <div class="swl-actions-box">
                        <h3><?php _e('روابط سريعة', 'sarah-loz-theme'); ?></h3>
                        <div class="swl-action-buttons">
                            <a href="<?php echo admin_url('edit.php?post_type=subscriber'); ?>" class="button">
                                <i class="dashicons dashicons-groups"></i>
                                <?php _e('إدارة المشتركين', 'sarah-loz-theme'); ?>
                            </a>
                            <a href="<?php echo admin_url('edit.php?post_type=newsletter'); ?>" class="button">
                                <i class="dashicons dashicons-email-alt"></i>
                                <?php _e('سجل النشرات', 'sarah-loz-theme'); ?>
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=swl-newsletter-settings'); ?>" class="button">
                                <i class="dashicons dashicons-admin-settings"></i>
                                <?php _e('الإعدادات', 'sarah-loz-theme'); ?>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="swl-newsletter-composer">
                    <h2><?php _e('إرسال نشرة بريدية جديدة', 'sarah-loz-theme'); ?></h2>
                    
                    <form id="swl-newsletter-form">
                        <div class="swl-form-row">
                            <label for="newsletter-subject"><?php _e('عنوان النشرة البريدية:', 'sarah-loz-theme'); ?></label>
                            <input type="text" id="newsletter-subject" name="subject" class="regular-text" required>
                        </div>
                        
                        <div class="swl-form-row">
                            <label for="newsletter-content"><?php _e('محتوى النشرة البريدية:', 'sarah-loz-theme'); ?></label>
                            <?php
                            wp_editor('', 'newsletter-content', array(
                                'media_buttons' => true,
                                'textarea_name' => 'content',
                                'textarea_rows' => 15,
                                'editor_height' => 300,
                                'teeny' => false,
                            ));
                            ?>
                        </div>
                        
                        <div class="swl-form-row">
                            <div class="swl-send-options">
                                <h3><?php _e('خيارات الإرسال:', 'sarah-loz-theme'); ?></h3>
                                <label class="swl-radio-option">
                                    <input type="radio" name="send_type" value="all" checked>
                                    <span class="option-text"><?php _e('إرسال إلى جميع المشتركين', 'sarah-loz-theme'); ?></span>
                                </label>
                                <label class="swl-radio-option swl-test-option">
                                    <input type="radio" name="send_type" value="test">
                                    <span class="option-text"><?php _e('إرسال اختبار إلى:', 'sarah-loz-theme'); ?></span>
                                    <input type="email" name="test_email" placeholder="example@example.com" class="regular-text">
                                </label>
                            </div>
                        </div>
                        
                        <div class="swl-form-row">
                            <input type="hidden" name="action" value="swl_send_newsletter">
                            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('swl_newsletter_nonce'); ?>">
                            <button type="submit" class="button button-primary button-large" id="swl-send-newsletter">
                                <i class="dashicons dashicons-email-alt"></i>
                                <?php _e('إرسال النشرة البريدية', 'sarah-loz-theme'); ?>
                            </button>
                            <span class="spinner" id="swl-newsletter-spinner"></span>
                        </div>
                    </form>
                    
                    <div id="swl-newsletter-results"></div>
                </div>
            </div>
        </div>
        <style>
            .swl-newsletter-admin {
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px 0;
            }
            .swl-newsletter-dashboard {
                display: flex;
                flex-wrap: wrap;
                margin: 20px 0;
                gap: 30px;
            }
            .swl-newsletter-stats {
                flex: 0 0 300px;
            }
            .swl-newsletter-composer {
                flex: 1;
                min-width: 600px;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                padding: 25px;
            }
            .swl-newsletter-composer h2 {
                margin-top: 0;
                padding-bottom: 15px;
                border-bottom: 1px solid #eee;
                color: #23282d;
            }
            .swl-stat-box, .swl-actions-box {
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                padding: 20px;
                margin-bottom: 20px;
                text-align: center;
            }
            .swl-stat-box h3, .swl-actions-box h3 {
                margin-top: 0;
                font-size: 16px;
                color: #23282d;
                font-weight: 600;
                text-align: center;
                margin-bottom: 15px;
                padding-bottom: 10px;
                border-bottom: 1px solid #eee;
            }
            .swl-big-number {
                font-size: 48px;
                font-weight: bold;
                color: #23282d;
                margin: 10px 0;
                line-height: 1;
            }
            .swl-stats-list {
                margin: 0;
                padding: 0;
                list-style: none;
                text-align: right;
            }
            .swl-stats-list li {
                margin-bottom: 10px;
                display: flex;
                justify-content: space-between;
                padding: 5px 0;
                border-bottom: 1px dashed #eee;
            }
            .swl-stats-list li:last-child {
                margin-bottom: 0;
                border-bottom: none;
            }
            .stat-label {
                color: #666;
                font-weight: normal;
            }
            .stat-value {
                font-weight: bold;
                color: #23282d;
            }
            .swl-action-buttons {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            .swl-action-buttons .button {
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                padding: 8px 12px;
                height: auto;
                transition: all 0.2s ease;
            }
            .swl-action-buttons .button:hover {
                transform: translateY(-2px);
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
            .swl-action-buttons .dashicons {
                margin-left: 5px;
            }
            .swl-form-row {
                margin-bottom: 25px;
            }
            .swl-form-row label {
                display: block;
                margin-bottom: 8px;
                font-weight: 600;
                color: #23282d;
            }
            .swl-form-row input[type="text"], 
            .swl-form-row input[type="email"] {
                width: 100%;
                max-width: 100%;
                padding: 8px 12px;
                border-radius: 4px;
            }
            .swl-send-options {
                background: #f9f9f9;
                padding: 20px;
                border-radius: 8px;
                border: 1px solid #eee;
            }
            .swl-send-options h3 {
                margin-top: 0;
                margin-bottom: 15px;
                font-size: 14px;
                font-weight: 600;
                color: #23282d;
            }
            .swl-radio-option {
                display: block;
                margin-bottom: 15px;
                padding: 8px 0;
                cursor: pointer;
            }
            .swl-radio-option:last-child {
                margin-bottom: 0;
            }
            .swl-radio-option input[type="radio"] {
                margin-left: 8px;
            }
            .swl-test-option {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
            }
            .swl-test-option .option-text {
                margin-left: 10px;
            }
            .swl-test-option input[type="email"] {
                margin-top: 5px;
                width: 100%;
            }
            #swl-send-newsletter {
                padding: 5px 20px;
                height: auto;
                min-height: 40px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 15px;
            }
            #swl-send-newsletter .dashicons {
                margin-left: 5px;
            }
            #swl-newsletter-spinner {
                float: none;
                margin: 0 10px;
            }
            #swl-newsletter-results {
                margin-top: 20px;
            }
            .swl-success {
                background: #f0f8e6;
                border-right: 4px solid #46b450;
                padding: 15px;
                margin: 15px 0;
                border-radius: 4px;
            }
            .swl-error {
                background: #fbeaea;
                border-right: 4px solid #dc3232;
                padding: 15px;
                margin: 15px 0;
                border-radius: 4px;
            }
            
            /* RTL specific styles */
            .rtl .swl-newsletter-stats {
                margin-left: 0;
                margin-right: 0;
            }
            .rtl .swl-action-buttons .dashicons {
                margin-left: 0;
                margin-right: 5px;
            }
            .rtl #swl-send-newsletter .dashicons {
                margin-left: 0;
                margin-right: 5px;
            }
            .rtl .swl-radio-option input[type="radio"] {
                margin-left: 0;
                margin-right: 8px;
            }
            .rtl .swl-test-option .option-text {
                margin-left: 0;
                margin-right: 10px;
            }
            
            /* Media queries for responsiveness */
            @media screen and (max-width: 1100px) {
                .swl-newsletter-dashboard {
                    flex-direction: column;
                }
                .swl-newsletter-stats {
                    flex: 0 0 100%;
                    display: flex;
                    flex-wrap: wrap;
                    gap: 20px;
                }
                .swl-stat-box, .swl-actions-box {
                    flex: 1;
                    min-width: 200px;
                }
                .swl-newsletter-composer {
                    min-width: 100%;
                }
            }
            
            @media screen and (max-width: 782px) {
                .swl-newsletter-stats {
                    flex-direction: column;
                }
            }
        </style>
        <?php
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        ?>
        <div class="wrap swl-newsletter-admin">
            <h1><?php _e('إعدادات النشرة البريدية', 'sarah-loz-theme'); ?></h1>
            
            <div class="swl-settings-container">
                <form method="post" action="options.php">
                    <?php settings_fields('swl_newsletter_settings'); ?>
                    <?php do_settings_sections('swl_newsletter_settings'); ?>
                    
                    <div class="swl-settings-section">
                        <h2 class="swl-settings-title"><?php _e('إعدادات البريد الإلكتروني', 'sarah-loz-theme'); ?></h2>
                        
                        <div class="swl-setting-field">
                            <label for="swl_newsletter_from_name"><?php _e('اسم المرسل', 'sarah-loz-theme'); ?></label>
                            <input type="text" id="swl_newsletter_from_name" name="swl_newsletter_from_name" 
                                value="<?php echo esc_attr(get_option('swl_newsletter_from_name', get_bloginfo('name'))); ?>" 
                                class="regular-text" />
                            <p class="description"><?php _e('الاسم الذي سيظهر كمرسل للنشرة البريدية', 'sarah-loz-theme'); ?></p>
                        </div>
                        
                        <div class="swl-setting-field">
                            <label for="swl_newsletter_from_email"><?php _e('البريد الإلكتروني للمرسل', 'sarah-loz-theme'); ?></label>
                            <input type="email" id="swl_newsletter_from_email" name="swl_newsletter_from_email" 
                                value="<?php echo esc_attr(get_option('swl_newsletter_from_email', get_bloginfo('admin_email'))); ?>" 
                                class="regular-text" />
                            <p class="description"><?php _e('عنوان البريد الإلكتروني الذي سيتم استخدامه لإرسال النشرات البريدية', 'sarah-loz-theme'); ?></p>
                        </div>
                    </div>
                    
                    <div class="swl-settings-section">
                        <h2 class="swl-settings-title"><?php _e('تخصيص القالب', 'sarah-loz-theme'); ?></h2>
                        
                        <div class="swl-setting-field">
                            <label for="swl_newsletter_header_image"><?php _e('صورة الترويسة (اختياري)', 'sarah-loz-theme'); ?></label>
                            <input type="url" id="swl_newsletter_header_image" name="swl_newsletter_header_image" 
                                value="<?php echo esc_attr(get_option('swl_newsletter_header_image')); ?>" 
                                class="regular-text" />
                            <p class="description"><?php _e('URL لصورة ترويسة رسائل البريد الإلكتروني', 'sarah-loz-theme'); ?></p>
                        </div>
                        
                        <div class="swl-setting-field">
                            <label for="swl_newsletter_footer_text"><?php _e('نص التذييل', 'sarah-loz-theme'); ?></label>
                            <textarea id="swl_newsletter_footer_text" name="swl_newsletter_footer_text" rows="3" class="large-text"><?php 
                                echo esc_textarea(get_option('swl_newsletter_footer_text', 
                                    sprintf(__('© %s %s - جميع الحقوق محفوظة', 'sarah-loz-theme'), 
                                        date('Y'), get_bloginfo('name'))
                                )); 
                            ?></textarea>
                            <p class="description"><?php _e('نص التذييل الذي سيظهر في أسفل رسائل البريد الإلكتروني', 'sarah-loz-theme'); ?></p>
                        </div>
                    </div>
                    
                    <div class="swl-settings-section">
                        <h2 class="swl-settings-title"><?php _e('الإعدادات المتقدمة', 'sarah-loz-theme'); ?></h2>
                        
                        <div class="swl-setting-field">
                            <label>
                                <input type="checkbox" name="swl_newsletter_auto_welcome" 
                                    value="1" <?php checked('1', get_option('swl_newsletter_auto_welcome', '0')); ?> />
                                <?php _e('إرسال رسالة ترحيبية تلقائية للمشتركين الجدد', 'sarah-loz-theme'); ?>
                            </label>
                        </div>
                    </div>
                    
                    <?php submit_button(__('حفظ الإعدادات', 'sarah-loz-theme'), 'primary', 'submit', false, ['class' => 'swl-submit-button']); ?>
                </form>
            </div>
        </div>
        <style>
            .swl-newsletter-admin {
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px 0;
            }
            .swl-settings-container {
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                padding: 25px;
                margin-top: 20px;
                max-width: 800px;
            }
            .swl-settings-section {
                margin-bottom: 30px;
                padding-bottom: 20px;
                border-bottom: 1px solid #eee;
            }
            .swl-settings-section:last-child {
                border-bottom: none;
                padding-bottom: 0;
            }
            .swl-settings-title {
                margin-top: 0;
                font-size: 18px;
                color: #23282d;
                font-weight: 600;
                margin-bottom: 20px;
                padding-bottom: 10px;
                border-bottom: 1px solid #f0f0f0;
            }
            .swl-setting-field {
                margin-bottom: 20px;
            }
            .swl-setting-field:last-child {
                margin-bottom: 0;
            }
            .swl-setting-field label {
                display: block;
                margin-bottom: 8px;
                font-weight: 600;
                color: #23282d;
            }
            .swl-setting-field input[type="text"],
            .swl-setting-field input[type="email"],
            .swl-setting-field input[type="url"],
            .swl-setting-field textarea {
                width: 100%;
                padding: 8px 12px;
                border-radius: 4px;
                border-color: #ddd;
            }
            .swl-setting-field textarea {
                min-height: 100px;
            }
            .swl-setting-field .description {
                margin-top: 6px;
                color: #666;
                font-style: italic;
            }
            .swl-setting-field input[type="checkbox"] {
                margin-left: 8px;
            }
            .swl-submit-button {
                margin-top: 15px !important;
                padding: 5px 20px !important;
                height: auto !important;
                min-height: 40px !important;
                font-size: 15px !important;
            }
            
            /* RTL specific styles */
            .rtl .swl-setting-field input[type="checkbox"] {
                margin-left: 0;
                margin-right: 8px;
            }
        </style>
        <?php
    }
    
    /**
     * Handle subscription form submission
     */
    public function handle_subscription() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'swl_newsletter_nonce')) {
            wp_send_json_error(array('message' => __('فشلت عملية التحقق من الأمان', 'sarah-loz-theme')));
        }
        
        // Check for required fields
        if (empty($_POST['email'])) {
            wp_send_json_error(array('message' => __('البريد الإلكتروني مطلوب', 'sarah-loz-theme')));
        }
        
        // Validate email
        $email = sanitize_email($_POST['email']);
        if (!is_email($email)) {
            wp_send_json_error(array('message' => __('البريد الإلكتروني غير صالح', 'sarah-loz-theme')));
        }
        
        // Check if already subscribed
        $existing = get_posts(array(
            'post_type' => 'subscriber',
            'meta_key' => 'email',
            'meta_value' => $email,
            'posts_per_page' => 1
        ));
        
        if (!empty($existing)) {
            wp_send_json_error(array('message' => __('هذا البريد الإلكتروني مشترك بالفعل', 'sarah-loz-theme')));
        }
        
        // Check terms consent
        if (!isset($_POST['terms']) || $_POST['terms'] !== 'on') {
            wp_send_json_error(array('message' => __('يجب الموافقة على سياسة الخصوصية وشروط الاستخدام', 'sarah-loz-theme')));
        }
        
        // Create subscriber
        $subscriber_id = wp_insert_post(array(
            'post_title' => $email,
            'post_type' => 'subscriber',
            'post_status' => 'publish'
        ));
        
        if (is_wp_error($subscriber_id)) {
            wp_send_json_error(array('message' => __('حدث خطأ أثناء تسجيل اشتراكك', 'sarah-loz-theme')));
        }
        
        // Add subscriber metadata
        update_post_meta($subscriber_id, 'email', $email);
        update_post_meta($subscriber_id, 'subscribe_date', current_time('mysql'));
        
        // Send welcome email if enabled
        if (get_option('swl_newsletter_auto_welcome', '0') === '1') {
            $this->send_welcome_email($email);
        }
        
        // Return success
        wp_send_json_success(array('message' => __('تم تسجيل اشتراكك بنجاح!', 'sarah-loz-theme')));
    }
    
    /**
     * Send welcome email to new subscribers
     */
    private function send_welcome_email($email) {
        $subject = __('مرحباً بك في النشرة البريدية', 'sarah-loz-theme');
        
        // Get site info
        $site_name = get_bloginfo('name');
        $site_url = get_site_url();
        
        // Build welcome message
        $content = '<h2>' . sprintf(__('مرحباً بك في النشرة البريدية لموقع %s', 'sarah-loz-theme'), $site_name) . '</h2>';
        $content .= '<p>' . __('شكراً لاشتراكك في نشرتنا البريدية! سنرسل لك آخر الأخبار والمستجدات والعروض الحصرية.', 'sarah-loz-theme') . '</p>';
        $content .= '<p>' . sprintf(__('يمكنك زيارة موقعنا <a href="%s">من هنا</a>.', 'sarah-loz-theme'), $site_url) . '</p>';
        $content .= '<p>' . __('مع تحيات فريق الموقع.', 'sarah-loz-theme') . '</p>';
        
        // Format and send the email
        $email_content = $this->format_email_content($subject, $content);
        $this->send_email($email, $subject, $email_content);
    }
    
    /**
     * Send newsletter
     */
    public function send_newsletter() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'swl_newsletter_nonce')) {
            wp_send_json_error(array('message' => __('فشلت عملية التحقق من الأمان', 'sarah-loz-theme')));
        }
        
        // Check user capability
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('ليس لديك صلاحيات كافية للقيام بهذا الإجراء', 'sarah-loz-theme')));
        }
        
        // Get form data
        $subject = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
        $content = isset($_POST['content']) ? wp_kses_post($_POST['content']) : '';
        $send_type = isset($_POST['send_type']) ? sanitize_text_field($_POST['send_type']) : 'all';
        
        if (empty($subject) || empty($content)) {
            wp_send_json_error(array('message' => __('يجب إدخال عنوان ومحتوى للنشرة البريدية', 'sarah-loz-theme')));
        }
        
        // Format email content
        $email_content = $this->format_email_content($subject, $content);
        
        // Test email
        if ($send_type === 'test') {
            $test_email = isset($_POST['test_email']) ? sanitize_email($_POST['test_email']) : '';
            
            if (!is_email($test_email)) {
                wp_send_json_error(array('message' => __('يرجى إدخال بريد إلكتروني صالح للاختبار', 'sarah-loz-theme')));
            }
            
            $sent = $this->send_email($test_email, $subject, $email_content);
            
            if ($sent) {
                wp_send_json_success(array('message' => sprintf(__('تم إرسال بريد اختباري إلى %s بنجاح', 'sarah-loz-theme'), $test_email)));
            } else {
                wp_send_json_error(array('message' => __('فشل إرسال البريد الاختباري', 'sarah-loz-theme')));
            }
        }
        // Send to all subscribers
        else {
            // Get all active subscribers
            $subscribers = get_posts(array(
                'post_type' => 'subscriber',
                'post_status' => 'publish',
                'posts_per_page' => -1,
            ));
            
            $success_count = 0;
            $fail_count = 0;
            
            if (empty($subscribers)) {
                wp_send_json_error(array('message' => __('لا يوجد مشتركين نشطين لإرسال النشرة البريدية', 'sarah-loz-theme')));
            }
            
            foreach ($subscribers as $subscriber) {
                $email = get_post_meta($subscriber->ID, 'email', true);
                
                if (empty($email) || !is_email($email)) {
                    $fail_count++;
                    continue;
                }
                
                $sent = $this->send_email($email, $subject, $email_content);
                
                if ($sent) {
                    $success_count++;
                } else {
                    $fail_count++;
                }
            }
            
            // Create record of this newsletter
            $newsletter_id = wp_insert_post(array(
                'post_title' => $subject,
                'post_type' => 'newsletter',
                'post_status' => 'publish',
                'post_content' => $content,
            ));
            
            if (!is_wp_error($newsletter_id)) {
                update_post_meta($newsletter_id, 'sent_date', current_time('mysql'));
                update_post_meta($newsletter_id, 'recipient_count', $success_count);
            }
            
            wp_send_json_success(array(
                'message' => sprintf(
                    __('تم إرسال النشرة البريدية بنجاح إلى %d مشتركين. فشل الإرسال إلى %d مشتركين.', 'sarah-loz-theme'),
                    $success_count,
                    $fail_count
                )
            ));
        }
    }
    
    /**
     * Format email content with header and footer
     */
    private function format_email_content($subject, $content) {
        $site_name = get_bloginfo('name');
        $site_url = get_site_url();
        $header_image = get_option('swl_newsletter_header_image');
        $footer_text = get_option('swl_newsletter_footer_text', sprintf(__('© %s %s - جميع الحقوق محفوظة', 'sarah-loz-theme'), date('Y'), $site_name));
        
        $html = '<!DOCTYPE html>
        <html dir="rtl">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . esc_html($subject) . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    margin: 0;
                    padding: 0;
                    background-color: #f5f5f5;
                    direction: rtl;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                }
                .header {
                    text-align: center;
                    padding: 20px;
                }
                .content {
                    padding: 20px;
                }
                .footer {
                    text-align: center;
                    padding: 20px;
                    background-color: #f9f9f9;
                    font-size: 12px;
                    color: #777;
                }
                a {
                    color: #0073aa;
                }
                .button {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #0073aa;
                    color: #ffffff !important;
                    text-decoration: none;
                    border-radius: 4px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">';
                
                if ($header_image) {
                    $html .= '<img src="' . esc_url($header_image) . '" alt="' . esc_attr($site_name) . '" style="max-width: 100%;">';
                } else {
                    $html .= '<h1>' . esc_html($site_name) . '</h1>';
                }
                
                $html .= '</div>
                <div class="content">' . $content . '</div>
                <div class="footer">
                    ' . wp_kses_post($footer_text) . '<br>
                    <a href="' . esc_url($site_url) . '">' . esc_html($site_name) . '</a>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Send email
     */
    private function send_email($to, $subject, $content) {
        $from_name = get_option('swl_newsletter_from_name', get_bloginfo('name'));
        $from_email = get_option('swl_newsletter_from_email', get_bloginfo('admin_email'));
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $from_name . ' <' . $from_email . '>',
        );
        
        return wp_mail($to, $subject, $content, $headers);
    }
    
    /**
     * Add bulk actions
     */
    public function register_bulk_actions($bulk_actions) {
        $bulk_actions['export_to_csv'] = __('Export to CSV', 'sarah-loz-theme');
        return $bulk_actions;
    }
    
    /**
     * Handle bulk actions
     */
    public function handle_bulk_actions($redirect_to, $action, $post_ids) {
        if ($action === 'export_to_csv') {
            $this->export_to_csv($post_ids);
            $redirect_to = add_query_arg('exported', count($post_ids), $redirect_to);
        }
        return $redirect_to;
    }
    
    /**
     * Export to CSV
     */
    private function export_to_csv($post_ids) {
        $subscribers = get_posts(array(
            'post_type' => 'subscriber',
            'post_status' => 'publish',
            'post__in' => $post_ids,
            'posts_per_page' => -1,
        ));
        
        $csv_data = array();
        $csv_data[] = array('البريد الإلكتروني', 'تاريخ الاشتراك');
        
        foreach ($subscribers as $subscriber) {
            $email = get_post_meta($subscriber->ID, 'email', true);
            $subscribe_date = get_post_meta($subscriber->ID, 'subscribe_date', true);
            $csv_data[] = array($email, $subscribe_date);
        }
        
        $csv_output = '';
        foreach ($csv_data as $row) {
            $csv_output .= implode(',', array_map('esc_html', $row)) . "\n";
        }
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="subscribers.csv"');
        echo $csv_output;
        exit;
    }
    
    /**
     * Add admin notices for bulk actions
     */
    public function bulk_action_admin_notice() {
        if (!empty($_REQUEST['exported'])) {
            $exported_count = intval($_REQUEST['exported']);
            echo '<div id="message" class="updated notice is-dismissible"><p>' . sprintf(__('تم تصدير %d مشتركين بنجاح', 'sarah-loz-theme'), $exported_count) . '</p></div>';
        }
    }
}

// Initialize the newsletter system
$swl_newsletter = new SWL_Newsletter(); 