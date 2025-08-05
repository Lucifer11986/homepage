<?php
session_start();

$db = new mysqli("127.0.0.1", "web145762", "Schnitzel12.,", "usr_web145762_1");
if ($db->connect_error) {
    die("Verbindung fehlgeschlagen: " . $db->connect_error);
}

$post_id = intval($_POST['post_id']);
$action = $_POST['action'];

if ($action === 'approve') {
    $stmt = $db->prepare("UPDATE forum_posts SET approved = 1, rejection_reason = NULL WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
} elseif ($action === 'reject') {
    $reason = $_POST['reason_text'];
    $stmt = $db->prepare("UPDATE forum_posts SET approved = -1, rejection_reason = ? WHERE id = ?");
    $stmt->bind_param("si", $reason, $post_id);
    $stmt->execute();
} else {
    die("Ungültige Aktion.");
}

header("Location: admin_review.php");
exit;
