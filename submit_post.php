<?php
session_start();

// Wenn Nutzer nicht eingeloggt ist, Weiterleitung zum Login
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Captcha-Überprüfung
if (!isset($_POST['captcha']) || intval($_POST['captcha']) !== $_SESSION['captcha_result']) {
    $_SESSION['error_message'] = "Sicherheitsfrage falsch beantwortet.";
    header("Location: forum.php");
    exit;
}

// Nur POST-Anfragen verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Datenbankverbindung
    $db = new mysqli("127.0.0.1", "web145762", "Schnitzel12.,", "usr_web145762_1");
    if ($db->connect_error) {
        die("DB-Verbindungsfehler: " . $db->connect_error);
    }

    // Eingaben vorbereiten
    $title = trim($_POST['title'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $author = $_SESSION['username'] ?? 'Unbekannt';

    // Validierung: Keine leeren Eingaben
    if ($title === '' || $message === '' || $category === '') {
        $_SESSION['error_message'] = "Titel, Inhalt und Kategorie dürfen nicht leer sein.";
        header("Location: forum.php");
        exit;
    }

    // Beitrag in die Datenbank einfügen, zunächst nicht freigegeben (approved = 0)
    $stmt = $db->prepare("INSERT INTO forum_posts (title, message, category, author, created_at, approved) VALUES (?, ?, ?, ?, NOW(), 0)");
    if (!$stmt) {
        die("DB-Fehler beim Vorbereiten: " . $db->error);
    }
    $stmt->bind_param("ssss", $title, $message, $category, $author);

    // Beitrag speichern
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Dein Beitrag wurde eingereicht und wartet auf Freigabe.";
    } else {
        $_SESSION['error_message'] = "Fehler beim Speichern des Beitrags.";
    }

    $stmt->close();
    $db->close();
    header("Location: forum.php");
    exit;
} else {
    header("Location: forum.php");
    exit;
}
