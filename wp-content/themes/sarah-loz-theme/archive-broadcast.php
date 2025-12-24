<?php
/**
 * The template for displaying the broadcasts archive
 */

get_header(); 

/**
 * Helper function to check if a broadcast requires registration and user access
 */
function sarah_loz_broadcast_requires_login($post_id) {
    $requires_registration = get_field('broadcast_requires_registration', $post_id);
    
    if ($requires_registration && !is_user_logged_in()) {
        return true;
    }
    
    return false;
}

/**
 * Get the login URL (WooCommerce if available, WordPress as fallback)
 */
function sarah_loz_get_login_url($redirect_url = '') {
    if (empty($redirect_url)) {
        $redirect_url = get_permalink();
    }
    
    if (function_exists('wc_get_page_permalink')) {
        $my_account_url = wc_get_page_permalink('myaccount');
        return add_query_arg(array(
            'register' => 'true',
            'redirect_to' => urlencode($redirect_url),
        ), $my_account_url);
    } else {
        return wp_login_url($redirect_url);
    }
}
?>

<div class="container mx-auto px-4 py-8">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-primary via-dark to-secondary rounded-xl shadow-xl p-8 mb-8 text-white text-center relative overflow-hidden" style="
    background-color: cadetblue;">
        <!-- Decorative background elements -->
        <div class="absolute top-0 left-0 w-full h-full opacity-10">
            <div class="absolute top-0 left-0 w-32 h-32 bg-accent rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-40 h-40 bg-primary rounded-full translate-x-1/3 translate-y-1/3"></div>
            <div class="absolute top-1/2 right-1/4 w-24 h-24 bg-secondary rounded-full"></div>
        </div>
        
        <div class="relative z-10">
            <h1 class="text-4xl font-bold mb-4"><?php _e('البث المباشر والمجدول', 'sarah-loz'); ?></h1>
            <p class="text-xl opacity-90 max-w-3xl mx-auto mb-6"><?php _e('استمتع بمشاهدة البث المباشر والمجدول للأطفال مع محتوى تعليمي وترفيهي مميز', 'sarah-loz'); ?></p>
            
            <div class="flex flex-wrap justify-center gap-4">
                <a href="#live-now" class="bg-white bg-opacity-20 hover:bg-accent hover:text-dark transition-all duration-300 rounded-lg py-2 px-6 text-white font-medium">
                    <i class="fas fa-satellite-dish mr-2"></i><?php _e('البث المباشر الآن', 'sarah-loz'); ?>
                </a>
                <a href="#upcoming" class="bg-white bg-opacity-20 hover:bg-accent hover:text-dark transition-all duration-300 rounded-lg py-2 px-6 text-white font-medium">
                    <i class="fas fa-calendar-alt mr-2"></i><?php _e('البث المجدول', 'sarah-loz'); ?>
                </a>
                <a href="#previous" class="bg-white bg-opacity-20 hover:bg-accent hover:text-dark transition-all duration-300 rounded-lg py-2 px-6 text-white font-medium">
                    <i class="fas fa-history mr-2"></i><?php _e('البث السابق', 'sarah-loz'); ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Live Now Section -->
    <section id="live-now" class="mb-12">
        <h2 class="text-3xl font-bold mb-6"><?php _e('البث المباشر الآن', 'sarah-loz'); ?></h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <?php
            // Get live broadcasts
            $live_args = array(
                'post_type' => 'broadcast',
                'posts_per_page' => 3,
                'meta_query' => array(
                    array(
                        'key' => 'broadcast_type',
                        'value' => 'live',
                    ),
                ),
            );
            
            $live_broadcasts = new WP_Query($live_args);
            
            if ($live_broadcasts->have_posts()) :
                while ($live_broadcasts->have_posts()) : $live_broadcasts->the_post();
            ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="relative">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium_large', array('class' => 'w-full h-48 object-cover')); ?>
                        <?php else : ?>
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-tv text-5xl text-gray-400"></i>
                            </div>
                        <?php endif; ?>
                        <div class="absolute top-3 right-3 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold flex items-center">
                            <span class="animate-ping absolute inline-flex h-3 w-3 rounded-full bg-red-400 opacity-75 mr-1"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 mr-1"></span>
                            <?php _e('مباشر الآن', 'sarah-loz'); ?>
                        </div>
                        <?php if (sarah_loz_broadcast_requires_login(get_the_ID())) : ?>
                        <div class="absolute top-3 left-3 bg-gray-800 bg-opacity-70 text-white p-2 rounded-full">
                            <i class="fas fa-lock"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2"><?php the_title(); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                        <a href="<?php the_permalink(); ?>" class="inline-block bg-primary text-white py-2 px-4 rounded-lg hover:bg-opacity-90 transition">
                            <?php echo sarah_loz_broadcast_requires_login(get_the_ID()) ? __('تسجيل الدخول', 'sarah-loz') : __('مشاهدة البث', 'sarah-loz'); ?>
                        </a>
                    </div>
                </div>
            <?php
                endwhile;
                wp_reset_postdata();
            else:
            ?>
                <div class="col-span-1 lg:col-span-3 text-center py-12 bg-gray-50 rounded-lg">
                    <i class="fas fa-satellite-dish text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-500 mb-2"><?php _e('لا يوجد بث مباشر الآن', 'sarah-loz'); ?></h3>
                    <p class="text-gray-500"><?php _e('تحقق من البث المجدول القادم', 'sarah-loz'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Upcoming Broadcasts Section -->
    <section id="upcoming" class="mb-12">
        <h2 class="text-3xl font-bold mb-6"><?php _e('البث المجدول', 'sarah-loz'); ?></h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            // Get scheduled broadcasts
            $scheduled_args = array(
                'post_type' => 'broadcast',
                'posts_per_page' => 6,
                'meta_query' => array(
                    array(
                        'key' => 'broadcast_type',
                        'value' => 'scheduled',
                    ),
                ),
                'meta_key' => 'broadcast_scheduled_date',
                'orderby' => 'meta_value',
                'order' => 'ASC',
            );
            
            $scheduled_broadcasts = new WP_Query($scheduled_args);
            
            if ($scheduled_broadcasts->have_posts()) :
                while ($scheduled_broadcasts->have_posts()) : $scheduled_broadcasts->the_post();
                    $scheduled_date = get_field('broadcast_scheduled_date');
            ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="relative">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium_large', array('class' => 'w-full h-48 object-cover')); ?>
                        <?php else : ?>
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-calendar-alt text-5xl text-gray-400"></i>
                            </div>
                        <?php endif; ?>
                        <div class="absolute top-3 right-3 bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                            <?php _e('مجدول', 'sarah-loz'); ?>
                        </div>
                        <?php if (sarah_loz_broadcast_requires_login(get_the_ID())) : ?>
                        <div class="absolute top-3 left-3 bg-gray-800 bg-opacity-70 text-white p-2 rounded-full">
                            <i class="fas fa-lock"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2"><?php the_title(); ?></h3>
                        <?php if ($scheduled_date) : ?>
                            <p class="text-sm text-gray-600 mb-3">
                                <i class="far fa-clock mr-1"></i> 
                                <?php echo date_i18n('j F Y, g:i a', strtotime($scheduled_date)); ?>
                            </p>
                        <?php endif; ?>
                        <p class="text-gray-600 mb-4"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                        <a href="<?php the_permalink(); ?>" class="inline-block bg-secondary text-white py-2 px-4 rounded-lg hover:bg-opacity-90 transition">
                            <?php echo sarah_loz_broadcast_requires_login(get_the_ID()) ? __('تسجيل الدخول', 'sarah-loz') : __('تفاصيل البث', 'sarah-loz'); ?>
                        </a>
                    </div>
                </div>
            <?php
                endwhile;
                wp_reset_postdata();
            else:
            ?>
                <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-12 bg-gray-50 rounded-lg">
                    <i class="fas fa-calendar-alt text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-500 mb-2"><?php _e('لا يوجد بث مجدول حالياً', 'sarah-loz'); ?></h3>
                    <p class="text-gray-500"><?php _e('تحقق من البث السابق', 'sarah-loz'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Previous Broadcasts Section -->
    <section id="previous" class="mb-12">
        <h2 class="text-3xl font-bold mb-6"><?php _e('البث السابق', 'sarah-loz'); ?></h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            // Get recorded broadcasts
            $recorded_args = array(
                'post_type' => 'broadcast',
                'posts_per_page' => 8,
                'meta_query' => array(
                    array(
                        'key' => 'broadcast_type',
                        'value' => 'recorded',
                    ),
                ),
                'orderby' => 'date',
                'order' => 'DESC',
            );
            
            $recorded_broadcasts = new WP_Query($recorded_args);
            
            if ($recorded_broadcasts->have_posts()) :
                while ($recorded_broadcasts->have_posts()) : $recorded_broadcasts->the_post();
            ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="relative">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium', array('class' => 'w-full h-40 object-cover')); ?>
                        <?php else : ?>
                            <div class="w-full h-40 bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-video text-4xl text-gray-400"></i>
                            </div>
                        <?php endif; ?>
                        <div class="absolute top-3 right-3 bg-gray-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                            <?php _e('مسجل', 'sarah-loz'); ?>
                        </div>
                        <?php if (sarah_loz_broadcast_requires_login(get_the_ID())) : ?>
                        <div class="absolute top-3 left-3 bg-gray-800 bg-opacity-70 text-white p-2 rounded-full">
                            <i class="fas fa-lock"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-bold mb-2"><?php the_title(); ?></h3>
                        <p class="text-gray-600 mb-3 text-sm"><?php echo wp_trim_words(get_the_excerpt(), 10); ?></p>
                        <a href="<?php the_permalink(); ?>" class="inline-block <?php echo sarah_loz_broadcast_requires_login(get_the_ID()) ? 'bg-gray-500 text-white' : 'bg-gray-200 text-gray-700'; ?> py-1 px-3 rounded-lg hover:bg-gray-300 transition text-sm">
                            <?php echo sarah_loz_broadcast_requires_login(get_the_ID()) ? __('تسجيل الدخول', 'sarah-loz') : __('مشاهدة', 'sarah-loz'); ?>
                        </a>
                    </div>
                </div>
            <?php
                endwhile;
                wp_reset_postdata();
            else:
            ?>
                <div class="col-span-1 md:col-span-2 lg:col-span-4 text-center py-12 bg-gray-50 rounded-lg">
                    <i class="fas fa-video text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-500 mb-2"><?php _e('لا يوجد بث سابق', 'sarah-loz'); ?></h3>
                    <p class="text-gray-500"><?php _e('ستظهر هنا تسجيلات البث المباشر السابق', 'sarah-loz'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <?php
        // Check if we have more recorded broadcasts than the initial display
        $total_recorded = $recorded_broadcasts->found_posts;
        if ($total_recorded > 8) :
        ?>
        <div class="text-center mt-8">
            <a href="<?php echo esc_url(add_query_arg('broadcast_type', 'recorded', get_post_type_archive_link('broadcast'))); ?>" class="inline-block bg-primary text-white py-2 px-6 rounded-lg hover:bg-opacity-90 transition">
                <?php _e('عرض المزيد من التسجيلات', 'sarah-loz'); ?>
            </a>
        </div>
        <?php endif; ?>
    </section>
</div>

<?php get_footer(); ?> 