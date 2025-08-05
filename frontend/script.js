// Twitch API Live Status Checker
const liveStatusElement = document.getElementById('live-status');

async function checkLiveStatus() {
    try {
        const response = await fetch('../backend/api/twitch_status.php');
        const data = await response.json();

        if (data.status === 'online') {
            setOnline();
        } else {
            setOffline();
        }
    } catch (error) {
        console.error('Fehler beim Abrufen des Twitch-Status:', error);
        setOffline();
    }
}

function setOnline() {
    liveStatusElement.textContent = 'LIVE';
    liveStatusElement.classList.remove('offline');
    liveStatusElement.classList.add('online');
}

function setOffline() {
    liveStatusElement.textContent = 'Stream Offline';
    liveStatusElement.classList.remove('online');
    liveStatusElement.classList.add('offline');
}

// Check live status beim Laden + alle 30 Sekunden aktualisieren
checkLiveStatus();
setInterval(checkLiveStatus, 30000);

// Particles.js Initialisierung für Feuer-Partikel
document.addEventListener('DOMContentLoaded', () => {
  if (window.particlesJS) {
    particlesJS('particles-js', {
      "particles": {
        "number": {
          "value": 50,
          "density": {
            "enable": true,
            "value_area": 800
          }
        },
        "color": {
          "value": "#ff4500"
        },
        "shape": {
          "type": "circle"
        },
        "opacity": {
          "value": 0.8,
          "random": true,
          "anim": {
            "enable": true,
            "speed": 1,
            "opacity_min": 0.3,
            "sync": false
          }
        },
        "size": {
          "value": 4,
          "random": true,
          "anim": {
            "enable": true,
            "speed": 3,
            "size_min": 1,
            "sync": false
          }
        },
        "move": {
          "enable": true,
          "speed": 2,
          "direction": "top",
          "random": true,
          "straight": false,
          "out_mode": "out",
          "bounce": false
        }
      },
      "interactivity": {
        "detect_on": "canvas",
        "events": {
          "onhover": {
            "enable": false
          },
          "onclick": {
            "enable": false
          },
          "resize": true
        }
      },
      "retina_detect": true
    });
  } else {
    console.error('particlesJS library not loaded');
  }
});

// === To The Top Button Funktionalität ===
window.onscroll = function() {
    const btn = document.getElementById("toTopBtn");
    if (!btn) return; // Falls Button fehlt, nicht abstürzen
    btn.style.display = (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) ? "block" : "none";
};

document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById("toTopBtn");
  if (btn) {
    btn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }
});

// Cookie Banner anzeigen / ausblenden
document.addEventListener('DOMContentLoaded', () => {
  const banner = document.getElementById('cookie-banner');
  const acceptBtn = document.getElementById('cookie-accept-btn');

  if (!localStorage.getItem('cookiesAccepted')) {
    banner.style.display = 'flex';
  }

  if (acceptBtn) {
    acceptBtn.addEventListener('click', () => {
      localStorage.setItem('cookiesAccepted', 'true');
      banner.style.display = 'none';
    });
  }
});

// Neue Funktion: Menüpunkt active setzen je nach aktueller URL
document.addEventListener("DOMContentLoaded", function() {
  const currentPath = window.location.pathname.replace(/\/$/, "");
  const navLinks = document.querySelectorAll("nav a");

  navLinks.forEach(link => {
    let linkPath = link.getAttribute("href");
    if (!linkPath) return;

    // Normalisieren, falls relativer Pfad ohne slash am Ende
    if (linkPath.indexOf("http") !== 0 && !linkPath.startsWith("#")) {
      linkPath = linkPath.replace(/\/$/, "");
      if (linkPath === "") linkPath = "/";
    }

    if (
      currentPath === linkPath ||
      (linkPath.startsWith("#") && window.location.hash === linkPath)
    ) {
      navLinks.forEach(l => l.classList.remove("active"));
      link.classList.add("active");
    }
  });
});
