<?php
/**
 * Child Activity Tracking System
 * 
 * This file contains functions to track and manage child activities on the website
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Sarah_Loz_Child_Activity_Tracker {
    private static $instance = null;
    
    // Activity types
    private $activity_types = array(
        'game_started' => array(
            'points' => 5,
            'label' => 'بدء لعبة',
            'icon' => 'fas fa-gamepad',
            'color' => 'blue'
        ),
        'game_completed' => array(
            'points' => 15,
            'label' => 'إكمال لعبة',
            'icon' => 'fas fa-trophy',
            'color' => 'green'
        ),
        'activity_started' => array(
            'points' => 3,
            'label' => 'بدء نشاط',
            'icon' => 'fas fa-tasks',
            'color' => 'purple'
        ),
        'activity_completed' => array(
            'points' => 10,
            'label' => 'إكمال نشاط',
            'icon' => 'fas fa-check-circle',
            'color' => 'green'
        ),
        'video_started' => array(
            'points' => 2,
            'label' => 'بدء مشاهدة فيديو',
            'icon' => 'fas fa-play-circle',
            'color' => 'red'
        ),
        'video_completed' => array(
            'points' => 8,
            'label' => 'إكمال مشاهدة فيديو',
            'icon' => 'fas fa-check-circle',
            'color' => 'green'
        ),
        'practice_started' => array(
            'points' => 3,
            'label' => 'بدء تمرين',
            'icon' => 'fas fa-pencil-alt',
            'color' => 'orange'
        ),
        'practice_completed' => array(
            'points' => 12,
            'label' => 'إكمال تمرين',
            'icon' => 'fas fa-check-circle',
            'color' => 'green'
        ),
        'login' => array(
            'points' => 5,
            'label' => 'تسجيل الدخول',
            'icon' => 'fas fa-sign-in-alt',
            'color' => 'blue'
        ),
        'share_content' => array(
            'points' => 7,
            'label' => 'مشاركة محتوى',
            'icon' => 'fas fa-share-alt',
            'color' => 'purple'
        ),
        'comment' => array(
            'points' => 5,
            'label' => 'إضافة تعليق',
            'icon' => 'fas fa-comment',
            'color' => 'teal'
        ),
        'daily_streak' => array(
            'points' => 10,
            'label' => 'تسلسل يومي',
            'icon' => 'fas fa-calendar-check',
            'color' => 'green'
        )
    );
    
    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->register_activity_types();
        $this->add_hooks();
    }

    /**
     * Register activity types
     */
    private function register_activity_types() {
        // Already defined in the class property
    }

    /**
     * Add hooks
     */
    private function add_hooks() {
        // Game tracking
        add_action('sarah_loz_game_started', array($this, 'track_game_started'), 10, 2);
        add_action('sarah_loz_game_completed', array($this, 'track_game_completed'), 10, 2);
        
        // Activity tracking
        add_action('sarah_loz_activity_started', array($this, 'track_activity_started'), 10, 2);
        add_action('sarah_loz_activity_completed', array($this, 'track_activity_completed'), 10, 2);
        
        // Video tracking
        add_action('sarah_loz_video_started', array($this, 'track_video_started'), 10, 2);
        add_action('sarah_loz_video_completed', array($this, 'track_video_completed'), 10, 2);
        
        // Practice tracking
        add_action('sarah_loz_practice_started', array($this, 'track_practice_started'), 10, 2);
        add_action('sarah_loz_practice_completed', array($this, 'track_practice_completed'), 10, 2);
        
        // Login tracking
        add_action('wp_login', array($this, 'track_login'), 10, 2);
        
        // Content sharing
        add_action('sarah_loz_content_shared', array($this, 'track_content_shared'), 10, 3);
        
        // Comments
        add_action('comment_post', array($this, 'track_comment'), 10, 2);
    }

    /**
     * Record activity
     */
    public function record_activity($user_id, $activity_type, $content_id = 0, $additional_data = array()) {
        // Skip if not a valid activity type
        if (!isset($this->activity_types[$activity_type])) {
            return false;
        }
        
        // Create activity entry
        $activity = array(
            'type' => $activity_type,
            'content_id' => $content_id,
            'timestamp' => current_time('timestamp'),
            'points' => $this->activity_types[$activity_type]['points'],
            'data' => $additional_data
        );
        
        // Get existing activity log
        $activity_log = get_user_meta($user_id, 'child_activity_log', true);
        if (!is_array($activity_log)) {
            $activity_log = array();
        }
        
        // Add new activity to log
        $activity_log[] = $activity;
        
        // Update activity log
        update_user_meta($user_id, 'child_activity_log', $activity_log);
        
        // Update activity timeline for dashboard
        $this->update_activity_timeline($user_id, $activity);
        
        // Award points
        $this->award_points($user_id, $activity['points']);
        
        // Check for achievements
        do_action('sarah_loz_activity_recorded', $user_id, $activity_type, $content_id, $additional_data);
        
        return true;
    }
    
    /**
     * Update activity timeline
     */
    private function update_activity_timeline($user_id, $activity) {
        $timeline = get_user_meta($user_id, 'activity_timeline', true);
        if (!is_array($timeline)) {
            $timeline = array();
        }
        
        // Add activity to timeline
        $timeline[] = array(
            'type' => $activity['type'],
            'content_id' => $activity['content_id'],
            'timestamp' => $activity['timestamp'],
            'points' => $activity['points'],
            'label' => $this->activity_types[$activity['type']]['label'],
            'icon' => $this->activity_types[$activity['type']]['icon'],
            'color' => $this->activity_types[$activity['type']]['color'],
            'data' => $activity['data']
        );
        
        // Keep only the latest 50 activities
        if (count($timeline) > 50) {
            $timeline = array_slice($timeline, -50);
        }
        
        // Update timeline
        update_user_meta($user_id, 'activity_timeline', $timeline);
    }
    
    /**
     * Award points
     */
    private function award_points($user_id, $points) {
        $current_points = get_user_meta($user_id, 'activity_points', true);
        if (!$current_points) {
            $current_points = 0;
        }
        
        $new_points = $current_points + $points;
        update_user_meta($user_id, 'activity_points', $new_points);
        
        // Update total points
        $this->update_total_points($user_id);
        
        return $new_points;
    }
    
    /**
     * Update total points
     */
    private function update_total_points($user_id) {
        $activity_points = get_user_meta($user_id, 'activity_points', true) ?: 0;
        $achievement_points = get_user_meta($user_id, 'achievement_points', true) ?: 0;
        
        $total_points = $activity_points + $achievement_points;
        update_user_meta($user_id, 'total_points', $total_points);
        
        return $total_points;
    }
    
    /**
     * Track game started
     */
    public function track_game_started($user_id, $game_id) {
        $game = get_post($game_id);
        $additional_data = array(
            'game_title' => $game ? $game->post_title : 'Unknown Game'
        );
        
        return $this->record_activity($user_id, 'game_started', $game_id, $additional_data);
    }
    
    /**
     * Track game completed
     */
    public function track_game_completed($user_id, $game_id) {
        $game = get_post($game_id);
        $additional_data = array(
            'game_title' => $game ? $game->post_title : 'Unknown Game'
        );
        
        // Update completed games list
        $completed_games = get_user_meta($user_id, 'completed_games', true);
        if (!is_array($completed_games)) {
            $completed_games = array();
        }
        $completed_games[$game_id] = current_time('timestamp');
        update_user_meta($user_id, 'completed_games', $completed_games);
        
        return $this->record_activity($user_id, 'game_completed', $game_id, $additional_data);
    }
    
    /**
     * Track activity started
     */
    public function track_activity_started($user_id, $activity_id) {
        $activity = get_post($activity_id);
        $additional_data = array(
            'activity_title' => $activity ? $activity->post_title : 'Unknown Activity'
        );
        
        return $this->record_activity($user_id, 'activity_started', $activity_id, $additional_data);
    }
    
    /**
     * Track activity completed
     */
    public function track_activity_completed($user_id, $activity_id) {
        $activity = get_post($activity_id);
        $additional_data = array(
            'activity_title' => $activity ? $activity->post_title : 'Unknown Activity'
        );
        
        // Update completed activities list
        $completed_activities = get_user_meta($user_id, 'completed_activities', true);
        if (!is_array($completed_activities)) {
            $completed_activities = array();
        }
        $completed_activities[$activity_id] = current_time('timestamp');
        update_user_meta($user_id, 'completed_activities', $completed_activities);
        
        return $this->record_activity($user_id, 'activity_completed', $activity_id, $additional_data);
    }
    
    /**
     * Track video started
     */
    public function track_video_started($user_id, $video_id) {
        $video = get_post($video_id);
        $additional_data = array(
            'video_title' => $video ? $video->post_title : 'Unknown Video'
        );
        
        return $this->record_activity($user_id, 'video_started', $video_id, $additional_data);
    }
    
    /**
     * Track video completed
     */
    public function track_video_completed($user_id, $video_id) {
        $video = get_post($video_id);
        $additional_data = array(
            'video_title' => $video ? $video->post_title : 'Unknown Video'
        );
        
        // Update watched videos list
        $watched_videos = get_user_meta($user_id, 'watched_videos', true);
        if (!is_array($watched_videos)) {
            $watched_videos = array();
        }
        $watched_videos[$video_id] = current_time('timestamp');
        update_user_meta($user_id, 'watched_videos', $watched_videos);
        
        return $this->record_activity($user_id, 'video_completed', $video_id, $additional_data);
    }
    
    /**
     * Track practice started
     */
    public function track_practice_started($user_id, $practice_id) {
        $practice = get_post($practice_id);
        $additional_data = array(
            'practice_title' => $practice ? $practice->post_title : 'Unknown Practice'
        );
        
        return $this->record_activity($user_id, 'practice_started', $practice_id, $additional_data);
    }
    
    /**
     * Track practice completed
     */
    public function track_practice_completed($user_id, $practice_id) {
        $practice = get_post($practice_id);
        $additional_data = array(
            'practice_title' => $practice ? $practice->post_title : 'Unknown Practice'
        );
        
        return $this->record_activity($user_id, 'practice_completed', $practice_id, $additional_data);
    }
    
    /**
     * Track login
     */
    public function track_login($user_login, $user) {
        // Skip if not a child user
        if (!in_array('child', (array) $user->roles)) {
            return;
        }
        
        // Record login activity
        $this->record_activity($user->ID, 'login', 0);
        
        // Update login streak
        $this->update_login_streak($user->ID);
    }
    
    /**
     * Update login streak
     */
    private function update_login_streak($user_id) {
        $last_login = get_user_meta($user_id, 'last_login_date', true);
        $current_date = current_time('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day', strtotime($current_date)));
        
        $streak = get_user_meta($user_id, 'login_streak', true);
        if (!$streak) {
            $streak = 1;
        } else {
            if ($last_login === $yesterday) {
                // Consecutive login
                $streak++;
                
                // Award streak points every 5 days
                if ($streak % 5 === 0) {
                    $this->record_activity($user_id, 'daily_streak', 0, array('streak' => $streak));
                }
            } elseif ($last_login !== $current_date) {
                // Streak broken
                $streak = 1;
            }
        }
        
        update_user_meta($user_id, 'login_streak', $streak);
        update_user_meta($user_id, 'last_login_date', $current_date);
        
        return $streak;
    }
    
    /**
     * Track content shared
     */
    public function track_content_shared($user_id, $content_id, $share_type) {
        $content = get_post($content_id);
        $additional_data = array(
            'content_title' => $content ? $content->post_title : 'Unknown Content',
            'share_type' => $share_type
        );
        
        return $this->record_activity($user_id, 'share_content', $content_id, $additional_data);
    }
    
    /**
     * Track comment
     */
    public function track_comment($comment_id, $comment_approved) {
        // Skip if comment is not approved
        if ($comment_approved !== 1) {
            return;
        }
        
        $comment = get_comment($comment_id);
        $user_id = $comment->user_id;
        
        // Skip if not a logged-in user
        if (!$user_id) {
            return;
        }
        
        // Skip if not a child user
        $user = get_user_by('id', $user_id);
        if (!in_array('child', (array) $user->roles)) {
            return;
        }
        
        $post_id = $comment->comment_post_ID;
        $post = get_post($post_id);
        
        $additional_data = array(
            'post_title' => $post ? $post->post_title : 'Unknown Post',
            'comment_id' => $comment_id
        );
        
        return $this->record_activity($user_id, 'comment', $post_id, $additional_data);
    }
    
    /**
     * Get user activity log
     */
    public function get_user_activity_log($user_id, $limit = 0) {
        $activity_log = get_user_meta($user_id, 'child_activity_log', true);
        if (!is_array($activity_log)) {
            return array();
        }
        
        // Sort by timestamp (newest first)
        usort($activity_log, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        
        // Apply limit if specified
        if ($limit > 0 && count($activity_log) > $limit) {
            $activity_log = array_slice($activity_log, 0, $limit);
        }
        
        return $activity_log;
    }
    
    /**
     * Get user activity timeline
     */
    public function get_user_activity_timeline($user_id) {
        $timeline = get_user_meta($user_id, 'activity_timeline', true);
        if (!is_array($timeline)) {
            return array();
        }
        
        // Sort by timestamp (newest first)
        usort($timeline, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        
        return $timeline;
    }
    
    /**
     * Get activity counts by type
     */
    public function get_activity_counts_by_type($user_id) {
        $activity_log = get_user_meta($user_id, 'child_activity_log', true);
        if (!is_array($activity_log)) {
            return array();
        }
        
        $counts = array();
        foreach ($activity_log as $activity) {
            $type = $activity['type'];
            if (!isset($counts[$type])) {
                $counts[$type] = 0;
            }
            $counts[$type]++;
        }
        
        return $counts;
    }
    
    /**
     * Get completed games
     */
    public function get_completed_games($user_id) {
        $completed_games = get_user_meta($user_id, 'completed_games', true);
        if (!is_array($completed_games)) {
            $completed_games = array();
        }
        return $completed_games;
    }
    
    /**
     * Get completed activities
     */
    public function get_completed_activities($user_id) {
        $completed_activities = get_user_meta($user_id, 'completed_activities', true);
        if (!is_array($completed_activities)) {
            $completed_activities = array();
        }
        return $completed_activities;
    }
    
    /**
     * Get watched videos
     */
    public function get_watched_videos($user_id) {
        $watched_videos = get_user_meta($user_id, 'watched_videos', true);
        if (!is_array($watched_videos)) {
            $watched_videos = array();
        }
        return $watched_videos;
    }
    
    /**
     * Get weekly activity data for chart
     */
    public function get_weekly_activity_data($user_id) {
        $activity_log = get_user_meta($user_id, 'child_activity_log', true);
        if (!is_array($activity_log)) {
            return array(
                'labels' => array(),
                'points' => array(),
                'counts' => array()
            );
        }
        
        // Get dates for the past 7 days
        $dates = array();
        $labels = array();
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dates[] = $date;
            $labels[] = date_i18n('D', strtotime($date));
        }
        
        // Initialize data arrays
        $points_data = array_fill(0, 7, 0);
        $counts_data = array_fill(0, 7, 0);
        
        // Process activity log
        foreach ($activity_log as $activity) {
            $activity_date = date('Y-m-d', $activity['timestamp']);
            $date_index = array_search($activity_date, $dates);
            
            if ($date_index !== false) {
                $points_data[$date_index] += $activity['points'];
                $counts_data[$date_index]++;
            }
        }
        
        return array(
            'labels' => $labels,
            'points' => $points_data,
            'counts' => $counts_data
        );
    }
    
    /**
     * Get activity types
     */
    public function get_activity_types() {
        return $this->activity_types;
    }
    
    /**
     * Get user points
     */
    public function get_user_points($user_id) {
        return get_user_meta($user_id, 'activity_points', true) ?: 0;
    }
    
    /**
     * Get user total points
     */
    public function get_user_total_points($user_id) {
        return get_user_meta($user_id, 'total_points', true) ?: 0;
    }
}

