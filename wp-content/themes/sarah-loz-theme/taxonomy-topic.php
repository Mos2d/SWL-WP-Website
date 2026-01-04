<?php
/**
 * Template for displaying topic archives
 * File: taxonomy-topic.php
 */

get_header(); 

// 1. Get Setup Variables
// ---------------------
$selected_age_group = function_exists('sarah_loz_get_selected_age_group') ? sarah_loz_get_selected_age_group() : null;
$current_age_data = function_exists('sarah_loz_get_current_age_group_data') ? sarah_loz_get_current_age_group_data() : null;

// Get current topic object
$current_topic = get_queried_object();
$topic_image = get_term_meta($current_topic->term_id, 'topic_image', true);
$topic_color = get_term_meta($current_topic->term_id, 'topic_color', true) ?: '#007cba';
$topic_description = get_term_meta($current_topic->term_id, 'topic_description', true);
?>

<div class="topic-archive-page">
    <div class="container">
        
        <div class="topic-header" style="background: white; padding: 40px; border-radius: 15px; margin-bottom: 40px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border-top: 5px solid <?php echo esc_attr($topic_color); ?>;">
            <div class="topic-info-row" style="display: flex; align-items: center; gap: 30px;">
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
                        <div class="age-indicator" style="display: inline-block; padding: 10px 20px; background: #e3f2fd; border-radius: 25px;">
                            <span style="color: #1976d2; font-weight: 500; font-size: 1.1rem;">
                                <i class="fas fa-child" style="margin-right: 8px;"></i>
                                <?php echo esc_html($current_age_data['label'] ?? ''); ?> - <?php echo esc_html($selected_age_group); ?> سنوات
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="content-filter-tabs" style="margin-bottom: 30px;">
            <div class="filter-tabs" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button class="filter-tab active" data-content-type="all">
                    <?php _e('الكل', 'sarah-loz'); ?>
                </button>
                
                <button class="filter-tab" data-content-type="vocabulary"><?php _e('المفردات', 'sarah-loz'); ?></button>
                <button class="filter-tab" data-content-type="video"><?php _e('فيديو', 'sarah-loz'); ?></button>
                <button class="filter-tab" data-content-type="game"><?php _e('ألعاب', 'sarah-loz'); ?></button>
                <button class="filter-tab" data-content-type="activity"><?php _e('أنشطة', 'sarah-loz'); ?></button>
                <button class="filter-tab" data-content-type="theater"><?php _e('مسرحيات', 'sarah-loz'); ?></button>
                <button class="filter-tab" data-content-type="practice"><?php _e('تدريبات', 'sarah-loz'); ?></button>
            </div>
        </div>

        <div id="vocabulary-section" style="display:none; margin-bottom: 50px;">
            <?php 
            $vocab_items = get_posts(array(
                'post_type' => 'vocabulary',
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'topic',
                        'field' => 'term_id',
                        'terms' => $current_topic->term_id,
                    ),
                ),
            ));
            
            if ($vocab_items) : ?>
                <div class="flashcard-container-wrapper">
                    <button id="prev-card" class="nav-btn right"><i class="dashicons dashicons-arrow-right-alt2"></i></button>
                    
                    <div class="flashcard-scene">
                        <?php foreach($vocab_items as $index => $item): 
                            // GET NEW FIELDS
                            $audio_url = get_field('vocab_audio', $item->ID);
                            $front_image = get_field('vocab_front_image', $item->ID);
                            $back_image = get_field('vocab_back_image', $item->ID);
                            $phonetic = get_field('vocab_phonetic', $item->ID);
                        ?>
                        <div class="flashcard <?php echo $index === 0 ? 'active' : ''; ?>" 
                             data-index="<?php echo $index; ?>" 
                             data-audio="<?php echo esc_url($audio_url); ?>">
                            
                            <div class="flashcard-inner">
                                <div class="flashcard-front">
                                    <div class="audio-icon"><i class="dashicons dashicons-controls-volumeon"></i></div>
                                    
                                    <?php if($front_image): ?>
                                        <div class="front-image-wrapper" style="max-width:80%; height:150px; margin: 10px auto;">
                                            <img src="<?php echo esc_url($front_image); ?>" alt="Word Text" style="width:100%; height:100%; object-fit:contain;">
                                        </div>
                                    <?php else: ?>
                                        <h2 class="word-title"><?php echo esc_html($item->post_title); ?></h2>
                                    <?php endif; ?>

                                    <?php if($phonetic): ?><p class="phonetic"><?php echo esc_html($phonetic); ?></p><?php endif; ?>
                                    <span class="tap-hint"><?php _e('اضغط للاستماع', 'sarah-loz'); ?></span>
                                </div>
                                
                                <div class="flashcard-back">
                                    <?php if($back_image): ?>
                                        <img src="<?php echo esc_url($back_image); ?>" alt="<?php echo esc_attr($item->post_title); ?>">
                                    <?php endif; ?>
                                    <div class="word-label"><?php echo esc_html($item->post_title); ?></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <button id="next-card" class="nav-btn left"><i class="dashicons dashicons-arrow-left-alt2"></i></button>
                </div>
                
                <audio id="vocab-player" style="display:none;"></audio>
                
                <div class="card-counter">
                    <span id="current-card-num">1</span> / <?php echo count($vocab_items); ?>
                </div>

            <?php else: ?>
                <div class="no-content-message" style="text-align: center; padding: 40px; color: #666;">
                    <p><?php _e('لا توجد مفردات مضافة لهذا الموضوع بعد.', 'sarah-loz'); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <div id="content-grid" class="content-grid">
            <?php
            // Fetch all other content types
            $content_items = get_topic_content_with_age_filtering($current_topic->term_id, $selected_age_group);
            
            if (!empty($content_items)) : 
                foreach ($content_items as $item) : 
                    // Skip vocabulary in the main grid (shown above)
                    if($item->post_type === 'vocabulary') continue;
                    
                    $item_type = $item->post_type;
                    $item_link = get_permalink($item->ID);
                    $item_image = get_the_post_thumbnail_url($item->ID, 'medium');
                    $item_excerpt = $item->post_excerpt ?: wp_trim_words($item->post_content, 20);
                    $age_range = get_post_meta($item->ID, 'age_range', true);
                    ?>
                    
                    <div class="content-item" data-content-type="<?php echo esc_attr($item_type); ?>" style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                        <div class="item-header" style="display: flex; gap: 20px; margin-bottom: 20px;">
                            <?php if ($item_image) : ?>
                                <div class="item-image" style="flex-shrink: 0; width: 80px; height: 80px; border-radius: 8px; overflow: hidden; background: #f0f0f0;">
                                    <img src="<?php echo esc_url($item_image); ?>" alt="<?php echo esc_attr($item->post_title); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                            
                            <div class="item-info" style="flex: 1;">
                                <div class="item-type-badge" style="display: inline-block; padding: 4px 12px; background: <?php echo esc_attr($topic_color); ?>; color: white; border-radius: 20px; font-size: 0.8rem; font-weight: 500; margin-bottom: 8px;">
                                    <?php echo get_post_type_label_arabic($item_type); ?>
                                </div>
                                
                                <h3 class="item-title" style="margin: 0 0 10px 0; font-size: 1.3rem; font-weight: 600;">
                                    <a href="<?php echo esc_url($item_link); ?>" style="color: #333; text-decoration: none;">
                                        <?php echo esc_html($item->post_title); ?>
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
                            
                            <a href="<?php echo esc_url($item_link); ?>" class="btn btn-primary" style="display: inline-block; padding: 10px 20px; background: <?php echo esc_attr($topic_color); ?>; color: white; text-decoration: none; border-radius: 6px; font-weight: 500;">
                                <?php _e('عرض', 'sarah-loz'); ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="no-content" style="grid-column: 1/-1; text-align: center; padding: 60px 20px; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <h3 style="color: #666; margin-bottom: 15px;"><?php _e('لا يوجد محتوى في هذا الموضوع حالياً', 'sarah-loz'); ?></h3>
                    <a href="<?php echo esc_url(home_url('/topics/')); ?>" class="btn" style="background: #6c757d; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">
                        <?php _e('العودة إلى الموضوعات', 'sarah-loz'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
/**
 * 6. Helper Functions
 * -------------------
 */

// Function to fetch content with age filtering
function get_topic_content_with_age_filtering($topic_id, $selected_age_group) {
    $args = array(
        'post_type' => array('game', 'activity', 'video', 'theater', 'practice', 'broadcast', 'product', 'vocabulary'),
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
    
    // Apply age filter if selected
    if ($selected_age_group && function_exists('sarah_loz_get_age_filter_meta_query')) {
        $args['meta_query'] = sarah_loz_get_age_filter_meta_query($selected_age_group);
    } elseif ($selected_age_group) {
        // Fallback simplified meta query
        $args['meta_query'] = array(
            'relation' => 'OR',
            array('key' => 'age_range', 'value' => 'all', 'compare' => '='),
            array('key' => 'age_range', 'value' => $selected_age_group, 'compare' => 'LIKE'),
            array('key' => 'age_range', 'compare' => 'NOT EXISTS')
        );
    }
    
    return get_posts($args);
}

// Function to get Arabic labels
function get_post_type_label_arabic($post_type) {
    $labels = array(
        'video' => 'فيديو',
        'activity' => 'نشاط',
        'game' => 'لعبة',
        'theater' => 'مسرحية',
        'practice' => 'تدريب',
        'broadcast' => 'بث مباشر',
        'product' => 'منتج',
        'vocabulary' => 'مفردات'
    );
    return isset($labels[$post_type]) ? $labels[$post_type] : ucfirst($post_type);
}
?>

<style>
/* Basic Page Layout */
.topic-archive-page { padding: 40px 0; background: #f8f9fa; min-height: 80vh; }
.container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
.content-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 25px; }
@media (max-width: 768px) { .content-grid { grid-template-columns: 1fr; } .topic-info-row { flex-direction: column; text-align: center; } }

/* Tabs */
.filter-tab { padding: 12px 24px; background: #fff; color: #333; border: 1px solid #eee; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.2s; }
.filter-tab:hover { background: #f0f0f0; }
.filter-tab.active { background: #007cba; color: white; border-color: #007cba; }

/* Flashcard Wrapper */
.flashcard-container-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    margin-top: 40px;
    margin-bottom: 20px;
}

/* 3D Scene */
.flashcard-scene {
    width: 320px;
    height: 420px;
    perspective: 1000px;
    position: relative;
}

/* The Card */
.flashcard {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    display: none;
    cursor: pointer;
}
.flashcard.active { display: block; }

.flashcard-inner {
    position: relative;
    width: 100%;
    height: 100%;
    text-align: center;
    transition: transform 0.6s;
    transform-style: preserve-3d;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    border-radius: 20px;
}

/* Flip State */
.flashcard.flipped .flashcard-inner {
    transform: rotateY(180deg);
}

/* Front & Back */
.flashcard-front, .flashcard-back {
    position: absolute;
    width: 100%;
    height: 100%;
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: white;
    overflow: hidden;
}

/* Front Design */
.flashcard-front {
    border: 3px solid #007cba;
    color: #333;
}
.word-title {
    font-size: 2.8rem;
    color: #007cba;
    margin: 15px 0;
    font-weight: bold;
}
.phonetic {
    font-size: 1.2rem;
    color: #888;
    margin-bottom: 20px;
    font-family: monospace;
}
.audio-icon {
    font-size: 48px;
    color: #eee;
    margin-bottom: 10px;
}
.tap-hint {
    font-size: 0.9rem;
    color: #aaa;
    margin-top: 30px;
}

/* Back Design */
.flashcard-back {
    transform: rotateY(180deg);
    border: 3px solid #007cba;
    padding: 0;
}
.flashcard-back img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.word-label {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(255,255,255,0.9);
    padding: 15px;
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
    border-top: 1px solid #eee;
}

/* Buttons */
.nav-btn {
    background: white;
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    cursor: pointer;
    font-size: 24px;
    color: #555;
    transition: 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}
.nav-btn:hover { background: #007cba; color: white; transform: scale(1.1); }
.card-counter { text-align: center; font-size: 1.2rem; color: #777; font-weight: bold; }
</style>

<script>
jQuery(document).ready(function($) {
    
    // 1. TABS SWITCHING LOGIC
    $('.filter-tab').click(function() {
        var type = $(this).data('content-type');
        
        // Visuals
        $('.filter-tab').removeClass('active').css({'background':'#fff', 'color':'#333', 'border-color':'#eee'});
        
        if(type === 'vocabulary') {
            $(this).addClass('active').css({'background':'#007cba', 'color':'white', 'border-color':'#007cba'});
            $('#content-grid').fadeOut(200, function(){
                $('#vocabulary-section').fadeIn(200);
            });
        } else {
            $(this).addClass('active').css({'background':'#007cba', 'color':'white', 'border-color':'#007cba'});
            $('#vocabulary-section').fadeOut(200, function(){
                $('#content-grid').fadeIn(200);
            });
            
            // Filter grid items
            if(type === 'all') {
                $('.content-item').fadeIn();
            } else {
                $('.content-item').hide();
                $('.content-item[data-content-type="'+type+'"]').fadeIn();
            }
        }
    });

    // 2. FLASHCARDS INTERACTION
    var cards = $('.flashcard');
    var currentIndex = 0;
    var audioPlayer = document.getElementById('vocab-player');
    var navButtons = $('.nav-btn'); // Select both buttons

    // CHECK: Disable buttons if only 1 card exists
    if (cards.length <= 1) {
        navButtons.prop('disabled', true)
                  .css({'opacity': '0.3', 'cursor': 'default', 'pointer-events': 'none'});
    }

    function showCard(index) {
        cards.removeClass('active flipped').hide();
        cards.eq(index).fadeIn().addClass('active');
        $('#current-card-num').text(index + 1);
    }

    // Navigation
    $('#next-card').click(function() {
        if (cards.length <= 1) return; // Extra safety check

        if (currentIndex < cards.length - 1) currentIndex++;
        else currentIndex = 0;
        showCard(currentIndex);
    });

    $('#prev-card').click(function() {
        if (cards.length <= 1) return; // Extra safety check

        if (currentIndex > 0) currentIndex--;
        else currentIndex = cards.length - 1;
        showCard(currentIndex);
    });

    // Card Click (Flip & Audio)
    $('.flashcard').click(function() {
        var card = $(this);
        var audioSrc = card.data('audio');
        
        // Flip
        card.toggleClass('flipped');
        
        // Play Audio
        if(audioSrc && audioPlayer) {
            audioPlayer.src = audioSrc;
            // Reset and play
            audioPlayer.pause();
            audioPlayer.currentTime = 0;
            var playPromise = audioPlayer.play();
            if (playPromise !== undefined) {
                playPromise.catch(error => { console.log("Audio play prevented"); });
            }
        }
    });
});
</script>

<?php get_footer(); ?>