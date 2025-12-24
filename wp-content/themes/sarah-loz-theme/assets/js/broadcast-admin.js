/**
 * Broadcast Admin JavaScript
 * Handles admin broadcast transitions and viewer analytics
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Handle transition broadcast buttons
        $('.transition-broadcast-button').on('click', function(e) {
            e.preventDefault();
            
            if (!confirm(sarah_loz_broadcast_admin.transition_confirm)) {
                return;
            }
            
            var $button = $(this);
            var broadcastId = $button.data('id');
            var nonce = $button.data('nonce');
            
            // Update button text
            var originalText = $button.text();
            $button.text('جاري التحويل...').css('opacity', 0.7);
            
            // Ajax request to transition broadcast
            $.ajax({
                url: sarah_loz_broadcast_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'sarah_loz_transition_broadcast',
                    broadcast_id: broadcastId,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert(sarah_loz_broadcast_admin.success_message);
                        // Refresh the page to show updated status
                        window.location.reload();
                    } else {
                        alert(response.data.message || sarah_loz_broadcast_admin.error_message);
                        $button.text(originalText).css('opacity', 1);
                    }
                },
                error: function() {
                    alert(sarah_loz_broadcast_admin.error_message);
                    $button.text(originalText).css('opacity', 1);
                }
            });
        });
        
        // Handle bulk actions if needed
        $('#doaction, #doaction2').on('click', function() {
            var action = $(this).prev('select').val();
            if (action === 'transition_to_recorded') {
                return confirm(sarah_loz_broadcast_admin.transition_confirm);
            }
        });
        
        // Live broadcast dashboard
        initLiveBroadcastDashboard();
    });
    
    /**
     * Initialize live broadcast dashboard (if present)
     */
    function initLiveBroadcastDashboard() {
        var $dashboard = $('#broadcast-dashboard');
        
        if (!$dashboard.length) {
            return;
        }
        
        var broadcastId = $dashboard.data('id');
        var nonce = $dashboard.data('nonce');
        var refreshInterval;
        var viewerChartInstance;
        var viewersData = {
            timestamps: [],
            counts: []
        };
        
        // Initialize tabs
        $('.dashboard-tab-button').on('click', function() {
            var target = $(this).data('target');
            
            // Update active tab
            $('.dashboard-tab-button').removeClass('active');
            $(this).addClass('active');
            
            // Show target content
            $('.dashboard-tab-content').removeClass('active').hide();
            $('#' + target).addClass('active').show();
            
            // Initialize chart if needed
            if (target === 'tab-analytics' && !viewerChartInstance && typeof Chart !== 'undefined') {
                initViewerChart();
            }
        });
        
        // Initialize dashboard refresh
        refreshDashboard();
        refreshInterval = setInterval(refreshDashboard, 30000); // Refresh every 30 seconds
        
        /**
         * Refresh dashboard data
         */
        function refreshDashboard() {
            $.ajax({
                url: sarah_loz_broadcast_admin.ajax_url,
                type: 'GET',
                data: {
                    action: 'sarah_loz_get_broadcast_viewers',
                    broadcast_id: broadcastId,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        updateDashboardStats(response.data);
                        updateViewersTable(response.data.active_viewers);
                        
                        // Update chart data
                        if (viewersData.timestamps.length === 0 || 
                            viewersData.counts[viewersData.counts.length - 1] !== response.data.active_count) {
                            
                            // Add current time and viewer count
                            var now = new Date();
                            var timeStr = now.getHours() + ':' + (now.getMinutes() < 10 ? '0' : '') + now.getMinutes();
                            
                            viewersData.timestamps.push(timeStr);
                            viewersData.counts.push(response.data.active_count);
                            
                            // Keep only the last 20 points
                            if (viewersData.timestamps.length > 20) {
                                viewersData.timestamps.shift();
                                viewersData.counts.shift();
                            }
                            
                            // Update chart if it exists
                            if (viewerChartInstance) {
                                viewerChartInstance.data.labels = viewersData.timestamps;
                                viewerChartInstance.data.datasets[0].data = viewersData.counts;
                                viewerChartInstance.update();
                            }
                        }
                    }
                }
            });
        }
        
        /**
         * Update dashboard statistics
         */
        function updateDashboardStats(data) {
            $('#active-viewers-count').text(data.active_count);
            $('#total-viewers-count').text(data.total_count);
            $('#peak-viewers-count').text(data.peak_viewers);
        }
        
        /**
         * Update viewers table
         */
        function updateViewersTable(viewers) {
            var $tableBody = $('#active-viewers-table tbody');
            
            // Clear existing rows
            $tableBody.empty();
            
            if (viewers.length === 0) {
                $tableBody.append('<tr><td colspan="4" class="text-center py-4">لا يوجد مشاهدون نشطون حالياً</td></tr>');
                return;
            }
            
            // Add viewer rows
            $.each(viewers, function(index, viewer) {
                var lastActive = new Date(viewer.last_active * 1000);
                var timeString = lastActive.toLocaleTimeString();
                
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
         * Initialize viewer chart (requires Chart.js)
         */
        function initViewerChart() {
            if (typeof Chart === 'undefined') {
                console.warn('Chart.js is not available');
                return;
            }
            
            var ctx = document.getElementById('viewers-chart');
            
            if (!ctx) {
                return;
            }
            
            viewerChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: viewersData.timestamps,
                    datasets: [{
                        label: 'المشاهدون النشطون',
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
                            precision: 0
                        }
                    }
                }
            });
        }
        
        // Clean up on page unload
        $(window).on('beforeunload', function() {
            clearInterval(refreshInterval);
        });
    }
    
})(jQuery); 