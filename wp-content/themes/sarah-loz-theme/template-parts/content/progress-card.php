<?php
/**
 * Progress Card Template Part
 * 
 * @param string $title Card title
 * @param string $count Number/count to display
 * @param string $label Label for the count
 * @param string $icon Font Awesome icon class
 * @param string $color Color class (primary, secondary, accent)
 * @param string $link Optional link URL
 */

$title = $args['title'] ?? '';
$count = $args['count'] ?? 0;
$label = $args['label'] ?? '';
$icon = $args['icon'] ?? 'fa-star';
$color = $args['color'] ?? 'primary';
$link = $args['link'] ?? '';
?>

<div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
    <div class="flex items-start justify-between mb-4">
        <div class="w-12 h-12 bg-<?php echo $color; ?> bg-opacity-10 rounded-full flex items-center justify-center">
            <i class="fas <?php echo $icon; ?> text-<?php echo $color; ?> text-xl"></i>
        </div>
        <?php if ($link) : ?>
            <a href="<?php echo esc_url($link); ?>" 
               class="text-<?php echo $color; ?> hover:text-opacity-80 transition-colors">
                <i class="fas fa-external-link-alt"></i>
            </a>
        <?php endif; ?>
    </div>

    <h3 class="text-xl font-bold text-dark mb-2"><?php echo esc_html($title); ?></h3>
    
    <div class="flex items-baseline space-x-2 rtl:space-x-reverse">
        <span class="text-3xl font-bold text-<?php echo $color; ?>"><?php echo esc_html($count); ?></span>
        <span class="text-gray-600"><?php echo esc_html($label); ?></span>
    </div>

    <?php if (isset($args['progress']) && $args['max_progress']) : ?>
        <div class="mt-4">
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-<?php echo $color; ?> h-2.5 rounded-full" 
                     style="width: <?php echo ($args['progress'] / $args['max_progress']) * 100; ?>%">
                </div>
            </div>
            <div class="text-sm text-gray-600 mt-1">
                <?php echo esc_html($args['progress']); ?> / <?php echo esc_html($args['max_progress']); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
