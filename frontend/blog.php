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
      <a href="blog.php" class="active">Blog</a>
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
      <select name="category" id="category">
        <option value="">-- Alle Kategorien --</option>
      </select>
    </form>

    <div id="blog-posts-container">
        <!-- Blog posts will be loaded here -->
    </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category');
    const postsContainer = document.getElementById('blog-posts-container');

    // Fetch categories and populate the dropdown
    fetch('../backend/api/categories.php')
        .then(response => response.json())
        .then(categories => {
            categories.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.name;
                option.textContent = cat.name;
                categorySelect.appendChild(option);
            });
        });

    // Function to fetch and render posts
    function fetchAndRenderPosts(category = '') {
        let apiUrl = '../backend/api/posts.php';
        if (category) {
            apiUrl += '?category=' + encodeURIComponent(category);
        }

        fetch(apiUrl)
            .then(response => response.json())
            .then(posts => {
                postsContainer.innerHTML = ''; // Clear existing posts

                // Group posts by category
                const postsByCategory = posts.reduce((acc, post) => {
                    if (!acc[post.category]) {
                        acc[post.category] = [];
                    }
                    acc[post.category].push(post);
                    return acc;
                }, {});

                for (const category in postsByCategory) {
                    const section = document.createElement('section');
                    section.className = 'blog-category';

                    const heading = document.createElement('h3');
                    heading.className = 'category-heading';
                    heading.textContent = '🗂️ ' + category;
                    section.appendChild(heading);

                    const cardsContainer = document.createElement('div');
                    cardsContainer.className = 'blog-cards';

                    postsByCategory[category].forEach(post => {
                        const card = document.createElement('div');
                        card.className = 'blog-card';

                        if (post.image_path) {
                            const img = document.createElement('img');
                            img.src = post.image_path;
                            img.alt = 'Beitragsbild';
                            img.className = 'blog-card-img';
                            card.appendChild(img);
                        }

                        const content = document.createElement('div');
                        content.className = 'blog-card-content';

                        const title = document.createElement('h4');
                        const titleLink = document.createElement('a');
                        titleLink.href = 'blog_post.php?id=' + post.id;
                        titleLink.textContent = post.title;
                        title.appendChild(titleLink);
                        content.appendChild(title);

                        const meta = document.createElement('p');
                        meta.className = 'blog-meta';
                        meta.textContent = new Date(post.created_at).toLocaleDateString('de-DE');
                        content.appendChild(meta);

                        const summary = document.createElement('p');
                        summary.innerHTML = post.content ? post.content.substring(0, 160) + '...' : '';
                        content.appendChild(summary);

                        const readMoreLink = document.createElement('a');
                        readMoreLink.href = 'blog_post.php?id=' + post.id;
                        readMoreLink.className = 'read-more';
                        readMoreLink.textContent = '➤ Weiterlesen';
                        content.appendChild(readMoreLink);

                        card.appendChild(content);
                        cardsContainer.appendChild(card);
                    });

                    section.appendChild(cardsContainer);
                    postsContainer.appendChild(section);
                }
            });
    }

    // Initial load of all posts
    fetchAndRenderPosts();

    // Add event listener to the category dropdown
    categorySelect.addEventListener('change', function() {
        fetchAndRenderPosts(this.value);
    });
});
</script>

</body>
</html>
