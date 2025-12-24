<?php
/**
 * Template Name: Age Group Content
 * Description: A template to display content filtered by age group
 *
 * @package Sarah_Loz
 */

get_header();

// Get the age group from the URL parameter
$age_group = isset($_GET['age_group']) ? sanitize_text_field($_GET['age_group']) : '';

// If no age group is specified, use the page content to display all age groups
$show_all = empty($age_group);

// Define age group labels and ranges
$age_groups = array(
    '3-5' => array(
        'label' => 'الأصدقاء الصغار',
        'icon' => '🦁',
        'color' => 'primary',
    ),
    '6-7' => array(
        'label' => 'المستكشفون',
        'icon' => '🦊',
        'color' => 'secondary',
    ),
    '8-9' => array(
        'label' => 'الأبطال المتقدمون',
        'icon' => '🦉',
        'color' => 'accent',
    ),
);

// Current age group data
$current_age_group = isset($age_groups[$age_group]) ? $age_groups[$age_group] : null;
?>

<main class="container mx-auto px-4 py-12">
    <?php if ($show_all) : ?>
        <!-- Display all age groups if no specific age group is selected -->
        <header class="text-center mb-12">
            <h1 class="text-4xl font-bold text-dark mb-4"><?php the_title(); ?></h1>
            <div class="max-w-3xl mx-auto">
                <?php the_content(); ?>
            </div>
        </header>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <?php foreach ($age_groups as $range => $group) : ?>
                <div class="bg-gradient-to-br from-<?php echo $group['color']; ?>/10 to-white rounded-3xl p-8 text-center hover:shadow-xl transition-all transform hover:-translate-y-2 cursor-pointer">
                    <div class="w-32 h-32 bg-<?php echo $group['color']; ?>/20 rounded-full flex flex-col items-center justify-center mx-auto mb-6">
                        <span class="text-4xl mb-2"><?php echo $group['icon']; ?></span>
                        <span class="text-2xl font-bold text-<?php echo $group['color']; ?>"><?php echo $range; ?></span>
                    </div>
                    <h3 class="text-2xl font-bold mb-4"><?php echo $group['label']; ?></h3>
                    <a href="<?php echo add_query_arg('age_group', $range, get_permalink()); ?>" 
                       class="inline-block bg-<?php echo $group['color']; ?> text-white px-8 py-4 rounded-full text-xl hover:bg-opacity-90 transition-colors transform hover:scale-105">
                        استكشف المحتوى
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <!-- Display content for specific age group -->
        <header class="mb-12">
            <div class="flex items-center mb-6">
                <a href="<?php echo remove_query_arg('age_group', get_permalink()); ?>" class="text-primary hover:underline ml-4">
                    <i class="fas fa-arrow-right ml-2"></i>
                    <?php _e('العودة إلى جميع الفئات العمرية', 'sarah-loz'); ?>
                </a>
            </div>
            
            <div class="flex flex-col md:flex-row items-center md:justify-between">
                <div class="flex items-center mb-4 md:mb-0">
                    <div class="w-16 h-16 bg-<?php echo $current_age_group['color']; ?>/20 rounded-full flex items-center justify-center ml-4">
                        <span class="text-3xl"><?php echo $current_age_group['icon']; ?></span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-dark">
                            <?php echo $current_age_group['label']; ?>
                            <span class="text-<?php echo $current_age_group['color']; ?>">(<?php echo $age_group; ?> سنوات)</span>
                        </h1>
                        <p class="text-gray-600"><?php _e('محتوى تعليمي وترفيهي مخصص لهذه الفئة العمرية', 'sarah-loz'); ?></p>
                    </div>
                </div>
                
                <div class="inline-flex rounded-md shadow-sm" role="group">
                    <button type="button" class="age-group-tab active py-2 px-4 text-sm font-medium text-gray-900 bg-white rounded-r-lg border border-gray-200 hover:bg-gray-100 focus:z-10 focus:text-primary" data-tab="all">
                        <?php _e('الكل', 'sarah-loz'); ?>
                    </button>
                    <button type="button" class="age-group-tab py-2 px-4 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 focus:z-10 focus:text-primary" data-tab="games">
                        <?php _e('الألعاب', 'sarah-loz'); ?>
                    </button>
                    <button type="button" class="age-group-tab py-2 px-4 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 focus:z-10 focus:text-primary" data-tab="activities">
                        <?php _e('الأنشطة', 'sarah-loz'); ?>
                    </button>
                    <button type="button" class="age-group-tab py-2 px-4 text-sm font-medium text-gray-900 bg-white rounded-l-lg border border-gray-200 hover:bg-gray-100 focus:z-10 focus:text-primary" data-tab="videos">
                        <?php _e('الفيديوهات', 'sarah-loz'); ?>
                    </button>
                </div>
            </div>
        </header>
        
        <?php
        // Query arguments for all content types with the specified age group
        $age_query_args = array(
            'meta_query' => sarah_loz_get_age_filter_meta_query($age_group),
            'posts_per_page' => 6,
        );
        
        // Query for games
        $games_args = array_merge($age_query_args, array('post_type' => 'game'));
        $games_query = new WP_Query($games_args);
        
        // Query for activities
        $activities_args = array_merge($age_query_args, array('post_type' => 'activity'));
        $activities_query = new WP_Query($activities_args);
        
        // Query for videos
        $videos_args = array_merge($age_query_args, array('post_type' => 'video'));
        $videos_query = new WP_Query($videos_args);
        
        // Calculate total count
        $total_count = $games_query->found_posts + $activities_query->found_posts + $videos_query->found_posts;
        ?>
        
        <!-- Content Tabs -->
        <div class="mb-8">
            <div class="age-group-tab-content" id="all-tab">
                <?php if ($total_count > 0) : ?>
                    <!-- Games Section -->
                    <?php if ($games_query->have_posts()) : ?>
                        <div class="mb-12">
                            <h2 class="text-2xl font-bold text-dark mb-6">
                                <span class="border-b-4 border-<?php echo $current_age_group['color']; ?> pb-2">
                                    <i class="fas fa-gamepad ml-2"></i>
                                    <?php _e('الألعاب', 'sarah-loz'); ?>
                                </span>
                            </h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php while ($games_query->have_posts()) : $games_query->the_post(); ?>
                                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <div class="aspect-w-16 aspect-h-9">
                                                <?php the_post_thumbnail('medium', ['class' => 'w-full h-full object-cover']); ?>
                                            </div>
                                        <?php else : ?>
                                            <div class="aspect-w-16 aspect-h-9 bg-<?php echo $current_age_group['color']; ?>/10 flex items-center justify-center">
                                                <i class="fas fa-gamepad text-6xl text-<?php echo $current_age_group['color']; ?>/40"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="p-6">
                                            <h3 class="text-xl font-bold mb-3">
                                                <a href="<?php the_permalink(); ?>" class="text-dark hover:text-<?php echo $current_age_group['color']; ?> transition-colors">
                                                    <?php the_title(); ?>
                                                </a>
                                            </h3>
                                            
                                            <a href="<?php the_permalink(); ?>" class="inline-block bg-<?php echo $current_age_group['color']; ?> text-white px-4 py-2 rounded-full hover:bg-opacity-90 transition-colors">
                                                <?php _e('العب الآن', 'sarah-loz'); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endwhile; wp_reset_postdata(); ?>
                            </div>
                            
                            <div class="text-center mt-6">
                                <a href="<?php echo add_query_arg(array('age_group' => $age_group), get_post_type_archive_link('game')); ?>" class="inline-block text-<?php echo $current_age_group['color']; ?> hover:underline">
                                    <?php _e('عرض جميع الألعاب', 'sarah-loz'); ?> <i class="fas fa-arrow-left mr-1"></i>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Activities Section -->
                    <?php if ($activities_query->have_posts()) : ?>
                        <div class="mb-12">
                            <h2 class="text-2xl font-bold text-dark mb-6">
                                <span class="border-b-4 border-<?php echo $current_age_group['color']; ?> pb-2">
                                    <i class="fas fa-paint-brush ml-2"></i>
                                    <?php _e('الأنشطة', 'sarah-loz'); ?>
                                </span>
                            </h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php while ($activities_query->have_posts()) : $activities_query->the_post(); ?>
                                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <div class="aspect-w-16 aspect-h-9">
                                                <?php the_post_thumbnail('medium', ['class' => 'w-full h-full object-cover']); ?>
                                            </div>
                                        <?php else : ?>
                                            <div class="aspect-w-16 aspect-h-9 bg-<?php echo $current_age_group['color']; ?>/10 flex items-center justify-center">
                                                <i class="fas fa-paint-brush text-6xl text-<?php echo $current_age_group['color']; ?>/40"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="p-6">
                                            <h3 class="text-xl font-bold mb-3">
                                                <a href="<?php the_permalink(); ?>" class="text-dark hover:text-<?php echo $current_age_group['color']; ?> transition-colors">
                                                    <?php the_title(); ?>
                                                </a>
                                            </h3>
                                            
                                            <a href="<?php the_permalink(); ?>" class="inline-block bg-<?php echo $current_age_group['color']; ?> text-white px-4 py-2 rounded-full hover:bg-opacity-90 transition-colors">
                                                <?php _e('شاهد النشاط', 'sarah-loz'); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endwhile; wp_reset_postdata(); ?>
                            </div>
                            
                            <div class="text-center mt-6">
                                <a href="<?php echo add_query_arg(array('age_group' => $age_group), get_post_type_archive_link('activity')); ?>" class="inline-block text-<?php echo $current_age_group['color']; ?> hover:underline">
                                    <?php _e('عرض جميع الأنشطة', 'sarah-loz'); ?> <i class="fas fa-arrow-left mr-1"></i>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Videos Section -->
                    <?php if ($videos_query->have_posts()) : ?>
                        <div class="mb-12">
                            <h2 class="text-2xl font-bold text-dark mb-6">
                                <span class="border-b-4 border-<?php echo $current_age_group['color']; ?> pb-2">
                                    <i class="fas fa-play-circle ml-2"></i>
                                    <?php _e('الفيديوهات', 'sarah-loz'); ?>
                                </span>
                            </h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php while ($videos_query->have_posts()) : $videos_query->the_post(); ?>
                                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <div class="aspect-w-16 aspect-h-9 relative">
                                                <?php the_post_thumbnail('medium', ['class' => 'w-full h-full object-cover']); ?>
                                                <div class="absolute inset-0 flex items-center justify-center">
                                                    <div class="w-16 h-16 bg-<?php echo $current_age_group['color']; ?>/80 rounded-full flex items-center justify-center">
                                                        <i class="fas fa-play text-white text-2xl"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else : ?>
                                            <div class="aspect-w-16 aspect-h-9 bg-<?php echo $current_age_group['color']; ?>/10 flex items-center justify-center">
                                                <i class="fas fa-play-circle text-6xl text-<?php echo $current_age_group['color']; ?>/40"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="p-6">
                                            <h3 class="text-xl font-bold mb-3">
                                                <a href="<?php the_permalink(); ?>" class="text-dark hover:text-<?php echo $current_age_group['color']; ?> transition-colors">
                                                    <?php the_title(); ?>
                                                </a>
                                            </h3>
                                            
                                            <a href="<?php the_permalink(); ?>" class="inline-block bg-<?php echo $current_age_group['color']; ?> text-white px-4 py-2 rounded-full hover:bg-opacity-90 transition-colors">
                                                <?php _e('شاهد الفيديو', 'sarah-loz'); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endwhile; wp_reset_postdata(); ?>
                            </div>
                            
                            <div class="text-center mt-6">
                                <a href="<?php echo add_query_arg(array('age_group' => $age_group), get_post_type_archive_link('video')); ?>" class="inline-block text-<?php echo $current_age_group['color']; ?> hover:underline">
                                    <?php _e('عرض جميع الفيديوهات', 'sarah-loz'); ?> <i class="fas fa-arrow-left mr-1"></i>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="bg-yellow-100 text-yellow-800 p-6 rounded-lg text-center">
                        <i class="fas fa-exclamation-circle text-4xl mb-4"></i>
                        <h3 class="text-xl font-bold mb-2"><?php _e('لا يوجد محتوى', 'sarah-loz'); ?></h3>
                        <p><?php _e('لم يتم العثور على محتوى لهذه الفئة العمرية.', 'sarah-loz'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Games Tab Content -->
            <div class="age-group-tab-content hidden" id="games-tab">
                <?php if ($games_query->have_posts()) : ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php while ($games_query->have_posts()) : $games_query->the_post(); ?>
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="aspect-w-16 aspect-h-9">
                                        <?php the_post_thumbnail('medium', ['class' => 'w-full h-full object-cover']); ?>
                                    </div>
                                <?php else : ?>
                                    <div class="aspect-w-16 aspect-h-9 bg-<?php echo $current_age_group['color']; ?>/10 flex items-center justify-center">
                                        <i class="fas fa-gamepad text-6xl text-<?php echo $current_age_group['color']; ?>/40"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="p-6">
                                    <h3 class="text-xl font-bold mb-3">
                                        <a href="<?php the_permalink(); ?>" class="text-dark hover:text-<?php echo $current_age_group['color']; ?> transition-colors">
                                            <?php the_title(); ?>
                                        </a>
                                    </h3>
                                    
                                    <a href="<?php the_permalink(); ?>" class="inline-block bg-<?php echo $current_age_group['color']; ?> text-white px-4 py-2 rounded-full hover:bg-opacity-90 transition-colors">
                                        <?php _e('العب الآن', 'sarah-loz'); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                    
                    <div class="text-center mt-6">
                        <a href="<?php echo add_query_arg(array('age_group' => $age_group), get_post_type_archive_link('game')); ?>" class="inline-block text-<?php echo $current_age_group['color']; ?> hover:underline">
                            <?php _e('عرض جميع الألعاب', 'sarah-loz'); ?> <i class="fas fa-arrow-left mr-1"></i>
                        </a>
                    </div>
                <?php else : ?>
                    <div class="bg-yellow-100 text-yellow-800 p-6 rounded-lg text-center">
                        <i class="fas fa-exclamation-circle text-4xl mb-4"></i>
                        <h3 class="text-xl font-bold mb-2"><?php _e('لا توجد ألعاب', 'sarah-loz'); ?></h3>
                        <p><?php _e('لم يتم العثور على ألعاب لهذه الفئة العمرية.', 'sarah-loz'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Activities Tab Content -->
            <div class="age-group-tab-content hidden" id="activities-tab">
                <?php if ($activities_query->have_posts()) : ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php while ($activities_query->have_posts()) : $activities_query->the_post(); ?>
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="aspect-w-16 aspect-h-9">
                                        <?php the_post_thumbnail('medium', ['class' => 'w-full h-full object-cover']); ?>
                                    </div>
                                <?php else : ?>
                                    <div class="aspect-w-16 aspect-h-9 bg-<?php echo $current_age_group['color']; ?>/10 flex items-center justify-center">
                                        <i class="fas fa-paint-brush text-6xl text-<?php echo $current_age_group['color']; ?>/40"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="p-6">
                                    <h3 class="text-xl font-bold mb-3">
                                        <a href="<?php the_permalink(); ?>" class="text-dark hover:text-<?php echo $current_age_group['color']; ?> transition-colors">
                                            <?php the_title(); ?>
                                        </a>
                                    </h3>
                                    
                                    <a href="<?php the_permalink(); ?>" class="inline-block bg-<?php echo $current_age_group['color']; ?> text-white px-4 py-2 rounded-full hover:bg-opacity-90 transition-colors">
                                        <?php _e('شاهد النشاط', 'sarah-loz'); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                    
                    <div class="text-center mt-6">
                        <a href="<?php echo add_query_arg(array('age_group' => $age_group), get_post_type_archive_link('activity')); ?>" class="inline-block text-<?php echo $current_age_group['color']; ?> hover:underline">
                            <?php _e('عرض جميع الأنشطة', 'sarah-loz'); ?> <i class="fas fa-arrow-left mr-1"></i>
                        </a>
                    </div>
                <?php else : ?>
                    <div class="bg-yellow-100 text-yellow-800 p-6 rounded-lg text-center">
                        <i class="fas fa-exclamation-circle text-4xl mb-4"></i>
                        <h3 class="text-xl font-bold mb-2"><?php _e('لا توجد أنشطة', 'sarah-loz'); ?></h3>
                        <p><?php _e('لم يتم العثور على أنشطة لهذه الفئة العمرية.', 'sarah-loz'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Videos Tab Content -->
            <div class="age-group-tab-content hidden" id="videos-tab">
                <?php if ($videos_query->have_posts()) : ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php while ($videos_query->have_posts()) : $videos_query->the_post(); ?>
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="aspect-w-16 aspect-h-9 relative">
                                        <?php the_post_thumbnail('medium', ['class' => 'w-full h-full object-cover']); ?>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <div class="w-16 h-16 bg-<?php echo $current_age_group['color']; ?>/80 rounded-full flex items-center justify-center">
                                                <i class="fas fa-play text-white text-2xl"></i>
                                            </div>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <div class="aspect-w-16 aspect-h-9 bg-<?php echo $current_age_group['color']; ?>/10 flex items-center justify-center">
                                        <i class="fas fa-play-circle text-6xl text-<?php echo $current_age_group['color']; ?>/40"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="p-6">
                                    <h3 class="text-xl font-bold mb-3">
                                        <a href="<?php the_permalink(); ?>" class="text-dark hover:text-<?php echo $current_age_group['color']; ?> transition-colors">
                                            <?php the_title(); ?>
                                        </a>
                                    </h3>
                                    
                                    <a href="<?php the_permalink(); ?>" class="inline-block bg-<?php echo $current_age_group['color']; ?> text-white px-4 py-2 rounded-full hover:bg-opacity-90 transition-colors">
                                        <?php _e('شاهد الفيديو', 'sarah-loz'); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                    
                    <div class="text-center mt-6">
                        <a href="<?php echo add_query_arg(array('age_group' => $age_group), get_post_type_archive_link('video')); ?>" class="inline-block text-<?php echo $current_age_group['color']; ?> hover:underline">
                            <?php _e('عرض جميع الفيديوهات', 'sarah-loz'); ?> <i class="fas fa-arrow-left mr-1"></i>
                        </a>
                    </div>
                <?php else : ?>
                    <div class="bg-yellow-100 text-yellow-800 p-6 rounded-lg text-center">
                        <i class="fas fa-exclamation-circle text-4xl mb-4"></i>
                        <h3 class="text-xl font-bold mb-2"><?php _e('لا توجد فيديوهات', 'sarah-loz'); ?></h3>
                        <p><?php _e('لم يتم العثور على فيديوهات لهذه الفئة العمرية.', 'sarah-loz'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</main>

<script>
jQuery(document).ready(function($) {
    // Tab switching functionality
    $('.age-group-tab').on('click', function() {
        const tab = $(this).data('tab');
        
        // Update active tab
        $('.age-group-tab').removeClass('active text-primary');
        $(this).addClass('active text-primary');
        
        // Show selected tab content
        $('.age-group-tab-content').addClass('hidden');
        $(`#${tab}-tab`).removeClass('hidden');
    });
});
</script>

<?php get_footer(); ?> 