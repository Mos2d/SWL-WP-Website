<?php
/**
 * Template Name: Test Account Tabs
 * 
 * A diagnostic template to test and fix WooCommerce account tabs
 */

get_header();
?>

<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Test Account Tabs</h1>
    
    <?php if (is_user_logged_in()) : 
        $current_user = wp_get_current_user();
        $user_roles = $current_user->roles;
    ?>
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-xl font-bold mb-2">Current User Information</h2>
            <p><strong>Username:</strong> <?php echo esc_html($current_user->user_login); ?></p>
            <p><strong>Display Name:</strong> <?php echo esc_html($current_user->display_name); ?></p>
            <p><strong>User Roles:</strong> <?php echo implode(', ', $user_roles); ?></p>
            
            <?php
            // Check if user is parent or child
            $is_parent = in_array('parent', $user_roles);
            $is_child = in_array('child', $user_roles);
            
            if ($is_parent) {
                echo '<p class="text-green-600 font-bold">User is a Parent</p>';
            } elseif ($is_child) {
                echo '<p class="text-blue-600 font-bold">User is a Child</p>';
            } else {
                echo '<p class="text-red-600 font-bold">User is neither Parent nor Child</p>';
            }
            ?>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-xl font-bold mb-2">Fix Account Tabs</h2>
            
            <p class="mb-4">Click the button below to fix the account tabs:</p>
            
            <form method="post" action="">
                <?php wp_nonce_field('fix_account_tabs', 'fix_account_tabs_nonce'); ?>
                <button type="submit" name="fix_account_tabs" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Fix Account Tabs
                </button>
            </form>
            
            <?php
            // Process form submission
            if (isset($_POST['fix_account_tabs']) && isset($_POST['fix_account_tabs_nonce']) && wp_verify_nonce($_POST['fix_account_tabs_nonce'], 'fix_account_tabs')) {
                // Force user role
                if ($is_parent) {
                    // Ensure user has parent role
                    $current_user->set_role('parent');
                    echo '<p class="text-green-600 mt-4">Parent role enforced.</p>';
                } elseif ($is_child) {
                    // Ensure user has child role
                    $current_user->set_role('child');
                    echo '<p class="text-green-600 mt-4">Child role enforced.</p>';
                }
                
                // Force flush rewrite rules
                flush_rewrite_rules();
                echo '<p class="text-green-600">Rewrite rules flushed.</p>';
                
                // Clear transients
                delete_transient('wc_account_menu_items');
                echo '<p class="text-green-600">WooCommerce transients cleared.</p>';
                
                // Re-register endpoints
                add_rewrite_endpoint('child-dashboard', EP_ROOT | EP_PAGES);
                add_rewrite_endpoint('child-reports', EP_ROOT | EP_PAGES);
                add_rewrite_endpoint('child-settings', EP_ROOT | EP_PAGES);
                flush_rewrite_rules();
                echo '<p class="text-green-600">Endpoints re-registered.</p>';
                
                echo '<p class="text-green-600 font-bold mt-4">Account tabs should now be fixed. Please check your account page.</p>';
                echo '<p><a href="' . esc_url(wc_get_account_endpoint_url('dashboard')) . '" class="text-blue-500 underline">Go to My Account</a></p>';
            }
            ?>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-xl font-bold mb-2">Current Account Menu Items</h2>
            
            <?php
            // Get account menu items
            $items = wc_get_account_menu_items();
            
            if (!empty($items)) {
                echo '<ul class="list-disc pl-6">';
                foreach ($items as $endpoint => $label) {
                    echo '<li><strong>' . esc_html($endpoint) . ':</strong> ' . esc_html($label) . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p class="text-red-600">No account menu items found.</p>';
            }
            ?>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-bold mb-2">Registered Endpoints</h2>
            
            <?php
            global $wp_rewrite;
            $endpoints = $wp_rewrite->endpoints;
            
            if (!empty($endpoints)) {
                echo '<ul class="list-disc pl-6">';
                foreach ($endpoints as $endpoint) {
                    echo '<li><strong>' . esc_html($endpoint[1]) . '</strong> - Mask: ' . esc_html($endpoint[0]) . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p class="text-red-600">No endpoints registered.</p>';
            }
            ?>
            
            <div class="mt-6 p-4 bg-gray-100 rounded">
                <h3 class="text-lg font-bold mb-2">Manual Fix Instructions</h3>
                <ol class="list-decimal pl-6">
                    <li>Go to WordPress Admin → Settings → Permalinks</li>
                    <li>Click "Save Changes" without making any changes</li>
                    <li>Clear your browser cache</li>
                    <li>Log out and log back in</li>
                    <li>Check your account page again</li>
                </ol>
            </div>
        </div>
    <?php else : ?>
        <div class="bg-yellow-100 p-4 rounded-lg border border-yellow-400">
            <p>Please log in to view and fix account tabs.</p>
            <p class="mt-2">
                <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 inline-block mt-2">
                    Log In
                </a>
            </p>
        </div>
    <?php endif; ?>
</div>

<?php
get_footer();
