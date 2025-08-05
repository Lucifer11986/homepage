<?php

// Include the database connection file
require_once '../database.php';

// Set the content type of the response to JSON
header('Content-Type: application/json');

// Check if a category is provided in the GET request
$category = isset($_GET['category']) ? $_GET['category'] : null;

if ($category) {
    // Use a prepared statement to prevent SQL injection
    $stmt = $db->prepare("SELECT id, title, content, created_at, category, image_path FROM blog_posts WHERE category = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // If no category is specified, fetch all posts
    $result = $db->query("SELECT id, title, content, created_at, category, image_path FROM blog_posts ORDER BY created_at DESC");
}

// Check if the query was successful
if ($result) {
    // Fetch all the posts into an associative array
    $posts = $result->fetch_all(MYSQLI_ASSOC);

    // Return the posts as a JSON response
    echo json_encode($posts);
} else {
    // If the query fails, return a 500 Internal Server Error
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch posts from the database.']);
}

// Close the database connection
$db->close();
