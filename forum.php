<?php
session_start();

$isUser = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
?>

<?php if ($isAdmin): ?>
    <a href="dashboard.php">Zum Dashboard</a>
<?php endif; ?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Forum – Lucifer11986 Community</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    /* Zusätzliche Styles wie zuvor ... */
    .delete-button {
      background-color: darkred;
      color: white;
      padding: 4px 8px;
      border: none;
      border-radius: 4px;
      font-size: 0.8rem;
      cursor: pointer;
      float: right;
    }
  </style>
</head>
<body>
<div class="overlay">
  <header>
    <h1 class="title-glow">🕯️ AbyssForge Community Forum</h1>
    <nav class="main-nav">
      <a href="index.html">🏠 Startseite</a>
      <a href="forum.php" class="active">🗨️ Forum</a>
    </nav>
  </header>

  <main>
    <section class="forum-intro">
      <h2>Willkommen im Forum</h2>
      <p>Diskutiere mit anderen über Games, Projekte, Streams oder einfach nur zum Spaß.</p>
    </section>

    <?php if (isset($_SESSION['error_message'])): ?>
      <p style="color: red; font-weight: bold;"><?= htmlspecialchars($_SESSION['error_message']); ?></p>
      <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
      <p style="color: green; font-weight: bold;"><?= htmlspecialchars($_SESSION['success_message']); ?></p>
      <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if ($isUser): ?>
      <section class="forum-posting">
        <h2>📬 Beitrag verfassen</h2>
        <form action="submit_post.php" method="POST">
          <input type="text" name="title" placeholder="Titel" required />
          <textarea name="message" placeholder="Dein Beitrag" required></textarea>

          <label for="category">Kategorie auswählen:</label>
          <select name="category" id="category" required>
            <option value="">-- Bitte wählen --</option>
            <option value="Ankündigungen">📢 Ankündigungen</option>
            <option value="Allgemeiner Talk">🕹️ Allgemeiner Talk</option>
            <option value="Diskussionen">💬 Diskussionen</option>
            <option value="Hilfe & Support">❓ Hilfe & Support</option>
            <option value="Vorschläge">💡 Vorschläge</option>
          </select>

          <div class="captcha-box">
            <?php
              $a = rand(1, 9);
              $b = rand(1, 9);
              $_SESSION['captcha_result'] = $a + $b;
              echo "<p>🧠 Sicherheitsfrage: Was ist $a + $b?</p>";
            ?>
            <input type="text" name="captcha" placeholder="Antwort eingeben" required />
          </div>

          <button type="submit">Beitrag absenden</button>
        </form>
      </section>
    <?php else: ?>
      <section class="forum-posting">
        <h2>📬 Beitrag verfassen</h2>
        <p style="color: darkred;">Bitte <a href="admin_login.php">melde dich an</a>, um einen Beitrag zu schreiben.</p>
      </section>
    <?php endif; ?>

    <section class="forum-entries">
      <h2>📖 Beiträge</h2>

      <?php
      $db = new mysqli("127.0.0.1", "web145762", "Schnitzel12.,", "usr_web145762_1");
      if ($db->connect_error) {
          echo "<p>Fehler bei der Datenbankverbindung.</p>";
      } else {
          $sql = "SELECT id, title, message, author, category, created_at FROM forum_posts WHERE approved = 1 ORDER BY created_at DESC";
          $result = $db->query($sql);

          if ($result && $result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  echo "<div class='post'>";
                  echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                  echo "<p><strong>Kategorie:</strong> " . htmlspecialchars($row['category']) . "</p>";
                  echo "<p>" . nl2br(htmlspecialchars($row['message'])) . "</p>";
                  echo "<small>von <strong>" . htmlspecialchars($row['author']) . "</strong> am " . $row['created_at'] . "</small>";

                  if ($isAdmin) {
                      echo "<form method='POST' action='delete_post.php' onsubmit='return confirm(\"Diesen Beitrag wirklich löschen?\");'>";
                      echo "<input type='hidden' name='post_id' value='" . (int)$row['id'] . "' />";
                      echo "<button type='submit' class='delete-button'>🗑️ Löschen</button>";
                      echo "</form>";
                  }

                  echo "</div>";
              }
          } else {
              echo "<p>Keine Beiträge gefunden.</p>";
          }
          $db->close();
      }
      ?>
    </section>

    <section class="forum-categories">
      <h2>🗂️ Kategorien</h2>
      <div class="forum-grid">
        <div class="forum-card"><h3>📢 Ankündigungen</h3><p>News zu Streams, Projekten und der Community.</p></div>
        <div class="forum-card"><h3>🕹️ Allgemeiner Talk</h3><p>Alles was euch sonst noch beschäftigt – frei von der Leber weg.</p></div>
        <div class="forum-card"><h3>💬 Diskussionen</h3><p>Diskussionen zu Games, Technik, Roleplay, uvm.</p></div>
        <div class="forum-card"><h3>❓ Hilfe & Support</h3><p>Fragen, Probleme oder Feedback? Hier bist du richtig.</p></div>
        <div class="forum-card"><h3>💡 Vorschläge</h3><p>Deine Ideen für neue Formate, Scripts oder Projekte.</p></div>
      </div>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 Lucifer11986 – AbyssForge. Alle Rechte vorbehalten.</p>

    <?php if ($isUser): ?>
      <p>Angemeldet als <?= $isAdmin ? 'Admin' : 'Benutzer' ?>. <a href="admin_logout.php" id="admin-logout">Logout</a></p>
      <a href="dashboard.php">Zum Dashboard</a>
    <?php else: ?>
      <form id="admin-login-form" action="admin_login.php" method="POST" style="margin-top:1rem;">
        <input type="text" name="username" placeholder="Benutzername" required />
        <input type="password" name="password" placeholder="Passwort" required />
        <button type="submit">Login</button>
      </form>
    <?php endif; ?>
  </footer>
</div>
</body>
</html>
