<?php
/**
 * Template Name: Contact Us
 * Template for displaying the Contact Us page.
 */

get_header();
?>

<main class="container mx-auto px-4 py-12">
  <div class="max-w-3xl mx-auto">
    <!-- Contact Page Header -->
    <div class="text-center mb-12">
      <div class="inline-block p-4 bg-primary/10 rounded-full text-primary mb-3">
        <i class="fas fa-envelope text-4xl floating"></i>
      </div>
      <h1 class="text-3xl font-bold text-dark mb-2"><?php the_title(); ?></h1>
      <p class="text-gray-600 text-lg">لديك سؤال أو استفسار؟ تواصل معنا وسنرد عليك في أقرب وقت</p>
    </div>

    <!-- Contact Form Section -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-12">
      <div class="p-8">
        <!-- Contact Form -->
        <div class="mb-8">
          <?php 
          // Display the contact form from Contact Form 7 or any other form plugin
          if( function_exists('wpcf7_contact_form') ) {
              // You need to replace '123' with the actual Contact Form 7 ID
              echo do_shortcode('[contact-form-7 id="123" title="نموذج الاتصال"]'); 
          } else {
              // Fallback message if Contact Form 7 is not active
              echo '<p class="text-center">نموذج الاتصال غير متاح حاليًا. يرجى المحاولة لاحقًا.</p>';
          }
          ?>
        </div>
      </div>
    </div>

    <!-- Contact Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
      <!-- Contact Details -->
      <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition">
        <div class="p-6">
          <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-map-marker-alt text-primary text-xl"></i>
          </div>
          <h2 class="text-xl font-bold text-dark mb-3">معلومات الاتصال</h2>
          <ul class="space-y-3 text-gray-600">
            <li class="flex items-start">
              <i class="fas fa-envelope mt-1 ml-2 text-primary"></i>
              <a href="mailto:info@saralouze.com" class="hover:text-primary">info@saralouze.com</a>
            </li>
            <li class="flex items-start">
              <i class="fas fa-phone mt-1 ml-2 text-primary"></i>
              <a href="tel:+966123456789" class="hover:text-primary">+966 12 345 6789</a>
            </li>
            <li class="flex items-start">
              <i class="fas fa-map-marker-alt mt-1 ml-2 text-primary"></i>
              <span>سارة ولوز، ص.ب 12345، الرياض، المملكة العربية السعودية</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Follow Us -->
      <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition">
        <div class="p-6">
          <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-share-alt text-primary text-xl"></i>
          </div>
          <h2 class="text-xl font-bold text-dark mb-3">تابعنا على</h2>
          <div class="flex space-x-6 space-x-reverse">
            <a href="#" class="w-12 h-12 bg-[#3b5998]/10 rounded-full flex items-center justify-center text-[#3b5998] hover:bg-[#3b5998] hover:text-white transition-colors">
              <i class="fab fa-facebook-f text-xl"></i>
            </a>
            <a href="#" class="w-12 h-12 bg-[#1da1f2]/10 rounded-full flex items-center justify-center text-[#1da1f2] hover:bg-[#1da1f2] hover:text-white transition-colors">
              <i class="fab fa-twitter text-xl"></i>
            </a>
            <a href="#" class="w-12 h-12 bg-[#e1306c]/10 rounded-full flex items-center justify-center text-[#e1306c] hover:bg-[#e1306c] hover:text-white transition-colors">
              <i class="fab fa-instagram text-xl"></i>
            </a>
            <a href="#" class="w-12 h-12 bg-[#ff0000]/10 rounded-full flex items-center justify-center text-[#ff0000] hover:bg-[#ff0000] hover:text-white transition-colors">
              <i class="fab fa-youtube text-xl"></i>
            </a>
          </div>
        </div>
      </div>
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

/* Contact Form 7 custom styling */
.wpcf7 form {
  @apply space-y-4;
}

.wpcf7 label {
  @apply block text-gray-700 mb-2 font-medium;
}

.wpcf7 input[type="text"],
.wpcf7 input[type="email"],
.wpcf7 input[type="tel"],
.wpcf7 textarea {
  @apply w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent;
}

.wpcf7 textarea {
  @apply h-32;
}

.wpcf7 input[type="submit"] {
  @apply bg-primary text-white px-6 py-3 rounded-full hover:bg-opacity-90 transition-colors w-full md:w-auto;
}

.wpcf7 .wpcf7-validation-errors {
  @apply bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4;
}

.wpcf7 .wpcf7-mail-sent-ok {
  @apply bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mt-4;
}
</style>

<?php get_footer(); ?> 