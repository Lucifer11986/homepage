<?php

// Include the database connection file
require_once '../database.php';

// Set the content type of the response to JSON
header('Content-Type: application/json');

// Base query
$sql = "SELECT id, title, content, created_at, category, image_path FROM blog_posts";
$params = [];
$types = "";

// Check for 'category' parameter
if (isset($_GET['category'])) {
    $sql .= " WHERE category = ?";
    $params[] = $_GET['category'];
    $types .= "s";
}

$sql .= " ORDER BY created_at DESC";

// Check for 'limit' parameter
if (isset($_GET['limit'])) {
    $sql .= " LIMIT ?";
    $params[] = $_GET['limit'];
    $types .= "i";
}

// Prepare and execute the SQL query
if (!empty($params)) {
    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $db->query($sql);
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
