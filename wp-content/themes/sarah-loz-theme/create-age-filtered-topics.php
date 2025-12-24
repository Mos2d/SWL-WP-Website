<?php
/**
 * Create Test Topics with Age Groups Script
 * This script creates sample topics with age group assignments for testing
 */

// Include WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('Access denied. Admin privileges required.');
}

// Sample topics data with age groups
$sample_topics = array(
    array(
        'name' => 'الرياضيات الأساسية',
        'slug' => 'basic-math',
        'description' => 'تعلم الرياضيات الأساسية بطريقة ممتعة ومفيدة',
        'color' => '#e74c3c',
        'order' => 1,
        'featured' => true,
        'icon' => 'fas fa-calculator',
        'age_groups' => array('3-5', '6-7')
    ),
    array(
        'name' => 'الرياضيات المتقدمة',
        'slug' => 'advanced-math',
        'description' => 'رياضيات متقدمة للأطفال الأكبر سناً',
        'color' => '#c0392b',
        'order' => 2,
        'featured' => false,
        'icon' => 'fas fa-square-root-alt',
        'age_groups' => array('8-9')
    ),
    array(
        'name' => 'العلوم للأطفال الصغار',
        'slug' => 'science-young',
        'description' => 'اكتشف أسرار العلوم للأطفال الصغار',
        'color' => '#3498db',
        'order' => 3,
        'featured' => true,
        'icon' => 'fas fa-flask',
        'age_groups' => array('3-5')
    ),
    array(
        'name' => 'العلوم التجريبية',
        'slug' => 'experimental-science',
        'description' => 'تجارب علمية للأطفال الأكبر سناً',
        'color' => '#2980b9',
        'order' => 4,
        'featured' => false,
        'icon' => 'fas fa-atom',
        'age_groups' => array('6-7', '8-9')
    ),
    array(
        'name' => 'اللغة العربية المبسطة',
        'slug' => 'simple-arabic',
        'description' => 'تعلم اللغة العربية بطريقة مبسطة',
        'color' => '#f39c12',
        'order' => 5,
        'featured' => false,
        'icon' => 'fas fa-book-open',
        'age_groups' => array('3-5', '6-7')
    ),
    array(
        'name' => 'الفنون والإبداع',
        'slug' => 'arts-creativity',
        'description' => 'اكتشف مواهبك الفنية والإبداعية',
        'color' => '#9b59b6',
        'order' => 6,
        'featured' => false,
        'icon' => 'fas fa-palette',
        'age_groups' => array('3-5', '6-7', '8-9') // All ages
    ),
    array(
        'name' => 'الرياضة والنشاط',
        'slug' => 'sports-activity',
        'description' => 'تعلم الرياضة والنشاط البدني',
        'color' => '#27ae60',
        'order' => 7,
        'featured' => false,
        'icon' => 'fas fa-running',
        'age_groups' => array('6-7', '8-9')
    )
);

echo '<h1>Creating Test Topics with Age Groups</h1>';

$created_count = 0;
$errors = array();

foreach ($sample_topics as $topic_data) {
    // Check if topic already exists
    $existing_term = get_term_by('slug', $topic_data['slug'], 'topic');
    
    if ($existing_term) {
        echo "<p>Topic '{$topic_data['name']}' already exists (ID: {$existing_term->term_id})</p>";
        continue;
    }
    
    // Create the topic
    $result = wp_insert_term(
        $topic_data['name'],
        'topic',
        array(
            'slug' => $topic_data['slug'],
            'description' => $topic_data['description']
        )
    );
    
    if (is_wp_error($result)) {
        $errors[] = "Failed to create topic '{$topic_data['name']}': " . $result->get_error_message();
        echo "<p style='color: red;'>✗ Failed to create topic '{$topic_data['name']}': " . $result->get_error_message() . "</p>";
    } else {
        $term_id = $result['term_id'];
        
        // Set custom fields
        update_term_meta($term_id, 'topic_color', $topic_data['color']);
        update_term_meta($term_id, 'topic_order', $topic_data['order']);
        update_term_meta($term_id, 'topic_featured', $topic_data['featured']);
        update_term_meta($term_id, 'topic_icon', $topic_data['icon']);
        update_term_meta($term_id, 'topic_age_groups', $topic_data['age_groups']);
        
        echo "<p style='color: green;'>✓ Created topic '{$topic_data['name']}' (ID: {$term_id})</p>";
        echo "<p style='color: blue; margin-left: 20px;'>Age groups: " . implode(', ', $topic_data['age_groups']) . "</p>";
        $created_count++;
    }
}

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<p>Created: {$created_count} topics with age groups</p>";

if (!empty($errors)) {
    echo "<h3>Errors:</h3>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li style='color: red;'>{$error}</li>";
    }
    echo "</ul>";
}

// Test the age filtering
echo "<hr>";
echo "<h2>Testing Age Filtering</h2>";

// Test different age groups
$test_age_groups = array('3-5', '6-7', '8-9');

foreach ($test_age_groups as $test_age) {
    echo "<h3>Testing Age Group: {$test_age}</h3>";
    
    // Get topics for this age group
    $topics = get_topics_with_age_filtering($test_age);
    
    if (!empty($topics) && !is_wp_error($topics)) {
        echo "<p>✓ Found " . count($topics) . " topics for age {$test_age}</p>";
        echo "<ul>";
        foreach ($topics as $topic) {
            echo "<li>{$topic->name}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠ No topics found for age {$test_age}</p>";
    }
}

echo "<hr>";
echo "<p><a href='../page-topics.php'>View Topics Page</a> | <a href='../wp-admin/edit-tags.php?taxonomy=topic'>Manage Topics in Admin</a></p>";

/**
 * Get topics with age filtering (copy of the function from page-topics.php)
 */
function get_topics_with_age_filtering($selected_age_group) {
    if (!$selected_age_group) {
        return get_terms(array(
            'taxonomy' => 'topic',
            'hide_empty' => false,
        ));
    }
    
    $all_topics = get_terms(array(
        'taxonomy' => 'topic',
        'hide_empty' => false,
    ));
    
    if (empty($all_topics) || is_wp_error($all_topics)) {
        return $all_topics;
    }
    
    $age_appropriate_topics = array();
    
    foreach ($all_topics as $topic) {
        if (topic_has_age_appropriate_content($topic->term_id, $selected_age_group)) {
            $age_appropriate_topics[] = $topic;
        }
    }
    
    return $age_appropriate_topics;
}

/**
 * Check if a topic has content appropriate for a specific age group
 */
function topic_has_age_appropriate_content($topic_id, $age_group) {
    // First check if the topic itself has age group restrictions
    $topic_age_groups = get_term_meta($topic_id, 'topic_age_groups', true);
    if (!empty($topic_age_groups) && is_array($topic_age_groups)) {
        if (!in_array($age_group, $topic_age_groups)) {
            return false;
        }
    }
    
    // For testing purposes, we'll consider topics with age groups as appropriate
    // In real usage, you'd also check the content
    return true;
}
?>
