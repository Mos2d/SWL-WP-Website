</main>

<!-- Footer -->
<footer class="bg-dark text-white py-10">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- About Column -->
            <div>
                <h3 class="text-xl font-bold mb-4"><?php bloginfo('name'); ?></h3>
                <p class="text-gray-300 mb-4">
                    <?php bloginfo('description'); ?>
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-xl font-bold mb-4">روابط سريعة</h3>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'footer',
                    'container' => false,
                    'menu_class' => 'space-y-2',
                    'fallback_cb' => false,
                    'link_before' => '<span class="text-gray-300 hover:text-white">',
                    'link_after' => '</span>',
                ));
                ?>
            </div>

            <!-- WooCommerce Links -->
            <div>
                <h3 class="text-xl font-bold mb-4">خدمة العملاء</h3>
                <ul class="space-y-2">
                    <?php if (class_exists('WooCommerce')) : ?>
                        <li>
                            <a href="<?php echo wc_get_page_permalink('shop'); ?>" class="text-gray-300 hover:text-white">المتجر</a>
                        </li>
                        <li>
                            <a href="<?php echo wc_get_page_permalink('cart'); ?>" class="text-gray-300 hover:text-white">سلة التسوق</a>
                        </li>
                        <li>
                            <a href="<?php echo wc_get_page_permalink('myaccount'); ?>" class="text-gray-300 hover:text-white">حسابي</a>
                        </li>
                        <?php if (wc_get_page_id('terms') > 0) : ?>
                            <li>
                                <a href="<?php echo get_permalink(wc_get_page_id('terms')); ?>" class="text-gray-300 hover:text-white">سياسة الخصوصية</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h3 class="text-xl font-bold mb-4">تواصل معنا</h3>
                <ul class="space-y-2">
                    <?php if ($email = get_theme_mod('sarah_loz_email', 'info@sarahloz.com')) : ?>
                        <li class="flex items-center">
                            <i class="fas fa-envelope ml-2 text-accent"></i>
                            <a href="mailto:<?php echo esc_attr($email); ?>" class="text-gray-300 hover:text-white"><?php echo esc_html($email); ?></a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if ($phone = get_theme_mod('sarah_loz_phone', '+123 456 789')) : ?>
                        <li class="flex items-center">
                            <i class="fas fa-phone ml-2 text-accent"></i>
                            <a href="tel:<?php echo esc_attr($phone); ?>" class="text-gray-300 hover:text-white"><?php echo esc_html($phone); ?></a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <div class="mt-4">
                    <h4 class="text-lg font-bold mb-2">تابعنا على</h4>
                    <div class="flex space-x-4 rtl:space-x-reverse">
                        <?php if ($facebook = get_theme_mod('sarah_loz_facebook', '#')) : ?>
                            <a href="<?php echo esc_url($facebook); ?>" class="text-white hover:text-accent transition">
                                <i class="fab fa-facebook-f text-xl"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($twitter = get_theme_mod('sarah_loz_twitter', '#')) : ?>
                            <a href="<?php echo esc_url($twitter); ?>" class="text-white hover:text-accent transition">
                                <i class="fab fa-twitter text-xl"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($instagram = get_theme_mod('sarah_loz_instagram', '#')) : ?>
                            <a href="<?php echo esc_url($instagram); ?>" class="text-white hover:text-accent transition">
                                <i class="fab fa-instagram text-xl"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($youtube = get_theme_mod('sarah_loz_youtube', '#')) : ?>
                            <a href="<?php echo esc_url($youtube); ?>" class="text-white hover:text-accent transition">
                                <i class="fab fa-youtube text-xl"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-8 pt-8 text-center">
            <p class="text-gray-400">
                &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?> - جميع الحقوق محفوظة
            </p>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html> 