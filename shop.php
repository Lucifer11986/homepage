<?php
// Fehleranzeige aktivieren (nur für Entwicklung, in Produktion deaktivieren)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop | AbyssForge Studio</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="overlay">
        <header>
            <h1 class="glow-text">AbyssForge Studio - Offizieller Shop</h1>
            <nav>
                <a href="index.php">Startseite</a>
                <a href="about.html">Über mich</a>
                <a href="streams.html">Streams</a>
                <a href="events.html">Events</a>
                <a href="contact.html">Kontakt</a>
                <a href="comingsoon.html">Coming Soon</a>
                <div class="dropdown">
                    <a href="#community" class="dropbtn">Community ▾</a>
                    <div class="dropdown-content">
                        <a href="discord.html">Discord</a>
                        <a href="forum.php">Forum</a>
                    </div>
                </div>
                <a href="shop.php" class="active">Shop</a>
                <a href="live.html">Live</a>
            </nav>
        </header>

        <main class="shop-section">
            <section class="shop-intro">
                <h2>Willkommen im offiziellen AbyssForge Studio Shop</h2>
                <p>Hier findest du hochwertige <strong>MLOs</strong>, <strong>Maps</strong>, exklusive <strong>Mods</strong> und weitere digitale Produkte rund um GTA RP und mehr.</p>
                <p>Unser Shop wird über die Plattform <strong>Tebex</strong> betrieben und ermöglicht dir einen schnellen, sicheren und automatisierten Kaufprozess.</p>

                <a href="https://abyssforge-studio.tebex.io/" target="_blank" class="shop-button">🔗 Zum Shop öffnen</a>
            </section>

            <section class="shop-preview">
                <h3>Was erwartet dich?</h3>
                <ul>
                    <li>✔ Professionell erstellte MLOs & Maps</li>
                    <li>✔ Sofortiger Download nach dem Kauf</li>
                    <li>✔ Regelmäßige Updates und neue Inhalte</li>
                    <li>✔ 24/7 Unterstützung über Discord</li>
                </ul>
            </section>
        </main>

        <footer>
            <p>&copy; 2025 AbyssForge Studio | Alle Rechte vorbehalten.</p>
            <div style="font-size: 0.85em; margin-top: 5px;">
                <a href="impressum.html">Impressum</a> |
                <a href="datenschutz.html">Datenschutz</a> |
                <a href="nutzungsbedingungen.html">Nutzungsbedingungen</a> |
                <a href="cookies.html">Cookies</a>
            </div>
        </footer>
    </div>

    <script src="script.js"></script>
</body>
</html>
