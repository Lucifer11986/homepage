<?php
session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new mysqli("127.0.0.1", "web145762", "Schnitzel12.,", "usr_web145762_1");
    if ($db->connect_error) die("DB-Fehler: " . $db->connect_error);

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if (empty($username) || empty($password)) {
        $error = "Benutzername und Passwort dürfen nicht leer sein.";
    } elseif ($password !== $confirm) {
        $error = "Passwörter stimmen nicht überein.";
    } else {
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Benutzername ist bereits vergeben.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hash);
            if ($stmt->execute()) {
                $success = "Registrierung erfolgreich. Du kannst dich jetzt <a href='admin_login.php'>einloggen</a>.";
            } else {
                $error = "Fehler beim Registrieren.";
            }
        }
        $stmt->close();
    }

    $db->close();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Registrierung</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Registrierung</h2>
<?php if ($error): ?><p style="color:red;"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
<?php if ($success): ?><p style="color:green;"><?php echo $success; ?></p><?php endif; ?>
<form method="post" action="">
    <input type="text" name="username" placeholder="Benutzername" required /><br><br>
    <input type="password" name="password" placeholder="Passwort" required /><br><br>
    <input type="password" name="confirm" placeholder="Passwort wiederholen" required /><br><br>
    <button type="submit">Registrieren</button>
</form>
<p>Schon ein Konto? <a href="admin_login.php">Zum Login</a></p>
</body>
</html>
