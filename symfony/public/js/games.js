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

function closeOnBackdrop(event) {
    if (event.target === document.getElementById('rulesModal')) {
        closeRules();
    }
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeRules();
});

// Recherche live (debounce)
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    let debounceTimer;
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            document.getElementById('searchForm').submit();
        }, 400);
    });
}

function clearSearch() {
    if (searchInput) {
        searchInput.value = '';
        document.getElementById('searchForm').submit();
    }
}

function checkStock(gameId, gameName, url) {
    fetch(`/game/check-stock/${gameId}`)
        .then(res => res.json())
        .then(data => {
            if (data.disponibles <= 0) {
                alert(`"${gameName}" is no longer available!`);
                window.location.reload();
            } else {
                // POST to game/reserve to set the session first
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/game/reserve/${gameId}`;
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '';
                form.appendChild(csrf);
                document.body.appendChild(form);
                form.submit();
            }
        });
    return false;
}