<?php
/**
 * Template Name: Debug Roles
 */

get_header();
?>

<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">User Role Debug</h1>
    
    <?php if (is_user_logged_in()) : 
        $current_user = wp_get_current_user();
        $user_roles = $current_user->roles;
    ?>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-bold mb-2">Current User Information</h2>
            <p><strong>Username:</strong> <?php echo esc_html($current_user->user_login); ?></p>
            <p><strong>Display Name:</strong> <?php echo esc_html($current_user->display_name); ?></p>
            <p><strong>Email:</strong> <?php echo esc_html($current_user->user_email); ?></p>
            <p><strong>ID:</strong> <?php echo esc_html($current_user->ID); ?></p>
            
            <h3 class="text-lg font-bold mt-4 mb-2">User Roles:</h3>
            <ul class="list-disc pl-6">
                <?php foreach ($user_roles as $role) : ?>
                    <li><?php echo esc_html($role); ?></li>
                <?php endforeach; ?>
            </ul>
            
            <h3 class="text-lg font-bold mt-4 mb-2">Custom User Meta:</h3>
            <div class="bg-gray-100 p-4 rounded">
                <?php 
                // Display parent-specific meta
                if (in_array('parent', $user_roles)) {
                    $parent_id = get_user_meta($current_user->ID, 'parent_id', true);
                    $children = get_user_meta($current_user->ID, 'children', true);
                    
                    echo '<p><strong>Parent ID:</strong> ' . esc_html($parent_id) . '</p>';
                    
                    if (is_array($children) && !empty($children)) {
                        echo '<p><strong>Children:</strong></p>';
                        echo '<ul class="list-disc pl-6">';
                        foreach ($children as $child_id) {
                            $child = get_user_by('ID', $child_id);
                            if ($child) {
                                echo '<li>' . esc_html($child->display_name) . ' (ID: ' . esc_html($child_id) . ')</li>';
                            } else {
                                echo '<li>Invalid child ID: ' . esc_html($child_id) . '</li>';
                            }
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>No children associated with this parent.</p>';
                    }
                }
                
                // Display child-specific meta
                if (in_array('child', $user_roles)) {
                    $child_name = get_user_meta($current_user->ID, 'child_name', true);
                    $child_age = get_user_meta($current_user->ID, 'child_age', true);
                    $parent_id = get_user_meta($current_user->ID, 'parent_id', true);
                    
                    echo '<p><strong>Child Name:</strong> ' . esc_html($child_name) . '</p>';
                    echo '<p><strong>Child Age:</strong> ' . esc_html($child_age) . '</p>';
                    echo '<p><strong>Parent ID:</strong> ' . esc_html($parent_id) . '</p>';
                    
                    if ($parent_id) {
                        $parent = get_user_by('ID', $parent_id);
                        if ($parent) {
                            echo '<p><strong>Parent Name:</strong> ' . esc_html($parent->display_name) . '</p>';
                        } else {
                            echo '<p><strong>Parent:</strong> Invalid parent ID</p>';
                        }
                    }
                }
                ?>
            </div>
            
            <h3 class="text-lg font-bold mt-4 mb-2">WooCommerce Account Endpoints:</h3>
            <div class="bg-gray-100 p-4 rounded">
                <?php
                // Check if endpoints are registered
                global $wp_rewrite;
                $endpoints = $wp_rewrite->endpoints;
                
                if (!empty($endpoints)) {
                    echo '<ul class="list-disc pl-6">';
                    foreach ($endpoints as $endpoint) {
                        if (in_array($endpoint[1], ['child-dashboard', 'child-reports', 'child-settings'])) {
                            echo '<li>' . esc_html($endpoint[1]) . ' - Mask: ' . esc_html($endpoint[0]) . '</li>';
                        }
                    }
                    echo '</ul>';
                } else {
                    echo '<p>No endpoints found.</p>';
                }
                
                // Display account menu items
                $items = wc_get_account_menu_items();
                if (!empty($items)) {
                    echo '<p><strong>Account Menu Items:</strong></p>';
                    echo '<ul class="list-disc pl-6">';
                    foreach ($items as $endpoint => $label) {
                        echo '<li>' . esc_html($endpoint) . ': ' . esc_html($label) . '</li>';
                    }
                    echo '</ul>';
                }
                ?>
            </div>
        </div>
    <?php else : ?>
        <div class="bg-yellow-100 p-4 rounded-lg border border-yellow-400">
            <p>Please log in to view user role information.</p>
            <p class="mt-2">
                <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Log In
                </a>
            </p>
        </div>
    <?php endif; ?>
</div>

<?php
get_footer();
