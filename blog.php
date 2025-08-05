<?php
require_once 'db.php';

// Kategorien abrufen für Dropdown
$categoryResult = $db->query("SELECT name FROM blog_categories ORDER BY name ASC");
$categories = [];
while ($cat = $categoryResult->fetch_assoc()) {
    $categories[] = $cat['name'];
}

// Wenn Kategorie über GET-Parameter gesetzt ist
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : null;

if ($selectedCategory && in_array($selectedCategory, $categories)) {
    $stmt = $db->prepare("SELECT id, title, content, created_at, category, image_path FROM blog_posts WHERE category = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $selectedCategory);
    $stmt->execute();
    $result = $stmt->get_result();
    $postsByCategory = [$selectedCategory => $result->fetch_all(MYSQLI_ASSOC)];
} else {
    // Alle Beiträge, gruppiert nach Kategorie
    $result = $db->query("SELECT id, title, content, created_at, category, image_path FROM blog_posts ORDER BY category ASC, created_at DESC");
    $postsByCategory = [];
    while ($row = $result->fetch_assoc()) {
        $postsByCategory[$row['category']][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Blog – AbyssForge</title>
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

  <main class="blog-list">
    <h2>📖 Blogbeiträge</h2>

    <form method="GET" style="margin-bottom: 20px;">
      <label for="category">Kategorie wählen:</label>
      <select name="category" id="category" onchange="this.form.submit()">
        <option value="">-- Alle Kategorien --</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= htmlspecialchars($cat) ?>" <?= ($cat === $selectedCategory) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>

    <?php foreach ($postsByCategory as $category => $posts): ?>
      <section class="blog-category">
        <h3 class="category-heading">🗂️ <?= htmlspecialchars($category) ?></h3>
        <div class="blog-cards">
          <?php foreach ($posts as $post): ?>
            <div class="blog-card">
              <?php if (!empty($post['image_path'])): ?>
                <img src="<?= htmlspecialchars($post['image_path']) ?>" alt="Beitragsbild" class="blog-card-img">
              <?php endif; ?>
              <div class="blog-card-content">
                <h4>
                  <a href="blog_post.php?id=<?= $post['id'] ?>">
                    <?= htmlspecialchars($post['title']) ?>
                  </a>
                </h4>
                <p class="blog-meta"><?= date('d.m.Y', strtotime($post['created_at'])) ?></p>
                <p><?= mb_strimwidth(strip_tags($post['message']), 0, 160, "...") ?></p>
                <a href="blog_post.php?id=<?= $post['id'] ?>" class="read-more">➤ Weiterlesen</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>
  </main>

  <footer>
    <p>&copy; 2025 Lucifer11986 – AbyssForge. Alle Rechte vorbehalten.</p>
    <div style="font-size: 0.85em; margin-top: 5px;">
      <a href="impressum.html">Impressum</a> |
      <a href="datenschutz.html">Datenschutz</a> |
      <a href="nutzungsbedingungen.html">Nutzungsbedingungen</a> |
      <a href="cookies.html">Cookies</a> |
      <a href="dashboard.php">Dashboard</a>
    </div>
  </footer>
</div>
</body>
</html>
