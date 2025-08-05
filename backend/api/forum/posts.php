<?php

// Include the database connection file
require_once '../../database.php';

// Set the content type of the response to JSON
header('Content-Type: application/json');

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    // Get the post data from the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Captcha validation
    if (!isset($data['captcha']) || intval($data['captcha']) !== $_SESSION['captcha_result']) {
        http_response_code(400);
        echo json_encode(['error' => 'Sicherheitsfrage falsch beantwortet.']);
        exit;
    }

    // Input validation
    $title = trim($data['title'] ?? '');
    $message = trim($data['message'] ?? '');
    $category = trim($data['category'] ?? '');
    $author = $_SESSION['username'] ?? 'Unbekannt';

    if (empty($title) || empty($message) || empty($category)) {
        http_response_code(400);
        echo json_encode(['error' => 'Titel, Inhalt und Kategorie dürfen nicht leer sein.']);
        exit;
    }

    // Insert the new post into the database
    $stmt = $db->prepare("INSERT INTO forum_posts (title, message, category, author, created_at, approved) VALUES (?, ?, ?, ?, NOW(), 0)");
    $stmt->bind_param("ssss", $title, $message, $category, $author);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Dein Beitrag wurde eingereicht und wartet auf Freigabe.']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Fehler beim Speichern des Beitrags.']);
    }

    $stmt->close();
    $db->close();
    exit;
}


// Base query
$sql = "SELECT id, title, message, author, category, created_at FROM forum_posts";
$params = [];
$types = "";

// Check for 'approved' parameter
if (isset($_GET['approved'])) {
    $sql .= " WHERE approved = ?";
    $params[] = $_GET['approved'];
    $types .= "i";
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
    echo json_encode(['error' => 'Failed to fetch forum posts from the database.']);
}

// Close the database connection
$db->close();
