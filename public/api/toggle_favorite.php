<?php
require_once('../../private/initialize.php');

header('Content-Type: application/json');

// Ensure request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Toggle favorite: Invalid method " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Ensure user is logged in
if (!$session->is_logged_in()) {
    error_log("Toggle favorite: User not logged in");
    http_response_code(401);
    echo json_encode(['error' => 'Must be logged in to favorite recipes']);
    exit;
}

// Get recipe_id from POST data
$recipe_id = $_POST['recipe_id'] ?? '';
if (empty($recipe_id)) {
    error_log("Toggle favorite: No recipe_id provided");
    http_response_code(400);
    echo json_encode(['error' => 'Recipe ID is required']);
    exit;
}

error_log("Toggle favorite: Processing request for recipe_id: " . $recipe_id . " and user_id: " . $session->get_user_id());

// Check if recipe exists
$recipe = Recipe::find_by_id($recipe_id);
if (!$recipe) {
    error_log("Toggle favorite: Recipe not found with ID " . $recipe_id);
    http_response_code(404);
    echo json_encode(['error' => 'Recipe not found']);
    exit;
}

// Try to find existing favorite
$favorite = UserFavorite::find_by_user_and_recipe($session->get_user_id(), $recipe_id);

try {
    if ($favorite) {
        // If exists, remove it
        error_log("Toggle favorite: Removing favorite");
        if ($favorite->delete()) {
            echo json_encode(['status' => 'removed']);
        } else {
            error_log("Toggle favorite: Error removing - " . print_r($favorite->errors, true));
            http_response_code(500);
            echo json_encode(['error' => 'Failed to remove favorite']);
        }
    } else {
        // If doesn't exist, add it
        error_log("Toggle favorite: Adding new favorite");
        $favorite = new UserFavorite([
            'user_id' => $session->get_user_id(),
            'recipe_id' => $recipe_id
        ]);
        if ($favorite->save()) {
            echo json_encode(['status' => 'added']);
        } else {
            error_log("Toggle favorite: Error saving - " . print_r($favorite->errors, true));
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add favorite']);
        }
    }
} catch (Exception $e) {
    error_log("Toggle favorite: Exception - " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
