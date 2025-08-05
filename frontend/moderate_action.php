<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header("Location: index.php");
    exit;
}

$db = new mysqli("127.0.0.1", "web145762", "Schnitzel12.,", "usr_web145762_1");
if ($db->connect_error) {
    die("Verbindung fehlgeschlagen: " . $db->connect_error);
}

$post_id = (int) $_POST['post_id'];
$action = $_POST['action'];

if ($action === 'approve') {
    $stmt = $db->prepare("UPDATE forum_posts SET approved = 1 WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
} elseif ($action === 'delete') {
    $stmt = $db->prepare("DELETE FROM forum_posts WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
}

header("Location: moderate_posts.php");
exit;
