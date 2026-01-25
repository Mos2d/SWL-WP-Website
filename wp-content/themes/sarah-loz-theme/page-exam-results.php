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
    
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="game-header rounded-xl mb-6 p-6 flex justify-between items-center" style="background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">📊 نتائج اختبارات تحديد المستوى</h1>
                <p class="text-gray-500">سجل بجميع الاختبارات التي قمت بإجرائها</p>
            </div>
            <a href="<?php echo home_url('/child-dashboard'); ?>" class="inline-flex items-center px-6 py-3 bg-primary text-white font-bold rounded-full shadow-md hover:shadow-lg hover:-translate-y-1 transition transform duration-200">
                <i class="fas fa-arrow-right ml-2"></i> العودة للرئيسية
            </a>
        </div>
            
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

    </div>
</div>

<?php get_footer(); ?>