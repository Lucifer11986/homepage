<?php
header("Referrer-Policy: no-referrer");
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$db = new mysqli("127.0.0.1", "web145762", "Schnitzel12.,", "usr_web145762_1");
if ($db->connect_error) {
    die("DB-Verbindungsfehler: " . $db->connect_error);
}

// Anzahl offener Beiträge im Forum
$result = $db->query("SELECT COUNT(*) AS count FROM forum_posts WHERE approved = 0");
$openCount = $result ? (int)$result->fetch_assoc()['count'] : 0;

// Letzte 5 freigegebene Beiträge im Forum
$latest = $db->query("SELECT title, author, created_at FROM forum_posts WHERE approved = 1 ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard – AbyssForge</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        body {
            background-color: #0d0d0d;
            color: #f0f0f0;
            font-family: 'Orbitron', sans-serif;
            max-width: 900px;
            margin: 2rem auto;
            padding: 1rem;
        }
        h1 {
            color: crimson;
            text-align: center;
            text-shadow: 0 0 10px crimson;
            margin-bottom: 2rem;
        }
        a {
            color: #ff3c00;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            color: #ff6a00;
        }
        .section {
            background: rgba(30,0,0,0.7);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 0 20px crimson;
        }
        .btn {
            background: #ff3c00;
            color: #000;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 0 0 15px crimson;
            text-decoration: none;
            display: inline-block;
            margin-top: 1rem;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background: darkred;
            color: #fff;
            box-shadow: 0 0 25px crimson;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 0.6rem;
            border-bottom: 1px solid #660000;
            text-align: left;
        }
        th {
            background: #330000;
            color: #ff3c00;
        }
        .center {
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Admin Dashboard</h1>

    <div class="section">
        <h2>Offene Beiträge zur Moderation</h2>
        <p>Es gibt <strong><?= $openCount ?></strong> Beitrag<?= ($openCount === 1 ? '' : 'e') ?>, die auf Freigabe warten.</p>
        <a href="moderate_posts.php" class="btn">Beiträge moderieren</a>
    </div>

    <div class="section">
        <h2>Letzte freigegebene Beiträge</h2>
        <?php if ($latest && $latest->num_rows > 0): ?>
            <table>
                <thead>
                    <tr><th>Titel</th><th>Autor</th><th>Datum</th></tr>
                </thead>
                <tbody>
                    <?php while ($row = $latest->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['author']) ?></td>
                            <td><?= $row['created_at'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Keine freigegebenen Beiträge gefunden.</p>
        <?php endif; ?>
    </div>

    <div class="section center">
        <h2>Blog-Beiträge verwalten</h2>
        <a href="admin_create_post.php" class="btn">📝 Neuen Blogbeitrag erstellen</a>
    </div>

    <div class="section center">
        <a href="admin_logout.php" class="btn" style="background:#7a0000;">Logout</a>
        <a href="blog.php" class="btn" style="background:#7a0000;">Zur Blogseite</a>
    </div>
</body>
</html>
