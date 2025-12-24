<?php
/**
 * Achievements and Rewards System
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Sarah_Loz_Achievements {
    private static $instance = null;
    private $achievements = array();

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->register_achievements();
        $this->add_hooks();
    }

    private function register_achievements() {
        // Games Achievements
        $this->achievements['games'] = array(
            'first_game' => array(
                'title' => 'أول لعبة',
                'description' => 'أكمل أول لعبة',
                'points' => 10,
                'icon' => 'fas fa-gamepad',
                'color' => 'primary'
            ),
            'game_master' => array(
                'title' => 'سيد الألعاب',
                'description' => 'أكمل 10 ألعاب',
                'points' => 50,
                'icon' => 'fas fa-trophy',
                'color' => 'primary'
            ),
        );

        // Activities Achievements
        $this->achievements['activities'] = array(
            'first_activity' => array(
                'title' => 'أول نشاط',
                'description' => 'أكمل أول نشاط',
                'points' => 10,
                'icon' => 'fas fa-paint-brush',
                'color' => 'secondary'
            ),
            'activity_master' => array(
                'title' => 'سيد الأنشطة',
                'description' => 'أكمل 10 أنشطة',
                'points' => 50,
                'icon' => 'fas fa-star',
                'color' => 'secondary'
            ),
        );

        // Videos Achievements
        $this->achievements['videos'] = array(
            'first_video' => array(
                'title' => 'أول فيديو',
                'description' => 'شاهد أول فيديو',
                'points' => 10,
                'icon' => 'fas fa-play-circle',
                'color' => 'accent'
            ),
            'video_master' => array(
                'title' => 'مشاهد نشط',
                'description' => 'شاهد 10 فيديوهات',
                'points' => 50,
                'icon' => 'fas fa-film',
                'color' => 'accent'
            ),
        );

        // Special Achievements
        $this->achievements['special'] = array(
            'daily_streak' => array(
                'title' => 'المثابر',
                'description' => 'تسجيل دخول لمدة 7 أيام متتالية',
                'points' => 100,
                'icon' => 'fas fa-calendar-check',
                'color' => 'success'
            ),
            'all_rounder' => array(
                'title' => 'متعدد المواهب',
                'description' => 'أكمل لعبة ونشاط وفيديو',
                'points' => 100,
                'icon' => 'fas fa-award',
                'color' => 'success'
            ),
        );

        // Practices Achievements
        $this->achievements['practices'] = array(
            'first_practice' => array(
                'title' => 'أول تدريب',
                'description' => 'أكمل أول تدريب',
                'points' => 10,
                'icon' => 'fas fa-tasks',
                'color' => 'warning'
            ),
            'practice_master' => array(
                'title' => 'سيد التدريبات',
                'description' => 'أكمل 10 تدريبات',
                'points' => 50,
                'icon' => 'fas fa-graduation-cap',
                'color' => 'warning'
            ),
            'perfect_score' => array(
                'title' => 'العلامة الكاملة',
                'description' => 'احصل على علامة كاملة في تدريب',
                'points' => 30,
                'icon' => 'fas fa-star',
                'color' => 'warning'
            ),
        );
    }

    private function add_hooks() {
        // Track game completion
        add_action('sarah_loz_game_completed', array($this, 'check_game_achievements'), 10, 2);
        
        // Track activity completion
        add_action('sarah_loz_activity_completed', array($this, 'check_activity_achievements'), 10, 2);
        
        // Track video watching
        add_action('sarah_loz_video_watched', array($this, 'check_video_achievements'), 10, 2);
        
        // Track daily login
        add_action('wp_login', array($this, 'check_login_streak'), 10, 2);
        
        // Track practice completion
        add_action('sarah_loz_practice_completed', array($this, 'check_practice_achievements'), 10, 3);
    }

    public function check_game_achievements($user_id, $game_id) {
        $completed_games = get_user_meta($user_id, 'completed_games', true);
        if (!is_array($completed_games)) {
            $completed_games = array();
        }

        // First game achievement
        if (count($completed_games) === 1) {
            $this->award_achievement($user_id, 'first_game');
        }

        // Game master achievement
        if (count($completed_games) === 10) {
            $this->award_achievement($user_id, 'game_master');
        }

        $this->check_all_rounder($user_id);
    }

    public function check_activity_achievements($user_id, $activity_id) {
        $completed_activities = get_user_meta($user_id, 'completed_activities', true);
        if (!is_array($completed_activities)) {
            $completed_activities = array();
        }

        // First activity achievement
        if (count($completed_activities) === 1) {
            $this->award_achievement($user_id, 'first_activity');
        }

        // Activity master achievement
        if (count($completed_activities) === 10) {
            $this->award_achievement($user_id, 'activity_master');
        }

        $this->check_all_rounder($user_id);
    }

    public function check_video_achievements($user_id, $video_id) {
        $watched_videos = get_user_meta($user_id, 'watched_videos', true);
        if (!is_array($watched_videos)) {
            $watched_videos = array();
        }

        // First video achievement
        if (count($watched_videos) === 1) {
            $this->award_achievement($user_id, 'first_video');
        }

        // Video master achievement
        if (count($watched_videos) === 10) {
            $this->award_achievement($user_id, 'video_master');
        }

        $this->check_all_rounder($user_id);
    }

    public function check_login_streak($user_login, $user) {
        $streak = get_user_meta($user->ID, 'login_streak', true);
        $last_login = get_user_meta($user->ID, 'last_login_date', true);
        $today = current_time('Y-m-d');

        if (!$streak) {
            $streak = 1;
        } else {
            if ($last_login === date('Y-m-d', strtotime('-1 day'))) {
                $streak++;
            } elseif ($last_login !== $today) {
                $streak = 1;
            }
        }

        update_user_meta($user->ID, 'login_streak', $streak);
        update_user_meta($user->ID, 'last_login_date', $today);

        if ($streak === 7) {
            $this->award_achievement($user->ID, 'daily_streak');
        }
    }

    public function check_practice_achievements($user_id, $practice_id, $score_percentage) {
        $completed_practices = get_user_meta($user_id, 'completed_practices', true);
        if (!is_array($completed_practices)) {
            $completed_practices = array();
        }

        // First practice achievement
        if (count($completed_practices) === 1) {
            $this->award_achievement($user_id, 'first_practice');
        }

        // Practice master achievement
        if (count($completed_practices) === 10) {
            $this->award_achievement($user_id, 'practice_master');
        }

        // Perfect score achievement
        if ($score_percentage === 100) {
            $this->award_achievement($user_id, 'perfect_score');
        }

        $this->check_all_rounder($user_id);
    }

    private function check_all_rounder($user_id) {
        $completed_games = get_user_meta($user_id, 'completed_games', true);
        $completed_activities = get_user_meta($user_id, 'completed_activities', true);
        $watched_videos = get_user_meta($user_id, 'watched_videos', true);
        $completed_practices = get_user_meta($user_id, 'completed_practices', true);

        if (!empty($completed_games) && !empty($completed_activities) && !empty($watched_videos) && !empty($completed_practices)) {
            $this->award_achievement($user_id, 'all_rounder');
        }
    }

    public function award_achievement($user_id, $achievement_key) {
        $user_achievements = get_user_meta($user_id, 'user_achievements', true);
        if (!is_array($user_achievements)) {
            $user_achievements = array();
        }

        // Check if achievement already awarded
        if (in_array($achievement_key, $user_achievements)) {
            return;
        }

        // Find achievement details
        $achievement = null;
        foreach ($this->achievements as $category) {
            if (isset($category[$achievement_key])) {
                $achievement = $category[$achievement_key];
                break;
            }
        }

        if (!$achievement) {
            return;
        }

        // Award achievement
        $user_achievements[] = $achievement_key;
        update_user_meta($user_id, 'user_achievements', $user_achievements);

        // Add points
        $current_points = get_user_meta($user_id, 'achievement_points', true);
        $current_points = $current_points ? $current_points + $achievement['points'] : $achievement['points'];
        update_user_meta($user_id, 'achievement_points', $current_points);

        // Trigger notification
        do_action('sarah_loz_achievement_earned', $user_id, $achievement_key, $achievement);
    }

    public function get_user_achievements($user_id) {
        $user_achievements = get_user_meta($user_id, 'user_achievements', true);
        if (!is_array($user_achievements)) {
            return array();
        }

        $achievements_data = array();
        foreach ($user_achievements as $achievement_key) {
            foreach ($this->achievements as $category) {
                if (isset($category[$achievement_key])) {
                    $achievements_data[] = array_merge(
                        array('key' => $achievement_key),
                        $category[$achievement_key]
                    );
                    break;
                }
            }
        }

        return $achievements_data;
    }

    public function get_available_achievements() {
        $all_achievements = array();
        foreach ($this->achievements as $category => $achievements) {
            foreach ($achievements as $key => $achievement) {
                $all_achievements[] = array_merge(
                    array('key' => $key, 'category' => $category),
                    $achievement
                );
            }
        }
        return $all_achievements;
    }

    public function get_user_points($user_id) {
        return get_user_meta($user_id, 'achievement_points', true) ?: 0;
    }
}

// Initialize the achievements system
function sarah_loz_achievements() {
    return Sarah_Loz_Achievements::get_instance();
}
add_action('init', 'sarah_loz_achievements');
