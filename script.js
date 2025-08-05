// Twitch API Live Status Checker
// Du brauchst einen Twitch Client-ID und Access Token.
// So bekommst du das:
// 1. Erstelle eine App hier: https://dev.twitch.tv/console/apps
// 2. Hol dir Client-ID
// 3. Hol dir einen Access Token (OAuth) mit scopes: user:read:email (für public info reicht meist)
//    https://dev.twitch.tv/docs/authentication/getting-tokens-oauth#oauth-client-credentials-flow

// Füge deine Daten hier ein:
const CLIENT_ID = 'ClientID';
// getKey("ClientID")
const ACCESS_TOKEN = 'AccessToken';
// getKey("AccessToken")
const CHANNEL_NAME = 'lucifer11986';

const liveStatusElement = document.getElementById('live-status');

async function getKey(keyName)
{
  return new Promise((resolve,reject) => {
    fetch(`http://localhost:3000/get_keys.php?name=${encodeURIComponent(keyName)}`)
    .then(response => response.json())
    .then(data => {
      if (data.key)
      {
        resolve(data.key);
      }
      else
      {
        console.error("Error: ", data.error);
        reject();
      }
    })
    .catch(error => {
      console.error("Fetch failed: ", error);
      reject();
    })
  });
}

async function checkLiveStatus() {
    try {
        // Schritt 1: Nutzer-ID holen (User Lookup)
        const userResponse = await fetch(`https://api.twitch.tv/helix/users?login=${CHANNEL_NAME}`, {
            headers: {
                'Client-ID': await getKey(CLIENT_ID),
                'Authorization': `Bearer ${ await getKey(ACCESS_TOKEN)}`
            }
        });
        const userData = await userResponse.json();
        if (!userData.data || userData.data.length === 0) {
            console.error('User nicht gefunden');
            setOffline();
            return;
        }
        const userId = userData.data[0].id;

        // Schritt 2: Live Status prüfen
        const streamResponse = await fetch(`https://api.twitch.tv/helix/streams?user_id=${userId}`, {
            headers: {
                'Client-ID': await getKey(CLIENT_ID),
                'Authorization': `Bearer ${await getKey(ACCESS_TOKEN)}`
            }
        });
        const streamData = await streamResponse.json();

        if (streamData.data && streamData.data.length > 0) {
            setOnline();
        } else {
            setOffline();
        }
    } catch (error) {
        console.error('Fehler beim Twitch API Request:', error);
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
