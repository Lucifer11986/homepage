<?php
session_start();

// Optional: rudimentärer Adminschutz
// if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) { die("Kein Zugriff"); }

$db = new mysqli("127.0.0.1", "web145762", "Schnitzel12.,", "usr_web145762_1");
if ($db->connect_error) {
    die("Verbindung fehlgeschlagen: " . $db->connect_error);
}

// Beiträge laden
$result = $db->query("SELECT * FROM forum_posts WHERE approved = 0");
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Beiträge prüfen</title>
    <style>
        body { font-family: Arial; background: #111; color: #ddd; padding: 20px; }
        .post { background: #222; border: 1px solid #444; padding: 10px; margin-bottom: 20px; }
        form { margin-top: 10px; }
        select, textarea, input[type="submit"] { margin-top: 5px; width: 100%; }
    </style>
</head>
<body>
    <h2>Offene Beiträge zur Freigabe</h2>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="post">
            <strong>Autor:</strong> <?= htmlspecialchars($row['author']) ?><br>
            <strong>Titel:</strong> <?= htmlspecialchars($row['title']) ?><br>
            <strong>Nachricht:</strong><br>
            <pre><?= htmlspecialchars($row['message']) ?></pre>

            <form action="review_action.php" method="post">
                <input type="hidden" name="post_id" value="<?= $row['id'] ?>">
                <label>
                    Aktion:
                    <select name="action" required onchange="this.form.querySelector('.reason').style.display = this.value === 'reject' ? 'block' : 'none';">
                        <option value="">-- Bitte wählen --</option>
                        <option value="approve">Genehmigen</option>
                        <option value="reject">Ablehnen</option>
                    </select>
                </label>
                <div class="reason" style="display: none;">
                    <label>Grund für Ablehnung:
                        <select name="reason_text">
                            <option value="Spam oder Werbung">Spam oder Werbung</option>
                            <option value="Beleidigender Inhalt">Beleidigender Inhalt</option>
                            <option value="Off-Topic">Off-Topic</option>
                            <option value="Unzureichender Beitrag">Unzureichender Beitrag</option>
                        </select>
                    </label>
                </div>
                <input type="submit" value="Speichern">
            </form>
        </div>
    <?php endwhile; ?>
</body>
</html>
