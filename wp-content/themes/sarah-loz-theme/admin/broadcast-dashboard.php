<?php
/**
 * Broadcast Dashboard Template
 * 
 * Provides a detailed dashboard for managing live broadcasts
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if user has permission
if (!current_user_can('edit_posts')) {
    wp_die(__('You do not have sufficient permissions to access this page.', 'sarah-loz'));
}

// Get broadcast data
$broadcast_id = isset($_GET['broadcast_id']) ? intval($_GET['broadcast_id']) : 0;

if ($broadcast_id <= 0) {
    wp_die(__('Invalid broadcast ID', 'sarah-loz'));
}

// Get broadcast details
$broadcast = get_post($broadcast_id);
$broadcast_type = get_field('broadcast_type', $broadcast_id);
$broadcast_title = $broadcast->post_title;
$total_viewers = get_post_meta($broadcast_id, 'broadcast_total_viewers', true);
$peak_viewers = get_post_meta($broadcast_id, 'broadcast_peak_viewers', true);

// Only allow live broadcasts
if ($broadcast_type !== 'live') {
    wp_die(__('This dashboard is only available for live broadcasts', 'sarah-loz'));
}

// Enqueue necessary scripts
wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.9.1', true);
wp_enqueue_script('sarah-loz-broadcast-admin');

// Get total viewers count
$total_viewers_count = is_array($total_viewers) ? count($total_viewers) : 0;
$peak_viewers_count = intval($peak_viewers) ?: 0;

// Get nonce
$nonce = wp_create_nonce('sarah_loz_broadcast_nonce');
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html(__('Broadcast Dashboard', 'sarah-loz')); ?></h1>
    <a href="<?php echo esc_url(get_edit_post_link($broadcast_id)); ?>" class="page-title-action"><?php _e('Edit Broadcast', 'sarah-loz'); ?></a>
    <a href="<?php echo esc_url(get_permalink($broadcast_id)); ?>" class="page-title-action" target="_blank"><?php _e('View Broadcast', 'sarah-loz'); ?></a>
    
    <hr class="wp-header-end">
    
    <div class="broadcast-dashboard-header">
        <div class="broadcast-status">
            <span class="broadcast-badge live"><?php _e('LIVE', 'sarah-loz'); ?></span>
            <h2><?php echo esc_html($broadcast_title); ?></h2>
        </div>
        
        <div class="broadcast-actions">
            <button id="transition-broadcast-button" class="button button-primary" data-id="<?php echo esc_attr($broadcast_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>">
                <i class="dashicons dashicons-video-alt3"></i> <?php _e('End Broadcast & Convert to Recording', 'sarah-loz'); ?>
            </button>
        </div>
    </div>
    
    <div id="broadcast-dashboard" class="broadcast-dashboard" data-id="<?php echo esc_attr($broadcast_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>">
        <!-- Dashboard Navigation -->
        <div class="dashboard-tabs">
            <button class="dashboard-tab-button active" data-target="tab-overview"><?php _e('Overview', 'sarah-loz'); ?></button>
            <button class="dashboard-tab-button" data-target="tab-viewers"><?php _e('Active Viewers', 'sarah-loz'); ?></button>
            <button class="dashboard-tab-button" data-target="tab-analytics"><?php _e('Analytics', 'sarah-loz'); ?></button>
            <button class="dashboard-tab-button" data-target="tab-chat"><?php _e('Chat Management', 'sarah-loz'); ?></button>
        </div>
        
        <!-- Overview Tab -->
        <div id="tab-overview" class="dashboard-tab-content active">
            <div class="stats-cards">
                <div class="stats-card">
                    <h3><?php _e('Active Viewers', 'sarah-loz'); ?></h3>
                    <div class="stats-value" id="active-viewers-count">-</div>
                    <div class="stats-label"><?php _e('Currently watching', 'sarah-loz'); ?></div>
                </div>
                
                <div class="stats-card">
                    <h3><?php _e('Total Viewers', 'sarah-loz'); ?></h3>
                    <div class="stats-value" id="total-viewers-count"><?php echo esc_html($total_viewers_count); ?></div>
                    <div class="stats-label"><?php _e('Unique viewers', 'sarah-loz'); ?></div>
                </div>
                
                <div class="stats-card">
                    <h3><?php _e('Peak Viewers', 'sarah-loz'); ?></h3>
                    <div class="stats-value" id="peak-viewers-count"><?php echo esc_html($peak_viewers_count); ?></div>
                    <div class="stats-label"><?php _e('Maximum concurrent', 'sarah-loz'); ?></div>
                </div>
                
                <div class="stats-card">
                    <h3><?php _e('Broadcast Duration', 'sarah-loz'); ?></h3>
                    <div class="stats-value" id="broadcast-duration">
                        <?php 
                        $start_time = get_post_time('U', true, $broadcast_id);
                        $current_time = current_time('timestamp', true);
                        $duration = $current_time - $start_time;
                        
                        echo sprintf(
                            '%02d:%02d:%02d',
                            floor($duration / 3600),
                            floor(($duration % 3600) / 60),
                            $duration % 60
                        );
                        ?>
                    </div>
                    <div class="stats-label"><?php _e('Time elapsed', 'sarah-loz'); ?></div>
                </div>
            </div>
            
            <div class="quick-stats">
                <div class="quick-stats-chart">
                    <h3><?php _e('Viewers Over Time', 'sarah-loz'); ?></h3>
                    <div class="chart-container" style="position: relative; height:200px;">
                        <canvas id="mini-viewers-chart"></canvas>
                    </div>
                </div>
                
                <div class="quick-stats-recent">
                    <h3><?php _e('Recent Viewers', 'sarah-loz'); ?></h3>
                    <div id="recent-viewers-list" class="recent-viewers-list">
                        <div class="loading-indicator"><?php _e('Loading...', 'sarah-loz'); ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Viewers Tab -->
        <div id="tab-viewers" class="dashboard-tab-content" style="display:none;">
            <h2><?php _e('Active Viewers', 'sarah-loz'); ?></h2>
            <p><?php _e('Users currently watching this broadcast:', 'sarah-loz'); ?></p>
            
            <div class="tablenav top">
                <div class="alignleft actions">
                    <button class="button action" id="refresh-viewers-table">
                        <?php _e('Refresh Now', 'sarah-loz'); ?>
                    </button>
                </div>
                <div class="tablenav-pages">
                    <span class="displaying-num" id="viewers-count-display">
                        <?php _e('Loading...', 'sarah-loz'); ?>
                    </span>
                </div>
                <br class="clear">
            </div>
            
            <table class="wp-list-table widefat fixed striped" id="active-viewers-table">
                <thead>
                    <tr>
                        <th scope="col"><?php _e('Name', 'sarah-loz'); ?></th>
                        <th scope="col"><?php _e('Email', 'sarah-loz'); ?></th>
                        <th scope="col"><?php _e('Role', 'sarah-loz'); ?></th>
                        <th scope="col"><?php _e('Last Active', 'sarah-loz'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" class="text-center"><?php _e('Loading...', 'sarah-loz'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Analytics Tab -->
        <div id="tab-analytics" class="dashboard-tab-content" style="display:none;">
            <h2><?php _e('Broadcast Analytics', 'sarah-loz'); ?></h2>
            
            <div class="analytics-container">
                <div class="analytics-chart-container">
                    <h3><?php _e('Viewers Over Time', 'sarah-loz'); ?></h3>
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="viewers-chart"></canvas>
                    </div>
                </div>
                
                <div class="analytics-metrics">
                    <div class="metric-card">
                        <h4><?php _e('Engagement Rate', 'sarah-loz'); ?></h4>
                        <div class="metric-value">
                            <?php
                            $chat_messages = get_post_meta($broadcast_id, 'broadcast_chat_messages', true);
                            $message_count = is_array($chat_messages) ? count($chat_messages) : 0;
                            $engagement_rate = $total_viewers_count > 0 ? round(($message_count / $total_viewers_count) * 100) : 0;
                            echo esc_html($engagement_rate . '%');
                            ?>
                        </div>
                        <div class="metric-desc">
                            <?php printf(__('%d messages from %d viewers', 'sarah-loz'), $message_count, $total_viewers_count); ?>
                        </div>
                    </div>
                    
                    <div class="metric-card">
                        <h4><?php _e('Average Watch Time', 'sarah-loz'); ?></h4>
                        <div class="metric-value">--:--</div>
                        <div class="metric-desc"><?php _e('Coming soon', 'sarah-loz'); ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Chat Management Tab -->
        <div id="tab-chat" class="dashboard-tab-content" style="display:none;">
            <h2><?php _e('Chat Management', 'sarah-loz'); ?></h2>
            
            <div class="chat-management">
                <div class="chat-controls">
                    <button class="button" id="refresh-chat"><?php _e('Refresh Chat', 'sarah-loz'); ?></button>
                    <button class="button" id="clear-chat"><?php _e('Clear All Messages', 'sarah-loz'); ?></button>
                </div>
                
                <div class="chat-preview">
                    <h3><?php _e('Chat Preview', 'sarah-loz'); ?></h3>
                    <div id="chat-messages-preview" class="chat-messages-preview">
                        <div class="loading-indicator"><?php _e('Loading messages...', 'sarah-loz'); ?></div>
                    </div>
                </div>
                
                <div class="send-message-form">
                    <h3><?php _e('Send Message as Admin', 'sarah-loz'); ?></h3>
                    <form id="admin-chat-form">
                        <input type="hidden" id="broadcast-id" value="<?php echo esc_attr($broadcast_id); ?>">
                        <input type="hidden" id="chat-nonce" value="<?php echo esc_attr($nonce); ?>">
                        <textarea id="admin-message" placeholder="<?php esc_attr_e('Type your message here...', 'sarah-loz'); ?>" rows="3" class="widefat"></textarea>
                        <button type="submit" class="button button-primary"><?php _e('Send Message', 'sarah-loz'); ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Dashboard Styles */
