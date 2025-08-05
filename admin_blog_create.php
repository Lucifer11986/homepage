<?php
session_start();

// Admin-Login prüfen
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$db = new mysqli("127.0.0.1", "web145762", "Schnitzel12.", "usr_web145762_1");

if ($db->connect_error) {
    die("Verbindungsfehler: " . $db->connect_error);
}

$message = "";

// Beitrag speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $summary = trim($_POST['summary']);
    $content = trim($_POST['content']);
    $category = trim($_POST['category']);
    $tags = explode(",", $_POST['tags']);
    $tags = array_map('trim', $tags);
    $image_path = "";

    // Bild-Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = "uploads/";
        $filename = basename($_FILES["image"]["name"]);
        $target_file = $upload_dir . time() . "_" . $filename;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    // Beitrag einfügen
    $stmt = $db->prepare("INSERT INTO blog_posts (title, summary, content, image_path, category) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $summary, $content, $image_path, $category);
    $stmt->execute();
    $post_id = $stmt->insert_id;

    // Tags verarbeiten
    foreach ($tags as $tag_name) {
        if (empty($tag_name)) continue;

        $tag_stmt = $db->prepare("SELECT id FROM blog_tags WHERE name = ?");
        $tag_stmt->bind_param("s", $tag_name);
        $tag_stmt->execute();
        $tag_stmt->store_result();

        if ($tag_stmt->num_rows > 0) {
            $tag_stmt->bind_result($tag_id);
            $tag_stmt->fetch();
        } else {
            $insert_tag = $db->prepare("INSERT INTO blog_tags (name) VALUES (?)");
            $insert_tag->bind_param("s", $tag_name);
            $insert_tag->execute();
            $tag_id = $insert_tag->insert_id;
        }

        // Verknüpfung speichern
        $link_stmt = $db->prepare("INSERT IGNORE INTO blog_post_tags (post_id, tag_id) VALUES (?, ?)");
        $link_stmt->bind_param("ii", $post_id, $tag_id);
        $link_stmt->execute();
    }

    $message = "✅ Beitrag erfolgreich erstellt!";
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Blogbeitrag erstellen – AbyssForge</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="overlay">
    <h1>✍️ Blogbeitrag erstellen</h1>
    <a href="dashboard.php" style="color: orange;">🔙 Zurück zum Dashboard</a>

    <?php if ($message): ?>
        <p style="color: limegreen;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" style="max-width: 600px; margin-top: 2rem;">
        <label>Titel:<br>
            <input type="text" name="title" required>
        </label><br><br>

        <label>Zusammenfassung:<br>
            <textarea name="summary" rows="3" required></textarea>
        </label><br><br>

        <label>Inhalt:<br>
            <textarea name="content" rows="10" required></textarea>
        </label><br><br>

        <label>Kategorie:<br>
            <input type="text" name="category">
        </label><br><br>

        <label>Tags (kommagetrennt):<br>
            <input type="text" name="tags" placeholder="z.B. Twitch, News, Update">
        </label><br><br>

        <label>Bild hochladen:<br>
            <input type="file" name="image" accept="image/*">
        </label><br><br>

        <button type="submit" style="background: firebrick; color: white;">🚀 Beitrag veröffentlichen</button>
    </form>
</div>
</body>
</html>
