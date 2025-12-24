<?php
/**
 * Game Admin Dashboard
 * Admin interface for managing and viewing interactive game statistics
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Add game stats menu item to WordPress admin
 */
function sarah_loz_add_game_stats_menu() {
    // Main games menu
    add_menu_page(
        __('Games Management', 'sarah-loz'),
        __('Games', 'sarah-loz'),
        'manage_options',
        'sarah_loz_games',
        'sarah_loz_games_main_page',
        'dashicons-games',
        30
    );
    
    // Game statistics submenu
    add_submenu_page(
        'sarah_loz_games',
        __('Game Statistics', 'sarah-loz'),
        __('Game Statistics', 'sarah-loz'),
        'manage_options',
        'sarah-loz-game-stats',
        'sarah_loz_game_stats_page'
    );
    
    // Traditional games submenu (keep existing functionality)
    add_submenu_page(
        'sarah_loz_games',
        __('Traditional Games', 'sarah-loz'),
        __('Traditional Games', 'sarah-loz'),
        'manage_options',
        'edit.php?post_type=game'
    );
}
add_action('admin_menu', 'sarah_loz_add_game_stats_menu');

/**
 * Display the main games page
 */
function sarah_loz_games_main_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Games Management', 'sarah-loz'); ?></h1>
        
        <div class="card">
            <h2><?php _e('Game Types', 'sarah-loz'); ?></h2>
            <p><?php _e('Manage different types of educational games for children.', 'sarah-loz'); ?></p>
            
            <div class="game-types-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;">
                
                <div class="game-type-card" style="border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
                    <h3><?php _e('Audio Matching Games', 'sarah-loz'); ?></h3>
                    <p><?php _e('Interactive audio games where children listen and match sounds with visual options. Now integrated with the main games system.', 'sarah-loz'); ?></p>
                    <a href="<?php echo admin_url('edit.php?post_type=game'); ?>" class="button button-primary">
                        <?php _e('Create Audio Game', 'sarah-loz'); ?>
                    </a>
                </div>
                
                <div class="game-type-card" style="border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
                    <h3><?php _e('Traditional Games', 'sarah-loz'); ?></h3>
                    <p><?php _e('Memory games, sorting games, and other traditional interactive games.', 'sarah-loz'); ?></p>
                    <a href="<?php echo admin_url('edit.php?post_type=game'); ?>" class="button button-primary">
                        <?php _e('Manage Traditional Games', 'sarah-loz'); ?>
                    </a>
                </div>
                
                <div class="game-type-card" style="border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
                    <h3><?php _e('Game Statistics', 'sarah-loz'); ?></h3>
                    <p><?php _e('View detailed statistics and analytics for all games.', 'sarah-loz'); ?></p>
                    <a href="<?php echo admin_url('admin.php?page=sarah-loz-game-stats'); ?>" class="button button-secondary">
                        <?php _e('View Statistics', 'sarah-loz'); ?>
                    </a>
                </div>
                
            </div>
        </div>
        
        <div class="card">
            <h2><?php _e('Quick Actions', 'sarah-loz'); ?></h2>
            <p>
                <a href="<?php echo admin_url('post-new.php?post_type=game'); ?>" class="button button-primary">
                    <?php _e('Create New Game', 'sarah-loz'); ?>
                </a>
                <a href="<?php echo admin_url('edit.php?post_type=game'); ?>" class="button button-primary">
                    <?php _e('Manage All Games', 'sarah-loz'); ?>
                </a>
                <a href="<?php echo admin_url('admin.php?page=sarah-loz-game-stats'); ?>" class="button button-secondary">
                    <?php _e('View All Statistics', 'sarah-loz'); ?>
                </a>
            </p>
        </div>
    </div>
    <?php
}

/**
 * Display the game statistics page
 */
