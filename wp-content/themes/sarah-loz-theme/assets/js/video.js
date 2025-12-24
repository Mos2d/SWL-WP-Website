/**
 * Sarah and Loz Theme Video JavaScript
 */
(function($) {
    'use strict';

    // Track video view on single video page
    function trackVideoView() {
        // Only track on single video page
        if (!$('body').hasClass('single-video')) {
            return;
        }
        
        // Get video ID from main element
        const videoId = $('main').data('id');
        
        if (!videoId) {
            return;
        }
        
        // Track view via AJAX
        $.ajax({
            url: sarah_loz_video.ajax_url,
            type: 'POST',
            data: {
                action: 'sarah_loz_track_video_view',
                nonce: sarah_loz_video.nonce,
                video_id: videoId
            },
            success: function(response) {
                if (response.success) {
                    $('.view-count').text(response.data.view_count);
                }
            }
        });
    }

    // Handle video like button
    function handleVideoLike() {
        $('.video-like-btn').on('click', function() {
            const videoId = $(this).data('id');
            const $button = $(this);
            
            $.ajax({
                url: sarah_loz_video.ajax_url,
                type: 'POST',
                data: {
                    action: 'sarah_loz_video_like',
                    nonce: sarah_loz_video.nonce,
                    video_id: videoId
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data.action === 'liked') {
                            $button.addClass('bg-dark').removeClass('bg-primary');
                            $button.find('i').addClass('fas').removeClass('far');
                        } else {
                            $button.removeClass('bg-dark').addClass('bg-primary');
                            $button.find('i').removeClass('fas').addClass('far');
                        }
                    } else {
                        // Show error message
                        alert(response.data.message);
                    }
                }
            });
        });
    }

    // Handle video save button
    function handleVideoSave() {
        $('.video-save-btn').on('click', function() {
            const videoId = $(this).data('id');
            const $button = $(this);
            
            $.ajax({
                url: sarah_loz_video.ajax_url,
                type: 'POST',
                data: {
                    action: 'sarah_loz_video_save',
                    nonce: sarah_loz_video.nonce,
                    video_id: videoId
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data.action === 'saved') {
                            $button.html('<i class="fas fa-check ml-2"></i> تم الحفظ');
                            $button.addClass('bg-dark text-white').removeClass('bg-accent text-dark');
                        } else {
                            $button.html('<i class="fas fa-save ml-2"></i> حفظ');
                            $button.removeClass('bg-dark text-white').addClass('bg-accent text-dark');
                        }
                    } else {
                        // Show error message
                        alert(response.data.message);
                    }
                }
            });
        });
    }

    // Handle video tab switching
    function handleVideoTabs() {
        $('.video-tab').on('click', function() {
            const tab = $(this).data('tab');
            
            // Update tab buttons
            $('.video-tab').removeClass('text-primary border-b-2 border-primary font-bold').addClass('text-gray-500 hover:text-primary');
            $(this).addClass('text-primary border-b-2 border-primary font-bold').removeClass('text-gray-500 hover:text-primary');
            
            // Show selected tab content
            $('.video-tab-content').addClass('hidden');
            $(`#${tab}-tab`).removeClass('hidden');
        });
    }

    // Handle video sharing
    function handleVideoShare() {
        $('.video-share-btn').on('click', function() {
            // Get current URL
            const url = window.location.href;
            
            // Create a temporary input element
            const $temp = $('<input>');
            $('body').append($temp);
            $temp.val(url).select();
            
            // Copy URL to clipboard
            document.execCommand('copy');
            $temp.remove();
            
            // Show message
            alert('تم نسخ رابط الفيديو للمشاركة');
        });
    }

    // Initialize video functionality
    $(document).ready(function() {
        trackVideoView();
        handleVideoLike();
        handleVideoSave();
        handleVideoTabs();
        handleVideoShare();
    });

})(jQuery); 