<?php get_header(); ?>

<div class="container mx-auto px-4 py-8">
    <?php if (have_posts()) : ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while (have_posts()) : the_post(); ?>
                <article class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="aspect-w-16 aspect-h-9">
                            <?php the_post_thumbnail('large', ['class' => 'w-full h-full object-cover']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="p-6">
                        <h2 class="text-xl font-bold mb-3">
                            <a href="<?php the_permalink(); ?>" class="text-dark hover:text-primary transition-colors">
                                <?php the_title(); ?>
                            </a>
                        </h2>
                        
                        <div class="text-gray-600 mb-4">
                            <?php the_excerpt(); ?>
                        </div>
                        
                        <a href="<?php the_permalink(); ?>" class="inline-block bg-primary text-white px-4 py-2 rounded-full hover:bg-opacity-90 transition-colors">
                            اقرأ المزيد
                        </a>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        
        <div class="mt-8">
            <?php the_posts_pagination(array(
                'prev_text' => '<i class="fas fa-chevron-right"></i>',
                'next_text' => '<i class="fas fa-chevron-left"></i>',
                'class' => 'flex justify-center space-x-2 rtl:space-x-reverse',
            )); ?>
        </div>
    <?php else : ?>
        <div class="text-center py-12">
            <h2 class="text-2xl font-bold text-gray-700 mb-4">لا يوجد محتوى</h2>
            <p class="text-gray-600">عذراً، لا يوجد محتوى متاح حالياً.</p>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
