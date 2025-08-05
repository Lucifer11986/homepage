<?php
session_start();

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    // Wenn schon eingeloggt, direkt weiterleiten
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
        header("Location: dashboard.php");
    } else {
        header("Location: forum.php");
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new mysqli("127.0.0.1", "web145762", "Schnitzel12.,", "usr_web145762_1");
    if ($db->connect_error) die("Datenbankfehler");

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare("SELECT id, password_hash, is_admin FROM users WHERE username = ?");
    if (!$stmt) die("Prepare fehlgeschlagen: " . $db->error);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $password_hash, $is_admin);
        $stmt->fetch();

        if (password_verify($password, $password_hash)) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;

            if ((int)$is_admin === 1) {
                $_SESSION['is_admin'] = true;
                $_SESSION['admin_logged_in'] = true;

                header("Location: dashboard.php"); // Admins gehen direkt ins Dashboard
                exit;
            } else {
                $_SESSION['is_admin'] = false;

                header("Location: forum.php"); // Normale User ins Forum
                exit;
            }
        } else {
            $error = "Benutzername oder Passwort ist falsch.";
        }
    } else {
        $error = "Benutzername oder Passwort ist falsch.";
    }

    $stmt->close();
    $db->close();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8" />
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Login</h2>
    <?php if($error): ?><p style="color:red;"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Benutzername" required><br><br>
        <input type="password" name="password" placeholder="Passwort" required><br><br>
        <button type="submit">Login</button>
    </form>
    <p>Noch kein Konto? <a href="register.php">Registrieren</a></p>
</body>
</html>
