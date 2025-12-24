<?php
/**
 * Footer Widgets Template Part
 */
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <!-- Footer Navigation -->
    <div>
        <h3 class="text-xl font-bold mb-4">روابط سريعة</h3>
        <?php
        wp_nav_menu(array(
            'theme_location' => 'footer',
            'container' => false,
            'menu_class' => 'space-y-2',
            'fallback_cb' => false,
        ));
        ?>
    </div>
    
    <!-- Contact Info -->
    <div>
        <h3 class="text-xl font-bold mb-4">تواصل معنا</h3>
        <div class="space-y-2">
            <?php if ($email = get_theme_mod('contact_email')) : ?>
                <p><i class="fas fa-envelope ml-2"></i><?php echo esc_html($email); ?></p>
            <?php endif; ?>
            <?php if ($phone = get_theme_mod('contact_phone')) : ?>
                <p><i class="fas fa-phone ml-2"></i><?php echo esc_html($phone); ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Social Media -->
    <div>
        <h3 class="text-xl font-bold mb-4">تابعنا</h3>
        <div class="flex space-x-4 rtl:space-x-reverse">
            <?php
            $social_platforms = array(
                'facebook' => array('icon' => 'fab fa-facebook-f', 'label' => 'Facebook'),
                'twitter' => array('icon' => 'fab fa-twitter', 'label' => 'Twitter'),
                'instagram' => array('icon' => 'fab fa-instagram', 'label' => 'Instagram'),
                'youtube' => array('icon' => 'fab fa-youtube', 'label' => 'YouTube')
            );

            foreach ($social_platforms as $platform => $data) {
                if ($url = get_theme_mod($platform . '_url')) :
                    ?>
                    <a href="<?php echo esc_url($url); ?>" 
                       class="text-light hover:text-accent transition-colors" 
                       target="_blank"
                       aria-label="<?php echo esc_attr($data['label']); ?>">
                        <i class="<?php echo esc_attr($data['icon']); ?> text-2xl"></i>
                    </a>
                    <?php
                endif;
            }
            ?>
        </div>
    </div>
</div>

<div class="mt-8 pt-8 border-t border-gray-700 text-center">
    <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. جميع الحقوق محفوظة.</p>
</div>
