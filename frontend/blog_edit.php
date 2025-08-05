<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

require_once 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Ungültige Anfrage.");
}

$id = intval($_GET['id']);
$error = '';
$success = '';

// Beitrag laden
$stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Beitrag nicht gefunden.");
}

$post = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category = trim($_POST['category']);
    $tags = trim($_POST['tags']);
    $imagePath = $post['image_path'];

    if (empty($title) || empty($content) || empty($category)) {
        $error = "Titel, Kategorie und Inhalt sind Pflichtfelder.";
    } else {
        if (!empty($_FILES['image']['name'])) {
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $allowedTypes)) {
                $error = "Nur JPG, JPEG, PNG und GIF Dateien sind erlaubt.";
            } else {
                $uniqueName = uniqid('blogimg_', true) . '.' . $imageFileType;
                $newImagePath = $targetDir . $uniqueName;
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $newImagePath)) {
                    // Altes Bild löschen, wenn vorhanden
                    if (!empty($imagePath) && file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                    $imagePath = $newImagePath;
                } else {
                    $error = "Fehler beim Hochladen des Bildes.";
                }
            }
        }

        if (!$error) {
            $stmt = $db->prepare("UPDATE blog_posts SET title = ?, content = ?, category = ?, tags = ?, image_path = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $title, $content, $category, $tags, $imagePath, $id);
            if ($stmt->execute()) {
                $success = "Beitrag erfolgreich aktualisiert.";
                // Reload Post-Daten
                $post['title'] = $title;
                $post['content'] = $content;
                $post['category'] = $category;
                $post['tags'] = $tags;
                $post['image_path'] = $imagePath;
            } else {
                $error = "Datenbankfehler: Beitrag konnte nicht aktualisiert werden.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <title>Beitrag bearbeiten – AbyssForge Admin</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="overlay">
  <header>
    <h1 class="title-glow">🕯️ AbyssForge – Beitrag bearbeiten</h1>
    <a href="blog_admin.php" class="back-link">🔙 Zurück zur Verwaltung</a>
  </header>

  <main class="form-section">
    <h2>✏️ Beitrag bearbeiten</h2>

    <?php if ($error): ?>
      <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
      <p class="success-message"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" autocomplete="off">
      <input type="text" name="title" placeholder="Titel" required value="<?= htmlspecialchars($post['title']) ?>">
      <input type="text" name="category" placeholder="Kategorie" required value="<?= htmlspecialchars($post['category']) ?>">
      <input type="text" name="tags" placeholder="Tags (kommagetrennt)" value="<?= htmlspecialchars($post['tags']) ?>">
      <textarea name="content" placeholder="Inhalt..." rows="10" required><?= htmlspecialchars($post['content']) ?></textarea>

      <?php if (!empty($post['image_path'])): ?>
        <p>Aktuelles Bild:</p>
        <img src="<?= htmlspecialchars($post['image_path']) ?>" alt="Beitragsbild" style="max-width: 300px; margin-bottom: 1rem;">
      <?php endif; ?>

      <label for="image">Bild ersetzen (optional):</label>
      <input type="file" name="image" accept="image/*">

      <button type="submit">Aktualisieren</button>
    </form>
  </main>

  <footer>
    <p>&copy; 2025 Lucifer11986 – AbyssForge. Alle Rechte vorbehalten.</p>
  </footer>
</div>
</body>
</html>
