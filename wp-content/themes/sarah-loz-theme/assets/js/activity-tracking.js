/**
 * Activity Tracking JavaScript
 * 
 * This file handles tracking child activities on the website
 */

(function($) {
    'use strict';
    
    // Activity Tracking Object
    var SarahLozActivity = {
        
        /**
         * Initialize activity tracking
         */
        init: function() {
            if (typeof sarah_loz_activity === 'undefined') {
                console.error('Activity tracking configuration not found');
                return;
            }
            
            this.setupEventListeners();
            this.trackPageView();
        },
        
        /**
         * Set up event listeners for tracking
         */
        setupEventListeners: function() {
            // Game tracking
            $(document).on('click', '.game-start-button', this.trackGameStart);
            $(document).on('click', '.game-complete-button', this.trackGameComplete);
            
            // Activity tracking
            $(document).on('click', '.activity-start-button', this.trackActivityStart);
            $(document).on('click', '.activity-complete-button', this.trackActivityComplete);
            
            // Video tracking
            $(document).on('play', '.video-player', this.trackVideoStart);
            $(document).on('ended', '.video-player', this.trackVideoComplete);
            
            // Practice tracking
            $(document).on('click', '.practice-start-button', this.trackPracticeStart);
            $(document).on('click', '.practice-complete-button', this.trackPracticeComplete);
            
            // Share content
            $(document).on('click', '.share-button', this.trackShare);
        },
        
        /**
         * Track page view
         */
        trackPageView: function() {
            // Track specific content types
            if ($('body').hasClass('single-game')) {
                this.trackGameView();
            } else if ($('body').hasClass('single-activity')) {
                this.trackActivityView();
            } else if ($('body').hasClass('single-video')) {
                this.trackVideoView();
            } else if ($('body').hasClass('single-practice')) {
                this.trackPracticeView();
            }
        },
        
        /**
         * Track game view
         */
        trackGameView: function() {
            var gameId = $('article.game').data('id');
            if (gameId) {
                // Auto-start tracking for games that start immediately
                if ($('article.game').hasClass('auto-start')) {
                    this.trackGameStart();
                }
            }
        },
        
        /**
         * Track activity view
         */
        trackActivityView: function() {
            var activityId = $('article.activity').data('id');
            if (activityId) {
                // Auto-start tracking for activities that start immediately
                if ($('article.activity').hasClass('auto-start')) {
                    this.trackActivityStart();
                }
            }
        },
        
        /**
         * Track video view
         */
        trackVideoView: function() {
            var videoId = $('article.video').data('id');
            if (videoId) {
                // Videos are tracked on play/ended events
            }
        },
        
        /**
         * Track practice view
         */
        trackPracticeView: function() {
            var practiceId = $('article.practice').data('id');
            if (practiceId) {
                // Auto-start tracking for practices that start immediately
                if ($('article.practice').hasClass('auto-start')) {
                    this.trackPracticeStart();
                }
            }
        },
        
        /**
         * Track game start
         */
        trackGameStart: function(e) {
            if (e) e.preventDefault();
            
            var gameId = $(this).data('id') || $('article.game').data('id');
            if (!gameId) return;
            
            $.ajax({
                url: sarah_loz_activity.ajax_url,
                type: 'POST',
                data: {
                    action: 'sarah_loz_start_game',
                    game_id: gameId,
                    nonce: sarah_loz_activity.nonce
                },
                success: function(response) {
                    console.log('Game start tracked');
                }
            });
        },
        
        /**
         * Track game complete
         */
        trackGameComplete: function(e) {
            if (e) e.preventDefault();
            
            var gameId = $(this).data('id') || $('article.game').data('id');
            if (!gameId) return;
            
            $.ajax({
                url: sarah_loz_activity.ajax_url,
                type: 'POST',
                data: {
                    action: 'sarah_loz_complete_game',
                    game_id: gameId,
                    nonce: sarah_loz_activity.nonce
                },
                success: function(response) {
                    console.log('Game completion tracked');
                }
            });
        },
        
        /**
         * Track activity start
         */
        trackActivityStart: function(e) {
            if (e) e.preventDefault();
            
            var activityId = $(this).data('id') || $('article.activity').data('id');
            if (!activityId) return;
            
            $.ajax({
                url: sarah_loz_activity.ajax_url,
                type: 'POST',
                data: {
                    action: 'sarah_loz_start_activity',
                    activity_id: activityId,
                    nonce: sarah_loz_activity.nonce
                },
                success: function(response) {
                    console.log('Activity start tracked');
                }
            });
        },
        
        /**
         * Track activity complete
         */
        trackActivityComplete: function(e) {
            if (e) e.preventDefault();
            
            var activityId = $(this).data('id') || $('article.activity').data('id');
            if (!activityId) return;
            
            $.ajax({
                url: sarah_loz_activity.ajax_url,
                type: 'POST',
                data: {
                    action: 'sarah_loz_complete_activity',
                    activity_id: activityId,
                    nonce: sarah_loz_activity.nonce
                },
                success: function(response) {
                    console.log('Activity completion tracked');
                }
            });
        },
        
        /**
         * Track video start
         */
        trackVideoStart: function(e) {
            var videoId = $(this).data('id') || $(this).closest('article.video').data('id');
            if (!videoId) return;
            
            $.ajax({
                url: sarah_loz_activity.ajax_url,
                type: 'POST',
                data: {
                    action: 'sarah_loz_start_video',
                    video_id: videoId,
                    nonce: sarah_loz_activity.nonce
                },
                success: function(response) {
                    console.log('Video start tracked');
                }
            });
        },
        
        /**
         * Track video complete
         */
        trackVideoComplete: function(e) {
            var videoId = $(this).data('id') || $(this).closest('article.video').data('id');
            if (!videoId) return;
            
            $.ajax({
                url: sarah_loz_activity.ajax_url,
                type: 'POST',
                data: {
                    action: 'sarah_loz_complete_video',
                    video_id: videoId,
                    nonce: sarah_loz_activity.nonce
                },
                success: function(response) {
                    console.log('Video completion tracked');
                }
            });
        },
        
        /**
         * Track practice start
         */
        trackPracticeStart: function(e) {
            if (e) e.preventDefault();
            
            var practiceId = $(this).data('id') || $('article.practice').data('id');
            if (!practiceId) return;
            
            $.ajax({
                url: sarah_loz_activity.ajax_url,
                type: 'POST',
                data: {
                    action: 'sarah_loz_start_practice',
                    practice_id: practiceId,
                    nonce: sarah_loz_activity.nonce
                },
                success: function(response) {
                    console.log('Practice start tracked');
                }
            });
        },
        
        /**
         * Track practice complete
         */
        trackPracticeComplete: function(e) {
            if (e) e.preventDefault();
            
            var practiceId = $(this).data('id') || $('article.practice').data('id');
            if (!practiceId) return;
            
            var score = $(this).data('score') || 100;
            
            $.ajax({
                url: sarah_loz_activity.ajax_url,
                type: 'POST',
                data: {
                    action: 'sarah_loz_complete_practice',
                    practice_id: practiceId,
                    score: score,
                    nonce: sarah_loz_activity.nonce
                },
                success: function(response) {
                    console.log('Practice completion tracked');
                }
            });
        },
        
        /**
         * Track share
         */
        trackShare: function(e) {
            if (e) e.preventDefault();
            
            var contentId = $(this).data('id');
            var shareType = $(this).data('share-type') || 'general';
            
            if (!contentId) return;
            
            $.ajax({
                url: sarah_loz_activity.ajax_url,
                type: 'POST',
                data: {
                    action: 'sarah_loz_share_content',
                    content_id: contentId,
                    share_type: shareType,
                    nonce: sarah_loz_activity.nonce
                },
                success: function(response) {
                    console.log('Content share tracked');
                    
                    // Continue with the actual sharing
                    if (e && !$(e.currentTarget).hasClass('track-only')) {
                        window.open($(e.currentTarget).attr('href'), '_blank');
                    }
                }
            });
        }
    };
    
    // Initialize on document ready
    $(document).ready(function() {
        SarahLozActivity.init();
    });
    
})(jQuery);
