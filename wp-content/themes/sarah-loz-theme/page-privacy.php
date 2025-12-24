<?php
/**
 * Template Name: Privacy Policy
 * Template for displaying the Privacy Policy page.
 */

get_header();
?>

<main class="container mx-auto px-4 py-12">
  <div class="max-w-3xl mx-auto">
    <!-- Privacy Policy Header -->
    <div class="text-center mb-12">
      <div class="inline-block p-4 bg-primary/10 rounded-full text-primary mb-3">
        <i class="fas fa-shield-alt text-4xl floating"></i>
      </div>
      <h1 class="text-3xl font-bold text-dark mb-2"><?php the_title(); ?></h1>
      <p class="text-gray-600 text-lg">سياسة الخصوصية لموقع سارة ولوز</p>
      <?php 
      $last_updated = get_post_meta(get_the_ID(), 'last_updated', true);
      if ($last_updated) : 
      ?>
        <p class="text-gray-500 text-sm mt-2">آخر تحديث: <?php echo esc_html($last_updated); ?></p>
      <?php else : ?>
        <p class="text-gray-500 text-sm mt-2">آخر تحديث: ١ يناير ٢٠٢٣</p>
      <?php endif; ?>
    </div>

    <!-- Privacy Policy Content -->
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
              نحن في سارة ولوز نقدر خصوصيتك ونلتزم بحمايتها. توضح سياسة الخصوصية هذه كيفية جمعنا واستخدامنا للمعلومات الشخصية عند استخدام موقعنا.
            </p>
            <p class="text-gray-600">
              باستخدامك لموقعنا، فإنك توافق على جمع واستخدام المعلومات وفقًا لهذه السياسة. نحن نجمع معلوماتك الشخصية لتوفير وتحسين خدماتنا. لن نستخدم معلوماتك أو نشاركها مع أي شخص إلا كما هو موضح في سياسة الخصوصية هذه.
            </p>
          </div>

          <!-- Information We Collect -->
          <div class="mb-8">
            <h2 class="text-2xl font-bold text-dark mb-4">المعلومات التي نجمعها</h2>
            
            <h3 class="text-xl font-bold text-dark mb-2">معلومات الحساب</h3>
            <p class="text-gray-600 mb-3">
              عند إنشاء حساب على موقعنا، قد نطلب منك تقديم معلومات شخصية معينة، بما في ذلك، على سبيل المثال لا الحصر، اسمك، وعنوان بريدك الإلكتروني، واسم الطفل وعمره.
            </p>
            
            <h3 class="text-xl font-bold text-dark mb-2">معلومات الاستخدام</h3>
            <p class="text-gray-600 mb-3">
              نجمع أيضًا معلومات حول كيفية استخدامك للموقع، بما في ذلك التفاعلات مع المحتوى، والإنجازات، والتقدم في الألعاب والأنشطة. نستخدم هذه المعلومات لتخصيص تجربة التعلم وتحسين خدماتنا.
            </p>
            
            <h3 class="text-xl font-bold text-dark mb-2">معلومات الجهاز</h3>
            <p class="text-gray-600 mb-3">
              قد نجمع معلومات عن جهازك، بما في ذلك نوع المتصفح، ونظام التشغيل، وعنوان IP، وذلك لتحسين أداء الموقع وتجربة المستخدم.
            </p>
          </div>

          <!-- How We Use Information -->
          <div class="mb-8">
            <h2 class="text-2xl font-bold text-dark mb-4">كيفية استخدام المعلومات</h2>
            
            <p class="text-gray-600 mb-3">
              نستخدم المعلومات التي نجمعها للأغراض التالية:
            </p>
            <ul class="list-disc list-inside text-gray-600 mr-6 mb-4 space-y-1">
              <li>توفير وصيانة وتحسين موقعنا وخدماتنا</li>
              <li>تخصيص تجربة التعلم لطفلك</li>
              <li>إرسال معلومات حول التحديثات والعروض الجديدة</li>
              <li>مراقبة استخدام موقعنا وتحليل اتجاهات الاستخدام</li>
              <li>اكتشاف ومنع الاحتيال والإساءة</li>
              <li>الامتثال لالتزاماتنا القانونية</li>
            </ul>
          </div>

          <!-- Sharing of Information -->
          <div class="mb-8">
            <h2 class="text-2xl font-bold text-dark mb-4">مشاركة المعلومات</h2>
            
            <p class="text-gray-600 mb-3">
              لن نبيع أو نؤجر أو نتاجر بمعلوماتك الشخصية مع أطراف ثالثة. ومع ذلك، قد نشارك معلوماتك في الحالات التالية:
            </p>
            <ul class="list-disc list-inside text-gray-600 mr-6 mb-4 space-y-1">
              <li>مع مقدمي الخدمات الذين يساعدوننا في تشغيل موقعنا</li>
              <li>للامتثال للقانون أو استجابة لطلبات قانونية</li>
              <li>لحماية حقوقنا وملكيتنا وسلامتنا وحقوق وملكية وسلامة مستخدمينا أو الجمهور</li>
              <li>في حالة الاندماج أو الاستحواذ أو بيع الأصول</li>
            </ul>
          </div>

          <!-- Children's Privacy -->
          <div class="mb-8">
            <h2 class="text-2xl font-bold text-dark mb-4">خصوصية الأطفال</h2>
            
            <p class="text-gray-600 mb-3">
              موقعنا موجه للأطفال، ولكنه يتطلب إشراف ومشاركة الوالدين. نحن نلتزم بحماية خصوصية الأطفال وفقًا للقوانين المعمول بها.
            </p>
            <p class="text-gray-600 mb-3">
              لن نجمع عن عمد معلومات شخصية من الأطفال دون سن ١٣ عامًا دون موافقة الوالدين. إذا كنت تعتقد أننا قد جمعنا معلومات من طفل دون موافقة الوالدين، يرجى الاتصال بنا وسنتخذ الخطوات اللازمة لإزالة هذه المعلومات.
            </p>
          </div>

          <!-- Cookies and Tracking -->
          <div class="mb-8">
            <h2 class="text-2xl font-bold text-dark mb-4">ملفات تعريف الارتباط والتتبع</h2>
            
            <p class="text-gray-600 mb-3">
              نستخدم ملفات تعريف الارتباط وتقنيات مماثلة لتتبع نشاط موقعنا وتخزين معلومات معينة. ملفات تعريف الارتباط هي ملفات تحتوي على كمية صغيرة من البيانات وقد تتضمن معرفًا فريدًا مجهولاً.
            </p>
            <p class="text-gray-600 mb-3">
              يمكنك توجيه متصفحك لرفض جميع ملفات تعريف الارتباط أو للإشارة عند إرسال ملف تعريف ارتباط. ومع ذلك، إذا كنت لا تقبل ملفات تعريف الارتباط، فقد لا تتمكن من استخدام بعض أجزاء موقعنا.
            </p>
          </div>

          <!-- Data Security -->
          <div class="mb-8">
            <h2 class="text-2xl font-bold text-dark mb-4">أمن البيانات</h2>
            
            <p class="text-gray-600 mb-3">
              أمن معلوماتك مهم بالنسبة لنا، ولكن لا يوجد طريقة نقل عبر الإنترنت أو طريقة تخزين إلكتروني آمنة بنسبة 100٪. في حين أننا نسعى جاهدين لاستخدام وسائل مقبولة تجاريًا لحماية معلوماتك الشخصية، لا يمكننا ضمان أمنها المطلق.
            </p>
          </div>

          <!-- Changes to Privacy Policy -->
          <div class="mb-8">
            <h2 class="text-2xl font-bold text-dark mb-4">التغييرات على سياسة الخصوصية</h2>
            
            <p class="text-gray-600 mb-3">
              قد نقوم بتحديث سياسة الخصوصية الخاصة بنا من وقت لآخر. سنخطرك بأي تغييرات عن طريق نشر سياسة الخصوصية الجديدة على هذه الصفحة وتحديث تاريخ "آخر تحديث" في أعلى هذه الصفحة.
            </p>
            <p class="text-gray-600 mb-3">
              يُنصح بمراجعة سياسة الخصوصية هذه بشكل دوري للتغييرات. تصبح التغييرات على سياسة الخصوصية هذه فعالة عند نشرها على هذه الصفحة.
            </p>
          </div>

          <!-- Contact -->
          <div>
            <h2 class="text-2xl font-bold text-dark mb-4">اتصل بنا</h2>
            <p class="text-gray-600 mb-3">
              إذا كان لديك أي أسئلة حول سياسة الخصوصية الخاصة بنا، يرجى الاتصال بنا على:
            </p>
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="flex items-center">
                <i class="fas fa-envelope ml-2 text-primary"></i>
                <a href="mailto:privacy@saralouze.com" class="text-primary hover:underline">privacy@saralouze.com</a>
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
      <a href="<?php echo home_url('/شروط-الاستخدام/'); ?>" class="inline-flex items-center bg-white text-dark py-2 px-4 rounded-full hover:bg-light transition shadow-sm">
        <i class="fas fa-file-contract ml-2 text-primary"></i>
        شروط الاستخدام
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