/**
 * Get the Child Activity Tracker instance
 */
function sarah_loz_child_activity_tracker() {
    return Sarah_Loz_Child_Activity_Tracker::get_instance();
}

// Initialize the Child Activity Tracker
sarah_loz_child_activity_tracker();

/**
 * Helper functions for child activity tracking
 */

/**
 * Get child points
 */
function sarah_loz_get_child_points($user_id) {
    return get_user_meta($user_id, 'activity_points', true) ?: 0;
}

/**
 * Get child total points
 */
function sarah_loz_get_child_total_points($user_id) {
    $total_points = get_user_meta($user_id, 'total_points', true);
    if (!$total_points) {
        $activity_points = sarah_loz_get_child_points($user_id);
        $achievement_points = get_user_meta($user_id, 'achievement_points', true) ?: 0;
        $total_points = $activity_points + $achievement_points;
        update_user_meta($user_id, 'total_points', $total_points);
    }
    return intval($total_points);
}

/**
 * Record child activity
 */
function sarah_loz_record_child_activity($user_id, $activity_type, $content_id = 0, $additional_data = array()) {
    $tracker = sarah_loz_child_activity_tracker();
    if (method_exists($tracker, 'record_activity')) {
        return $tracker->record_activity($user_id, $activity_type, $content_id, $additional_data);
    }
    return false;
}
