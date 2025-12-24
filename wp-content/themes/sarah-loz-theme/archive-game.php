<?php 
// Check if we have an age group selected
$selected_age_group = sarah_loz_get_selected_age_group();
$current_age_data = sarah_loz_get_current_age_group_data();

get_header(); ?>

<div class="container mx-auto px-4 py-8">
    <header class="text-center mb-12">
        <?php if ($selected_age_group && $current_age_data) : ?>
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
                            ألعاب خاصة بـ<?php echo $current_age_data['label']; ?>!
                        </h1>
                        <p class="text-lg text-gray-600"><?php echo $selected_age_group; ?> سنوات</p>
                    </div>
                </div>
                <p class="text-lg text-gray-700"><?php echo $current_age_data['description']; ?></p>
            </div>
        <?php else : ?>
            <h1 class="text-4xl font-bold text-primary mb-4 animate-bounce-slow">الألعاب التعليمية</h1>
            <p class="text-xl text-gray-600">مجموعة متنوعة من الألعاب التعليمية الممتعة للأطفال</p>
        <?php endif; ?>
        
        <!-- Child-friendly decorative elements -->
        <div class="flex justify-center mt-4 space-x-4 rtl:space-x-reverse">
            <div class="text-4xl text-primary animate-float">🎮</div>
            <div class="text-4xl text-secondary animate-float-delayed">🧩</div>
            <div class="text-4xl text-primary animate-float-slow">🎯</div>
            <div class="text-4xl text-secondary animate-wiggle">🎨</div>
        </div>
    </header>

    <!-- Games Grid -->
    <?php
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $args = array(
        'post_type' => 'game',
        'paged' => $paged,
        'posts_per_page' => 12
    );

    // Add age filtering if age group is selected
    $meta_query = array();
    if ($selected_age_group) {
        $age_filter = sarah_loz_get_age_filter_meta_query($selected_age_group);
        if (!empty($age_filter)) {
            $meta_query[] = $age_filter;
        }
    }

    // Add taxonomy queries
    $tax_query = array();
    
    if (isset($_GET['age_group']) && !empty($_GET['age_group'])) {
        $tax_query[] = array(
            'taxonomy' => 'age_group',
            'field' => 'slug',
            'terms' => sanitize_text_field($_GET['age_group'])
        );
    }

    if (isset($_GET['skill']) && !empty($_GET['skill'])) {
        $tax_query[] = array(
            'taxonomy' => 'educational_skill',
            'field' => 'slug',
            'terms' => sanitize_text_field($_GET['skill'])
        );
    }

    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }

    // Add meta query for difficulty and age filtering
    if (isset($_GET['difficulty']) && !empty($_GET['difficulty'])) {
        $meta_query[] = array(
            'key' => 'difficulty_level',
            'value' => sanitize_text_field($_GET['difficulty']),
            'compare' => '='
        );
    }
    
    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }

    $games_query = new WP_Query($args);

    if ($games_query->have_posts()) : ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php while ($games_query->have_posts()) : $games_query->the_post(); ?>
                <article class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow transform hover:-translate-y-2 duration-300 border-2 border-primary/20">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="aspect-w-16 aspect-h-9 relative overflow-hidden">
                            <?php the_post_thumbnail('medium', ['class' => 'w-full h-full object-cover']); ?>
                            <div class="absolute inset-0 bg-gradient-to-t from-primary/30 to-transparent"></div>
                            <?php if ($current_age_data) : ?>
                                <div class="absolute top-2 right-2 bg-<?php echo $current_age_data['color']; ?> text-white px-2 py-1 rounded-full text-xs font-bold">
                                    مناسب لعمرك!
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="p-6">
                        <h2 class="text-2xl font-bold mb-3">
                            <a href="<?php the_permalink(); ?>" class="text-primary hover:text-secondary transition-colors">
                                <?php the_title(); ?>
                            </a>
                        </h2>
                        
                        <!-- Game Meta with child-friendly icons -->
                        <div class="flex flex-wrap gap-3 mb-4">
                            <?php if ($age_range = get_field('age_range')) : ?>
                                <span class="text-sm bg-primary/10 text-primary rounded-full px-3 py-1 flex items-center">
                                    <i class="fas fa-child ml-1"></i><?php echo esc_html($age_range); ?> سنوات
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($difficulty = get_field('difficulty_level')) : ?>
                                <span class="text-sm bg-secondary/10 text-secondary rounded-full px-3 py-1 flex items-center">
                                    <?php 
                                    $difficulty_icon = 'fa-star';
                                    if ($difficulty == 'easy') {
                                        $difficulty_text = 'سهل';
                                        $difficulty_icon = 'fa-smile';
                                    } elseif ($difficulty == 'medium') {
                                        $difficulty_text = 'متوسط';
                                        $difficulty_icon = 'fa-thumbs-up';
                                    } else {
                                        $difficulty_text = 'صعب';
                                        $difficulty_icon = 'fa-trophy';
                                    }
                                    ?>
                                    <i class="fas <?php echo $difficulty_icon; ?> ml-1"></i><?php echo esc_html($difficulty_text); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php 
                            $skills = get_the_terms(get_the_ID(), 'educational_skill');
                            if ($skills && !is_wp_error($skills)) : 
                                $skill = reset($skills); // Get the first skill
                            ?>
                                <span class="text-sm bg-primary/10 text-primary rounded-full px-3 py-1 flex items-center">
                                    <i class="fas fa-graduation-cap ml-1"></i><?php echo esc_html($skill->name); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <a href="<?php the_permalink(); ?>" class="inline-block bg-primary text-white px-5 py-2 rounded-full hover:bg-secondary transition-colors animate-pulse">
                            <i class="fas fa-gamepad ml-1"></i> العب الآن
                        </a>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <!-- Pagination with child-friendly styling -->
        <div class="mt-12 text-center">
            <?php
            $pagination = paginate_links(array(
                'total' => $games_query->max_num_pages,
                'prev_text' => '<i class="fas fa-chevron-right"></i>',
                'next_text' => '<i class="fas fa-chevron-left"></i>',
                'type' => 'array',
            ));
            
            if ($pagination) {
                echo '<div class="inline-flex bg-white p-2 rounded-full shadow-md">';
                foreach ($pagination as $page_link) {
                    $active = strpos($page_link, 'current') !== false;
                    $styled_link = str_replace(
                        ['page-numbers', 'current'], 
                        ['inline-flex items-center justify-center w-10 h-10 mx-1 rounded-full transition-colors ' . 
                         ($active ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-secondary/20')], 
                        $page_link
                    );
                    echo $styled_link;
                }
                echo '</div>';
            }
            ?>
        </div>
        <?php wp_reset_postdata();
    else : ?>
        <div class="text-center py-12 bg-white rounded-2xl shadow-lg p-8 max-w-2xl mx-auto">
            <div class="text-6xl mb-4 animate-bounce-slow">🎮</div>
            <h2 class="text-2xl font-bold text-primary mb-4">لا توجد ألعاب</h2>
            <p class="text-gray-600 mb-6">
                <?php if ($selected_age_group) : ?>
                    لا توجد ألعاب مناسبة لعمر <?php echo $selected_age_group; ?> سنوات حالياً. تحقق لاحقاً!
                <?php else : ?>
                    عذراً، لا توجد ألعاب متاحة حالياً بناءً على معايير البحث المحددة.
                <?php endif; ?>
            </p>
            <a href="<?php echo esc_url(get_post_type_archive_link('game')); ?>" class="inline-block bg-primary text-white px-6 py-3 rounded-full hover:bg-secondary transition-colors">
                <i class="fas fa-redo ml-2"></i> عرض جميع الألعاب
            </a>
        </div>
    <?php endif; ?>
    
    <!-- Decorative footer elements -->
    <div class="mt-16 flex justify-center">
        <div class="text-5xl text-primary/30 animate-spin-slow">🎡</div>
    </div>
</div>

<?php get_footer(); ?>
