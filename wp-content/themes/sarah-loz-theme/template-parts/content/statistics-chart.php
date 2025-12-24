<?php
/**
 * Statistics Chart Template Part
 * 
 * @param array $data Array of statistical data
 * @param string $chart_type Type of chart (line, bar, pie)
 * @param string $title Chart title
 * @param string $period Time period (daily, weekly, monthly)
 */

$data = $args['data'] ?? array();
$chart_type = $args['chart_type'] ?? 'line';
$title = $args['title'] ?? '';
$period = $args['period'] ?? 'weekly';
?>

<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-dark"><?php echo esc_html($title); ?></h3>
        
        <select class="period-selector bg-light rounded-full px-4 py-2 text-sm">
            <option value="daily" <?php selected($period, 'daily'); ?>>يومي</option>
            <option value="weekly" <?php selected($period, 'weekly'); ?>>أسبوعي</option>
            <option value="monthly" <?php selected($period, 'monthly'); ?>>شهري</option>
        </select>
    </div>

    <div class="chart-container" style="position: relative; height: 300px;">
        <canvas id="<?php echo esc_attr($args['chart_id'] ?? 'statsChart'); ?>"></canvas>
    </div>

    <?php if (isset($args['summary'])) : ?>
        <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-200">
            <?php foreach ($args['summary'] as $item) : ?>
                <div class="text-center">
                    <div class="text-2xl font-bold text-<?php echo $item['color'] ?? 'primary'; ?>">
                        <?php echo esc_html($item['value']); ?>
                    </div>
                    <div class="text-sm text-gray-600">
                        <?php echo esc_html($item['label']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('<?php echo esc_js($args['chart_id'] ?? 'statsChart'); ?>').getContext('2d');
    const chartData = <?php echo json_encode($data); ?>;
    
    new Chart(ctx, {
        type: '<?php echo esc_js($chart_type); ?>',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
