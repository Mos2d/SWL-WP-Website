<?php
/**
 * Template Name: Help Center
 * Template for displaying the Help Center page.
 */

get_header();
?>

<main class="container mx-auto px-4 py-12">
  <div class="max-w-4xl mx-auto">
    <!-- Help Page Header -->
    <div class="text-center mb-12">
      <div class="inline-block p-4 bg-primary/10 rounded-full text-primary mb-3">
        <i class="fas fa-life-ring text-4xl floating"></i>
      </div>
      <h1 class="text-3xl font-bold text-dark mb-2"><?php the_title(); ?></h1>
      <p class="text-gray-600 text-lg">كيف يمكننا مساعدتك؟</p>
    </div>

    <!-- Help Categories -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
      <?php
      // Check if ACF plugin is active and if help categories are defined
      if (function_exists('have_rows') && have_rows('help_categories')) :
        while (have_rows('help_categories')) : the_row();
          $title = get_sub_field('title');
          $description = get_sub_field('description');
          $icon = get_sub_field('icon') ?: 'fa-rocket';
      ?>
        <!-- Help Category -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition">
          <div class="p-6">
            <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-4">
              <i class="fas <?php echo esc_attr($icon); ?> text-primary text-xl"></i>
            </div>
            <h2 class="text-xl font-bold text-dark mb-3"><?php echo esc_html($title); ?></h2>
            <p class="text-gray-600 mb-4"><?php echo esc_html($description); ?></p>
            
            <?php if (have_rows('help_links')) : ?>
              <ul class="space-y-2 text-gray-600">
                <?php while (have_rows('help_links')) : the_row(); 
                  $link_text = get_sub_field('link_text');
                  $link_url = get_sub_field('link_url');
                ?>
                  <li class="flex items-start">
                    <i class="fas fa-check-circle text-secondary mt-1 ml-2"></i>
                    <a href="<?php echo esc_url($link_url); ?>" class="hover:text-primary"><?php echo esc_html($link_text); ?></a>
                  </li>
                <?php endwhile; ?>
              </ul>
            <?php endif; ?>
          </div>
        </div>
      <?php
        endwhile;
      else :
        // Fallback static categories if ACF is not available
      ?>
        <!-- Getting Started -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition">
          <div class="p-6">
            <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-4">
              <i class="fas fa-rocket text-primary text-xl"></i>
            </div>
            <h2 class="text-xl font-bold text-dark mb-3">البداية مع سارة ولوز</h2>
            <p class="text-gray-600 mb-4">دليلك للبدء باستخدام الموقع والاستفادة من محتواه التعليمي</p>
            <ul class="space-y-2 text-gray-600">
              <li class="flex items-start">
                <i class="fas fa-check-circle text-secondary mt-1 ml-2"></i>
                <a href="#" class="hover:text-primary">كيفية إنشاء حساب جديد</a>
              </li>
              <li class="flex items-start">
                <i class="fas fa-check-circle text-secondary mt-1 ml-2"></i>
                <a href="#" class="hover:text-primary">استكشاف المحتوى التعليمي</a>
              </li>
              <li class="flex items-start">
                <i class="fas fa-check-circle text-secondary mt-1 ml-2"></i>
                <a href="#" class="hover:text-primary">ضبط الإعدادات لتجربة أفضل</a>
              </li>
            </ul>
          </div>
        </div>

        <!-- Account & Profile -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition">
          <div class="p-6">
            <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-4">
              <i class="fas fa-user-circle text-primary text-xl"></i>
            </div>
            <h2 class="text-xl font-bold text-dark mb-3">الحساب والملف الشخصي</h2>
            <p class="text-gray-600 mb-4">كل ما يتعلق بإدارة حسابك ومعلوماتك الشخصية</p>
            <ul class="space-y-2 text-gray-600">
              <li class="flex items-start">
                <i class="fas fa-check-circle text-secondary mt-1 ml-2"></i>
                <a href="#" class="hover:text-primary">تعديل بيانات الحساب</a>
              </li>
              <li class="flex items-start">
                <i class="fas fa-check-circle text-secondary mt-1 ml-2"></i>
                <a href="#" class="hover:text-primary">استعادة كلمة المرور</a>
              </li>
              <li class="flex items-start">
                <i class="fas fa-check-circle text-secondary mt-1 ml-2"></i>
                <a href="#" class="hover:text-primary">إدارة الاشتراكات والمدفوعات</a>
              </li>
            </ul>
          </div>
        </div>

        <!-- Content & Activities -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition">
          <div class="p-6">
            <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-4">
              <i class="fas fa-gamepad text-primary text-xl"></i>
            </div>
            <h2 class="text-xl font-bold text-dark mb-3">المحتوى والأنشطة</h2>
            <p class="text-gray-600 mb-4">مساعدة حول الألعاب والأنشطة والفيديوهات التعليمية</p>
            <ul class="space-y-2 text-gray-600">
              <li class="flex items-start">
                <i class="fas fa-check-circle text-secondary mt-1 ml-2"></i>
                <a href="#" class="hover:text-primary">كيفية اللعب والتفاعل</a>
              </li>
              <li class="flex items-start">
                <i class="fas fa-check-circle text-secondary mt-1 ml-2"></i>
                <a href="#" class="hover:text-primary">متطلبات تشغيل المحتوى</a>
              </li>
              <li class="flex items-start">
                <i class="fas fa-check-circle text-secondary mt-1 ml-2"></i>
                <a href="#" class="hover:text-primary">حل مشاكل تشغيل الفيديوهات</a>
              </li>
            </ul>
          </div>
        </div>

        <!-- Technical Support -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition">
          <div class="p-6">
            <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-4">
              <i class="fas fa-tools text-primary text-xl"></i>
            </div>
            <h2 class="text-xl font-bold text-dark mb-3">الدعم الفني</h2>
            <p class="text-gray-600 mb-4">حل المشكلات التقنية والإبلاغ عن الأخطاء</p>
            <ul class="space-y-2 text-gray-600">
              <li class="flex items-start">
                <i class="fas fa-check-circle text-secondary mt-1 ml-2"></i>
                <a href="#" class="hover:text-primary">مشاكل تسجيل الدخول</a>
              </li>
              <li class="flex items-start">
                <i class="fas fa-check-circle text-secondary mt-1 ml-2"></i>
                <a href="#" class="hover:text-primary">الإبلاغ عن خطأ أو مشكلة</a>
              </li>
              <li class="flex items-start">
                <i class="fas fa-check-circle text-secondary mt-1 ml-2"></i>
                <a href="#" class="hover:text-primary">متطلبات النظام والمتصفح</a>
              </li>
            </ul>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- Contact Support -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden p-6 mb-12">
      <div class="text-center mb-6">
        <div class="inline-block p-4 bg-primary/10 rounded-full text-primary mb-3">
          <i class="fas fa-headset text-3xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-dark mb-2">لم تجد ما تبحث عنه؟</h2>
        <p class="text-gray-600">فريق الدعم الخاص بنا جاهز لمساعدتك</p>
      </div>
      
      <div class="flex flex-wrap justify-center gap-4">
        <a href="<?php echo home_url('/اتصل-بنا/'); ?>" class="inline-flex items-center bg-primary text-white py-3 px-6 rounded-full font-bold hover:bg-primary/90 transition shadow-md">
          <i class="fas fa-envelope ml-2"></i>
          راسلنا
        </a>
        <a href="#" class="inline-flex items-center bg-secondary text-white py-3 px-6 rounded-full font-bold hover:bg-secondary/90 transition shadow-md">
          <i class="fas fa-comments ml-2"></i>
          الدردشة المباشرة
        </a>
        <a href="tel:+966123456789" class="inline-flex items-center bg-accent text-dark py-3 px-6 rounded-full font-bold hover:bg-accent/90 transition shadow-md">
          <i class="fas fa-phone ml-2"></i>
          اتصل بنا
        </a>
      </div>
    </div>
    
    <!-- Quick Help Links -->
    <div class="flex flex-wrap justify-center gap-4 mb-8">
      <a href="<?php echo home_url('/الأسئلة-الشائعة/'); ?>" class="inline-flex items-center bg-white text-dark py-2 px-4 rounded-full hover:bg-light transition shadow-sm">
        <i class="fas fa-question-circle ml-2 text-primary"></i>
        الأسئلة الشائعة
      </a>
      <a href="<?php echo home_url('/سياسة-الخصوصية/'); ?>" class="inline-flex items-center bg-white text-dark py-2 px-4 rounded-full hover:bg-light transition shadow-sm">
        <i class="fas fa-shield-alt ml-2 text-primary"></i>
        سياسة الخصوصية
      </a>
      <a href="<?php echo home_url('/شروط-الاستخدام/'); ?>" class="inline-flex items-center bg-white text-dark py-2 px-4 rounded-full hover:bg-light transition shadow-sm">
        <i class="fas fa-file-contract ml-2 text-primary"></i>
        شروط الاستخدام
      </a>
    </div>
  </div>
</main>

<style>
.floating {
  animation: float 3s ease-in-out infinite;
}
@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}
</style>

<?php get_footer(); ?> 