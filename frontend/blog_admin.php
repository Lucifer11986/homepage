<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

require_once 'db.php';

// Beitrag löschen
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $db->prepare("DELETE FROM blog_posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: blog_admin.php");
    exit();
}

$result = $db->query("SELECT id, title, category, created_at FROM blog_posts ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <title>Blog Admin – AbyssForge</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="overlay">
  <header>
    <h1 class="title-glow">🕯️ AbyssForge – Blog Admin</h1>
    <a href="dashboard.php" class="back-link">🔙 Zurück zum Dashboard</a>
    <a href="blog_create.php" class="btn" style="margin-left:1rem;">➕ Neuer Beitrag</a>
  </header>

  <main class="form-section">
    <h2>📝 Beiträge verwalten</h2>
    <table>
      <thead>
        <tr>
          <th>Titel</th>
          <th>Kategorie</th>
          <th>Erstellt</th>
          <th>Aktionen</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td><?= date('d.m.Y', strtotime($row['created_at'])) ?></td>
            <td>
              <a href="blog_edit.php?id=<?= $row['id'] ?>" class="btn">✏️ Bearbeiten</a>
              <a href="blog_admin.php?delete=<?= $row['id'] ?>" class="btn delete-button" onclick="return confirm('Beitrag wirklich löschen?');">🗑️ Löschen</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </main>

  <footer>
    <p>&copy; 2025 Lucifer11986 – AbyssForge. Alle Rechte vorbehalten.</p>
  </footer>
</div>
</body>
</html>
