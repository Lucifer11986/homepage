<?php
require_once 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Ungültige Anfrage.");
}

$id = intval($_GET['id']);
$stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Beitrag nicht gefunden.");
}

$post = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post['title']) ?> – AbyssForge Blog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="overlay">
    <header>
            <h1 class="glow-text">Twitch Streamer Lucifer11986</h1>
            <nav>
                <a href="index.html">Startseite</a>
                <a href="about.html">Über mich</a>
                <a href="streams.html">Streams</a>
                <a href="events.html">Events</a>
                <a href="contact.html">Kontakt</a>
                <a href="cblog.php" class="active">Blog</a>
                <div class="dropdown">
                    <a href="#community" class="dropbtn">Community ▾</a>
                    <div class="dropdown-content">
                        <a href="discord.html">Discord</a>
                        <a href="forum.php">Forum</a>
                    </div>
                </div>
                <a href="https://abyssforge-studio.tebex.io/">Shop</a>
                <a href="live.html">Live</a>
            </nav>
            <div id="live-status" class="status offline">Streamstatus wird geladen...</div>
        </header>

    <main class="blog-detail">
        <article>
            <h2><?= htmlspecialchars($post['title']) ?></h2>
            <p class="blog-meta">
                <?= htmlspecialchars($post['category']) ?> –
                <?= date('d.m.Y', strtotime($post['created_at'])) ?>
            </p>

            <?php if (!empty($post['image_path'])): ?>
                <img src="<?= htmlspecialchars($post['image_path']) ?>" alt="Beitragsbild" class="blog-detail-image">
            <?php endif; ?>

            <div class="blog-text">
                <?= nl2br(htmlspecialchars($post['content'])) ?>
            </div>

            <?php if (!empty($post['tags'])): ?>
                <p class="blog-tags">🔖 Tags: <?= htmlspecialchars($post['tags']) ?></p>
            <?php endif; ?>
        </article>

        <hr>

        <section class="comments">
            <h3>Kommentare</h3>
            <?php
            $comment_stmt = $db->prepare("SELECT * FROM blog_comments WHERE post_id = ? ORDER BY created_at DESC");
            $comment_stmt->bind_param("i", $id);
            $comment_stmt->execute();
            $comments = $comment_stmt->get_result();

            if ($comments->num_rows > 0):
                while ($comment = $comments->fetch_assoc()):
            ?>
                    <div class="comment">
                        <strong><?= htmlspecialchars($comment['username']) ?></strong>
                        <em><?= date("d.m.Y H:i", strtotime($comment['created_at'])) ?></em>
                        <p><?= nl2br(htmlspecialchars($comment['comment'])) ?></p>
                    </div>
            <?php
                endwhile;
            else:
                echo "<p>Keine Kommentare vorhanden.</p>";
            endif;
            ?>

            <h4>Kommentar schreiben</h4>
            <form action="submit_comment.php" method="post" class="comment-form">
                <input type="hidden" name="post_id" value="<?= $id ?>">
                <label for="username">Name:</label>
                <input type="text" name="username" required>

                <label for="comment">Kommentar:</label>
                <textarea name="comment" rows="4" required></textarea>

                <button type="submit">Absenden</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Lucifer11986 – AbyssForge. Alle Rechte vorbehalten.</p>
        <div style="font-size: 0.85em; margin-top: 5px;">
            <a href="impressum.html">Impressum</a> |
            <a href="datenschutz.html">Datenschutz</a> |
            <a href="nutzungsbedingungen.html">Nutzungsbedingungen</a> |
            <a href="cookies.html">Cookies</a>
        </div>
    </footer>
</div>
</body>
</html>
