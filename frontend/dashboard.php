<?php
header("Referrer-Policy: no-referrer");
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}
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
        <p>Es gibt <strong id="open-posts-count">...</strong> Beitrag, die auf Freigabe warten.</p>
        <a href="moderate_posts.php" class="btn">Beiträge moderieren</a>
    </div>

    <div class="section">
        <h2>Letzte freigegebene Beiträge</h2>
        <table id="latest-posts-table">
            <thead>
                <tr><th>Titel</th><th>Autor</th><th>Datum</th></tr>
            </thead>
            <tbody>
                <!-- Latest posts will be loaded here -->
            </tbody>
        </table>
    </div>

    <div class="section center">
        <h2>Blog-Beiträge verwalten</h2>
        <a href="admin_create_post.php" class="btn">📝 Neuen Blogbeitrag erstellen</a>
    </div>

    <div class="section center">
        <a href="admin_logout.php" class="btn" style="background:#7a0000;">Logout</a>
        <a href="blog.php" class="btn" style="background:#7a0000;">Zur Blogseite</a>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch open posts count
    fetch('../backend/api/forum/posts/count.php?approved=0')
        .then(response => response.json())
        .then(data => {
            document.getElementById('open-posts-count').textContent = data.count;
        });

    // Fetch latest posts
    fetch('../backend/api/forum/posts.php?approved=1&limit=5')
        .then(response => response.json())
        .then(posts => {
            const tableBody = document.querySelector('#latest-posts-table tbody');
            tableBody.innerHTML = ''; // Clear existing rows
            if (posts.length > 0) {
                posts.forEach(post => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${post.title}</td>
                        <td>${post.author}</td>
                        <td>${post.created_at}</td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="3">Keine freigegebenen Beiträge gefunden.</td>';
                tableBody.appendChild(row);
            }
        });
});
</script>

</body>
</html>
