<?php
/**
 * Template Name: Exam Results
 * Description: Displays the child's assessment history
 */

// 1. Security & Header
if (!defined('ABSPATH')) exit;
acf_form_head();
get_header(); 

// 2. User Check
if (!is_user_logged_in()) {
    echo '<script>window.location.href = "' . home_url('/login') . '";</script>';
    exit;
}

$current_user = wp_get_current_user();
?>

<div class="game-page-container" style="min-height: 100vh; padding-top: 20px;">
    
    <div class="container-responsive">
        <div class="game-header rounded-xl mb-6 p-6 flex justify-between items-center" style="background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">📊 نتائج اختبارات تحديد المستوى</h1>
                <p class="text-gray-500">سجل بجميع الاختبارات التي قمت بإجرائها</p>
            </div>
            <a href="<?php echo home_url('/child-dashboard'); ?>" class="game-button bg-gray-100 text-gray-700 hover:bg-gray-200">
                <i class="fas fa-arrow-right ml-2"></i> العودة للرئيسية
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 space-y-6">
                
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">ملخص الأداء</h3>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <div class="text-3xl font-bold text-blue-600">
                                <?php 
                                    // Count total exams taken
                                    $logs = get_user_meta($current_user->ID, 'game_play_log', true);
                                    $count = 0;
                                    if(is_array($logs)) {
                                        foreach($logs as $l) { if(!empty($l['breakdown'])) $count++; }
                                    }
                                    echo $count;
                                ?>
                            </div>
                            <div class="text-sm text-gray-600 mt-1">اختبارات مكتملة</div>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg">
                            <div class="text-3xl font-bold text-green-600">
                                <?php 
                                    // Calculate Average Score
                                    $total_score = 0;
                                    if($count > 0) {
                                        foreach($logs as $l) { 
                                            if(!empty($l['breakdown']) && isset($l['score'])) $total_score += $l['score']; 
                                        }
                                        echo round($total_score / $count) . '%';
                                    } else {
                                        echo '-';
                                    }
                                ?>
                            </div>
                            <div class="text-sm text-gray-600 mt-1">معدل الدرجات</div>
                        </div>
                        <div class="p-4 bg-purple-50 rounded-lg">
                            <div class="text-3xl font-bold text-purple-600">A+</div>
                            <div class="text-sm text-gray-600 mt-1">التقدير العام</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <?php echo do_shortcode('[assessment_history]'); ?>
                </div>

            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                    <div class="mb-4">
                        <?php echo get_avatar($current_user->ID, 96, '', '', array('class' => 'rounded-full mx-auto border-4 border-white shadow-md')); ?>
                    </div>
                    <h3 class="font-bold text-lg"><?php echo esc_html($current_user->display_name); ?></h3>
                    <p class="text-sm text-gray-500 mb-4"><?php echo esc_html($current_user->user_email); ?></p>
                    
                    <a href="<?php echo home_url('/profile'); ?>" class="block w-full py-2 px-4 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition">
                        تعديل الملف الشخصي
                    </a>
                </div>

                <div class="bg-yellow-50 rounded-xl p-6 border border-yellow-100">
                    <h3 class="font-bold text-yellow-800 mb-2">💡 نصيحة اليوم</h3>
                    <p class="text-sm text-yellow-700">
                        مراجعة أخطائك في "القواعد" ستساعدك على تحسين درجاتك في الاختبار القادم بنسبة 20%!
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<?php get_footer(); ?>