.broadcast-dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.broadcast-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: bold;
    color: white;
    margin-right: 10px;
}

.broadcast-badge.live {
    background-color: #dc3545;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

.dashboard-tabs {
    margin-bottom: 20px;
    border-bottom: 1px solid #ccc;
}

.dashboard-tab-button {
    padding: 10px 15px;
    margin-right: 5px;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
}

.dashboard-tab-button.active {
    border-bottom-color: #0073aa;
    font-weight: bold;
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stats-card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.stats-card h3 {
    margin-top: 0;
    margin-bottom: 10px;
    font-size: 14px;
    color: #555;
}

.stats-value {
    font-size: 28px;
    font-weight: bold;
    color: #0073aa;
    margin-bottom: 5px;
}

.stats-label {
    font-size: 12px;
    color: #777;
}

.quick-stats {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
}

.quick-stats-chart, .quick-stats-recent {
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.analytics-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
}

.analytics-chart-container, .analytics-metrics {
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.metric-card {
    padding: 15px;
    margin-bottom: 15px;
    border: 1px solid #eee;
    border-radius: 4px;
}

.metric-value {
    font-size: 24px;
    font-weight: bold;
    color: #0073aa;
    margin: 10px 0;
}

.metric-desc {
    font-size: 12px;
    color: #777;
}

.chat-management {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
}

.chat-preview {
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    grid-column: 1;
}

.send-message-form {
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    grid-column: 2;
}

.chat-controls {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.chat-messages-preview {
    height: 300px;
    overflow-y: auto;
    border: 1px solid #eee;
    border-radius: 4px;
    padding: 10px;
    background: #f9f9f9;
}

@media screen and (max-width: 782px) {
    .stats-cards {
        grid-template-columns: 1fr;
    }
    
    .quick-stats, .analytics-container, .chat-management {
        grid-template-columns: 1fr;
    }
    
    .send-message-form {
        grid-column: 1;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Duration update timer
    var durationUpdateInterval = setInterval(function() {
        var startTime = <?php echo get_post_time('U', true, $broadcast_id); ?>;
        var currentTime = Math.floor(Date.now() / 1000);
        var duration = currentTime - startTime;
        
        var hours = Math.floor(duration / 3600);
        var minutes = Math.floor((duration % 3600) / 60);
        var seconds = duration % 60;
        
        $('#broadcast-duration').text(
            (hours < 10 ? '0' : '') + hours + ':' +
            (minutes < 10 ? '0' : '') + minutes + ':' +
            (seconds < 10 ? '0' : '') + seconds
        );
    }, 1000);
    
    // Handle transition button
    $('#transition-broadcast-button').on('click', function() {
        if (!confirm(sarah_loz_broadcast_admin.transition_confirm)) {
            return;
        }
        
        var $button = $(this);
        var broadcastId = $button.data('id');
        var nonce = $button.data('nonce');
        
        $button.prop('disabled', true).text('<?php _e('Processing...', 'sarah-loz'); ?>');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'sarah_loz_transition_broadcast',
                broadcast_id: broadcastId,
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    window.location.href = response.data.redirect;
                } else {
                    alert(response.data.message || '<?php _e('An error occurred', 'sarah-loz'); ?>');
                    $button.prop('disabled', false).text('<?php _e('End Broadcast & Convert to Recording', 'sarah-loz'); ?>');
                }
            },
            error: function() {
                alert('<?php _e('An error occurred', 'sarah-loz'); ?>');
                $button.prop('disabled', false).text('<?php _e('End Broadcast & Convert to Recording', 'sarah-loz'); ?>');
            }
        });
    });
    
    // Dashboard functionality
    const broadcastId = <?php echo $broadcast_id; ?>;
    const nonce = '<?php echo $nonce; ?>';
    let viewerChartInstance = null;
    let miniViewerChartInstance = null;
    let viewersData = {
        timestamps: [],
        counts: []
    };
    
    // Initialize dashboard tabs
    $('.dashboard-tab-button').on('click', function() {
        const target = $(this).data('target');
        
        // Update active tab
        $('.dashboard-tab-button').removeClass('active');
        $(this).addClass('active');
        
        // Show target content
        $('.dashboard-tab-content').hide();
        $('#' + target).show();
        
        // Initialize chart if needed
        if (target === 'tab-analytics' && !viewerChartInstance) {
            initViewerChart();
        }
        
        // Refresh data based on active tab
        if (target === 'tab-viewers') {
            refreshViewersTable();
        } else if (target === 'tab-chat') {
            refreshChatMessages();
        }
    });
    
    // Refresh data periodically
    refreshDashboardData();
    const refreshInterval = setInterval(refreshDashboardData, 30000); // Every 30 seconds
    
    // Refresh buttons
    $('#refresh-viewers-table').on('click', function() {
        refreshViewersTable();
    });
    
    $('#refresh-chat').on('click', function() {
        refreshChatMessages();
    });
    
    // Clear chat button
    $('#clear-chat').on('click', function() {
        if (confirm('<?php _e('Are you sure you want to clear all chat messages? This cannot be undone.', 'sarah-loz'); ?>')) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'sarah_loz_clear_chat_messages',
                    broadcast_id: broadcastId,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        refreshChatMessages();
                    } else {
                        alert(response.data.message || '<?php _e('An error occurred', 'sarah-loz'); ?>');
                    }
                },
                error: function() {
                    alert('<?php _e('An error occurred', 'sarah-loz'); ?>');
                }
            });
        }
    });
    
    // Admin chat form
    $('#admin-chat-form').on('submit', function(e) {
        e.preventDefault();
        
        const message = $('#admin-message').val().trim();
        if (!message) return;
        
        $('#admin-chat-form button').prop('disabled', true);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'sarah_loz_send_admin_chat_message',
                broadcast_id: broadcastId,
                message: message,
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#admin-message').val('');
                    refreshChatMessages();
                } else {
                    alert(response.data.message || '<?php _e('An error occurred', 'sarah-loz'); ?>');
                }
                $('#admin-chat-form button').prop('disabled', false);
            },
            error: function() {
                alert('<?php _e('An error occurred', 'sarah-loz'); ?>');
                $('#admin-chat-form button').prop('disabled', false);
            }
        });
    });
    
    /**
     * Refresh all dashboard data
     */
    function refreshDashboardData() {
        $.ajax({
            url: ajaxurl,
            type: 'GET',
            data: {
                action: 'sarah_loz_get_broadcast_viewers',
                broadcast_id: broadcastId,
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    updateDashboardStats(response.data);
                    updateRecentViewers(response.data.active_viewers);
                    
                    // Update viewers data for charts
                    if (viewersData.timestamps.length === 0 || 
                        viewersData.counts[viewersData.counts.length - 1] !== response.data.active_count) {
                        
                        const now = new Date();
                        const timeStr = now.getHours() + ':' + 
                            (now.getMinutes() < 10 ? '0' : '') + now.getMinutes();
                        
                        viewersData.timestamps.push(timeStr);
                        viewersData.counts.push(response.data.active_count);
                        
                        // Keep only last 20 points
                        if (viewersData.timestamps.length > 20) {
                            viewersData.timestamps.shift();
                            viewersData.counts.shift();
                        }
                        
                        // Update charts if initialized
                        updateCharts();
                    }
                }
            }
        });
    }
    
    /**
     * Refresh viewers table
     */
    function refreshViewersTable() {
        $.ajax({
            url: ajaxurl,
            type: 'GET',
            data: {
                action: 'sarah_loz_get_broadcast_viewers',
                broadcast_id: broadcastId,
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    updateViewersTable(response.data.active_viewers);
                    $('#viewers-count-display').text(
                        response.data.active_count + ' ' + 
                        '<?php _e('active viewers', 'sarah-loz'); ?>'
                    );
                }
            }
        });
    }
    
    /**
     * Refresh chat messages
     */
    function refreshChatMessages() {
        $.ajax({
            url: ajaxurl,
            type: 'GET',
            data: {
                action: 'sarah_loz_get_admin_chat_messages',
                broadcast_id: broadcastId,
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    updateChatMessages(response.data.messages);
                }
            }
        });
    }
    
    /**
     * Update dashboard stats
     */
    function updateDashboardStats(data) {
        $('#active-viewers-count').text(data.active_count);
        $('#total-viewers-count').text(data.total_count);
        $('#peak-viewers-count').text(data.peak_viewers);
    }
    
    /**
     * Update recent viewers list
     */
    function updateRecentViewers(viewers) {
        const $recentList = $('#recent-viewers-list');
        $recentList.empty();
        
        if (!viewers || viewers.length === 0) {
            $recentList.html('<div class="text-center text-gray-500"><?php _e('No active viewers', 'sarah-loz'); ?></div>');
            return;
        }
        
        // Show up to 5 most recent viewers
        const recentViewers = viewers.slice(-5).reverse();
        
        recentViewers.forEach(function(viewer) {
            const lastActive = new Date(viewer.last_active * 1000);
            const timeString = lastActive.toLocaleTimeString();
            
            $recentList.append(`
                <div class="bg-gray-50 rounded p-2 mb-2 flex justify-between items-center">
                    <div>
                        <strong>${viewer.user_name}</strong><br>
                        <small>${viewer.user_email}</small>
                    </div>
                    <small class="text-gray-500">${timeString}</small>
                </div>
            `);
        });
    }
    
    /**
     * Update viewers table
     */
    function updateViewersTable(viewers) {
        const $tableBody = $('#active-viewers-table tbody');
        $tableBody.empty();
        
        if (!viewers || viewers.length === 0) {
            $tableBody.html(`<tr><td colspan="4" class="text-center py-4"><?php _e('No active viewers', 'sarah-loz'); ?></td></tr>`);
            return;
        }
        
        viewers.forEach(function(viewer) {
            const lastActive = new Date(viewer.last_active * 1000);
            const timeString = lastActive.toLocaleTimeString();
            
            $tableBody.append(`
                <tr>
                    <td class="p-2 border-b">${viewer.user_name}</td>
                    <td class="p-2 border-b">${viewer.user_email}</td>
                    <td class="p-2 border-b">${viewer.user_role}</td>
                    <td class="p-2 border-b">${timeString}</td>
                </tr>
            `);
        });
    }
    
    /**
     * Update chat messages
     */
    function updateChatMessages(messages) {
        const $chatPreview = $('#chat-messages-preview');
        $chatPreview.empty();
        
        if (!messages || messages.length === 0) {
            $chatPreview.html(`<div class="text-center text-gray-500 py-4"><?php _e('No chat messages yet', 'sarah-loz'); ?></div>`);
            return;
        }
        
        messages.forEach(function(message) {
            const isAdmin = message.is_admin;
            const messageClass = isAdmin ? 'bg-orange-100 border-orange-300' : 'bg-gray-100 border-gray-300';
            const nameClass = isAdmin ? 'text-orange-700' : 'text-gray-700';
            
            $chatPreview.append(`
                <div class="p-2 mb-2 rounded ${messageClass} border">
                    <div class="flex justify-between items-center mb-1">
                        <strong class="${nameClass}">${message.user_name}</strong>
                        <small class="text-gray-500">${message.timestamp}</small>
                    </div>
                    <div>${message.message}</div>
                </div>
            `);
        });
        
        // Scroll to bottom
        $chatPreview.scrollTop($chatPreview[0].scrollHeight);
    }
    
    /**
     * Initialize viewer chart
     */
    function initViewerChart() {
        // Main chart
        const viewerCtx = document.getElementById('viewers-chart');
        if (viewerCtx) {
            viewerChartInstance = new Chart(viewerCtx, {
                type: 'line',
                data: {
                    labels: viewersData.timestamps,
                    datasets: [{
                        label: '<?php _e('Active Viewers', 'sarah-loz'); ?>',
                        data: viewersData.counts,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
        
        // Mini chart for overview
        const miniCtx = document.getElementById('mini-viewers-chart');
        if (miniCtx) {
            miniViewerChartInstance = new Chart(miniCtx, {
                type: 'line',
                data: {
                    labels: viewersData.timestamps,
                    datasets: [{
                        label: '<?php _e('Active Viewers', 'sarah-loz'); ?>',
                        data: viewersData.counts,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
    }
    
    /**
     * Update charts with new data
     */
    function updateCharts() {
        if (viewerChartInstance) {
            viewerChartInstance.data.labels = viewersData.timestamps;
            viewerChartInstance.data.datasets[0].data = viewersData.counts;
            viewerChartInstance.update();
        }
        
        if (miniViewerChartInstance) {
            miniViewerChartInstance.data.labels = viewersData.timestamps;
            miniViewerChartInstance.data.datasets[0].data = viewersData.counts;
            miniViewerChartInstance.update();
        }
    }
    
    // Initialize mini chart immediately
    initViewerChart();
    
    // Clean up on page unload
    $(window).on('beforeunload', function() {
        clearInterval(durationUpdateInterval);
        clearInterval(refreshInterval);
    });
});
</script> 