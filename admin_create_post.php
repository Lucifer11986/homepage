<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Zugriff nur für angemeldete Admins
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

require_once 'db.php';

// Kategorien aus DB laden
$categories = [];
$catResult = $db->query("SELECT id, name FROM blog_categories ORDER BY name ASC");
if ($catResult) {
    while ($row = $catResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $categoryId = intval($_POST['category']);
    $tags = trim($_POST['tags']);
    $imagePath = '';

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $imagePath = $targetDir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
    }

    // Kategorie als Name abspeichern (Alternativ: ID speichern, dann JOIN beim Lesen)
    $categoryName = '';
    foreach ($categories as $cat) {
        if ((int)$cat['id'] === $categoryId) {
            $categoryName = $cat['name'];
            break;
        }
    }

    if ($categoryName === '') {
        die("Ungültige Kategorie ausgewählt.");
    }

    $stmt = $db->prepare("INSERT INTO blog_posts (title, content, category, tags, image_path, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssss", $title, $content, $categoryName, $tags, $imagePath);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <title>Neuen Blogbeitrag erstellen – AbyssForge</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="overlay">
  <header>
    <h1>🕯️ AbyssForge – Adminbereich</h1>
    <a href="dashboard.php" class="back-link">🔙 Zurück zum Dashboard</a>
  </header>

  <main class="form-section">
    <h2>📝 Neuen Beitrag erstellen</h2>
    <form action="" method="POST" enctype="multipart/form-data">
      <input type="text" name="title" placeholder="Titel" required>

      <label for="category">Kategorie:</label>
      <select name="category" id="category" required>
        <option value="" disabled selected>Bitte Kategorie wählen</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <input type="text" name="tags" placeholder="Tags (kommagetrennt)">
      <textarea name="content" placeholder="Inhalt..." rows="10" required></textarea>

      <label for="image">Bild hochladen:</label>
      <input type="file" name="image" accept="image/*">

      <button type="submit">Veröffentlichen</button>
    </form>
  </main>

  <footer>
    <p>&copy; 2025 Lucifer11986 – AbyssForge</p>
  </footer>
</div>
</body>
</html>
