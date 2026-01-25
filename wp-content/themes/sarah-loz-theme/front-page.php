<?php 
// Check if we have an age group selected
$selected_age_group = sarah_loz_get_selected_age_group();
$current_age_data = sarah_loz_get_current_age_group_data();

get_header(); ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

<style>
    /* --- NEW: Background Patterns & Colors --- */
    .bg-pastel-purple { background-color: #F3E8FF; }
    .bg-pastel-orange { background-color: #FFF7ED; }
    .bg-pastel-blue { background-color: #EBF8FF; }
    .bg-pastel-green { background-color: #F0FDF4; }
    .bg-pastel-pink { background-color: #FFF1F2; }

    .pattern-dots {
        background-color: #ffffff;
        background-image: radial-gradient(#e5e7eb 2px, transparent 2px);
        background-size: 30px 30px;
    }

    .pattern-grid {
        background-color: #ffffff;
        background-image: linear-gradient(#f0f0f0 1px, transparent 1px), linear-gradient(90deg, #f0f0f0 1px, transparent 1px);
        background-size: 40px 40px;
    }

    /* --- NEW: Wave Separators --- */
    .section-separator {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        overflow: hidden;
        line-height: 0;
        transform: rotate(180deg);
        z-index: 1;
        pointer-events: none; /* Prevents wave from blocking clicks */
    }

    .section-separator svg {
        position: relative;
        display: block;
        width: calc(100% + 1.3px);
        height: 80px; /* Adjustable height for the wave */
    }

    .section-separator-up {
        transform: rotate(0deg) !important;
        bottom: -1px; /* Ensures it sits perfectly on the next section */
    }

    .section-separator-top {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        overflow: hidden;
        line-height: 0;
        z-index: 1;
        pointer-events: none;
    }
    
    .section-separator-top svg {
        position: relative;
        display: block;
        width: calc(100% + 1.3px);
        height: 80px;
    }

    @media (min-width: 768px) {
        .section-separator svg, .section-separator-top svg {
            height: 120px;
        }
    }

    /* --- EXISTING ANIMATIONS --- */
    /* Enhanced floating animation */
    @keyframes floating {
        0% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(5deg); }
        100% { transform: translateY(0px) rotate(0deg); }
    }
    
    /* Slow spin animation */
    @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Delayed float animation */
    @keyframes float-delayed {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }
    
    /* Reverse float animation */
    @keyframes float-reverse {
        0% { transform: translateY(0px); }
        50% { transform: translateY(15px); }
        100% { transform: translateY(0px); }
    }
    
    /* Shooting star animation */
    @keyframes shooting-star {
        0% { transform: translate(0, 0) scale(0.5); opacity: 0; }
        50% { transform: translate(-100px, 100px) scale(1); opacity: 1; }
        100% { transform: translate(-200px, 200px) scale(0.5); opacity: 0; }
    }
    
    /* Delayed shooting star */
    @keyframes shooting-star-delayed {
        0% { transform: translate(0, 0) scale(0.5); opacity: 0; }
        50% { transform: translate(100px, 100px) scale(1); opacity: 1; }
        100% { transform: translate(200px, 200px) scale(0.5); opacity: 0; }
    }
    
    /* Glow pulse animation */
    @keyframes pulse-glow {
        0% { transform: scale(1); opacity: 0.7; }
        50% { transform: scale(1.5); opacity: 1; }
        100% { transform: scale(1); opacity: 0.7; }
    }
    
    /* Gradient shift animation */
    @keyframes gradient-shift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    /* Slow bounce animation */
    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }
    
    /* Apply animations */
    .floating {
        animation: floating 6s ease-in-out infinite;
    }
    
    .animate-spin-slow {
        animation: spin-slow 12s linear infinite;
    }
    
    .animate-float {
        animation: floating 5s ease-in-out infinite;
    }
    
    .animate-float-delayed {
        animation: float-delayed 6s ease-in-out infinite;
        animation-delay: 1s;
    }
    
    .animate-float-slow {
        animation: floating 8s ease-in-out infinite;
    }
    
    .animate-float-reverse {
        animation: float-reverse 7s ease-in-out infinite;
    }
    
    .animate-shooting-star {
        animation: shooting-star 5s ease-out infinite;
    }
    
    .animate-shooting-star-delayed {
        animation: shooting-star-delayed 5s ease-out infinite;
        animation-delay: 2.5s;
    }
    
    .animate-pulse-glow {
        animation: pulse-glow 3s ease-in-out infinite;
    }
    
    .animate-pulse-glow-delayed {
        animation: pulse-glow 3s ease-in-out infinite;
        animation-delay: 1.5s;
    }
    
    .animate-gradient-shift {
        background-size: 200% 200%;
        animation: gradient-shift 5s ease infinite;
    }
    
    .animate-bounce-slow {
        animation: bounce-slow 3s ease-in-out infinite;
    }
    
    /* Cartoon-style elements */
    .rounded-bubble {
        border-radius: 50% 50% 50% 20% / 50% 50% 30% 50%;
    }
    
    /* Child-friendly focus states */
    a:focus, button:focus, input:focus {
        outline: 4px solid rgba(255, 105, 180, 0.5);
        outline-offset: 2px;
    }
    
    /* Improved card hover effects */
    .card-hover {
        transition: all 0.3s ease;
    }
    
    .card-hover:hover {
        transform: translateY(-10px) scale(1.03);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
</style>

<section class="min-h-screen flex flex-col justify-center relative py-20 md:py-32 pb-32 md:pb-48">
    <div class="absolute top-10 left-5 w-16 h-16 animate-bounce"></div>
    <div class="absolute bottom-10 right-5 w-20 h-20 animate-pulse">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/balloons.png" alt="Decorative balloon" class="w-full h-full object-contain">
    </div>
    <div class="absolute top-1/3 right-10 w-14 h-14 animate-spin-slow">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/sun.png" alt="Decorative sun" class="w-full h-full object-contain">
    </div>
    <div class="absolute bottom-1/4 left-10 w-12 h-12 animate-bounce" style="animation-delay: 0.5s;"></div>
    <div class="absolute top-2/3 left-1/4 w-10 h-10 animate-ping" style="animation-delay: 1.2s;">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/balloons.png" alt="Decorative balloon" class="w-full h-full object-contain">
    </div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 text-center md:text-right mb-10 md:mb-0">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-dark mb-4 animate__animated animate__bounceIn">
                    مرحباً بكم في عالم
                    <div class="mt-3">
                        <span class="text-5xl md:text-6xl lg:text-7xl text-primary inline-block animate__animated animate__rubberBand animate__delay-1s">سارة</span>
                        <span class="mx-2">و</span>
                        <span class="text-5xl md:text-6xl lg:text-7xl text-secondary inline-block animate__animated animate__rubberBand animate__delay-2s">لوز</span>
                    </div>
                </h1>
                
                <?php if ($selected_age_group) : ?>
                    <p class="text-xl md:text-2xl text-gray-700 mb-8 animate__animated animate__fadeInUp animate__delay-1s">
                        محتوى مخصص للأطفال من عمر <?php echo $selected_age_group; ?> سنوات
                    </p>
                <?php else : ?>
                    <p class="text-xl md:text-2xl text-gray-700 mb-8 animate__animated animate__fadeInUp animate__delay-1s">
                        موقع ترفيهي وتعليمي للأطفال من عمر ٣ إلى ٩ سنوات
                    </p>
                <?php endif; ?>

                <div class="flex flex-wrap justify-center md:justify-start gap-4">
                    <a href="<?php echo get_post_type_archive_link('game'); ?>" class="bg-primary text-white px-6 py-3 rounded-full text-xl shadow-lg hover:bg-primary/90 transition transform hover:-translate-y-2 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-primary/50 animate__animated animate__bounceIn animate__delay-1s">
                        <i class="fas fa-gamepad ml-2"></i>
                        العب الآن
                    </a>
                    <a href="<?php echo get_post_type_archive_link('activity'); ?>" class="bg-secondary text-white px-6 py-3 rounded-full text-xl shadow-lg hover:bg-secondary/90 transition transform hover:-translate-y-2 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-secondary/50 animate__animated animate__bounceIn animate__delay-2s">
                        <i class="fas fa-paint-brush ml-2"></i>
                        أنشطة ممتعة
                    </a>
                </div>
            </div>
            <div class="md:w-1/2 relative">
                <div class="w-72 h-72 md:w-96 md:h-96 mx-auto relative">
                    <div class="absolute w-64 h-64 md:w-80 md:h-80 bg-primary rounded-bubble floating animate-pulse"></div>
                    <div class="absolute w-64 h-64 md:w-80 md:h-80 bg-secondary rounded-bubble right-4 top-4 floating animate-pulse" style="animation-delay: 0.5s"></div>
                    
                    <div class="absolute inset-0 flex items-center justify-center text-6xl font-bold text-white animate__animated animate__zoomIn">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/SWL-Logo.png" alt="سارة ولوز" class="w-250 h-250 md:w-350 md:h-350 object-contain">
                    </div>
                    
                    <div class="absolute -z-10 inset-0 bg-gradient-to-r from-primary/20 to-secondary/20 blur-xl rounded-full animate-pulse" style="animation-duration: 3s;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="hidden md:block absolute -top-10 right-10 w-24 h-24 bg-accent rounded-full opacity-40 floating animate-pulse"></div>
    <div class="hidden md:block absolute top-20 left-10 w-20 h-20 bg-primary rounded-full opacity-40 floating animate-bounce" style="animation-delay: 0.7s"></div>
    <div class="hidden md:block absolute bottom-10 right-20 w-28 h-28 bg-secondary rounded-full opacity-40 floating animate-ping" style="animation-delay: 1.2s"></div>

    <div class="section-separator">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" fill="#ffffff" class="fill-current text-white"></path>
        </svg>
    </div>
</section>

<section class="min-h-screen flex flex-col justify-center relative py-20 md:py-32 pb-32 md:pb-48 pattern-dots">
    <div class="container mx-auto px-4 relative z-10">
        <h2 class="text-3xl md:text-4xl font-bold text-center text-dark mb-6 animate__animated animate__bounceIn">
            <?php if ($selected_age_group && $current_age_data) : ?>
                عالم خاص بـ<?php echo $current_age_data['label']; ?> 
                <span class="text-<?php echo $current_age_data['color']; ?>"><?php echo $current_age_data['icon']; ?></span>
            <?php else : ?>
                عالم من المرح والتعلم 
            <?php endif; ?>
        </h2>
        <p class="text-xl text-center text-gray-600 mb-12 max-w-3xl mx-auto animate__animated animate__fadeIn animate__delay-1s">
            <?php if ($selected_age_group) : ?>
                محتوى مصمم خصيصاً للأطفال من عمر <?php echo $selected_age_group; ?> سنوات! 🚀
            <?php else : ?>
                انضموا إلينا في رحلة مليئة بالمغامرات والتعلم والإبداع! أنشطة ممتعة مصممة خصيصاً للأطفال 🚀
            <?php endif; ?>
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-gradient-to-br from-primary/10 to-primary/90 rounded-3xl shadow-lg p-6 text-center transform transition hover:-translate-y-4 hover:shadow-xl cursor-pointer group animate__animated animate__fadeInUp border-4 border-white">
                <div class="w-28 h-28 mx-auto mb-6 group-hover:scale-110 transition-transform">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/1.png" alt="ألعاب ممتعة" class="w-full h-full object-contain animate-bounce-slow">
                </div>
                <h3 class="text-2xl font-bold text-dark mb-3">ألعاب ممتعة</h3>
                <p class="text-xl text-gray-600 mb-4">
                    العب وتعلم مع سارة ولوز في ألعابنا التفاعلية الشيقة!
                </p>
                <a href="<?php echo get_post_type_archive_link('game'); ?>" class="inline-block bg-white text-primary font-bold px-6 py-3 rounded-full hover:bg-primary hover:text-white transition-all shadow-md">
                    هيا نلعب!
                </a>
            </div>

            <div class="bg-gradient-to-br from-secondary/10 to-secondary/90 rounded-3xl shadow-lg p-6 text-center transform transition hover:-translate-y-4 hover:shadow-xl cursor-pointer group animate__animated animate__fadeInUp animate__delay-1s border-4 border-white">
                <div class="w-28 h-28 mx-auto mb-6 group-hover:scale-110 transition-transform">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/2.png" alt="أنشطة رائعة" class="w-full h-full object-contain animate-pulse">
                </div>
                <h3 class="text-2xl font-bold text-dark mb-3">أنشطة رائعة</h3>
                <p class="text-xl text-gray-600 mb-4">
                    ارسم والعب واصنع أشياء جميلة مع أنشطتنا الإبداعية!
                </p>
                <a href="<?php echo get_post_type_archive_link('activity'); ?>" class="inline-block bg-white text-secondary font-bold px-6 py-3 rounded-full hover:bg-secondary hover:text-white transition-all shadow-md">
                    اكتشف الأنشطة!
                </a>
            </div>

            <div class="bg-gradient-to-br from-accent/10 to-accent/90 rounded-3xl shadow-lg p-6 text-center transform transition hover:-translate-y-4 hover:shadow-xl cursor-pointer group animate__animated animate__fadeInUp animate__delay-2s border-4 border-white">
                <div class="w-28 h-28 mx-auto mb-6 group-hover:scale-110 transition-transform">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/3.png" alt="متجر الهدايا" class="w-full h-full object-contain animate-bounce-slow">
                </div>
                <h3 class="text-2xl font-bold text-dark mb-3">متجر الهدايا</h3>
                <p class="text-xl text-gray-600 mb-4">
                    اكتشف ألعاباً وهدايا مميزة في متجرنا الخاص بالأطفال!
                </p>
                <?php if (function_exists('wc_get_page_id') && wc_get_page_id('shop') > 0) : ?>
                <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="inline-block bg-white text-accent font-bold px-6 py-3 rounded-full hover:bg-accent hover:text-white transition-all shadow-md">
                    زر المتجر!
                </a>
                <?php else: ?>
                <a href="#products" class="inline-block bg-white text-accent font-bold px-6 py-3 rounded-full hover:bg-accent hover:text-white transition-all shadow-md">
                    زر المتجر!
                </a>
                <?php endif; ?>
            </div>

            <div class="bg-gradient-to-br from-highlight/10 to-highlight/90 rounded-3xl shadow-lg p-6 text-center transform transition hover:-translate-y-4 hover:shadow-xl cursor-pointer group animate__animated animate__fadeInUp animate__delay-3s border-4 border-white">
                <div class="w-28 h-28 mx-auto mb-6 group-hover:scale-110 transition-transform">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/4.png" alt="فيديوهات شيقة" class="w-full h-full object-contain animate-pulse">
                </div>
                <h3 class="text-2xl font-bold text-dark mb-3">فيديوهات شيقة</h3>
                <p class="text-xl text-gray-600 mb-4">
                    شاهد قصصاً وأنشطة ممتعة في فيديوهاتنا التعليمية!
                </p>
                <a href="<?php echo get_post_type_archive_link('video'); ?>" class="inline-block bg-white text-primary font-bold px-6 py-3 rounded-full hover:bg-primary hover:text-white transition-all shadow-md">
                    شاهد الآن!
                </a>
            </div>
        </div>
    </div>
    
    <div class="section-separator section-separator-up">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z" fill="#F3E8FF"></path>
        </svg>
    </div>
</section>

<section id="games" class="min-h-screen flex flex-col justify-center relative py-24 md:py-32 pb-32 md:pb-48 bg-pastel-purple">
    <div class="container mx-auto px-4 h-full flex flex-col justify-center relative z-10">
        <div class="flex flex-col md:flex-row items-center justify-between mb-10">
            <h2 class="text-3xl md:text-4xl font-bold text-dark">
                <i class="fas fa-gamepad text-primary ml-2"></i>
                <?php if ($selected_age_group && $current_age_data && false) : ?>
                    ألعاب للأطفال من عمر <?php echo $selected_age_group; ?> سنوات
                <?php else : ?>
                    قسم الألعاب
                <?php endif; ?>
            </h2>
            <a href="<?php echo get_post_type_archive_link('game'); ?>" class="mt-4 md:mt-0 bg-primary text-white px-6 py-3 rounded-full hover:bg-primary/90 transition shadow-md">عرض كل الألعاب</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            // Get games with age filtering
            $games_args = array(
                'post_type' => 'game',
                'posts_per_page' => 3,
                'orderby' => 'date',
                'order' => 'DESC'
            );

            // Add age filtering if age group is selected
            if ($selected_age_group) {
                $games_args['meta_query'] = sarah_loz_get_age_filter_meta_query($selected_age_group);
            }

            $featured_games = new WP_Query($games_args);
            
            if ($featured_games->have_posts()) :
                while ($featured_games->have_posts()) : $featured_games->the_post();
                $age_range = get_field('age_range') ? get_field('age_range') : '';
                $game_category = '';
                $game_terms = get_the_terms(get_the_ID(), 'game_category');
                if ($game_terms && !is_wp_error($game_terms)) {
                    $game_categories = array();
                    foreach ($game_terms as $term) {
                        $game_categories[] = $term->name;
                    }
                    $game_category = join(", ", $game_categories);
                }
            ?>
                <div class="bg-white rounded-3xl shadow-lg overflow-hidden card-hover border-4 border-white">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="h-48 relative">
                            <?php the_post_thumbnail('medium_large', ['class' => 'h-full w-full object-cover']); ?>
                            <?php if ($current_age_data) : ?>
                                <div class="absolute top-2 right-2 bg-<?php echo $current_age_data['color']; ?> text-white px-2 py-1 rounded-full text-xs font-bold shadow">
                                    مناسب لعمرك!
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="h-48 bg-primary/20 relative">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <i class="fas fa-gamepad text-6xl text-primary/50"></i>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-xl font-bold text-dark"><?php the_title(); ?></h3>
                            <?php if ($age_range) : ?>
                                <span class="text-xs bg-accent text-dark px-2 py-1 rounded-full"><?php echo esc_html($age_range); ?> سنوات</span>
                            <?php endif; ?>
                        </div>
                        <?php if ($game_category) : ?>
                            <div class="mb-3">
                                <span class="bg-primary/10 text-primary px-2 py-1 rounded-full text-xs">
                                    <i class="fas fa-tag ml-1"></i>
                                    <?php echo esc_html($game_category); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <p class="text-gray-600 mb-4">
                            <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
                        </p>
                        <a href="<?php the_permalink(); ?>" class="inline-block bg-primary/10 text-primary px-4 py-2 rounded-full hover:bg-primary/20 transition">
                            العب الآن
                        </a>
                    </div>
                </div>
            <?php endwhile;
                wp_reset_postdata();
            else: 
                ?>
                <div class="col-span-3 text-center py-10 bg-white/50 rounded-lg">
                    <i class="fas fa-gamepad text-6xl text-primary/30 mb-4"></i>
                    <h3 class="text-2xl font-bold text-gray-600 mb-2">لا توجد ألعاب متاحة</h3>
                    <p class="text-gray-500">
                        <?php if ($selected_age_group) : ?>
                            لا توجد ألعاب مناسبة لعمر <?php echo $selected_age_group; ?> سنوات حالياً. تحقق لاحقاً!
                        <?php else : ?>
                            لم يتم العثور على ألعاب. يرجى المحاولة لاحقاً.
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="section-separator" style="transform: translateY(98%); z-index: 20;">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" 
                fill="#F3E8FF"></path> </svg>
    </div>
</section>

<section id="activities" class="min-h-screen flex flex-col justify-center relative py-24 md:py-32 pb-32 md:pb-48 pattern-grid">
    <div class="container mx-auto px-4 h-full flex flex-col justify-center relative z-10">
        <div class="flex flex-col md:flex-row items-center justify-between mb-10">
            <h2 class="text-3xl md:text-4xl font-bold text-dark">
                <i class="fas fa-paint-brush text-secondary ml-2"></i>
                <?php if ($selected_age_group && $current_age_data && false) : ?>
                    أنشطة للأطفال من عمر <?php echo $selected_age_group; ?> سنوات
                <?php else : ?>
                    قسم الأنشطة
                <?php endif; ?>
            </h2>
            <a href="<?php echo get_post_type_archive_link('activity'); ?>" class="mt-4 md:mt-0 bg-secondary text-white px-6 py-3 rounded-full hover:bg-secondary/90 transition shadow-md">عرض كل الأنشطة</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            // Get activities with age filtering
            $activities_args = array(
                'post_type' => 'activity',
                'posts_per_page' => 3,
                'orderby' => 'date',
                'order' => 'DESC'
            );

            // Add age filtering if age group is selected
            if ($selected_age_group) {
                $activities_args['meta_query'] = sarah_loz_get_age_filter_meta_query($selected_age_group);
            }

            $featured_activities = new WP_Query($activities_args);

            if ($featured_activities->have_posts()) :
                while ($featured_activities->have_posts()) : $featured_activities->the_post(); 
                    // Get age range if available
                    $age_range = get_field('age_range') ? get_field('age_range') : '';
                    ?>
                    <div class="bg-white rounded-3xl shadow-lg overflow-hidden card-hover border-2 border-gray-100">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="h-48 relative">
                                <?php the_post_thumbnail('medium_large', ['class' => 'h-full w-full object-cover']); ?>
                                <?php if ($current_age_data) : ?>
                                    <div class="absolute top-2 right-2 bg-<?php echo $current_age_data['color']; ?> text-white px-2 py-1 rounded-full text-xs font-bold shadow">
                                        مناسب لعمرك!
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="h-48 bg-secondary/20 relative">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <i class="fas fa-palette text-6xl text-secondary/50"></i>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-xl font-bold text-dark"><?php the_title(); ?></h3>
                                <?php if ($age_range) : ?>
                                    <span class="text-xs bg-accent text-dark px-2 py-1 rounded-full"><?php echo esc_html($age_range); ?> سنوات</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-gray-600 mb-4">
                                <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
                            </p>
                            <a href="<?php the_permalink(); ?>" class="inline-block bg-secondary/10 text-secondary px-4 py-2 rounded-full hover:bg-secondary/20 transition">
                                اعرض النشاط
                            </a>
                        </div>
                    </div>
                <?php endwhile;
                wp_reset_postdata();
            else: 
                ?>
                <div class="col-span-3 text-center py-10 bg-white/50 rounded-lg">
                    <i class="fas fa-palette text-6xl text-secondary/30 mb-4"></i>
                    <h3 class="text-2xl font-bold text-gray-600 mb-2">لا توجد أنشطة متاحة</h3>
                    <p class="text-gray-500">
                        <?php if ($selected_age_group) : ?>
                            لا توجد أنشطة مناسبة لعمر <?php echo $selected_age_group; ?> سنوات حالياً. تحقق لاحقاً!
                        <?php else : ?>
                            لم يتم العثور على أنشطة. يرجى المحاولة لاحقاً.
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="section-separator section-separator-up">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z" fill="#FFF7ED"></path>
        </svg>
    </div>
</section>

<section id="products" class="min-h-screen flex flex-col justify-center relative py-24 md:py-32 pb-32 md:pb-48 bg-pastel-orange">
    <div class="container mx-auto px-4 h-full flex flex-col justify-center relative z-10">
        <div class="flex flex-col md:flex-row items-center justify-between mb-10">
            <h2 class="text-3xl md:text-4xl font-bold text-dark">
                <i class="fas fa-shopping-bag text-accent ml-2"></i>
                متجر سارة ولوز
            </h2>
            <?php if (function_exists('wc_get_page_id') && wc_get_page_id('shop') > 0) : ?>
                <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="mt-4 md:mt-0 bg-accent text-white px-6 py-3 rounded-full hover:bg-accent/90 transition shadow-md">تسوق الآن</a>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php 
            if (function_exists('wc_get_products')) {
                $products = wc_get_products(array(
                    'limit' => 3,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'status' => 'publish',
                ));

                if (!empty($products)) {
                    foreach ($products as $product) {
                        $product_id = $product->get_id();
                        $product_url = get_permalink($product_id);
                        $product_title = $product->get_name();
                        $product_price = $product->get_price_html();
                        $product_image_id = $product->get_image_id();
                        $age_range = get_field('age_range', $product_id) ? get_field('age_range', $product_id) : '';
                        
                        // Get product categories
                        $categories = wc_get_product_category_list($product_id);
                        ?>
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition transform hover:-translate-y-2 hover:shadow-xl border-4 border-white">
                            <div class="h-52 relative">
                                <?php if ($product_image_id) : ?>
                                    <?php echo wp_get_attachment_image($product_image_id, 'medium_large', false, ['class' => 'h-full w-full object-cover']); ?>
                                <?php else : ?>
                                    <div class="h-full bg-accent/20 flex items-center justify-center">
                                        <i class="fas fa-box-open text-6xl text-accent/50"></i>
                                    </div>
                                <?php endif; ?>
                                <?php if ($product->is_on_sale()) : ?>
                                    <div class="absolute top-2 left-2">
                                        <span class="bg-primary text-white px-3 py-1 rounded-full text-sm shadow">تخفيض</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-3">
                                    <h3 class="text-xl font-bold text-dark"><?php echo esc_html($product_title); ?></h3>
                                    <?php if ($age_range) : ?>
                                        <span class="text-xs bg-accent text-dark px-2 py-1 rounded-full"><?php echo esc_html($age_range); ?> سنوات</span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex justify-between items-center">
                                    <div class="text-lg font-bold text-primary"><?php echo $product_price; ?></div>
                                    <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" data-quantity="1" data-product_id="<?php echo esc_attr($product_id); ?>" class="add_to_cart_button ajax_add_to_cart bg-primary text-white px-4 py-2 rounded-full hover:bg-primary/90 transition shadow-sm">
                                        <i class="fas fa-cart-plus ml-1"></i> أضف للسلة
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php }
                } else {
                    // Fallback static content if no products are found
                    ?>
                    <div class="col-span-3 text-center py-10 bg-white/50 rounded-lg">
                        <i class="fas fa-shopping-bag text-6xl text-accent/30 mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-600 mb-2">المتجر قريباً</h3>
                        <p class="text-gray-500">سيتم إضافة منتجات رائعة قريباً!</p>
                    </div>
                    <?php
                }
            } else {
                // If WooCommerce functions are not available
                echo '<div class="col-span-3 text-center py-10 bg-white/50 rounded-lg">';
                echo '<p>المتجر غير متاح حالياً. يرجى التحقق لاحقاً.</p>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
    
    <div class="section-separator" style="transform: translateY(98%); z-index: 20;">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" 
                fill="#FFF7ED"></path>
        </svg>
    </div>
</section>

<section id="theaters" class="min-h-screen flex flex-col justify-center relative py-24 md:py-32 pb-32 md:pb-48 pattern-dots">
    <div class="container mx-auto px-4 h-full flex flex-col justify-center relative z-10">
        <div class="flex flex-col md:flex-row items-center justify-between mb-10">
            <h2 class="text-3xl md:text-4xl font-bold text-dark">
                <i class="fas fa-theater-masks text-dark ml-2"></i>
                <?php if ($selected_age_group && $current_age_data && false) : ?>
                    مسرحيات للأطفال من عمر <?php echo $selected_age_group; ?> سنوات
                <?php else : ?>
                    مسرحيات تعليمية
                <?php endif; ?>
            </h2>
            <a href="<?php echo get_post_type_archive_link('theater'); ?>" class="mt-4 md:mt-0 bg-dark text-white px-6 py-3 rounded-full hover:bg-dark/90 transition shadow-md">عرض كل المسرحيات</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            // Get theaters with age filtering
            $theaters_args = array(
                'post_type' => 'theater',
                'posts_per_page' => 3,
                'orderby' => 'date',
                'order' => 'DESC'
            );

            // Add age filtering if age group is selected
            if ($selected_age_group) {
                $theaters_args['meta_query'] = sarah_loz_get_age_filter_meta_query($selected_age_group);
            }

            $featured_theaters = new WP_Query($theaters_args);

            if ($featured_theaters->have_posts()) :
                while ($featured_theaters->have_posts()) : $featured_theaters->the_post(); 
                    // Get age range if available
                    $age_range = get_field('age_range') ? get_field('age_range') : '';
                    // Get theater type if available
                    $theater_type = get_field('theater_type') ? get_field('theater_type') : '';
                    ?>
                    <div class="bg-white rounded-3xl shadow-lg overflow-hidden card-hover border-2 border-gray-100">
                        <div class="h-48 relative group">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium_large', ['class' => 'h-full w-full object-cover']); ?>
                                <?php if ($current_age_data) : ?>
                                    <div class="absolute top-2 right-2 bg-<?php echo $current_age_data['color']; ?> text-white px-2 py-1 rounded-full text-xs font-bold shadow">
                                        مناسب لعمرك!
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="h-full bg-dark/20 flex items-center justify-center">
                                    <i class="fas fa-theater-masks text-6xl text-dark/50"></i>
                                </div>
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-dark/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                <a href="<?php echo get_permalink(); ?>" class="w-16 h-16 rounded-full bg-primary/80 flex items-center justify-center hover:bg-primary transition-colors shadow-lg">
                                    <i class="fas fa-play text-white text-xl"></i>
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-xl font-bold text-dark"><?php the_title(); ?></h3>
                                <?php if ($age_range) : ?>
                                    <span class="text-xs bg-accent text-dark px-2 py-1 rounded-full"><?php echo esc_html($age_range); ?> سنوات</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-gray-600 mb-4">
                                <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
                            </p>
                            <div class="flex justify-between items-center">
                                <a href="<?php echo get_permalink(); ?>" class="inline-block bg-dark/10 text-dark px-4 py-2 rounded-full hover:bg-dark/20 transition">
                                    <i class="fas fa-play-circle ml-1"></i> شاهد المسرحية
                                </a>
                                <?php if ($theater_type) : ?>
                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full"><?php echo esc_html($theater_type); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
                wp_reset_postdata();
            else: 
                ?>
                <div class="col-span-3 text-center py-10 bg-white/50 rounded-lg">
                    <i class="fas fa-theater-masks text-6xl text-dark/30 mb-4"></i>
                    <h3 class="text-2xl font-bold text-gray-600 mb-2">لا توجد مسرحيات متاحة</h3>
                    <p class="text-gray-500">
                        <?php if ($selected_age_group) : ?>
                            لا توجد مسرحيات مناسبة لعمر <?php echo $selected_age_group; ?> سنوات حالياً. تحقق لاحقاً!
                        <?php else : ?>
                            لم يتم العثور على مسرحيات. يرجى المحاولة لاحقاً.
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="section-separator section-separator-up">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z" fill="#EBF8FF"></path>
        </svg>
    </div>
</section>

<section id="videos" class="min-h-screen flex flex-col justify-center relative py-24 md:py-32 pb-32 md:pb-48 bg-pastel-blue">
    <div class="container mx-auto px-4 h-full flex flex-col justify-center relative z-10">
        <div class="flex flex-col md:flex-row items-center justify-between mb-10">
            <h2 class="text-3xl md:text-4xl font-bold text-dark">
                <i class="fas fa-video text-dark ml-2"></i>
                <?php if ($selected_age_group && $current_age_data && false) : ?>
                    فيديوهات للأطفال من عمر <?php echo $selected_age_group; ?> سنوات
                <?php else : ?>
                    فيديوهات تعليمية
                <?php endif; ?>
            </h2>
            <a href="<?php echo get_post_type_archive_link('video'); ?>" class="mt-4 md:mt-0 bg-dark text-white px-6 py-3 rounded-full hover:bg-dark/90 transition shadow-md">عرض كل الفيديوهات</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            // Get videos with age filtering
            $videos_args = array(
                'post_type' => 'video',
                'posts_per_page' => 3,
                'orderby' => 'date',
                'order' => 'DESC'
            );

            // Add age filtering if age group is selected
            if ($selected_age_group) {
                $videos_args['meta_query'] = sarah_loz_get_age_filter_meta_query($selected_age_group);
            }

            $featured_videos = new WP_Query($videos_args);

            if ($featured_videos->have_posts()) :
                while ($featured_videos->have_posts()) : $featured_videos->the_post(); 
                    // Get age range if available
                    $age_range = get_field('age_range') ? get_field('age_range') : '';
                    ?>
                    <div class="bg-white rounded-3xl shadow-lg overflow-hidden card-hover border-4 border-white">
                        <div class="h-48 relative group">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium_large', ['class' => 'h-full w-full object-cover']); ?>
                                <?php if ($current_age_data) : ?>
                                    <div class="absolute top-2 right-2 bg-<?php echo $current_age_data['color']; ?> text-white px-2 py-1 rounded-full text-xs font-bold shadow">
                                        مناسب لعمرك!
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="h-full bg-dark/20 flex items-center justify-center">
                                    <i class="fas fa-film text-6xl text-dark/50"></i>
                                </div>
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-dark/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                <a href="<?php echo get_permalink(); ?>" class="w-16 h-16 rounded-full bg-primary/80 flex items-center justify-center hover:bg-primary transition-colors shadow-lg">
                                    <i class="fas fa-play text-white text-xl"></i>
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-xl font-bold text-dark"><?php the_title(); ?></h3>
                                <?php if ($age_range) : ?>
                                    <span class="text-xs bg-accent text-dark px-2 py-1 rounded-full"><?php echo esc_html($age_range); ?> سنوات</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-gray-600 mb-4">
                                <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
                            </p>
                            <a href="<?php echo get_permalink(); ?>" class="inline-block bg-dark/10 text-dark px-4 py-2 rounded-full hover:bg-dark/20 transition">
                                <i class="fas fa-play-circle ml-1"></i> شاهد الفيديو
                            </a>
                        </div>
                    </div>
                <?php endwhile;
                wp_reset_postdata();
            else: 
                ?>
                <div class="col-span-3 text-center py-10 bg-white/50 rounded-lg">
                    <i class="fas fa-film text-6xl text-dark/30 mb-4"></i>
                    <h3 class="text-2xl font-bold text-gray-600 mb-2">لا توجد فيديوهات متاحة</h3>
                    <p class="text-gray-500">
                        <?php if ($selected_age_group) : ?>
                            لا توجد فيديوهات مناسبة لعمر <?php echo $selected_age_group; ?> سنوات حالياً. تحقق لاحقاً!
                        <?php else : ?>
                            لم يتم العثور على فيديوهات. يرجى المحاولة لاحقاً.
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="section-separator" style="transform: translateY(98%); z-index: 20;">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" 
                fill="#EBF8FF"></path>
        </svg>
    </div>
</section>

<?php if (!$selected_age_group) : ?>
    <section class="min-h-screen flex flex-col justify-center relative py-24 md:py-32 pb-32 md:pb-48 pattern-grid">
        <div class="container mx-auto px-4 h-full flex flex-col justify-center relative z-10">
            <header class="text-center mb-12">
                <h2 class="text-3xl font-bold text-dark mb-4">
                    <span class="text-primary">🌈</span> 
                    اختر مغامرتك
                    <span class="text-secondary">✨</span>
                </h2>
                <p class="text-2xl text-gray-600">كل عمر له عالمه الخاص من المرح!</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php 
                $age_groups = sarah_loz_get_age_groups();
                foreach ($age_groups as $range => $group) : 
                ?>
                    <div class="bg-gradient-to-br from-<?php echo $group['color']; ?>/10 to-white rounded-3xl p-8 text-center hover:shadow-xl transition-all transform hover:-translate-y-2 cursor-pointer border-2 border-<?php echo $group['color']; ?>/20">
                        <div class="w-32 h-32 bg-<?php echo $group['color']; ?>/20 rounded-full flex flex-col items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform shadow-inner">
                            <?php if (isset($group['custom_image'])) : ?>
                                <img src="<?php echo esc_url($group['custom_image']); ?>" alt="<?php echo esc_attr($group['label']); ?>" class="w-20 h-20 object-cover rounded-full mb-2">
                            <?php else : ?>
                                <span class="text-4xl mb-2"><?php echo $group['icon']; ?></span>
                            <?php endif; ?>
                            <span class="text-2xl font-bold text-<?php echo $group['color']; ?>"><?php echo $range; ?></span>
                        </div>
                        <h3 class="text-2xl font-bold mb-4"><?php echo $group['label']; ?></h3>
                        <p class="text-xl text-gray-600 mb-6"><?php echo $group['description']; ?></p>
                        <a href="<?php echo add_query_arg('age_group', $range, get_permalink(get_page_by_path('age-group'))); ?>" 
                           class="inline-block bg-<?php echo $group['color']; ?> text-white px-8 py-4 rounded-full text-xl hover:bg-opacity-90 transition-colors transform hover:scale-105 shadow-md"
                           onclick="selectAgeGroupFromFrontPage('<?php echo $range; ?>')">
                            <span class="ml-2"><?php echo $range === '3-5' ? '🎮' : ($range === '6-7' ? '🎨' : '🏆'); ?></span>
                            اختر هذا العمر!
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="section-separator section-separator-up">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z" fill="#FF9AA2"></path>
            </svg>
        </div>
    </section>
<?php endif; ?>

<section class="min-h-screen flex flex-col justify-center relative py-24 md:py-32 pb-32 md:pb-48 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-[#FF9AA2] via-[#FFDAC1] to-[#B5EAD7] opacity-90"></div>
    
    <div class="absolute inset-0">
        <div class="absolute top-10 left-10 w-24 h-24 bg-white/20 rounded-full animate-float blur-sm"></div>
        <div class="absolute bottom-10 right-10 w-20 h-20 bg-white/20 rounded-full animate-float-delayed blur-sm"></div>
        <div class="absolute top-1/2 left-1/4 w-16 h-16 bg-white/20 rounded-full animate-float-slow blur-sm"></div>
        
        <div class="absolute top-1/3 right-1/4 w-28 h-28 bg-white/10 rounded-bubble animate-spin-slow blur-md"></div>
        <div class="absolute bottom-1/4 left-1/3 w-36 h-36 bg-white/10 rounded-bubble animate-float-reverse blur-md"></div>
        
        <div class="absolute top-1/4 left-10 w-40 h-20 bg-white/70 rounded-full blur-md animate-float-slow"></div>
        <div class="absolute top-1/4 left-20 w-32 h-16 bg-white/70 rounded-full blur-md animate-float-slow" style="animation-delay: 0.3s;"></div>
        <div class="absolute bottom-1/4 right-10 w-40 h-20 bg-white/70 rounded-full blur-md animate-float-delayed"></div>
        <div class="absolute bottom-1/4 right-20 w-32 h-16 bg-white/70 rounded-full blur-md animate-float-delayed" style="animation-delay: 0.3s;"></div>
        
        <div class="absolute -top-4 -right-4 w-10 h-10 bg-white/50 rounded-full animate-shooting-star"></div>
        <div class="absolute -top-4 -left-4 w-8 h-8 bg-white/40 rounded-full animate-shooting-star-delayed"></div>
        
        <div class="absolute top-1/3 right-1/3 w-6 h-6 animate-ping" style="animation-duration: 3s;">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/star.jpg" alt="" class="w-full h-full object-contain opacity-70">
        </div>
        <div class="absolute bottom-1/3 left-1/3 w-8 h-8 animate-ping" style="animation-duration: 4s; animation-delay: 1s;">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/star.jpg" alt="" class="w-full h-full object-contain opacity-70">
        </div>
    </div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center max-w-3xl mx-auto bg-white/30 backdrop-blur-sm p-10 rounded-3xl shadow-2xl animate__animated animate__zoomIn">
            <div class="inline-block bg-white/40 rounded-full p-6 mb-8 shadow-xl animate-bounce-slow">
                <span class="text-6xl">🌟</span>
            </div>
            <h2 class="text-4xl md:text-5xl font-bold text-dark mb-6">
                هيا نبدأ المغامرة معاً!
            </h2>
            <p class="text-2xl text-dark/90 mb-10">
                انضموا إلى عائلتنا السعيدة واستمتعوا بعالم من المرح والتعلم مع سارة ولوز! 🚀
            </p>
            <div class="flex flex-wrap justify-center gap-6">
                <a href="<?php echo esc_url(get_permalink(get_page_by_path('register'))); ?>" 
                   class="group bg-primary text-white px-8 py-5 rounded-2xl font-bold text-xl shadow-xl hover:bg-primary/90 transition transform hover:-translate-y-2 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-primary/50">
                    <span class="inline-block group-hover:scale-125 transition-transform mr-2">🎮</span>
                    ابدأ المغامرة
                </a>
                <a href="<?php echo esc_url(get_permalink(get_page_by_path('about'))); ?>" 
                   class="group bg-white/40 backdrop-blur border-2 border-white text-dark px-8 py-5 rounded-2xl font-bold text-xl hover:bg-white/60 transition transform hover:-translate-y-2 hover:scale-105 shadow-xl">
                    <span class="inline-block mr-2 group-hover:scale-125 transition-transform">✨</span>
                    اكتشف المزيد
                </a>
            </div>
            
            <div class="flex justify-center mt-12 gap-4">
                <div class="w-24 h-24 bg-primary/20 rounded-full flex items-center justify-center animate-bounce" style="animation-delay: 0.1s;">
                    <span class="text-5xl">🦁</span>
                </div>
                <div class="w-24 h-24 bg-secondary/20 rounded-full flex items-center justify-center animate-bounce" style="animation-delay: 0.3s;">
                    <span class="text-5xl">🦊</span>
                </div>
                <div class="w-24 h-24 bg-accent/20 rounded-full flex items-center justify-center animate-bounce" style="animation-delay: 0.5s;">
                    <span class="text-5xl">🦉</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="section-separator">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" fill="#ffffff" class="fill-current text-white"></path>
        </svg>
    </div>
</section>

<section class="min-h-screen flex flex-col justify-center relative py-20 md:py-32 bg-gradient-to-b from-white to-light overflow-hidden">
    <div class="absolute -top-16 right-0 w-48 h-48 opacity-20 animate-spin-slow" style="animation-duration: 20s;">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/sun.png" alt="" class="w-full h-full object-contain">
    </div>
    <div class="absolute -bottom-20 left-0 w-48 h-48 opacity-20 transform rotate-180 animate-float-slow">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/balloons.png" alt="" class="w-full h-full object-contain">
    </div>
    
    <div class="absolute top-1/4 left-10 w-16 h-16 bg-primary/20 rounded-full animate-ping" style="animation-duration: 4s;"></div>
    <div class="absolute bottom-1/4 right-10 w-16 h-16 bg-secondary/20 rounded-full animate-ping" style="animation-duration: 5s;"></div>
    
    <div class="absolute top-0 left-1/4 w-64 h-16 bg-white rounded-full blur-md"></div>
    <div class="absolute top-8 left-1/4 w-48 h-16 bg-white rounded-full blur-md"></div>
    <div class="absolute bottom-0 right-1/4 w-64 h-16 bg-white rounded-full blur-md"></div>
    <div class="absolute bottom-8 right-1/4 w-48 h-16 bg-white rounded-full blur-md"></div>

    <div class="container mx-auto px-4 relative z-10 h-full flex flex-col justify-center">
        <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12 max-w-4xl mx-auto transform hover:scale-105 transition-transform animate__animated animate__fadeInUp border-4 border-white/50">
            <div class="absolute -top-10 -right-10 w-20 h-20 bg-primary/20 rounded-full animate-bounce-slow"></div>
            <div class="absolute -bottom-10 -left-10 w-20 h-20 bg-secondary/20 rounded-full animate-bounce-slow" style="animation-delay: 0.5s;"></div>
            
            <div class="flex flex-col md:flex-row items-center gap-10">
                <div class="md:w-1/2 text-center md:text-right">
                    <div class="inline-block bg-light rounded-full p-4 mb-6 animate-pulse">
                        <span class="text-5xl">📨</span>
                    </div>
                    <h2 class="text-3xl font-bold text-dark mb-4">انضموا إلى عائلة سارة ولوز!</h2>
                    <p class="text-xl text-gray-600 mb-6">
                        اشتركوا معنا للحصول على مزايا خاصة ومحتوى حصري!
                    </p>
                    <ul class="text-lg text-gray-600 space-y-4 mb-6">
                        <li class="flex items-center justify-start gap-3 bg-light/50 p-3 rounded-xl transform hover:scale-105 transition-transform">
                            <span class="text-2xl animate-bounce">🎮</span>
                            <span>ألعاب حصرية مجانية</span>
                        </li>
                        <li class="flex items-center justify-start gap-3 bg-light/50 p-3 rounded-xl transform hover:scale-105 transition-transform">
                            <span class="text-2xl animate-bounce" style="animation-delay: 0.2s;">🎨</span>
                            <span>أنشطة إبداعية للأطفال</span>
                        </li>
                        <li class="flex items-center justify-start gap-3 bg-light/50 p-3 rounded-xl transform hover:scale-105 transition-transform">
                            <span class="text-2xl animate-bounce" style="animation-delay: 0.4s;">🎁</span>
                            <span>عروض خاصة للعائلة</span>
                        </li>
                    </ul>
                </div>
                <div class="md:w-1/2 w-full bg-gradient-to-br from-light/50 to-white p-6 rounded-3xl shadow-lg border-2 border-primary/10">
                    <form class="swl-newsletter-form flex flex-col space-y-5">
                        <h3 class="text-2xl font-bold text-center mb-4">انضم إلينا الآن! 🚀</h3>
                        
                        <div class="relative transform hover:scale-105 transition-transform">
                            <input type="text" name="name" placeholder="الاسم" required 
                                   class="w-full px-6 py-4 rounded-xl border-2 border-primary/30 focus:outline-none focus:ring-2 focus:ring-primary/30 text-lg">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-2xl">👤</span>
                        </div>
                        
                        <div class="relative transform hover:scale-105 transition-transform">
                            <input type="email" name="email" placeholder="البريد الإلكتروني" required 
                                   class="w-full px-6 py-4 rounded-xl border-2 border-primary/30 focus:outline-none focus:ring-2 focus:ring-primary/30 text-lg">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-2xl">✉️</span>
                        </div>
                        
                        <div class="flex items-center p-4 rounded-xl bg-light/50 transform hover:scale-105 transition-transform border-2 border-primary/10">
                            <input type="checkbox" id="terms" name="terms" class="ml-3 w-6 h-6 rounded text-primary focus:ring-primary/30">
                            <label for="terms" class="text-lg text-gray-600">أوافق على استلام الرسائل الترحيبية <span class="animate-pulse inline-block">🌟</span></label>
                        </div>
                        
                        <button type="submit" class="bg-primary text-white px-8 py-4 rounded-xl text-xl font-bold shadow-lg hover:bg-primary/90 transition transform hover:-translate-y-2 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-primary/50 group">
                            <span class="inline-block group-hover:scale-125 transition-transform mr-2">🚀</span>
                            انضم إلى المغامرة!
                        </button>
                        
                        <div class="swl-newsletter-response text-center mt-4 text-lg font-bold"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add animation classes on scroll for better performance
    const animateOnScroll = function() {
        const elements = document.querySelectorAll('.animate__animated');
        
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            
            // If element is in viewport
            if (elementPosition < windowHeight - 50) {
                const animationClass = element.dataset.animation || 'animate__fadeIn';
                element.classList.add(animationClass);
            }
        });
    };
    
    // Run once on page load
    animateOnScroll();
    
    // Add event listener for scroll
    window.addEventListener('scroll', animateOnScroll);
    
    // Interactive elements for children
    const interactiveElements = document.querySelectorAll('.card-hover, .floating, [class*="animate-"]');
    
    interactiveElements.forEach(element => {
        element.addEventListener('mouseover', () => {
            element.style.animationPlayState = 'running';
            element.style.animationDuration = '1s';
        });
        
        element.addEventListener('mouseout', () => {
            element.style.animationDuration = '';
        });
    });
    
    // Newsletter form child-friendly validation
    const newsletterForm = document.querySelector('.swl-newsletter-form');
    
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = this.querySelector('input[name="email"]');
            const responseDiv = this.querySelector('.swl-newsletter-response');
            
            if (emailInput.validity.valid) {
                // Success message with animation
                responseDiv.innerHTML = '<span class="text-green-500">🎉 شكراً لانضمامك إلى مغامرتنا! 🎉</span>';
                responseDiv.classList.add('animate__animated', 'animate__bounceIn');
                
                // Reset form
                setTimeout(() => {
                    this.reset();
                }, 2000);
            } else {
                // Child-friendly error message
                responseDiv.innerHTML = '<span class="text-primary">🤔 يبدو أن هناك خطأ في البريد الإلكتروني. هل يمكنك التحقق منه؟</span>';
                responseDiv.classList.add('animate__animated', 'animate__shakeX');
                
                // Remove animation class after animation completes
                setTimeout(() => {
                    responseDiv.classList.remove('animate__animated', 'animate__shakeX');
                }, 1000);
            }
        });
    }
});

// Function to select age group from front page
function selectAgeGroupFromFrontPage(ageGroup) {
    // Set the age group via AJAX
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
            
            // Show success message and reload page
            const successMsg = document.createElement('div');
            successMsg.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate__animated animate__bounceIn';
            successMsg.innerHTML = `🎉 تم اختيار الفئة العمرية ${ageGroup} سنوات!`;
            document.body.appendChild(successMsg);
            
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            console.error('Failed to set age group:', data);
            alert('حدث خطأ. يرجى المحاولة مرة أخرى.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ. يرجى المحاولة مرة أخرى.');
    });
    
    // Prevent default link behavior
    return false;
}
</script>