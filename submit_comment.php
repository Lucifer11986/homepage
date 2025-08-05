<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = intval($_POST['post_id'] ?? 0);
    $username = trim($_POST['username'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    if ($post_id > 0 && $username !== '' && $comment !== '') {
        $stmt = $db->prepare("INSERT INTO blog_comments (post_id, username, comment, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $post_id, $username, $comment);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect zurück zum Beitrag
    header("Location: blog_post.php?id=" . $post_id);
    exit;
}
?>
