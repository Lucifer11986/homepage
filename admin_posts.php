<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$db = new mysqli("127.0.0.1", "web145762", "Schnitzel12.,", "usr_web145762_1");
if ($db->connect_error) die("DB-Fehler: " . $db->connect_error);

// Verarbeitung der Formulareingabe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = intval($_POST['post_id']);
    $action = $_POST['action'];
    $reject_reason = isset($_POST['reject_reason']) ? trim($_POST['reject_reason']) : null;

    if ($action === 'approve') {
        $stmt = $db->prepare("UPDATE forum_posts SET approved = 1, reject_reason = NULL WHERE id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'reject') {
        $stmt = $db->prepare("UPDATE forum_posts SET approved = 0, reject_reason = ? WHERE id = ?");
        $stmt->bind_param("si", $reject_reason, $post_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM forum_posts WHERE id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: admin_posts.php");
    exit;
}

// Lade Beiträge
$pending_posts = $db->query("SELECT forum_posts.*, COALESCE(forum_categories.name, '') AS category_name 
                              FROM forum_posts
                              LEFT JOIN forum_categories ON forum_posts.category_id = forum_categories.id
                              WHERE approved = 0 ORDER BY created_at DESC");
$approved_posts = $db->query("SELECT forum_posts.*, COALESCE(forum_categories.name, '') AS category_name 
                              FROM forum_posts
                              LEFT JOIN forum_categories ON forum_posts.category_id = forum_categories.id
                              WHERE approved = 1 ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Admin Beiträge verwalten</title>
<link rel="stylesheet" href="style.css">
<script>
function toggleRejectReason(id) {
  var sel = document.getElementById('action_' + id);
  var box = document.getElementById('reject_reason_box_' + id);
  box.style.display = sel.value === 'reject' ? 'block' : 'none';
}
</script>
</head>
<body>

<a class="logout" href="admin_logout.php">Logout</a>
<h1>Adminbereich – Beiträge verwalten</h1>

<h2>Nicht freigegebene Beiträge</h2>
<?php if ($pending_posts->num_rows === 0): ?>
  <p class="forum-hint">Keine Beiträge zur Freigabe vorhanden.</p>
<?php else: ?>
  <?php while ($row = $pending_posts->fetch_assoc()): ?>
    <div class="post">
      <h3><?php echo htmlspecialchars($row['title']); ?></h3>
      <p><strong>Von:</strong> <?php echo htmlspecialchars($row['author']); ?> – <em><?php echo htmlspecialchars($row['created_at']); ?></em></p>
      <p><strong>Kategorie:</strong> <?php echo htmlspecialchars($row['category_name']); ?></p>
      <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>

      <form method="post" action="" onsubmit="return confirm('Aktion durchführen?');">
        <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
        <label for="action_<?php echo $row['id']; ?>">Aktion:</label>
        <select name="action" id="action_<?php echo $row['id']; ?>" onchange="toggleRejectReason(<?php echo $row['id']; ?>)" required>
          <option value="">Bitte wählen</option>
          <option value="approve">✔ Freigeben</option>
          <option value="reject">✖ Ablehnen</option>
          <option value="delete">🗑️ Löschen</option>
        </select>

        <div class="reject-reason" id="reject_reason_box_<?php echo $row['id']; ?>" style="display: none;">
          <label>Ablehnungsgrund:</label>
          <select name="reject_reason">
            <option value="Unpassender Inhalt">Unpassender Inhalt</option>
            <option value="Spam">Spam</option>
            <option value="Werbung">Werbung</option>
            <option value="Sonstiges">Sonstiges</option>
          </select>
        </div>

        <button type="submit">Aktion ausführen</button>
      </form>
    </div>
  <?php endwhile; ?>
<?php endif; ?>

<h2>Freigegebene Beiträge</h2>
<?php if ($approved_posts->num_rows === 0): ?>
  <p class="forum-hint">Keine freigegebenen Beiträge vorhanden.</p>
<?php else: ?>
  <?php while ($row = $approved_posts->fetch_assoc()): ?>
    <div class="post">
      <h3><?php echo htmlspecialchars($row['title']); ?></h3>
      <p><strong>Von:</strong> <?php echo htmlspecialchars($row['author']); ?> – <em><?php echo htmlspecialchars($row['created_at']); ?></em></p>
      <p><strong>Kategorie:</strong> <?php echo htmlspecialchars($row['category_name']); ?></p>
      <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>

      <form method="post" action="" onsubmit="return confirm('Beitrag wirklich löschen?');">
        <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
        <input type="hidden" name="action" value="delete">
        <button type="submit" class="danger">Löschen</button>
      </form>
    </div>
  <?php endwhile; ?>
<?php endif; ?>

</body>
</html>
