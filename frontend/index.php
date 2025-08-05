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
                <section id="latest-forum-post">
                    <h2>Neuster Forum-Beitrag</h2>
                    <!-- Latest forum post will be loaded here -->
                </section>

                <section id="latest-blog-post">
                    <h2>Neuster Blog-Eintrag</h2>
                    <!-- Latest blog post will be loaded here -->
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fetch latest forum post
            fetch('../backend/api/forum/posts.php?approved=1&limit=1')
                .then(response => response.json())
                .then(posts => {
                    const container = document.getElementById('latest-forum-post');
                    if (posts.length > 0) {
                        const post = posts[0];
                        container.innerHTML += `
                            <div class="entry">
                                <a href="forum_post.php?id=${post.id}">
                                    <div class="entry-title">${post.title}</div>
                                </a>
                                <div class="entry-meta">${post.author} - ${new Date(post.created_at).toLocaleString('de-DE')}</div>
                            </div>
                        `;
                    } else {
                        container.innerHTML += '<p>Keine Beiträge gefunden.</p>';
                    }
                });

            // Fetch latest blog post
            fetch('../backend/api/posts.php?limit=1')
                .then(response => response.json())
                .then(posts => {
                    const container = document.getElementById('latest-blog-post');
                    if (posts.length > 0) {
                        const post = posts[0];
                        container.innerHTML += `
                            <div class="entry">
                                <a href="blog_post.php?id=${post.id}">
                                    <div class="entry-title">${post.title}</div>
                                </a>
                                <div class="entry-meta">Veröffentlicht am ${new Date(post.created_at).toLocaleDateString('de-DE')}</div>
                            </div>
                        `;
                    } else {
                        container.innerHTML += '<p>Keine Blogbeiträge gefunden.</p>';
                    }
                });
        });
    </script>
    <button id="toTopBtn" title="Nach oben">⬆</button>
</body>
</html>
