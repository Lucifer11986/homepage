<?php

require_once 'config.php';

// Create a new mysqli object with database connection parameters
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Check for connection errors
if ($db->connect_error) {
    // Log the error to a file or a logging service
    error_log("Database connection failed: " . $db->connect_error);

    // Send a generic error response to the client
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Internal Server Error']);
    exit;
}

// Set the character set to utf8mb4 for proper handling of Unicode characters
$db->set_charset("utf8mb4");
