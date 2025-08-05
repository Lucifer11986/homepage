<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    $_SESSION['error_message'] = "Zugriff verweigert.";
    header("Location: forum.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $postId = (int)$_POST['post_id'];

    $db = new mysqli("sql206.infinityfree.com", "if0_39556359", "qAUYOr3FsWT4287", "if0_39556359_website");
    if ($db->connect_error) {
        $_SESSION['error_message'] = "Verbindungsfehler zur Datenbank.";
        header("Location: forum.php");
        exit;
    }

    $stmt = $db->prepare("DELETE FROM forum_posts WHERE id = ?");
    if (!$stmt) {
        $_SESSION['error_message'] = "Datenbankfehler (prepare).";
        header("Location: forum.php");
        exit;
    }

    $stmt->bind_param("i", $postId);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Beitrag erfolgreich gelöscht.";
    } else {
        $_SESSION['error_message'] = "Fehler beim Löschen des Beitrags.";
    }

    $stmt->close();
    $db->close();
} else {
    $_SESSION['error_message'] = "Ungültige Anfrage.";
}

header("Location: forum.php");
exit;
?>
