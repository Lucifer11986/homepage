<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$db = new mysqli("127.0.0.1", "web145762", "Schnitzel12.,", "usr_web145762_1");
if ($db->connect_error) {
    die("Verbindungsfehler: " . $db->connect_error);
}

// Beitrag freigeben oder löschen (TESTWEISE PER GET!)
if (isset($_GET['post_id']) && is_numeric($_GET['post_id'])) {
    $postId = (int)$_GET['post_id'];

    if (isset($_GET['action']) && $_GET['action'] === 'approve') {
        $stmt = $db->prepare("UPDATE forum_posts SET approved = 1 WHERE id = ?");
        if ($stmt && $stmt->bind_param("i", $postId) && $stmt->execute()) {
            $_SESSION['success_message'] = "Beitrag freigegeben.";
        } else {
            $_SESSION['error_message'] = "Fehler beim Freigeben des Beitrags.";
        }
        $stmt->close();
    }

    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        $stmt = $db->prepare("DELETE FROM forum_posts WHERE id = ?");
        if ($stmt && $stmt->bind_param("i", $postId) && $stmt->execute()) {
            $_SESSION['success_message'] = "Beitrag gelöscht.";
        } else {
            $_SESSION['error_message'] = "Fehler beim Löschen des Beitrags.";
        }
        $stmt->close();
    }

    header("Location: moderate_posts.php");
    exit();
}

// Beiträge laden
$result = $db->query("SELECT id, title, message, author, created_at FROM forum_posts WHERE approved = 0 ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Moderation – AbyssForge</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .moderation-box {
            margin: 1rem 0;
            padding: 1rem;
            border: 1px solid #444;
            border-radius: 6px;
            background-color: #1c1c1c;
        }
        .moderation-box h2 {
            margin-top: 0;
            color: #ff6b00;
        }
        .moderation-box .action-btn {
            margin-right: 0.5rem;
            padding: 6px 12px;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            text-decoration: none;
        }
        .approve-btn {
            background-color: green;
            color: white;
        }
        .delete-btn {
            background-color: darkred;
            color: white;
        }
        .message {
            padding: 0.5rem;
            margin: 1rem 0;
            border-radius: 4px;
            font-weight: bold;
        }
        .success {
            background-color: #004d00;
            color: #aaffaa;
        }
        .error {
            background-color: #660000;
            color: #ff9999;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 1rem;
            color: orange;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="overlay">
    <h1>🛡️ Moderation</h1>
    <a href="dashboard.php" class="back-link">🔙 Zurück zum Dashboard</a>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="message success"><?= htmlspecialchars($_SESSION['success_message']) ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="message error"><?= htmlspecialchars($_SESSION['error_message']) ?></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="moderation-box">
                <h2><?= htmlspecialchars($row['title']) ?></h2>
                <p><?= nl2br(htmlspecialchars($row['message'])) ?></p>
                <small>Von <strong><?= htmlspecialchars($row['author']) ?></strong> am <?= $row['created_at'] ?></small>
                <div style="margin-top: 0.8rem;">
                    <a class="action-btn approve-btn" href="moderate_posts.php?action=approve&post_id=<?= $row['id'] ?>">✔️ Freigeben</a>
                    <a class="action-btn delete-btn" href="moderate_posts.php?action=delete&post_id=<?= $row['id'] ?>">🗑️ Löschen</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Keine Beiträge zur Moderation vorhanden.</p>
    <?php endif; ?>
</div>
</body>
</html>
