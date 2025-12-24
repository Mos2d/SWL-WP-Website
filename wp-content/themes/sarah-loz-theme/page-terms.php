<?php
/**
 * Template Name: Terms of Use
 * Template for displaying the Terms of Use page.
 */

get_header();
?>

<main class="container mx-auto px-4 py-12">
  <div class="max-w-3xl mx-auto">
    <!-- Terms Header -->
    <div class="text-center mb-12">
      <div class="inline-block p-4 bg-primary/10 rounded-full text-primary mb-3">
        <i class="fas fa-file-contract text-4xl floating"></i>
      </div>
      <h1 class="text-3xl font-bold text-dark mb-2"><?php the_title(); ?></h1>
      <p class="text-gray-600 text-lg">الشروط والأحكام لاستخدام موقع سارة ولوز</p>
      <?php 
      $last_updated = get_post_meta(get_the_ID(), 'last_updated', true);
      if ($last_updated) : 
      ?>
        <p class="text-gray-500 text-sm mt-2">آخر تحديث: <?php echo esc_html($last_updated); ?></p>
      <?php else : ?>
        <p class="text-gray-500 text-sm mt-2">آخر تحديث: ١ يناير ٢٠٢٣</p>
      <?php endif; ?>
    </div>

    <!-- Terms Content -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-12 p-8">
      <?php
      if (have_posts()) :
        while (have_posts()) :
          the_post();
          
          // Get the post content
          $content = get_the_content();
          
          // Check if content is empty, then use fallback content
          if (!empty($content)) :
            the_content();
          else :
      ?>
          <!-- Introduction -->
          <div class="mb-8">
            <h2 class="text-2xl font-bold text-dark mb-4">مقدمة</h2>
            <p class="text-gray-600 mb-3">
              مرحبًا بك في موقع سارة ولوز ("الموقع"). تحدد شروط الاستخدام هذه القواعد والأحكام لاستخدام موقعنا.
            </p>
            <p class="text-gray-600">
              باستخدامك لموقعنا، فإنك توافق على الالتزام بهذه الشروط بالكامل. إذا كنت لا توافق على أي جزء من هذه الشروط، فيجب عليك عدم استخدام موقعنا. يرجى قراءة هذه الشروط بعناية قبل البدء في استخدام الموقع.
            </p>
          </div>

          <!-- Account Terms -->
          <div class="mb-8">
            <h2 class="text-2xl font-bold text-dark mb-4">شروط الحساب</h2>
            
            <h3 class="text-xl font-bold text-dark mb-2">إنشاء الحساب</h3>
            <p class="text-gray-600 mb-3">
              عند إنشاء حساب على موقعنا، يجب عليك تقديم معلومات دقيقة وكاملة وحديثة في جميع الأوقات. قد يؤدي عدم القيام بذلك إلى إنهاء حسابك على موقعنا.
            </p>
            
            <h3 class="text-xl font-bold text-dark mb-2">مسؤولية الحساب</h3>
            <p class="text-gray-600 mb-3">
              أنت مسؤول عن الحفاظ على سرية كلمة المرور الخاصة بحسابك وعن جميع الأنشطة التي تحدث تحت حسابك. يجب عليك إخطارنا فورًا بأي استخدام غير مصرح به لحسابك أو أي خرق آخر للأمان.
            </p>
            
            <h3 class="text-xl font-bold text-dark mb-2">حسابات الأطفال</h3>
            <p class="text-gray-600 mb-3">
              موقعنا مصمم للأطفال من عمر ٣ إلى ٩ سنوات، ولكننا نتوقع أن يكون إنشاء الحسابات وإدارتها من قبل الوالدين أو الأوصياء القانونيين. يجب أن يكون لدى الأطفال دون سن ١٣ عامًا موافقة والديهم أو الوصي القانوني عليهم لاستخدام الموقع.
            </p>
          </div>

          <!-- Service Terms -->
          <div class="mb-8">
            <h2 class="text-2xl font-bold text-dark mb-4">شروط الخدمة</h2>
            
            <h3 class="text-xl font-bold text-dark mb-2">استخدام المحتوى</h3>
            <p class="text-gray-600 mb-3">
              يوفر موقع سارة ولوز محتوى تعليميًا وترفيهيًا للأطفال. هذا المحتوى، بما في ذلك النصوص والصور والرسومات والصوت والفيديو، محمي بموجب قوانين حقوق النشر والعلامات التجارية والحقوق الأخرى.
            </p>
            
            <h3 class="text-xl font-bold text-dark mb-2">القيود على الاستخدام</h3>
            <p class="text-gray-600 mb-3">
              لا يجوز لك:
            </p>
            <ul class="list-disc list-inside text-gray-600 mr-6 mb-4 space-y-1">
              <li>نسخ أو إعادة إنتاج أو توزيع أو نشر أو تنزيل أي جزء من المحتوى لأغراض تجارية</li>
              <li>استخدام الموقع لأي غرض غير قانوني أو ضار</li>
              <li>محاولة الوصول إلى أجزاء مقيدة من الموقع</li>
              <li>استخدام برامج آلية للوصول إلى الموقع أو استخراج البيانات منه</li>
              <li>نشر أو تحميل أي محتوى غير لائق أو تشهيري أو مسيء</li>
              <li>استخدام الموقع بطريقة قد تعطل أو تضر بالموقع أو تعيق وصول الآخرين إليه</li>
            </ul>
            
            <h3 class="text-xl font-bold text-dark mb-2">التغييرات في الخدمة</h3>
            <p class="text-gray-600 mb-3">
              نحتفظ بالحق في تغيير المحتوى أو تعديله أو تعليقه أو إنهائه، بشكل مؤقت أو دائم، دون إشعار مسبق. لن نكون مسؤولين تجاهك أو تجاه أي طرف ثالث عن أي تغيير أو تعليق أو إنهاء للخدمة.
            </p>
          </div>

          <!-- Subscription and Payments -->
          <div class="mb-8">
            <h2 class="text-2xl font-bold text-dark mb-4">الاشتراكات والمدفوعات</h2>
            
            <h3 class="text-xl font-bold text-dark mb-2">أنواع الاشتراكات</h3>
            <p class="text-gray-600 mb-3">
              قد نقدم اشتراكات مجانية ومدفوعة لمحتوى موقعنا. تخضع الاشتراكات المدفوعة للرسوم المعلنة وقت الشراء. قد نغير رسوم الاشتراك من وقت لآخر، وسنخطرك بأي تغييرات في الأسعار قبل أن تصبح سارية.
            </p>
            
            <h3 class="text-xl font-bold text-dark mb-2">التجديد التلقائي</h3>
            <p class="text-gray-600 mb-3">
              تتجدد الاشتراكات المدفوعة تلقائيًا في نهاية فترة الاشتراك ما لم تلغيها قبل تاريخ التجديد. يمكنك إلغاء التجديد التلقائي في أي وقت من خلال إعدادات حسابك.
            </p>
            
            <h3 class="text-xl font-bold text-dark mb-2">سياسة الاسترداد</h3>
            <p class="text-gray-600 mb-3">
              قد نوفر فترة استرداد محدودة للاشتراكات الجديدة. بعد هذه الفترة، لا تكون المدفوعات قابلة للاسترداد. يرجى الاطلاع على سياسة الاسترداد الخاصة بنا للحصول على تفاصيل محددة.
            </p>
          </div>
            
          <!-- Additional sections omitted for brevity -->
          
          <!-- Contact -->
          <div>
            <h2 class="text-2xl font-bold text-dark mb-4">اتصل بنا</h2>
            <p class="text-gray-600 mb-3">
              إذا كان لديك أي أسئلة حول شروط الاستخدام هذه، يرجى الاتصال بنا على:
            </p>
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="flex items-center">
                <i class="fas fa-envelope ml-2 text-primary"></i>
                <a href="mailto:terms@saralouze.com" class="text-primary hover:underline">terms@saralouze.com</a>
              </p>
              <p class="flex items-center mt-2">
                <i class="fas fa-map-marker-alt ml-2 text-primary"></i>
                <span>سارة ولوز، ص.ب 12345، الرياض، المملكة العربية السعودية</span>
              </p>
            </div>
          </div>
      <?php 
          endif;
        endwhile;
      endif;
      ?>
    </div>

    <!-- Quick Links -->
    <div class="flex flex-wrap justify-center gap-4 mb-8">
      <a href="<?php echo home_url('/المساعدة/'); ?>" class="inline-flex items-center bg-white text-dark py-2 px-4 rounded-full hover:bg-light transition shadow-sm">
        <i class="fas fa-life-ring ml-2 text-primary"></i>
        المساعدة
      </a>
      <a href="<?php echo home_url('/الأسئلة-الشائعة/'); ?>" class="inline-flex items-center bg-white text-dark py-2 px-4 rounded-full hover:bg-light transition shadow-sm">
        <i class="fas fa-question-circle ml-2 text-primary"></i>
        الأسئلة الشائعة
      </a>
      <a href="<?php echo home_url('/سياسة-الخصوصية/'); ?>" class="inline-flex items-center bg-white text-dark py-2 px-4 rounded-full hover:bg-light transition shadow-sm">
        <i class="fas fa-shield-alt ml-2 text-primary"></i>
        سياسة الخصوصية
      </a>
      <a href="<?php echo home_url('/اتصل-بنا/'); ?>" class="inline-flex items-center bg-white text-dark py-2 px-4 rounded-full hover:bg-light transition shadow-sm">
        <i class="fas fa-envelope ml-2 text-primary"></i>
        اتصل بنا
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