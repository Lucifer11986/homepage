<?php
// Verbindung zur MySQL-Datenbank aufbauen
$db = new mysqli("127.0.0.1", "web145762", "Schnitzel12.,", "usr_web145762_1");

// Verbindungsfehler abfangen
if ($db->connect_error) {
    die("Verbindungsfehler: " . $db->connect_error);
}

// UTF-8 als Standard-Zeichensatz setzen
$db->set_charset("utf8mb4");
?>
