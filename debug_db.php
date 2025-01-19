<?php
require_once('private/initialize.php');

/**
 * Debug Database Utility
 * 
 * This script provides various debugging functions for the FlavorConnect application.
 * Use it to test database connections, check table structures, and verify data integrity.
 */

// Helper function to print section headers
function print_section($title) {
    echo "\n" . str_repeat("=", 80) . "\n";
    echo $title . "\n";
    echo str_repeat("=", 80) . "\n";
}

// Helper function to print debug info
function debug_var($var, $label = '') {
    echo "\n" . ($label ? $label . ": " : "");
    var_dump($var);
    echo "\n";
}

// 1. Database Connection Test
print_section("Database Connection Test");
$database = DatabaseObject::get_database();
debug_var($database, "Database Object");

// 2. Table Structure Tests
print_section("Table Structure Tests");
$tables = [
    'user_account' => ['user_id', 'username', 'email', 'password_hash', 'user_level', 'is_active'],
    'recipe' => ['recipe_id', 'user_id', 'title', 'description', 'prep_time', 'cook_time', 'servings'],
    'recipe_ingredient' => ['recipe_id', 'ingredient_id', 'amount', 'measurement_id'],
    'recipe_step' => ['recipe_id', 'step_number', 'instruction'],
    'recipe_rating' => ['recipe_id', 'user_id', 'rating', 'comment', 'created_date']
];

foreach ($tables as $table => $expected_columns) {
    echo "\nChecking table: $table\n";
    $sql = "SHOW COLUMNS FROM $table";
    $result = $database->query($sql);
    if (!$result) {
        echo "Error: Unable to get columns for $table\n";
        echo "MySQL Error: " . $database->error . "\n";
        continue;
    }
    
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
        echo "  - {$row['Field']} ({$row['Type']})\n";
    }
    
    // Check for missing columns
    $missing = array_diff($expected_columns, $columns);
    if (!empty($missing)) {
        echo "WARNING: Missing expected columns in $table: " . implode(", ", $missing) . "\n";
    }
}

// 3. Sample Data Tests
print_section("Sample Data Tests");

// Test User queries
echo "\nTesting User queries:\n";
$test_username = "hcvaughn";
$user = User::find_by_username($test_username);
debug_var($user, "User found by username '$test_username'");

// Test Recipe queries
echo "\nTesting Recipe queries:\n";
$featured_recipes = Recipe::find_featured(2);
debug_var($featured_recipes, "Featured Recipes (limit 2)");

// 4. Database Stats
print_section("Database Statistics");

// Count records in main tables
$tables_to_count = ['user_account', 'recipe', 'recipe_ingredient', 'recipe_step', 'recipe_rating'];
foreach ($tables_to_count as $table) {
    $sql = "SELECT COUNT(*) as count FROM $table";
    $result = $database->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        echo "$table: {$row['count']} records\n";
    } else {
        echo "$table: Unable to count records\n";
    }
}

// 5. Data Integrity Checks
print_section("Data Integrity Checks");

// Check for recipes without steps
$sql = "SELECT r.recipe_id, r.title FROM recipe r 
        LEFT JOIN recipe_step rs ON r.recipe_id = rs.recipe_id 
        WHERE rs.recipe_id IS NULL";
$result = $database->query($sql);
if ($result && $result->num_rows > 0) {
    echo "\nWARNING: Found recipes without steps:\n";
    while ($row = $result->fetch_assoc()) {
        echo "  - Recipe ID {$row['recipe_id']}: {$row['title']}\n";
    }
}

// Check for recipes without ingredients
$sql = "SELECT r.recipe_id, r.title FROM recipe r 
        LEFT JOIN recipe_ingredient ri ON r.recipe_id = ri.recipe_id 
        WHERE ri.recipe_id IS NULL";
$result = $database->query($sql);
if ($result && $result->num_rows > 0) {
    echo "\nWARNING: Found recipes without ingredients:\n";
    while ($row = $result->fetch_assoc()) {
        echo "  - Recipe ID {$row['recipe_id']}: {$row['title']}\n";
    }
}

// Check for orphaned ratings (ratings for non-existent recipes)
$sql = "SELECT rr.recipe_id FROM recipe_rating rr 
        LEFT JOIN recipe r ON rr.recipe_id = r.recipe_id 
        WHERE r.recipe_id IS NULL";
$result = $database->query($sql);
if ($result && $result->num_rows > 0) {
    echo "\nWARNING: Found orphaned ratings:\n";
    while ($row = $result->fetch_assoc()) {
        echo "  - Rating for non-existent recipe ID: {$row['recipe_id']}\n";
    }
}

// 6. Performance Checks
print_section("Performance Checks");

// Check if important columns are indexed
$indexes_to_check = [
    'recipe' => ['recipe_id', 'user_id'],
    'recipe_ingredient' => ['recipe_id', 'ingredient_id'],
    'recipe_rating' => ['recipe_id', 'user_id'],
    'user_account' => ['user_id', 'username', 'email']
];

foreach ($indexes_to_check as $table => $columns) {
    echo "\nChecking indexes for $table:\n";
    $sql = "SHOW INDEX FROM $table";
    $result = $database->query($sql);
    if ($result) {
        $indexes = [];
        while ($row = $result->fetch_assoc()) {
            $indexes[] = $row['Column_name'];
            echo "  - {$row['Column_name']} is indexed ({$row['Key_name']})\n";
        }
        
        // Check for missing indexes
        $missing = array_diff($columns, $indexes);
        if (!empty($missing)) {
            echo "WARNING: Missing indexes on columns: " . implode(", ", $missing) . "\n";
        }
    }
}

print_section("Debug Complete");
?>
