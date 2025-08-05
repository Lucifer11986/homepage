<?php

// Include the database connection file
require_once '../database.php';

// Set the content type of the response to JSON
header('Content-Type: application/json');

// Prepare and execute the SQL query to fetch blog categories
$result = $db->query("SELECT name FROM blog_categories ORDER BY name ASC");

// Check if the query was successful
if ($result) {
    // Fetch all the categories into an associative array
    $categories = $result->fetch_all(MYSQLI_ASSOC);

    // Return the categories as a JSON response
    echo json_encode($categories);
} else {
    // If the query fails, return a 500 Internal Server Error
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch categories from the database.']);
}

// Close the database connection
$db->close();
