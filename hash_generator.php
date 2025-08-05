<?php
// Passwort-Hash Generator

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if (empty($password)) {
        echo "Bitte gib ein Passwort ein.";
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    echo "<h2>Dein Passwort-Hash:</h2>";
    echo "<pre>" . htmlspecialchars($hash) . "</pre>";
    echo '<p><a href="">Neues Passwort generieren</a></p>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8" />
    <title>Passwort-Hash Generator</title>
</head>
<body>
    <h1>Passwort-Hash Generator</h1>
    <form method="POST">
        <label for="password">Passwort eingeben:</label><br>
        <input type="password" id="password" name="password" required autofocus /><br><br>
        <button type="submit">Hash generieren</button>
    </form>
</body>
</html>
