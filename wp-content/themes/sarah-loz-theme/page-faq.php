<?php
/**
 * Template Name: FAQ Page
 * Template for displaying the Frequently Asked Questions page.
 */

get_header();
?>

<main class="container mx-auto px-4 py-12">
  <div class="max-w-3xl mx-auto">
    <!-- FAQ Header -->
    <div class="text-center mb-12">
      <div class="inline-block p-4 bg-primary/10 rounded-full text-primary mb-3">
        <i class="fas fa-question-circle text-4xl floating"></i>
      </div>
      <h1 class="text-3xl font-bold text-dark mb-2"><?php the_title(); ?></h1>
      <p class="text-gray-600 text-lg">إجابات عن الأسئلة المتكررة حول موقع سارة ولوز</p>
    </div>

    <!-- FAQ Categories -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-8">
      <div class="p-6">
        <div class="flex flex-wrap mb-6 faq-categories">
          <button class="category-btn bg-primary text-white py-2 px-4 rounded-full mr-2 mb-2" data-category="all">عام</button>
          <button class="category-btn bg-white text-dark py-2 px-4 rounded-full border mr-2 mb-2" data-category="account">الحساب</button>
          <button class="category-btn bg-white text-dark py-2 px-4 rounded-full border mr-2 mb-2" data-category="content">المحتوى</button>
          <button class="category-btn bg-white text-dark py-2 px-4 rounded-full border mr-2 mb-2" data-category="subscription">الاشتراكات</button>
          <button class="category-btn bg-white text-dark py-2 px-4 rounded-full border mr-2 mb-2" data-category="technical">المشاكل التقنية</button>
        </div>
      </div>
    </div>

    <!-- FAQ Accordion -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-12">
      <div class="divide-y divide-gray-100">
        <?php
        // Check if ACF plugin is active and the 'faq_items' repeater field exists
        if (function_exists('have_rows') && have_rows('faq_items')) :
          while (have_rows('faq_items')) : the_row();
            $question = get_sub_field('question');
            $answer = get_sub_field('answer');
            $category = get_sub_field('category') ?: 'all';
        ?>
            <!-- FAQ Item -->
            <div class="faq-item" data-category="<?php echo esc_attr($category); ?>">
              <button class="faq-question w-full text-right p-6 focus:outline-none">
                <div class="flex items-center justify-between">
                  <h3 class="text-lg font-bold text-dark"><?php echo esc_html($question); ?></h3>
                  <i class="fas fa-chevron-down text-primary transition-transform"></i>
                </div>
              </button>
              <div class="faq-answer px-6 pb-6 pt-0 hidden">
                <p class="text-gray-600"><?php echo wp_kses_post($answer); ?></p>
              </div>
            </div>
        <?php
          endwhile;
        else :
          // Fallback static FAQs if ACF is not available
        ?>
            <!-- FAQ Item 1 -->
            <div class="faq-item" data-category="all">
              <button class="faq-question w-full text-right p-6 focus:outline-none">
                <div class="flex items-center justify-between">
                  <h3 class="text-lg font-bold text-dark">ما هو موقع سارة ولوز؟</h3>
                  <i class="fas fa-chevron-down text-primary transition-transform"></i>
                </div>
              </button>
              <div class="faq-answer px-6 pb-6 pt-0 hidden">
                <p class="text-gray-600">
                  موقع سارة ولوز هو منصة تعليمية تفاعلية للأطفال من عمر ٣ إلى ٩ سنوات، يهدف إلى تنمية مهارات الأطفال بطريقة ممتعة وتفاعلية من خلال الألعاب التعليمية، الأنشطة، الفيديوهات، والتحميلات المختلفة التي تساعد على تطوير مهارات الطفل اللغوية والحسابية والإبداعية.
                </p>
              </div>
            </div>
            
            <!-- FAQ Item 2 -->
            <div class="faq-item" data-category="subscription">
              <button class="faq-question w-full text-right p-6 focus:outline-none">
                <div class="flex items-center justify-between">
                  <h3 class="text-lg font-bold text-dark">هل الموقع مجاني؟</h3>
                  <i class="fas fa-chevron-down text-primary transition-transform"></i>
                </div>
              </button>
              <div class="faq-answer px-6 pb-6 pt-0 hidden">
                <p class="text-gray-600">
                  يقدم موقع سارة ولوز مجموعة من المحتويات المجانية التي يمكن للجميع الوصول إليها، بالإضافة إلى محتوى مميز متاح فقط للمشتركين. يمكنك الاطلاع على خطط الاشتراك المختلفة والمزايا المتاحة لكل خطة من خلال زيارة صفحة الاشتراكات.
                </p>
              </div>
            </div>
            
            <!-- FAQ Item 3 -->
            <div class="faq-item" data-category="account">
              <button class="faq-question w-full text-right p-6 focus:outline-none">
                <div class="flex items-center justify-between">
                  <h3 class="text-lg font-bold text-dark">كيف يمكنني إنشاء حساب؟</h3>
                  <i class="fas fa-chevron-down text-primary transition-transform"></i>
                </div>
              </button>
              <div class="faq-answer px-6 pb-6 pt-0 hidden">
                <p class="text-gray-600">
                  يمكنك إنشاء حساب جديد بسهولة من خلال النقر على "تسجيل الدخول" ثم "إنشاء حساب جديد". ستحتاج إلى تقديم بعض المعلومات الأساسية مثل البريد الإلكتروني وإنشاء كلمة مرور. كما يمكنك أيضًا التسجيل باستخدام حسابك على جوجل أو فيسبوك لتسهيل العملية.
                </p>
              </div>
            </div>
            
            <!-- FAQ Item 4 -->
            <div class="faq-item" data-category="technical">
              <button class="faq-question w-full text-right p-6 focus:outline-none">
                <div class="flex items-center justify-between">
                  <h3 class="text-lg font-bold text-dark">ما هي المتطلبات التقنية لاستخدام الموقع؟</h3>
                  <i class="fas fa-chevron-down text-primary transition-transform"></i>
                </div>
              </button>
              <div class="faq-answer px-6 pb-6 pt-0 hidden">
                <p class="text-gray-600">
                  يعمل موقع سارة ولوز على جميع المتصفحات الحديثة مثل كروم، فايرفوكس، سفاري، وإيدج. للحصول على أفضل تجربة، نوصي باستخدام أحدث إصدار من المتصفح. كما يمكن استخدام الموقع على أجهزة الكمبيوتر والأجهزة اللوحية والهواتف الذكية.
                </p>
              </div>
            </div>
            
            <!-- FAQ Item 5 -->
            <div class="faq-item" data-category="account">
              <button class="faq-question w-full text-right p-6 focus:outline-none">
                <div class="flex items-center justify-between">
                  <h3 class="text-lg font-bold text-dark">كيف يمكنني تغيير كلمة المرور الخاصة بي؟</h3>
                  <i class="fas fa-chevron-down text-primary transition-transform"></i>
                </div>
              </button>
              <div class="faq-answer px-6 pb-6 pt-0 hidden">
                <p class="text-gray-600">
                  يمكنك تغيير كلمة المرور الخاصة بك من خلال الانتقال إلى صفحة "حسابي" ثم "إعدادات الحساب" والنقر على "تغيير كلمة المرور". إذا كنت قد نسيت كلمة المرور، يمكنك استخدام خيار "نسيت كلمة المرور" في صفحة تسجيل الدخول وسنرسل لك رابطًا لإعادة تعيينها.
                </p>
              </div>
            </div>
            
            <!-- FAQ Item 6 -->
            <div class="faq-item" data-category="content">
              <button class="faq-question w-full text-right p-6 focus:outline-none">
                <div class="flex items-center justify-between">
                  <h3 class="text-lg font-bold text-dark">هل المحتوى آمن للأطفال؟</h3>
                  <i class="fas fa-chevron-down text-primary transition-transform"></i>
                </div>
              </button>
              <div class="faq-answer px-6 pb-6 pt-0 hidden">
                <p class="text-gray-600">
                  نعم، جميع محتويات موقع سارة ولوز مصممة خصيصًا للأطفال وتخضع لمراجعة دقيقة للتأكد من أنها آمنة ومناسبة للفئات العمرية المستهدفة. نحن نلتزم بتوفير بيئة تعليمية آمنة وإيجابية للأطفال.
                </p>
              </div>
            </div>
            
            <!-- FAQ Item 7 -->
            <div class="faq-item" data-category="subscription">
              <button class="faq-question w-full text-right p-6 focus:outline-none">
                <div class="flex items-center justify-between">
                  <h3 class="text-lg font-bold text-dark">كيف يمكنني الغاء اشتراكي؟</h3>
                  <i class="fas fa-chevron-down text-primary transition-transform"></i>
                </div>
              </button>
              <div class="faq-answer px-6 pb-6 pt-0 hidden">
                <p class="text-gray-600">
                  يمكنك إلغاء اشتراكك في أي وقت من خلال زيارة صفحة "حسابي" ثم "الاشتراكات" والنقر على "إلغاء الاشتراك". سيظل حسابك نشطًا حتى نهاية فترة الاشتراك الحالية. إذا واجهتك أي مشكلة، يمكنك التواصل مع فريق خدمة العملاء للمساعدة.
                </p>
              </div>
            </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Still Have Questions -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden p-6 mb-12">
      <div class="text-center">
        <div class="inline-block p-4 bg-primary/10 rounded-full text-primary mb-3">
          <i class="fas fa-comments text-3xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-dark mb-2">لم تجد إجابة لسؤالك؟</h2>
        <p class="text-gray-600 mb-6">فريق الدعم الخاص بنا جاهز للإجابة على استفساراتك</p>
        
        <div class="flex flex-wrap justify-center gap-4">
          <a href="<?php echo home_url('/اتصل-بنا/'); ?>" class="inline-flex items-center bg-primary text-white py-3 px-6 rounded-full font-bold hover:bg-primary/90 transition shadow-md">
            <i class="fas fa-envelope ml-2"></i>
            راسلنا
          </a>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
  jQuery(document).ready(function($) {
    // FAQ accordion
    $('.faq-question').on('click', function() {
      const answer = $(this).next('.faq-answer');
      const icon = $(this).find('i');
      
      // Toggle current FAQ item
      answer.toggleClass('hidden');
      icon.toggleClass('rotate-180');
      
      // Close other FAQ items
      $('.faq-question').not($(this)).each(function() {
        $(this).next('.faq-answer').addClass('hidden');
        $(this).find('i').removeClass('rotate-180');
      });
    });
    
    // Category buttons
    $('.category-btn').on('click', function() {
      const category = $(this).data('category');
      
      // Reset all buttons
      $('.category-btn').removeClass('bg-primary text-white').addClass('bg-white text-dark border');
      
      // Set active button
      $(this).removeClass('bg-white text-dark border').addClass('bg-primary text-white');
      
      // Show/hide FAQ items based on category
      if (category === 'all') {
        $('.faq-item').show();
      } else {
        $('.faq-item').hide();
        $(`.faq-item[data-category="${category}"]`).show();
      }
    });
  });
</script>

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