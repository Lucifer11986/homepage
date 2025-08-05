<?php
session_start();
require_once '../../database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Benutzername und Passwort sind erforderlich.']);
    exit;
}

$stmt = $db->prepare("SELECT id, password_hash, is_admin FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($user_id, $password_hash, $is_admin);
    $stmt->fetch();

    if (password_verify($password, $password_hash)) {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['is_admin'] = (int)$is_admin === 1;
        $_SESSION['admin_logged_in'] = (int)$is_admin === 1;

        echo json_encode([
            'success' => true,
            'isAdmin' => $_SESSION['is_admin']
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Benutzername oder Passwort ist falsch.']);
    }
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Benutzername oder Passwort ist falsch.']);
}

$stmt->close();
$db->close();
