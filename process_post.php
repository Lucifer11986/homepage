<?php
session_start();
// Admin-Check hier

$db = new mysqli("127.0.0.1", "web145762", "Schnitzel12.,", "usr_web145762_1");
if ($db->connect_error) die("DB-Fehler");

if (!isset($_POST['post_id'], $_POST['action'])) die("Ungültige Anfrage");

$post_id = intval($_POST['post_id']);
$action = $_POST['action'];

if ($action === 'approve') {
    $stmt = $db->prepare("UPDATE forum_posts SET approved = 1 WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_posts.php");
    exit;
} elseif ($action === 'reject') {
    if (!isset($_POST['reject_reason']) || empty($_POST['reject_reason'])) {
        die("Ablehnungsgrund fehlt.");
    }
    $reason = htmlspecialchars($_POST['reject_reason']);
    $stmt = $db->prepare("UPDATE forum_posts SET approved = -1, reject_reason = ? WHERE id = ?");
    $stmt->bind_param("si", $reason, $post_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_posts.php");
    exit;
} else {
    die("Unbekannte Aktion.");
}
