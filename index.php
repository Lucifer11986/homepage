<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DB-Verbindung
$db = new mysqli("127.0.0.1", "web145762", "Schnitzel12.,", "usr_web145762_1", 3306);
if ($db->connect_error) {
    die("Verbindungsfehler: " . $db->connect_error);
}

// Neuster freigegebener Forum-Beitrag
$forumResult = $db->query("SELECT id, title, author, created_at FROM forum_posts WHERE approved = 1 ORDER BY created_at DESC LIMIT 1");
$forumPost = $forumResult && $forumResult->num_rows > 0 ? $forumResult->fetch_assoc() : null;

// Neuster Blog-Beitrag (angepasst auf deine Tabelle)
$blogResult = $db->query("SELECT id, title, created_at FROM blog_posts ORDER BY created_at DESC LIMIT 1");
$blogPost = $blogResult && $blogResult->num_rows > 0 ? $blogResult->fetch_assoc() : null;
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lucifer11986 - Twitch Kanal | Gaming, Events & Community</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="overlay">
        <header>
            <h1 class="glow-text">Twitch Streamer Lucifer11986</h1>
            <nav>
                <a href="#home" class="active">Startseite</a>
                <a href="about.html">Über mich</a>
                <a href="streams.html">Streams</a>
                <a href="events.html">Events</a>
                <a href="contact.html">Kontakt</a>
                <a href="blog.php">Blog</a>
                <div class="dropdown">
                    <a href="#community" class="dropbtn">Community ▾</a>
                    <div class="dropdown-content">
                        <a href="discord.html">Discord</a>
                        <a href="forum.php">Forum</a>
                    </div>
                </div>
                <a href="shop.php">Shop</a>
                <a href="live.html">Live</a>
            </nav>
            <div id="live-status" class="status offline">Streamstatus wird geladen...</div>
        </header>

        <div class="container">
            <main>
                <?php include 'main_content.php'; ?>
            </main>

            <aside class="sidebar">
                <section>
                    <h2>Neuster Forum-Beitrag</h2>
                    <?php if ($forumPost): ?>
                        <div class="entry">
                            <a href="forum_post.php?id=<?= (int)$forumPost['id'] ?>">
                                <div class="entry-title"><?= htmlspecialchars($forumPost['title']) ?></div>
                            </a>
                            <div class="entry-meta"><?= htmlspecialchars($forumPost['author']) ?> - <?= date('d.m.Y H:i', strtotime($forumPost['created_at'])) ?></div>
                        </div>
                    <?php else: ?>
                        <p>Keine Beiträge gefunden.</p>
                    <?php endif; ?>
                </section>

                <section>
                    <h2>Neuster Blog-Eintrag</h2>
                    <?php if ($blogPost): ?>
                        <div class="entry">
                            <a href="blog_post.php?id=<?= (int)$blogPost['id'] ?>">
                                <div class="entry-title"><?= htmlspecialchars($blogPost['title']) ?></div>
                            </a>
                            <div class="entry-meta">Veröffentlicht am <?= date('d.m.Y', strtotime($blogPost['created_at'])) ?></div>
                        </div>
                    <?php else: ?>
                        <p>Keine Blogbeiträge gefunden.</p>
                    <?php endif; ?>
                </section>
            </aside>
        </div>

        <footer>
            <p>&copy; 2025 Lucifer11986. Alle Rechte vorbehalten.</p>
            <div style="font-size: 0.85em; margin-top: 5px;">
                <a href="impressum.html">Impressum</a> |
                <a href="datenschutz.html">Datenschutz</a> |
                <a href="nutzungsbedingungen.html">Nutzungsbedingungen</a> |
                <a href="cookies.html">Cookies</a>
            </div>
        </footer>
    </div>

    <script src="script.js"></script>
    <button id="toTopBtn" title="Nach oben">⬆</button>
</body>
</html>
