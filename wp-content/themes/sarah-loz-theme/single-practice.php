<?php
/**
 * The template for displaying single practice pages
 */

// Check if we have an age group selected
$selected_age_group = sarah_loz_get_selected_age_group();
$current_age_data = sarah_loz_get_current_age_group_data();

get_header();
?>

<style>
    /* Mobile device styles for practices */
    @media (max-width: 768px) {
        .matching-practice {
            overflow-x: auto;
            padding-bottom: 20px;
        }
        
        .touch-draggable {
            -webkit-tap-highlight-color: transparent;
            touch-action: pan-x pan-y;
        }
        
        .touch-active {
            box-shadow: 0 0 0 2px #4299e1;
            transform: scale(1.05);
            transition: all 0.2s ease;
        }
        
        .rotation-notice {
            position: sticky;
            top: 0;
            z-index: 50;
        }
    }
</style>

<main class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <?php while (have_posts()) : the_post(); ?>
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
                                تمرين مناسب لـ<?php echo $current_age_data['label']; ?>!
                            </h2>
                            <p class="text-sm text-gray-600">مخصص للأطفال من عمر <?php echo $selected_age_group; ?> سنوات</p>
                            
                            <?php 
                            // Check if this practice matches the age group
                            $practice_age = get_field('age_range');
                            if ($practice_age && strpos($practice_age, $selected_age_group) !== false) : ?>
                                <span class="inline-block bg-<?php echo $current_age_data['color']; ?> text-white px-2 py-1 rounded-full text-xs font-bold mt-1">
                                    ✨ مناسب تماماً لعمرك!
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="mb-6">
                        <div class="flex flex-wrap items-center justify-between mb-4">
                            <h1 class="text-2xl font-bold"><?php the_title(); ?></h1>
                            
                            <?php
                            // Get practice type and difficulty
                            $practice_type = get_field('practice_type');
                            $difficulty = get_field('difficulty_level');
                            $practice_age = get_field('age_range');
                            
                            // Display badges
                            echo '<div class="flex flex-wrap gap-2">';
                            
                            if ($practice_type) {
                                $practice_type_label = $practice_type === 'multiple_choice' ? __('اختيار من متعدد', 'sarah-loz') : __('تمرين توصيل', 'sarah-loz');
                                echo '<span class="inline-block px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">' . $practice_type_label . '</span>';
                            }
                            
                            if ($difficulty) {
                                $difficulty_label = '';
                                $difficulty_class = '';
                                
                                switch ($difficulty) {
                                    case 'easy':
                                        $difficulty_label = __('سهل', 'sarah-loz');
                                        $difficulty_class = 'bg-green-100 text-green-800';
                                        break;
                                    case 'medium':
                                        $difficulty_label = __('متوسط', 'sarah-loz');
                                        $difficulty_class = 'bg-yellow-100 text-yellow-800';
                                        break;
                                    case 'hard':
                                        $difficulty_label = __('صعب', 'sarah-loz');
                                        $difficulty_class = 'bg-red-100 text-red-800';
                                        break;
                                }
                                
                                echo '<span class="inline-block px-3 py-1 text-sm rounded-full ' . $difficulty_class . '">' . $difficulty_label . '</span>';
                            }
                            
                            // Show age range badge
                            if ($practice_age) {
                                $age_class = ($current_age_data && strpos($practice_age, $selected_age_group) !== false) 
                                    ? 'bg-' . $current_age_data['color'] . '-100 text-' . $current_age_data['color'] . '-800' 
                                    : 'bg-gray-100 text-gray-800';
                                echo '<span class="inline-block px-3 py-1 text-sm rounded-full ' . $age_class . '">';
                                echo '<i class="fas fa-child ml-1"></i>' . esc_html($practice_age) . ' سنوات';
                                echo '</span>';
                            }
                            
                            // If this practice is related to a video or activity, show link
                            $related_content_id = get_field('related_content');
                            if ($related_content_id) {
                                $related_content_type = get_post_type($related_content_id);
                                $related_content_title = get_the_title($related_content_id);
                                $type_label = '';
                                
                                if ($related_content_type === 'video') {
                                    $type_label = __('فيديو', 'sarah-loz');
                                    $icon_class = 'fas fa-play-circle';
                                    $badge_class = 'bg-purple-100 text-purple-800';
                                } elseif ($related_content_type === 'activity') {
                                    $type_label = __('نشاط', 'sarah-loz');
                                    $icon_class = 'fas fa-paint-brush';
                                    $badge_class = 'bg-teal-100 text-teal-800';
                                }
                                
                                echo '<a href="' . get_permalink($related_content_id) . '" class="inline-block px-3 py-1 text-sm rounded-full ' . $badge_class . '">';
                                echo '<i class="' . $icon_class . ' mr-1"></i> ' . $type_label . ': ' . $related_content_title;
                                echo '</a>';
                            }
                            
                            echo '</div>'; // .flex
                            ?>
                        </div>

                        <?php if (has_post_thumbnail()) : ?>
                            <div class="mb-6">
                                <?php the_post_thumbnail('large', array('class' => 'w-full h-auto rounded-lg')); ?>
                            </div>
                        <?php endif; ?>

                        <div class="practice-description mb-8">
                            <?php the_content(); ?>
                        </div>
                        
                        <?php 
                        // Display the practice via shortcode
                        echo do_shortcode('[practice id="' . get_the_ID() . '"]'); 
                        ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</main>

<?php
get_footer();
?> 