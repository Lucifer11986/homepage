<?php
session_start();

$db = new mysqli("127.0.0.1", "web145762", "Schnitzel12.,", "usr_web145762_1");
if ($db->connect_error) {
    die("Verbindung fehlgeschlagen: " . $db->connect_error);
}

$result = $db->query("SELECT * FROM forum_posts WHERE approved = 1 ORDER BY created_at DESC");

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='post'>";
        echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
        echo "<p><strong>Von:</strong> " . htmlspecialchars($row['author']) .
             " | <strong>Kategorie:</strong> " . htmlspecialchars($row['category']) .
             " – <em>" . $row['created_at'] . "</em></p>";
        echo "<p>" . nl2br(htmlspecialchars($row['message'])) . "</p>";
        echo "</div>";
    }
} else {
    echo "<p>Keine Beiträge gefunden.</p>";
}
?><?php
session_start();

$db = new mysqli("127.0.0.1", "web145762", "Schnitzel12.,", "usr_web145762_1", 3306);
if ($db->connect_error) {
    die("Verbindung fehlgeschlagen: " . $db->connect_error);
}

$result = $db->query("SELECT * FROM forum_posts WHERE approved = 1 ORDER BY created_at DESC");

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='post'>";
        echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
        echo "<p><strong>Von:</strong> " . htmlspecialchars($row['author']) .
             " | <strong>Kategorie:</strong> " . htmlspecialchars($row['category']) .
             " – <em>" . $row['created_at'] . "</em></p>";
        echo "<p>" . nl2br(htmlspecialchars($row['message'])) . "</p>";
        echo "</div>";
    }
} else {
    echo "<p>Keine Beiträge gefunden.</p>";
}
?>