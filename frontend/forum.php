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
        <form id="post-form">
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
      <div id="forum-posts-container">
        <!-- Forum posts will be loaded here -->
      </div>
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
      <p>Angemeldet als <?= $isAdmin ? 'Admin' : 'Benutzer' ?>. <a href="#" id="logout-btn">Logout</a></p>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const postsContainer = document.getElementById('forum-posts-container');
    const postForm = document.getElementById('post-form');
    const logoutBtn = document.getElementById('logout-btn');

    if(logoutBtn) {
        logoutBtn.addEventListener('click', function(event) {
            event.preventDefault();
            fetch('../backend/api/users/logout.php')
                .then(() => {
                    window.location.href = 'forum.php';
                });
        });
    }

    if(postForm) {
        postForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(postForm);
            const data = Object.fromEntries(formData.entries());

            fetch('../backend/api/forum/posts.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                const messageContainer = document.createElement('p');
                if (result.success) {
                    messageContainer.style.color = 'green';
                    messageContainer.textContent = result.success;
                    postForm.reset();
                } else {
                    messageContainer.style.color = 'red';
                    messageContainer.textContent = result.error;
                }
                postForm.prepend(messageContainer);
            })
            .catch(error => {
                console.error('Error submitting post:', error);
                const messageContainer = document.createElement('p');
                messageContainer.style.color = 'red';
                messageContainer.textContent = 'Ein unerwarteter Fehler ist aufgetreten.';
                postForm.prepend(messageContainer);
            });
        });
    }

    fetch('../backend/api/forum/posts.php')
        .then(response => response.json())
        .then(posts => {
            if (posts.length === 0) {
                postsContainer.innerHTML = '<p>Keine Beiträge gefunden.</p>';
                return;
            }

            posts.forEach(post => {
                const postElement = document.createElement('div');
                postElement.className = 'post';

                const title = document.createElement('h3');
                title.textContent = post.title;
                postElement.appendChild(title);

                const category = document.createElement('p');
                category.innerHTML = '<strong>Kategorie:</strong> ' + post.category;
                postElement.appendChild(category);

                const message = document.createElement('p');
                message.innerHTML = post.message.replace(/\\n/g, '<br>');
                postElement.appendChild(message);

                const meta = document.createElement('small');
                meta.innerHTML = 'von <strong>' + post.author + '</strong> am ' + post.created_at;
                postElement.appendChild(meta);

                postsContainer.appendChild(postElement);
            });
        })
        .catch(error => {
            console.error('Error fetching forum posts:', error);
            postsContainer.innerHTML = '<p>Fehler beim Laden der Beiträge.</p>';
        });
});
</script>

</body>
</html>