function sarah_loz_game_stats_page() {
    // Get all published games
    $games = get_posts(array(
        'post_type' => 'game',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    ));
    
    // Get selected game ID from query string
    $selected_game_id = isset($_GET['game_id']) ? intval($_GET['game_id']) : 0;
    
    ?>
    <div class="wrap">
        <h1><?php _e('Interactive Game Statistics', 'sarah-loz'); ?></h1>
        
        <h2 class="screen-reader-text"><?php _e('Filter games', 'sarah-loz'); ?></h2>
        <div class="tablenav top">
            <div class="alignleft actions">
                <form method="get">
                    <input type="hidden" name="post_type" value="game">
                    <input type="hidden" name="page" value="sarah-loz-game-stats">
                    
                    <select name="game_id" id="filter-by-game">
                        <option value="0"><?php _e('All Games', 'sarah-loz'); ?></option>
                        <?php foreach ($games as $game) : ?>
                            <option value="<?php echo $game->ID; ?>" <?php selected($selected_game_id, $game->ID); ?>>
                                <?php echo esc_html($game->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <input type="submit" class="button" value="<?php _e('Filter', 'sarah-loz'); ?>">
                </form>
            </div>
            <br class="clear">
        </div>
        
        <?php
        // Display overall stats
        $total_play_count = 0;
        foreach ($games as $game) {
            $play_count = (int)get_post_meta($game->ID, 'game_play_count', true);
            $total_play_count += $play_count;
        }
        ?>
        
        <div class="sarah-loz-stats-cards">
            <div class="sarah-loz-stats-card">
                <h2><?php _e('Total Games', 'sarah-loz'); ?></h2>
                <span class="sarah-loz-stats-number"><?php echo count($games); ?></span>
            </div>
            
            <div class="sarah-loz-stats-card">
                <h2><?php _e('Total Plays', 'sarah-loz'); ?></h2>
                <span class="sarah-loz-stats-number"><?php echo $total_play_count; ?></span>
            </div>
            
            <?php if (function_exists('count_users')) : 
                $users = count_users();
                ?>
                <div class="sarah-loz-stats-card">
                    <h2><?php _e('Total Players', 'sarah-loz'); ?></h2>
                    <span class="sarah-loz-stats-number"><?php echo $users['total_users']; ?></span>
                </div>
            <?php endif; ?>
        </div>
        
        <?php
        // If specific game is selected, show its detailed stats
        if ($selected_game_id > 0) {
            $game = get_post($selected_game_id);
            
            if ($game && $game->post_type === 'game') {
                $play_count = (int)get_post_meta($selected_game_id, 'game_play_count', true);
                $high_score = (int)get_post_meta($selected_game_id, 'game_high_score', true);
                
                // Get game type and settings
                $game_type = get_field('game_type', $selected_game_id);
                $interactive_type = get_field('interactive_game_type', $selected_game_id);
                
                echo '<h2>' . esc_html($game->post_title) . ' ' . __('Statistics', 'sarah-loz') . '</h2>';
                
                ?>
                <div class="sarah-loz-stats-cards">
                    <div class="sarah-loz-stats-card">
                        <h2><?php _e('Game Type', 'sarah-loz'); ?></h2>
                        <span class="sarah-loz-stats-text">
                            <?php 
                            if ($game_type === 'interactive') {
                                echo ucfirst($interactive_type) . ' ' . __('Game', 'sarah-loz');
                            } else {
                                _e('External Embed', 'sarah-loz');
                            }
                            ?>
                        </span>
                    </div>
                    
                    <div class="sarah-loz-stats-card">
                        <h2><?php _e('Total Plays', 'sarah-loz'); ?></h2>
                        <span class="sarah-loz-stats-number"><?php echo $play_count; ?></span>
                    </div>
                    
                    <?php if ($game_type === 'interactive') : ?>
                        <div class="sarah-loz-stats-card">
                            <h2><?php _e('High Score', 'sarah-loz'); ?></h2>
                            <span class="sarah-loz-stats-number"><?php echo $high_score; ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <h3><?php _e('Top Players', 'sarah-loz'); ?></h3>
                
                <?php
                // Get leaderboard for this game
                $leaderboard = sarah_loz_game_leaderboard($selected_game_id);
                
                if (!empty($leaderboard)) {
                    ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th scope="col"><?php _e('Rank', 'sarah-loz'); ?></th>
                                <th scope="col"><?php _e('Player', 'sarah-loz'); ?></th>
                                <th scope="col"><?php _e('High Score', 'sarah-loz'); ?></th>
                                <th scope="col"><?php _e('Plays', 'sarah-loz'); ?></th>
                                <th scope="col"><?php _e('Last Played', 'sarah-loz'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leaderboard as $index => $player) : ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <a href="<?php echo admin_url('user-edit.php?user_id=' . $player['user_id']); ?>">
                                            <?php echo esc_html($player['display_name']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo esc_html($player['high_score']); ?></td>
                                    <td><?php echo esc_html($player['plays']); ?></td>
                                    <td><?php echo !empty($player['last_played']) ? date_i18n(get_option('date_format'), strtotime($player['last_played'])) : '-'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php
                } else {
                    echo '<div class="notice notice-info"><p>' . __('No player data available for this game yet.', 'sarah-loz') . '</p></div>';
                }
            }
        } else {
            // If no specific game selected, show general stats for all games
            ?>
            <h3><?php _e('Games Overview', 'sarah-loz'); ?></h3>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col"><?php _e('Game', 'sarah-loz'); ?></th>
                        <th scope="col"><?php _e('Type', 'sarah-loz'); ?></th>
                        <th scope="col"><?php _e('Plays', 'sarah-loz'); ?></th>
                        <th scope="col"><?php _e('High Score', 'sarah-loz'); ?></th>
                        <th scope="col"><?php _e('Actions', 'sarah-loz'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($games as $game) : 
                        $play_count = (int)get_post_meta($game->ID, 'game_play_count', true);
                        $high_score = (int)get_post_meta($game->ID, 'game_high_score', true);
                        $game_type = get_field('game_type', $game->ID);
                        $interactive_type = get_field('interactive_game_type', $game->ID);
                        ?>
                        <tr>
                            <td>
                                <strong>
                                    <a href="<?php echo admin_url('post.php?post=' . $game->ID . '&action=edit'); ?>">
                                        <?php echo esc_html($game->post_title); ?>
                                    </a>
                                </strong>
                            </td>
                            <td>
                                <?php 
                                if ($game_type === 'interactive') {
                                    echo ucfirst($interactive_type) . ' ' . __('Game', 'sarah-loz');
                                } else {
                                    _e('External Embed', 'sarah-loz');
                                }
                                ?>
                            </td>
                            <td><?php echo $play_count; ?></td>
                            <td><?php echo $game_type === 'interactive' ? $high_score : '-'; ?></td>
                            <td>
                                <a href="<?php echo admin_url('edit.php?post_type=game&page=sarah-loz-game-stats&game_id=' . $game->ID); ?>" class="button">
                                    <?php _e('View Stats', 'sarah-loz'); ?>
                                </a>
                                <a href="<?php echo get_permalink($game->ID); ?>" class="button" target="_blank">
                                    <?php _e('View Game', 'sarah-loz'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php
        }
        ?>
    </div>
    
    <style>
    .sarah-loz-stats-cards {
        display: flex;
        gap: 20px;
        margin: 20px 0;
    }
    
    .sarah-loz-stats-card {
        background-color: #fff;
        border: 1px solid #ccd0d4;
        border-radius: 4px;
        padding: 20px;
        flex: 1;
        max-width: 250px;
        text-align: center;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
    }
    
    .sarah-loz-stats-card h2 {
        margin: 0 0 10px 0;
        font-size: 16px;
        font-weight: 600;
        color: #23282d;
    }
    
    .sarah-loz-stats-number {
        font-size: 32px;
        font-weight: 700;
        color: #0073aa;
    }
    
    .sarah-loz-stats-text {
        font-size: 18px;
        color: #23282d;
    }
    </style>
    <?php
}

/**
 * Add game analytics metabox to game edit screen
 */
function sarah_loz_add_game_stats_metabox() {
    add_meta_box(
        'sarah_loz_game_stats',
        __('Game Statistics', 'sarah-loz'),
        'sarah_loz_game_stats_metabox',
        'game',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'sarah_loz_add_game_stats_metabox');

/**
 * Display game stats metabox content
 */
function sarah_loz_game_stats_metabox($post) {
    $play_count = (int)get_post_meta($post->ID, 'game_play_count', true);
    $high_score = (int)get_post_meta($post->ID, 'game_high_score', true);
    $game_type = get_field('game_type', $post->ID);
    
    echo '<p><strong>' . __('Total Plays:', 'sarah-loz') . '</strong> ' . $play_count . '</p>';
    
    if ($game_type === 'interactive') {
        echo '<p><strong>' . __('High Score:', 'sarah-loz') . '</strong> ' . $high_score . '</p>';
    }
    
    echo '<p><a href="' . admin_url('edit.php?post_type=game&page=sarah-loz-game-stats&game_id=' . $post->ID) . '" class="button">' . __('View Detailed Stats', 'sarah-loz') . '</a></p>';
}

/**
 * Add interactive game information to the admin games list
 */
function sarah_loz_add_game_type_column($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        
        // Add custom columns after title
        if ($key === 'title') {
            $new_columns['game_type'] = __('Game Type', 'sarah-loz');
            $new_columns['game_plays'] = __('Plays', 'sarah-loz');
        }
    }
    
    return $new_columns;
}
add_filter('manage_game_posts_columns', 'sarah_loz_add_game_type_column');

/**
 * Display game type and play count in admin column
 */
function sarah_loz_display_game_columns($column, $post_id) {
    switch ($column) {
        case 'game_type':
            $game_type = get_field('game_type', $post_id);
            if ($game_type === 'interactive') {
                $interactive_type = get_field('interactive_game_type', $post_id);
                echo ucfirst($interactive_type) . ' ' . __('Game', 'sarah-loz');
            } else {
                _e('External Embed', 'sarah-loz');
            }
            break;
            
        case 'game_plays':
            $play_count = (int)get_post_meta($post_id, 'game_play_count', true);
            echo $play_count;
            break;
    }
}
add_action('manage_game_posts_custom_column', 'sarah_loz_display_game_columns', 10, 2);

/**
 * Make custom columns sortable
 */
function sarah_loz_sortable_game_columns($columns) {
    $columns['game_plays'] = 'game_plays';
    $columns['game_type'] = 'game_type';
    return $columns;
}
add_filter('manage_edit-game_sortable_columns', 'sarah_loz_sortable_game_columns');

/**
 * Add sorting logic
 */
function sarah_loz_sort_game_columns($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->get('post_type') === 'game') {
        if ($query->get('orderby') === 'game_plays') {
            $query->set('meta_key', 'game_play_count');
            $query->set('orderby', 'meta_value_num');
        }
        
        if ($query->get('orderby') === 'game_type') {
            $query->set('meta_key', 'game_type');
            $query->set('orderby', 'meta_value');
        }
    }
}
add_action('pre_get_posts', 'sarah_loz_sort_game_columns'); 