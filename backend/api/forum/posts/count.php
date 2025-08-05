<?php

// Include the database connection file
require_once '../../database.php';

// Set the content type of the response to JSON
header('Content-Type: application/json');

// Base query
$sql = "SELECT COUNT(*) as count FROM forum_posts";
$params = [];
$types = "";

// Check for 'approved' parameter
if (isset($_GET['approved'])) {
    $sql .= " WHERE approved = ?";
    $params[] = $_GET['approved'];
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
    // Fetch the count
    $count = (int)$result->fetch_assoc()['count'];

    // Return the count as a JSON response
    echo json_encode(['count' => $count]);
} else {
    // If the query fails, return a 500 Internal Server Error
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch post count from the database.']);
}

// Close the database connection
$db->close();
