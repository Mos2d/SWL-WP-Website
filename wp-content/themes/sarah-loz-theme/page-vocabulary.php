<?php
/**
 * Template Name: Vocabulary Dictionary
 * Description: Displays all vocabulary flashcards sorted by alphabet tabs (ignoring "Al-").
 */

get_header(); 

// --- 1. Helper Function for Arabic Sorting ---
function sarah_loz_get_first_letter($text) {
    if (empty($text)) return '#';
    
    // Clean whitespace
    $text = trim($text);
    
    // Remove "Al-" (ال) if it exists at the start
    // Note: Checking for distinct Alef-Lam. 
    // We check for 'ال' (Alef + Lam)
    if (mb_substr($text, 0, 2) === 'ال') {
        $text = mb_substr($text, 2);
    }
    
    // Get the first letter
    $first_letter = mb_substr($text, 0, 1);
    
    // Normalize Alefs (Unify أ, إ, آ, ا into just "أ")
    $alefs = array('أ', 'إ', 'آ', 'ا');
    if (in_array($first_letter, $alefs)) {
        return 'أ';
    }
    
    return $first_letter;
}

// --- 2. Fetch All Vocabulary ---
$vocab_posts = get_posts(array(
    'post_type'      => 'vocabulary',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC'
));

// --- 3. Group Posts by Letter ---
$grouped_vocab = array();
$letters = array();

foreach ($vocab_posts as $post) {
    $title = $post->post_title;
    // You can swap this to use the ACF 'vocab_front_text' if preferred:
    // $front_text = get_field('vocab_front_text', $post->ID);
    // $title = $front_text ? $front_text : $post->post_title;
    
    $letter = sarah_loz_get_first_letter($title);
    
    // Add to group
    $grouped_vocab[$letter][] = $post;
    
    // Track unique letters for tabs
    if (!in_array($letter, $letters)) {
        $letters[] = $letter;
    }
}

// Sort the letters array alphabetically
sort($letters);
?>

