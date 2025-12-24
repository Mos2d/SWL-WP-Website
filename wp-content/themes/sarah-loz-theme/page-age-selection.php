<?php
/**
 * Template Name: Age Selection
 * Description: Age group selection page that appears before the main content
 *
 * @package Sarah_Loz
 */

// Start session if not already started
if (!session_id()) {
    session_start();
}

// Check if request is from a bot/crawler and redirect to home page for sharing purposes
if (function_exists('sarah_loz_is_bot_or_crawler') && sarah_loz_is_bot_or_crawler()) {
    wp_redirect(home_url());
    exit;
}

// Check if age group is already selected and redirect if so
if (isset($_SESSION['swl_selected_age_group']) || isset($_COOKIE['swl_selected_age_group'])) {
    $redirect_url = isset($_GET['redirect_to']) ? esc_url_raw(urldecode($_GET['redirect_to'])) : home_url();
    wp_redirect($redirect_url);
    exit;
}

// Get age groups data
$age_groups = sarah_loz_get_age_groups();

get_header(); 
?>

<!-- Add enhanced animations and styles -->
<style>
    body {
        overflow-x: hidden;
    }
    
    /* Enhanced floating animation for age selection */
    @keyframes magical-float {
        0% { transform: translateY(0px) rotate(0deg) scale(1); }
        25% { transform: translateY(-8px) rotate(1deg) scale(1.02); }
        50% { transform: translateY(-15px) rotate(0deg) scale(1.05); }
        75% { transform: translateY(-8px) rotate(-1deg) scale(1.02); }
        100% { transform: translateY(0px) rotate(0deg) scale(1); }
    }
    
    /* Sparkle animation */
    @keyframes sparkle {
        0% { opacity: 0; transform: scale(0); }
        50% { opacity: 1; transform: scale(1); }
        100% { opacity: 0; transform: scale(0); }
    }
    
    /* Rainbow border animation - slower */
    @keyframes rainbow-border {
        0% { border-color: #ff6b6b; }
        16% { border-color: #ffa726; }
        33% { border-color: #ffee58; }
        50% { border-color: #66bb6a; }
        66% { border-color: #42a5f5; }
        83% { border-color: #ab47bc; }
        100% { border-color: #ff6b6b; }
    }
    
    /* Age group card animations */
    .age-card {
        animation: magical-float 6s ease-in-out infinite;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .age-card:nth-child(1) { animation-delay: 0s; }
    .age-card:nth-child(2) { animation-delay: 2s; }
    .age-card:nth-child(3) { animation-delay: 4s; }
    
    .age-card:hover {
        transform: translateY(-20px) scale(1.05) !important;
        animation-play-state: paused;
    }
    
    .sparkle-element {
        animation: sparkle 2s ease-in-out infinite;
        animation-delay: var(--delay, 0s);
    }
    
    .rainbow-border {
        animation: rainbow-border 8s linear infinite;
    }
    
    /* Character bounce animation - very gentle movement */
    @keyframes character-bounce {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-4px) scale(1.02); }
    }
    
    .character-bounce {
        animation: character-bounce 4s ease-in-out infinite;
    }
    
    .character-icon {
        animation: character-bounce 4s ease-in-out infinite;
    }
    
    /* Background animation */
    @keyframes background-shift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    .animated-background {
        background: linear-gradient(-45deg, #ff9aa2, #ffdac1, #e2f0cb, #b5ead7, #c7ceea);
        background-size: 400% 400%;
        animation: background-shift 15s ease infinite;
    }
</style>

<main class="min-h-screen animated-background relative overflow-hidden">
    <!-- Floating decorative elements -->
    <div class="absolute inset-0 pointer-events-none">
        <!-- Stars -->
        <div class="absolute top-10 left-10 w-8 h-8 sparkle-element" style="--delay: 0s;">⭐</div>
        <div class="absolute top-20 right-20 w-6 h-6 sparkle-element" style="--delay: 0.5s;">✨</div>
        <div class="absolute bottom-20 left-20 w-10 h-10 sparkle-element" style="--delay: 1s;">🌟</div>
        <div class="absolute bottom-10 right-10 w-8 h-8 sparkle-element" style="--delay: 1.5s;">💫</div>
        
        <!-- Floating shapes -->
        <div class="absolute top-1/4 left-5 w-16 h-16 bg-yellow-300 rounded-full opacity-30 animate-ping" style="animation-duration: 4s;"></div>
        <div class="absolute bottom-1/4 right-5 w-20 h-20 bg-pink-300 rounded-full opacity-30 animate-ping" style="animation-duration: 5s;"></div>
        <div class="absolute top-1/2 left-10 w-12 h-12 bg-blue-300 rounded-full opacity-30 animate-ping" style="animation-duration: 3s;"></div>
        
        <!-- Cloud shapes -->
        <div class="absolute top-0 left-1/4 w-32 h-16 bg-white/50 rounded-full blur-sm"></div>
        <div class="absolute top-5 left-1/3 w-24 h-12 bg-white/50 rounded-full blur-sm"></div>
        <div class="absolute bottom-0 right-1/4 w-40 h-20 bg-white/50 rounded-full blur-sm"></div>
        <div class="absolute bottom-5 right-1/3 w-28 h-14 bg-white/50 rounded-full blur-sm"></div>
    </div>

    <div class="container mx-auto px-4 py-8 relative z-10">
        <!-- Header Section -->
        <div class="text-center mb-16">
             <div class="inline-block bg-green-500 rounded-3xl p-8 mb-8 shadow-2xl">
                 <h1 class="text-4xl md:text-6xl font-bold text-white mb-4 drop-shadow-lg">
                     مرحباً بك في عالم سارة ولوز!
                 </h1>
                 <p class="text-2xl md:text-3xl text-white mb-6 drop-shadow">
                     اختر عمرك لنبدأ المغامرة معاً! 🚀
                 </p>
                 <!-- Character icons removed as requested -->
             </div>
        </div>

        <!-- Age Selection Cards -->
        <div class="grid grid-cols-1 md:grid-cols-<?php echo count($age_groups); ?> gap-8 max-w-6xl mx-auto">
            
            <?php 
            $card_index = 1;
            $color_classes = array(
                'primary' => array('gradient' => 'from-pink-200 to-pink-300', 'button' => 'from-pink-400 to-pink-600', 'bg' => 'bg-pink-500'),
                'secondary' => array('gradient' => 'from-blue-200 to-blue-300', 'button' => 'from-blue-400 to-blue-600', 'bg' => 'bg-blue-500'),
                'accent' => array('gradient' => 'from-purple-200 to-purple-300', 'button' => 'from-purple-400 to-purple-600', 'bg' => 'bg-purple-500'),
            );
            
            foreach ($age_groups as $age_range => $group_data) :
                $color_class = isset($color_classes[$group_data['color']]) ? $color_classes[$group_data['color']] : $color_classes['primary'];
                $activities = array();
                
                // Set activities based on age group
                switch ($age_range) {
                    case '3-5':
                        $activities = array(
                            array('icon' => '🎮', 'text' => 'ألعاب بسيطة وآمنة'),
                            array('icon' => '🎨', 'text' => 'رسم وتلوين'),
                            array('icon' => '🎵', 'text' => 'أغاني وموسيقى'),
                        );
                        $button_emoji = '🌟';
                        break;
                    case '6-7':
                        $activities = array(
                            array('icon' => '🧩', 'text' => 'ألعاب ذكاء'),
                            array('icon' => '📚', 'text' => 'قصص تفاعلية'),
                            array('icon' => '🔬', 'text' => 'تجارب علمية'),
                        );
                        $button_emoji = '🚀';
                        break;
                    case '8-9':
                        $activities = array(
                            array('icon' => '🎯', 'text' => 'ألعاب استراتيجية'),
                            array('icon' => '💻', 'text' => 'برمجة للأطفال'),
                            array('icon' => '🏅', 'text' => 'تحديات متقدمة'),
                        );
                        $button_emoji = '🎖️';
                        break;
                    default:
                        $activities = array(
                            array('icon' => '🎯', 'text' => 'أنشطة ممتعة'),
                            array('icon' => '📚', 'text' => 'تعلم تفاعلي'),
                            array('icon' => '🎨', 'text' => 'إبداع وفن'),
                        );
                        $button_emoji = '✨';
                        break;
                }
            ?>
            
            <!-- Age Group <?php echo esc_html($age_range); ?> Years -->
            <div class="age-card bg-white/90 backdrop-blur-sm rounded-3xl p-8 text-center shadow-2xl cursor-pointer border-8 rainbow-border hover:bg-white transition-all"
                 onclick="selectAgeGroup('<?php echo esc_attr($age_range); ?>')">
                <div class="relative mb-8">
                    <!-- Character container -->
                    <div class="w-32 h-32 bg-gradient-to-br <?php echo $color_class['gradient']; ?> rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                                                 <?php if (isset($group_data['custom_image'])) : ?>
                             <img src="<?php echo esc_url($group_data['custom_image']); ?>" 
                                  alt="<?php echo esc_attr($group_data['label']); ?>" 
                                  class="w-full h-full object-cover rounded-full character-bounce" 
                                  style="animation-delay: <?php echo ($card_index - 1) * 0.5; ?>s;">
                         <?php else : ?>
                             <span class="text-6xl character-bounce" style="animation-delay: <?php echo ($card_index - 1) * 0.5; ?>s;">
                                 <?php echo esc_html($group_data['icon']); ?>
                             </span>
                         <?php endif; ?>
                    </div>
                    <!-- Age badge -->
                    <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 <?php echo $color_class['bg']; ?> text-white px-4 py-2 rounded-full font-bold text-xl shadow-lg">
                        <?php echo esc_html($age_range); ?> سنوات
                    </div>
                </div>
                
                <h3 class="text-3xl font-bold text-<?php echo $group_data['color']; ?> mb-4">
                    <?php echo esc_html($group_data['label']); ?>
                </h3>
                <p class="text-xl text-gray-700 mb-6">
                    <?php echo esc_html($group_data['description']); ?>
                </p>
                
                <ul class="text-lg text-gray-600 space-y-3 mb-6">
                    <?php foreach ($activities as $activity) : ?>
                    <li class="flex items-center justify-center gap-2">
                        <span class="text-2xl"><?php echo esc_html($activity['icon']); ?></span>
                        <span><?php echo esc_html($activity['text']); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                
                <button class="bg-gradient-to-r <?php echo $color_class['button']; ?> text-white px-8 py-4 rounded-full text-xl font-bold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all">
                    هذا عمري! <?php echo esc_html($button_emoji); ?>
                </button>
                
                <!-- Fallback form for non-JavaScript users -->
                <form method="post" action="<?php echo admin_url('admin-ajax.php'); ?>" style="display: none;" class="fallback-form">
                    <input type="hidden" name="action" value="set_age_group">
                    <input type="hidden" name="age_group" value="<?php echo esc_attr($age_range); ?>">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('set_age_group_nonce'); ?>">
                    <input type="hidden" name="redirect_to" value="<?php echo isset($_GET['redirect_to']) ? esc_url(urldecode($_GET['redirect_to'])) : home_url(); ?>">
                </form>
            </div>

            <?php 
            $card_index++;
            endforeach; ?>
        </div>

        <!-- Call to Action -->
        <div class="text-center mt-16">
            <div class="bg-green-500 rounded-2xl p-6 max-w-2xl mx-auto shadow-xl">
                <p class="text-xl text-white mb-4">
                    💡 اختر الفئة العمرية التي تناسبك لنعرض لك المحتوى المناسب!
                </p>
                <p class="text-lg text-white/90">
                    يمكنك تغيير اختيارك في أي وقت من الإعدادات 🔧
                </p>
            </div>
        </div>
    </div>

    <!-- Loading overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center z-50 hidden">
        <div class="text-center text-white">
            <div class="animate-pulse mb-8">
                <img src="http://sarawalauze.com/wp-content/uploads/2025/04/cropped-SWL.png" 
                     alt="Sarah & Luz" 
                     class="h-32 w-auto mx-auto">
            </div>
            <h2 class="text-4xl font-bold mb-4">جاري التحضير...</h2>
            <p class="text-2xl">نحضر لك المحتوى المناسب لعمرك!</p>
        </div>
    </div>
</main>

<script>
// Prevent form resubmission
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}

function selectAgeGroup(ageGroup) {
    // Show loading overlay
    document.getElementById('loading-overlay').classList.remove('hidden');
    
    // Set the age group in session and cookie
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'set_age_group',
            age_group: ageGroup,
            nonce: '<?php echo wp_create_nonce('set_age_group_nonce'); ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Set cookie for persistence
            document.cookie = `swl_selected_age_group=${ageGroup}; path=/; max-age=${30 * 24 * 60 * 60}`; // 30 days
            
            // Redirect after a short delay
            setTimeout(() => {
                const redirectUrl = new URLSearchParams(window.location.search).get('redirect_to') 
                    ? decodeURIComponent(new URLSearchParams(window.location.search).get('redirect_to'))
                    : '<?php echo home_url(); ?>';
                window.location.href = redirectUrl;
            }, 2000);
        } else {
            console.error('Failed to set age group:', data);
            document.getElementById('loading-overlay').classList.add('hidden');
            alert('حدث خطأ. يرجى المحاولة مرة أخرى.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loading-overlay').classList.add('hidden');
        alert('حدث خطأ. يرجى المحاولة مرة أخرى.');
    });
}



// Add sound effect on hover (optional)
document.querySelectorAll('.age-card').forEach(card => {
    card.addEventListener('mouseenter', () => {
        // You can add a subtle sound effect here if needed
        card.style.transform = 'translateY(-10px) scale(1.05)';
    });
    
    card.addEventListener('mouseleave', () => {
        card.style.transform = '';
    });
});
</script>

<?php get_footer(); ?> 