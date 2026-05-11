function openRules(rules, gameName) {
    document.getElementById('rulesTitle').innerText = gameName + ' – Rules';

    const list = document.getElementById('rulesText');
    list.innerHTML = '';

    if (!rules || rules.length === 0) {
        list.innerHTML = '<li>Rules not available.</li>';
    } else {
        rules.forEach(rule => {
            const li = document.createElement('li');
            li.textContent = rule;
            list.appendChild(li);
        });
    }

    const modal = document.getElementById('rulesModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
// Rafraîchir le stock toutes les 30 secondes
setInterval(() => {
    window.location.reload();
}, 30000);

function closeRules() {
    document.getElementById('rulesModal').style.display = 'none';
    document.body.style.overflow = '';
}

// Fermer en cliquant sur le fond du modal
function closeOnBackdrop(event) {
    if (event.target === document.getElementById('rulesModal')) {
        closeRules();
    }
}

// Fermer avec la touche Escape
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeRules();
});

// ── Recherche live (debounce) ─────────────────────────────────────────────────
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    let debounceTimer;
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            document.getElementById('searchForm').submit();
        }, 400); // soumettre après 400ms sans frappe
    });
}

function clearSearch() {
    if (searchInput) {
        searchInput.value = '';
        document.getElementById('searchForm').submit();
    }
}
function checkStock(gameId, gameName) {
    const date = new URLSearchParams(window.location.search).get('date') 
                 || new Date().toISOString().split('T')[0];
    
    let dispo = false;
    
    fetch(`check_stock.php?id_game=${gameId}&date=${date}`)
        .then(res => res.json())
        .then(data => {
            if (data.disponibles <= 0) {
                alert(`"${gameName}" is no longer available on this date!`);
                window.location.reload(); // rafraîchir les badges
            }
        });
    
    return true; // laisser la navigation se faire
}

// ── Booking (sessionStorage pour reservation.html) ────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const bookButtons = document.querySelectorAll('.btn-reserve');
    bookButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            // Si c'est un lien Symfony (href != '#'), laisser passer
            if (this.tagName === 'A' && this.getAttribute('href') !== '#') return;

            e.preventDefault();
            const gameCard = this.closest('.game-card');
            if (!gameCard) return;
            const gameNameElem = gameCard.querySelector('.game-content h3');
            const gameName = gameNameElem ? gameNameElem.innerText.trim() : null;
            if (gameName) {
                sessionStorage.setItem('activity', gameName);
                window.location.href = 'reservation.php';
            }
        });
    });
});