<div class="vocab-dictionary-page">
    <div class="vocab-container">
        
        <div class="dictionary-header">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <div class="page-intro">
                    <?php the_content(); ?>
                </div>
            <?php endwhile; endif; ?>
        </div>

        <div class="tabs-container-wrapper">
            <div class="alphabet-tabs" id="alphabetTabs">
                <button class="vocab-tab active" data-letter="all">الكل</button>
                <?php foreach ($letters as $letter) : ?>
                    <button class="vocab-tab" data-letter="<?php echo esc_attr($letter); ?>">
                        <?php echo esc_html($letter); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="vocab-controls">
            <button id="reset-cards" class="action-btn">
                <i class="dashicons dashicons-image-rotate"></i> <?php _e('إعادة قلب البطاقات', 'sarah-loz'); ?>
            </button>
        </div>

        <div class="vocabulary-grid">
            <?php 
            // Loop through groups to render items
            foreach ($grouped_vocab as $letter => $posts) : 
                foreach ($posts as $item) :
                    // Get ACF Fields
                    $audio_url = get_field('vocab_audio', $item->ID);
                    $back_image = get_field('vocab_back_image', $item->ID);   
                    $phonetic = get_field('vocab_phonetic', $item->ID);
                    $front_text = get_field('vocab_front_text', $item->ID);
                    $display_text = $front_text ? $front_text : $item->post_title;
                    // --- LOGIC TO COLOR FIRST LETTER (Ignoring Al-) ---
                    $display_text = trim($display_text);
                    $final_html = '';
                    
                    if (mb_substr($display_text, 0, 2, 'UTF-8') === 'ال') {
                        $part_al   = mb_substr($display_text, 0, 2, 'UTF-8');
                        $part_char = mb_substr($display_text, 2, 1, 'UTF-8');
                        $part_rest = mb_substr($display_text, 3, null, 'UTF-8');
                        
                        $final_html = esc_html($part_al) . '<span class="vocab-highlight">' . esc_html($part_char) . '</span>' . esc_html($part_rest);
                    } else {
                        $part_char = mb_substr($display_text, 0, 1, 'UTF-8');
                        $part_rest = mb_substr($display_text, 1, null, 'UTF-8');
                        
                        $final_html = '<span class="vocab-highlight">' . esc_html($part_char) . '</span>' . esc_html($part_rest);
                    }
            ?>
                    <div class="vocab-card-wrapper" data-letter="<?php echo esc_attr($letter); ?>">
                        <div class="flashcard" data-audio="<?php echo esc_url($audio_url); ?>">
                            <div class="flashcard-inner">
                                <div class="flashcard-front">
                                    <h2 class="cartoon-word"><?php echo $final_html; ?></h2>
                                    <?php if($phonetic): ?><p class="phonetic"><?php echo esc_html($phonetic); ?></p><?php endif; ?>
                                </div>
                                <div class="flashcard-back">
                                    <?php if($back_image): ?>
                                        <img src="<?php echo esc_url($back_image); ?>" alt="<?php echo esc_attr($item->post_title); ?>">
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php 
                endforeach; 
            endforeach; 
            
            if (empty($grouped_vocab)) : ?>
                <div class="no-content-message">
                    <p><?php _e('لا توجد كلمات مضافة بعد.', 'sarah-loz'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <audio id="vocab-player" style="display:none;"></audio>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Lalezar&display=swap');
/* Page Layout */
.vocab-dictionary-page {
    background: #f4f7f6;
    min-height: 100vh;
    padding: 10px 0;
}
.vocab-container{
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}
.dictionary-header {
    text-align: center;
    margin-bottom: 40px;
}
.dictionary-header h1 {
    font-family: 'Lalezar', cursive;
    color: #007cba;
    font-size: 3rem;
    margin-bottom: 15px;
}
.page-intro {
    font-size: 1.2rem;
    color: #666;
    max-width: 800px;
    margin: 0 auto;
}

/* --- TABS DESIGN (Folder Style) --- */
.tabs-container-wrapper {
    background: #fff;
    padding: 15px 20px 1px 20px;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    overflow-x: auto; /* Scrollable on mobile */
    overflow-y: hidden;
}

.alphabet-tabs {
    display: flex;
    gap: 8px;
    justify-content: center; /* Center tabs on large screens */
    flex-wrap: wrap;         /* Wrap on large screens */
    min-width: min-content;  /* Ensure scroll works on mobile */
}

.vocab-tab {
    padding: 10px 20px;
    background: #f0f0f0;
    border: 2px solid #e0e0e0;
    border-bottom: none; /* Folder tab look */
    border-radius: 10px 10px 0 0;
    color: #555;
    font-size: 1.2rem;
    font-family: 'Lalezar', cursive;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 50px;
    position: relative;
    top: 2px; /* Connect with border */
}

.vocab-tab:hover {
    background: #e3f2fd;
    color: #007cba;
}

.vocab-tab.active {
    background: #007cba;
    color: white;
    border-color: #007cba;
    top: 0;
    padding-bottom: 12px; /* Make it look "popped up" */
    box-shadow: 0 -2px 5px rgba(0,124,186,0.2);
    z-index: 2;
}

/* Mobile: Force single line scroll */
@media (max-width: 768px) {
    .alphabet-tabs {
        flex-wrap: nowrap;
        justify-content: flex-start;
        padding-bottom: 5px;
    }
}

/* Controls */
.vocab-controls {
    text-align: left;
    margin-bottom: 20px;
}
.action-btn {
    background: #007cba;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 20px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.action-btn:hover { background: #5a6268; }

/* Grid & Cards (Reused from Topic Page) */
.vocabulary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 60px;
    justify-content: center;
    padding-bottom: 60px;
}

.vocab-card-wrapper {
    width: 100%;
    max-width: 340px;
    height: 420px; 
    perspective: 1000px;
    margin: 0 auto;
}

/* Flip Animation */
.flashcard {
    width: 100%;
    height: 100%;
    position: relative;
    transform-style: preserve-3d;
    transition: transform 0.6s;
    cursor: pointer;
    display: block; /* Always visible in grid */
}

.flashcard-inner {
    position: relative;
    width: 100%;
    height: 100%;
    text-align: center;
    transition: transform 0.6s;
    transform-style: preserve-3d;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    border-radius: 20px;
}
.flashcard.flipped .flashcard-inner {
    transform: rotateY(180deg);
}
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
    border: 3px solid #007cba;
    padding: 15px;
    overflow: hidden;
}
/* Front Design */
.flashcard-front {
    border: 3px solid #007cba;
    color: #333;
    justify-content: center;
}
.cartoon-word {
    /* Use Lalezar for thick cartoon look */
    font-family: 'Lalezar', cursive !important;
    font-size: 6rem;
    font-weight: 400 !important;
    color: #007cba;
    margin: 0;
    line-height: 1.1;
}
h2.cartoon-word .vocab-highlight {
    color: #e74c3c !important; /* Nice Red */
}
.phonetic {
    font-size: 1.2rem;
    color: #888;
    margin-bottom: 10px;
}
.flashcard-back {
    transform: rotateY(180deg);
    border: 3px solid #007cba;
    padding: 0;
    justify-content: flex-start; 
    background: white;
}
.flashcard-back img {
    width: 100%;
    height: auto;
    object-fit: contain; 
    object-position: top center;
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

/* Animation: Hidden State */
.vocab-card-wrapper.hidden {
    display: none;
}

/* Responsive Tweaks */
@media (max-width: 480px) {
    .vocabulary-grid {
        grid-template-columns: 1fr;
    }
    .vocab-card-wrapper {
        height: 320px;
    }
    .cartoon-word { font-size: 4rem; }
}
</style>

<script>
jQuery(document).ready(function($) {
    
    // 1. Tab Filtering Logic
    $('.vocab-tab').click(function() {
        var letter = $(this).data('letter');
        
        // Active State
        $('.vocab-tab').removeClass('active');
        $(this).addClass('active');
        
        // Filter Items
        if (letter === 'all') {
            $('.vocab-card-wrapper').fadeIn();
        } else {
            $('.vocab-card-wrapper').hide(); // Hide all
            // Show only matching data-letter
            $('.vocab-card-wrapper[data-letter="' + letter + '"]').fadeIn();
        }
    });

    // 2. Audio & Flip Logic (Exact same as Topic Page)
    var audioPlayer = document.getElementById('vocab-player');

    $('.flashcard').click(function() {
        var card = $(this);
        var audioSrc = card.data('audio');
        
        // Flip
        card.toggleClass('flipped');
        
        // Play Audio
        if(audioSrc && audioPlayer) {
            audioPlayer.src = audioSrc;
            audioPlayer.pause();
            audioPlayer.currentTime = 0;
            var playPromise = audioPlayer.play();
            if (playPromise !== undefined) {
                playPromise.catch(error => { console.log("Audio play prevented"); });
            }
        }
    });

    // 3. Reset Button
    $('#reset-cards').click(function() {
        $('.flashcard').removeClass('flipped');
        if(audioPlayer) {
            audioPlayer.pause();
            audioPlayer.currentTime = 0;
        }
    });
});
</script>

<?php get_footer(); ?>